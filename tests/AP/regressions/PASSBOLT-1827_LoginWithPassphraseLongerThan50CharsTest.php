<?php
/**
 * Bug PASSBOLT-1827 - Regression test
 *
 * @copyright (c) 2015-present Bolt Softwares Pvt Ltd
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class PASSBOLT1827 extends PassboltSetupTestCase
{

	/**
	 * Scenario: As a user I should be able to login with a passphrase longer than 50 char
	 *
	 * Given    I register an account as John Doe
	 * When     I complete the setup with a passphrase longer than 50 char
	 * Then     I am able to login
	 */
	public function testSetupAndLoginWithLongPassphrase() {
		// Reset database at the end of test.
		$this->resetDatabaseWhenComplete();

		// Given I register an account as John Doe
		$john = User::get('john');
		$this->registerUser($john['FirstName'], $john['LastName'], $john['Username']);

		// When I complete the setup with a passphrase longer than 50 char
		$john['MasterPassword'] = 'As a AP I should be able to log in with a passphrase length that is longer than fifty character in length';
		$this->goToSetup($john['Username']);
		$this->completeSetupWithKeyGeneration([
			'username' => $john['Username'],
			'masterpassword' =>  $john['MasterPassword']
		]);

		// Then I am able to login
		$this->loginAs($john);
	}
}