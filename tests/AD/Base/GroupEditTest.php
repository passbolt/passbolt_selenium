<?php
/**
 * Passbolt ~ Open source password manager for teams
 * Copyright (c) Passbolt SARL (https://www.passbolt.com)
 *
 * Licensed under GNU Affero General Public License version 3 of the or any later version.
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Passbolt SARL (https://www.passbolt.com)
 * @license   https://opensource.org/licenses/AGPL-3.0 AGPL License
 * @link      https://www.passbolt.com Passbolt(tm)
 * @since     2.0.0
 */
/**
 * Feature: As an administrator I can edit groups
 *
 * Scenarios :
 *  - As an administrator I can edit a group using the right click contextual menu
 *  - As an administrator I can edit the group name
 *  - As an administrator I can edit a group from the sidebar
 *  - As an administrator I cannot add people to a group I am not a group manager of
 *  - As an administrator I can't change a group with a name for a name already used by another group
 *  - As a user I should receive a notification when I am deleted from a group
 *  - As a group member I should receive a notification when my role in the group has changed
 *  - As a group manager I should receive a notification when admin updated the members of a group I manage
 *  - As an administrator I can edit the existing group members and promote a group member to group manager
 */
namespace Tests\AD\Base;

use App\Actions\GroupActionsTrait;
use App\Actions\PasswordActionsTrait;
use App\Actions\SidebarActionsTrait;
use App\Actions\WorkspaceActionsTrait;
use App\Assertions\GroupAssertionsTrait;
use App\Assertions\PasswordAssertionsTrait;
use App\Assertions\WorkspaceAssertionsTrait;
use App\PassboltTestCase;
use App\Lib\UuidFactory;
use Data\Fixtures\User;
use Data\Fixtures\Group;

class ADGroupEditTest extends PassboltTestCase
{
    use GroupActionsTrait;
    use GroupAssertionsTrait;
    use PasswordActionsTrait;
    use PasswordAssertionsTrait;
    use SidebarActionsTrait;
    use WorkspaceActionsTrait;
    use WorkspaceAssertionsTrait;

    /**
     * Scenario: As an administrator I can edit a group using the right click contextual menu
     *
     * Given I am logged in as an administrator and I am on the users workspace
     * When  I click on the contextual menu button of a group on the right
     * Then  I should see the group contextual menu
     * And   I should see the “Edit group” option
     * When  I click on “Edit group”
     * Then  I should see the Edit group dialog
     *
     * @group AD
     * @group group
     * @group edit
     * @group v2
     */
    public function testEditGroupRightClick() 
    {
        // Given I am logged in as an administrator
        $user = User::get('admin');
        
        $this->loginAs($user);
        $this->gotoWorkspace('user');

        // When I click on the contextual menu button of a group on the right
        $groupId = UuidFactory::uuid('group.id.ergonom');
        $groupElement = $this->find("#group_$groupId");
        $this->driver->getMouse()->mouseMove($groupElement->getCoordinates());
        $this->click("#group_$groupId .right-cell a");

        // Then I should see the group contextual menu
        $this->assertVisible('js_contextual_menu');
        $this->assertVisible('js_group_browser_menu_edit');

        // When I click on “Edit group”
        $this->click("#js_contextual_menu #js_group_browser_menu_edit a");

        // Then I should see the Edit group dialog
        $this->waitUntilISee('.edit-group-dialog');
    }

