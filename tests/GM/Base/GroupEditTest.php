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
 * Feature: As a group manager I can edit groups
 *
 * Scenarios :
 *  - As a group manager I can edit a group using the right click contextual menu
 *  - As a group manager I shouldn't be able to edit a group I don't manager from the contextual menu
 *  - As a group manager As a GM I can edit a group from the sidebar
 *  - As a group manager I shouldn’t be able to edit the group name
 *  - As a group manager I can edit the existing group members and promote a group member to group manager
 *  - As a group manager I cannot change the latest group manager role
 *  - As a group manager I can add a user to a group that doesn't have any password using the edit group dialog
 *  - As a group manager I can add a user to a group that accesses passwords using the edit group dialog
 *  - As an group manager I can remove a user from a group I manage using the edit group dialog
 *  - As a user I should receive a notification when I am added to a group
 *  - As a user I should receive a notification when I am deleted from a group
 *  - As a group member I should receive a notification when my role in the group has changed
 *  - As a group manager I should receive a notification when another group manager updated the members of a group I manage
 */
namespace Tests\GM\Base;

use App\Actions\GroupActionsTrait;
use App\Actions\MasterPasswordActionsTrait;
use App\Actions\PasswordActionsTrait;
use App\Actions\WorkspaceActionsTrait;
use App\Assertions\ClipboardAssertions;
use App\Assertions\GroupAssertionsTrait;
use App\Assertions\MasterPasswordAssertionsTrait;
use App\Assertions\WorkspaceAssertionsTrait;
use App\Lib\UuidFactory;
use App\PassboltTestCase;
use Data\Fixtures\User;
use Data\Fixtures\Group;
use Data\Fixtures\Resource;

class GMGroupEditTest extends PassboltTestCase
{
    use ClipboardAssertions;
    use GroupActionsTrait;
    use GroupAssertionsTrait;
    use MasterPasswordAssertionsTrait;
    use MasterPasswordActionsTrait;
    use PasswordActionsTrait;
    use WorkspaceActionsTrait;
    use WorkspaceAssertionsTrait;

