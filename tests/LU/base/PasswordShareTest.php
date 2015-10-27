<?php
/**
 * Feature :  As a user I can share passwords
 *
 * Scenarios :
 * As a user I can see the share dialog using the share button in the action bar
 * As a user I can see the share dialog using the right click contextual menu
 * As a user I cannot access the share dialog from the action bar or the contextual menu if I have only read or update access to
 * As a user I can view the permissions for a password I own
 * As a user I can share a password with other users
 * As a user I edit the permissions of a password I own
 * As a user I delete a permission of a password I own
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
	 * Scenario: As a user I can view the permissions for a password I own
	 *
	 * Given    I am Ada
	 * And      I am logged in on the password workspace
	 * When     I go to the sharing dialog of the apache password
	 * Then     I can see that Ada is owner
	 * And      I can see that Betty can update
	 * And      I can see that Carol can read
	 * And      I can see that Dame can read
	 */
	public function testViewPasswordPermissions() {
		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);

		// And I am logged in on the password workspace
		$this->loginAs($user['Username']);

		// When I go to the sharing dialog of a password I own
		$resource = Resource::get(array(
			'user' => 'ada',
			'id' => Uuid::get('resource.id.apache')
		));
		$this->gotoSharePassword(Uuid::get('resource.id.apache'));

		// Then I can see that Ada is owner
		$this->assertPermission($resource, 'ada@passbolt.com', 'is owner');

		// And I can see that Betty can update
		$this->assertPermission($resource, 'betty@passbolt.com', 'can update');

		// And I can see that Carol can read
		$this->assertPermission($resource, 'carol@passbolt.com', 'can read');

		// And I can see that Dame can read
		$this->assertPermission($resource, 'dame@passbolt.com', 'can read');
	}

	/**
	 * Scenario: As a user I can share a password with other users
	 *
	 * Given    I am Carol
	 * And      I am logged in on the password workspace
	 * When     I go to the sharing dialog of a password I own
	 * Then     I can see Betty has no right on the password
	 * When     I give read access to betty for a password I own
	 * Then     I can see Betty has read access on the password
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

		// When I go to the sharing dialog of a password I own
		$resource = Resource::get(array(
			'user' => 'betty',
			'id' => Uuid::get('resource.id.gnupg')
		));
		$this->gotoSharePassword(Uuid::get('resource.id.gnupg'));

		// Then I can see Betty has no right on the password
		$this->assertElementNotContainText(
			$this->findByCss('#js_permissions_list'),
			'betty@passbolt.com'
		);

		// When I give read access to betty for a password I own
		$this->sharePassword($resource, 'betty@passbolt.com', 'can read', $user);

		// Then I can see Betty has read access on the password
		$this->assertPermission($resource, 'betty@passbolt.com', 'can read');

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

		// Since content was edited, we reset the database
		$this->resetDatabase();
	}

	/**
	 * Scenario: As a user I edit the permissions of a password I own
	 *
	 * Given    I am Carol
	 * And      I am logged in on the password workspace
	 * When     I go to the sharing dialog of a password I own
	 * Then     I can see Betty has update right on the password
	 * When     I change the permission of Betty to read access only
	 * Then     I can see Betty has read access on the password
	 */
	public function testEditPasswordPermission() {
		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);

		// And I am logged in on the password workspace
		$this->loginAs($user['Username']);

		// When I go to the sharing dialog of a password I own
		$resource = Resource::get(array(
			'user' => 'betty',
			'id' => Uuid::get('resource.id.apache')
		));
		$this->gotoSharePassword(Uuid::get('resource.id.apache'));

		// Then I can see Betty has update right on the password
		$this->assertPermission($resource, 'betty@passbolt.com', 'can update');

		// When I change the permission of Betty to read access only
		$this->editPermission($resource, 'betty@passbolt.com', 'can read', $user);

		// Then I can see Betty has read access on the password
		$this->assertPermission($resource, 'betty@passbolt.com', 'can read');

		// Since content was edited, we reset the database
		$this->resetDatabase();
	}

	/**
	 * Scenario: As a user I delete the permission of a password I own
	 *
	 * Given    I am Carol
	 * And      I am logged in on the password workspace
	 * When     I go to the sharing dialog of a password I own
	 * Then     I can see Betty has update right on the password
	 * When     I delete the permission of Betty
	 * Then     I can see Betty has no right anymore
	 */
	public function testDeletePasswordPermission() {
		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);

		// And I am logged in on the password workspace
		$this->loginAs($user['Username']);

		// When I go to the sharing dialog of a password I own
		$resource = Resource::get(array(
			'user' => 'betty',
			'id' => Uuid::get('resource.id.apache')
		));
		$this->gotoSharePassword(Uuid::get('resource.id.apache'));

		// Then I can see Betty has update right on the password
		$this->assertPermission($resource, 'betty@passbolt.com', 'can update');

		// When I delete the permission of Betty
		$this->deletePermission($resource, 'betty@passbolt.com');

		// Then I can see Betty has no right anymore
		$this->assertElementNotContainText(
			$this->findByCss('#js_permissions_list'),
			'betty@passbolt.com'
		);

		// Since content was edited, we reset the database
		$this->resetDatabase();
	}

}