    /**
     * Scenario: As an administrator I can edit the group name
     *
     * Given I am logged in as administrator
     * And   I am editing a group
     * When  I observe the content of the edit group dialog
     * Then  I should see a “group name” field containing the current group name.
     * When  I modify the group name
     * And   I click on “save”
     * Then  I should see that the dialog disappears
     * And   I should see a confirmation message saying that the group has been edited
     * And   I should see that the group name has been changed in the groups list
     *
     * @group AD
     * @group group
     * @group edit
     * @group v2
     */
    public function testEditGroupName() 
    {
        $this->resetDatabaseWhenComplete();

        // Given I am logged in as an administrator
        $user = User::get('admin');
        
        $this->loginAs($user);
        $group = Group::get(['id' => UuidFactory::uuid('group.id.ergonom')]);
        $this->gotoEditGroup($group['id']);

        // When	I observe the content of the edit group dialog
        // Then	I should see a “group name” field containing the current group name.
        $this->assertInputValue('js_field_name', $group['name']);

        // When	I modify the group name
        $groupNameUpdate = $group['name'] . ' UPDATED';
        $this->inputText('js_field_name', $groupNameUpdate);

        // And I click on “save”
        $this->click('.edit-group-dialog a.button.primary');

        // Then	I should see that the dialog disappears
        $this->waitUntilIDontSee('.edit-group-dialog');

        // And I should see a confirmation message saying that the group has been edited
        $this->assertNotification('app_groups_edit_success');

        // And I should see that the group name has been changed in the groups list
        $this->waitUntilISee('#js_wsp_users_groups_list', '/' . $groupNameUpdate . '/');
    }

    /**
     * Scenario: As an administrator I can edit a group from the sidebar
     *
     * Given I am logged in as administrator
     * And   I am on the user workspace
     * And   I should see a “edit” button next to the Information section
     * When  I press the “Edit” button
     * Then  I should see the Edit group dialog
     *
     * @group AD
     * @group group
     * @group edit
     * @group v2
     */
    public function testEditGroupFromSidebar() 
    {
        // Given I am logged in as an administrator
        $user = User::get('admin');
        
        $this->loginAs($user);
        $this->gotoWorkspace('user');

        // When I click a group name
        $group = Group::get(['id' => UuidFactory::uuid('group.id.ergonom')]);
        $this->clickGroup($group['id']);

        // Then I should see a “edit” button next to the Information section
        $this->clickSecondarySidebarSectionHeader('members');
        $editButtonSelector = '#js_group_details #js_group_details_members #js_edit_members_button';
        $this->waitUntilISee($editButtonSelector);

        // When I press the “Edit” button
        $this->click($editButtonSelector);

        // Then I should see the Edit group dialog
        $this->waitUntilISee('.edit-group-dialog');
    }

    /**
     * Scenario: As an administrator I cannot add people to a group I am not a group manager of.
     *
     * Given I am logged in as administrator
     * And   I am editing a group that I am not the group manager of
     * When  I observe the content of the edit group dialog
     * Then  I should not see a Add people section
     * And     I should see a warning message saying that "Only the group manager can add new people to a group."
     *
     * @group AD
     * @group group
     * @group edit
     * @group v2
     */
    public function testEditGroupAsNotGroupManager() 
    {
        // Given I am logged in as an administrator
        $user = User::get('admin');
        
        $this->loginAs($user);
        $group = Group::get(['id' => UuidFactory::uuid('group.id.accounting')]);
        $this->gotoEditGroup($group['id']);

        // And I shouldn't see the Add people iframe.
        $this->assertNotVisibleByCss('#js_group_members_add #passbolt-iframe-group-edit');

        // And I see a warning message saying that only the group manager can add new people to a group.
        $this->assertElementContainsText('#js_group_members .message.warning', 'Only the group manager can add new people to a group.');
    }

    /**
     * Scenario: As an administrator I can't change a group with a name for a name already used by another group.
     * Given that   I am logged in as an administrator
     * And   I am on the users workspace
     * When  I edit the "accounting" group
     * And   I fill 'Board' for name
     * And   I click on submit
     * Then  I should see a notification saying that the group couldn't be updated
     * And   I should see an error message under the name field
     * And   I should see that this error message says that the group name is already in use
     *
     * @group AD
     * @group group
     * @group edit
     * @group v2
     */
    public function testEditGroupNameValidation() 
    {
        // Given I am logged in as an administrator
        $user = User::get('admin');
        
        $this->loginAs($user);
        $group = Group::get(['id' => UuidFactory::uuid('group.id.accounting')]);

        // When I edit a the group "accounting".
        $this->gotoEditGroup($group['id']);

        // When I enter board as a name (the name is already used).
        $this->inputText('js_field_name', $group = Group::get(['id' => UuidFactory::uuid('group.id.board')])['name']);

        // And I click on “save”
        $this->click('.edit-group-dialog a.button.primary');

        // Then I should see a success notification message saying the group couldn't be created
        $this->assertNotification('app_groups_add_error');

        // Then I see an error message saying that the name contain invalid characters
        $this->waitUntilISee('#js_field_name_feedback.error.message');

        $this->assertElementContainsText(
            $this->find('js_field_name_feedback'), 'The name provided is already used by another group.'
        );
    }

