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
 * Feature: As a admin I can delete users
 *
 * Scenarios :
 *  - As admin I should be able to delete a user on a right click
 *  - As admin I should be able to delete a user using the delete button
 *  - As Admin I should not be able to delete my own user account
 *  - As Admin I should not be able to delete a user who is the sole owner of some shared passwords
 *  - As Admin I should not be able to delete a user who is the sole group manager of groups
 */
namespace Tests\AD\Base;

use App\Actions\ConfirmationDialogActionsTrait;
use App\Actions\UserActionsTrait;
use App\Actions\WorkspaceActionsTrait;
use App\Assertions\ConfirmationDialogAssertionsTrait;
use App\Assertions\WorkspaceAssertionsTrait;
use App\PassboltTestCase;
use Data\Fixtures\User;

class ADUserDeleteTest extends PassboltTestCase
{

    use ConfirmationDialogActionsTrait;
    use ConfirmationDialogAssertionsTrait;
    use UserActionsTrait;
    use WorkspaceAssertionsTrait;
    use WorkspaceActionsTrait;

    /**
     * Scenario: As admin I should be able to delete a user on a right click
     *
     * Given I am logged in as admin in the user workspace
     * And   I right click on a user
     * Then  I should see a contextual menu with a delete option
     * When  I click on the delete option
     * Then  I should see a confirmation dialog
     * When  I click ok in the confirmation dialog
     * Then  I should see a confirmation message
     * And   I should not see the user in the user list anymore
     * When  I refresh the page
     * Then  I still should not see the user in the user list anymore
     *
     * @group AD
     * @group user
     * @group delete
     */
    public function testDeleteUserRightClick() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // And I am Admin
        $user = User::get('admin');

        // And I am logged in on the user workspace
        $this->loginAs($user);

        // Go to user workspace
        $this->gotoWorkspace('user');

        // When I right click on a user
        $user = User::get('ursula');
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
        $this->assertTrue($this->isNotVisible('#user_' . $user['id']));

        // When I refresh the page
        $this->refresh();

        // And go to user workspace
        $this->gotoWorkspace('user');

        // Then I should not see the user in the list anymore
        $this->assertTrue($this->isNotVisible('#user_' . $user['id']));
    }

    /**
     * Scenario: As admin I should be able to delete a user using the delete button
     *
     * Given I am logged in as admin in the user workspace
     * And   I click on the user
     * And   I click on delete button
     * Then  I should see a confirmation dialog
     * When  I click ok in the confirmation dialog
     * Then  I should see a confirmation message
     * And   I should not see the user in the user list anymore
     * When  I refresh the page
     * Then  I still should not see the user in the user list anymore
     *
     * @group AD
     * @group user
     * @group delete
     * @group saucelabs
     */
    public function testDeleteUserButton() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Admin
        $user = User::get('admin');


        // And I am logged in on the user workspace
        $this->loginAs($user);

        // Go to user workspace
        $this->gotoWorkspace('user');

        // When I click on the user
        $user = User::get('ursula');
        $this->clickUser($user['id']);

        // Then I click on delete button
        $this->click('js_user_wk_menu_deletion_button');

        // Assert that the confirmation dialog is displayed.
        $this->assertConfirmationDialog('Do you really want to delete user ?');

        // Click ok in confirmation dialog.
        $this->confirmActionInConfirmationDialog();

        // Then I should see a success notification message saying the user is deleted
        $this->assertNotification('app_users_delete_success');

        // And I should not see the user in the list anymore
        $this->assertTrue($this->isNotVisible('#user_' . $user['id']));

        // When I refresh the page
        $this->refresh();

        // And go to user workspace
        $this->gotoWorkspace('user');

        // Then I should not see the user in the list anymore
        $this->assertTrue($this->isNotVisible('#user_' . $user['id']));
    }

    /**
     * Scenario: As Admin I should not be able to delete my own user account
     *
     * Given I am logged in as admin in the user workspace
     * And   I click on my own name in the user list
     * Then  I should see that the delete button is disabled
     * When  I right click on my name in the users list
     * Then  I should see a contextual menu
     * And   I should see that the delete option is not available.
     *
     * @group AD
     * @group user
     * @group delete
     */
    public function testDeleteUserMyself() 
    {
        // And I am Admin
        $user = User::get('admin');


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
     * Scenario: As Admin I should not be able to delete a user who is the sole owner of some shared passwords
     *
     * Given I am logged in as admin in the user workspace
     * And   I click on the user
     * And   I click on delete button
     * Then  I should see a confirmation dialog
     * When  I click ok in the confirmation dialog
     * Then  I should see a message explaining me why the user can't be deleted
     * When  I click on the dialog main action
     * Then  I should see that the dialog disappears
     *
     * @group AD
     * @group user
     * @group delete
     */
    public function testDeletedUserSoleOwner() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Admin
        $user = User::get('admin');


        // And I am logged in on the user workspace
        $this->loginAs($user);

        // Go to user workspace
        $this->gotoWorkspace('user');

        // When I click on a user
        $userA = User::get('ada');
        $this->clickUser($userA['id']);

        // And I click on delete button
        $this->click('js_user_wk_menu_deletion_button');

        // Then I should see a message explaining me why the user can't be deleted
        $this->assertConfirmationDialog('You cannot delete this user!');

        // When I click on the dialog main action
        $this->confirmActionInConfirmationDialog();

        // Then I should see that the dialog disappears
        $this->waitUntilIDontSee('.mad_component_confirm');
    }

    /**
     * Scenario: As Admin I should not be able to delete a user who is the sole group manager of groups
     *
     * Given I am logged in as admin in the user workspace
     * And   I click on the user
     * And   I click on delete button
     * Then  I should see a confirmation dialog
     * When  I click ok in the confirmation dialog
     * Then  I should see a message explaining me why the user can't be deleted
     * When  I click on the dialog main action
     * Then  I should see that the dialog disappears
     *
     * @group AD
     * @group user
     * @group delete
     */
    public function testDeletedUserSoleGroupManager() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Admin
        $user = User::get('admin');


        // And I am logged in on the user workspace
        $this->loginAs($user);

        // Go to user workspace
        $this->gotoWorkspace('user');

        // When I click on a user
        $userF = User::get('frances');
        $this->clickUser($userF['id']);

        // And I click on delete button
        $this->click('js_user_wk_menu_deletion_button');

        // Then I should see a message explaining me why the user can't be deleted
        $this->assertConfirmationDialog('You cannot delete this user!');

        // When I click on the dialog main action
        $this->confirmActionInConfirmationDialog();

        // Then I should see that the dialog disappears
        $this->waitUntilIDontSee('.mad_component_confirm');
    }

}