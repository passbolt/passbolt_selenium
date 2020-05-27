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
 * Feature: As a user I can share passwords
 *
 * Scenarios :
 * As a user I can see the share dialog using the share button in the action bar
 * As a user I can see the share dialog using the right click contextual menu
 * As a user I can see the share dialog using the edit permissions button in the sidebar
 * As a user I cannot access the share dialog from the action bar or the contextual menu if I have only read or update access to
 * As a user I can view the permissions for a password I own
 * As a user I can view the permissions for a password I own in the sidebar
 * As a user I can view the permissions for a password I don't own
 * As a user I can view the permissions for a password I have read-only rights in the sidebar
 * As a user I can share a password with other users
 * As a user I can share a password with other users, and see them immediately in the sidebar
 * As a user I can share a password with a groups
 * As a user I can unshare a password with a group
 * As a user I edit the permissions of a password I own
 * As a user I delete a permission of a password I own
 * As a user I should not let a resource without at least one owner
 * As a user I should be able to drop my owner permission if there is another owner
 * As a user I can view the permissions for a password I don't own
 */
namespace Tests\LU\Base;

use App\Actions\ConfirmationDialogActionsTrait;
use App\Actions\MasterPasswordActionsTrait;
use App\Actions\PasswordActionsTrait;
use App\Actions\ShareActionsTrait;
use App\Actions\SidebarActionsTrait;
use App\Actions\UserActionsTrait;
use App\Actions\WorkspaceActionsTrait;
use App\Assertions\ClipboardAssertions;
use App\Assertions\ConfirmationDialogAssertionsTrait;
use App\Assertions\MasterPasswordAssertionsTrait;
use App\Assertions\PasswordAssertionsTrait;
use App\Assertions\PermissionAssertionsTrait;
use App\Assertions\ShareAssertionsTrait;
use App\Assertions\SidebarAssertionsTrait;
use App\Assertions\WorkspaceAssertionsTrait;
use App\Lib\UuidFactory;
use App\PassboltTestCase;
use Data\Fixtures\User;
use Data\Fixtures\Resource;

class PasswordShareTest extends PassboltTestCase
{
    use ClipboardAssertions;
    use ConfirmationDialogAssertionsTrait;
    use ConfirmationDialogActionsTrait;
    use MasterPasswordAssertionsTrait;
    use MasterPasswordActionsTrait;
    use PasswordActionsTrait;
    use PasswordAssertionsTrait;
    use PermissionAssertionsTrait;
    use ShareActionsTrait;
    use ShareAssertionsTrait;
    use SidebarActionsTrait;
    use SidebarAssertionsTrait;
    use UserActionsTrait;
    use WorkspaceActionsTrait;
    use WorkspaceAssertionsTrait;

    /**
     * Scenario As LU I can share a password I own
     *
     */
    public function testSharePasswordSpecial()
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        $resourceAId = UuidFactory::uuid('resource.id.apache');

        // Given I am Ada
        // And I am logged in on the password workspace
        $this->loginAs(User::get('ada'));

        // Then I can see the share password button is disabled by default
        $this->assertVisible('js_wk_menu_sharing_button');
        $this->assertVisibleByCss('#js_wk_menu_sharing_button.disabled');

        // When I click on a password I am owner
        $resource = Resource::get(array('user' => 'ada', 'id' => $resourceAId));
        $this->clickPassword($resource['id']);

        // Then I can see the share button is enabled
        $this->assertNotVisibleByCss('#js_wk_menu_sharing_button.disabled');
        $this->assertVisible('js_wk_menu_sharing_button');

        // When I click on the share button
        $this->click('js_wk_menu_sharing_button');

        // Then I can see the share password dialog
        $this->goIntoReactAppIframe();
        $this->waitUntilISee('.share-dialog');
        $this->waitUntilIDontSee('.row.skeleton');
        $this->closeShareDialog();

        // When I open the permissions section in the password details sidebar
        $this->clickSecondarySidebarSectionHeader('permissions');