    /**
     * Scenario: As a user I should receive a notification when I am deleted from a group
     *
     * Given I am logged in as an admin
     * And   I am on the users workspace
     * And   I am editing a group
     * When  I remove a user from the group
     * And   I click on save
     * Then  I should see a success notification message
     * When  I access last email sent to the user
     * Then  I should see the expected email title
     * And   I should see the expected email content
     *
     * @group AD
     * @group group
     * @group edit
     * @group v2
     * @group skip
     * @group email
     */
    public function testEditGroupDeleteUserEmailNotification() 
    {
        $this->resetDatabaseWhenComplete();

        // Given I am logged in as an admin
        $user = User::get('admin');
        
        $this->loginAs($user);

        // And I am on the users workspace
        // And I am editing a group
        $group = Group::get(['id' => UuidFactory::uuid('group.id.accounting')]);
        $this->gotoEditGroup($group['id']);

        // When I remove a user from the group
        $grace = User::get('grace');
        $groupUserId = UuidFactory::uuid('group_user.id.accounting-grace');
        $this->click("#js_group_user_delete_$groupUserId");

        // And I click save.
        $this->click('.edit-group-dialog a.button.primary');

        // And I should see a success notification message
        $this->assertNotification('app_groups_edit_success');

        // When I access last email sent to the group manager.
        $this->getUrl('seleniumtests/showlastemail/' . $grace['Username']);

        // Then I should see the expected email title
        $this->assertMetaTitleContains(sprintf('%s removed you from the group %s', $user['FirstName'], $group['name']));

        // And I should see the expected email content
        $this->assertElementContainsText('bodyTable', 'You are no longer a member of this group');
    }

    /**
     * Scenario: As a group member I should receive a notification when my role in the group has changed
     *
     * Given I am logged in as a group manager
     * And   I am on the users workspace
     * And   I am editing a group that I manage
     * When  I change a user role to group manager
     * And   I change a user role to member
     * And   I click on save
     * Then  I should see a success notification message
     * When  When I access last email sent to the group manager
     * Then  I should see the expected email
     * When  When I access last email sent to the member
     * Then  I should see the expected email
     *
     * @group AD
     * @group group
     * @group edit
     * @group v2
     * @group skip
     * @group email
     */
    public function testEditGroupUpdateUserEmailNotification() 
    {
        $this->resetDatabaseWhenComplete();

        // Given I am a group manager.
        $user = User::get('admin');
        

        // I am logged in as admin
        $this->loginAs($user);

        // And I am on the users workspace
        // And I am editing a group that I manage
        $group = Group::get(['id' => UuidFactory::uuid('group.id.human_resource')]);
        $this->gotoEditGroup($group['id']);

        // When I change a user role to group manager
        $userW = User::get('wang');
        $this->editTemporaryGroupUserRole($userW, true);

        // And I change a user role to member
        $userT = User::get('thelma');
        $this->editTemporaryGroupUserRole($userT, false);

        // And I click save.
        $this->click('.edit-group-dialog a.button.primary');

        // And I should see a success notification message
        $this->assertNotification('app_groups_edit_success');

        // When I access last email sent to the group manager.
        $this->getUrl('seleniumtests/showlastemail/' . $userW['Username']);

        // Then I should see the expected email
        $this->assertMetaTitleContains(sprintf('%s updated your membership in the group %s', $user['FirstName'], $group['name']));
        $this->assertElementContainsText('bodyTable', "{$user['FirstName']} ({$user['Username']})");
        $this->assertElementContainsText('bodyTable', sprintf('updated your membership in the group %s', $group['name']));
        $this->assertElementContainsText('bodyTable', 'You are now a group manager of this group');

        // When I access last email sent to the member
        $this->getUrl('seleniumtests/showlastemail/' . $userT['Username']);

        // Then I should see the expected email
        $this->assertMetaTitleContains(sprintf('%s updated your membership in the group %s', $user['FirstName'], $group['name']));
        $this->assertElementContainsText('bodyTable', "{$user['FirstName']} ({$user['Username']})");
        $this->assertElementContainsText('bodyTable', sprintf('updated your membership in the group %s', $group['name']));
        $this->assertElementContainsText('bodyTable', 'You are no longer a group manager of this group');
    }

