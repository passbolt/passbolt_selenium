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
 * As a user I cannot add twice a permission for the same user
 * As a user I can add a permission after previously adding and deleting one for the same user
 * As a user I can share a password with other users
 * As a user I can share a password with other users, and see them immediately in the sidebar
 * As a user I can share a password with a groups
 * As a user I can unshare a password with a group
 * As a user I edit the permissions of a password I own
 * As a user I delete a permission of a password I own
 * As a user I should not let a resource without at least one owner
 * As a user I should be able to drop my owner permission if there is another owner
 * As a user I can view the permissions for a password I don't own
 * As LU I can use passbolt on multiple windows and edit the permissions of a password I own
 * As a user I can share a password with other users after I restart the browser
 * As a user I can share a password with other users after I close and restore the passbolt tab
 */
namespace Tests\LU\Base;

use App\Actions\ConfirmationDialogActionsTrait;
use App\Actions\MasterPasswordActionsTrait;
use App\Actions\PasswordActionsTrait;
use App\Actions\PermissionActionsTrait;
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
use Data\Fixtures\Group;
use Facebook\WebDriver\WebDriverBy;

class PasswordShareTest extends PassboltTestCase
{
    use ClipboardAssertions;
    use ConfirmationDialogAssertionsTrait;
    use ConfirmationDialogActionsTrait;
    use MasterPasswordAssertionsTrait;
    use MasterPasswordActionsTrait;
    use PasswordActionsTrait;
    use PasswordAssertionsTrait;
    use PermissionActionsTrait;
    use PermissionAssertionsTrait;
    use ShareActionsTrait;
    use ShareAssertionsTrait;
    use SidebarActionsTrait;
    use SidebarAssertionsTrait;
    use UserActionsTrait;
    use WorkspaceActionsTrait;
    use WorkspaceAssertionsTrait;

