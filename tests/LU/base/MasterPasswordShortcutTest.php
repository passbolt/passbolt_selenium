<?php
/**
 * Feature :  As a user I can enter my passphrase using the keyboard shortcuts
 *
 * Scenarios :
 * As a user I can copy a password using the button in the action bar, and enter my passphrase from keyboard only
 * As a user I can copy a password using the button in the action bar, and enter my passphrase from keyboard only by pressing tab first
 * As a user I can edit the secret of a password I have own, and enter my passphrase from keyboard only
 * As a user I can edit the secret of a password I have own, and enter my passphrase from keyboard only, using tab first
 *
 * @copyright (c) 2017 Passbolt SARL
 * @licence   GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class MasterPasswordShortcutTest extends PassboltTestCase
{

    /**
     * @group saucelabs
     * Scenario : As a user I can copy a password using the button in the action bar,
     * and enter my passphrase from keyboard only.
     *
     * Given I am Ada
     * And I am logged in on the password workspace
     * When I click a password
     * When I click on the copy button in the action bar
     * Then I can see the master key dialog
     * When I enter my passphrase by typing it on keyboard only
     * Then I can see a success message saying the password was 'copied to clipboard'
     * And      The content of the clipboard is valid
     */
    function testMasterPasswordShortcutCopyPasswordButton() 
    {
        // Given I am Ada
        $user = User::get('ada');
        $resource = Resource::get(array('user' => 'ada'));


        // And I am logged in on the password workspace
        $this->loginAs($user);

        // When I click on the first password in the list
        $this->clickPassword($resource['id']);

        // When I click on the link 'copy password'
        $this->click('js_wk_menu_secretcopy_button');

        // When I enter my passphrase from keyboard only.
        $this->enterMasterPasswordWithKeyboardShortcuts($user['MasterPassword']);

        // Then I can see a success message telling me the password was copied to clipboard
        $this->assertNotification('plugin_clipboard_copy_success');

        // And the content of the clipboard is valid
        $this->assertClipboard($resource['password']);
    }

    /**
     * Scenario : As a user I can copy a password using the button in the action bar,
     * and enter my passphrase from keyboard only by pressing tab first.
     *
     * Given I am Ada
     * And I am logged in on the password workspace
     * When I click a password
     * When I click on the copy button in the action bar
     * Then I can see the master key dialog
     * When I press tab
     * Then I can see that the passphrase field gets the focus
     * When I type my passphrase on the keyboard
     * And I press enter
     * Then I can see a success message saying the password was 'copied to clipboard'
     * And      The content of the clipboard is valid
     */
    function testMasterPasswordShortcutTabFirstCopyPasswordButton() 
    {
        // Given I am Ada
        $user = User::get('ada');
        $resource = Resource::get(array('user' => 'ada'));


        // And I am logged in on the password workspace
        $this->loginAs($user);

        // When I click on the first password in the list
        $this->clickPassword($resource['id']);

        // When I click on the link 'copy password'
        $this->click('js_wk_menu_secretcopy_button');

        // When I enter my passphrase from keyboard only, by pressing tab first.
        $this->enterMasterPasswordWithKeyboardShortcuts($user['MasterPassword'], true);

        // Then I can see a success message telling me the password was copied to clipboard
        $this->assertNotification('plugin_clipboard_copy_success');

        // And the content of the clipboard is valid
        $this->assertClipboard($resource['password']);
    }

    /**
     * Scenario: As a user I can edit the secret of a password I have own,
     * and enter my passphrase from keyboard only.
     *
     * Given I am Ada
     * And I am logged in on the password workspace
     * And I am editing a password I own
     * When I click on the secret password field
     * Then I see the passphrase dialog
     * When I enter the passphrase from keyboard only
     * And I press enter
     * Then I can see the password decrypted in the secret field
     */
    function testMasterPasswordShortcutEditPasswordSecret() 
    {
        // Given I am Ada
        $user = User::get('ada');


        // And I am logged in
        $this->loginAs($user);

        // And I am editing a password I own
        $resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));
        $this->gotoEditPassword($resource['id']);

        // When I switch to the password iframe
        $this->goIntoSecretIframe();

        $this->click('js_secret');

        $this->goOutOfIframe();

        // When I enter my passphrase from keyboard only, by pressing tab first.
        $this->enterMasterPasswordWithKeyboardShortcuts($user['MasterPassword']);

        $this->goIntoSecretIframe();

        // Wait for password to be decrypted.
        $this->waitUntilSecretIsDecryptedInField();

        // Assert that password matches what we expect.
        $this->assertInputValue('js_secret', $resource['password']);

        $this->goOutOfIframe();
    }

    /**
     * Scenario: As a user I can edit the secret of a password I have own,
     * and enter my passphrase from keyboard only, using tab first.
     *
     * Given I am Ada
     * And I am logged in on the password workspace
     * And I am editing a password I own
     * When I click on the secret password field
     * Then I see the passphrase dialog
     * When I press tab
     * Then I can see that the passphrase field gets the focus
     * When I type my passphrase on the keyboard
     * And I press enter
     * Then I can see the password decrypted in the secret field
     */
    function testMasterPasswordShortcutTabFirstEditPasswordSecret() 
    {
        // Given I am Ada
        $user = User::get('ada');


        // And I am logged in
        $this->loginAs($user);

        // And I am editing a password I own
        $resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));
        $this->gotoEditPassword($resource['id']);

        // When I switch to the password iframe
        $this->goIntoSecretIframe();

        $this->click('js_secret');

        $this->goOutOfIframe();

        // When I enter my passphrase from keyboard only, by pressing tab first.
        $this->enterMasterPasswordWithKeyboardShortcuts($user['MasterPassword'], true);

        $this->goIntoSecretIframe();

        // Wait for password to be decrypted.
        $this->waitUntilSecretIsDecryptedInField();

        $this->assertInputValue('js_secret', $resource['password']);

        $this->goOutOfIframe();
    }
}