    /**
     * Scenario: As a group manager I should receive a notification when admin updated the members of a group I manage
     *
     * Given I am logged in as a group manager
     * And   I am on the users workspace
     * And   I am editing a group that I manage
     * When  I add some users to a group
     * And   I remove some users from the group
     * And   I update the role of some users
     * And   I click on save
     * Then  I should see a success notification message
     * When  I access last email sent to me
     * Then  I should not see any email
     * When  I access last email sent to the other group manager
     * Then  I should see the expected email title
     * And   I should see the expected email content
     *
     * @group AD
     * @group group
     * @group edit
     * @group v2
     * @group skip
     * @group email
     */
    public function testEditGroupGroupUpdatedSummaryEmailNotification()
    {
        $this->resetDatabaseWhenComplete();

        // Given I am an administrator.
        $user = User::get('admin');

        // I am logged in as admin
        $this->loginAs($user);

        // And I am on the users workspace
        // And I am editing a group that I manage
        $group = Group::get(['id' => UuidFactory::uuid('group.id.human_resource')]);
        $this->gotoEditGroup($group['id']);

        // When I remove some users from the group
        $ursula = User::get('ursula');
        $groupUserId = UuidFactory::uuid('group_user.id.human_resource-ursula');
        $this->click("#js_group_user_delete_$groupUserId");

        // And I update the role of some users
        $wang = User::get('wang');
        $this->editTemporaryGroupUserRole($wang, true);

        // And I click save.
        $this->click('.edit-group-dialog a.button.primary');

        // And I should see a success notification message
        $this->assertNotification('app_groups_edit_success');

        // When I access last email sent to me
        $this->getUrl('seleniumtests/showlastemail/' . $user['Username']);

        // Then I shouldn't see any email
        $this->assertElementContainsText('body', 'No email was sent to this user');

        // When I access last email sent to the other group managers
        $groupManagers[] = User::get('ping');
        $groupManagers[] = User::get('thelma');
        foreach ($groupManagers as $groupManager) {
            $this->getUrl('seleniumtests/showlastemail/' . $groupManager['Username']);

            // Then I should see the expected email title
            $this->assertMetaTitleContains(sprintf('%s updated the group %s', $user['FirstName'], $group['name']));

            // And I should see the expected email content
            $this->assertElementContainsText('bodyTable', "{$user['FirstName']} ({$user['Username']})");
            $this->assertElementContainsText('bodyTable', sprintf('updated the group %s', $group['name']));
            $this->assertElementNotContainText('bodyTable', 'Added members');
            $this->assertElementContainsText('bodyTable', 'Removed members');
            $this->assertElementContainsText('#deleted_users', "{$ursula['FirstName']} {$ursula['LastName']} (Member)");
            $this->assertElementContainsText('bodyTable', 'Updated roles');
            $this->assertElementContainsText('#updated_roles', "{$wang['FirstName']} {$wang['LastName']} is now group manager");
        }
    }

