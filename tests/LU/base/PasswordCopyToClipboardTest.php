<?php
/**
 * Feature :  As a user I should be able to copy my password to the clipboard
 *
 * Scenarios :
 * As a user I should be able to copy my password to the clipboard
 * As a user I should see errors when entering the wrong master key
 *
 * @copyright    (c) 2015-present Bolt Software Pvt. Ltd.
 * @licence      GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
 */
class PasswordCopyToClipboardTest extends PassboltTestCase
{
    public function testCopyToClipboard() {
        // Given I am Betty
        $user = User::get('betty');
        $resource = Resource::get('betty');
        //$this->setClientConfig($user);

        // And the database is in a clean state
        //$this->PassboltServer->resetDatabase(1);

        // And I am logged in as Carol, and I go to the user workspace
        $this->loginAs($user['Username']);

        // When I select the first password in the list
        $this->click('multiple_select_checkbox_' . $resource[0]['id']);

        // And I right click
        $this->rightClick($resource[0]['id']);

        // Then I can see the contextual menu
        $this->assertTrue($this->isVisible('js_contextual_menu'));

        // When I select copy password to clipboard in the contextual menu
        $this->clickLink('Copy password');

        // Then I can see the master key dialog
        $this->assertTrue($this->isVisible('﻿passbolt-iframe-master-password'));
        $this->goIntoMasterPasswordIframe();

        // When I enter my master key
        $this->inputText('js_master_password',$user['MasterPassword']);

        // And click on save
        $this->click('master-password-submit');

        // Then I can see a success message telling me the password was copied
        $this->waitUntilISee('.notification-container', '/to clipboard/i');

        // And I have the password in my clipboard

    }

    // I can see the list of options when clicking right
    // I can close the master password dialog
    // Using more > copy to clipboard
    // Using right click copy to clipboard
    // copy username
    // copy url
}