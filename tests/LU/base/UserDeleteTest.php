<?php
/**
 * Feature :  As a admin I can delete users
 *
 * Scenarios :
 *  - As admin I should be able to delete a user on a right click
 *  - As admin I should be able to delete a user using the delete button
 *  - As Admin I should'nt be able to delete my own user account
 *  - As LU I should be able to get a clear feedback at login if my account has been deleted.
 *
 * @copyright (c) 2015-present Bolt Softwares Pvt Ltd
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class UserDeleteTest extends PassboltTestCase {

	/**
     * Scenario :   As admin I should be able to delete a user on a right click
	 * Given        I am logged in as admin in the user workspace
	 * And          I right click on a user
	 * Then         I should see a contextual menu with a delete option
	 * When         I click on the delete option
	 * Then         I should see a confirmation dialog
	 * When         I click ok in the confirmation dialog
	 * Then         I should see a confirmation message
	 * And          I shouldn't see the user in the user list anymore
	 * When         I refresh the page
	 * Then         I still shouldn't see the user in the user list anymore
	 */
	public function testDeleteUserRightClick() {
		// Reset database at the end of test.
		$this->resetDatabaseWhenComplete();

		// And I am Admin
		$user = User::get('admin');
		$this->setClientConfig($user);

		// And I am logged in on the user workspace
		$this->loginAs($user);

		// Go to user workspace
		$this->gotoWorkspace('user');

		// When I right click on a user
		$user = User::get('betty');
		$this->rightClickUser($user['id']);

		// Then I select the delete option in the contextual menu
		$this->click('#js_user_browser_menu_delete a');

		// Assert that the confirmation dialog is displayed.
		$this->assertConfirmationDialog('Do you really want to delete user ?');

		// Click ok in confirmation dialog.
		$this->confirmActionInConfirmationDialog();

		// Then I should see a success notification message saying the user is deleted
		$this->assertNotification('app_users_delete_success');

		// And I should not see the user in the list anymore
		$this->assertTrue($this->isNotVisible('user_' . $user['id']));

		// When I refresh the page
		$this->refresh();

		// And go to user workspace
		$this->gotoWorkspace('user');

		// Then I should not see the user in the list anymore
		$this->assertTrue($this->isNotVisible('user_' . $user['id']));
	}

	/**
	 * @group saucelabs
	 * Scenario :   As admin I should be able to delete a user using the delete button
	 * Given        I am logged in as admin in the user workspace
	 * And          I click on the user
	 * And          I click on delete button
	 * Then         I should see a confirmation dialog
	 * When         I click ok in the confirmation dialog
	 * Then         I should see a confirmation message
	 * And          I shouldn't see the user in the user list anymore
	 * When         I refresh the page
	 * Then         I still shouldn't see the user in the user list anymore
	 */
	public function testDeleteUserButton() {
		// Reset database at the end of test.
		$this->resetDatabaseWhenComplete();

		// Given I am Admin
		$user = User::get('admin');
		$this->setClientConfig($user);

		// And I am logged in on the user workspace
		$this->loginAs($user);

		// Go to user workspace
		$this->gotoWorkspace('user');

		// When I right click on a user
		$user = User::get('betty');
		$this->clickUser($user['id']);

		// Then I select the delete option in the contextual menu
		$this->click('js_user_wk_menu_deletion_button');

		// Assert that the confirmation dialog is displayed.
		$this->assertConfirmationDialog('Do you really want to delete user ?');

		// Click ok in confirmation dialog.
		$this->confirmActionInConfirmationDialog();

		// Then I should see a success notification message saying the user is deleted
		$this->assertNotification('app_users_delete_success');

		// And I should not see the user in the list anymore
		$this->assertTrue($this->isNotVisible('user_' . $user['id']));

		// When I refresh the page
		$this->refresh();

		// And go to user workspace
		$this->gotoWorkspace('user');

		// Then I should not see the user in the list anymore
		$this->assertTrue($this->isNotVisible('user_' . $user['id']));
	}

	/**
	 * Scenario :   As Admin I should'nt be able to delete my own user account
	 * Given        I am logged in as admin in the user workspace
	 * And          I click on my own name in the user list
	 * Then         I should see that the delete button is disabled
	 * When         I right click on my name in the users list
	 * Then         I should see a contextual menu
	 * And          I should see that the delete option is not available.
	 */
	public function testDeleteUserMyself() {
		// And I am Admin
		$user = User::get('admin');
		$this->setClientConfig($user);

		// And I am logged in on the user workspace
		$this->loginAs($user);

		// Go to user workspace
		$this->gotoWorkspace('user');

		// When I right click on a user
		$this->clickUser($user['id']);

		// Then I should see that the delete button is disabled.
		$this->assertElementAttributeEquals(
			$this->find('js_user_wk_menu_deletion_button'),
			'disabled',
			'true'
		);

		// Right click on the same user.
		$this->rightClickUser($user['id']);

		// I should see that the delete option is not available.
		$this->assertNotVisible('js_user_browser_menu_delete');
	}

	/**
	 * Scenario :   As LU I should be able to get a clear feedback at login if my account has been deleted.
	 * Given        I am logged in as admin in the user workspace
	 * And          I click on the user
	 * And          I click on delete button
	 * Then         I should see a confirmation dialog
	 * When         I click ok in the confirmation dialog
	 * Then         I should see a confirmation message
	 * When         I log out
	 * And          I become betty
	 * And          I go to the login page
	 * Then         I should see a feedback telling me that my account doesn't exist on server
	 */
	public function testDeletedUserGetFeedback() {
		// Reset database at the end of test.
		$this->resetDatabaseWhenComplete();

		// Given I am Admin
		$user = User::get('admin');
		$this->setClientConfig($user);

		// And I am logged in on the user workspace
		$this->loginAs($user);

		// Go to user workspace
		$this->gotoWorkspace('user');

		// When I right click on a user
		$user = User::get('betty');
		$this->clickUser($user['id']);

		// Then I select the delete option in the contextual menu
		$this->click('js_user_wk_menu_deletion_button');

		// Assert that the confirmation dialog is displayed.
		$this->assertConfirmationDialog('Do you really want to delete user ?');

		// Click ok in confirmation dialog.
		$this->confirmActionInConfirmationDialog();

		// Then I should see a success notification message saying the user is deleted
		$this->assertNotification('app_users_delete_success');

		// Log out.
		$this->logout();

		// I become betty.
		$betty = User::get('betty');
		$this->setClientConfig($betty);

		// When I go to login.
		$this->getUrl('login');

		// I should see a feedback telling me that the user doesn't exist on server.
		$this->waitUntilISee('html.server-not-verified.server-no-user');
		$this->waitUntilISee('.plugin-check.gpg.error', '/There is no user associated with this key/');
		$this->waitUntilISee('.users.login.form .feedback', '/The account configured doesn\'t exist/');
	}

}