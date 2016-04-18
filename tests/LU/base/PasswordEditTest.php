<?php
/**
 * Feature: As a user I can edit a password
 *
 * Scenarios:
 * As a user I can edit a password using the edit button in the action bar
 * As a user I can edit a password using the right click contextual menu
 * As a user I can open close the edit password dialog
 * As a user I can see the edit password dialog content
 * As a user I can edit a password by using keyboard shortcuts only
 * As a user I can edit the name of a password I have own
 * As a user I can edit the description of a password I have own
 * As a user I can edit the uri of a password I have own
 * As a user I can see the current password complexity when editing a password
 * As a user I can edit the secret of a password I have own
 * As a user editing my password I can use the button to view my secret in clear text
 * As a user editing my password I can generate a new random password automatically
 * As a user I can not edit a password I have only read access to
 * As user B I can see the changes are reflected when user A is editing a password we share
 * As a user I can see error messages when editing a password with wrong inputs
 *
 * @copyright (c) 2015-present Bolt Softwares Pvt Ltd
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class PasswordEditTest extends PassboltTestCase
{

    /**
     * Scenario: As a user I can edit a password using the edit button in the action bar
     *
     * Given    I am Ada
     * And      I am logged in on the password workspace
     * Then     I can see the edit password button is disabled
     * When     I click on a password I own
     * Then     I can see the edit button is enabled
     * When     I click on the edit button
     * Then     I can see the edit password dialog
     */
    public function testEditPasswordButton() {
        // Given I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // Then I can see the edit password button is disabled
        $this->assertVisible('js_wk_menu_edition_button');
        $this->assertVisible('#js_wk_menu_edition_button.disabled');

        // When I click on a password I own
        $resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));
        $this->clickPassword($resource['id']);

        // Then I can see the edit button is enabled
        $this->assertNotVisible('#js_wk_menu_edition_button.disabled');
        $this->assertVisible('js_wk_menu_edition_button');

        // When I click on the edit button
        $this->click('js_wk_menu_edition_button');

        // Then I can see the edit password dialog
        $this->assertVisible('.edit-password-dialog');
    }

    /**
     * Scenario: As a user I can edit a password using the right click contextual menu
     *
     * Given    I am Ada
     * And      I am logged in on the password workspace
     * When     I right click on a password I own
     * Then     I can see the contextual menu
     * And      I can see the the edit option is enabled
     * When     I click on the edit link in the contextual menu
     * Then     I can see the edit password dialog
     */
    public function testEditPasswordRightClick() {
        // Given I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // When I right click on a password I own
        $resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));
        $this->rightClickPassword($resource['id']);

        // Then I can see the contextual menu
        $this->assertVisible('js_contextual_menu');

        // And I can see the the edit option is enabled
        // @TODO PASSBOLT-1028

        // When I click on the edit link in the contextual menu
        $this->click('#js_password_browser_menu_edit a');

        // Then I can see the edit password dialog
	    $this->assertVisible('.edit-password-dialog');
    }

    /**
     * Scenario: As a user I can open close the edit password dialog
     *
     * Given    I am Ada
     * And      I am logged in on the password workspace
     * And      I am editing a password I own
     * When     I click on the cancel button
     * Then     I do not see the edit password dialog
     * When     I reopen the edit password dialog
     * And      I click on the close dialog button (in the top right corner)
     * Then     I do not see the edit password dialog
     * When     I reopen the edit password dialog
     * And      I press the escape button
     * Then     I do not see the edit password dialog
     */
    public function testEditPasswordDialogOpenClose() {
        // Given I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // And I am editing a password I own
        $resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));

        $this->gotoEditPassword($resource['id']);

        // When I click on the cancel button
        $this->click('.edit-password-dialog .js-dialog-cancel');

        // Then I do not see the edit password dialog
        $this->assertNotVisible('.edit-password-dialog');

        // When I reopen the edit password dialog
        $this->click('js_wk_menu_edition_button');
        $this->waitCompletion();
        $this->assertVisible('.edit-password-dialog');

        // And I click on the close dialog button (in the top right corner)
        $this->click('.edit-password-dialog .dialog-close');

        // Then I do not see the edit password dialog
        $this->assertNotVisible('.edit-password-dialog');

        // When I reopen the edit password dialog
        $this->click('js_wk_menu_edition_button');
        $this->waitCompletion();
        $this->assertVisible('.edit-password-dialog');

        // And I press the escape button
        $this->pressEscape();

        // Then I do not see the edit password dialog
        $this->assertTrue($this->isNotVisible('.edit-password-dialog'));
    }

    /**
     * Scenario: As a user I can see the edit password dialog
     *
     * Given    I am Ada
     * And      I am logged on the password workspace
     * And      I am editing a password I own
     * Then     I can see the edit password dialog
     * And      I can see the title is set to "edit password"
     * And      I can see the name of the resource after the title
     * And      I can see the close dialog button
     * And      I can see the edit tab is selected
     * And      I can see the share tab is not selected
     * And      I can see the name input and label is marked as mandatory
     * And      I can see the resource name in the text input
     * And      I can see the url text input and label
     * And      I can see the resource url in the text input
     * And      I can see the username text input and label marked as mandatory
     * And      I can see the resource ursername in the text input
     * And      I can see the password iframe
     * And      I can see the iframe label
     * When     I switch to the password iframe
     * And      I can see the password input
     * And      I can not see the password in cleartext
     * And      I can see the security token
     * And      I can see the view password button
     * And      I can see the generate password button
     * And      I can see the complexity meter
     * And      I can see the complexity textual indicator
     * When     I switch back out of the password iframe
     * And      I can see the description textarea and label
     * And      I can see the resource description in the textarea
     * And      I can see the save button
     * And      I can see the cancel button
     */
    public function testEditPasswordDialogView() {
        // Given I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // And I am editing a password I own
        $resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));
        $this->gotoEditPassword($resource['id']);

        // Then I can see the edit password dialog
        $this->assertVisible('.edit-password-dialog');

        // And I can see the title is set to "edit password"
        $this->assertElementContainsText(
            $this->findByCss('.edit-password-dialog h2'),
            'Edit'
        );

        // And I can see the name of the resource after the title
        $this->assertElementContainsText(
            $this->findByCss('.edit-password-dialog h2 .dialog-header-subtitle'),
            $resource['name']
        );

        // And I can see the close dialog button
        $this->assertVisible('.edit-password-dialog .dialog-close');

        // And I can see the edit tab is selected
        $this->assertElementContainsText(
            $this->findByCss('.edit-password-dialog #js_tab_nav_js_rs_edit a.selected'),
            'Edit'
        );

        // And I can see the share tab is not selected
        $this->assertElementContainsText(
            $this->findByCss('.edit-password-dialog #js_tab_nav_js_rs_permission a'),
            'Share'
        );
        $this->assertNotVisible('.edit-password-dialog #js_tab_nav_js_rs_permission a.selected');

        // And I can see the name text input and label is marked as mandatory
        $this->assertVisible('.edit-password-dialog input[type=text]#js_field_name.required');
        $this->assertVisible('.edit-password-dialog label[for=js_field_name]');

        // And I can see the resource name in the text input
        $this->assertInputValue('js_field_name', $resource['name']);

        // And I can see the url text input and label
        $this->assertVisible('.edit-password-dialog input[type=text]#js_field_uri');
        $this->assertVisible('.edit-password-dialog label[for=js_field_uri]');

        // And  I can see the resource url in the text input
        $this->assertInputValue('js_field_uri', $resource['uri']);

        // And I can see the username field
        $this->assertVisible('.edit-password-dialog input[type=text]#js_field_username');
        $this->assertVisible('.edit-password-dialog label[for=js_field_username]');

        // And I can see the resource ursername in the text input
        $this->assertInputValue('js_field_username', $resource['username']);

        // And I can see the password iframe
        $this->assertVisible('.edit-password-dialog #passbolt-iframe-secret-edition');

        // And I can see the iframe label
        $this->assertVisible('.edit-password-dialog label[for=js_field_secret_data_0]');

        // When I switch to the password iframe
        $this->goIntoSecretIframe();

        // And I can see the password input
        $this->assertVisible('input[type=password]#js_secret');

        // And I can not see the password in cleartext
        $this->assertNotVisible('input[type=password]#js_secret_clear');
        $this->assertInputValue('js_secret_clear','');
        $this->assertInputValue('js_secret','');

        // And I can see the security token
        $this->assertSecurityToken($user, 'has_encrypted_secret');

        // And I can see the view password button
        $this->assertVisible('js_secret_view');
        // And I can see the generate password button
        $this->assertVisible('js_secret_generate');

        // And I can see the complexity meter
        // And I can see the complexity textual indicator
        $this->assertComplexity('not available');

        // When I switch back out of the password iframe
        $this->goOutOfIframe();

        // And I can see the description field and label
        $this->assertVisible('.edit-password-dialog textarea#js_field_description');
        $this->assertVisible('.edit-password-dialog label[for=js_field_description]');

        // And  I can see the resource description in the textarea
        $this->assertInputValue('js_field_description', $resource['description']);

        // And I can see the save button
        $this->assertVisible('.edit-password-dialog input[type=submit].button.primary');

        // And I can see the cancel button
        $this->assertVisible('.edit-password-dialog a.cancel');

    }

    /**
     * Scenario: As a user I can edit a password by using keyboard shortcuts only
     *
     * Given    I am Ada
     * And      I am logged in
     * And      I am on the edit password dialog
     * Then     I can see that the field name has the focus
     * When     I type a new name on the keyboard to modify the current one already selected
     * And      I press the tab key
     * Then     I should see that the field username has the focus
     * When     I press the tab key
     * Then     I should see that the field uri has the focus
     * When     I press the tab key
     * Then     I should see the master password dialog opening
     * When     I type the master password on keyboard (without clicking anywhere first)
     * And      I press enter
     * And      I wait for a few seconds
     * Then     I should see the password field populated with my password
     * and      I should see that the password field has the focus
     * When     I press the tab key
     * Then     I should see that the field description has the focus
     * When     I press tab
     * And      I press enter
     * Then     I should see a notice message saying that the password was edited succesfully
     * And      I see the password updated with the new name I just entered in my password list
     */
    public function testEditPasswordWithKeyboardShortcutAndView() {
        // Given I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // And I am editing a password I own
        $resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));
        $this->gotoEditPassword($resource['id']);

        // Then I can see the edit password dialog
        $this->assertVisible('.edit-password-dialog');

        // The field name should have the focus
        $this->assertElementHasFocus('js_field_name');

        // I type a new name using keyboard only
        $this->typeTextLikeAUser('keyboardupdate');

        // Press tab key.
        $this->pressTab();

        // Then the field uri should have the focus.
        $this-> assertElementHasFocus('js_field_uri');

        // Press tab key.
        $this->pressTab();

        // Then the field username should have the focus.
        $this-> assertElementHasFocus('js_field_username');

        // Press tab key.
        $this->pressTab();

        // Then I see the master password dialog
        // Given I can see the iframe
        $this->waitUntilISee('passbolt-iframe-master-password');

        // I type the master password using keyboard only
        $this->typeTextLikeAUser($user['MasterPassword']);

        // And I press enter
        $this->pressEnter();

        // Then the master password dialog should disappear
        $this->waitUntilIDontSee('passbolt-iframe-master-password');

        // Then I can see the password edit dialog
        $this->assertVisible('.edit-password-dialog');

        // The field password should have the focus (inside the iframe).
        $this->goIntoSecretIframe();
	    $this->waitUntilSecretIsDecryptedInField();
        $this-> assertElementHasFocus('js_secret');

        // Press tab key.
        $this->pressTab();
        $this->goOutOfIframe();

        // Then the field description should have the focus.
        $this-> assertElementHasFocus('js_field_description');

        // Press tab key.
        $this->pressTab();

        // Press enter.
        $this->pressEnter();

        // Then I can see a success notification
        $this->assertNotification('app_resources_edit_success');

        // And I can see that the password name have changed in the overview
        $this->assertElementContainsText('#js_wsp_pwd_browser .tableview-content', 'keyboardupdate');

        // Reset database.
        $this->resetDatabase();
    }

    /**
     * Scenario: As a user I can go to next / previous field in the edit password form by using the keyboard tabs
     *
     * Given    I am Ada
     * And      I am logged in
     * And      I am on the edit password dialog
     * Then     I can see that the field name has the focus
     * When     I press the tab key
     * Then     I should see that the field username has the focus
     * When     I press the tab key
     * Then     I should see that the field uri has the focus
     * When     I press the tab key
     * Then     I should see the master password dialog opening
     * When     I type the master password on keyboard (without clicking anywhere first)
     * And      I press enter
     * And      I wait for a few seconds
     * Then     I should see the password field populated with my password
     * And      I should see that the password field has the focus
     * When     I press the tab key
     * Then     I should see that the field description has the focus
     * When     I press backtab key
     * Then     I should see that the password field has the focus
     * When     I press the backtab key
     * Then     I should see that the uri field has the focus
     * When     I press the backtab key
     * Then     I should see that the username field has the focus
     * When     I press the backtab key
     * Then     I should see that the name field has the focus.
     */
    public function testEditPasswordKeyboardShortcuts() {
        // Given I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

        // And I am logged in
        $this->loginAs($user);

        // And I am editing a password I own
        $resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));
        $this->gotoEditPassword($resource['id']);

        // I should see that the field name has the focus.
        $this-> assertElementHasFocus('js_field_name');

        // Press tab key.
        $this->pressTab();

        // I should see that the field name has the focus.
        $this-> assertElementHasFocus('js_field_uri');

        // Press tab key.
        $this->pressTab();

        // I should see that the field name has the focus.
        $this-> assertElementHasFocus('js_field_username');

        // Press tab key.
        $this->pressTab();

        // Then I see the master password dialog
        // Given I can see the iframe
        $this->waitUntilISee('passbolt-iframe-master-password');

        // I type the master password using keyboard only
        $this->typeTextLikeAUser($user['MasterPassword']);

        // And I press enter
        $this->pressEnter();

        // Then the master password dialog should disappear
        $this->waitUntilIDontSee('passbolt-iframe-master-password');

        // The field password should have the focus (inside the iframe).
        $this->goIntoSecretIframe();
	    $this->waitUntilSecretIsDecryptedInField();
        $this-> assertElementHasFocus('js_secret');

        // Press tab key.
        $this->pressTab();
        $this->goOutOfIframe();

        // Then the field description should have the focus.
        $this-> assertElementHasFocus('js_field_description');

        // Press backtab.
        $this->pressBacktab();

        // The field password should have the focus (inside the iframe).
        $this->goIntoSecretIframe();
        $this-> assertElementHasFocus('js_secret');

        // Press tab key.
        // TODO (PASSBOLT-1295) : fix the below part of the test.
        // Backtab doesn't seem to be done properly. Tab is received by the plugin, but shiftKey in the event
        // is set to false.
        //$this->pressBacktab();
        $this->goOutOfIframe();

        // I should see that the field name has the focus.
