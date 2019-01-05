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
 * Bug PASSBOLT-1606 - Regression test
 */
namespace Tests\LU\Regressions;

use App\Assertions\ConfirmationDialogAssertionsTrait;
use App\PassboltTestCase;
use Data\Fixtures\User;
use App\Common\Servers\PassboltServer;

class PASSBOLT1606 extends PassboltTestCase
{
    use ConfirmationDialogAssertionsTrait;

    /**
     * Scenario: As LU I can't select multiple passwprd
     *
     * Given I am Ada
     * And   I am logged in on the users workspace
     * When  I select a user
     * And   I click on the recently modified filter
     * And   I click on the all users filter
     * Then  I shouldn't see duplicated users in the list
     *
     * @group LU
     * @group regression
     * @group v2
     * @group broken
     */
    public function testAutoLogoutFromAnotherTab() 
    {
        // Given I am Ada
        $timeout = 0.25;
        $user = User::get('ada');
        $this->setClientConfig($user);

        // And I am on second tab
        $this->openNewTab();

        // Reduce the session timeout to accelerate the test
        PassboltServer::setExtraConfig(['Session' => ['timeout' => $timeout]]);

        // When I am logged in on the password workspace
        $this->loginAs($user, ['setConfig' => false]);

        // Then I should see the session expired dialog
        $this->assertSessionExpiredDialog();

        // And I switch to the previous
        $this->switchToPreviousTab();

        // And I wait until the expired dialog redirect the user to the login page
        sleep(($timeout*60)+1);

        // And I switch to the passbolt tab
        $this->switchToNextTab();

        // Then I should see the login page
        $this->waitUntilUrlMatches('/auth/login');
    }

}