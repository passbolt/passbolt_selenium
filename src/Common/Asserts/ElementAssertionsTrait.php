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

use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use PHPUnit_Framework_Assert;

trait ElementAssertionsTrait
{

    abstract public function getDriver() : RemoteWebDriver;
    abstract public function find($selector) : RemoteWebElement;

    /**
     * Check if an element has a given class name
     *
     * @param mixed     $elt string selector or RemoteWebElement
     * @param $className
     * @return bool
     */
    public function elementHasClass($elt, $className)
    {
        if(!is_object($elt)) {
            $elt = $this->find($elt);
        }
        $eltClasses = $elt->getAttribute('class');
        $eltClasses = explode(' ', $eltClasses);
        if(in_array($className, $eltClasses)) {
            return true;
        }
        return false;
    }

    /**
     * Assert if a given element contains a given text
     *
     * @param mixed  $elt the WebDriverElement or csselector string
     * @param $needle
     */
    public function assertElementContainsText($elt, $needle)
    {
        if(!is_object($elt)) {
            $elt = $this->find($elt);
        }
        $eltText = $elt->getText();
        if(preg_match('/^\/.+\/[a-z]*$/i', $needle)) {
            $contains = preg_match($needle, $eltText) != false;
        } else {
            $contains = strpos($eltText, $needle) !== false;
        }
        $msg = sprintf("Failed asserting that element contains '%s' '%s' found instead", $needle, $eltText);
        PHPUnit_Framework_Assert::assertTrue($contains, $msg);
    }

    /**
     * Assert if a given element is empty
     *
     * @param $needle
     */
    public function assertElementIsEmpty($elt)
    {
        if(!is_object($elt)) {
            $elt = $this->find($elt);
        }
        $eltText = $elt->getText();
        $msg = sprintf("Failed asserting that element is empty, '%s' found instead", $eltText);
        PHPUnit_Framework_Assert::assertEmpty($eltText, $msg);
    }

    /**
     * Assert if a given element does not contain a given text
     *
     * @param $elt
     * @param $needle
     */
    public function assertElementNotContainText($elt, $needle)
    {
        if(!is_object($elt)) {
            $elt = $this->find($elt);
        }
        $eltText = $elt->getText();
        $contains = strpos($eltText, $needle) !== false;
        $msg = sprintf("Failed asserting that element does not contain '%s'", $needle);
        PHPUnit_Framework_Assert::assertFalse($contains, $msg);
    }

    /**
     * Assert if an element has a given class name
     *
     * @param RemoteWebElement $elt
     * @param string $className
     */
    public function assertElementHasClass(RemoteWebElement $elt, $className)
    {
        $contains = $this->elementHasClass($elt, $className);
        $msg = sprintf("Failed asserting that element has class '%s'", $className);
        PHPUnit_Framework_Assert::assertTrue($contains, $msg);
    }

    /**
     * Assert that an element's attribute is equal to the one given.
     *
     * @param RemoteWebElement $elt
     * @param $attribute
     * @param $value
     */
    public function assertElementAttributeEquals(RemoteWebElement $elt, $attribute, $value)
    {
        $attr = $elt->getAttribute($attribute);
        $msg = sprintf("Failed asserting that element attribute %s equals %s", $attribute, $value);
        PHPUnit_Framework_Assert::assertEquals($attr, $value, $msg);
    }

    /**
     * Assert that an element's attribute matches the given regex.
     *
     * @param RemoteWebElement $elt
     * @param string           $attribute
     * @param $regex
     */
    public function assertElementAttributeMatches(RemoteWebElement $elt, $attribute, $regex)
    {
        $attributeValue = $elt->getAttribute($attribute);
        PHPUnit_Framework_Assert::assertRegExp($regex, $attributeValue);
    }

    /**
     * Assert if an element has a given class name
     *
     * @param RemoteWebElement $elt
     * @param $className
     */
    public function assertElementHasNotClass(RemoteWebElement $elt, $className)
    {
        $eltClasses = $elt->getAttribute('class');
        $eltClasses = explode(' ', $eltClasses);
        $contains = in_array($className, $eltClasses);
        $msg = sprintf("Failed asserting that element has not the class '%s'", $className);
        PHPUnit_Framework_Assert::assertFalse($contains, $msg);
    }

    /**
     * Assert if an element has the focus.
     *
     * @param $id
     */
    public function assertElementHasFocus($id)
    {
        $activeElt = $this->getDriver()->switchTo()->activeElement();
        $activeId = $activeElt->getAttribute('id');
        PHPUnit_Framework_Assert::assertEquals($id, $activeId);
    }

}