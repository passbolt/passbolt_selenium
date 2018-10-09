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

use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverSelect;

trait PermissionActionsTrait
{

    /**
     * Edit temporary a permission
     *
     * @param $password
     * @param $username
     * @param $permissionType
     */
    public function editTemporaryPermission($password, $username, $permissionType)
    {
        $this->gotoSharePassword($password['id']);

        // I can see the user has a direct permission
        $this->assertElementContainsText(
            $this->findById('js-share-edit-list'),
            $username
        );

        // Find the permission row element
        // and change the permission
        try {
            $rowElement = $this->findByXpath('//*[@id="js-share-edit-list"]//*[.="' . $username . '"]//ancestor::li[1]');
            $select = new WebDriverSelect($rowElement->findElement(WebDriverBy::cssSelector('.select.rights .permission')));
            $select->selectByVisibleText($permissionType);
        } catch (NoSuchElementException $exception) {
            \PHPUnit_Framework_Assert::fail('Could not find the permission to edit');
        }
    }

    /**
     * Edit a password permission helper
     *
     * @param $password
     * @param $username
     * @param $permissionType
     * @param $user
     */
    public function editPermission($password, $username, $permissionType, $user) 
    {
        // Make a temporary edition
        $this->editTemporaryPermission($password, $username, $permissionType, $user);

        // When I click on the save button
        $this->saveShareChanges($user);
    }

    /**
     * Delete temporary a permission helper
     *
     * @param $password
     * @param $username
     */
    public function deleteTemporaryPermission($password, $username) 
    {
        // I can see the user has a direct permission
        $this->assertElementContainsText(
            $this->findById('js-share-edit-list'),
            $username
        );

        // Find the permission row element
        // I delete the permission
        $rowElement = $this->findByXpath('//*[@id="js-share-edit-list"]//*[.="' . $username . '"]//ancestor::li[1]');
        $deleteButton = $rowElement->findElement(WebDriverBy::cssSelector('.js-share-delete-button'));
        $this->assertTrue($deleteButton->isDisplayed());
        $deleteButton->click();
    }

    /**
     * Delete a password permission helper
     *
     * @param $password
     * @param $aroName
     */
    public function deletePermission($password, $aroName, $user)
    {
        // Delete temporary the permission
        $this->deleteTemporaryPermission($password, $aroName);

        try {
            // if it didn't work try to scroll and try again
            $this->findByCss('#js_permissions_changes.hidden');
            $this->scrollElementToBottom('js-share-edit-list');
            $this->deleteTemporaryPermission($password, $aroName);
        } catch(NoSuchElementException $exception) {
        }

        // I can see that temporary changes are waiting to be saved
        $this->assertElementContainsText(
            $this->findByCss('.share-password-dialog'),
            'You need to save to apply the changes'
        );

        // When I click on the save button
        $this->saveShareChanges($user);
    }

}