    /**
     * Scenario: As an administrator I can edit the existing group members and promote a group member to group manager
     *
     * Given I am logged in as administrator
     * And   I am on the users workspace
     * When I’m editing a group
     * Then  I should see the list of users that are part of this group in the edit group dialog
     * And   I should see next to each user the role that he has in the group in a select box
     * When  I change the role of one simple group member to group manager
     * Then  I should see the member marked as going to be updated next to it
     * And   I should see a warning message saying that the changes will be applied after clicking on save
     * When  I click on save
     * Then  I should see a confirmation message saying that the group was edited
     * And         A notification should be sent to the user that was promoted group manager
     * When  I log in as the user who was promoted group manager
     * And   I go to the users workspace
     * Then  I should be able to add users to the new group that I manage
     *
     * @group AD
     * @group group
     * @group edit
     * @group v2
     */
    public function testEditGroupPromoteMember() 
    {
        $this->resetDatabaseWhenComplete();
        $promotedUser = User::get('ursula');

        // Given I am logged in as a group manager
        $user = User::get('admin');
        
        $this->loginAs($user);

        // When I am editing a group that I manage
        $group = Group::get(['id' => UuidFactory::uuid('group.id.human_resource')]);
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

        // And I see a warning message saying that I need to save changes before they can take effect.
        $this->assertElementContainsText('#js_group_members .message.warning', 'You need to click save for the changes to take place.');

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
     * Scenario: As an administrator I cannot change the latest group manager role
     *
     * Given I am logged in as administrator
     * And   I am on the users workspace
     * And   I edit a group
     * When  I change all the members roles to member (except one admin)
     * Then  I should not be able to change the role of this user
     *
     * @group AD
     * @group group
     * @group edit
     * @group v2
     */
    public function testAtLeastOneGroupManager() 
    {
        // Given I am logged in as a group manager
        $user = User::get('admin');
        
        $this->loginAs($user);

        // When I am editing a group that I manage
        $group = Group::get(['id' => UuidFactory::uuid('group.id.human_resource')]);
        $this->gotoEditGroup($group['id']);

        // When I change the role of one simple group member to group manager
        $this->editTemporaryGroupUserRole(User::get('thelma'), false);

        // Then I should not be able to change the role of this user
        $groupUserId = UuidFactory::uuid('group_user.id.human_resource-ping');
        $this->waitUntilDisabled("js_group_user_is_admin_$groupUserId");
    }

    /**
     * Scenario: As an administrator I can remove a user from a group I manage using the edit group dialog
     *
     * Given I am logged in as administrator
     * And   I am editing a group I manager
     * When  I observe the content of the edit group dialog
     * And   I should see that next to each group member there is a cross icon to remove the membership
     * When  I click on the cross next to the user I want to remove
     * Then  I should see that the user disappears from the list of group members
     * And   I should see a warning message saying that the changes will be applied only after save
     * When  I press the “save” button
     * Then  I should see that the dialog disappears
     * And   I should see a confirmation message
     * When  I log in as the user that was removed from the group
     * And   I go to the users workspace
     * And   I filter by the group the user has been removed
     * Then  I filter by the group the user has been removed
     *
     * @group AD
     * @group group
     * @group edit
     * @group v2
     */
    public function testRemoveGroupMember() 
    {
        $this->resetDatabaseWhenComplete();
        $removedUser = User::get('wang');

        // Given I am logged in as an group manager
        $user = User::get('admin');
        
        $this->loginAs($user);

        // And I am editing a group I manage
        $group = Group::get(['id' => UuidFactory::uuid('group.id.human_resource')]);
        $this->gotoEditGroup($group['id']);

        // When I observe the content of the edit group dialog
        // And I should see that next to each group member there is a cross icon to remove the membership
        $this->assertVisibleByCss('.js_group_user_delete');

        // When I click on the cross next to the user I want to remove
        $groupUserId = UuidFactory::uuid('group_user.id.human_resource-wang');
        $this->click("#js_group_user_delete_$groupUserId");

        // Then I should see that the user disappears from the list of group members
        $this->assertNotVisibleByCss("#$groupUserId");

        // And I should see a warning message saying that the changes will be applied only after save
        $this->assertElementContainsText('#js_group_members .message.warning', 'You need to click save for the changes to take place.');

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