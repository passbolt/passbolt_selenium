<?php
/**
 * Feature :  As a user I can share passwords
 *
 * Scenarios :
 * As a user I can see the share dialog using the share button in the action bar
 * As a user I can see the share dialog using the right click contextual menu
 * As a user I cannot access the share dialog from the action bar or the contextual menu if I have only read or update access to
 * As a user I can share a password with other users
 *
 * @copyright    (c) 2015-present Bolt Software Pvt. Ltd.
 * @licence      GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
 */
class PasswordShareTest extends PassboltTestCase
{

	/**
	 * Scenario: As a user I can see the share dialog using the share button in the action bar
	 *
	 * Given    I am Ada
	 * And      I am logged in on the password workspace
	 * Then     I can see the share password button is disabled
	 * When     I click on a password I own
	 * Then     I can see the share button is enabled
	 * When     I click on the share button
	 * Then     I can see the share password dialog
	 */
	public function testSharePasswordButton() {
		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);

		// And I am logged in on the password workspace
		$this->loginAs($user['Username']);

		// Then I can see the share password button is disabled
		$this->assertVisible('js_wk_menu_sharing_button');
		$this->assertVisible('#js_wk_menu_sharing_button.disabled');

		// When I click on a password I own
		$resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));
		$this->clickPassword($resource['id']);

		// Then I can see the share button is enabled
		$this->assertNotVisible('#js_wk_menu_sharing_button.disabled');
		$this->assertVisible('js_wk_menu_sharing_button');

		// When I click on the share button
		$this->click('js_wk_menu_sharing_button');

		// Then I can see the share password dialog
		$this->assertVisible('.share-password-dialog');
	}

	/**
	 * Scenario: As a user I can see the share dialog using the right click contextual menu
	 *
	 * Given    I am Ada
	 * And      I am logged in on the password workspace
	 * When     I right click on a password I own
	 * Then     I can see the contextual menu
	 * And      I can see the the share option is enabled
	 * When     I click on the share link in the contextual menu
	 * Then     I can see the share password dialog
	 */
	public function testSharePasswordRightClick() {
		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);

		// And I am logged in on the password workspace
		$this->loginAs($user['Username']);

		// When I right click on a password I own
		$resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));
		$this->rightClickPassword($resource['id']);

		// Then I can see the contextual menu
		$this->assertVisible('js_contextual_menu');

		// I can see the the share option is enabled
		$this->assertVisible('#js_password_browser_menu_share.ready');

		// When I click on the share link in the contextual menu
		$this->click('#js_password_browser_menu_share a');

		// Then I can see the share password dialog
		$this->assertVisible('.share-password-dialog');
	}

	/**
	 * Scenario: As a user I cannot access the share dialog from the action bar or the contextual menu if I have only read or update access to
	 *
	 * Given    I am Ada
	 * And      I am logged in on the password workspace
	 * When     I click on a password I can read
	 * Then     I can see the share button is not active
	 * When     I right click on a password I have only read access to
	 * Then     I can see the contextual menu
	 * And      I can see the share option is disabled
	 * When     I click on a password I can update
	 * Then     I can see the share button is not active
	 * When     I right click on a password I have only update access to
	 * Then     I can see the contextual menu
	 * And      I can see the share option is disabled
	 */
	public function testEditPasswordNoRightNoShare() {
		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);

		// And I am logged in on the password workspace
		$this->loginAs($user['Username']);

		// When I click on a password I have only read access to
		$r = Resource::get(array(
			'user' => 'ada',
			'permission' => 'read'
		));
		$this->clickPassword($r['id']);

		// Then I can see the share button is not active
		$this->assertDisabled('js_wk_menu_sharing_button');

		// When I right click on a password I have only update access to
		$this->rightClickPassword($r['id']);

		// Then I can see the contextual menu
		$this->findById('js_contextual_menu');

		// And I can see the share option is disabled
		$this->click('#js_password_browser_menu_share a');
		$this->assertNotVisible('.share-password-dialog');

		// When I click on a password I have only update access to
		$r = Resource::get(array(
			'user' => 'ada',
			'permission' => 'update'
		));
		$this->clickPassword($r['id']);

		// Then I can see the share button is not active
		$this->assertDisabled('js_wk_menu_sharing_button');

		// When I right click on a password I have only update access to
		$this->rightClickPassword($r['id']);

		// Then I can see the contextual menu
		$this->findById('js_contextual_menu');

		// And I can see the share option is disabled
		$this->click('#js_password_browser_menu_share a');
		$this->assertNotVisible('.share-password-dialog');
	}

	/**
	 * Scenario: As a user I can share a password with other users
	 *
	 * Given    I am Carol
	 * And      I am logged in on the password workspace
	 * When     I click on a password I own
	 * And      I click on the share button
	 * Then     I can not see Betty in the list of people the password is shared with
	 * When     I enter 'betty' as username
	 * And      I wait until I see the automplete list resolved
	 * And      I click on 'betty@passbolt.com'
	 * And      I select the option 'can read' as permission
	 * And      I click on the Add button
	 * Then     I can see that temporary changes are waiting to be saved
	 * When     I click on the save button
	 * Then     I see the master password dialog
	 * When     I enter the master password and click submit
	 * Then     I see a dialog telling me encryption is in progress
	 * And      I see a notice message that the operation was a success
	 * And      I can see Betty in the list of people the password is shared with
	 * 
	 * When     I logout
	 * And      I am Betty
	 * And      I am logged in on the password workspace
	 * And      I click on a password shared with me
	 * And      I click on the link 'copy password'
	 * Then     I can see the master key dialog
	 * When     I enter my master password and click submit
	 * Then     I can see a success message telling me the password was copied to clipboard
	 * And      the content of the clipboard is valid
	 */
	public function testSharePasswordAndView() {
		// Given I am Carol
		$user = User::get('carol');
		$this->setClientConfig($user);

		// And I am logged in on the password workspace
		$this->loginAs($user['Username']);

		// When I click on a password I own
		$resource = Resource::get(array(
			'user' => 'betty',
			'id' => Uuid::get('resource.id.gnupg')
		));
		$this->clickPassword($resource['id']);

		// And I click on the share button
		$this->click('js_wk_menu_sharing_button');

		// Then I can not see Betty in the list of people the password is shared with
		$this->assertElementNotContainText(
			$this->findByCss('#js_permissions_list'),
			'betty@passbolt.com'
		);

		// When I enter 'betty' as username
		$this->inputText('js_perm_create_form_aro_auto_cplt', 'betty');

		// And I wait until I see the automplete list resolved
		$this->waitUntilISee('.share-password-dialog .autocomplete-content', '/betty@passbolt.com/i');

		// And I click on 'betty@passbolt.com'
		$this->clickLink('betty@passbolt.com');

		// And I select the option 'can read' as permission
		$this->selectOption('js_perm_create_form_type', 'can read');

		// And I click on the Add button
		$this->click('js_perm_create_form_add_btn');

		// Then I can see that temporary changes are waiting to be saved
		$this->assertElementContainsText(
			$this->findByCss('.share-password-dialog #js_permissions_changes'),
			'You need to save to apply the changes'
		);

		// When I click on the save button
		$this->click('js_rs_share_save');

		// Then I see the master password dialog
		$this->assertMasterPasswordDialog($user);

		// When I enter the master password and click submit
		$this->enterMasterPassword($user['MasterPassword']);

		// Then I see a dialog telling me encryption is in progress
		$this->waitUntilISee('passbolt-iframe-progress-dialog');
		$this->waitCompletion();

		// And I see a notice message that the operation was a success
		$this->assertNotification('app_share_update_success');

		// And I can see Betty in the list of people the password is shared with
		$this->assertElementContainsText(
			$this->findByCss('#js_permissions_list'),
			'betty@passbolt.com'
		);

		// When I logout
		$this->logout();

		// And I am Betty
		$user = User::get('betty');
		$this->setClientConfig($user);

		// And I am logged in on the password workspace
		$this->loginAs($user['Username']);

		// And I click on a password shared with me
		$this->clickPassword($resource['id']);

		// And I click on the link 'copy password'
		$this->click('js_wk_menu_secretcopy_button');

		// Then I can see the master key dialog
		$this->assertMasterPasswordDialog($user);

		// When I enter my master password and click submit
		$this->enterMasterPassword($user['MasterPassword']);

		// Then I can see a success message telling me the password was copied to clipboard
		$this->assertNotification('plugin_secret_copy_success');

		// And the content of the clipboard is valid
		$this->assertClipboard($resource['password']);
	}

}
