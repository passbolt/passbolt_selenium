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

trait ShareActionsTrait
{

    /**
     * Goto the share password dialog for a given resource id
     *
     * @param $id string
     */
    public function gotoSharePassword($id) 
    {
        if(!$this->isVisible('.page.password')) {
            $this->getUrl('');
            $this->waitUntilISee('.page.password');
            $this->waitUntilISee('#js_wk_menu_sharing_button');
        }
        if(!$this->isVisible('#js_rs_permission')) {
            $this->releaseFocus(); // we click somewhere in case the password is already active
            if (!$this->isPasswordSelected($id)) {
                $this->clickPassword($id);
            }
            $this->click('js_wk_menu_sharing_button');
            $this->waitUntilISee('.share-password-dialog #js_rs_permission.ready');
            $this->waitUntilISee('#passbolt-iframe-password-share.ready');
        }
    }

    /**
     * Search an aro (User or Group) to share a password with.
     *
     * @param $password
     * @param $aroName
     * @param $user
     */
    public function searchAroToGrant($password, $aroName, $user) 
    {
        $this->gotoSharePassword($password['id']);

        // I enter the username I want to share the password with in the autocomplete field
        $this->goIntoShareIframe();
        $this->assertSecurityToken($user, 'share');
        $this->inputText('js_perm_create_form_aro_auto_cplt', $aroName, true);
        $this->click('.security-token');
        $this->goOutOfIframe();

        // I wait the autocomplete box is loaded.
        $this->waitCompletion(10, '#passbolt-iframe-password-share-autocomplete.loaded');
    }

    /**
     * Add a temporary permission helper
     *
     * @param $password
     * @param $aroName
     * @param $user
     */
    public function addTemporaryPermission($password, $aroName, $user) 
    {
        // Search the user to grant.
        $this->searchAroToGrant($password, $aroName, $user);

        // I wait until I see the automplete field resolved
        $this->goIntoShareAutocompleteIframe();
        $this->waitUntilISee('.autocomplete-content', '/' . $aroName . '/i');

        // I click on the username link the autocomplete field retrieved.
        $element = $this->findByXpath('//*[contains(., "' . $aroName . '")]//ancestor::li[1]');
        $element->click();
        $this->goOutOfIframe();

        // I can see that temporary changes are waiting to be saved
        $this->assertElementContainsText(
            $this->findByCss('.share-password-dialog #js_permissions_changes'),
            'You need to save to apply the changes'
        );
    }

    /**
     * Share a password helper
     *
     * @param $password
     * @param $aroName
     * @param $user
     */
    public function sharePassword($password, $aroName, $user) 
    {
        $this->addTemporaryPermission($password, $aroName, $user);
        $this->saveShareChanges($user);
    }

    /**
     * Save share
     *
     * @param $user
     */
    public function saveShareChanges($user) 
    {
        // When I click on the save button
        $this->click('js_rs_share_save');

        // Then I see the passphrase dialog
        $this->assertMasterPasswordDialog($user);

        // When I enter the passphrase and click submit
        $this->enterMasterPassword($user['MasterPassword']);

        // Then I see a dialog telling me encryption is in progress
        // Assert that the progress dialog is not displayed anymore (if it was displayed).
        $this->waitUntilIDontSee('#passbolt-iframe-progress-dialog');

        // And I see a notice message that the operation was a success
        $this->assertNotification('app_share_update_success');

        // And I should not see the share dialog anymore
        $this->waitUntilIDontSee('.share-password-dialog');
    }

    /**
     * Put the focus inside the password share iframe
     */
    public function goIntoShareIframe() 
    {
        $this->getDriver()->switchTo()->frame('passbolt-iframe-password-share');
    }

    /**
     * Put the focus inside the password share autocomplete iframe
     */
    public function goIntoShareAutocompleteIframe() 
    {
        $this->getDriver()->switchTo()->frame('passbolt-iframe-password-share-autocomplete');
    }

}