//        $this-> assertElementHasFocus('js_field_username');
//
//        // Press backtab key.
//        $this->pressBacktab();
//
//        // I should see that the field name has the focus.
//        $this-> assertElementHasFocus('js_field_uri');
//
//        // Press backtab key.
//        $this->pressBacktab();
//
//        // I should see that the field name has the focus.
//        $this-> assertElementHasFocus('js_field_name');
    }

	/**
	 * Scenario: As a user I should be notified I will lose my changes on the edit password dialog after editing a field
	 *
	 * Given    I am Ada
	 * And      I am logged in on the password workspace
	 * And      I am editing a password I own
	 * When     I click on name input text field
	 * And      I empty the name input text field value
	 * And      I switch to the share screen
	 * Then     I should see a confirmation dialog notifying me regarding the changes I'm going to lose
	 *
	 * When     I click cancel in confirmation dialog
	 * Then		I should stay on the edit dialog
	 * 
	 * When     I switch to the share screen
	 * And		I click ok in confirmation dialog
	 * Then     I should leave the edit dialog for the share dialog
	 */
	public function testEditPasswordLoseChanges() {
		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);

		// And I am logged in on the password workspace
		$this->loginAs($user);

		// And I am editing a password I own
		$resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));
		$this->gotoEditPassword($resource['id']);

		// When I click on name input text field
		$this->click('js_field_name');

		// And I change the name input text field value
		$newname = 'New password name';
		$this->inputText('js_field_name', $newname);

		// And I switch to the share screen
		$this->findByCss('#js_tab_nav_js_rs_permission a')->click();

		// Then I should see a confirmation dialog notifying me regarding the changes I'm going to lose
		$this->assertConfirmationDialog('Do you really want to leave ?');

		// When I click cancel in confirmation dialog
		$this->cancelActionInConfirmationDialog();

		// Then	I should stay on the edit dialog
		$this->assertVisible('#js_rs_edit');

	 	// When	I switch to the share dialog
		$this->findByCss('#js_tab_nav_js_rs_permission a')->click();
		$this->assertConfirmationDialog('Do you really want to leave ?');

	 	// And I click ok in confirmation dialog
		$this->confirmActionInConfirmationDialog();

		// Then I should leave the edit dialog for the share dialog
		$this->assertVisible('#js_rs_permission');
	}

	/**
	 * Scenario: As a user I should be notified I will lose my changes on the edit password dialog after editing the secret
	 *
	 * Given    I am Ada
	 * And      I am logged in on the password workspace
	 * And      I am editing a password I own
	 * When     I edit the secret
	 * And      I switch to the share screen
	 * Then     I should see a confirmation dialog notifying me regarding the changes I'm going to lose
	 *
	 * When     I click cancel in confirmation dialog
	 * Then		I should stay on the edit dialog
	 *
	 * When     I switch to the share screen
	 * And		I click ok in confirmation dialog
	 * Then     I should leave the edit dialog for the share dialog
	 */
	public function testEditPasswordSecretLoseChanges() {
		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);

		// And I am logged in on the password workspace
		$this->loginAs($user);

		// And I am editing a password I own
		$resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));
		$this->gotoEditPassword($resource['id']);

		// When edit the secret
		$this->goIntoSecretIframe();
		$this->click('js_secret');
		$this->goOutOfIframe();
		$this->assertMasterPasswordDialog($user);
		$this->enterMasterPassword($user['MasterPassword']);
		$this->waitUntilIDontSee('passbolt-iframe-master-password');

		// Wait until the password is decrypted and displayed in the field.
		$this->goIntoSecretIframe();
		$this->waitUntilSecretIsDecryptedInField();
		$this->goOutOfIframe();

		$this->assertVisible('.edit-password-dialog');
		$this->inputSecret('My new password');

		// And I switch to the share screen
		$this->findByCss('#js_tab_nav_js_rs_permission a')->click();

		// Then I should see a confirmation dialog notifying me regarding the changes I'm going to lose
		$this->assertConfirmationDialog('Do you really want to leave ?');

		// When I click cancel in confirmation dialog
		$this->cancelActionInConfirmationDialog();

		// Then	I should stay on the edit dialog
		$this->assertVisible('#js_rs_edit');

		// When	I switch to the share dialog
		$this->findByCss('#js_tab_nav_js_rs_permission a')->click();
		$this->assertConfirmationDialog('Do you really want to leave ?');

		// And I click ok in confirmation dialog
		$this->confirmActionInConfirmationDialog();

		// Then I should leave the edit dialog for the share dialog
		$this->assertVisible('#js_rs_permission');
	}

    /**
     * Scenario: As a user I can edit the name of a password I have own
     * Regression: PASSBOLT-1038
     *
     * Given    I am Ada
     * And      I am logged in on the password workspace
     * And      I am editing a password I own
     * When     I click on name input text field
     * And      I empty the name input text field value
     * And      I enter a new value
     * And      I click save
     * Then     I can see a success notification
     * And      I can see that the password name have changed in the overview
     * And      I can see the new name value in the sidebar
     * When     I click edit button
     * Then     I can see the new name in the edit password dialog
     */
    public function testEditPasswordName() {
        // Given I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // And I am editing a password I own
        $resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));
        $this->gotoEditPassword($resource['id']);

        // When I click on name input text field
        $this->click('js_field_name');

        // And I empty the name input text field value
        // And I enter a new value
        $newname = 'New password name';
        $this->inputText('js_field_name',$newname);

        // And I click save
        $this->click('.edit-password-dialog input[type=submit]');

        // Then I can see a success notification
        $this->assertNotification('app_resources_edit_success');

        // And I can see that the password name have changed in the overview
        $this->assertElementContainsText('#js_wsp_pwd_browser .tableview-content', $newname);

        // And I can see the new name value in the sidebar
        $this->assertVisible('#js_pwd_details.panel.aside');
        $this->assertElementContainsText('js_pwd_details', $newname);

        // When I click edit button
        $this->click('js_wk_menu_edition_button');

        // Then I can see the new name in the edit password dialog
        $this->assertInputValue('js_field_name', $newname);

        // Since content was edited, we reset the database
        $this->resetDatabase();
    }

    /**
     * Scenario: As a user I can edit the description of a password I have own
     *
     * Given    I am Ada
     * And      I am logged in on the password workspace
     * And      I am editing the description of a password I own
     * Then     I can see the success notification
     * And      I can see the new description in the sidebar
     * When     I click edit button
     * Then     I can see the new description in the edit password dialog
     */
    public function testEditPasswordDescription() {
        // Given I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // And I am editing the description of a password I own
        // Then I can see a success notification
        $resource = Resource::get(array(
            'user' => 'ada',
            'permission' => 'owner'
        ));
        $r['id'] = $resource['id'];
        $r['description'] = 'this is a new description';
        $this->editPassword($r);

        // And I can see the new description in the sidebar
        $this->assertElementContainsText('#js_pwd_details .description_content', $r['description']);

        // When I click edit button
        $this->click('js_wk_menu_edition_button');

        // Then I can see the new description in the edit password dialog
        $this->assertInputValue('js_field_description', $r['description']);

        // Since content was edited, we reset the database
        $this->resetDatabase();
    }

    /**
     * Scenario: As a user I can edit the uri of a password I have own
     *
     * Given    I am Ada
     * And      I am logged in on the password workspace
     * And      I am editing the uri of a password I own
     * Then     I can see the success notification
     * And      I can see the new uri in the sidebar
     * When     I click edit button
     * Then     I can see the new uri in the edit password dialog
     */
    public function testEditPasswordUri() {
        // Given I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // And I am editing the uri of a password I own
        // Then I can see a success notification
        $resource = Resource::get(array(
            'user' => 'ada',
            'permission' => 'owner'
        ));
        $r['id'] = $resource['id'];
        $r['uri'] = 'https://newurl.com/checkonetwo';
        $this->editPassword($r);

        // And I can see that the password url have changed in the overview
        $this->assertElementContainsText('#js_wsp_pwd_browser .tableview-content', $r['uri']);

        // And I can see the new uri in the sidebar
        $this->assertElementContainsText('#js_pwd_details .uri', $r['uri']);

        // When I click edit button
        $this->click('js_wk_menu_edition_button');

        // Then I can see the new uri in the edit password dialog
        $this->assertInputValue('js_field_uri', $r['uri']);

        // Since content was edited, we reset the database
        $this->resetDatabase();
    }

    /**
     * Scenario: As a user I can edit the secret of a password I have own
     *
     * Given    I am Ada
     * And      I am logged in on the password workspace
     * And      I am editing a password I own
     * When     I click on the secret password field
     * Then     I see the master password dialog
     * When     I enter the master password and click submit
     * Then     I can see the password edit dialog
     * When     I enter a new password
     * And      I press the submit button
     * Then     I can see the encryption in progress dialog
     * Then     I can see the success notification
     * When     I copy the password to clipboard
     * Then     I can see that password have been updated
     */
    public function testEditPasswordSecret() {
        // Given I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // And I am editing a password I own
        $r1 = Resource::get(array(
            'user' => 'ada',
            'permission' => 'owner'
        ));
        $r2 = array(
            'id' => $r1['id'],
            'password' => 'our_brand_new_password'
        );
        $this->gotoEditPassword($r1['id']);

        // When I click on the secret password field
        $this->goIntoSecretIframe();
        $this->click('js_secret');
        $this->goOutOfIframe();

        // Then I see the master password dialog
        $this->assertMasterPasswordDialog($user);

        // When I enter the master password and click submit
        $this->enterMasterPassword($user['MasterPassword']);

	    $this->waitUntilIDontSee('passbolt-iframe-master-password');

	    $this->goIntoSecretIframe();
	    $this->waitUntilSecretIsDecryptedInField();
	    $this->goOutOfIframe();

        // Then I can see the password edit dialog
        $this->assertVisible('.edit-password-dialog');

        // When I enter a new password
        $this->inputSecret($r2['password']);

        // And I press the submit button
        $this->click('.edit-password-dialog input[type=submit]');

        // Then I can see the encryption in progress dialog
        $this->waitUntilISee('passbolt-iframe-progress-dialog');

	    // Then wait until I don't see  the encryption dialog anymore.
	    $this->waitUntilIDontSee('passbolt-iframe-progress-dialog');

        // Then I can see the success notification
        $this->assertNotification('app_resources_edit_success');

        // When I copy the password to clipboard
        $this->copyToClipboard($r2, $user);

        // Then I can see that password have been updated
        $this->assertClipboard($r2['password']);

        // Since content was edited, we reset the database
        $this->resetDatabase();
    }

    /**
     * Scenario: As a user editing my password I can use the button to view my secret in clear text
     *
     * Given    I am Ada
     * And      I am logged in on the password workspace
     * And      I am editing a password I own
     * When     I click the button to view my password in clear text
     * Then     I see the master password dialog
     * When     I enter the master password in the input field
     * And      I press the submit button
     * Then     I can see the password in clear text
     * When     I press the same button to hide my password again
     * Then     I do not see the password in clear text
     * When     I press the button to view my password in cleartext
     * Then     I do not the master password dialog
     * Then     I can see the password in clear text
     */
    public function testEditPasswordViewClearText() {
        // Given I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // And I am editing a password I own
        $r1 = Resource::get(array(
            'user' => 'ada',
            'permission' => 'owner'
        ));
        $this->gotoEditPassword($r1['id']);

        // When I click the button to view my password in clear text
        $this->goIntoSecretIframe();
        $this->click('js_secret_view');

        // Then I see the master password dialog
        $this->assertMasterPasswordDialog($user);

        // When I enter the master password and I press the submit button
        $this->enterMasterPassword($user['MasterPassword']);

	    // Wait until I don't see the master password window anymore.
	    $this->waitUntilIDontSee('passbolt-iframe-master-password');

        // Then I should see the input field with the password in clear text
        $this->goIntoSecretIframe();
	    $this->waitUntilSecretIsDecryptedInField();
        $this->assertNotVisible('js_secret');
        $this->assertVisible('js_secret_clear');
        $this->assertTrue($this->findById('js_secret_clear')->getAttribute('value') == $r1['password']);

        // When I press the same button to hide my password again
        $this->click('js_secret_view');

        // Then I should not see the input field with the password in clear text
        $this->assertNotVisible('js_secret_clear');

        // When I press the button to view my password in cleartext
        $this->click('js_secret_view');

        // Then I do not the master password dialog
        $this->assertNotVisible('passbolt-iframe-master-password');

        // Then I can see the password in clear text
        $this->assertVisible('js_secret_clear');

	    $this->goOutOfIframe();
    }

    /**
     * Scenario: As a user editing my password I can generate a new random password automatically
     *
     * Given    I am Ada
     * And      I am logged in on the password workspace
     * And      I am editing a password I own
	 * And      I can see the generate button is not active
     * When     I click on the secret password field
     * Then     I see the master password dialog
     * And      I enter the master password in the input field and press the submit button
     * And      I can see the secret field populated
     * And      I can see the generate button is now active
     * When     I click the button the generate a new random password button
     * And      I click the button to view my password in clear text
     * Then     I can see the secret is different than the previous one
     * And      I can see that the password complexity is set to fair
     */
    public function testEditPasswordGenerateRandom() {
	    // TODO : fix corresponding feature in plugin. #PASSBOLT-1060
        // Given I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // And I am editing a password I own
        $r1 = Resource::get(array(
            'user' => 'ada',
            'permission' => 'owner'
        ));
        $this->gotoEditPassword($r1['id']);
	    $this->goIntoSecretIframe();

		// And I can see the generate button is not active
	    $this->assertDisabled('js_secret_generate');

	    // When I click on the secret password field
	    $this->click('js_secret');

        // Then I see the master password dialog
        $this->assertMasterPasswordDialog($user);

        // When I enter the master password in the input field
        $this->enterMasterPassword($user['MasterPassword']);

	    // Wait until I don't see the master password window anymore.
	    $this->waitUntilIDontSee('passbolt-iframe-master-password');

        // Then I should see the secret field populated
        $this->goIntoSecretIframe();

	    $this->waitUntilSecretIsDecryptedInField();

	    $s = $this->findById('js_secret')->getAttribute('value');
	    $this->assertNotEmpty($s);

	    // When I click the button to generate a new random password automatically
	    $this->click('js_secret_generate');
	    $s = $this->findById('js_secret')->getAttribute('value');

        // And I click the button to view my password in clear text
        $this->click('js_secret_view');

        // Then I can see the secret is different than the previous one
        $this->assertTrue(($s != $r1['password']));

        // And I should see that the password complexity is set to fair
        $this->assertTrue(strlen($s) == SystemDefaults::$AUTO_PASSWORD_LENGTH);
        $this->assertComplexity(SystemDefaults::$AUTO_PASSWORD_STRENGTH);

    }

    /**
     * Scenario: As a user I can not edit a password I have only read access to
     *
     * Given    I am Ada
     * And      I am logged in on the password workspace
     * And      I click on a password I cannot edit
     * Then     I can see the edit button is not active
     * When     I right click on a password I cannot edit
     * Then     I can see the contextual menu
     * And      I can see the edit option is disabled
     */
    public function testEditPasswordNoRightNoEdit() {
        // Given I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // And I click on a password I cannot edit
        $r = Resource::get(array(
            'user' => 'ada',
            'permission' => 'read'
        ));
        $this->clickPassword($r['id']);

        // Then I can see the edit button is not active
        $this->assertDisabled('js_wk_menu_edition_button');

        // When I right click on a password I cannot edit
        $this->rightClickPassword($r['id']);

        // Then I can see the contextual menu
        $this->findById('js_contextual_menu');

        // And I can see the edit option is disabled
	    $this->click('#js_password_browser_menu_edit a');
        $this->assertNotVisible('.edit-password-dialog');
    }

    /**
     * Scenario: As user B I can see the changes are reflected when user A is editing a password we share
     *
     * Given    I am Ada
     * And      I am logged in on the password workspace
     * And      I edit a password that I share with betty
     * And      I logout
     * And      I am Betty
     * And      I am logged in on the password workspace
     * And      I copy the password Ada edited to clipboard
     * Then     I can see the new password
     */
    public function testEditPasswordUserAEditUserBCanSee() {
        // Given I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // And I edit a password that I share with betty
        $r1 = Resource::get(array(
            'user' => 'betty',
            'permission' => 'update'
        ));
        $r2 = array(
            'id' => $r1['id'],
            'password' => 'our_brand_new_password'
        );
        $this->editPassword($r2, $user);

        // And I logout
        $this->getUrl('logout');

        // And I am Betty
        $user = User::get('betty');
        $this->setClientConfig($user);

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // And I copy the password Ada edited to clipboard
        $this->copyToClipboard($r1, $user);

        // Then I can see the new password
        $this->assertClipboard($r2['password']);

        // Since content was edited, we reset the database
        $this->resetDatabase();
    }

    /**
     * Scenario: As a user I can see error messages when editing a password with wrong inputs
     *
     * Given    I am Ada
     * And      I am logged in on the password workspace
     * And      I edit a password I own
     * When     I click on the name input field
     * And      I empty the name input field
     * And      I empty the username input field
     * And      I press enter
     * Then     I see an error message saying that the name is required
     * Then     I don't see an error message saying that the username is required
     * When     I enter & as a name
     * And      I enter & as a username
     * And      I enter & as a url
     * And      I enter & as a description
     * And      I click save
     * Then     I see an error message saying that the name contain invalid characters
     * Then     I see an error message saying that the username contain invalid characters
     * Then     I see an error message saying that the url is not valid
     * Then     I see an error message saying that the description contain invalid characters
     */
    public function testEditPasswordErrorMessages() {
        // Given I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // And I edit a password I own
        $r1 = Resource::get(array(
            'user' => 'ada',
            'permission' => 'owner'
        ));
        $this->gotoEditPassword($r1['id']);

        // And I empty the name input field
	    $this->emptyFieldLikeAUser('js_field_name');

        // And I empty the username input field
	    $this->emptyFieldLikeAUser('js_field_username');

        // And I press enter
        $this->pressEnter();

        // Then I see an error message saying that the name is required
        $this->assertVisible('#js_field_name_feedback.error.message');
        $this->assertElementContainsText(
            $this->find('js_field_name_feedback'), 'is required'
        );

        // Then I see an error message saying that the username is required
        $this->assertNotVisible('#js_field_username_feedback.error.message');

        // When I enter & as a name
        $this->inputText('js_field_name', '&');

        // And I enter & as a username
        $this->inputText('js_field_username', '&');

        // And I enter & as a url
        $this->inputText('js_field_uri', '&');

        // And I enter & as a description
        $this->inputText('js_field_description', '&');

        // And I click save
        $this->click('.edit-password-dialog input[type=submit]');

        // Then I see an error message saying that the name contain invalid characters
        $this->assertVisible('#js_field_name_feedback.error.message');
        $this->assertElementContainsText(
            $this->find('js_field_name_feedback'), 'should only contain alphabets, numbers'
        );

        // Then I see an error message saying that the username contain invalid characters
        $this->assertVisible('#js_field_username_feedback.error.message');
        $this->assertElementContainsText(
            $this->find('js_field_username_feedback'), 'should only contain alphabets, numbers'
        );

        // Then I see an error message saying that the url is not valid
        $this->assertVisible('#js_field_uri_feedback.error.message');
        $this->assertElementContainsText(
            $this->find('js_field_uri_feedback'), 'should only contain alphabets, numbers'
        );

        // Then I see an error message saying that the description contain invalid characters
        $this->assertVisible('#js_field_description_feedback.error.message');
        $this->assertElementContainsText(
            $this->find('js_field_description_feedback'), 'should only contain alphabets, numbers'
        );
    }
}


