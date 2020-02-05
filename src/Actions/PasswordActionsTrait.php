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

use App\Lib\Color;

trait PasswordActionsTrait
{
    /**
     * Go to the password workspace and click on the create password button
     */
    public function gotoCreatePassword() 
    {
        if(!$this->isVisible('.page.password')) {
            $this->getUrl('');
            $this->waitUntilISee('.page.password');
            $this->waitUntilISee('#js_wsp_create_button');
        }
        $this->click('#js_wsp_create_button');
        try {
            $this->waitUntilISee('.main-action-wrapper ul.dropdown-content', null, 1);
            $this->click('.main-action-wrapper ul.dropdown-content li.create-resource');
        } catch (Exception $e) {
            // nothing to do, CE does not have a drop down
        }
    }

    /**
     * Click on a password inside the password workspace.
     *
     * @param string $id id of the password
     */
    public function clickPassword($id) 
    {
        if (!$this->isVisible('.page.password')) {
            $this->fail('Click password requires to be on the password workspace');
        }
        $this->click('#resource_' . $id . ' .cell_name');
    }

    /**
     * Right click on a password with a given id.
     *
     * @param string $id
     */
    public function rightClickPassword($id) 
    {
        if(!$this->isVisible('.page.password')) {
            $this->fail("right click password requires to be on the password workspace");
        }
        $eltSelector = '#resource_' . $id . ' .cell_name';
        //$this->rightClick('#resource_' . $id . ' .cell_name');
        // Instead of rightClick function, we execute a script.
        // This is because passbolt opens a contextual menu on the mousedown event
        // and not on the contextMenu event. (and the primitive mouseDown doesn't exist in the webDriver).
        $this->getDriver()->executeScript(
            "
			var element = jQuery('$eltSelector')[0];
            var rect = element.getBoundingClientRect();
            jQuery('.tableview-content')[0].scrollTo(rect.left, rect.top);
            var mouseDownEvent = new MouseEvent('contextmenu', {view: window, clientX:rect.left, clientY:rect.top, bubbles: true, cancelable: true, button:2});
			element.dispatchEvent(mouseDownEvent);
		"
        );
        // Without this little interval, the menu doesn't have time to open.
        $this->waitUntilISee('#js_contextual_menu.ready');
    }

    /**
     * Mark or unmark a password as a favorite
     *
     * @param $id string
     */
    public function clickPasswordFavorite($id) 
    {
        $eltSelector = "tr#resource_$id .cell_favorite i";
        $this->click($eltSelector);
        $this->waitCompletion();
    }

    /**
     * Goto the edit password dialog for a given resource id
     *
     * @param $id string
     */
    public function gotoEditPassword($id) 
    {
        if(!$this->isVisible('.page.password')) {
            $this->getUrl('');
            $this->waitUntilISee('.page.password');
            $this->waitUntilISee('#js_wsp_create_button');
        }
        $this->releaseFocus(); // we click somewhere in case the password is already active
        if (!$this->isPasswordSelected($id)) {
            $this->clickPassword($id);
        }
        $this->click('js_wk_menu_edition_button');
    }

    /**
     * Input a given string in the secret field (create only)
     *
     * @param string $secret
     */
    public function inputSecret($secret) 
    {
        $this->goIntoSecretIframe();
        $this->inputText('js_secret', $secret);
        $this->goOutOfIframe();
    }

    /**
     * Put the focus inside the secret iframe
     */
    public function goIntoSecretIframe() 
    {
        $this->getDriver()->switchTo()->frame('passbolt-iframe-secret-edition');
    }

    /**
     * Helper to create a password
     * @param {array} $password The password details
     * @param {array} $user The user who is creating the password
     * @return {string} The created resource id
     */
    public function createPassword($password, $user)
    {
        $this->gotoCreatePassword();
        $this->goIntoReactAppIframe();
        $this->waitUntilISee('.create-password-dialog');
        $this->inputText('.create-password-dialog input[name="name"]', isset($password['name']) ? $password['name'] : '');
        $this->inputText('.create-password-dialog input[name="username"]', isset($password['username']) ? $password['username'] : '');
        $this->inputText('.create-password-dialog input[name="uri"]', isset($password['uri']) ? $password['uri'] : '');
        $this->inputText('.create-password-dialog input[name="password"]', isset($password['password']) ? $password['password'] : '');
        $this->inputText('.create-password-dialog textarea[name="description"]', isset($password['description']) ? $password['description'] : '');
        $this->click('.create-password-dialog input[type=submit]');
        $this->waitUntilISee('.dialog.passphrase-entry');
        $this->inputText('.passphrase-entry input[name="passphrase"]', $user['Username']);
        $this->click('.passphrase-entry input[type=submit]');
        $this->waitUntilIDontSee('.create-password-dialog');
        $this->goOutOfIframe();
        $this->assertNotificationMessage('The password has been added successfully');
        $this->waitUntilNotificationDisappear();

        $resourceId = null;
        $this->waitUntil(
            function () use (&$resourceId, $password) {
                $resourceId = $this->findPasswordIdByName($password['name']);
            }, null, 4
        );
        
        return $resourceId;
    }

