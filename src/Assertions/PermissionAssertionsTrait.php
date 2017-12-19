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
namespace App\Assertions;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverSelect;
use Facebook\WebDriver\Exception\NoSuchElementException;

trait PermissionAssertionsTrait
{

    /**
     * Assert that the password has a specific permission for a target user
     *
     * @param $password
     * @param $username
     * @param $permissionType
     * @param $options
     */
    public function assertPermission($password, $username, $permissionType, $options = array()) 
    {
        $this->gotoSharePassword($password['id']);

        // I can see the user has a direct permission
        $this->assertElementContainsText(
            $this->findById('js_permissions_list'),
            $username
        );

        // Find the permission row element
        $rowElement = $this->findByXpath('//*[@id="js_permissions_list"]//*[.="' . $username . '"]//ancestor::li[1]');

        // I can see the permission is as expected
        try {
            $elt = $rowElement->findElement(WebDriverBy::cssSelector('.js_share_rs_perm_type'));
            $select = new WebDriverSelect($elt);
            $text = $select->getFirstSelectedOption()->getText();
            $this->assertEquals($permissionType, $text);
        } catch (NoSuchElementException $exception) {
            \PHPUnit_Framework_Assert::fail('Could not find the permission type select input.');
        }

        // Close the dialog
        if (!isset($options['closeDialog']) || $options['closeDialog'] == true) {
            $this->find('.dialog .dialog-close')->click();
        }
    }

    /**
     * Assert that the password has a specific permission for a target user, inside the sidebar
     *
     * @param $aro_name
     * @param $permissionType
     */
    public function assertPermissionInSidebar($aro_name, $permissionType) 
    {
        // Wait until the permissions are loaded. (ready state).
        $this->waitUntilISee('#js_rs_details_permissions_list.ready');

        // I can see the user has a direct permission
        $this->assertElementContainsText(
            $this->findById('js_rs_details_permissions_list'),
            $aro_name
        );

        // Find the permission row element
        $rowElement = $this->findByXpath('//*[@id="js_rs_details_permissions_list"]//*[contains(@class, "permission")]//*[contains(text(), "' . $aro_name . '")]//ancestor::li');

        // I can see the permission is as expected
        $permissionTypeElt = $rowElement->findElement(WebDriverBy::cssSelector('.subinfo'));
        $this->assertEquals($permissionType, $permissionTypeElt->getText());
    }

    /**
     * Assert that the password has no direct permission for a target user
     *
     * @param $password
     * @param $username
     */
    public function assertNoPermission($password, $username) 
    {
        $this->gotoSharePassword($password['id']);

        // I can see the user has a direct permission
        $this->assertElementNotContainText(
            $this->findById('js_permissions_list'),
            $username
        );
    }
}