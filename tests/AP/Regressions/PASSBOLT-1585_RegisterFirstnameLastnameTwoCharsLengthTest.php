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
namespace Tests\AP\Regressions;

use App\PassboltSetupTestCase;
use Data\Fixtures\User;

class PASSBOLT1585 extends PassboltSetupTestCase
{

    /**
     * Scenario: As an AP I should be able to register a user with 2 char length as firstname or lastname
     *
     * Given I am Chien Shiung
     * And   I create and account
     * And   I proceed through the entire setup.
     * Then  I should be able to login
     *
     * @group AP
     * @group setup
     * @group regression
     * @group v2
     */
    public function testRegisterTwoCharsLengthFirstNameLastName() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // When I create an account as Chien Shiung, and I proceed through the entire setup.
        $chienShiung = User::get('chien-shiung');
        $this->registerUser($chienShiung['FirstName'], $chienShiung['LastName'], $chienShiung['Username']);
        $this->goToSetup($chienShiung['Username']);
        $this->waitForSection('domain_check');
        $this->assertNotVisibleByCss('.plugin-check.warning');
        $this->completeRegistration($chienShiung);

        // Then I should be able to login
        $this->loginAs($chienShiung, false);
    }
}