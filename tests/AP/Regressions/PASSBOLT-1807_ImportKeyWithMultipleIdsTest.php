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
namespace Tests\AP\Regressions;

use App\Actions\ConfirmationDialogActionsTrait;
use App\Actions\UserActionsTrait;
use App\Actions\WorkspaceActionsTrait;
use App\Assertions\ConfirmationDialogAssertionsTrait;
use App\Assertions\WorkspaceAssertionsTrait;
use App\PassboltSetupTestCase;
use Data\Fixtures\User;
use Data\Fixtures\Gpgkey;

class PASSBOLT1807 extends PassboltSetupTestCase
{
    use ConfirmationDialogActionsTrait;
    use ConfirmationDialogAssertionsTrait;
    use UserActionsTrait;
    use WorkspaceActionsTrait;
    use WorkspaceAssertionsTrait;

    /**
     * Scenario: As a user I should be able to import a key with multiple IDs
     *
     * Given I am Margaret
     * And   I register an account
     * When  I import a key with multiple ids
     * Then  I am able to complete the setup
     * And   I can login
     *
     * @group AP
     * @group setup
     * @group regression
     * @group v2
     * @group import-key
     */
    public function testSetupImportKeyWithMultipleIds() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // CONTEXTUAL change, margaret is already in db but we need to test her key
        $user = User::get('admin');
        $this->loginAs($user);
        $this->gotoWorkspace('user');
        $user = User::get('margaret');
        $this->clickUser($user['id']);
        $this->click('js_user_wk_menu_deletion_button');
        $this->assertConfirmationDialog('Delete user?');
        $this->confirmActionInConfirmationDialog();
        $this->assertNotification('app_users_delete_success');
        $this->logout();

        // Given I register an account as Margaret
        $user = User::get('margaret');
        $this->registerUser($user['FirstName'], $user['LastName'], $user['Username']);

        // When I import a key with multiple ids
        $this->goToSetup($user['Username'], 'warning');

        // Then I am able to complete the setup
        $this->completeSetupWithKeyImport([
            'private_key' => file_get_contents(Gpgkey::get(['name' => 'margaret'])['filepath'])
        ]);

        // And I can login
        $this->loginAs($user);
    }
}