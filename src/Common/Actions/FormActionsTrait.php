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
namespace App\Common\Actions;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Remote\RemoteWebDriver;

trait FormActionsTrait
{

    abstract public function getDriver(): RemoteWebDriver;

    /**
     * Input some text in an element
     *
     * @param string $selector the element selector
     * @param string $txt the text to be typed on keyboard
     * @param bool $append boolean (optional) true if you want to keep the current value intact
     */
    public function inputText(string $selector, string $txt, bool $append = false)
    {
        $input = $this->find($selector);
        $input->click();
        if (!$append) {
            $input->clear();
        }
        $input->sendKeys($txt);
    }

    /**
     * Input some text in an element
     *
     * @param string $selector the element selector
     * @param string $txt the text to be typed on keyboard
     * @param bool $append boolean (optional) true if you want to keep the current value intact
     */
    public function inputTextWithEmojis(string $selector, string $txt, bool $append = false)
    {
        $input = $this->find($selector);
        $input->click();
        if (!$append) {
            $input->clear();
        }
        $setData = "
			const element = jQuery('$selector')[0];
            element.value = '$txt';
            element.dispatchEvent(new Event('keydown', {bubbles: true}));
            element.dispatchEvent(new Event('keypress', {bubbles: true}));
            element.dispatchEvent(new Event('input', {bubbles: true}));
            element.dispatchEvent(new Event('keyup', {bubbles: true}));
		";
        $this->getDriver()->executeScript($setData);
    }

    /**
     * Input some text in an element
     *
     * @param string $selector CSS selector
     * @param string $txt the text to be typed on keyboard
     * @param bool $append boolean (optional) true if you want to keep the current value intact
     */
    public function inputTextByCss(string $selector, string $txt, bool $append = false)
    {
        $input = $this->getDriver()->findElement(WebDriverBy::cssSelector($selector));
        $input->click();
        if (!$append) {
            $input->clear();
        }
        $input->sendKeys($txt);
    }

    /**
     * Check the checkbox with given id
     *
     * @param string $selector The selector
     */
    public function checkCheckbox(string $selector)
    {
        $input = $this->find($selector);
        $input->click();
    }
}