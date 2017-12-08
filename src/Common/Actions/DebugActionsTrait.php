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

use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Exception;
use PHPUnit_Framework_Assert;

trait DebugActionsTrait
{
    abstract public function getDriver() : RemoteWebDriver;
    abstract public function getUrl($url = null);
    abstract public function waitUntilISee($ids, $regexps = null, $timeout = 15);

    /**
     * The addon url.
     * It will be initialized the first the test access the function getAddonUrl.
     *
     * @var string
     */
    public $addonUrl = '';

    /**
     * Go to debug page.
     */
    public function goToDebug()
    {
        $addonUrl = $this->getAddonBaseUrl();
        $this->getUrl($addonUrl . 'data/config-debug.html');
        $this->waitUntilISee('.config.page.ready');
    }

    /**
     * Get the addon url
     *
     * @return string
     */
    public function getAddonBaseUrl() 
    {
        try {
            // A passbolt debug meta data is required to build the debug url.
            if (empty($this->addonUrl)) {
                $headElement = $this->getDriver()->findElement(WebDriverBy::id('head'));
                $this->addonUrl = $headElement->getAttribute('data-passbolt-addon-url');

                // If the debut meta data not found, go to a passbolt page first.
                // The data is available only on passbolt page.
                if (empty($this->addonUrl)) {
                    $this->getUrl('');
                    $this->waitUntilISee('.passbolt');
                    $headElement = $this->getDriver()->findElement(WebDriverBy::id('head'));
                    $this->addonUrl = $headElement->getAttribute('data-passbolt-addon-url');
                }
            }
        } catch(NoSuchElementException $exception) {
            PHPUnit_Framework_Assert::fail('Could not find addon base url.');
        }
        return $this->addonUrl;
    }

    /**
     * Set client config data.
     * Populate the field js_auto_settings from the debug page, with the settings given.
     * The settings are encoded in json, and base64 to avoir return to lines which cause issues in javascript.
     * The debug page then decode these data, and populate the settings fields.
     * This method is much faster that asking the driver to fill the fields manually.
     *
     * @param $config
     */
    function _setClientConfigData($config)
    {
        $configBase64 = base64_encode(json_encode($config));
        $setData = "
			document.getElementById(\"js_auto_settings\").value='$configBase64';
		";
        $this->getDriver()->executeScript($setData);
    }

}