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
 * Feature: Toolbar
 * - As a user with non configured plugin I can see an explanation page when clicking on the toolbar
 * - As a user with configured plugin I can see the login page when clicking on the toolbar
 */
namespace Tests\AP\Base;

use App\Common\Actions\DebugActionsTrait;
use App\PassboltSetupTestCase;
use Data\Fixtures\Gpgkey;

class ToolbarTest extends PassboltSetupTestCase
{
    use DebugActionsTrait;


    /**
     * Simulate click on the toolbar passbolt icon.
     */
    public function clickToolbarIcon()
    {
        $this->goToDebug();
        $this->click('#simulateToolbarIcon');
        sleep(1);
        // Ensure the selenium works on the new tab.
        $handles=$this->getDriver()->getWindowHandles();
        $last_window = end($handles);
        $this->getDriver()->switchTo()->window($last_window);
    }

    /**
     * Scenario: As a user with non configured plugin I can see an explanation page when clicking on the toolbar
     *
     * Given I am a user with the plugin installed but not configured
     * When  I click on the passbolt toolbar icon or I compose the passbolt shortcut
     * Then  I should reach the passbolt public page "getting started" in a new tab
     * And   This page provide me with some information
     *
     * @group AP
     * @group toolbar
     * @group v2
     */
    public function testToolbarIconOpenPassboltNoConfig()
    {
        $this->waitUntilISee('body');
        $this->clickToolbarIcon();
        $this->waitUntilUrlMatches('https://www.passbolt.com/start', false);
        $this->assertElementContainsText(
            $this->findByCss('h1'),
            'How would you like to use passbolt?'
        );
    }

    /**
     * Scenario: As a user with configured plugin I can see the login page when clicking on the toolbar
     *
     * Given I am a user with the plugin configured
     * And   I am anywhere on the web
     * And   The passbolt plugin is installed
     * And   The passbolt plugin is partially configured
     * When  I click on the passbolt toolbar icon or I compose the passbolt shortcut
     * Then  I should reach the passbolt application in a new tab
     *
     * @group AP
     * @group toolbar
     * @group v2
     */
    public function testToolbarIconOpenPassboltConfig()
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