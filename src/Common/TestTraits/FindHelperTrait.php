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
namespace App\Common\TestTraits;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use PHPUnit_Framework_Assert;

trait FindHelperTrait
{
    abstract public function getDriver() : RemoteWebDriver;

    /**
     * A generic find, try by id, then css
     *
     * @param  mixed $selector Element or selector string
     * @return mixed
     */
    public function find($selector) : RemoteWebElement
    {
        $element = null;

        // If the given selector is already an element.
        if (is_object($selector)) {
            return $selector;
        }

        // Could the selector be an identifier
        $matches = [];
        if (preg_match('/^[#]?([^\.\s]*)$/', $selector, $matches)) {
            try {
                $id = $matches[1];
                $element = $this->getDriver()->findElement(WebDriverBy::id($id));
            } catch (\Exception $e) {
                // error treated later
            }
        }

        // If the element selector looked liked an id but wasn't found
        // or was not an id like, try to search by css
        if (is_null($element)) {
            try {
                $element = $this->getDriver()->findElement(WebDriverBy::cssSelector($selector));
            } catch (\Exception $e) {
                // error treated later
            }
        }

        if (is_null($element)) {
            PHPUnit_Framework_Assert::fail('Cannot find element: ' . $selector);
        }

        return $element;
    }

    /**
     * Find an element by a CSS selector
     *
     * @param  $css
     * @return mixed
     */
    public function findByCss($css) : RemoteWebElement
    {
        return $this->getDriver()->findElement(WebDriverBy::cssSelector($css));
    }

    /**
     * Find all elements by a CSS selector
     *
     * @param  $css
     * @return mixed
     */
    public function findAllByCss($css) : RemoteWebElement
    {
        return $this->getDriver()->findElements(WebDriverBy::cssSelector($css));
    }

    /**
     * Find an element by a XPath selector
     *
     * @param  $xpath
     * @return RemoteWebElement
     */
    public function findByXpath($xpath) : RemoteWebElement
    {
        return $this->getDriver()->findElement(WebDriverBy::xpath($xpath));
    }

    /**
     * Find all elements by a XPath selector
     *
     * @param  $xpath
     * @return array RemoteWebElement[]
     */
    public function findAllByXpath($xpath) : array
    {
        return $this->getDriver()->findElements(WebDriverBy::xpath($xpath));
    }

    /**
     * Find an element by ID
     *
     * @param  $id
     * @return RemoteWebElement
     */
    public function findById($id) : RemoteWebElement
    {
        return $this->getDriver()->findElement(WebDriverBy::id($id));
    }

    /**
     * Find a link by its text
     *
     * @param  $text
     * @return RemoteWebElement
     */
    public function findLinkByText($text) : RemoteWebElement
    {
        return $this->getDriver()->findElement(WebDriverBy::linkText($text));
    }
}