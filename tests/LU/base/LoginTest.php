<?php
/**
 * Feature : Login
 *
 * As AN I can login to passbolt
 * As AN I can login to passbolt by submitting the login form with the enter key
 * As AN I can login to passbol on different tabs without conflict between workers
 * As LU I should still be logged in after I restart the browser
 * As LU I should still be logged in after I close and restore the passbolt tab
 *
 * @copyright (c) 2017 Passbolt SARL
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class LoginTest extends PassboltTestCase {

	/**
	 * @group saucelabs
	 * Scenario:  As AN I can login to passbolt
	 * @todo document the steps
	 * @throws Exception
	 */
    public function testLogin() {
        $this->getUrl('login');
	    sleep(1);
        $this->assertVisible('.plugin-check.' . $this->_browser['type'] . '.warning');

        $user = User::get('ada');
        $this->setClientConfig($user);

        $this->getUrl('login');

        $this->waitUntilISee('.plugin-check.' . $this->_browser['type'] . '.success');
        $this->waitUntilISee('.plugin-check.gpg.success');

        $this->assertVisible('passbolt-iframe-login-form');
        $this->goIntoLoginIframe();

        $this->assertVisible('.login-form.master-password');
        $this->assertInputValue('UserUsername', $user['Username']);

        $this->inputText('js_master_password', 'somethingwrong');
        $this->click('loginSubmit');

        $this->waitUntilISee('#loginMessage.error');
        $this->inputText('js_master_password', $user['MasterPassword']);

        $this->click('loginSubmit');
        $this->assertElementContainsText('loginMessage','Please wait');
        $this->goOutOfIframe();

        $this->waitUntilISee('.login.form .feedback');
        $this->assertElementContainsText('.login.form .feedback','Logging in');
        $this->waitCompletion();

        // wait for redirection trigger
        sleep(1);
        $this->waitCompletion();

	    $this->assertElementContainsText(
		    $this->findByCss('.header .user.profile .details .name'),
		    'Ada Lovelace'
	    );
    }

	/**
	 * Scenario:  As AN I can login to passbolt by submitting the login form with the enter key
	 * @todo document the steps
	 * @throws Exception
	 */
	public function testLoginWithEnterKey() {
		$this->getUrl('login');
		sleep(1);
		$this->assertVisible('.plugin-check.' . $this->_browser['type'] . '.warning');

		$user = User::get('ada');
		$this->setClientConfig($user);

		$this->getUrl('login');

		$this->waitUntilISee('.plugin-check.' . $this->_browser['type'] . '.success');
		$this->waitUntilISee('.plugin-check.gpg.success');

		$this->assertVisible('passbolt-iframe-login-form');
		$this->goIntoLoginIframe();

		$this->assertVisible('.login-form.master-password');
		$this->assertInputValue('UserUsername', $user['Username']);

		$this->click('js_master_password');
		$this->waitUntilElementHasFocus('js_master_password');
		$this->typeTextLikeAUser($user['MasterPassword']);
		$this->pressEnter();

		$this->assertElementContainsText('loginMessage','Please wait');
		$this->goOutOfIframe();

		$this->waitUntilISee('.login.form .feedback');
		$this->assertElementContainsText('.login.form .feedback','Logging in');
		$this->waitCompletion();

		// wait for redirection trigger
		sleep(1);
		$this->waitCompletion();

		$this->assertElementContainsText(
			$this->findByCss('.header .user.profile .details .name'),
			'Ada Lovelace'
		);
	}

	/**
	 * Scenario:  As AN I can login to passbolt on different tabs without conflict between workers
	 * Given 	As AN with plugin on the login page
	 * When 	I open a new tab and go to the login page
	 * And 		I switch back to the first tab
	 * Then 	I should be able to login to passbolt from the first tab
	 * When 	I logout
	 * And 		I switch to the second tab
	 * Then 	I should be able to login to passbolt from the second tab
	 *
	 * @throws Exception
	 */
	public function testMultipleTabsLogin() {
		$user = User::get('ada');
		$this->setClientConfig($user);

		// Given As AN with plugin on the login page
		$this->getUrl('login');
		$this->waitUntilISee('.plugin-check.gpg.success');

		// When I open a new tab and go to the login page
		$this->openNewTab('login');
		$this->waitUntilISee('.plugin-check.gpg.success');

		// And I switch back to the first tab
		$this->switchToPreviousTab();

		// Then I should be able to login to passbolt from the first tab.
		$this->loginAs($user, false);

		// When I logout
		$this->logout();

		// And I switch to the second tab
		$this->switchToNextTab();

		// Then I should be able to login to passbolt from the second tab
		$this->loginAs($user, false);
	}

	/**
	 * @group chrome-only no-saucelabs
	 *
	 * Scenario:  As LU I should still be logged in after I restart the browser
	 * Given    I am Ada
	 * And      I am logged in on the passwords workspace
	 * When 	I restart the browser
	 * Then 	I should still be logged in
	 *
	 * @throws Exception
	 */
	public function testRestartBrowserAndStillLoggedIn() {
		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);

		// And I am logged in
		$this->loginAs($user, false);

		// When I restart the browser
		$this->restartBrowser();

		// Then I should still be logged in
		$this->waitUntilISee('.logout');
	}

	/**
	 * @group firefox-only
	 * @todo PASSBOLT-2263 close and restore doesn't work with the latest chrome driver
	 *
	 * Scenario:  As LU I should still be logged in after I close and restore the passbolt tab
	 * Given    I am Ada
	 * And 		I am on second tab
	 * And      I am logged in on the passwords workspace
	 * When 	I close and restore the tab
	 * Then 	I should still be logged in
	 *
	 * @throws Exception
	 */
	public function testCloseRestoreTabAndStillLoggedIn() {
		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);

		// And I am on second tab
		$this->openNewTab();

		// And I am logged in
		$this->loginAs($user, false);

		// When I close and restore the tab
		$this->closeAndRestoreTab();
		$this->waitCompletion();

		// Then I should still be logged in
		$this->waitUntilISee('.logout');
	}

}
