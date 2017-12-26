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
 * Bug PASSBOLT-1620 - Regression test
 */
namespace Tests\LU\Regressions;

use App\Actions\UserActionsTrait;
use App\Actions\WorkspaceActionsTrait;
use App\PassboltTestCase;
use Data\Fixtures\User;

class PASSBOLT1620 extends PassboltTestCase
{
    use WorkspaceActionsTrait;
    use UserActionsTrait;

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
     */
    public function testNoDuplicateAfterSelectionAndFilterUserWorkspace() 
    {
        // Given I am Ada
        $user = User::get('ada');


        // And I am logged in on the password workspace
        $this->loginAs($user);
        $this->gotoWorkspace('user');

        // When I select a user
        $betty = User::get('betty');
        $this->clickUser($betty);

        // And I click on the recently modified filter
        $this->clickLink("Recently modified");
        $this->waitCompletion();

        // And I click on the all users filter
        $this->clickLink("All users");
        $this->waitCompletion();

        // Then I shouldn't see duplicated users in the list
        $carol = User::get('carol');

        $duplicatesCarolUsername = $this->findAllByXpath('//*[@id="js_wsp_users_browser"]//*[contains(text(), "' . $carol['Username'] . '")]');
        $this->assertEquals(1, count($duplicatesCarolUsername));
    }

}