    /**
     * Scenario: As a group manager I can edit a group using the right click contextual menu
     *
     * Given I am logged in as a group manager and I am on the users workspace
     * When  I click on the contextual menu button of a group on the right
     * Then  I should see the group contextual menu
     * And   I should see the “Edit group” option
     * When  I click on “Edit group”
     * Then  I should see the Edit group dialog
     *
     * @group GM
     * @group group
     * @group edit
     */
    public function testEditGroupRightClick() 
    {
        // Given I am logged in as an administrator
        $user = User::get('irene');

        $this->loginAs($user);
        $this->gotoWorkspace('user');

        // When I click on the contextual menu button of a group on the right
        $groupId = UuidFactory::uuid('group.id.ergonom');
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
     * Scenario: As a group manager I shouldn't be able to edit groups from the users workspace
     *
     * Given I am a group manager
     * And   I am on the user workspace
     * When  I select a group
     * Then  I should see that there is no dropdown button next to the groups
     *
     * @group GM
     * @group group
     * @group edit
     */
    public function testCantEditGroup() 
    {
        // Given I am a group manager
        $user = User::get('ping');


        // I am logged in as admin
        $this->loginAs($user);

        // I am on the user workspace
        $this->gotoWorkspace('user');

        // When I select a group
        $group = Group::get(['id' => UuidFactory::uuid('group.id.ergonom')]);
        $this->clickGroup($group['id']);

        // Then I should see that there is no dropdown button next to the groups
        $this->assertNotVisible("#group_{$group['id']} .right-cell a");
    }

    /**
     * Scenario: As a GM I can edit a group from the sidebar
     *
     * Given I am logged in as administrator
     * And   I am on the user workspace
     * And   I should see a “edit” button next to the Information section
     * When  I press the “Edit” button
     * Then  I should see the Edit group dialog
     *
     * @group GM
     * @group group
     * @group edit
     */
    public function testEditGroupFromSidebar() 
    {
        // Given I am logged in as an administrator
        $user = User::get('irene');

        $this->loginAs($user);
        $this->gotoWorkspace('user');

        // When I click a group name
        $group = Group::get(['id' => UuidFactory::uuid('group.id.ergonom')]);
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
     * Given I am logged in as administrator
     * And   I am on the user workspace
     * When  I click a group name
     * And   I should not see a “edit” button next to the Information section
     *
     * @group GM
     * @group group
     * @group edit
     */
    public function testCantEditGroupDontManageFromSidebar() 
    {
        // Given I am logged in as an administrator
        $user = User::get('irene');

        $this->loginAs($user);
        $this->gotoWorkspace('user');

        // When I click a group name
        $group = Group::get(['id' => UuidFactory::uuid('group.id.accounting')]);
        $this->clickGroup($group['id']);

        // Then I should not see a “edit” button next to the Information section
        $editButtonSelector = '#js_group_details #js_group_details_members #js_edit_members_button';
        $this->assertNotVisible($editButtonSelector);
    }

    /**
     * Scenario: As a group manager I shouldn’t be able to edit the group name
     *
     * Given I am logged in as a group manager
     * And   I am editing a group that I manage
     * When  I observe the content of the edit group dialog
     * Then  I should see a “group name” field containing the current group name.
     * And   I should see that the group name field is disabled.
     *
     * @group GM
     * @group group
     * @group edit
     */
    public function testCannotEditGroupName() 
    {
        // Given I am logged in as a group manager
        $user = User::get('irene');

        $this->loginAs($user);
        // And I am editing a group that I manage
        $group = Group::get(['id' => UuidFactory::uuid('group.id.ergonom')]);
        $this->gotoEditGroup($group['id']);

        // When	I observe the content of the edit group dialog
        // Then	I should see a “group name” field containing the current group name.
        $this->assertInputValue('js_field_name', $group['name']);

        // And I should see that the group name field is disabled.
        $this->waitUntilDisabled('js_field_name');
    }

    /**
     * Scenario: As a group manager I can edit the existing group members and promote a group member to group manager
     *
     * Given I am logged in as a group manager
     * And   I am on the users workspace
     * When  I’m editing a group
     * Then  I should see the list of users that are part of this group in the edit group dialog
     * And   I should see next to each user the role that he has in the group in a select box
     * When  I change the role of one simple group member to group manager
     * Then  I should see the member marked as going to be updated next to it
     * And   I should see a warning message saying that the changes will be applied after clicking on save
     * When  I click on save
     * Then  I should see a confirmation message saying that the group was edited
     * And   A notification should be sent to the user that was promoted group manager
     * When  I log in as the user who was promoted group manager
     * And   I go to the users workspace
     * Then  I should be able to add users to the new group that I manage
     *
     * @group GM
     * @group group
     * @group edit
     */
    public function testEditGroupPromoteMember() 
    {
        $this->resetDatabaseWhenComplete();
        $promotedUser = User::get('ursula');

        // Given I am logged in as a group manager
        $user = User::get('ping');

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
     * Scenario: As a group manager I cannot change the latest group manager role
     *
     * Given I am logged in as group manager
     * And   I am on the users workspace
     * And   I edit a group
     * When  I change all the members roles to member (except one admin)
     * Then  I should not be able to change the role of this user
     *
     * @group GM
     * @group group
     * @group edit
     */
    public function testAtLeastOneGroupManager() 
    {
        // Given I am logged in as a group manager
        $user = User::get('ping');

        $this->loginAs($user);

        // When I am editing a group that I manage
        $group = Group::get(['id' => UuidFactory::uuid('group.id.human_resource')]);
        $this->gotoEditGroup($group['id']);

        // When I change the role of one simple group member to group manager
        $this->editTemporaryGroupUserRole(User::get('thelma'), false);

        // Then I should not be able to change the role of this user
        $groupUserId = UuidFactory::uuid('group_user.id.human_resource-ping');
        $this->waitUntilDisabled("#js_group_user_is_admin_$groupUserId");
    }

    /**
     * Scenario: As a group manager I can add a user to a group that doesn't access any password using the edit group dialog
     *
     * Given I am logged in as a group manager
     * And   I am on the users workspace
     * And   I edit a group
     * When  I add a member to the group
     * Then  I should see that the user is added in the list of group members
     * And   I should see that the list of users automatically scrolled down so I can see the last user that was added
     * And   I should see that his group role is “group member”
     * And   I should see a warning message saying that the changes will be applied after clicking on save
     * When  I press the save button
     * Then  I should see that the dialog disappears
     * And   I should see a confirmation message saying that the group members have been edited
     * When  I log in as the user that was newly added to the group
     * And   I go to the users workspace
     * Then  I filter the list of users with the group
     *
     * @group GM
     * @group group
     * @group edit
     */
    public function testAddGroupMemberWithoutPasswordsEncryption() 
    {
        $this->resetDatabaseWhenComplete();

        // Given I am logged in as a group manager
        $user = User::get('ping');

        $this->loginAs($user);

        // And I am on the users workspace
        // When I am editing a group that I manage
        $group = Group::get(['id' => UuidFactory::uuid('group.id.human_resource')]);
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

        // And I see a warning message saying that I need to save changes before they can take effect.
        $this->assertElementContainsText('#js_group_members .message.warning', 'You need to click save for the changes to take place.');

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
     * Scenario: As a group manager I can add a user to a group that accesses passwords using the edit group dialog
     *
     * Given I am logged in as a group manager
     * And   I am on the users workspace
     * And   I edit a group
     * When  I add a member to the group
     * Then  I should see that the user is added in the list of group members
     * And   I should see that the list of users automatically scrolled down so I can see the last user that was added
     * And   I should see that his group role is “group member”
     * And   I should see a warning message saying that the changes will be applied after clicking on save
     * When  I press the save button
     * Then  I should see that the dialog disappears
     * And   I should see a confirmation message saying that the group members have been edited
     * When  I log in as the user that was newly added to the group
     * And   I go to the passwords workspace
     * Then  I should see that the group passwords are now accessible
     * When  I click on the "chai" password
     * And   I click on the button "copy password to clipboard"
     * And   I enter the appropriate master key
     * Then  I should see that the password copied in the clipboard is the one corresponding to chai
     * When  I go to the users workspace
     * And   I filter the list of users with the group
     * Then  I should see that Ping appears in the list of group members.
     *
     * @group GM
     * @group group
     * @group edit
     */
    public function testAddGroupMemberWithPasswordsEncryption() 
    {
        $this->resetDatabaseWhenComplete();

        // Given I am logged in as a group manager
        $user = User::get('irene');

        $this->loginAs($user);

        // And I am on the users workspace
        // When I am editing a group that I manage
        $group = Group::get(['id' => UuidFactory::uuid('group.id.developer')]);
        $this->gotoEditGroup($group['id']);

        // When I add a member to the group
        $ping = User::get('ping');
        $this->searchGroupUserToAdd($ping, $user);
        $this->addTemporaryGroupUser($ping);

        // Then I should see that the user is added in the list of group members
        // And I should see that his group role is “group member”
        $this->assertGroupMemberInEditDialog($group['id'], $ping);

        // And I should see a warning message saying that the changes will be applied after clicking on save
        $this->assertElementContainsText(
            $this->getTemporaryGroupUserElement($ping),
            'Will be added'
        );

        // And I see a warning message saying that I need to save changes before they can take effect.
        $this->assertElementContainsText('#js_group_members .message.warning', 'You need to click save for the changes to take place.');

        // When I press the save button
        $this->click('.edit-group-dialog a.button.primary');

        $this->assertMasterPasswordDialog($user);
        $this->enterMasterPassword($user['MasterPassword']);

        // Then I should see that the dialog disappears
        $this->waitUntilIDontSee('.edit-group-dialog');

        // And I should see a confirmation message saying that the group members have been edited
        $this->assertNotification('app_groups_edit_success');

        // When I log in as the user that was newly added to the group
        $this->logout();
        $this->setClientConfig($ping);
        $this->loginAs($ping);

        // When I click on enligthenment, a newly accessible password.
        $resource = Resource::get(
            array(
            'user' => 'ada',
            'id' => UuidFactory::uuid('resource.id.enlightenment')
            )
        );
        $this->clickPassword($resource['id']);

        // When I click on the link 'copy password'
        $this->click('js_wk_menu_secretcopy_button');

        // Then I can see the master key dialog
        $this->assertMasterPasswordDialog($ping);

        // When I enter my passphrase and click submit
        $this->enterMasterPassword($ping['MasterPassword']);

        // Then I can see a success message telling me the password was copied to clipboard
        $this->assertNotification('plugin_clipboard_copy_success');

        // And the content of the clipboard is valid
        $this->assertClipboard($resource['password']);

        // And I go to the users workspace
        $this->gotoWorkspace('user');

        // And I filter the list of users with the group
        $this->clickGroup($group['id']);

        // Then I should see me in the list
        $this->assertElementContainsText(
            $this->findByCss('#js_wsp_users_browser .tableview-content'),
            $ping['FirstName'] . ' ' . $ping['LastName']
        );
    }

    /**
     * Scenario: As an group manager I can remove a user from a group I manage using the edit group dialog
     *
     * Given I am logged in as a group manager
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
     * @group GM
     * @group group
     * @group edit
     */
    public function testRemoveGroupMember() 
    {
        $this->resetDatabaseWhenComplete();
        $removedUser = User::get('wang');

        // Given I am logged in as an group manager
        $user = User::get('ping');

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
        $this->assertNotVisible("#$groupUserId");

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

    /**
     * Scenario: As a user I should receive a notification when I am added to a group
     *
     * Given I am logged in as a group manager
     * And   I am on the users workspace
     * And   I am editing a group that I manage
     * When  I add Ada as group manager
     * And   I add Betty as member
     * And   I click on save
     * Then  I should see a success notification message
     * When  I access last email sent to the group manager
     * Then  I should see the expected email title
     * And   I should see the expected email content
     * When  I access last email sent to the group member
     * Then  I should see the expected email title
     * And   I should see the expected email content
     *
     * @group GM
     * @group group
     * @group edit
     */
    public function testEditGroupAddUserEmailNotification() 
    {
        $this->resetDatabaseWhenComplete();

        // Given I am a group manager.
        $user = User::get('frances');


        // I am logged in as admin
        $this->loginAs($user);

        // And I am on the users workspace
        // And I am editing a group that I manage
        $group = Group::get(['id' => UuidFactory::uuid('group.id.accounting')]);
        $this->gotoEditGroup($group['id']);

        // When I add Ada as group manager
        $ada = User::get('ada');
        $this->searchGroupUserToAdd($ada, $user);
        $this->addTemporaryGroupUser($ada);
        $this->editTemporaryGroupUserRole($ada, true);

        // And I add Betty as member
        $betty = User::get('betty');
        $this->searchGroupUserToAdd($betty, $user);
        $this->addTemporaryGroupUser($betty);

        // And I click save.
        $this->click('.edit-group-dialog a.button.primary');

        // And I should see a success notification message
        $this->assertNotification('app_groups_edit_success');

        // When I access last email sent to the group manager.
        $this->getUrl('seleniumtests/showlastemail/' . $ada['Username']);

        // Then I should see the expected email title
        $this->assertMetaTitleContains(sprintf('%s added you to the group %s', $user['FirstName'], $group['name']));

        // And I should see the expected email content
        $this->assertElementContainsText('bodyTable', 'Name: ' . $group['name']);
        $this->assertElementContainsText('bodyTable', 'Your role: Group manager');
        $this->assertElementContainsText('bodyTable', 'As member of the group');
        $this->assertElementContainsText('bodyTable', 'And as group manager');

        // When I access last email sent to the group manager.
        $this->getUrl('seleniumtests/showlastemail/' . $betty['Username']);

        // Then I should see the expected email title
        $this->assertMetaTitleContains(sprintf('%s added you to the group %s', $user['FirstName'], $group['name']));

        // And I should see the expected email content
        $this->assertElementContainsText('bodyTable', 'Name: ' . $group['name']);
        $this->assertElementContainsText('bodyTable', 'Your role: Member');
        $this->assertElementContainsText('bodyTable', 'As member of the group');
        $this->assertElementNotContainText('bodyTable', 'And as group manager');
    }

    /**
     * Scenario: As a user I should receive a notification when I am deleted from a group
     *
     * Given I am logged in as a group manager
     * And   I am on the users workspace
     * And   I am editing a group that I manage
     * When  I remove a user from the group
     * And   I click on save
     * Then  I should see a success notification message
     * When  I access last email sent to the user
     * Then  I should see the expected email title
     * And   I should see the expected email content
     *
     * @group GM
     * @group group
     * @group edit
     */
    public function testEditGroupDeleteUserEmailNotification() 
    {
        $this->resetDatabaseWhenComplete();

        // Given I am a group manager.
        $user = User::get('frances');

        // I am logged in as admin
        $this->loginAs($user);

        // And I am on the users workspace
        // And I am editing a group that I manage
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
        $this->assertElementContainsText('bodyTable', 'Name: ' . $group['name']);
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
     * @group GM
     * @group group
     * @group edit
     */
    public function testEditGroupUpdateUserEmailNotification() 
    {
        $this->resetDatabaseWhenComplete();

        // Given I am a group manager.
        $user = User::get('ping');


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
        $this->assertMetaTitleContains(sprintf('%s updated your group membership', $user['FirstName'], $group['name']));
        $this->assertElementContainsText('bodyTable', 'Group name: ' . $group['name']);
        $this->assertElementContainsText('bodyTable', 'New role: Group manager');
        $this->assertElementContainsText('bodyTable', 'You are now a group manager of this group');

        // When I access last email sent to the member
        $this->getUrl('seleniumtests/showlastemail/' . $userT['Username']);

        // Then I should see the expected email
        $this->assertMetaTitleContains(sprintf('%s updated your group membership', $user['FirstName'], $group['name']));
        $this->assertElementContainsText('bodyTable', 'Group name: ' . $group['name']);
        $this->assertElementContainsText('bodyTable', 'New role: Member');
        $this->assertElementContainsText('bodyTable', 'You are no longer a group manager of this group');
    }

    /**
     * Scenario: As a group manager I should receive a notification when another group manager updated the members of a group I manage
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
     * Then  I shouldn't see any email
     * When  I access last email sent to the other group manager
     * Then  I should see the expected email title
     * And   I should see the expected email content
     *
     * @group GM
     * @group group
     * @group edit
     */
    public function testEditGroupGroupUpdatedSummaryEmailNotification() 
    {
        $this->resetDatabaseWhenComplete();

        // Given I am an administrator.
        $user = User::get('ping');


        // I am logged in as admin
        $this->loginAs($user);

        // And I am on the users workspace
        // And I am editing a group that I manage
        $group = Group::get(['id' => UuidFactory::uuid('group.id.human_resource')]);
        $this->gotoEditGroup($group['id']);

        // When I add some users to the groups
        $ada = User::get('ada');
        $this->searchGroupUserToAdd($ada, $user);
        $this->addTemporaryGroupUser($ada);
        $this->editTemporaryGroupUserRole($ada, true);

        $betty = User::get('betty');
        $this->searchGroupUserToAdd($betty, $user);
        $this->addTemporaryGroupUser($betty);

        // And I remove some users from the group
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

        // When I access last email sent to the other group manager
        $thelma = User::get('thelma');
        $this->getUrl('seleniumtests/showlastemail/' . $thelma['Username']);

        // Then I should see the expected email title
        $this->assertMetaTitleContains(sprintf('%s updated members of the group %s', $user['FirstName'], $group['name']));

        // And I should see the expected email content
        $this->assertElementContainsText('bodyTable', 'Name: ' . $group['name']);
        $this->assertElementContainsText('bodyTable', 'Added members');
        $this->assertElementContainsText('#added_users', "{$ada['FirstName']} {$ada['LastName']} (Group manager)");
        $this->assertElementContainsText('#added_users', "{$betty['FirstName']} {$betty['LastName']} (Member)");
        $this->assertElementContainsText('bodyTable', 'Removed members');
        $this->assertElementContainsText('#deleted_users', "{$ursula['FirstName']} {$ursula['LastName']} (Member)");
        $this->assertElementContainsText('bodyTable', 'Updated roles');
        $this->assertElementContainsText('#updated_roles', "{$wang['FirstName']} {$wang['LastName']} is now group manager");
    }

}
