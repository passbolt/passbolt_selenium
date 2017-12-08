<?php

use Facebook\WebDriver\WebDriverKeys;

/**
 * Anonymous user with plugin but no config open passbolt test
 *
 * @copyright (c) 2017 Passbolt SARL
 * @licence   GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class OpenPassboltTest extends PassboltSetupTestCase
{

    /**
     * Scenario :   As an AP with a plugin installed but not configured, clicking on the passbolt toolbar icon or using
     *                 the passbolt shortcut, I should open the passbolt public page "getting started" in a new tab
     * Given         Wherever I am on the web
     * And            The passbolt plugin is installed
     * And            The passbolt plugin is not configured
     * When            I click on the passbolt toolbar icon or I compose the passbolt shortcut
     * Then            I should reach the passbolt public page "getting started" in a new tab
     * And            This page redirects me to the demo login
     */
    public function testOpenPassboltNoConfig() 
    {
        $this->waitUntilISee('body');
        $this->clickToolbarIcon();
        $this->waitUntilUrlMatches('https://demo.passbolt.com/auth/login', false);
    }

    /**
     * Scenario :   As an AP with a plugin installed and configured, clicking on the passbolt toolbar icon or using
     *                 the passbolt shortcut, I should open the passbolt application in a new tab
     * Given         Wherever I am on the web
     * And            The passbolt plugin is installed
     * And            The passbolt plugin is partially configured
     * When            I click on the passbolt toolbar icon or I compose the passbolt shortcut
     * Then            I should reach the passbolt application in a new tab
     */
    public function testOpenPassboltConfig() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        $key = Gpgkey::get(['name' => 'johndoe']);

        // Register John Doe as a user.
        $this->registerUser('John', 'Doe', $key['owner_email']);

        // Go to setup page.
        $this->goToSetup($key['owner_email']);
        $this->completeRegistration();

        // Simulate click on the passbolt toolbar icon
        $this->clickToolbarIcon();

        // I should be on the login page.
        $this->waitUntilISee('.information h2', '/Welcome back!/');
    }

}