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

class PASSBOLT1827 extends PassboltSetupTestCase
{

    /**
     * Scenario: As a user I should be able to login with a passphrase longer than 50 char
     *
     * Given I register an account as John Doe
     * When I complete the setup with a passphrase longer than 50 char
     * Then  I am able to login
     */
    public function testSetupAndLoginWithLongPassphrase() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I register an account as John Doe
        $john = User::get('john');
        $this->registerUser($john['FirstName'], $john['LastName'], $john['Username']);

        // When I complete the setup with a passphrase longer than 50 char
        $john['MasterPassword'] = 'As a AP I should be able to log in with a passphrase length that is longer than fifty character in length';
        $this->goToSetup($john['Username']);
        $this->completeSetupWithKeyGeneration(
            [
            'username' => $john['Username'],
            'masterpassword' =>  $john['MasterPassword']
            ]
        );

        // Then  I am able to login
        $this->loginAs($john);
    }
}