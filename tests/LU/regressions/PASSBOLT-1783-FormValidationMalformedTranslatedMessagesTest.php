<?php
/**
 * Bug PASSBOLT-1783 - Regression test
 *
 * @copyright (c) 2017 Passbolt SARL
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class PASSBOLT1783 extends PassboltTestCase {

	/**
	 * Scenario :   After creating a user, the given user can complete the setup and login with the chosen password
	 * Given        I am admin
	 * And          I am logged in
	 * When         I go to user workspace
	 * And          I create a user with a first name of 1 character
	 * Then         I should a well formed error message
	 */
	public function testCreateUserWrongDataWellformedErrorFeedback() {
		// Reset database at the end of test.
		$this->resetDatabaseWhenComplete();

		// Given I am Ada
		$user = User::get('admin');
		$this->setClientConfig($user);

		// And I am logged in
		$this->loginAs($user);

		// When Go to user workspace
		$this->gotoWorkspace('user');

		// And I create a user with a first name of 1 character
		$this->gotoCreateUser();
		$this->inputText('js_field_first_name', 'a');
		$this->inputText('js_field_last_name', 'a');
		$this->inputText('js_field_username', 'a');
		if (isset($user['admin']) && $user['admin'] === true) {
			// Check box admin
			$this->checkCheckbox('#js_field_role_id .role-admin input[type=checkbox]');
		}
		$this->click('.create-user-dialog input[type=submit]');

		// Then I should a well formed error message
		$this->assertElementContainsText(
			$this->find('js_field_first_name_feedback'),
			'First name should be between 2 and 64 characters long'
		);
	}

}