        // And I click on the edit permissions link
        $this->click('#js_edit_permissions_button');

        // Then I can see the share password dialog
        $this->goIntoReactAppIframe();
        $this->waitUntilISee('.share-dialog');
        $this->waitUntilIDontSee('.row.skeleton');
        $this->closeShareDialog();

        // When I right click on the password
        $this->rightClickPassword($resource['id']);

        // Then I can see the contextual menu
        $this->assertVisible('js_contextual_menu');

        // I can see the the share option is enabled
        $menuItem = $this->find('#js_password_browser_menu_share');
        $this->assertElementHasNotClass($menuItem, 'disabled');

        // When I click on the share link in the contextual menu
        $this->click('#js_password_browser_menu_share a');

        // Then I can see the share password dialog
        $this->goIntoReactAppIframe();
        $this->waitUntilISee('.share-dialog');
        $this->waitUntilIDontSee('.row.skeleton');

        // And I can see the actual permissions applied on the resource.
        $permissions = [
            'ada@passbolt.com' => 15,
            'betty@passbolt.com' => 7,
            'carol@passbolt.com' => 1,
            'dame@passbolt.com' => 1,
        ];
        $this->assertDialogPermissions($permissions);

        // When I add a user
        $username = 'edith@passbolt.com';
        $this->addTemporaryPermission($username);
        $permissions['edith@passbolt.com'] = 1;

        // And I remove a user
        $username = 'carol@passbolt.com';
        $this->deleteTemporaryPermission($username);
        unset($permissions[$username]);

        // Then I can see that temporary changes are waiting to be saved
        $this->waitUntilISee('.share-dialog', '/Click save to apply your pending changes/');
        // And I can see the modified list of permissions
        $this->assertDialogPermissions($permissions);

        // When I click Save
        $this->click('.submit-wrapper input[type="submit"]');

        // Then I should see the passphrase entry dialog.
        $this->waitUntilISee('.dialog.passphrase-entry');

        // When I enter 'ada@passbolt.com' as password
        $this->inputText('.passphrase-entry input[name="passphrase"]', 'ada@passbolt.com');

        // And I click on the OK button
        $this->click('.passphrase-entry input[type=submit]');
        $this->goOutOfIframe();

        // I see a notice message that the operation was a success
        $this->assertNotificationMessage('The permissions have been changed successfully.');

        // When I open the permissions section in the password details sidebar
        $this->clickSecondarySidebarSectionHeader('permissions');

        // Then I can confirm that the permission changes have been applied.
        $permissionLabels = [
            1 => 'can read',
            7 => 'can update',
            15 => 'is owner'
        ];
        foreach ($permissions as $username => $permissionType) {
            $permissionLabel = $permissionLabels[$permissionType];
            $this->assertPermissionInSidebar($username, $permissionLabel);
        }

        // When I open the share dialog
        $this->openShareDialog();

        // Then I can confirm that the permission changes have been applied.
        $this->assertDialogPermissions($permissions);

        // When I remove my permission
        $this->deleteTemporaryPermission('ada@passbolt.com');

        // Then I can see that a owner is missing
        $this->waitUntilISee('.share-dialog', '/Please make sure there is at least one owner./');

        // And I can see the modified list of permissions
        $this->editTemporaryPermission('betty@passbolt.com', 'is owner');

        // When I click Save
        $this->click('.submit-wrapper input[type="submit"]');

        // Then I should see the passphrase entry dialog.
        $this->waitUntilISee('.dialog.passphrase-entry');

        // When I enter 'ada@passbolt.com' as password
        $this->inputText('.passphrase-entry input[name="passphrase"]', 'ada@passbolt.com');

        // And I click on the OK button
        $this->click('.passphrase-entry input[type=submit]');
        $this->goOutOfIframe();

        // Then I see a notice message that the operation was a success
        $this->assertNotificationMessage('The permissions have been changed successfully.');

        // And the password details sidebar should be closed
        $this->waitUntilIDontSee('#js_pwd_details');
    }
}
