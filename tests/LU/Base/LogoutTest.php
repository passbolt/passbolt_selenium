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
 * Feature : Logout
 *
 * As LU I should be logged out when I click on the logout link in the navigation
 * As LU I should be logged out when the session expires
 * As LU I should be logged out when I quit the browser and restart it after my session expired
 * As LU I should be logged out when I close the passbolt tab and restore it after my session expired
 */
namespace Tests\LU\Base;

use App\Actions\PasswordActionsTrait;
use App\Assertions\ConfirmationDialogAssertionsTrait;
use App\PassboltTestCase;
use App\Common\Servers\PassboltServer;
use Data\Fixtures\User;
use Data\Fixtures\Resource;

class LogoutTest extends PassboltTestCase
{
    use PasswordActionsTrait;
    use ConfirmationDialogAssertionsTrait;

    /**
     * Executed after every tests
     */
    protected function tearDown() 
    {
        // Reset the selenium extra configuration.
        PassboltServer::resetExtraConfig();
        parent::tearDown();
    }

    /**
     * Scenario: As LU I should be logged out when I click on the logout link in the navigation
     *
     * Given I am Ada
     * And   I am logged in on the password workspace
     * When  I click on the logout button
     * Then  I should see the login page
     *
     * @group LU
     * @group logout
     * @group saucelabs
     * @group v2
     */
    public function testLogout() 
    {
        // Given I am Ada
        $user = User::get('ada');

        // Reduce the session timeout to accelerate the test
        PassboltServer::setExtraConfig([
            'Session' => [
                'timeout' => 0.25
            ]]
        );

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // When I click on the logout button
        $this->click('#js_app_navigation_right .logout a');

        // Then I should see the login page
        $this->waitUntilUrlMatches('/auth/login');
    }

    /**
     * Scenario: As LU I should be logged out when the session expires (auto redirect)
     *
     * Given I am Ada
     * And   I am logged in on the password workspace
     * And   I wait until the session expires
     * When  I click on a password I own
     * Then  I should see the session expired dialog
     * And   I should be redirected to the login page in 60 seconds
     *
     * @group LU
     * @group logout
     * @group saucelabs
     * @group v2
     */
    public function testOnClickSessionExpiredAutoRedirect() 
    {
        // Given I am Ada
        $timeout = 0.25;
        $user = User::get('ada');

        // Reduce the session timeout to accelerate the test
        PassboltServer::setExtraConfig(['Session' => ['timeout' => $timeout]]);

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // And I wait until the session expires
        sleep(($timeout*60)+1);

        // And I click on a password I own
        $resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));
        $this->clickPassword($resource['id']);

        // Then I should see the session expired dialog
        $this->assertSessionExpiredDialog();

        // And I should be redirected to the login page in 60 seconds
        $this->waitUntilUrlMatches('/auth/login');
    }

    /**
     * Scenario: As LU I should be logged out when the session expires (manual redirect)
     *
     * Given I am Ada
     * And   I am logged in on the passwords workspace
     * When  I wait until the session timeout
     * And   I click on a password I own
     * Then  I should see the session expired dialog
     * When  I click on the 'Redirect now' button
     * Then  I should see the login page
     *
     * @group LU
     * @group logout
     * @group no-saucelabs
     * @group v2
     */
    public function testOnClickSessionExpiredManualRedirect() 
    {
        // Given I am Ada
        $timeout = 0.25;
        $user = User::get('ada');

        // Reduce the session timeout to accelerate the test
        PassboltServer::setExtraConfig(['Session' => ['timeout' => $timeout]]);

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // When I wait until the session timeout
        sleep(($timeout*60)+1);

        // And I click on a password I own
        $resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));
        $this->clickPassword($resource['id']);

        // Then I should see the session expired dialog
        $this->assertSessionExpiredDialog();

        // When I click on Redirect now
        $this->click('confirm-button');

        // Then I should see the login page
        $this->waitUntilUrlMatches('/auth/login');
    }

    /**
     * Scenario: As LU I should be logged out when I quit the browser and restart it after my session expired
     *
     * Given I am Ada
     * And   I am logged in on the passwords workspace
     * When  I wait until the session timeout
     * Then  I should see the session expired dialog
     * When  I click on the 'Redirect now' button
     * Then  I should see the login page
     *
     * @group LU
     * @group logout
     * @group saucelabs
     * @group v2
     * @group broken
     */
    public function testSessionExpired() 
    {
        // Given I am Ada
        $timeout = 0.25;
        $user = User::get('ada');

        // Reduce the session timeout to accelerate the test
        PassboltServer::setExtraConfig(['Session' => ['timeout' => $timeout]]);

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // Then I should see the session expired dialog
        $this->assertSessionExpiredDialog();

        // When I click on Redirect now button
        $this->click('confirm-button');

        // Then I should see the login page
        $this->waitUntilUrlMatches('/auth/login');
    }

    /**
     * Scenario: As LU I should be logged out when I quit the browser and restart it after my session expired
     *
     * Given I am Ada
     * And   I am logged in on the passwords workspace
     * When  I quit the browser and restart it after my session is expired
     * Then  I should be logged out
     *
     * @group LU
     * @group logout
     * @group no-saucelabs
     * @group v2
     * @group skip
     */
    public function testRestartBrowserAndLoggedOutAfterSessionExpired() 
    {
        // Given I am Ada
        $timeout = 0.25;
        $user = User::get('ada');

        // Reduce the session timeout to accelerate the test
        PassboltServer::setExtraConfig(['Session' => ['timeout' => $timeout]]);

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // When I restart the browser
        $this->restartBrowser(['waitBeforeRestart' => ($timeout*60)+1]);

        // Then I should be logged out
        $this->waitUntilUrlMatches('/auth/login');
    }

    /**
     * Scenario: As LU I should be logged out when I close the passbolt tab and restore it after my session expired
     *
     * Given I am Ada
     * And   I am logged in on the passwords workspace
     * When  I close the tab and restore it after my session is expired
     * Then  I should be logged out
     *
     * @group LU
     * @group logout
     * @group broken
     * @group skip
     * PASSBOLT-2263 close and restore doesn't work with the latest chrome driver
     * PASSBOLT-2419 close and restore doesn't work with the latest firefox driver
     */
    public function testCloseRestoreTabAndLoggedOutAfterSessionExpired() 
    {
        // Given I am Ada
        $timeout = 0.25;
        $user = User::get('ada');
        $this->setClientConfig($user);

        // And I am on second tab
        $this->openNewTab();

        // Reduce the session timeout to accelerate the test
        PassboltServer::setExtraConfig(['Session' => ['timeout' => $timeout]]);

        // And I am logged in on the password workspace
        $this->loginAs($user, ['setConfig' => false]);

        // When I close and restore the tab
        $this->closeAndRestoreTab(['waitBeforeRestore' => ($timeout*60)+1]);

        // Then I should be logged out
        $this->waitUntilUrlMatches('/auth/login');
    }

}