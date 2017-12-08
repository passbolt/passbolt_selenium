<?php
//use Facebook\WebDriver\WebDriverBy;
//use Facebook\WebDriver\WebDriverSelect;

/**
 * Feature :  As an administrator I can delete a group
 *
 * Scenarios :
 *  - As an administrator I can delete a group that doesn't have any passwords shared with it
 *  - As an administrator I can delete a group that has passwords shared with it
 *  - As an administrator I can't delete a group that is the sole owner of passwords
 *  - As an administrator I can delete a group that is already selected in the list
 *
 * @copyright (c) 2017-present Passbolt SARL
 * @licence   GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class ADGroupDeleteTest extends PassboltTestCase
{

    /**
     * Scenario: As an administrator I can delete a group that doesn't have any passwords shared with it.
     * Given    that I am logged in as an administrator
     *   And    that I am on the users workspace
     * When I click on the contextual menu of a group that I want to remove that doesn't have any password shared with it
     * Then  I should see a contextual menu with an option "delete group"
     * When I click on the "delete group" menu item.
     * Then  I should see a confirmation dialog opening
     *   And    I should see a message saying that there is no password associated with the group
     *   And    I should see a call to action button with the "delete group" text
     * When I click on "delete group" button
     * Then  I should see that the confirmation dialog disappears
     * And I should see that the group is not present in the list anymore
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
        $groupId = Uuid::get('group.id.accounting');
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
     * Given    that I am logged in as an administrator
     *   And    that I am on the users workspace
     * When I click on the contextual menu of a group that I want to remove that has passwords shared with it
     * Then  I should see a contextual menu with an option "delete group"
     * When I click on the "delete group" menu item.
     * Then  I should see a confirmation dialog opening
     *   And    I should see a message saying that there are x passwords associated with this group
     *   And    I should see a call to action button with the "delete group" text
     * When I click on "delete group" button
     * Then  I should see that the confirmation dialog disappears
     * And I should see that the group is not present in the list anymore
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
        $groupId = Uuid::get('group.id.board');
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
     * Given    that I am logged in as an administrator
     *   And    that I am on the passwords workspace
     * When I create a password
     *   And    I share this password with the group "Accounting"
     *   And    I set the group as "owner" of the password
     *   And    I set myself as "can read" only so that the group is the only owner left
     *   And    I save the permissions
     * Then  I should see a notification saying that the permissions have been saved
     * When I go to the users workspace
     *   And      I click on the contextual menu icon of the group "accounting" which is now the sole owner of a password
     * Then  I should see a contextual menu with an option "delete group"
     * When I click on the "delete group" menu item.
     * Then  I should see a confirmation dialog opening
     *   And    I should see a message saying that the group is the sole owner of the password created
     *   And    I should see the name of the password listed in the message
     *   And    I should see a button "Got it"
     * When I click on "Got it" button
     * Then  I should see that the confirmation dialog disappears
     * And I should see that the group is still present in the list
     */
    public function testDeleteGroupSoleOwnerOfPasswords() 
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

        // Then  I can see the group has no right on the password
        $this->assertElementNotContainText(
            $this->findByCss('#js_permissions_list'),
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
        $groupId = Uuid::get('group.id.accounting');
        $this->waitUntilISee("#js_wsp_users_groups_list #group_${groupId}");
        $this->goToRemoveGroup($groupId);

        // Assert that I can see text.
        $this->waitUntilISee('.dialog.confirm', '/You are trying to delete the group \"Accounting\"/');
        $this->waitUntilISee('.dialog.confirm', '/This group is the sole owner of 1 password: ' . $resource['name'] . '\. You need to transfer the ownership to other users before you can proceed./');

        // Confirm action.
        $this->assertActionNameInConfirmationDialog('Got it!');
        $this->confirmActionInConfirmationDialog();

        $this->waitUntilIDontSee('.confirm.dialog');
        sleep(2);

        // Assert that the group name is still there.
        $this->waitUntilISee("#js_wsp_users_groups_list #group_${groupId}");
    }

    /**
     * Scenario: As an administrator I can delete a group that is already selected in the list
     * Given    that I am logged in as an administrator
     *   And    that I am on the users workspace
     * When I click on the group that I want to delete
     * Then  I should see that the group is selected
     *   And    I should see that the breadcrum shows "All users > group name"
     * When I click on the contextual menu of a group that I want to remove that doesn't have any password shared with it
     * Then  I should see a contextual menu with an option "delete group"
     * When I click on the "delete group" menu item
     * Then  I should see a confirmation dialog opening
     *   And    I should see a message saying that there is no password associated with the group
     *   And    I should see a call to action button with the "delete group" text
     * When I click on "delete group" button
     * Then  I should see that the confirmation dialog disappears
     *   And    I should see that the group is not present in the list anymore
     *   And    I should see that the group is not selected anymore
     *   And    I should see that instead, the All users filter is selected
     *   And    I should see that the breadcrumb is now "All users" only and doesn't contain the name of the group
     */
    public function testDeleteSelectedGroup() 
    {
        $this->resetDatabaseWhenComplete = true;

        // Given I am an administrator.
        $user = User::get('admin');
        

        $groupId = Uuid::get('group.id.accounting');

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