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
 * Feature: As a user I can get the system to remember my passphrase for a limited time
 *
 * Scenarios :
 * As a user I can have my passphrase remembered by the system from the login page
 * As a user I can have my passphrase remembered by the system.
 */
namespace Tests\LU\Base;

use App\Actions\MasterPasswordActionsTrait;
use App\Actions\PasswordActionsTrait;
use App\Assertions\ClipboardAssertions;
use App\Assertions\MasterPasswordAssertionsTrait;
use App\Assertions\WorkspaceAssertionsTrait;
use App\Lib\UuidFactory;
use App\PassboltTestCase;
use Data\Fixtures\User;
use Data\Fixtures\Resource;

class MasterPasswordRememberTest extends PassboltTestCase
{
    use ClipboardAssertions;
    use MasterPasswordActionsTrait;
    use MasterPasswordAssertionsTrait;
    use PasswordActionsTrait;
    use WorkspaceAssertionsTrait;

    /**
     * Scenario: As a user I can have my passphrase remembered by the system from the login page
     *
     * Given I am Ada
     * When  I got to the login page
     * Then  I should the see a remember me option
     * When  I click the remember me box
     * And   I login on the password workspace
     * And   I copy the secret of a password
     * Then  The password should have been copied to clipboard without asking me for my master password
     *
     * @group LU
     * @group master-password
     * @group saucelabs
     * @group v2
     */
    function testMasterPasswordRemember_RememberFromLogin()
    {
        // Given I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

        // When I got to the login page
        $this->getUrl('/');

        // Then I should the see a remember me option
        $this->waitUntilISee('#passbolt-iframe-login-form.ready');
        $this->goIntoLoginIframe();
        $this->waitUntilISee('#rememberMe');
        $this->goOutOfIframe();

        // When I click the remember me box
        $this->goIntoLoginIframe();
        $this->click('#rememberMe');
        $this->goOutOfIframe();

        // And I login on the password workspace
        $this->loginAs($user, ['setConfig' => false]);

        // And I copy the secret of a password
        $rsA = Resource::get(['user' => 'ada', 'id' => UuidFactory::uuid('resource.id.apache')]);
        $this->clickPassword($rsA['id']);
        $this->click('js_wk_menu_secretcopy_button');
        $this->waitCompletion();

        // Then The password should have been copied to clipboard without asking me for my master password
        $this->assertNotification('plugin_clipboard_copy_success');
        $this->waitUntilNotificationDisappears('plugin_clipboard_copy_success');
        $this->assertClipboard($rsA['password']);
    }

    /**
     * Scenario: As a user I can have my passphrase remembered by the system.
     *
     * Given I am Ada
     * And   I am logged in on the password workspace
     * When  I click on a password in the list
     * And   I click on the link 'copy password'
     * Then  I should see the passphrase dialog.
     * And   I should see a checkbox remember my passphrase.
     * When  I enter my passphrase from keyboard only
     * Then  The password should have been copied to clipboard
     * When  I click on another password in the list
     * And   I click on the link 'copy password'
     * Then  I should see the passphrase dialog
     * When  I enter my passphrase from keyboard only
     * And   I check the remember checkbox
     * Then  The password should have been copied to clipboard
     * When  I click on another password in the list
     * And   I click again on the copy button in the action bar
     * Then  The password should have been copied to clipboard
     *
     * @group LU
     * @group master-password
     * @group saucelabs
     * @group v2
     */
    function testMasterPasswordRemember() 
    {
        // Given I am Ada
        $user = User::get('ada');

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // When I click on a password in the list
        $rsA = Resource::get(['user' => 'ada', 'id' => UuidFactory::uuid('resource.id.apache')]);
        $this->clickPassword($rsA['id']);

        // And I click on the link 'copy password'
        $this->click('js_wk_menu_secretcopy_button');

        // Then I should see the passphrase dialog.
        $this->assertMasterPasswordDialog($user);

        // And I should see a checkbox remember my passphrase
        $this->goIntoMasterPasswordIframe();
        $this->assertVisible('js_remember_master_password');
        $this->assertVisible('js_remember_master_password_duration');
        $this->goOutOfIframe();

        // When I enter my passphrase from keyboard only
        $this->enterMasterPassword($user['MasterPassword'], false);
        $this->waitCompletion();

        // Then The password should have been copied to clipboard
        $this->assertNotification('plugin_clipboard_copy_success');
        $this->waitUntilNotificationDisappears('plugin_clipboard_copy_success');
        $this->assertClipboard($rsA['password']);

        // When I click on another password in the list
        $rsB = Resource::get(['user' => 'ada', 'id' => UuidFactory::uuid('resource.id.bower')]);
        $this->clickPassword($rsB['id']);

        // And I click on the link 'copy password'
        $this->click('js_wk_menu_secretcopy_button');

        // Then I should see the passphrase dialog
        $this->assertMasterPasswordDialog($user);

        // When I enter my passphrase from keyboard only
        // And I check the remember checkbox
        $this->enterMasterPassword($user['MasterPassword'], true);
        $this->waitUntilIDontSee('#passbolt-iframe-progress-dialog');
        $this->waitCompletion();

        // Then The password should have been copied to clipboard
        $this->assertNotification('plugin_clipboard_copy_success');
        $this->waitUntilNotificationDisappears('plugin_clipboard_copy_success');
        $this->assertClipboard($rsB['password']);

        // When I click on another password in the list
        $rsC = Resource::get(array('user' => 'ada', 'id' => UuidFactory::uuid('resource.id.centos')));
        $this->clickPassword($rsC['id']);

        // And I click on the link 'copy password'
        $this->click('js_wk_menu_secretcopy_button');
        $this->waitCompletion();

        // Then The password should have been copied to clipboard
        $this->assertNotification('plugin_clipboard_copy_success');
        $this->waitUntilNotificationDisappears('plugin_clipboard_copy_success');
        $this->assertClipboard($rsC['password']);
    }
}