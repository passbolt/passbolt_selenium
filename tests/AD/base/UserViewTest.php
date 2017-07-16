<?php
/**
 * Feature :  As Admin I can view user information
 *
 * Scenarios :
 * - As an admin I should see the sidebar groups section updated when I create a group
 *
 * @copyright (c) 2017-present Passbolt SARL
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class ADUserViewTest extends PassboltTestCase {

	/**
	 * @group saucelabs
	 * Scenario :   As an admin I should see the sidebar groups section updated when I create a group
	 *
	 * Given	I am logged in as Admin, and I go to the user workspace
	 * When		I click on a user
	 * And 		I create a group where the user I selected is member of
	 * Then 	I should see the groups membership list updated with the new group
	 */
	public function testUpdateSidebarGroupsListWhenCreateGroup() {
		// Reset database at the end of test.
		$this->resetDatabaseWhenComplete();

		// Given I am Ada
		$user = User::get('admin');
		$this->setClientConfig($user);

		// And I am logged in on the user workspace
		$this->loginAs($user);
		$this->gotoWorkspace('user');

		// When I click on a user
		$userF = User::get('frances');
		$this->clickUser($userF);

		// And I create a group where the user I selected is member of.
		$group = ['name' => 'New group'];
		$users = ['frances'];
		$this->createGroup($group, $users, $user);

		// Then I should see a success notification
		$this->assertNotification('app_groups_add_success');

		// I should see the groups membership list updated with the new group
		$this->assertGroupUserInSidebar('New group', true);
	}

}
