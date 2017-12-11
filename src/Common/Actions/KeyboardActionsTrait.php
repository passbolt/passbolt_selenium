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
use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\Remote\RemoteWebDriver;

trait KeyboardActionsTrait
{
    abstract public function getDriver() : RemoteWebDriver;

    /**
     * Empty a field like a user would do it.
     * Click on the field, go at the end of the text, and backspace to remove the whole text.
     *
     * @param string $id to be used for WebDriverBy::id
     */
    public function emptyFieldLikeAUser(string $id)
    {
        $field = $this->getDriver()->findElement(WebDriverBy::id($id));
        $val = $field->getAttribute('value');
        $sizeStr = strlen($val);
        $field->click();
        $activeElt = $this->getDriver()->switchTo()->activeElement();
        for ($i = 0; $i < $sizeStr; $i++) {
            $activeElt->sendKeys(WebDriverKeys::ARROW_RIGHT);
        }
        for ($i = 0; $i < $sizeStr; $i++) {
            $activeElt->sendKeys(WebDriverKeys::BACKSPACE);
        }
    }

    /**
     * Type text like a user would do, pressing different keystrokes
     *
     * @param string $text to type
     */
    public function typeTextLikeAUser($text)
    {
        $sizeStr = strlen($text);
        for ($i = 0; $i < $sizeStr; $i++) {
            $activeElt = $this->getDriver()->switchTo()->activeElement();
            $activeElt->sendKeys($text[$i]);
        }
    }

    /**
     * Press enter on keyboard
     */
    public function pressEnter()
    {
        $activeElt = $this->getDriver()->switchTo()->activeElement();
        $activeElt->sendKeys(WebDriverKeys::ENTER);
    }

    /**
     * Press tab key
     */
    public function pressTab()
    {
        $activeElt = $this->getDriver()->switchTo()->activeElement();
        $activeElt->sendKeys(WebDriverKeys::TAB);
    }

    /**
     * Press backtab key
     */
    public function pressBacktab()
    {
        $activeElt = $this->getDriver()->switchTo()->activeElement();
        $activeElt->sendKeys([WebDriverKeys::SHIFT, WebDriverKeys::TAB]);
    }

    /**
     * Emulate escape key press
     */
    public function pressEscape()
    {
        $activeElt = $this->getDriver()->switchTo()->activeElement();
        $activeElt->sendKeys(WebDriverKeys::ESCAPE);
    }
}