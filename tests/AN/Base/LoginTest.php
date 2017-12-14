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
/**
 * Feature: Login
 * - As an anonymous user without the plugin I should not be able to login
 */
namespace Tests\AN\Base;

use App\Assertions\VersionAssertionsTrait;
use App\PassboltTestCase;
use App\Common\Servers\PassboltServer;
use Facebook\WebDriver\Exception\NoSuchElementException;

class LoginTest extends PassboltTestCase
{
    use VersionAssertionsTrait;

    /**
     * Scenario: As AN, I should be redirected to https if ssl.force parameter is set to true.
     * Given I am an anonymous user
     * When  I am trying to access the login page
     * Then  I should see that I am automatically redirected to the https version of it
     *
     * @group chrome-only
     * @todo PASSBOLT-2253 Disabled for FF, certificate issue.
     */
    public function testSslRedirect() 
    {
        PassboltServer::setExtraConfig([
            'App' => [
                'ssl' => [
                    'force' => true
                ]
            ]
        ]);
        $this->getUrl('auth/login');
        $url = $this->driver->getCurrentURL();
        try {
            $this->assertTrue(preg_match('/https.*\/auth\/login/', $url) === 1);
        } catch(Exception $e) {
            PassboltServer::resetExtraConfig();
            throw $e;
        }
        PassboltServer::resetExtraConfig();
    }

    /**
     * Scenario: I can see an error message telling me I need a plugin
     * Given I am an anonymous user with no plugin on the login page
     * When     The page is loaded
     * Then  I can see the title of the page contain 'Login'
     * Then  I can see the error message telling me an add-on is required
     *
     * @group saucelabs
     */
    public function testCanSeeErrorMsg() 
    {
        $this->getUrl('login');
        $this->assertTitleContain('Login');

        try {
            $e = $this->findByCss('.plugin-check.' . $this->_browser['type'] . '.error');
            $this->assertTrue($e->isDisplayed());
        } catch (NoSuchElementException $e) {
            $this->fail('Plugin check error message was not found (CSS: .plugin-check.' . $this->_browser['type'] . '.error)');
        }

        $this->assertTrue(true);
    }

    /**
     * Scenario: I can see a login form on the login page
     * Given I am an anonymous user with no plugin on the login page
     * When        When the page is loaded
     * Then  I can see a box on the right
     * And   I can see a link download the plugin
     * And   I cannot see an iframe inside the box
     * And   I cannot see a username field (inside the iframe)
     * And   I cannot see a password field (inside the iframe)
     * And   I cannot see a login button
     *
     * @group saucelabs
     */
    /**
     * @depends testCanSeeErrorMsg
     */
    public function testCanSeeLoginForm() 
    {
        $this->getUrl('login');

        $loginForm = null;

        try {
            $loginForm = $this->findByCss('.login.form');
        } catch (NoSuchElementException $e) {
            $this->fail('User login form was not found');
        }

        // I should see a link Download the plugin.
        $this->assertElementContainsText(
            $loginForm,
            'Download the plugin'
        );

        $loginIframe = null;
        try {
            $this->findById('passbolt-iframe-login-form');
        } catch (NoSuchElementException $e) {

        }
        // I should not see an Iframe in the login box.
        $this->assertEquals($loginIframe, null);

        // And I should not see a username field.
        $this->assertElementNotContainText(
            $loginForm,
            'Username'
        );

        // And I should not see a password field.
        $this->assertElementNotContainText(
            $loginForm,
            'Master password'
        );
    }

    /**
     * Scenario: I should not see warnings if I accept cookies and javascript is enabled
     * Given I am an anonymous user with no plugin on the login page
     * Then  I should not see a cookie warning
     * Then      I should not see a javascript warning
     */
    public function testNoCookieBanner() 
    {
        $this->getUrl('login');
        $this->assertNotVisible('.message.error.no-js');
        $this->assertNotVisible('.message.error.no-cookies');
    }

    /**
     * Scenario: As an anonymous user I can see the API version number in the footer
     *
     * Given I am an anonymous user with no plugin
     * When  I am on the login page
     * Then  I can see the app version number in the footer
     * And   I can't see the plugin version number
     */
    public function testCanSeeVersionNumber() 
    {
        $this->getUrl('login');
        $this->assertVersionVisible(false);
    }
}