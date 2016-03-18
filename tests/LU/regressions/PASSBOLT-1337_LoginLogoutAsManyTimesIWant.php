<?php
/**
 * Bug PASSBOLT-1377 - Regression test
 *
 * @copyright (c) 2015-present Bolt Softwares Pvt Ltd
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class PASSBOLT1377 extends PassboltTestCase {

	/**
	 * Scenario: As a user while editing a password that had been shared with a deleted user, the application shouldn't crash silently
	 *
	 * Given        I am ada
	 *
	 * [LOOP]
	 * When         I login
	 * And          I logout
	 * Then         I should see the login page
	 * [END_LOOP]
	 *
	 */
	public function testLoginLogoutAsManyTimesIWant() {
		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);

		for ($i=0; $i<5; $i++) {
			// When I am logged in on the user workspace
			$this->loginAs($user);

			// And I logout
			$this->logout();

			// Then I should be redirected to the login page
			$this->waitUntilISee('.plugin-check.firefox.success');
		}
	}
}