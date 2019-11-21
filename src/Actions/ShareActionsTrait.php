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
        if ($this->isVisible('#passbolt-iframe-password-share.ready') || $this->isVisible('.share-password-dialog')) {
            return;
        }
        if(!$this->isVisible('.page.password')) {
            $this->getUrl('');
            $this->waitUntilISee('.page.password');
            $this->waitUntilISee('#js_wk_menu_sharing_button');
        }
        if(!$this->isVisible('.share-password-dialog')) {
            $this->releaseFocus(); // we click somewhere in case the password is already active
            if (!$this->isPasswordSelected($id)) {
                $this->clickPassword($id);
            }
            $this->click('js_wk_menu_sharing_button');
            $this->assertShareDialogVisible();
            $this->goIntoShareIframe();
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
        $this->assertSecurityToken($user, 'share');
        $this->inputText('js-search-aros-input', $aroName, true);
        $this->click('.security-token');
        $this->waitUntilISee('#js-search-aro-autocomplete.ready');
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
        $this->waitUntilISee('.autocomplete-content', '/' . $aroName . '/i');

        // I click on the username link the autocomplete field retrieved.
        $element = $this->findByXpath('//*[@id="js-search-aro-autocomplete"]//*[contains(., "' . $aroName . '")]//ancestor::li[1]');
        $element->click();

        // I can see that temporary changes are waiting to be saved
        $this->assertElementContainsText(
            $this->findByCss('.share-password-dialog'),
            'You need to save to apply the changes.'
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
        $this->gotoSharePassword($password['id']);
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
        $this->click('js-share-save');

        // Then I see the passphrase dialog
        $this->assertMasterPasswordDialog($user);

        // When I enter the passphrase and click submit
        $this->enterMasterPassword($user['MasterPassword']);
        $this->goOutOfIframe();

        // And I don't see the share password iframe
        $this->waitUntilIDontSee('#passbolt-iframe-share-password');

        // And I see a notice message that the operation was a success
        $this->assertNotification('app_share_share_success');

        // And the application is ready
        $this->waitCompletion();
    }

    /**
     * Put the focus inside the password share iframe
     */
    public function goIntoShareIframe() 
    {
        $this->getDriver()->switchTo()->frame('passbolt-iframe-password-share');
    }

}