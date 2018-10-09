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
 * Feature: As an administrator I can delete a group
 *
 * Scenarios :
 *  - As an admin I should be able to access the group delete dialog using route
 *  - As an admin I can delete a group that doesn't have any passwords shared with it
 *  - As an admin I can delete a group that has passwords shared with it
 *  - As an admin I can't delete a group that is the sole owner of passwords
 *  - As an admin I can delete a group that is already selected in the list
 */
namespace Tests\AD\Base;

use App\Actions\ConfirmationDialogActionsTrait;
use App\Actions\GroupActionsTrait;
use App\Actions\MasterPasswordActionsTrait;
use App\Actions\PasswordActionsTrait;
use App\Actions\PermissionActionsTrait;
use App\Actions\ShareActionsTrait;
use App\Actions\WorkspaceActionsTrait;
use App\Assertions\ConfirmationDialogAssertionsTrait;
use App\Assertions\GroupAssertionsTrait;
use App\Assertions\MasterPasswordAssertionsTrait;
use App\Assertions\PasswordAssertionsTrait;
use App\Assertions\ShareAssertionsTrait;
use App\Assertions\WorkspaceAssertionsTrait;
use App\Lib\UuidFactory;
use App\PassboltTestCase;
use Data\Fixtures\User;

class ADGroupDeleteTest extends PassboltTestCase
{

    use ConfirmationDialogActionsTrait;
    use ConfirmationDialogAssertionsTrait;
    use GroupActionsTrait;
    use GroupAssertionsTrait;
    use MasterPasswordActionsTrait;
    use MasterPasswordAssertionsTrait;
    use PasswordActionsTrait;
    use PasswordAssertionsTrait;
    use PermissionActionsTrait;
    use ShareActionsTrait;
    use ShareAssertionsTrait;
    use WorkspaceActionsTrait;
    use WorkspaceAssertionsTrait;

    /**
     * Scenario: As an admin I should be able to access the group delete dialog using route
     *
     * When  I am logged in as Admin
     * And   I enter the route in the url
     * Then  I should see the group delete dialog
     *
     * @group AD
     * @group group
     * @group group-delete
     * @group saucelabs
     * @group v2
     */
    public function testRoute_DeleteGroup()
    {
        $this->loginAs(User::get('admin'), ['url' => '/app/groups/delete/36563004-3f25-50c0-b22e-6554c3ccc4e7']);
        $this->waitCompletion();
        $this->assertConfirmationDialog('You are about to delete the group "Board"!');
    }

    /**
     * Scenario: As an administrator I can delete a group that doesn't have any passwords shared with it.
     *
     * Given I am logged in as an administrator
     * And   I am on the users workspace
     * When  I click on the contextual menu of a group that I want to remove that doesn't have any password shared with it
     * Then  I should see a contextual menu with an option "delete group"
     * When  I click on the "delete group" menu item.
     * Then  I should see a confirmation dialog opening
     * And   I should see a message saying that there is no password associated with the group
     * And   I should see a call to action button with the "delete group" text
     * When  I click on "delete group" button
     * Then  I should see that the confirmation dialog disappears
     * And   I should see that the group is not present in the list anymore
     *
     * @group AD
     * @group user
     * @group group
     * @group delete
     * @group v2
     */
    public function testDeleteGroupWithoutPasswords() 
    {
        $this->resetDatabaseWhenComplete = true;

        // Given I am an administrator.
        $user = User::get('admin');

        // I am logged in as admin
        $this->loginAs($user);

        // Go to user workspace
        $this->gotoWorkspace('user');

        // When I click on the contextual menu button of a group on the right
        $groupId = UuidFactory::uuid('group.id.accounting');
        $this->waitUntilISee("#js_wsp_users_groups_list #group_${groupId}");
        $this->goToRemoveGroup($groupId);

        // Assert that I can see text.
        $this->waitUntilISee('.dialog.confirm', '/You are about to delete the group \"Accounting\"/');
        $this->waitUntilISee('.dialog.confirm', '/This group is not associated with any password\. You are good to go/');

        // Confirm action.
        $this->assertActionNameInConfirmationDialog('delete group');
        $this->confirmActionInConfirmationDialog();

        // The group should disappear from the list.
        $this->waitUntilIDontSee("#js_wsp_users_groups_list", '/Accounting/');
        $this->waitUntilIDontSee("#js_wsp_users_groups_list #group_${groupId}");
    }

