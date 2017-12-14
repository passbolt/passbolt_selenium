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
use PHPUnit_Framework_AssertionFailedError;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Remote\RemoteWebDriver;

trait UserAssertionsTrait
{
    abstract function assertTrue($condition, $message = '');
    abstract function elementHasClass($elt, $className);
    abstract public function getDriver(): RemoteWebDriver;

    /**
     * Check if the user has already been selected
     *
     * @param $id string
     * @return bool
     */
    public function isUserSelected($id) 
    {
        $eltSelector = '#user_' . $id;
        if ($this->elementHasClass($eltSelector, 'selected')) {
            return true;
        }
        return false;
    }

    /**
     * Check if the user has not been selected
     *
     * @param $id string
     * @return bool
     */
    public function isUserNotSelected($id) 
    {
        $eltSelector = '#user_' . $id;
        if ($this->elementHasClass($eltSelector, 'selected')) {
            return false;
        }
        return true;
    }

    /**
     * Check if the user is inactive.
     *
     * @param $id
     * @return bool
     */
    public function isUserInactive($id) 
    {
        $eltSelector = '#user_' . $id;
        if ($this->elementHasClass($eltSelector, 'inactive')) {
            return true;
        }
        return false;
    }

    /**
     * Assert a user is selected
     *
     * @param $id string
     */
    public function assertUserSelected($id) 
    {
        $this->assertTrue($this->isUserSelected($id));
    }

    /**
     * Assert a is not selected
     *
     * @param $id string
     */
    public function assertUserNotSelected($id) 
    {
        $this->assertTrue($this->isUserNotSelected($id));
    }

    /**
     * Assert that a user is inactive
     *
     * @param $id
     */
    public function assertUserInactive($id) 
    {
        $this->assertTrue($this->isUserInactive($id));
    }

}