    /**
     * Find a password id by name in the interface.
     *
     * @param $name
     * @return uuid id
     */
    public function findPasswordIdByName($name) 
    {
        $xpathSelector = "//div[contains(@class, 'tableview-content')]//tr[.//*[contains(text(),'" . $name . "')]]";
        $resource = $this->findByXpath($xpathSelector);
        return str_replace('resource_', '', $resource->getAttribute('id'));
    }

    /**
     * Edit a password helper
     *
     * @param $password
     * $param $user
     */
    public function editPassword($password, $user = []) 
    {
        $this->gotoEditPassword($password['id']);
        $this->goIntoReactAppIframe();
        $this->waitUntilISee('.edit-password-dialog');

        if (isset($password['name'])) {
            $this->inputText('.edit-password-dialog input[name="name"]', isset($password['name']) ? $password['name'] : '');
        }
        if (isset($password['username'])) {
            $this->inputText('.edit-password-dialog input[name="username"]', isset($password['username']) ? $password['username'] : '');
        }
        if (isset($password['uri'])) {
            $this->inputText('.edit-password-dialog input[name="uri"]', isset($password['uri']) ? $password['uri'] : '');
        }
        if (isset($password['password'])) {
            $this->click('.edit-password-dialog input[name="password"]');
            $this->waitUntilISee('.dialog.passphrase-entry');
            $this->inputText('.passphrase-entry input[name="passphrase"]', $user['Username']);
            $this->click('.passphrase-entry input[type=submit]');
            $this->waitUntilElementHasFocus('.edit-password-dialog input[name="password"]');
            $this->inputText('.edit-password-dialog input[name="password"]', isset($password['password']) ? $password['password'] : '');
        }
        if (isset($password['description'])) {
            $this->inputText('.edit-password-dialog textarea[name="description"]', isset($password['description']) ? $password['description'] : '');
        }
        $this->click('.edit-password-dialog input[type=submit]');
        if (isset($password['password'])) {
            $this->waitUntilISee('.dialog.passphrase-entry');
            $this->inputText('.passphrase-entry input[name="passphrase"]', $user['Username']);
            $this->click('.passphrase-entry input[type=submit]');
        }
        $this->waitUntilIDontSee('.edit-password-dialog');
        $this->goOutOfIframe();
        $this->assertNotificationMessage('The password has been updated successfully');
        $this->waitUntilNotificationDisappear();
    }

    /**
     * Assert if a security token match user parameters
     *
     * @param $user array see fixtures
     * @param $context where is the security token (master or else)
     */
    public function assertSecurityToken($user, $context = null) 
    {
        // check base color
        $this->waitUntilISee('.security-token');
        $securityTokenElt = $this->findByCss('.security-token');
        $this->assertElementContainsText($securityTokenElt, $user['TokenCode']);
        $this->waitUntilCssValueEqual($securityTokenElt, 'background-color', Color::hexToRgba($user['TokenColor']), 2);
        $this->waitUntilCssValueEqual($securityTokenElt, 'color', Color::hexToRgba($user['TokenTextColor']), 2);

        if ($context != 'has_encrypted_secret') {
            // check color switch when input is selected
            switch ($context) {
            case 'master':
                $this->waitUntilElementHasFocus('js_master_password_focus_first');
                $this->waitUntilISee('#js_master_password');
                $this->click('#js_master_password');
                break;
            case 'login':
                $this->waitUntilISee('#js_master_password');
                $this->click('#js_master_password');
                break;
            case 'share':
                $this->waitUntilISee('#js-search-aros-input');
                $this->click('#js-search-aros-input');
                break;
            case 'group':
                $this->waitUntilISee('#js_group_edit_form_auto_cplt');
                $this->click('#js_group_edit_form_auto_cplt');
                break;
            default:
                $this->waitUntilISee('#js_secret');
                $this->click('#js_secret');
                break;
            }

            $this->waitUntilCssValueEqual($securityTokenElt, 'background-color', Color::hexToRgba($user['TokenTextColor']), 2);
            $this->waitUntilCssValueEqual($securityTokenElt, 'color', Color::hexToRgba($user['TokenColor']), 2);

            // back to normal
            $securityTokenElt->click('.security-token');
        }
    }
}