    /**
     * Scenario: As an administrator I can delete a group that has passwords shared with it
     *
     * Given I am logged in as an administrator
     * And   I am on the users workspace
     * When  I click on the contextual menu of a group that I want to remove that has passwords shared with it
     * Then  I should see a contextual menu with an option "delete group"
     * When  I click on the "delete group" menu item.
     * Then  I should see a confirmation dialog opening
     * And   I should see a message saying that there are x passwords associated with this group
     * And   I should see a call to action button with the "delete group" text
     * When  I click on "delete group" button
     * Then  I should see that the confirmation dialog disappears
     * And   I should see that the group is not present in the list anymore
     *
     * @group AD
     * @group user
     * @group group
     * @group delete
     * @group broken
     * @group PASSBOLT-2544
     */
    public function testDeleteGroupWithPasswords() 
    {
        $this->resetDatabaseWhenComplete = true;

        // Given I am an administrator.
        $user = User::get('admin');

        // I am logged in as admin
        $this->loginAs($user);

        // Go to user workspace
        $this->gotoWorkspace('user');

        // When I click on the contextual menu button of a group on the right
        $groupId = UuidFactory::uuid('group.id.board');
        $this->waitUntilISee("#js_wsp_users_groups_list #group_${groupId}");
        $this->goToRemoveGroup($groupId);

        // Assert that I can see text.
        $this->waitUntilISee('.dialog.confirm', '/You are about to delete the group \"Board\"/');
        $this->waitUntilISee('.dialog.confirm', '/This group is associated with 12 passwords\. All users in this group will lose access to these passwords/');

        // Confirm action.
        $this->assertActionNameInConfirmationDialog('delete group');
        $this->confirmActionInConfirmationDialog();

        // The group should disappear from the list.
        $this->waitUntilIDontSee("#js_wsp_users_groups_list", '/Board/');
        $this->waitUntilIDontSee("#js_wsp_users_groups_list #group_${groupId}");
    }

    /**
     * Scenario: As an administrator I can't delete a group that is the sole owner of passwords
     * Given I am logged in as an administrator
     * And   I am on the passwords workspace
     * When  I create a password
     * And   I share this password with the group "Accounting"
     * And   I set the group as "owner" of the password
     * And   I set myself as "can read" only so that the group is the only owner left
     * And   I save the permissions
     * Then  I should see a notification saying that the permissions have been saved
     * When  I go to the users workspace
     * And   I click on the contextual menu icon of the group "accounting" which is now the sole owner of a password
     * Then  I should see a contextual menu with an option "delete group"
     * When  I click on the "delete group" menu item.
     * Then  I should see a confirmation dialog opening
     * And   I should see a message saying that the group is the sole owner of the password created
     * And   I should see the name of the password listed in the message
     * And   I should see a button "Got it"
     * When  I click on "Got it" button
     * Then  I should see that the confirmation dialog disappears
     * And   I should see that the group is still present in the list
     *
     * @group AD
     * @group user
     * @group group
     * @group delete
     * @group v2
     */
    public function testDeleteGroupSoleOwner()
    {
        $this->resetDatabaseWhenComplete = true;

        // Given I am an administrator.
        $user = User::get('admin');

        // I am logged in as admin
        $this->loginAs($user);

        // I create a password.
        $resource = [
            'name' => 'bankaccount',
            'username' => 'admin',
            'uri' => 'https://www.bankaccount.com',
            'password' => 'testpassword'
        ];
        $this->createPassword($resource);

        // Share password.
        $resource['id'] = $this->findPasswordIdByName($resource['name']);
        $this->gotoSharePassword($resource['id']);

        // Then I can see the group has no right on the password
        $this->assertElementNotContainText(
            $this->findById('js-share-edit-list'),
            'Accounting'
        );

        // When I give read access to the group for a password I own
        $this->addTemporaryPermission($resource, 'Accounting', $user);
        $this->editTemporaryPermission($resource, 'Accounting', 'is owner', $user);
        $this->editTemporaryPermission($resource, 'admin@passbolt.com', 'can read', $user);

        // When I click on the save button
        $this->saveShareChanges($user);

        // Go to user workspace
        $this->gotoWorkspace('user');

        // When I click on the contextual menu button of a group on the right
        $groupId = UuidFactory::uuid('group.id.accounting');
        $this->waitUntilISee("#js_wsp_users_groups_list #group_${groupId}");
        $this->goToRemoveGroup($groupId);

        // Assert that I can see text.
        $this->waitUntilISee('.dialog');
        $this->assertElementContainsText('.dialog', 'You cannot delete this group!');
        $this->waitUntilISee('.dialog', '/You are about to delete Accounting/');
        $this->waitUntilISee('.dialog', '/This group is the owner of passwords. You need to transfer the ownership to other users or groups to continue./');

        // And I should see a the passwords transfer section
        $this->waitUntilISee('.ownership-transfer-items');

        // And I should see a password requiring a transfer
        $this->assertElementContainsText('.ownership-transfer-items', 'bankaccount (Password) new owner:');

        // And I should see a list of possible owners
        $this->assertElementContainsText('.ownership-transfer-items', 'Admin User (admin@passbolt.com)');

        // When I click on delete button
        $this->click('.dialog .submit-wrapper input.button.primary');

        // Then I should see a success notification message saying the group is deleted
        $this->assertNotification('app_groups_delete_success');

        // And I should not see the group in the group list
        $this->waitUntilIDontSee("#js_wsp_users_groups_list", '/Accounting/');
    }

