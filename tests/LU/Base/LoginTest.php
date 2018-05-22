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
 * Feature : Login
 *
 * As AN I can login to passbolt
 * As AN I can login to passbolt by submitting the login form with the enter key
 * As AN I can login to passbol on different tabs without conflict between workers
 * As LU I should still be logged in after I restart the browser
 * As LU I should still be logged in after I close and restore the passbolt tab
 */
namespace Tests\LU\Base;

use App\PassboltTestCase;
use Data\Fixtures\User;

class LoginTest extends PassboltTestCase
{

    /**
     * Scenario: As a LU I can login to passbolt
     *
     * Given I am Ada
     * And   I am on the login page
     * Then  I can see that my plugin is configured
     * And   I can see the login form
     * And   I can see my username in the login form
     * When  I click on the submit button
     * Then  I can see an error message saying a password is required
     * When  I input my master password
     * And   I press enter
     * Then  I can see a progress feedback in the place of the form
     * Then  I can see that I am on the password workspace
     * And   my username is mentioned at the top
     *
     * @group LU
     * @group login
     * @group saucelabs
     * @group v2
     */
    public function testLogin() 
    {
        $user = User::get('ada');
        $this->setClientConfig($user);

        $this->getUrl('login');

        $this->waitUntilISee('.plugin-check.' . $this->_browser['type'] . '.success');
        $this->waitUntilISee('.plugin-check.gpg.success');

        $this->assertVisible('passbolt-iframe-login-form');
        $this->goIntoLoginIframe();

        $this->assertVisibleByCss('.login-form.master-password');
        $this->assertInputValue('UserUsername', $user['Username']);

        $this->inputText('js_master_password', 'somethingwrong');
        $this->click('loginSubmit');

        $this->waitUntilISee('#loginMessage.error');
        $this->inputText('js_master_password', $user['MasterPassword']);

        $this->pressEnter();

        $this->assertElementContainsText('loginMessage', 'Please wait');
        $this->goOutOfIframe();

        $this->waitUntilISee('.login.form .feedback');
        $this->assertElementContainsText('.login.form .feedback', 'Logging in');
        $this->waitCompletion();

        // wait for redirection trigger
        $this->waitUntilISee('html.passboltplugin-ready');

        $this->waitUntilISee('.header .user.profile .details .name');
        $this->assertElementContainsText(
            $this->findByCss('.header .user.profile .details .name'),
            'Ada Lovelace'
        );
    }

    /**
     * Scenario: As AN I can login to passbolt on different tabs without conflict between workers
     *
     * Given As AN with plugin on the login page
     * When  I open a new tab and go to the login page
     * And   I switch back to the first tab
     * Then  I should be able to login to passbolt from the first tab
     * When  I logout
     * And   I switch to the second tab
     * Then  I should be able to login to passbolt from the second tab
     *
     * @group LU
     * @group login
     * @group v2
     */
    public function testMultipleTabsLogin() 
    {
        $user = User::get('ada');
        $this->setClientConfig($user);

        // Given As AN with plugin on the login page
        $this->getUrl('login');
        $this->waitUntilISee('.plugin-check.gpg.success');

        // When I open a new tab and go to the login page
        $this->openNewTab('login');
        $this->waitUntilISee('.plugin-check.gpg.success');

        // And I switch back to the first tab
        $this->switchToPreviousTab();

        // Then I should be able to login to passbolt from the first tab.
        $this->loginAs($user, false);

        // When I logout
        $this->logout();

        // And I switch to the second tab
        $this->switchToNextTab();

        // Then I should be able to login to passbolt from the second tab
        $this->loginAs($user, false);
    }

    /**
     * Scenario: As LU I should still be logged in after I restart the browser
     *
     * Given I am Ada
     * And   I am logged in on the passwords workspace
     * When  I restart the browser
     * Then  I should still be logged in
     *
     * @group LU
     * @group login
     * @group v2
     * @group skip
     */
    public function testRestartBrowserAndStillLoggedIn() 
    {
        // Given I am Ada
        $user = User::get('ada');

        // And I am logged in
        $this->loginAs($user);

        // When I restart the browser
        $this->restartBrowser();

        // Then I should still be logged in
        $this->waitUntilISee('.logout');
    }

    /**
     * Scenario: As LU I should still be logged in after I close and restore the passbolt tab
     *
     * Given I am Ada
     * And   I am on second tab
     * And   I am logged in on the passwords workspace
     * When  I close and restore the tab
     * Then  I should still be logged in
     *
     * @group LU
     * @group login
     * @group v2
     * @group firefox-only
     * PASSBOLT-2263 close and restore doesn't work with the latest chrome driver
     */
    public function testCloseRestoreTabAndStillLoggedIn() 
    {
        // Given I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

        // And I am on second tab
        $this->openNewTab();

        // And I am logged in
        $this->loginAs($user, false);

        // When I close and restore the tab
        $this->closeAndRestoreTab();
        $this->waitCompletion();

        // Then I should still be logged in
        $this->waitUntilISee('.logout');
    }
}
