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
namespace Tests\AP\base;

use App\PassboltSetupTestCase;
use Data\Fixtures\User;

class PASSBOLT1494 extends PassboltSetupTestCase
{

    /**
     * @group no-saucelabs
     *
     * Scenario: As an AP going through the setup two times, I should be able to login at the end of the second setup.
     * Given I create an account as John Doe, and I proceed through the entire setup.
     * When I register again with another username, and proceed again through the entire setup
     * Then  I should be able to see the login form
     * And I should be able to login
     */
    public function testPluginShouldStartAfterTwoSetup() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Register John Doe as a user.
        $john = User::get('john');
        $this->registerUser($john['FirstName'], $john['LastName'], $john['Username']);

        // Go to setup page.
        $this->goToSetup($john['Username']);

        // Wait until I see the setup section domain check.
        $this->waitForSection('domain_check');

        // I should not se any warning.
        $this->assertNotVisible('.plugin-check.warning');

        // Complete registration.
        $this->completeRegistration($john);

        // I should be able to login
        $this->loginAs($john);

        // I logout.
        $this->logout();

        // Register Curtis Mayfield as a user.
        $curtis = User::get('curtis');
        $this->registerUser($curtis['FirstName'], $curtis['LastName'], $curtis['Username']);

        // Go to setup page.
        $this->goToSetup($curtis['Username'], false);

        // Wait until I see the setup section domain check.
        $this->waitForSection('domain_check');
        $this->waitUntilISee('.plugin-check.warning');

        // Complete registration.
        $this->completeRegistration($curtis);

        // And I am logged in on the password workspace
        $this->loginAs($curtis);

        // wait for redirection trigger
        sleep(1);
        $this->waitCompletion();

        $this->assertElementContainsText(
            $this->findByCss('.header .user.profile .details .name'),
            $curtis['FirstName'] . ' ' . $curtis['LastName']
        );

        // Switch back config to primary domain.
        $this->switchToPrimaryDomain();
    }
}