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
use Facebook\WebDriver\Remote\RemoteWebDriver;
use PHPUnit_Framework_AssertionFailedError;
use Facebook\WebDriver\Exception\NoSuchElementException;

trait PluginsAssertionsTrait
{

    abstract public function getDriver(): RemoteWebDriver;

    /**
     * Check that there is no plugin
     */
    public function assertNoPlugin() 
    {
        $msg = 'A passbolt plugin was not found';
        try {
            $css = 'html.no-passboltplugin';
            $e =  $this->getDriver()->findElement(WebDriverBy::cssSelector($css));
            if(count($e) !== 1) {
                throw new PHPUnit_Framework_AssertionFailedError($msg);
            }
        } catch (NoSuchElementException $e) {
            throw new PHPUnit_Framework_AssertionFailedError($msg);
        }
    }

    /**
     * Check that there is a plugin
     */
    public function assertPlugin() 
    {
        $this->waitUntilISee('html.passboltplugin');
    }

    /**
     * Check that there is a plugin
     */
    public function assertNoPluginConfig() 
    {
        try {
            $css = 'html.passboltplugin.no-passboltconfig';
            $e =  $this->getDriver()->findElement(WebDriverBy::cssSelector($css));
            $this->assertTrue(count($e) === 0);
        } catch (NoSuchElementException $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Check that there is a plugin with a config set
     */
    public function assertPluginConfig() 
    {
        try {
            $css = 'html.passboltplugin-config';
            $e =  $this->getDriver()->findElement(WebDriverBy::cssSelector($css));

            $this->assertTrue((isset($e)));
        } catch (NoSuchElementException $e) {
            $this->fail('Passbolt plugin config html header not found');
        }
    }

}