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
 * Anonymous user with plugin but no config login test
 */
namespace Tests\AP\base;

use App\Assertions\VersionAssertionsTrait;
use App\PassboltTestCase;
use Data\Fixtures\User;

class LoginTest extends PassboltTestCase
{
    use VersionAssertionsTrait;

    /**
     * @group saucelabs
     * Test that if not registered I should see a warning
     * @throws Exception
     */
    public function testLogin() 
    {
        $this->getUrl('login');
        $this->waitUntIlISee('.plugin-check.' . $this->_browser['type'] . '.warning', null, 2);
    }

    /**
     * Scenario: As an anonymous user
     * Test that if the wrong domain is configured, we will see a page explaining that
     * the domain is not known.
     *
     * @group saucelabs
     */
    public function testWrongDomain() 
    {
        $user = User::get('ada');
        $this->setClientConfig($user);

        $user['domain'] = 'https://custom.passbolt.com';
        $this->getUrl('login');
        $this->waitUntilISee('html.domain-unknown');
        $this->waitUntilISee('a.trusteddomain', '/https:\/\/custom\.passbolt\.com/');
    }

    /**
     * Test that if the server verification failed, we will see a page explaining that
     * something went wrong with a message explaining what happened
     */
    public function testStage0VerifyError() 
    {
        $user = User::get('ada');
        $this->setClientConfig($user);

        // Load a wrong public server key.
        $this->goToDebug();
        $key = file_get_contents(GPG_FIXTURES . DS . 'user_public.key');
        $this->inputText('serverKeyAscii', $key);
        $this->click('saveServerKey');
        $this->waitUntilISee('.server.key-import.feedback', '/The key has been imported successfully/');

        $this->getUrl('login');
        $this->waitUntilISee('html.server-not-verified');
        $this->assertElementContainsText('.plugin-check.gpg', 'Decryption failed');
    }

    /**
     * Test that if the account doesn't exist on server, we get a proper feedback.
     *
     * @throws Exception
     */
    public function testStage0VerifyNoAccount() 
    {
        $user = User::get('john');
        $this->setClientConfig($user);

        $this->getUrl('login');
        $this->waitUntilISee('html.server-not-verified.server-no-user');
        $this->waitUntilISee('.plugin-check.gpg.error', '/There is no user associated with this key/');
        $this->waitUntilISee('.users.login.form .feedback', '/The supplied account does not exist/');
        $this->click('.users.login.form a.primary');
        $this->waitUntilISee('div.page.register');
    }

    /**
     * Scenario: I can see the app version number and the plugin version number in the footer
     *
     * Given I am an anonymous user with plugin
     * When  I am on the login page
     * Then  I can see the app version number in the footer
     * And   I can see the plugin version number
     */
    public function testCanSeeVersionNumber() 
    {
        $this->getUrl('login');
        $this->assertVersionVisible();
    }
}
