<?php
/**
 * Feature : Logout
 *
 * As LU I should be logged out when I click on the logout link in the navigation
 * As LU I should be logged out when the session expires
 * As LU I should be logged out when I quit the browser and restart it after my session expired
 * As LU I should be logged out when I close the passbolt tab and restore it after my session expired
 *
 * @copyright (c) 2017 Passbolt SARL
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class LogoutTest extends PassboltTestCase {

	/**
	 * Executed after every tests
	 */
	protected function tearDown() {
		// Reset the selenium extra configuration.
		PassboltServer::resetExtraConfig();
		parent::tearDown();
	}

	/**
	 * @group saucelabs
	 *
     * Scenario: As LU I should be logged out when I click on the logout link in the navigation
     * Given    I am Ada
     * And      I am logged in on the password workspace
     * When     I click on the logout button
     * Then     Then I should see the login page
     *
	 * @throws Exception
	 */
	public function testLogout() {
		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);

		// Reduce the session timeout to accelerate the test
		PassboltServer::setExtraConfig([
			'Session' => [
				'timeout' => 0.25
			]
		]);

		// And I am logged in on the password workspace
		$this->loginAs($user);

		// When I click on the logout button
		$this->click('#js_app_navigation_right .logout a');

		// Then I should see the login page
		$this->waitUntilISee('.plugin-check.' . $this->_browser['type'] . '.success');
	}

    /**
     * Scenario: As LU I should be logged out when the session expires (auto redirect)
     * Given    I am Ada
     * And      I am logged in on the password workspace
     * And      I wait until the session expires
     * When     I click on a password I own
     * Then     I should see the session expired dialog
     * And      I should be redirected to the login page in 60 seconds
     */
	public function testOnClickSessionExpiredAutoRedirect() {
		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);

		// Reduce the session timeout to accelerate the test
		PassboltServer::setExtraConfig([
			'Session' => [
				'timeout' => 0.25
			]
		]);

		// And I am logged in on the password workspace
		$this->loginAs($user);

        // And I wait until the session expires
		sleep(15);

		// And I click on a password I own
		$resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));
		$this->clickPassword($resource['id']);

		// Then I should see the session expired dialog
		$this->assertSessionExpiredDialog();

		// And I should be redirected to the login page in 60 seconds
		$this->waitUntilISee('.plugin-check.' . $this->_browser['type'] . '.success', null, 7);
	}

    /**
     * Scenario: As LU I should be logged out when the session expires (manual redirect)
     * Given    I am Ada
     * And      I am logged in on the passwords workspace
     * When     I wait until the session timeout
     * And      I click on a password I own
     * Then     I should see the session expired dialog
     * When     I click on the 'Redirect now' button
     * Then     I should see the login page
     */
	public function testOnClickSessionExpiredManualRedirect() {
		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);

		// Reduce the session timeout to accelerate the test
		PassboltServer::setExtraConfig([
			'Session' => [
				'timeout' => 0.25
			]
		]);

		// And I am logged in on the password workspace
		$this->loginAs($user);

        // When I wait until the session timeout
		sleep(15);

		// And I click on a password I own
		$resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));
		$this->clickPassword($resource['id']);

		// Then I should see the session expired dialog
		$this->assertSessionExpiredDialog();

		// When I click on Redirect now
		$this->click('confirm-button');

		// Then I should see the login page
		$this->waitUntilISee('.plugin-check.' . $this->_browser['type'] . '.success');
	}

	/**
     * @group saucelabs
     *
     * Scenario: As LU I should be logged out when I quit the browser and restart it after my session expired
     * Given    I am Ada
     * And      I am logged in on the passwords workspace
     * When     I wait until the session timeout
     * Then     I should see the session expired dialog
     * When     I click on the 'Redirect now' button
     * Then     I should see the login page
     *
	 * @throws Exception
	 */
	public function testSessionExpired() {
		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);

		// Reduce the session timeout to accelerate the test
		PassboltServer::setExtraConfig([
			'Session' => [
				'timeout' => 0.25
			]
		]);

		// And I am logged in on the password workspace
		$this->loginAs($user);

		// Then I should see the session expired dialog
		$this->assertSessionExpiredDialog();

		// When I click on Redirect now button
		$this->click('confirm-button');

		// Then I should see the login page
		$this->waitUntilISee('.plugin-check.' . $this->_browser['type'] . '.success');
	}

	/**
	 * @group no-saucelabs
	 *
	 * Scenario:  As LU I should be logged out when I quit the browser and restart it after my session expired
	 * Given    I am Ada
	 * And      I am logged in on the passwords workspace
	 * When 	I quit the browser and restart it after my session is expired
	 * Then 	I should be logged out
	 *
	 * @throws Exception
	 */
	public function testRestartBrowserAndLoggedOutAfterSessionExpired() {
		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);

		// Reduce the session timeout to accelerate the test
		PassboltServer::setExtraConfig([
			'Session' => [
				'timeout' => 0.25
			]
		]);

		// And I am logged in on the password workspace
		$this->loginAs($user);

		// When I restart the browser
		$this->restartBrowser(array(
			'waitBeforeRestart' => 15
		));

		// Then I should be logged out
		$this->assertUrlMatch('/\/auth\/login/');
	}

	/**
	 * @group firefox-only
	 * @todo PASSBOLT-2263 close and restore doesn't work with the latest chrome driver
	 *
	 * Scenario:  As LU I should be logged out when I close the passbolt tab and restore it after my session expired
	 * Given    I am Ada
	 * And      I am logged in on the passwords workspace
	 * When 	I close the tab and restore it after my session is expired
	 * Then 	I should be logged out
	 *
	 * @throws Exception
	 */
	public function testCloseRestoreTabAndLoggedOutAfterSessionExpired() {
		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);

		// And I am on second tab
		$this->openNewTab();

		// Reduce the session timeout to accelerate the test
		PassboltServer::setExtraConfig([
			'Session' => [
				'timeout' => 0.25
			]
		]);

		// And I am logged in on the password workspace
		$this->loginAs($user);

		// When I close and restore the tab
		$this->closeAndRestoreTab(array(
			'waitBeforeRestore' => 15
		));

		// Then I should be logged out
		$this->waitUntilUrlMatches('auth/login', 120);
	}

}