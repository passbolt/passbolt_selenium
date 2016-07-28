<?php
/**
 * Feature : Login
 *
 * As AN I can login to passbolt
 * As AN I can login to passbolt by submitting the login form with the enter key
 * As AN I can login to passbol on different tabs without conflict between workers
 * As LU I can leave the browser and reload it I should still be logged in
 *
 * @copyright (c) 2015-present Bolt Softwares Pvt Ltd
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class LoginTest extends PassboltTestCase {

	/**
	 * Scenario:  As AN I can login to passbolt
	 * @todo document the steps
	 * @throws Exception
	 */
    public function testLogin() {
        $this->getUrl('login');
	    sleep(1);
        $this->assertVisible('.plugin-check.firefox.warning');

        $user = User::get('ada');
        $this->setClientConfig($user);

        $this->getUrl('login');

        $this->waitUntilISee('.plugin-check.firefox.success');
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
		$this->assertVisible('.plugin-check.firefox.warning');

		$user = User::get('ada');
		$this->setClientConfig($user);

		$this->getUrl('login');

		$this->waitUntilISee('.plugin-check.firefox.success');
		$this->waitUntilISee('.plugin-check.gpg.success');

		$this->assertVisible('passbolt-iframe-login-form');
		$this->goIntoLoginIframe();

		$this->assertVisible('.login-form.master-password');
		$this->assertInputValue('UserUsername', $user['Username']);

		$this->click('js_master_password');
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
	 * When 	I open a new window and go to the login page
	 * And 		I switch to the first window
	 * Then 	I should be able to login to passbolt from the first window
	 * When 	I logout
	 * And 		I switch to the second window
	 * Then 	I should be able to login to passbolt from the second window
	 *
	 * @throws Exception
	 */
	public function testMultipleTabsLogin() {
		$user = User::get('ada');
		$this->setClientConfig($user);

		// Given As AN with plugin on the login page
		$this->getUrl('login');
		$this->waitUntilISee('.plugin-check.gpg.success');

		// When I open a new window and go to the login page
		$this->driver->getKeyboard()
			->sendKeys([WebDriverKeys::CONTROL, 'n']);
		$windowHandles = $this->driver->getWindowHandles();
		$this->driver->switchTo()->window($windowHandles[1]);
		$this->click('html');
		$this->getUrl('login');
		$this->waitUntilISee('.plugin-check.gpg.success');

		// And I switch to the first window
		$this->driver->switchTo()->window($windowHandles[0]);
		$this->click('html');

		// Then I should be able to login to passbolt from the first window.
		$this->loginAs($user);

		// When I logout
		$this->logout();

		// And I switch to the second window
		$this->driver->switchTo()->window($windowHandles[1]);
		$this->click('html');

		// Then I should be able to login to passbolt from the second window
		$this->loginAs($user);
	}

	/**
	 * Scenario:  As LU I can leave the browser and reload it I should still be logged in
	 * Given    I am Ada
	 * And      I am logged in on the passwords workspace
	 * When 	I quit the browser and reload it
	 * Then 	I should still be logged in
	 *
	 * @throws Exception
	 */
	public function testLoggedInAfterLeavingBrowserAndReload() {
		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);

		// And I am logged in
		$this->loginAs($user);

		// When I quit the browser and reload it
		$this->quitAndReload('');

		// Then I should still be logged in
		$this->waitUntilISee('.logout');
	}

}
