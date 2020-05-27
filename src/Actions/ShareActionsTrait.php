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

trait ShareActionsTrait
{
    /**
     * Open the share dialog and switch to the react iframe
     */
    public function openShareDialog()
    {
        $this->click('#js_wk_menu_sharing_button');
        $this->goIntoReactAppIframe();
        $this->waitUntilISee('.share-dialog');
        $this->waitUntilIDontSee('.row.skeleton');
    }

    /**
     * Close the share dialog and switch out of the react iframe
     */
    public function closeShareDialog()
    {
        $this->waitUntilISee('.dialog-close');
        $this->click('.share-dialog a.cancel');
        $this->goOutOfIframe();
    }

    /**
     * Assert the dialog permissions
     * @param array $permissions the list of permissions to check
     * [
     *    USERNAME => PERMISSION TYPE
     * ]
     * i.e.
     * [
     *    'ada@passbolt.com' => 15,
     *    'betty@passbolt.com' => 7
     * ]
     */
    public function assertDialogPermissions(array $permissions)
    {
        $liElements = $this->findAllByCss('.share-dialog .permissions li');
        foreach($liElements as $liElement) {
            $permissionType = $liElement->findElement(WebDriverBy::cssSelector('select'))->getAttribute('value');
            $username = trim($liElement->findElement(WebDriverBy::cssSelector('.aro-details span'))->getAttribute('innerHTML'));
            $this->assertEquals($permissions[$username], $permissionType);
        }
    }

    /**
     * Add a temporary permission
     *
     * @param string $username the username to add a permission for
     * @throws NoSuchElementException
     */
    public function addTemporaryPermission(string $username)
    {
        $this->inputText('.autocomplete input', $username);
        $this->waitUntilISee('.autocomplete-content', '/' . $username . '/i');
        $liElements = $this->findAllByCss('.autocomplete-content li');
        foreach($liElements as $liElement) {
            if (strpos($liElement->getAttribute('innerHTML'), $username) !== false) {
                $liElement->click();
                return;
            }
        }
        throw new NoSuchElementException("Cannot find the user: $username");
    }

    /**
     * Delete a permission
     *
     * @param string $username The user to delete
     * @throws NoSuchElementException
     */
    public function deleteTemporaryPermission(string $username)
    {
        $liElements = $this->findAllByCss('.share-dialog .permissions li');
        foreach($liElements as $liElement) {
            if (strpos($liElement->getAttribute('innerHTML'), $username) !== false) {
                $deleteButton = $liElement->findElement(WebDriverBy::cssSelector('.actions a.remove-item'));
                $deleteButton->click();
                return;
            }
        }
        throw new NoSuchElementException("Cannot find a permission associated to the user: $username");
    }

    /**
     * Edit a permission
     *
     * @param string $username The user to edit the permission
     * @param string $permissionType The permission type. is owner, can update or can read
     * @throws \Facebook\WebDriver\Exception\NoSuchElementException
     * @throws \Facebook\WebDriver\Exception\UnexpectedTagNameException
     */
    public function editTemporaryPermission(string $username, string $permissionType)
    {
        $liElements = $this->findAllByCss('.share-dialog .permissions li');
        foreach($liElements as $liElement) {
            if (strpos($liElement->getAttribute('innerHTML'), $username) !== false) {
                $permissionSelect = new WebDriverSelect($liElement->findElement(WebDriverBy::cssSelector('select')));
                $permissionSelect->selectByVisibleText($permissionType);
                return;
            }
        }
        throw new NoSuchElementException("Cannot find a permission associated to the user: $username");
    }
}