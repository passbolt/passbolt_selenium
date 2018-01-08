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
 * Bug PASSBOLT-1337 - Regression test
 */
namespace Tests\LU\Regressions;

use App\Actions\ConfirmationDialogActionsTrait;
use App\Actions\PasswordActionsTrait;
use App\Actions\UserActionsTrait;
use App\Actions\WorkspaceActionsTrait;
use App\Assertions\ConfirmationDialogAssertionsTrait;
use App\Assertions\UserAssertionsTrait;
use App\Assertions\WorkspaceAssertionsTrait;
use App\PassboltTestCase;
use Data\Fixtures\User;
use App\Lib\UuidFactory;

class PASSBOLT1337 extends PassboltTestCase
{
    use UserAssertionsTrait;
    use UserActionsTrait;
    use WorkspaceActionsTrait;
    use WorkspaceAssertionsTrait;
    use ConfirmationDialogAssertionsTrait;
    use ConfirmationDialogActionsTrait;
    use PasswordActionsTrait;

    /**
     * Scenario: As a user while editing a password that had been shared with a deleted user, the application shouldn't crash silently
     *
     * Given I am logged in as admin in the user workspace
     * And   I click on the user
     * And   I click on delete button
     * Then  I should see a confirmation dialog
     * When  I click ok in the confirmation dialog
     * Then  I should see a confirmation message
     * When  I logout and I log in as Ada
     * And   I go on the password workspace
     * And   I am editing a password that was shared with betty
     * When  I click on name input text field
     * And   I empty the name input text field value
     * And   I enter a new value
     * And   I click save
     * Then  I can see a success notification
     * And   I can see that the password name have changed in the overview
     * And   I can see the new name value in the sidebar
     * When  I click edit button
     * Then  I can see the new name in the edit password dialog
     *
     * @group LU
     * @group regression
     * @group v2
     */
    public function testEditingPasswordSharedWithDeletedUsersShouldntCrash() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Admin
        $user = User::get('admin');

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
        $this->assertConfirmationDialog('Do you really want to delete?');

        // Click ok in confirmation dialog.
        $this->confirmActionInConfirmationDialog();

        // Then I should see a success notification message saying the user is deleted
        $this->assertNotification('app_users_delete_success');

        // When I logout
        $this->logout();

        // And I am Ada
        $user = User::get('ada');

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // And I am editing a password that was shared with betty
        $this->gotoEditPassword(UuidFactory::uuid('resource.id.apache'));

        // When I click on name input text field
        $this->click('js_field_name');

        // And I empty the name input text field value
        // And I enter a new value
        $newname = 'New password name';
        $this->inputText('js_field_name', $newname);

        // And I click save
        $this->click('.edit-password-dialog input[type=submit]');

        // Then I can see a success notification
        $this->assertNotification('app_resources_update_success');

        // And I can see that the password name have changed in the overview
        $this->assertElementContainsText('#js_wsp_pwd_browser .tableview-content', $newname);

        // And I can see the new name value in the sidebar
        $this->assertVisibleByCss('#js_pwd_details.panel.aside');
        $this->assertElementContainsText('js_pwd_details', $newname);

        // When I click edit button
        $this->click('js_wk_menu_edition_button');

        // Then I can see the new name in the edit password dialog
        $this->assertInputValue('js_field_name', $newname);
    }
}