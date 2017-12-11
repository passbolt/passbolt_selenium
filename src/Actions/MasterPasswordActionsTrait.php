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
namespace App\actions;

use Facebook\WebDriver\Exception\NoSuchElementException;

trait MasterPasswordActionsTrait
{

    use PasswordActionsTrait;

    /**
     * Copy a password to clipboard
     *
     * @param  $resource
     * @param  $user
     * @throws Exception
     * @throws NoSuchElementException
     */
    public function copyToClipboard($resource, $user) 
    {
        $this->rightClickPassword($resource['id']);
        $this->waitUntilISee('js_contextual_menu');
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
        $this->waitUntilISee('#passbolt-iframe-master-password.ready');
        $this->goIntoMasterPasswordIframe();
        $this->inputText('js_master_password', $pwd);

        if ($remember == true) {
            $this->checkCheckbox('js_remember_master_password');
        }

        // Get master password submit button element.
        $submit = $this->find('master-password-submit');

        // Click on button.
        $submit->click();

        // Check that button has processing class.
        try {
            $this->assertElementHasClass(
                $submit,
                'processing'
            );
        } catch(StaleElementReferenceException $e) {
            // Everything alright.
            // It's just that the element has already been removed from the dom.
        } catch(UnknownServerException $e) {
            // Everything alright.
            // It's just that the element has already been removed and the selenium doesn't find it.
        }

        $this->goOutOfIframe();
        $this->waitUntilIDontSee('#passbolt-iframe-master-password');
    }

    /**
     * Enter the password in the passphrase iframe using only keyboard, and no clicks.
     *
     * @param $pwd
     *   passphrase string
     * @param $tabFirst
     *   if tab should be pressed first to give focus
     */
    public function enterMasterPasswordWithKeyboardShortcuts($pwd, $tabFirst = false) 
    {
        $this->waitUntilISee('#passbolt-iframe-master-password.ready');

        $this->goIntoMasterPasswordIframe();

        // The scenario using tab can only be tested on chrome.
        // Firefox cannot use keyboard on element not visible.
        // The element we use to hold the user focus is hidden.
        if ($this->getBrowser()['type'] == 'chrome') {
            if ($tabFirst) {
                $this->pressTab();
                $this->assertElementHasFocus('js_master_password');
            }
            $this->typeTextLikeAUser($pwd);
            $this->pressEnter();
        } else {
            $this->inputText('js_master_password', $pwd);
            $this->pressEnter();
        }

        try {
            $this->waitUntilISee('#master-password-submit.processing');
        } catch (StaleElementReferenceException $e) {
            // Do nothing.
            // This happens sometimes when the master password decryption is too fast
        }

        $this->goOutOfIframe();
    }

    /**
     * Type master password like a user would do, pressing key after key.
     * Take in account that with firefox we cannot sendKeys to invisible element.
     *
     * @param $text
     */
    public function typeMasterPasswordLikeAUser($text) 
    {
        $activeElementIsMasterPasswordFocus = false;
        $activeElt = $this->getDriver()->switchTo()->activeElement();

        // With the Firefox driver we cannot use the sendKeys function on invisible elements.
        // If the current active element is the "focus first" element, make it visible first.
        if ($this->getBrowser()['type'] == 'firefox') {
            $activeElementIsMasterPasswordFocus = false;
            $activeEltId = $activeElt->getAttribute('id');
            if($activeEltId == 'js_master_password_focus_first') {
                $activeElementIsMasterPasswordFocus = true;
                $this->getDriver()->executeScript("$('#$activeEltId').css('line-height', '1px');");
            }
        }

        // Type each character
        $this->typeTextLikeAUser($text);

        // Hide the "focus first" element if required.
        if ($activeElementIsMasterPasswordFocus) {
            $this->getDriver()->executeScript("$('#$activeEltId').css('line-height', '0');");
        }
    }

    /**
     * Dig into the passphrase iframe
     */
    public function goIntoMasterPasswordIframe() 
    {
        $this->getDriver()->switchTo()->frame('passbolt-iframe-master-password');
    }

}