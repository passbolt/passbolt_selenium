<?php
/**
 * Feature :  As a user I can get the system to remember my passphrase for a limited time
 *
 * Scenarios :
 * As a user I can have my passphrase remembered by the system.
 *
 * @copyright (c) 2017 Passbolt SARL
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class MasterPasswordRememberTest extends PassboltTestCase {

	/**
	 * @group saucelabs
	 * Scenario : As a user I can have my passphrase remembered by the system.
	 *
	 * Given    I am Ada
	 * And      I am logged in on the password workspace
	 * When     I click on a password in the list
	 * And      I click on the link 'copy password'
	 * Then     I should see the passphrase dialog.
	 * And      I should see a checkbox remember my passphrase.
	 * When     I enter my passphrase from keyboard only
	 * Then     The password should have been copied to clipboard
	 * When     I click on another password in the list
	 * And		I click on the link 'copy password'
	 * Then     I should see the passphrase dialog
	 * When     I enter my passphrase from keyboard only
	 * And      I check the remember checkbox
	 * Then     The password should have been copied to clipboard
	 * When     I click on another password in the list
	 * And      I click again on the copy button in the action bar
	 * Then     The password should have been copied to clipboard
	 */
	function testMasterPasswordRemember() {
		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);

		// And I am logged in on the password workspace
		$this->loginAs($user);

		// When I click on a password in the list
		$rsA = Resource::get(array('user' => 'ada', 'id' => Uuid::get('resource.id.apache')));
		$this->clickPassword($rsA['id']);

		// And I click on the link 'copy password'
		$this->click('js_wk_menu_secretcopy_button');

		// Then I should see the passphrase dialog.
		$this->assertMasterPasswordDialog($user);

		// And I should see a checkbox remember my passphrase
		$this->goIntoMasterPasswordIframe();
		$this->assertVisible('js_remember_master_password');
		$this->goOutOfIframe();

		// When I enter my passphrase from keyboard only
		$this->enterMasterPassword($user['MasterPassword'], false);

		// Then The password should have been copied to clipboard
		$this->waitCompletion();
		$this->assertClipboard($rsA['password']);

		// When I click on another password in the list
		$rsB = Resource::get(array('user' => 'ada', 'id' => Uuid::get('resource.id.bower')));
		$this->clickPassword($rsB['id']);

		// And I click on the link 'copy password'
		$this->click('js_wk_menu_secretcopy_button');

		// Then I should see the passphrase dialog
		$this->assertMasterPasswordDialog($user);

		// When I enter my passphrase from keyboard only
		// And I check the remember checkbox
		$this->enterMasterPassword($user['MasterPassword'], true);
		$this->waitUntilIDontSee('#passbolt-iframe-progress-dialog');
		$this->waitCompletion();

		// Then The password should have been copied to clipboard
		$this->assertClipboard($rsB['password']);

		// When I click on another password in the list
		$rsC = Resource::get(array('user' => 'ada', 'id' => Uuid::get('resource.id.centos')));
		$this->clickPassword($rsC['id']);

		// And I click on the link 'copy password'
		$this->click('js_wk_menu_secretcopy_button');

		// Then The password should have been copied to clipboard
		$this->waitCompletion();
		$this->assertClipboard($rsC['password']);

	}
}