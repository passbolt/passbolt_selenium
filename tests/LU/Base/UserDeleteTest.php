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
 * Feature: As a LU regarding the delete user feature.
 *
 * Scenarios :
 *  - As LU I should be able to get a clear feedback at login if my account has been deleted.
 */
namespace Tests\LU\Base;

use App\Actions\ConfirmationDialogActionsTrait;
use App\Actions\UserActionsTrait;
use App\Actions\WorkspaceActionsTrait;
use App\Assertions\ConfirmationDialogAssertionsTrait;
use App\Assertions\WorkspaceAssertionsTrait;
use App\PassboltTestCase;
use Data\Fixtures\User;

class UserDeleteTest extends PassboltTestCase
{
    use ConfirmationDialogActionsTrait;
    use ConfirmationDialogAssertionsTrait;
    use UserActionsTrait;
    use WorkspaceActionsTrait;
    use WorkspaceAssertionsTrait;

    /**
     * Scenario: As LU I should be able to get a clear feedback at login if my account has been deleted.
     *
     * Given I am logged in as admin in the user workspace
     * And   I click on the user
     * And   I click on delete button
     * Then  I should see a confirmation dialog
     * When  I click ok in the confirmation dialog
     * Then  I should see a confirmation message
     * When  I log out
     * And   I become the user I deleted
     * And   I go to the login page
     * Then  I should see a feedback telling me that my account doesn't exist on server
     *
     * @group LU
     * @group user
     * @group user-delete
     * @group v2
     */
    public function testDeletedUserGetFeedback() 
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
        $userU = User::get('ursula');
        $this->clickUser($userU['id']);

        // Then I select the delete option in the contextual menu
        $this->click('js_user_wk_menu_deletion_button');

        // Assert that the confirmation dialog is displayed.
        $this->assertConfirmationDialog('Delete user?');

        // Click ok in confirmation dialog.
        $this->confirmActionInConfirmationDialog();

        // Then I should see a success notification message saying the user is deleted
        $this->assertNotification('app_users_delete_success');

        // Log out.
        $this->logout();

        // I become the user I deleted.
        $this->setClientConfig($userU);

        // When I go to login.
        $this->getUrl('login');

        // I should see a feedback telling me that the user doesn't exist on server.
        $this->waitUntilISee('html.server-not-verified.server-no-user');
        $this->waitUntilISee('.plugin-check.gpg.error', '/There is no user associated with this key/');
        $this->waitUntilISee('.users.login.form .feedback', '/The supplied account does not exist/');
    }

}