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
use Facebook\WebDriver\WebDriverBy;
use PHPUnit_Framework_Assert;

trait PageAssertionsTrait
{

    abstract public function getDriver(): RemoteWebDriver;

    /**
     * Check the title contains.
     *
     * @param $title
     */
    public function assertTitleContain($title) 
    {
        $t = $this->getDriver()->getTitle();
        PHPUnit_Framework_Assert::assertContains($title, $t);
    }

    /**
     * Check if the page contains the given text
     *
     * @param $text
     */
    public function assertPageContainsText($text) 
    {
        $source = $this->getDriver()->getPageSource();
        $strippedSource = strip_tags($source);
        $contains = strpos($strippedSource, $text) !== false;
        $msg = sprintf("Failed asserting that page contains '%s'", $text);
        PHPUnit_Framework_Assert::assertTrue($contains, $msg);
    }

    /**
     * Check if Meta title contains the given title.
     *
     * @param $title
     */
    public function assertMetaTitleContains($title) 
    {
        $source = $this->getDriver()->getPageSource();
        $contains = preg_match("/<title>$title<\\/title>/", $source);
        $msg = sprintf("Failed asserting that meta title contains '%s'", $title);
        PHPUnit_Framework_Assert::assertTrue($contains == 1, $msg);
    }

    /**
     * Assert if the page contains the given element
     *
     * @param $cssSelector
     */
    public function assertPageContainsElement($cssSelector) 
    {
        // todo find by id first
        try {
            $this->getDriver()->findElement(WebDriverBy::cssSelector($cssSelector));
        } catch (NoSuchElementException $e) {
            $msg = sprintf("Failed asserting that the page contains the element %s", $cssSelector);
            PHPUnit_Framework_Assert::fail($msg);
        }
    }

}