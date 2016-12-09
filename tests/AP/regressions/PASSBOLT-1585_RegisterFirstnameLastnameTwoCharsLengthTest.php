<?php
/**
 * Bug PASSBOLT-1585 - Regression test
 *
 * @copyright (c) 2015-present Bolt Softwares Pvt Ltd
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class PASSBOLT1585 extends PassboltSetupTestCase
{

	/**
	 * Scenario:  As an AP I should be able to register a user with 2 char length as firstname or lastname
	 * When     I create an account as Chien Shiung, and I proceed through the entire setup.
	 * Then     I should be able to login
	 */
	public function testRegisterTwoCharsLengthFirstNameLastName() {
		// Reset database at the end of test.
		$this->resetDatabaseWhenComplete();

		// When I create an account as Chien Shiung, and I proceed through the entire setup.
		$chienShiung = User::get('chien-shiung');
		$this->registerUser($chienShiung['FirstName'], $chienShiung['LastName'], $chienShiung['Username']);
		$this->goToSetup($chienShiung['Username']);
		$this->waitForSection('domain_check');
		$this->assertNotVisible('.plugin-check.warning');
		$this->completeRegistration($chienShiung);

		// Then I should be able to login
		$this->loginAs($chienShiung, false);
	}
}