<?php
/**
 * Feature :  As a user I can get the system to remember my passphrase for a limited time
 *
 * Scenarios :
 * As a user I can have my passphrase remembered by the system.
 *
 * @copyright (c) 2015-present Bolt Softwares Pvt Ltd
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class MasterPasswordRememberTest extends PassboltTestCase {

	/**
	 * Scenario : As a user I can have my passphrase remembered by the system.
	 *
	 * Given    I am Ada
	 * And      I am logged in on the password workspace
	 * When     I click a password
	 * When     I click on the copy button in the action bar
	 * Then     I can see the master key dialog
	 * And      I can see a checkbox to remember the password for x minutes
	 * When     I enter my passphrase and click on submit
	 * Then     I can see a success message saying the password was 'copied to clipboard'
	 * When     I click again on the copy button in the action bar
	 * And      I can see the master key dialog
	 * When     I enter my password
	 * And      I check the remember checkbox
	 * And      I click on submit
	 * Then     I can see a success message saying the password was 'copied to clipboard'
	 * When     I wait for 20 seconds to remove all the notifications
	 * And      I click again on the copy button in the action bar
	 * Then     I can see that the passphrase is not asked
	 * And      I can see a success message saying the password was 'copied to clipboard'
	 */
	function testMasterPasswordRemember() {
		// Given I am Ada
		$user = User::get('ada');
		$resource = Resource::get(array('user' => 'ada'));
		$this->setClientConfig($user);

		// And I am logged in on the password workspace
		$this->loginAs($user);

		// When I click on the first password in the list
		$this->clickPassword($resource['id']);

		// When I click on the link 'copy password'
		$this->click('js_wk_menu_secretcopy_button');

		// I should see the passphrase dialog.
		$this->assertMasterPasswordDialog($user);

		// I should see a checkbox remember my passphrase.
		$this->goIntoMasterPasswordIframe();
		$this->assertVisible('js_remember_master_password');
		$this->goOutOfIframe();

		// When I enter my passphrase from keyboard only.
		$this->enterMasterPassword($user['MasterPassword'], false);

		// Then I can see a success message telling me the password was copied to clipboard
		$this->assertNotification('plugin_secret_copy_success');

		// When I click on the link 'copy password'
		$this->click('js_wk_menu_secretcopy_button');

		// I should see the passphrase dialog.
		$this->assertMasterPasswordDialog($user);

		// When I enter my passphrase.
		$this->enterMasterPassword($user['MasterPassword'], true);

		// Then I can see a success message telling me the password was copied to clipboard
		$this->assertNotification('plugin_secret_copy_success');

		// Wait 20 seconds for all notifications to be cleared.
		sleep(20);

		// When I click on the link 'copy password'
		$this->click('js_wk_menu_secretcopy_button');

		// Then I can see a success message telling me the password was copied to clipboard
		$this->assertNotification('plugin_secret_copy_success');

		// No passphrase should have appeared.
		$this->waitUntilIDontSee('passbolt-iframe-master-password');
	}
}