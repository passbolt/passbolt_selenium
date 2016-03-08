<?php
/**
 * User with configured plugin login test
 *
 * @copyright 	(c) 2015-present Bolt Software Pvt. Ltd.
 * @licence			GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
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

	public function assertSessionExpiredDialog() {
		// Assert I can see the confirm dialog.
		$this->waitUntilISee('.session-expired-dialog');
		// Then I can see the close dialog button
		$this->assertNotVisible('.session-expired-dialog a.dialog-close');
		// Then I can see the cancel link.
		$this->assertNotVisible('.session-expired-dialog a.cancel');
		// Then I can see the Ok button.
		$this->assertVisible('.session-expired-dialog input#confirm-button');
		// Then I can see the title
		$this->assertElementContainsText('.session-expired-dialog', 'Session expired');
	}

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
		$this->waitUntilISee('.plugin-check.firefox.success');
	}

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

		sleep(15);

		// When I click on a password I own
		$resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));
		$this->clickPassword($resource['id']);

		// Then I should see the session expired dialog
		$this->assertSessionExpiredDialog();

		// And I should be redirected to the login page in 60 seconds
		$this->waitUntilISee('.plugin-check.firefox.success', null, 7);
	}

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

		sleep(15);

		// When I click on a password I own
		$resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));
		$this->clickPassword($resource['id']);

		// Then I should see the session expired dialog
		$this->assertSessionExpiredDialog();

		// When I click on Redirect now
		$this->click('confirm-button');

		// Then I should see the login page
		$this->waitUntilISee('.plugin-check.firefox.success');
	}

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

		sleep(60);

		// Then I should see the session expired dialog
		$this->assertSessionExpiredDialog();

		// When I click on Redirect now
		$this->click('confirm-button');

		// Then I should see the login page
		$this->waitUntilISee('.plugin-check.firefox.success');
	}

}