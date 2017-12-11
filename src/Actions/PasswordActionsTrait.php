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

use App\Assertions\PasswordAssertionsTrait;
use App\Assertions\WorkspaceAssertionsTrait;
use Color;

trait PasswordActionsTrait
{

    use PasswordAssertionsTrait;
    use WorkspaceActionsTrait;
    use WorkspaceAssertionsTrait;

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
        $this->assertVisible('.create-password-dialog');
    }

    /**
     * Click on a password inside the password workspace.
     *
     * @param string $id id of the password
     *
     * @throws Exception
     */
    public function clickPassword($id) 
    {
        if(!$this->isVisible('.page.password')) {
            $this->fail("click password requires to be on the password workspace");
        }
        $this->click('#resource_' . $id . ' .cell_name');
    }

    /**
     * Right click on a password with a given id.
     *
     * @param string $id
     *
     * @throws Exception
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
			jQuery('$eltSelector').trigger({
				type:'mousedown',
				which:3
			});
		"
        );
        // Without this little interval, the menu doesn't have time to open.
        $this->waitUntilISee('#js_contextual_menu.ready');
    }

    /**
     * Mark or unmark a password as a favorite
     *
     * @param  $id string
     * @throws Exception
     */
    public function clickPasswordFavorite($id) 
    {
        $eltSelector = '#favorite_' . $id . ' i';
        $this->click($eltSelector);
        $this->waitCompletion();
    }

    /**
     * Goto the edit password dialog for a given resource id
     *
     * @param  $id string
     * @throws Exception
     */
    public function gotoEditPassword($id) 
    {
        if(!$this->isVisible('.page.password')) {
            $this->getUrl('');
            $this->waitUntilISee('.page.password');
            $this->waitUntilISee('#js_wk_menu_edition_button');
        }
        $this->releaseFocus(); // we click somewhere in case the password is already active
        if (!$this->isPasswordSelected($id)) {
            $this->clickPassword($id);
        }
        $this->click('js_wk_menu_edition_button');
        $this->waitCompletion();
        $this->assertVisible('.edit-password-dialog');
        $this->waitUntilISee('#passbolt-iframe-secret-edition.ready');
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
     * Helper to fill the password form
     */
    public function fillPasswordForm($password) 
    {
        $this->gotoCreatePassword();
        $this->inputText('js_field_name', $password['name']);
        $this->inputText('js_field_username', $password['username']);
        if (isset($password['uri'])) {
            $this->inputText('js_field_uri', $password['uri']);
        }
        $this->inputSecret($password['password']);
        if (isset($password['description'])) {
            $this->inputText('js_field_description', $password['description']);
        }
    }

    /**
     * Helper to create a password
     */
    public function createPassword($password) 
    {
        $this->fillPasswordForm($password);
        $this->click('.create-password-dialog input[type=submit]');
        $this->waitUntilIDontSee('#passbolt-iframe-progress-dialog');
        $this->assertNotification('app_resources_add_success');
    }

    /**
     * Find a password id by name in the interface.
     *
     * @param  $name
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
     * @param  $password
     * @throws Exception
     */
    public function editPassword($password, $user = []) 
    {
        $this->gotoEditPassword($password['id']);

        if (isset($password['name'])) {
            $this->inputText('js_field_name', $password['name']);
        }
        if (isset($password['username'])) {
            $this->inputText('js_field_username', $password['username']);
        }
        if (isset($password['uri'])) {
            $this->inputText('js_field_uri', $password['uri']);
        }
        if (isset($password['password'])) {
            if (empty($user)) {
                $this->fail("a user must be provided to the function in order to update the secret");
            }
            $this->goIntoSecretIframe();
            $this->click('js_secret');
            $this->goOutOfIframe();
            $this->assertMasterPasswordDialog($user);
            $this->enterMasterPassword($user['MasterPassword']);
            $this->waitUntilIDontSee('#passbolt-iframe-master-password');

            // Wait for password to be decrypted.
            $this->goIntoSecretIframe();
            $this->waitUntilSecretIsDecryptedInField();
            $this->goOutOfIframe();

            $this->inputSecret($password['password']);
        }
        if (isset($password['description'])) {
            $this->inputText('js_field_description', $password['description']);
        }
        $this->click('.edit-password-dialog input[type=submit]');

        if (isset($password['password'])) {
            $this->waitUntilIDontSee('#passbolt-iframe-progress-dialog');
        }
        // And I should not see the edit dialog anymore
        $this->waitUntilIDontSee('.edit-password-dialog');

        // And I should see the notification.
        $this->assertNotification('app_resources_edit_success');
    }

    /**
     * Assert if a security token match user parameters
     *
     * @param  $user array see fixtures
     * @param  $context where is the security token (master or else)
     * @throws Exception
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
                $this->waitUntilISee('js_master_password');
                $this->click('js_master_password');
                break;
            case 'login':
                $this->waitUntilISee('js_master_password');
                $this->click('js_master_password');
                break;
            case 'share':
                $this->waitUntilISee('js_perm_create_form_aro_auto_cplt');
                $this->click('js_perm_create_form_aro_auto_cplt');
                break;
            case 'group':
                $this->waitUntilISee('js_group_edit_form_auto_cplt');
                $this->click('js_group_edit_form_auto_cplt');
                break;
            default:
                $this->waitUntilISee('js_secret');
                $this->click('js_secret');
                break;
            }

            $this->waitUntilCssValueEqual($securityTokenElt, 'background-color', Color::hexToRgba($user['TokenTextColor']), 2);
            $this->waitUntilCssValueEqual($securityTokenElt, 'color', Color::hexToRgba($user['TokenColor']), 2);

            // back to normal
            $securityTokenElt->click('.security-token');
        }
    }
}