    /**
     * Scenario: As an administrator I can delete a group that is already selected in the list
     *
     * Given I am logged in as an administrator
     * And   I am on the users workspace
     * When  I click on the group that I want to delete
     * Then  I should see that the group is selected
     * And   I should see that the breadcrum shows "All users > group name"
     * When  I click on the contextual menu of a group that I want to remove that doesn't have any password shared with it
     * Then  I should see a contextual menu with an option "delete group"
     * When  I click on the "delete group" menu item
     * Then  I should see a confirmation dialog opening
     * And   I should see a message saying that there is no password associated with the group
     * And   I should see a call to action button with the "delete group" text
     * When  I click on "delete group" button
     * Then  I should see that the confirmation dialog disappears
     * And   I should see that the group is not present in the list anymore
     * And   I should see that the group is not selected anymore
     * And   I should see that instead, the All users filter is selected
     * And   I should see that the breadcrumb is now "All users" only and doesn't contain the name of the group
     *
     * @group AD
     * @group user
     * @group group
     * @group delete
     * @group v2
     */
    public function testDeleteSelectedGroup()
    {
        $this->resetDatabaseWhenComplete = true;

        // Given I am an administrator.
        $user = User::get('admin');
        $groupId = UuidFactory::uuid('group.id.accounting');

        // I am logged in as admin
        $this->loginAs($user);

        // Go to user workspace
        $this->gotoWorkspace('user');

        $this->waitUntilISee("#js_wsp_users_groups_list #group_${groupId}");
        $this->clickGroup($groupId);
        $this->assertTrue($this->isGroupSelected($groupId));

        $this->assertBreadcrumb('users', ['All users', 'Accounting (group)']);

        $this->goToRemoveGroup($groupId);

        // Assert that I can see text.
        $this->waitUntilISee('.dialog.confirm', '/You are about to delete the group \"Accounting\"/');
        $this->waitUntilISee('.dialog.confirm', '/This group is not associated with any password\. You are good to go/');

        // Confirm action.
        $this->assertActionNameInConfirmationDialog('delete group');
        $this->confirmActionInConfirmationDialog();

        // Wait until the group has disappeared from the list.
        $this->waitUntilIDontSee("#js_wsp_users_groups_list", '/Accounting/');

        // The filter All users should now be selected.
        $this->waitUntilISee('#js_users_wsp_filter_all .row.selected');

        // Assert that the breadcrumb is related to All users and not to Accounting anymore.
        $this->assertBreadcrumb('users', ['All users']);
        $this->assertElementNotContainText($this->findById('js_wsp_users_breadcrumb'),  'Accounting (group)');
    }
}