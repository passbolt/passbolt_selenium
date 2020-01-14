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
namespace App\Actions;

trait MasterPasswordActionsTrait
{
    /**
     * Copy a password to clipboard
     *
     * @param $resource
     * @param $user
     */
    public function copyToClipboard($resource, $user) 
    {
        $this->rightClickPassword($resource['id']);
        $this->waitUntilISee('#js_contextual_menu');
        $this->clickLink('Copy password');
        $this->assertMasterPasswordDialog($user);
        $this->enterMasterPassword($user['MasterPassword']);
        $this->assertNotification('plugin_clipboard_copy_success');
    }

    /**
     * Enter the password in the passphrase iframe
     *
     * @param $pwd
     * @param $remember
     */
    public function enterMasterPassword($pwd, $remember = false) 
    {
        // Get out of the previous iframe in case we are in one
        $this->goOutOfIframe();
        // Go into the react iframe.
        $this->goIntoReactAppIframe();
        // I wait until the passphrase entry dialog is displayed.
        $this->waitUntilISee('.dialog.passphrase-entry');
        // Fill the passphrase entry.
        $this->inputText('.passphrase-entry input[name="passphrase"]', $pwd);
        // Remember the passphrase if requested.
        if ($remember == true) {
            $this->checkCheckbox('.passphrase-entry input[name="rememberMe"]');
        }
        // Submit.
        $this->click('.passphrase-entry input[type=submit]');
        // I go out of the iframe
        $this->goOutOfIframe();
    }

}