    /**
     * Scenario: As a user I can see the share dialog using the share button in the action bar
     *
     * Given I am Ada
     * And   I am logged in on the password workspace
     * Then  I can see the share password button is disabled
     * When  I click on a password I own
     * Then  I can see the share button is enabled
     * When  I click on the share button
     * Then  I can see the share password dialog
     *
     * @group LU
     * @group password
     * @group password-share
     * @group v2
     */
    public function testSharePasswordButton() 
    {
        // Given I am Ada
        // And I am logged in on the password workspace
        $this->loginAs(User::get('ada'));

        // Then I can see the share password button is disabled
        $this->assertVisible('js_wk_menu_sharing_button');
        $this->assertVisibleByCss('#js_wk_menu_sharing_button.disabled');

        // When I click on a password I own
        $resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));
        $this->clickPassword($resource['id']);

        // Then I can see the share button is enabled
        $this->assertNotVisibleByCss('#js_wk_menu_sharing_button.disabled');
        $this->assertVisible('js_wk_menu_sharing_button');

        // When I click on the share button
        $this->click('js_wk_menu_sharing_button');

        // Then I can see the share password dialog
        $this->assertShareDialogVisible();
    }

    /**
     * Scenario: As a user I can see the share dialog using the right click contextual menu
     *
     * Given I am Ada
     * And   I am logged in on the password workspace
     * When  I right click on a password I own
     * Then  I can see the contextual menu
     * And   I can see the the share option is enabled
     * When  I click on the share link in the contextual menu
     * Then  I can see the share password dialog
     *
     * @group LU
     * @group password
     * @group password-share
     * @group v2
     */
    public function testSharePasswordRightClick() 
    {
        // Given I am Ada
        // And I am logged in on the password workspace
        $this->loginAs(User::get('ada'));

        // When I right click on a password I own
        $resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));
        $this->rightClickPassword($resource['id']);

        // Then I can see the contextual menu
        $this->assertVisible('js_contextual_menu');

        // I can see the the share option is enabled
        $menuItem = $this->find('#js_password_browser_menu_share');
        $this->assertElementHasNotClass($menuItem, 'disabled');

        // When I click on the share link in the contextual menu
        $this->click('#js_password_browser_menu_share a');

        // Then I can see the share password dialog
        $this->assertShareDialogVisible();
    }

    /**
     * Scenario: As a user I can see the share dialog using the edit permissions button in the sidebar
     *
     * Given I am Ada
     * And   I am logged in on the password workspace
     * When  I click on a password I own
     * And   I open the shared with section
     * Then  I can see the sidebar with a permissions section
     * And   I can see a edit permissions button
     * When  I click on the edit permissions button
     * Then  I can see the share password dialog
     *
     * @group LU
     * @group password
     * @group password-share
     * @group v2
     */
    public function testSharePasswordFromSidebar() 
    {
        // Given I am Ada
        // And I am logged in on the password workspace
        $this->loginAs(User::get('ada'));

        // When I right click on a password I own
        $resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));
        $this->clickPassword($resource['id']);

        // Then I can see the sidebar.
        $this->waitUntilISee('#js_pwd_details');

        // And I open the shared with section
        $this->clickSecondarySidebarSectionHeader('permissions');

        // And I can see the permission details in the sidebar.
        $this->assertVisible('js_rs_details_permissions');

        // When I click on the edit permissions button
        $this->click('js_edit_permissions_button');

        // Then I can see the share password dialog
        $this->assertShareDialogVisible();
    }

    /**
     * Scenario: As a user I cannot access the share dialog from the action bar or the contextual menu if I have only read or update access to
     *
     * Given I am Ada
     * And   I am logged in on the password workspace
     * When  I click on a password I can read
     * Then  I can see the share button is not active
     * When  I right click on a password I have only read access to
     * Then  I can see the contextual menu
     * And   I can see the share option is disabled
     * When  I click on a password I can update
     * Then  I can see the share button is not active
     * When  I right click on a password I have only update access to
     * Then  I can see the contextual menu
     * And   I can see the share option is disabled
     *
     * @group LU
     * @group password
     * @group password-share
     * @group v2
     */
    public function testEditPasswordNoRightNoShare()
    {
        // Given I am Ada
        // And I am logged in on the password workspace
        $this->loginAs(User::get('ada'));

        // When I click on a password I have only read access to
        $r = Resource::get(['user' => 'ada', 'permission' => 'read']);
        $this->clickPassword($r['id']);

        // Then I can see the share button is not active
        $editButton = $this->find('#js_wk_menu_sharing_button');
        $this->assertElementHasClass($editButton, 'disabled');

        // When I right click on a password I have only update access to
        $this->rightClickPassword($r['id']);

        // Then I can see the contextual menu
        $this->findById('js_contextual_menu');

        // And I can see the share option is disabled
        $this->click('#js_password_browser_menu_share a');
        $this->assertNotVisibleByCss('.share-password-dialog');

        // When I click on a password I have only update access to
        $r = Resource::get(['user' => 'ada', 'permission' => 'update']);
        $this->clickPassword($r['id']);

        // Then I can see the share button is not active
        $editButton = $this->find('#js_wk_menu_sharing_button');
        $this->assertElementHasClass($editButton, 'disabled');

        // When I right click on a password I have only update access to
        $this->rightClickPassword($r['id']);

        // Then I can see the contextual menu
        $this->findById('js_contextual_menu');

        // And I can see the share option is disabled
        $this->click('#js_password_browser_menu_share a');
        $this->assertNotVisibleByCss('.share-password-dialog');
    }

    /**
     * Scenario: As a user I can view the permissions for a password I own
     *
     * Given I am Ada
     * And   I am logged in on the password workspace
     * When  I go to the sharing dialog of a password I own
     * And   I can see the save button is disabled
     * Then  I can see that Ada is owner
     * And   I can see that Betty can update
     * And   I can see that Carol can read
     * And   I can see that Dame can read
     *
     * @group LU
     * @group password
     * @group password-share
     * @group v2
     */
    public function testViewPasswordPermissions()
    {
        // Given I am Ada
        // And I am logged in on the password workspace
        $this->loginAs(User::get('ada'));

        // When I go to the sharing dialog of a password I own
        $resource = Resource::get([
            'user' => 'ada',
            'id' => UuidFactory::uuid('resource.id.apache')
        ]);
        $this->gotoSharePassword(UuidFactory::uuid('resource.id.apache'));

        // Then I can see the save button is disabled
        $this->assertVisibleByCss('#js-share-save.disabled');

        // And I can see that Ada is owner
        $this->assertPermission($resource, 'ada@passbolt.com', 'is owner', ['closeDialog' => false]);

        // And I can see that Betty can update
        $this->assertPermission($resource, 'betty@passbolt.com', 'can update', ['closeDialog' => false]);

        // And I can see that Carol can read
        $this->assertPermission($resource, 'carol@passbolt.com', 'can read', ['closeDialog' => false]);

        // And I can see that Dame can read
        $this->assertPermission($resource, 'dame@passbolt.com', 'can read', ['closeDialog' => false]);
        $this->goOutOfIframe();
    }

    /**
     * Scenario: As a user I can view the permissions for a password I own in the sidebar
     *
     * Given I am Ada
     * And   I am logged in on the password workspace
     * When  I click on a password I own
     * Then  I should see the sidebar with a section about the permissions
     * And   I can see that Ada is owner
     * And   I can see that Betty can update
     * And   I can see that Carol can read
     * And   I can see that Dame can read
     *
     * @group LU
     * @group password
     * @group password-share
     * @group saucelabs
     * @group v2
     */
    public function testViewPasswordPermissionsInSidebar() 
    {
        // Given I am Ada
        // And I am logged in on the password workspace
        $this->loginAs(User::get('ada'));

        // When I go to the sharing dialog of a password I own
        $resource = Resource::get(['user' => 'ada', 'id' => UuidFactory::uuid('resource.id.apache')]);

        // Click on the password.
        $this->clickPassword($resource['id']);

        // Wait until I see the list of permissions.
        $this->waitUntilISee('#js_rs_details_permissions');

        // Then I can see that Ada is owner
        $this->assertPermissionInSidebar('ada@passbolt.com', 'is owner');

        // And I can see that Betty can update
        $this->assertPermissionInSidebar('betty@passbolt.com', 'can update');

        // And I can see that Carol can read
        $this->assertPermissionInSidebar('carol@passbolt.com', 'can read');

        // And I can see that Dame can read
        $this->assertPermissionInSidebar('dame@passbolt.com', 'can read');
    }

    /**
     * Scenario: As a user I can see which groups a password is shared with from the sidebar
     *
     * Given I am logged in user
     * And   I am on the passwords workspace
     * When  I select a password
     * Then  I should see the information sidebar opening
     * And   I should see that it contains a “shared with” section
     * And   I should see a user the password is shared with
     * And   I should see a group the password is shared with
     *
     * @group LU
     * @group password
     * @group password-share
     * @group saucelabs
     * @group v2
     */
    public function testViewPasswordPermissionsForGroupsInSidebar() 
    {
        // Given I am Ada
        // And I am logged in on the password workspace
        $this->loginAs(User::get('ada'));

        // When I go to the sharing dialog of a password I own
        $resource = Resource::get(
            ['user' => 'ada', 'id' => UuidFactory::uuid('resource.id.gnupg')]
        );

        // Click on the password.
        $this->clickPassword($resource['id']);

        // Wait until I see the list of permissions.
        $this->waitUntilISee('#js_rs_details_permissions');

        // Then I should see a user the password is shared with
        $this->assertPermissionInSidebar('ada@passbolt.com', 'can read');

        // Then I should see a group the password is shared with
        $this->assertPermissionInSidebar('Board (group)', 'can update');
    }

    /**
     * Scenario: As a user I can view the permissions for a password I have read-only rights in the sidebar
     *
     * Given I am Ada
     * And   I am logged in on the password workspace
     * When  I click on a password I own
     * Then  I should see the sidebar with a section about the permissions
     * And   I can see that Ada can update
     * And   I can see that Betty can read
     * And   I can see that Carol can read
     * And   I can see that Edith is owner
     *
     * @group LU
     * @group password
     * @group password-share
     * @group v2
     */
    public function testViewPasswordPermissionsWithReadOnlyRightInSidebar() 
    {
        // Given I am Ada
        // And I am logged in on the password workspace
        $this->loginAs(User::get('carol'));

        // When I click on a password for which I have read only rights.
        $resource = Resource::get(
            ['user' => 'carol', 'id' => UuidFactory::uuid('resource.id.canjs')]
        );

        // Click on the password.
        $this->clickPassword($resource['id']);

        // Wait until I see the list of permissions.
        $this->waitUntilISee('#js_rs_details_permissions');

        // Then I can see that Ada is owner
        $this->assertPermissionInSidebar('ada@passbolt.com', 'can update');

        // And I can see that Betty can update
        $this->assertPermissionInSidebar('betty@passbolt.com', 'can read');

        // And I can see that Carol can read
        $this->assertPermissionInSidebar('carol@passbolt.com', 'can read');

        // And I can see that Dame can read
        $this->assertPermissionInSidebar('edith@passbolt.com', 'is owner');
    }

    /**
     * Scenario: As a user I cannot add twice a permission for the same user
     *
     * Given I am Carol
     * And   I am logged in on the password workspace
     * When  I add a temporary permission for Betty
     * And   I search again Betty
     * Then  I should not see it in the autocomplete results
     *
     * @group LU
     * @group password
     * @group password-share
     * @group v2
     */
    public function testCannotAddTwiceAPermissionForTheSameUser() 
    {
        // Given I am Carol
        $user = User::get('carol');

        // Retrieve the user to share the password with.
        $shareWithUser = User::get('betty');
        $shareWithUserFullName = $shareWithUser['FirstName'] . ' ' . $shareWithUser['LastName'];

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // When I add a temporary permission for Betty on a password I own
        $resource = Resource::get(
            array(
            'user' => 'carol',
            'id' => UuidFactory::uuid('resource.id.gnupg')
            )
        );
        $this->gotoSharePassword($resource['id']);
        $this->addTemporaryPermission($resource, $shareWithUser['name'], $user);

        // And I search again Betty
        $this->searchAroToGrant($resource, $shareWithUser['name'], $user);

        // Then I should not see her in the autocomplete results
        $this->assertElementNotContainText($this->findByCss('ul'), $shareWithUserFullName);
        $this->goOutOfIframe();
    }
    

    /**
     * Scenario: As a user I can add a permission after previously adding and deleting one for the same user
     *
     * Given I am Carol
     * And   I am logged in on the password workspace
     * When  I add a temporary permission for Betty
     * And   I can see the save button is enabled
     * And   I delete the just added temporary permission
     * Then  I should not see anymore the changes feedback
     * And   I can see the save button is disabled
     * When  I search again Betty
     * Then  I should see her in the autocomplete results
     *
     * @group LU
     * @group password
     * @group password-share
     * @group v2
     */
    public function testAddAfterAddAndDelete() 
    {
        // Given I am Carol
        $user = User::get('carol');

        // Retrieve the user to share the password with.
        $shareWithUser = User::get('betty');

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // When I add a temporary permission for Betty on a password I own
        $resource = Resource::get(['user' => 'carol', 'id' => UuidFactory::uuid('resource.id.gnupg')]);
        $this->gotoSharePassword($resource['id']);
        $this->addTemporaryPermission($resource, $shareWithUser['name'], $user);

        // And I can see the save button is enabled
        $this->assertNotVisibleByCss('#js-share-save.disabled');

        // And I delete the just added temporary permission
        $this->deleteTemporaryPermission($resource, $shareWithUser['Username']);

        // And I can see the save button is disabled
        $this->assertVisibleByCss('#js-share-save.disabled');

        // Then I should not see anymore the changes feedback
        $this->assertElementNotContainText(
            $this->findByCss('.share-password-dialog'),
            'You need to save to apply the changes'
        );

        // When I search again Betty
        $this->searchAroToGrant($resource, $shareWithUser['name'], $user);

        // Then I should see her in the autocomplete results
        $this->find($shareWithUser['id']);
    }

    /**
     * Scenario: As a user I can share a password with other users
     *
     * Given I am Carol
     * And   I am logged in on the password workspace
     * When  I go to the sharing dialog of a password I own
     * Then  I can see Betty has no right on the password
     * When  I give read access to betty for a password I own
     * Then  I can see Betty has read access on the password
     * When  I logout
     * And   I am Betty
     * And   I am logged in on the password workspace
     * And   I click on a password shared with me
     * And   I click on the link 'copy password'
     * Then  I can see the master key dialog
     * When  I enter my passphrase and click submit
     * Then  I can see a success message telling me the password was copied to clipboard
     * And   the content of the clipboard is valid
     *
     * @group LU
     * @group password
     * @group password-share
     * @group saucelabs
     * @group v2
     */
    public function testSharePasswordWithUserAndView()
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Carol
        $user = User::get('betty');

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // When I go to the sharing dialog of a password I own
        $resource = Resource::get([
            'user' => 'ada',
            'id' => UuidFactory::uuid('resource.id.april')
        ]);
        $this->gotoSharePassword(UuidFactory::uuid('resource.id.april'));

        // Then I can see Betty has no right on the password
        $this->assertElementNotContainText(
            $this->findById('js-share-edit-list'),
            'ada@passbolt.com'
        );

        // When I give read access to betty for a password I own
        $this->sharePassword($resource, 'ada@passbolt.com', $user);

        // Then I can see Betty has read access on the password
        $this->assertPermission($resource, 'ada@passbolt.com', 'can read');

        // When I logout
        $this->logout();

        // And I am Betty
        $user = User::get('ada');

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // And I click on a password shared with me
        $this->clickPassword($resource['id']);

        // And I click on the link 'copy password'
        $this->click('js_wk_menu_secretcopy_button');

        // Then I can see the master key dialog
        $this->assertMasterPasswordDialog($user);

        // When I enter my passphrase and click submit
        $this->enterMasterPassword($user['MasterPassword']);

        // Then I can see a success message telling me the password was copied to clipboard
        $this->assertNotification('plugin_clipboard_copy_success');

        // And the content of the clipboard is valid
        $this->assertClipboard($resource['password']);
    }

    /**
     * Scenario: As a user I should receive an email notification when another user share a password with me
     *
     * Given I am Carol
     * And   I am logged in on the password workspace
     * When  I go to the sharing dialog of a password I own
     * When  I give read access to betty for a password I own
     * And   I access last email sent to betty
     * Then  I should see the expected email title
     * And   I should see the expected email content
     *
     * @group LU
     * @group password
     * @group password-share
     * @group saucelabs
     * @group v2
     * @group skip
     * @group email
     */
    public function testSharePasswordWithUserNotification() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Carol
        $user = User::get('carol');

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // When I go to the sharing dialog of a password I own
        $resource = Resource::get([
            'user' => 'carol',
            'id' => UuidFactory::uuid('resource.id.gnupg')
        ]);

        // And I give read access to betty for a password I own
        $betty = User::get('betty');
        $this->sharePassword($resource, $betty['Username'], $user);

        // When I access last email sent to the other group manager
        $this->getUrl('seleniumtests/showlastemail/' . $betty['Username']);

        // Then I should see the expected email title
        $this->assertMetaTitleContains(sprintf('%s shared the password %s', $user['FirstName'], $resource['name']));

        // And I should see the expected email content
        $this->assertElementContainsText('bodyTable', '-----BEGIN PGP MESSAGE-----');
    }

    /**
     * Scenario: As a user I can share a password with other users, and see them immediately in the sidebar
     *
     * Given I am Carol
     * And   I am logged in on the password workspace
     * When  I go to the sharing dialog of a password I own
     * Then  I can see Betty has no right on the password
     * When  I give read access to betty for a password I own
     * Then  I can see Betty is in the sidebar, under the permissions section
     *
     * @group LU
     * @group password
     * @group password-share
     * @group v2
     * @group unstable
     */
    public function testSharePasswordWithUserAndViewNewPermissionInSidebar() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Carol
        $user = User::get('carol');

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // When I go to the sharing dialog of a password I own
        $resource = Resource::get(
           [
            'user' => 'betty',
            'id'   => UuidFactory::uuid('resource.id.gnupg')
           ]
        );
        $this->gotoSharePassword(UuidFactory::uuid('resource.id.gnupg'));

        // Then I can see Betty has no right on the password
        $this->assertElementNotContainText(
            $this->findById('js-share-edit-list'),
            'betty@passbolt.com'
        );

        // When I give read access to betty for a password I own
        $this->sharePassword($resource, 'betty@passbolt.com', $user);

        // I can see the new permission in sidebar
        $this->assertPermissionInSidebar('betty@passbolt.com', 'can read');
    }

    /**
     * Scenario: As a user I can share a password with a groups
     *
     * Given I am Carol
     * And   I am logged in on the password workspace
     * When  I go to the sharing dialog of a password I own
     * Then  I can see Accounting has no right on the password
     * When  I give read access to Accounting for a password I own
     * Then  I can see Accouning has read access on the password
     * When  I logout
     * And   I am Frances member of Accounting
     * And   I am logged in on the password workspace
     * And   I click on a password shared with me
     * And   I click on the link 'copy password'
     * Then  I can see the master key dialog
     * When  I enter my passphrase and click submit
     * Then  I can see a success message telling me the password was copied to clipboard
     * And   the content of the clipboard is valid
     *
     * @group LU
     * @group password
     * @group password-share
     * @group v2
     * @group saucelabs
     */
    public function testSharePasswordWithGroupAndView() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Carol
        $user = User::get('ada');

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // When I go to the sharing dialog of a password I own
        $resource = Resource::get(
           ['user' => 'ada', 'id' => UuidFactory::uuid('resource.id.apache')]
        );
        $this->gotoSharePassword($resource['id']);

        // Then I can see accounting has no right on the password
        $this->assertElementNotContainText(
            $this->findById('js-share-edit-list'),
            'Accounting'
        );

        // When I give read access to Accounting for a password I own
        $this->sharePassword($resource, 'Accounting', $user);

        // Then I can see Accounting has read access on the password
        $this->assertPermission($resource, 'Accounting', 'can read');

        // When I logout
        $this->logout();

        // And I am Betty
        $user = User::get('frances');

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // And I click on a password shared with me
        $this->clickPassword($resource['id']);

        // And I click on the link 'copy password'
        $this->click('js_wk_menu_secretcopy_button');

        // Then I can see the master key dialog
        $this->assertMasterPasswordDialog($user);

        // When I enter my passphrase and click submit
        $this->enterMasterPassword($user['MasterPassword']);

        // Then I can see a success message telling me the password was copied to clipboard
        $this->assertNotification('plugin_clipboard_copy_success');

        // And the content of the clipboard is valid
        $this->assertClipboard($resource['password']);
    }

    /**
     * Scenario: As a user I should receive an email notification when another user share a password with me
     *
     * Given I am Carol
     * And   I am logged in on the password workspace
     * When  I go to the sharing dialog of a password I own
     * When  I give read access to the group Freelance for a password I own
     * And   I access last email sent to a member of the group
     * Then  I should see the expected email title
     * And   I should see the expected email content
     *
     * @group LU
     * @group password
     * @group password-share
     * @group saucelabs
     * @group v2
     * @group skip
     * @group email
     */
    public function testSharePasswordWithGroupNotification() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Carol
        $user = User::get('ada');

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // When I go to the sharing dialog of a password I own
        $resource = Resource::get(
           ['user' => 'ada', 'id' => UuidFactory::uuid('resource.id.apache')]
        );

        // And I give read access to a group for a password I own
        $freelancer = Group::get(['id' => UuidFactory::uuid('group.id.freelancer')]);
        $jean = User::get('jean');
        $this->sharePassword($resource, $freelancer['name'], $user);

        // When I access last email sent to the other group manager
        $this->getUrl('seleniumtests/showlastemail/' . $jean['Username']);

        // Then I should see the expected email title
        $this->assertMetaTitleContains(sprintf('%s shared the password %s', $user['FirstName'], $resource['name']));

        // And I should see the expected email content
        $this->assertElementContainsText('bodyTable', '-----BEGIN PGP MESSAGE-----');
    }

    /**
     * Scenario: As a user I can unshare a password with a group
     *
     * Given I am Carol
     * And   I am logged in on the password workspace
     * When  I go to the sharing dialog of a password I own
     * And   I remove the access for a user
     * Then  I can see a success message
     * When  I logout and I login with the user who lost the access on the password
     * And   I go to the password workspace
     * And   I shouldn't see anymore the password in the list
     *
     * @group LU
     * @group password
     * @group password-share
     * @group saucelabs
     * @group v2
     */
    public function testUnsharePasswordWithGroupAndView() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Carol
        $user = User::get('ada');

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // When I go to the sharing dialog of a password I own
        $resource = Resource::get(
            ['user' => 'ada', 'id' => UuidFactory::uuid('resource.id.cakephp')]
        );
        $this->gotoSharePassword($resource['id']);

        // Then I can see freelancer has a permission on the password
        $this->assertElementContainsText(
            $this->findById('js-share-edit-list'),
            'Freelancer'
        );

        // When I delete a group permission
        $group = Group::get(['id' => UuidFactory::uuid('group.id.freelancer')]);
        $this->deletePermission($resource, $group['name'], $user);

        // And I see a notice message that the operation was a success
        $this->assertNotification('app_share_share_success');

        // When I logout and I login with a user who is member of the group
        $this->logout();
        $user = User::get('jean');
        $this->loginAs($user);

        // And I go to the password workspace
        $this->gotoWorkspace('password');

        // Then I shouldn't see anymore the password in the list
        $this->assertElementNotContainText(
            $this->find('#js_wsp_pwd_browser'),
            $resource['name']
        );
    }

    /**
     * Scenario: As a user I can share a password with groups, and see them immediately in the sidebar
     *
     * Given I am Ada
     * And   I am logged in on the password workspace
     * When  I go to the sharing dialog of a password I own
     * Then  I can see the group has no right on the password
     * When  I give read access to a group for a password I own
     * Then  I can see the group is in the sidebar, under the permissions section
     *
     * @group LU
     * @group password
     * @group password-share
     * @group v2
     * @group unstable
     */
    public function testSharePasswordWithGroupAndViewNewPermissionInSidebar() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Carol
        $user = User::get('ada');

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // When I go to the sharing dialog of a password I own
        $resource = Resource::get(
            ['user' => 'ada', 'id'   => UuidFactory::uuid('resource.id.apache')]
        );
        $this->gotoSharePassword($resource['id']);

        // Then I can see the group has no right on the password
        $this->assertElementNotContainText(
            $this->findById('js-share-edit-list'),
            'Accounting'
        );

        // When I give read access to the group for a password I own
        $this->sharePassword($resource, 'Accounting', $user);

        // I can see the new permission in sidebar
        $this->assertPermissionInSidebar('Accounting', 'can read');
    }

    /**
     * Scenario: As a user I edit the permissions of a password I own
     *
     * Given I am Carol
     * And   I am logged in on the password workspace
     * When  I go to the sharing dialog of a password I own
     * Then  I can see Betty has update right on the password
     * When  I change the permission of Betty to read access only
     * And   I should see the password remains selected
     * Then  I can see Betty has read access on the password
     *
     * @group LU
     * @group password
     * @group password-share
     * @group v2
     */
    public function testEditPasswordPermission()
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Ada
        $user = User::get('ada');

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // When I go to the sharing dialog of a password I own
        $resourceId = UuidFactory::uuid('resource.id.apache');
        $resource = Resource::get(
            array(
            'user' => 'betty',
            'id' => $resourceId
            )
        );
        $this->gotoSharePassword(UuidFactory::uuid('resource.id.apache'));

        // Then I can see Betty has update right on the password
        $this->assertPermission($resource, 'betty@passbolt.com', 'can update');

        // When I change the permission of Betty to read access only
        $this->editPermission($resource, 'betty@passbolt.com', 'can read', $user);

        // And I should see the password remains selected
        $this->assertTrue($this->isPasswordSelected($resourceId));

        // And I can see Betty has read access on the password
        $this->assertPermission($resource, 'betty@passbolt.com', 'can read');
    }

    /**
     * Scenario: As a user I delete the permission of a password I own
     *
     * Given I am Carol
     * And   I am logged in on the password workspace
     * When  I go to the sharing dialog of a password I own
     * Then  I can see Betty has update right on the password
     * When  I delete the permission of Betty
     * And   I should see the password remains selected
     * Then  I can see Betty has no right anymore
     *
     * @group LU
     * @group password
     * @group password-share
     * @group v2
     */
    public function testDeletePasswordPermission_D()
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Ada
        $user = User::get('ada');

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // When I go to the sharing dialog of a password I own
        $resourceId = UuidFactory::uuid('resource.id.apache');
        $resource = Resource::get(
            array(
            'user' => 'betty',
            'id' =>$resourceId
            )
        );
        $this->gotoSharePassword($resourceId);

        // Then I can see Betty has update right on the password
        $this->assertPermission($resource, 'betty@passbolt.com', 'can update');

        // When I delete the permission of Betty
        $this->deletePermission($resource, 'betty@passbolt.com', $user);

        // And I should see the password remains selected
        $this->assertTrue($this->isPasswordSelected($resourceId));

        // And I go to the sharing dialog of a password I update the permissions
        $this->gotoSharePassword(UuidFactory::uuid('resource.id.apache'));

        // Then I can see Betty has no right anymore
        $this->assertElementNotContainText(
            $this->findById('js-share-edit-list'),
            'betty@passbolt.com'
        );

    }

    /**
     * Scenario: As a user I should not let a resource without at least one owner
     *
     * Given I am Ada
     * And   I am logged in on the password workspace
     * When  I go to the sharing dialog of a password I own
     * Then  I can see the permission type dropdown of the owner Ada is disabled
     * And   I can see the permission delete button of the owner Ada is disabled
     * When  I change the permission of Betty to owner access
     * Then  I can see the permission type dropdown of the owner Ada is enabled
     * And   I can see the permission delete button of the owner Ada is enabled
     * And   I can see the permission type dropdown of the owner Betty is enabled
     * And   I can see the permission delete button of the owner Betty is enabled
     * When  I delete the permission of Betty
     * Then  I can see the permission type dropdown of the owner Ada is disabled
     * And   I can see the permission delete button of the owner Ada is disabled
     * When  I add a temporary permission for Frances
     * And   I change the permission of Frances to owner access
     * Then  I can see the permission type dropdown of the owner Ada is enabled
     * And   I can see the permission delete button of the owner Ada is enabled
     * And   I can see the permission type dropdown of the owner Betty is enabled
     * And   I can see the permission delete button of the owner Betty is enabled
     * When  I click on the save button
     * Then  I see the passphrase dialog
     * When  I enter the passphrase and click submit
     * Then  I wait until I don't see the encryption dialog anymore.
     * And   I see a notice message that the operation was a success
     *
     * @group LU
     * @group password
     * @group password-share
     * @group v2
     */
    public function testAtLeastOneOwner() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Ada
        $userAda = User::get('ada');
        $userBetty = User::get('betty');
        $userFrances = User::get('frances');

        // And I am logged in on the password workspace
        $this->loginAs($userAda);

        // And I go to the sharing dialog of a password I own
        $resourceId = UuidFactory::uuid('resource.id.apache');
        $resource = Resource::get(
            ['user' => 'ada', 'id' => $resourceId]
        );
        $this->gotoSharePassword($resourceId);

        // When I change the permission of Ada to read access
        $this->editTemporaryPermission($resource, 'ada@passbolt.com', 'can read');

        // Then I can see the owner feedback error
        $this->assertElementContainsText(
            $this->findByCss('.share-password-dialog'),
            'The password must have a owner.'
        );

        // And the save button should be disabled
        $this->assertVisibleByCss('#js-share-save.disabled');

        // When I edit the permission of betty to owner
        $this->editTemporaryPermission($resource, 'betty@passbolt.com', 'is owner');

        // Then I should not see the owner feedback error
        $this->assertElementNotContainText(
            $this->findByCss('.share-password-dialog'),
            'The password must have a owner.'
        );

        // And the save button should be enabled
        $this->assertNotVisibleByCss('#js-share-save.disabled');

        // And I can share
        $this->saveShareChanges($userAda);
    }

    /**
     * Scenario: As a user I should be able to drop my owner permission if there is another owner.
     *
     * Given I am Ada
     * And   I am logged in on the password workspace
     * When  I go to the sharing dialog of a password I own
     * And   I change the permission of Betty to owner access
     * And   I delete my own permission
     * And   I click on the save button
     * Then  I see a notice message that the operation was a success
     * And   I should not see the share password dialog
     * And   I should not see the resource anymore in my browser
     *
     * @group LU
     * @group password
     * @group password-share
     * @group v2
     */
    public function testOwnerDropHisPermission() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Ada
        $userAda = User::get('ada');
        $this->setClientConfig($userAda);

        // And I am logged in on the password workspace
        $this->loginAs($userAda);

        // When I go to the sharing dialog of a password I own
        $resourceId = UuidFactory::uuid('resource.id.apache');
        $resource = Resource::get(
            ['user' => 'ada', 'id' => $resourceId]
        );
        $this->gotoSharePassword($resourceId);

        // And I change the permission of Betty to owner access
        $this->editTemporaryPermission($resource, 'betty@passbolt.com', 'is owner', $userAda);

        // And I delete my own permission
        $this->deleteTemporaryPermission($resource, 'ada@passbolt.com');

        // And I can share
        $this->saveShareChanges($userAda);
    }

    /**
     * Scenario: As a user I shouldn't see the permissions of deleted users for a password I own
     *
     * Given I am Ada
     * And   I am logged in on the password workspace
     * When  I go to the sharing dialog of a password I own
     * And   I give read access to betty for a password I own
     * Then  I can see Betty has read access on the password
     *
     * When  I login as Admin
     * And   I go on the user workspace
     * When  I click on a user
     * And   I click on the delete button
     * Then  I should see confirmation dialog
     * When  I click ok in confirmation dialog.
     * Then  I should see a success notification message saying the user is deleted
     *
     * When  I logout
     * And   I login as Ada
     * When  I go to the sharing dialog of a password I own
     * And   I can see that Ada is owner
     * And   I don't see Frances in the list of permissions
     *
     * @group LU
     * @group password
     * @group password-share
     * @group v2
     */
    public function testDeletedUsersShouldntBeVisibleInTheListOfPermissions() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // The user to share a resource with and also the user to delete
        $userU = User::get('ursula');

        // Given I am Ada
        $userA = User::get('ada');

        // And I am logged in on the password workspace
        $this->loginAs($userA);

        // When I go to the sharing dialog of a password I own
        $resource = Resource::get(
            ['user' => $userA['name'], 'id' => UuidFactory::uuid('resource.id.apache')]
        );
        $this->gotoSharePassword($resource['id']);

        // And I give read access to betty for a password I own
        $this->sharePassword($resource, $userU['Username'], $userA);

        // Then I can see Betty has read access on the password
        $this->assertPermission($resource, $userU['Username'], 'can read');

        // When I login as Admin
        $this->logout();
        $admin = User::get('admin');
        $this->loginAs($admin);

        // And I Go to user workspace
        $this->gotoWorkspace('user');

        // When I click on a user
        $this->click('#js_wsp_users_browser .js_grid_column_name');
        $this->clickUser($userU['id']);

        // Then I click on the delete button
        $this->click('js_user_wk_menu_deletion_button');

        // Then I should see confirmation dialog
        $this->assertConfirmationDialog('Delete user?');

        // When	I click ok in confirmation dialog.
        $this->confirmActionInConfirmationDialog();

        // Then I should see a success notification message saying the user is deleted
        $this->assertNotification('app_users_delete_success');

        // When I logout
        $this->logout();

        // And I login as Ada
        $userA = User::get('ada');
        $this->loginAs($userA);

        // When I go to the sharing dialog of a password I own
        $this->gotoSharePassword($resource['id']);

        // Then I can see that Ada is owner
        $this->assertPermission($resource, $userA['Username'], 'is owner', ['closeDialog' => false]);

        // And I don't see France in the list of permissions
        $this->assertNoPermission($resource, $userU['Username']);
    }

    /**
     * Scenario: As LU I can use passbolt on multiple tabs and edit the permissions of a password I own
     *
     * Given I am Ada
     * And   I am logged in on the password workspace
     * When  I open a new tab and go to passbolt url
     * And   I switch back to the first tab
     * And   I go to the sharing dialog of a password I own
     * Then  I can see Betty has update right on the password
     * When  I change the permission of Betty to read access only
     * Then  I should see the password remains selected
     * And   I can see Betty has read access on the password
     * When  I switch to the second tab
     * And   I go to the sharing dialog of a password I own
     * Then  I can see Betty has read access on the password
     * When  I change the permission of Betty to owner
     * Then  I should see the password remains selected
     * And   I can see Betty has read access on the password
     * When  I switch to the first tab
     * And   I go to the sharing dialog of a password I own
     * Then  I can see Betty has read access on the password
     *
     * @group LU
     * @group password
     * @group password-share
     * @group v2
     * @group unstable
     * @todo The latest instruction which checks the latest change on the other tab show the previous result. Tested manually everything works as expected.
     */
    public function testMultipleTabsEditPasswordPermission() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Ada
        $user = User::get('ada');

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // When I open a new tab and go to passbolt url
        $this->openNewTab('');

        // And I switch back to the first tab
        $this->switchToPreviousTab();

        // And I go to the sharing dialog of a password I own
        $resourceId = UuidFactory::uuid('resource.id.apache');
        $resource = Resource::get(['user' => 'betty', 'id' => $resourceId]);
        $this->gotoSharePassword(UuidFactory::uuid('resource.id.apache'));

        // Then I can see Betty has update right on the password
        $this->assertPermission($resource, 'betty@passbolt.com', 'can update', ['closeDialog' => false]);

        // When I change the permission of Betty to read access only
        $this->editPermission($resource, 'betty@passbolt.com', 'can read', $user);

        // Then I should see the password remains selected
        $this->assertTrue($this->isPasswordSelected($resourceId));

        // And I can see Betty has read access on the password
        $this->assertPermission($resource, 'betty@passbolt.com', 'can read');
        $this->goOutOfIframe();

        // And I switch to the second tab
        $this->switchToNextTab();

        // And I go to the sharing dialog of a password I own
        $this->gotoSharePassword(UuidFactory::uuid('resource.id.apache'));

        // Then I can see Betty has read access on the password
        $this->assertPermission($resource, 'betty@passbolt.com', 'can read', ['closeDialog' => false]);

        // When I change the permission of Betty to owner
        $this->editPermission($resource, 'betty@passbolt.com', 'is owner', $user);

        // Then I should see the password remains selected
        $this->assertTrue($this->isPasswordSelected($resourceId));

        // And I can see Betty has read access on the password
        $this->assertPermission($resource, 'betty@passbolt.com', 'is owner');
        $this->goOutOfIframe();

        // When I switch to the first tab
        $this->switchToPreviousTab();

        // And I go to the sharing dialog of a password I own
        $this->gotoSharePassword(UuidFactory::uuid('resource.id.apache'));

        // Then I can see Betty has read access on the password
        $this->assertPermission($resource, 'betty@passbolt.com', 'is owner');
    }

    /**
     * Scenario: As a user I can share a password with other users after I restart the browser
     *
     * Given I am Carol
     * And   I am logged in on the password workspace
     * When  I restart the browser
     * And   I go to the sharing dialog of a password I own
     * And   I give read access to betty for a password I own
     * Then  I can see Betty has read access on the password
     *
     * @group LU
     * @group password
     * @group password-share
     * @group no-saucelabs
     * @group skip
     */
    public function testRestartBrowserAndSharePassword() 
    {
        $this->markTestSkipped();

        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Carol
        $user = User::get('carol');

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // When restart the browser
        $this->restartBrowser();
        $this->waitCompletion();

        // And I go to the sharing dialog of a password I own
        $resource = Resource::get(
           [
            'user' => 'betty',
            'id' => UuidFactory::uuid('resource.id.gnupg')
           ]
        );
        $this->gotoSharePassword(UuidFactory::uuid('resource.id.gnupg'));

        // And I give read access to betty for a password I own
        $this->sharePassword($resource, 'betty@passbolt.com', $user);

        // Then I can see Betty has read access on the password
        $this->assertPermission($resource, 'betty@passbolt.com', 'can read');
    }

    /**
     * Scenario: As a user I can share a password with other users after I close and restore the passbolt tab
     *
     * Given I am Carol
     * And   I am on second tab
     * And   I am logged in on the password workspace
     * When  I close and restore the tab
     * And   I go to the sharing dialog of a password I own
     * And   I give read access to betty for a password I own
     * Then  I can see Betty has read access on the password
     *
     * @group LU
     * @group password
     * @group password-share
     * @group v2
     * @group firefox-only
     * PASSBOLT-2419 close and restore doesn't work with the latest chrome driver
     */
    public function testCloseRestoreTabAndSharePassword() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Carol
        $user = User::get('carol');
        $this->setClientConfig($user);

        // And I am on second tab
        $this->openNewTab();

        // And I am logged in on the password workspace
        $this->loginAs($user, ['setConfig' => false]);

        // When I close and restore the tab
        $this->closeAndRestoreTab();
        $this->waitCompletion();

        // And I go to the sharing dialog of a password I own
        $resource = Resource::get(
            ['user' => 'betty', 'id' => UuidFactory::uuid('resource.id.gnupg')]
        );
        $this->gotoSharePassword(UuidFactory::uuid('resource.id.gnupg'));

        // And I give read access to betty for a password I own
        $this->sharePassword($resource, 'betty@passbolt.com', $user);

        // Then I can see Betty has read access on the password
        $this->assertPermission($resource, 'betty@passbolt.com', 'can read');
    }
}
