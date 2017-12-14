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
namespace App\Common\Asserts;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\WebDriverBy;
use PHP_CodeSniffer\Tokenizers\PHP;
use PHPUnit_Framework_Assert;

trait VisibilityAssertionsTrait
{

    abstract public function getDriver(): RemoteWebDriver;

    /**
     * Tell if an element is visible
     *
     * @param string $cssSelector selector
     * @return boolean
     */
    public function isVisible($cssSelector)
    {
        $element = null;
        try {
            $element = $this->getDriver()->findElement(WebDriverBy::cssSelector($cssSelector));
        } catch (NoSuchElementException $e) {
        }
        return (!is_null($element) && $element->isDisplayed());
    }

    /**
     * Tell if an element is not visible or not present
     *
     * @param string $cssSelector
     * @return boolean
     */
    public function isNotVisible($cssSelector)
    {
        try {
            $element = $this->getDriver()->findElement(WebDriverBy::cssSelector($cssSelector));
        } catch (NoSuchElementException $e) {
            return true; // not found == not visible
        }
        return (!$element->isDisplayed());
    }

    /**
     * Assert if an element identified via its id is visible
     *
     * @param $id
     */
    public function assertVisibleByCss($id, $message = '')
    {
        if (empty($message)) {
            $message = 'Failed to assert that the element ' . $id . ' is visible';
        }
        PHPUnit_Framework_Assert::assertTrue(
            $this->isVisible($id),
            $message
        );
    }

    /**
     * Assert if an element identified via its id is visible
     *
     * @param $id
     */
    public function assertVisible($id, $message = '')
    {
        if (empty($message)) {
            $message = 'Failed to assert that the element ' . $id . ' is visible';
        }
        try {
            $element = $this->getDriver()->findElement(WebDriverBy::id($id));
        } catch (NoSuchElementException $exception) {
            PHPUnit_Framework_Assert::fail($message);
        }
        if (!$element->isDisplayed()) {
            PHPUnit_Framework_Assert::fail($message);
        }
        PHPUnit_Framework_Assert::assertTrue(
            $element->isDisplayed(),
            $message
        );
    }

    /**
     * Assert if an element identified by its id is not visible or not present
     *
     * @param $id
     */
    public function assertNotVisible($id)
    {
        PHPUnit_Framework_Assert::assertTrue(
            $this->isNotVisible($id),
            'Failed to assert that the element ' . $id . ' is not visible'
        );
    }

}