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
use App\Common\Config;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use PHPUnit_Framework_Assert;

trait UrlAssertionsTrait
{

    abstract public function getDriver(): RemoteWebDriver;

    /**
     * Check if the current url match the one given in parameter
     *
     * @param string $url
     * @param bool   $addBase
     */
    public function assertCurrentUrl($url, $addBase = true)
    {
        if ($addBase) {
            $url = Config::read('passbolt.url') . DS . $url;
        }
        PHPUnit_Framework_Assert::assertEquals($url, $this->getDriver()->getCurrentURL());
    }

    /**
     * Check if the current url match the regexp given in parameter
     *
     * @param $regexp
     */
    public function assertUrlMatch($regexp)
    {
        $url = $this->getDriver()->getCurrentURL();
        $match = preg_match($regexp, $url);
        PHPUnit_Framework_Assert::assertTrue($match >= 1, sprintf("Failed asserting that url %s matches with %s", $url, $regexp));
    }
}