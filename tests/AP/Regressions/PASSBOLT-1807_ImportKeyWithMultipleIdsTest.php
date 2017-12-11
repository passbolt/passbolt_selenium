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

class PASSBOLT1807 extends PassboltSetupTestCase
{

    /**
     * Scenario: As a user I should be able to import a key with multiple IDs
     *
     * Given I register an account as Margaret
     * When I import a key with multiple ids
     * Then  I am able to complete the setup
     * And I can login
     */
    public function testSetupImportKeyWithMultipleIds() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I register an account as Margaret
        $user = User::get('margaret');
        $this->registerUser($user['FirstName'], $user['LastName'], $user['Username']);

        // When I import a key with multiple ids
        $this->goToSetup($user['Username']);

        // Then  I am able to complete the setup
        $this->completeSetupWithKeyImport(
            [
            'private_key' => file_get_contents(Gpgkey::get(['name' => 'margaret'])['filepath'])
            ]
        );

        // And I can login
        $this->loginAs($user);
    }
}