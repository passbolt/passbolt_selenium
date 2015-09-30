<?php
/**
 * Feature :  As a user I can copy my password info to clipboard
 *
 * Scenarios :
 * As a user I can see the list of copy options when clicking right on a password
 * As a user I can copy my password to clipboard with a right click
 * As a user I can copy the URI of one resource to clipboard with a right click
 * As a user I can copy the username of one resource to clipboard with a right click
 *
 * @TODO Missing scenarios
 * As a user I can copy my password to clipboard using the action bar button
 * As a user I can copy my password to clipboard by clicking on the password preview
 *
 * @TODO more MASTER KEY TESTS
 * As a user I can cancel and close the master password dialog
 * As a user I should see errors when entering the wrong master key
 *
 * @TODO Move somewhere else
 * As a user I can open the url of a resource in a new tab
 *
 * @copyright    (c) 2015-present Bolt Software Pvt. Ltd.
 * @licence      GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
 */
class PasswordCopyToClipboardTest extends PassboltTestCase
{

    /**
     * Scenario : As a user I can see the list of copy options when clicking right on a password
     *
     * Given    I am Betty
     * And      I am logged in on the password workspace
     * When     I right click on the first password in the list
     * Then     I can see the contextual menu
     * And      I can see the first option is 'Copy username' and is enabled
     * And      I can see next option is 'Copy password' and is enabled
     * And      I can see next option is 'Copy URI' and is enabled
     */
    function testCopyContextualMenuView() {
        // Given I am Betty
        $user = User::get('betty');
        $resource = Resource::get(array('user' => 'betty'));
        $this->setClientConfig($user);

        // And I am logged in on the password workspace
        $this->loginAs($user['Username']);

        // When I right click on the first password in the list
        $this->rightClick($resource['id']);

        // Then I can see the contextual menu
        $e = $this->findById('js_contextual_menu');

        // And I can see the first option is 'Copy username' and is enabled
        $this->assertElementContainsText($e, 'Copy username');

        // And I can see next option is 'Copy password' and is enabled
        $this->assertElementContainsText($e, 'Copy password');

        // And I can see next option is 'Copy URI' and is enabled
        $this->assertElementContainsText($e, 'Copy URI');
    }

    /**
     * Scenario : As a user I can copy a password to clipboard using a right click
     *
     * Given    I am Betty
     * And      I am logged in on the password workspace
     * When     I select the first password in the list
     * And      I right click
     * Then     I can see the contextual menu
     * When     I click on the link 'copy password'
     * Then     I can see the master key dialog
     * When     I enter my master password and click submit
     * Then     I can see a success message saying the password was 'copied to clipboard'
     * And      The content of the clipboard is valid
     */
    public function testCopyPasswordToClipboardViaContextualMenu() {
        // Given I am Betty
        $user = User::get('betty');
        $resource = Resource::get(array('user' => 'betty'));
        $this->setClientConfig($user);

        // And I am logged on the password workspace
        $this->loginAs($user['Username']);

        // When I select the first password in the list
        $this->click('multiple_select_checkbox_' . $resource['id']);

        // And I right click
        $this->rightClick($resource['id']);

        // Then I can see the contextual menu
        $this->assertVisible('js_contextual_menu');

        // When I click on the link 'copy password'
        $this->clickLink('Copy password');

        // Then I can see the master key dialog
        $this->assertMasterPasswordDialog($user);

        // When I enter my master password and click submit
        $this->enterMasterPassword($user['MasterPassword']);

        // Then I can see a success message telling me the password was copied to clipboard
        $this->assertNotification('plugin_secret_copy_success');

        // And the content of the clipboard is valid
        $this->assertClipboard($resource['password']);
    }

    /**
     * Scenario : As a user I can copy the URI of one resource to clipboard with a right click
     *
     * Given    I am Betty
     * And      I am logged in on the password workspace
     * When     I right click on the first password in the list
     * And      I click on the 'Copy URI' in the contextual menu
     * Then     I can see a success message saying the URI was copied to clipboard
     * And      The content of the clipboard is valid
     */
    function testCopyURIToClipboardViaContextualMenu () {
        // Given I am Betty
        $user = User::get('betty');
        $resource = Resource::get(array('user' => 'betty'));
        $this->setClientConfig($user);

        // And I am logged in on the password workspace
        $this->loginAs($user['Username']);

        // When I right click on the first password in the list
        $this->rightClick($resource['id']);

        // When I click on the 'Copy URI' in the contextual menu
        $this->clickLink('Copy URI');

        // Then I can see a success message saying the uri was copied to clipboard
        $this->assertNotification('plugin_clipboard_copy_success');

        // And the content of the clipboard is valid
        $this->assertClipboard($resource['uri']);
    }

    /**
     * Scenario : As a user I can copy the username of one resource to clipboard with a right click
     *
     * Given    I am Betty
     * And      I am logged in on the password workspace
     * When     I right click on the first password in the list
     * And      I click on the 'Copy username' in the contextual menu
     * Then     I can see a success message saying the username was copied to clipboard
     * And      The content of the clipboard is valid
     */
    function testCopyUsernameToClipboardViaContextualMenu() {
        // Given I am Betty
        $user = User::get('betty');
        $resource = Resource::get(array('user' => 'betty'));
        $this->setClientConfig($user);

        // And I am logged in on the password workspace
        $this->loginAs($user['Username']);

        // When I right click on the first password in the list
        $this->rightClick($resource['id']);

        // When I click on the link 'copy URI' in the contextual menu
        $this->clickLink('Copy username');

        // Then I can see a success message saying the username was copied to clipboard
        $this->assertNotification('plugin_clipboard_copy_success');

        // And the content of the clipboard is valid
        $this->assertClipboard($resource['username']);
    }
}