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
use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use PHPUnit_Framework_Assert;

trait ClipboardAssertions
{
    abstract public function getDriver() : RemoteWebDriver;
    abstract public function waitUntilISee($ids, $regexps = null, $timeout = 15);
    abstract public function waitUntil($callback, $args = array(), $timeout = 15);

    /**
     * Assert that the content of the clipboard match what is given
     *
     * @param $content
     */
    public function assertClipboard($content) 
    {
        // trick: we create a temporary textarea in the page.
        // and check its content match the content given
        $this->appendHtmlInPage('container', '<textarea id="webdriver-clipboard-content" style="position:absolute; top:0; left:0; z-index:999;"></textarea>');
        $this->waitUntilISee('#webdriver-clipboard-content');
        $this->waitUntil(
            function () use (&$content) {
                $id = 'webdriver-clipboard-content';
                $e = $this->getDriver()->findElement(WebDriverBy::id($id));
                $e->clear();
                $e->click();
                $e->sendKeys(array(WebDriverKeys::CONTROL, 'v'));
                PHPUnit_Framework_Assert::assertTrue($e->getAttribute('value') == $content);
            }, null, 5
        );
        $this->removeElementFromPage('webdriver-clipboard-content');
    }

    /**
     * Remove an HTML element from the page.
     *
     * @param $elId
     */
    public function removeElementFromPage($elId)
    {
        $script = "
		var element = document.getElementById('$elId');
		element.outerHTML = '';
		delete element;
		";
        $this->getDriver()->executeScript($script);
    }

    /**
     * Append Html in a given element according to the given id.
     * Beware : no multiline html will be processed.
     *
     * @param $elId
     * @param $html
     */
    public function appendHtmlInPage($elId, $html)
    {
        $html = str_replace("'", "\'", $html);
        $script = "
		function appendHtml(el, str) {
			var div = document.createElement('div');
			div.innerHTML = str;
			while (div.children.length > 0) {
				el.appendChild(div.children[0]);
			}
		}
		var el = document.getElementById('$elId');
		appendHtml(el, '$html');
		";
        $this->getDriver()->executeScript($script);
    }
}