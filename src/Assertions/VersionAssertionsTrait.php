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

use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use PHPUnit_Framework_Assert;

trait VersionAssertionsTrait {

    abstract public function getDriver() : RemoteWebDriver;
    abstract public function waitUntil($callback, $args = array(), $timeout = 15);

    /**
     * Assert if the API version and optionally the extension version are present in footer tooltip
     *
     * @param bool $extension
     */
    public function assertVersionVisible($extension = true) {
        try {
            $versionElt = $this->getDriver()->findElement(WebDriverBy::cssSelector('#version a'));
        } catch (NoSuchElementException $e) {
            PHPUnit_Framework_Assert::fail('No element with id #version was found');
        }

        // Example: 2.1.2-rc2 / 2.1.1
        $reg_version = '[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2}(-[Rr]{1}[Cc]{1}[0-9]){0,1}';
        if ($extension) {
            $reg = '/^' . $reg_version . ' \/ ' . $reg_version . '$/';
        } else {
            $reg = '/^' . $reg_version . '$/';
        }
        if ($extension) {
            // Wait until extension is loaded
            $callback = array($this, 'assertElementAttributeMatches');
            $this->waitUntil($callback, array($versionElt, 'data-tooltip', $reg));
        } else {
            $version = $versionElt->getAttribute('data-tooltip');
            PHPUnit_Framework_Assert::assertRegExp($reg, $version);
        }
    }
}
