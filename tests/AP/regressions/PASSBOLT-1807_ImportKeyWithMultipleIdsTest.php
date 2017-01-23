<?php
/**
 * Bug PASSBOLT-1807 - Regression test
 *
 * @copyright (c) 2015-present Bolt Softwares Pvt Ltd
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class PASSBOLT1807 extends PassboltSetupTestCase
{

    /**
     * Scenario: As a user I should be able to import a key with multiple IDs
     *
     * Given    I register an account as Margaret
     * When     I import a key with multiple ids
     * Then     I am able to complete the setup
     * And      I can login
     */
    public function testSetupImportKeyWithMultipleIds() {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I register an account as Margaret
        $user = User::get('margaret');
        $this->registerUser($user['FirstName'], $user['LastName'], $user['Username']);

        // When I import a key with multiple ids
        $this->goToSetup($user['Username']);

        // Then I am able to complete the setup
        $this->completeSetupWithKeyImport([
            'private_key' => file_get_contents(Gpgkey::get(['name' => 'margaret'])['filepath'])
        ]);

        // And I can login
        $this->loginAs($user);
    }
}