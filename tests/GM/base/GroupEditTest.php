<?php
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverSelect;

/**
 * Feature :  As a group manager I can edit groups
 *
 * Scenarios :
 *  - As a group manager I can edit a group using the right click contextual menu
 *  - As a group manager I shouldn't be able to edit a group I don't manager from the contextual menu
 *  - As a group manager As a GM I can edit a group from the sidebar
 *  - As a group manager I shouldn’t be able to edit the group name
 *  - As a group manager I can edit the existing group members and promote a group member to group manager
 *  - As a group manager I cannot change the latest group manager role
 *  - As a group manager I can add a user to a group using the edit group dialog
 *  - As an group manager I can remove a user from a group I manage using the edit group dialog
 *
 * @copyright (c) 2017-present Passbolt SARL
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class GMGroupEditTest extends PassboltTestCase {

	/**
	 * Scenario: As a group manager I can edit a group using the right click contextual menu
	 *
	 * Given	I am logged in as a group manager and I am on the users workspace
	 * When 	I click on the contextual menu button of a group on the right
	 * Then 	I should see the group contextual menu
	 * And  	I should see the “Edit group” option
	 * When		I click on “Edit group”
	 * Then		I should see the Edit group dialog
	 */
	public function testEditGroupRightClick() {
		// Given I am logged in as an administrator
		$user = User::get('irene');
		$this->setClientConfig($user);
		$this->loginAs($user);
		$this->gotoWorkspace('user');

		// When I click on the contextual menu button of a group on the right
		$groupId = Uuid::get('group.id.ergonom');
		$this->click("#group_$groupId .right-cell a");

		// Then I should see the group contextual menu
		$this->assertVisible('#js_contextual_menu');
		$this->assertVisible('js_group_browser_menu_edit');

		// When I click on “Edit group”
		$this->click("#js_contextual_menu #js_group_browser_menu_edit a");

		// Then I should see the Edit group dialog
		$this->waitUntilISee('.edit-group-dialog');
	}

	/**
	 * Scenario :   As a group manager I shouldn't be able to edit groups from the users workspace
	 *
	 * Given        I am a group manager
	 * And          I am on the user workspace
	 * When         I select a group
	 * Then         I should see that there is no dropdown button next to the groups
	 */
	public function testCantEditGroup() {
		// Given I am a group manager
		$user = User::get('ping');
		$this->setClientConfig($user);

		// I am logged in as admin
		$this->loginAs($user);

		// I am on the user workspace
		$this->gotoWorkspace('user');

		// When I select a group
		$group = Group::get(['id' => Uuid::get('group.id.ergonom')]);
		$this->clickGroup($group['id']);

		// Then I should see that there is no dropdown button next to the groups
		$this->assertNotVisible("#group_${$group['id']} .right-cell a");
	}

	/**
	 * Scenario: As a GM I can edit a group from the sidebar
	 *
	 * Given	I am logged in as administrator
	 * And		I am on the user workspace
	 * And		I should see a “edit” button next to the Information section
	 * When		I press the “Edit” button
	 * Then 	I should see the Edit group dialog
	 */
	public function testEditGroupFromSidebar() {
		// Given I am logged in as an administrator
		$user = User::get('irene');
		$this->setClientConfig($user);
		$this->loginAs($user);
		$this->gotoWorkspace('user');

		// When I click a group name
		$group = Group::get(['id' => Uuid::get('group.id.ergonom')]);
		$this->clickGroup($group['id']);

		// Then I should see a “edit” button next to the Information section
		$editButtonSelector = '#js_group_details #js_group_details_members #js_edit_members_button';
		$this->waitUntilISee($editButtonSelector);

		// When I press the “Edit” button
		$this->click($editButtonSelector);

		// Then I should see the Edit group dialog
		$this->waitUntilISee('.edit-group-dialog');
	}

	/**
	 * Scenario: As a GM I shouldn't be able to edit a group I don't manager from the sidebar
	 *
	 * Given	I am logged in as administrator
	 * And		I am on the user workspace
	 * When		I click a group name
	 * And		I should not see a “edit” button next to the Information section
	 */
	public function testCantEditGroupDontManageFromSidebar() {
		// Given I am logged in as an administrator
		$user = User::get('irene');
		$this->setClientConfig($user);
		$this->loginAs($user);
		$this->gotoWorkspace('user');

		// When I click a group name
		$group = Group::get(['id' => Uuid::get('group.id.accounting')]);
		$this->clickGroup($group['id']);

		// Then I should not see a “edit” button next to the Information section
		$editButtonSelector = '#js_group_details #js_group_details_members #js_edit_members_button';
		$this->assertNotVisible($editButtonSelector);
	}

	/**
	 * Scenario: As a group manager I shouldn’t be able to edit the group name
	 *
	 * Given 	I am logged in as a group manager
	 * And		I am editing a group that I manage
	 * When		I observe the content of the edit group dialog
	 * Then		I should see a “group name” field containing the current group name.
	 * And		I should see that the group name field is disabled.
	 */
	public function testCannotEditGroupName() {
		// Given I am logged in as a group manager
		$user = User::get('irene');
		$this->setClientConfig($user);
		$this->loginAs($user);
		// And I am editing a group that I manage
		$group = Group::get(['id' => Uuid::get('group.id.ergonom')]);
		$this->gotoEditGroup($group['id']);

		// When	I observe the content of the edit group dialog
		// Then	I should see a “group name” field containing the current group name.
		$this->assertInputValue('js_field_name', $group['name']);

		// And I should see that the group name field is disabled.
		$this->assertDisabled('js_field_name');
	}

	/**
	 * Scenario: As a group manager I can edit the existing group members and promote a group member to group manager
	 *
	 * Given 	I am logged in as a group manager
	 * And 		I am on the users workspace
	 * When 	I’m editing a group
	 * Then 	I should see the list of users that are part of this group in the edit group dialog
	 * And 		I should see next to each user the role that he has in the group in a select box
	 * When 	I change the role of one simple group member to group manager
	 * Then		I should see the member marked as going to be updated next to it
	 * When		I click on save
	 * Then 	I should see a confirmation message saying that the group was edited
	 * And 		A notification should be sent to the user that was promoted group manager
	 * When 	I log in as the user who was promoted group manager
	 * And 		I go to the users workspace
	 * Then 	I should be able to add users to the new group that I manage
	 */
	public function testEditGroupPromoteMember() {
		$this->resetDatabaseWhenComplete();
		$promotedUser = User::get('ursula');

		// Given I am logged in as a group manager
		$user = User::get('ping');
		$this->setClientConfig($user);
		$this->loginAs($user);

		// When I am editing a group that I manage
		$group = Group::get(['id' => Uuid::get('group.id.human_resource')]);
		$this->gotoEditGroup($group['id']);

		// Then I should see the list of users that are part of this group in the edit group dialog
		// And I should see next to each user the role that he has in the group in a select box
		$groupMembers = [
			['user_id' => User::get('ping'), 'is_admin' => true],
			['user_id' => User::get('thelma'), 'is_admin' => true],
			['user_id' => User::get('ursula'), 'is_admin' => false],
			['user_id' => User::get('wang'), 'is_admin' => false],
		];
		foreach($groupMembers as $groupMember) {
			$this->assertGroupMemberInEditDialog($group['id'], $groupMember['user_id'], $groupMember['is_admin']);
		}

		// When I change the role of one simple group member to group manager
		$this->editTemporaryGroupUserRole($promotedUser, true);

		// Then I should see the member marked as going to be updated next to it
		$this->assertElementContainsText(
			$this->getTemporaryGroupUserElement($promotedUser),
			'Will be updated'
		);

		// When I click on save
		$this->click('.edit-group-dialog a.button.primary');

		// Then I should see a confirmation message saying that the group was edited
		$this->assertNotification('app_groups_edit_success');

		// @todo
		// And A notification should be sent to the user that was promoted group manager

		// When I log in as the user who was promoted group manager
		$this->logout();
		$this->setClientConfig($promotedUser);
		$this->loginAs($promotedUser);

		// And I go to the users workspace
		$this->gotoWorkspace('user');

		// Then I should be able to add users to the new group that I manage
		$this->gotoEditGroup($group['id']);
		$ada = User::get('ada');
		$this->searchGroupUserToAdd($ada, $promotedUser);
		$this->addTemporaryGroupUser($ada);
		$this->click('.edit-group-dialog a.button.primary');
		$this->assertNotification('app_groups_edit_success');
	}

	/**
	 * Scenario: As a group manager I cannot change the latest group manager role
	 *
	 * Given	I am logged in as group manager
	 * And		I am on the users workspace
	 * And		I edit a group
	 * When		I change all the members roles to member (except one admin)
	 * Then		I should not be able to change the role of this user
	 */
	public function testAtLeastOneGroupManager() {
		// Given I am logged in as a group manager
		$user = User::get('ping');
		$this->setClientConfig($user);
		$this->loginAs($user);

		// When I am editing a group that I manage
		$group = Group::get(['id' => Uuid::get('group.id.human_resource')]);
		$this->gotoEditGroup($group['id']);

		// When I change the role of one simple group member to group manager
		$this->editTemporaryGroupUserRole(User::get('thelma'), false);

		// Then I should not be able to change the role of this user
		$groupUserId = Uuid::get('group_user.id.human_resource-ping');
		$this->assertDisabled("#js_group_user_is_admin_$groupUserId");
	}

	/**
	 * Scenario: As a group manager I can add a user to a group using the edit group dialog
	 *
	 * Given	I am logged in as a group manager
	 * And		I am on the users workspace
	 * And		I edit a group
	 * When 	I add a member to the group
	 * Then		I should see that the user is added in the list of group members
	 * And		I should see that the list of users automatically scrolled down so I can see the last user that was added
	 * And		I should see that his group role is “group member”
	 * And		I should see a warning message saying that the changes will be applied after clicking on save
	 * When		I press the save button
	 * Then		I should see that the dialog disappears
	 * And		I should see a confirmation message saying that the group members have been edited
	 * When		I log in as the user that was newly added to the group
	 * And		I go to the users workspace
	 * Then		I filter the list of users with the group
	 */
	public function testAddGroupMember() {
		$this->resetDatabaseWhenComplete();

		// Given I am logged in as a group manager
		$user = User::get('ping');
		$this->setClientConfig($user);
		$this->loginAs($user);

		// And I am on the users workspace
		// When I am editing a group that I manage
		$group = Group::get(['id' => Uuid::get('group.id.human_resource')]);
		$this->gotoEditGroup($group['id']);

		// When I add a member to the group
		$ada = User::get('ada');
		$this->searchGroupUserToAdd($ada, $user);
		$this->addTemporaryGroupUser($ada);

		// Then I should see that the user is added in the list of group members
		// And I should see that his group role is “group member”
		$this->assertGroupMemberInEditDialog($group['id'], $ada);

		// And I should see a warning message saying that the changes will be applied after clicking on save
		$this->assertElementContainsText(
			$this->getTemporaryGroupUserElement($ada),
			'Will be added'
		);

		// When I press the save button
		$this->click('.edit-group-dialog a.button.primary');

		// Then I should see that the dialog disappears
		$this->waitUntilIDontSee('.edit-group-dialog');

		// And I should see a confirmation message saying that the group members have been edited
		$this->assertNotification('app_groups_edit_success');

		// When I log in as the user that was newly added to the group
		$this->logout();
		$this->setClientConfig($ada);
		$this->loginAs($ada);

		// And I go to the users workspace
		$this->gotoWorkspace('user');

		// And I filter the list of users with the group
		$this->clickGroup($group['id']);

		// Then I should see me in the list
		$this->assertElementContainsText(
			$this->findByCss('#js_wsp_users_browser .tableview-content'),
			$ada['FirstName'] . ' ' . $ada['LastName']
		);
	}

	/**
	 * Scenario: As an group manager I can remove a user from a group I manage using the edit group dialog
	 *
	 * Given 	I am logged in as a group manager
	 * And 		I am editing a group I manager
	 * When 	I observe the content of the edit group dialog
	 * And 		I should see that next to each group member there is a cross icon to remove the membership
	 * When 	I click on the cross next to the user I want to remove
	 * Then 	I should see that the user disappears from the list of group members
	 * And 		I should see a warning message saying that the changes will be applied only after save
	 * When 	I press the “save” button
	 * Then 	I should see that the dialog disappears
	 * And 		I should see a confirmation message
	 * When 	I log in as the user that was removed from the group
	 * And 		I go to the users workspace
	 * And 		I filter by the group the user has been removed
	 * Then 	I filter by the group the user has been removed
	 */
	public function testRemoveGroupMember() {
		$this->resetDatabaseWhenComplete();
		$removedUser = User::get('wang');

		// Given I am logged in as an group manager
		$user = User::get('ping');
		$this->setClientConfig($user);
		$this->loginAs($user);

		// And I am editing a group I manage
		$group = Group::get(['id' => Uuid::get('group.id.human_resource')]);
		$this->gotoEditGroup($group['id']);

		// When I observe the content of the edit group dialog
		// And I should see that next to each group member there is a cross icon to remove the membership
		$this->assertVisible('.js_group_user_delete');

		// When I click on the cross next to the user I want to remove
		$groupUserId = Uuid::get('group_user.id.human_resource-wang');
		$this->click("#js_group_user_delete_$groupUserId");

		// Then I should see that the user disappears from the list of group members
		$this->assertNotVisible("#$groupUserId");

		// @todo
		// And I should see a warning message saying that the changes will be applied only after save

		// When I press the “save” button
		$this->click('.edit-group-dialog a.button.primary');

		// And I should see that the dialog disappears
		$this->waitUntilIDontSee('.edit-group-dialog');

		// And I should see a confirmation message saying that the group members have been edited
		$this->assertNotification('app_groups_edit_success');

		// When I log in as the user that was removed from the group
		$this->logout();
		$this->setClientConfig($removedUser);
		$this->loginAs($removedUser);

		// And I go to the users workspace
		$this->gotoWorkspace('user');

		// And I filter by the group the user has been removed
		$this->clickGroup($group['id']);

		// Then I filter by the group the user has been removed
		$this->assertElementNotContainText(
			$this->findByCss('#js_wsp_users_browser .tableview-content'),
			$removedUser['FirstName'] . ' ' . $removedUser['LastName']
		);
	}

}
