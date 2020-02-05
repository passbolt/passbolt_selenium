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
 * Feature: As a user I can edit a password
 *
 * Scenarios:
 * As a user I can edit a password
 * As a user I can edit a password using the right click contextual menu
 * As a user I can not edit a password I have only read access to
 */
namespace Tests\LU\Base;

use App\Actions\ConfirmationDialogActionsTrait;
use App\Actions\MasterPasswordActionsTrait;
use App\Actions\PasswordActionsTrait;
use App\Actions\SidebarActionsTrait;
use App\Actions\WorkspaceActionsTrait;
use App\Assertions\ClipboardAssertions;
use App\Assertions\ConfirmationDialogAssertionsTrait;
use App\Assertions\MasterPasswordAssertionsTrait;
use App\Assertions\PasswordAssertionsTrait;
use App\Assertions\ShareAssertionsTrait;
use App\Assertions\WorkspaceAssertionsTrait;
use App\PassboltTestCase;
use Data\Fixtures\User;
use Data\Fixtures\Resource;

class PasswordEditTest extends PassboltTestCase
{
    use ClipboardAssertions;
    use ConfirmationDialogActionsTrait;
    use ConfirmationDialogAssertionsTrait;
    use MasterPasswordActionsTrait;
    use MasterPasswordAssertionsTrait;
    use PasswordActionsTrait;
    use PasswordAssertionsTrait;
    use ShareAssertionsTrait;
    use SidebarActionsTrait;
    use WorkspaceActionsTrait;
    use WorkspaceAssertionsTrait;

    /**
     * Scenario: As a user I can edit the secret of a password I have own
     *
     * Given I am Ada
     * And   I am logged in on the password workspace
     * And   I am editing a password I own
     * When  I click on the secret password field
     * Then  I see the passphrase dialog
     * When  I enter the passphrase and click submit
     * Then  I can see the password edit dialog
     * When  I enter a new password
     * And   I press the submit button
     * Then  I can see the success notification
     * When  I copy the password to clipboard
     * Then  I can see that password have been updated
     *
     * @group LU
     * @group password
     * @group password-edit
     * @group v2
     * @group saucelabs
     */
    public function testEditPassword()
    {
        $resource = Resource::get([
            'user' => 'ada',
            'permission' => 'owner'
        ]);
        $meta = [
            'id' => $resource['id'],
            'name' => 'updated_name',
            'username' => 'updated_username',
            'uri' => 'https://updated_uri',
            'password' => 'updated_password',
            'description' => 'updated_description'
        ];

        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Ada
        $user = User::get('ada');

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // When I am editing a password I own
        $this->editPassword($meta, $user);

        // Then the password name has been updated in the grid
        $this->assertElementContainsText('#js_wsp_pwd_browser .tableview-content', $meta['name']);

        // And the password name has been updated in the sidebar
        $this->assertVisible('js_pwd_details');
        $this->assertElementContainsText('js_pwd_details', $meta['name']);

        // And the password name has been updated in the grid
        $this->assertElementContainsText('#js_wsp_pwd_browser .tableview-content', $meta['username']);

        // And the password name has been updated in the sidebar
        $this->assertVisible('js_pwd_details');
        $this->assertElementContainsText('js_pwd_details', $meta['username']);

        // And the password uri has been updated in the grid
        $this->assertElementContainsText('#js_wsp_pwd_browser .tableview-content', $meta['uri']);

        // And the password uri has been updated in the grid
        $this->assertElementContainsText('#js_pwd_details .uri', $meta['uri']);

        // And the password description has been updated in the sidebar
        $this->clickSecondarySidebarSectionHeader('description');
        $this->assertElementContainsText('#js_pwd_details .description_content', $meta['description']);

        // And the secret has been updated
        $this->copyToClipboard($resource, $user);
        $this->assertClipboard($meta['password']);

        // And I have received an email notifying me the changes
        $this->getUrl('seleniumtests/showlastemail/' . $user['Username']);
        $this->assertMetaTitleContains('Ada edited the password ' . $meta['name']);
        $this->assertElementContainsText('bodyTable', $meta['name']);
        $this->assertElementContainsText('bodyTable','Ada (' . $user['Username'] . ')');
    }

    /**
     * Scenario: As a user I can edit a password using the right click contextual menu
     *
     * Given I am Ada
     * And   I am logged in on the password workspace
     * When  I right click on a password I own
     * Then  I can see the contextual menu
     * And   I can see the the edit option is enabled
     * When  I click on the edit link in the contextual menu
     * Then  I can see the edit password dialog
     *
     * @group LU
     * @group password
     * @group password-edit
     * @group v2
     */
    public function testEditPasswordRightClick()
    {
        // Given I am Ada
        // And I am logged in on the password workspace
        $this->loginAs(User::get('ada'));

        // When I right click on a password I own
        $resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));
        $this->rightClickPassword($resource['id']);

        // Then I can see the contextual menu
        $this->assertVisible('js_contextual_menu');

        // And I can see the the edit option is enabled
        // @TODO PASSBOLT-1028

        // When I click on the edit link in the contextual menu
        $this->click('#js_password_browser_menu_edit a');

        // Then I can see the edit password dialog
        $this->goIntoReactAppIframe();
        $this->assertVisibleByCss('.edit-password-dialog');
    }

    /**
     * Scenario: As a user I can not edit a password I have only read access to
     *
     * Given I am Ada
     * And   I am logged in on the password workspace
     * And   I click on a password I cannot edit
     * Then  I can see the edit button is not active
     * When  I right click on a password I cannot edit
     * Then  I can see the contextual menu
     * And   I can see the edit option is disabled
     *
     * @group LU
     * @group password
     * @group password-edit
     * @group v2
     */
    public function testEditPasswordNoRightNoEdit()
    {
        // Given I am Ada
        $user = User::get('ada');

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // And I click on a password I cannot edit
        $r = Resource::get([
            'user' => 'ada',
            'permission' => 'read'
        ]);
        $this->clickPassword($r['id']);

        // Then I can see the edit button is not active
        $editButton = $this->find('#js_wk_menu_edition_button');
        $this->assertElementHasClass($editButton, 'disabled');

        // When I right click on a password I cannot edit
        $this->rightClickPassword($r['id']);

        // Then I can see the contextual menu
        $this->findById('js_contextual_menu');

        // And I can see the edit option is disabled
        $this->click('#js_password_browser_menu_edit a');
        $this->goIntoReactAppIframe();
        $this->assertNotVisibleByCss('.edit-password-dialog');
    }
}
