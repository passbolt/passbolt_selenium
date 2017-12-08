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

use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Interactions\WebDriverActions;

trait MouseActionsTrait
{

    abstract public function getDriver(): RemoteWebDriver;
    abstract public function find($selector): RemoteWebElement;
    abstract public function findLinkByText($text): RemoteWebElement;

    /**
     * Follow a link url defined by a css selector. (Doesn't click on it).
     * This prevents opening the url in another tab in case of target="_blank"
     *
     * @param $text
     */
    public function followLink($text)
    {
        $linkElement = $this->findLinkByText($text);
        $url = $linkElement->getAttribute('href');
        $this->getDriver()->get($url);
        $this->getDriver()->switchTo()->activeElement();
    }

    /**
     * Click on a link element defined by a text.
     * This prevents opening the url in another tab in case of target="_blank"
     *
     * @param string $text
     */
    public function clickLink($text)
    {
        $linkElement = $this->findLinkByText($text);
        $linkElement->click();
    }

    /**
     * Click on an element defined by its Id or CSS selector
     *
     * @param mixed $selector Element or selector string
     * @throw NoSuchElementException
     */
    public function click($selector)
    {
        $elt = $this->find($selector);
        $elt->click();
    }

    /**
     * Right click on something
     *
     * @param $id
     */
    public function rightClick($id)
    {
        $action = new WebDriverActions($this->getDriver());
        $element = $this->find($id);
        $action->contextClick($element)->perform();
    }

    /**
     * Click on a non significant element to release the focus
     *
     * @param string $where to release focus
     */
    public function releaseFocus($where = null)
    {
        if (empty($where)) {
            $where = '.header.second';
        }
        $elt = $this->find($where);
        $elt->click();
    }

}