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
namespace App;

use App\Common\Actions\ScriptedActionsTrait;
use App\Common\Config;
use App\Common\RecordableTestCase;
use App\Lib\Cakephp\Hash;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\WebDriverBy;

abstract class AuthenticatedTestCase extends RecordableTestCase
{
    public $currentUser;
    use ScriptedActionsTrait;

    /**
     * Put the focus inside the login iframe
     */
    public function goIntoLoginIframe() 
    {
        $this->getDriver()->switchTo()->frame('passbolt-iframe-login-form');
    }

    /**
     * Login on the application with the given user.
     *
     * @param array $user
     * @param array $options
     */
    public function loginAs($user, $options = [])
    {
        $setConfig = Hash::get($options, 'setConfig', true);
        if($setConfig) {
            $this->setClientConfig($user);
        }
        if (!is_array($user)) {
            $user = [
                'Username' => $user,
                'MasterPassword' => $user
            ];
        }

        // Store the current username.
        $this->currentUser = $user;

        // If not on the login page, we redirect to it.
        try {
            $this->getDriver()->findElement(WebDriverBy::cssSelector('.users.login.form'));
        }
        catch(NoSuchElementException $e) {
            $url = Hash::get($options, 'url', 'login');
            $this->getUrl($url);
        }

        $this->waitUntilISee('#passbolt-iframe-login-form.ready');
        $this->waitUntilISee('.plugin-check.' . $this->getBrowser()['type'] . '.success');
        $this->waitUntilISee('.plugin-check.gpg.success');
        $this->goIntoLoginIframe();
        $this->assertInputValue('UserUsername', $user['Username']);
        $this->inputText('js_master_password', $user['MasterPassword']);
        $this->click('loginSubmit');
        $this->goOutOfIframe();

        // wait for the login iframe to disappear
        $this->waitUntilIDontSee('.page.login-form.master-password');

        // wait for redirection trigger
        $this->waitUntilISee('.logout');
        $this->waitCompletion();
        $this->waitUntilISee('html.passboltplugin-ready');

        // save the cookie
        self::$loginCookies[$this->currentUser['Username']] = $this->getDriver()->manage()->getCookies();
    }

    /**
     * Logout user.
     */
    public function logout() 
    {
        $this->getUrl('logout');
    }

    /**
     * Use the debug screen to set the values set by the setup
     *
     * @param $config array user config (see fixtures)
     * @param $manual bool whether the data should be entered manually, or through javascript.
     */
    public function setClientConfig($config, $manual = false)
    {
        $this->goToDebug();

        $userPrivateKey = '';
        // If the key provided is a path, then look in the complete path.
        if (strpos($config['PrivateKey'], '/') !== false) {
            $userPrivateKey = file_get_contents($config['PrivateKey']);
        }
        // Else look in the fixtures only.
        else {
            $userPrivateKey = file_get_contents(GPG_FIXTURES . DS . $config['PrivateKey']);
        }

        // Fill config data through javascript
        if (!$manual) {
            $conf = [
                'baseUrl' => isset($config['domain']) ? $config['domain'] : Config::read('passbolt.url'),
                'UserId'  => $config['id'],
                'ProfileFirstName' => $config['FirstName'],
                'ProfileLastName' => $config['LastName'],
                'UserUsername' => $config['Username'],
                'securityTokenCode' => $config['TokenCode'],
                'securityTokenColor' => $config['TokenColor'],
                'securityTokenTextColor' => $config['TokenTextColor'],
                'myKeyAscii' => $userPrivateKey,
                'serverKeyAscii' => file_get_contents(Config::read('passbolt.server_key.path'))
            ];
            $this->_setClientConfigData($conf);
            $this->triggerEvent('passbolt.debug.settings.set');

            $this->waitUntilISee('.debug-data-set');
        }
        // Fill config data manually
        else {
            $this->inputText('baseUrl', isset($config['domain']) ? $config['domain'] : Config::read('passbolt.url'));
            $this->inputText('UserId', $config['id']);
            $this->inputText('ProfileFirstName', $config['FirstName']);
            $this->inputText('ProfileLastName', $config['LastName']);
            $this->inputText('UserUsername', $config['Username']);
            $this->inputText('securityTokenCode', $config['TokenCode']);
            $this->inputText('securityTokenColor', $config['TokenColor']);
            $this->inputText('securityTokenTextColor', $config['TokenTextColor']);

            // Set the keys.
            $key = '';
            // If the key provided is a path, then look in the complete path.
            if (strpos($config['PrivateKey'], '/') !== false) {
                $key = file_get_contents($config['PrivateKey']);
            }
            // Else look in the fixtures only.
            else {
                $key = file_get_contents(GPG_FIXTURES . DS . $config['PrivateKey']);
            }
            $this->inputText('myKeyAscii', $key);
            $filePath = Config::read('passbolt.server_key.path');
            $key = file_get_contents($filePath);
            $this->inputText('serverKeyAscii', $key);
        }

        // Save the profile.
        $this->click('#js_save_conf');
        $this->waitUntilISee('.user.settings.feedback', '/User and settings have been saved!/');

        // Save the user private key.
        $this->click('#saveKey');
        $this->waitUntilISee('.my.key-import.feedback', '/The key has been imported succesfully/');

        // Save the server public key.
        $this->click('#saveServerKey');
        $this->waitUntilISee('.server.key-import.feedback', '/The key has been imported successfully/');
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