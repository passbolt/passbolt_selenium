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
 * Feature : As AN on the login or setup stage 0 page I can download the plugin
 *
 * @copyright (c) 2017 Passbolt SARL
 * @licence   GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
namespace Tests\AN\Regressions;

use App\PassboltTestCase;
use App\Actions\SetupActionsTrait;

class PASSBOLT1380_Login extends PassboltTestCase
{
    use SetupActionsTrait;

    /**
     * Scenario: As AN on the login page I can download the plugin
     * Given I am an AN on the login page
     * Then  I can see a block saying that I need an addon
     * Then  I can see "Download it here" link in the plugin check box
     * When  I click on the link
     * Then  I am redirected to https://addons.mozilla.org
     *
     * @group AN
     * @group regression
     * @group firefox-only
     * @group v2
     */
    public function testFirefoxDownloadLinkOnLogin() 
    {
        $this->getUrl('/');
        $this->assertTitleContain('Login');
        $this->assertVisibleByCss('.plugin-check.firefox.error');
        $this->followLink('Download it here');
        $this->waitUntilUrlMatches('https://addons.mozilla.org/en-US/firefox/addon/passbolt/');
        $this->assertTitleContain('Add-ons for Firefox');
    }

    /**
     * Scenario: As AN on the stage 0 of the setup I can download the plugin
     *
     * Given I am an AN on the login page
     * When  I register as John Doe
     * And   I follow the link to the setup in confirmation email
     * Then  I can see a block saying that I need an addon
     * Then  I can see "Download it here" link in the plugin check box
     * When  I click on the link
     * Then  I am redirected to https://addons.mozilla.org
     *
     * @group AN
     * @group regression
     * @group firefox-only
     */
    public function testFirefoxDownloadLinkOnSetup() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Register John Doe as a user.
        $this->registerUser('John', 'Doe', 'johndoe@passbolt.com');

        // Go to setup page and register
        $this->goToSetup('johndoe@passbolt.com', false);

        // Then I can see a block saying that I need an addon
        $this->assertVisibleByCss('.plugin-check.firefox.error');

        // ThenI can see "Download it here" link in the plugin check box
        // When I click on the link
        $this->followLink('Download it here');

        // Then I am redirected to https://addons.mozilla.org
        $this->waitUntilUrlMatches('https://addons.mozilla.org/en-US/firefox/addon/passbolt/');
        $this->assertTitleContain('Add-ons for Firefox');
    }
}