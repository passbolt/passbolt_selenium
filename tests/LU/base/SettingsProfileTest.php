<?php
/**
 * Feature : Settings Workspace, keys section
 *
 * - As a LU I should be able to see my profile information in the profile section
 * - As a LU I should be able to see and use the breadcrumb of the profile section.
 * - As LU, I should be able to edit my avatar picture.
 * - As LU, I shouldn't be able to upload a wrong file format as my avatar picture
 * - As LU, I should be able to edit my profile and see the editable fields.
 * - As LU I can see validation error messages while editing my profile information.
 * - As LU I can edit my own last name.
 * - As LU I can edit my own first name.
 *
 * @copyright (c) 2015-present Bolt Softwares Pvt Ltd
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */

class SettingsProfileTest extends PassboltTestCase {

	/**
	 * Scenario :   As a LU I should be able to see my profile information in the profile section
	 * Given        I am logged in as a LU in the settings workspace, profile section
	 * Then         I should see the title Profile
	 * And          I should see my name in the profile information
	 * And          I should see my email in the profile information
	 * And          I should see my role in the profile information
	 * And          I should see my public key id in the profile information
	 * And          I should see my profile creation date in the profile information
	 * And          I should see my profile modification date in the profile information
	 * And          I should see my picture in the profile information
	 * And          I should see my picture in the drop down menu at the top
	 *
	 * @throws Exception
	 */
	public function testSettingsProfile() {
		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);


		// And I am logged in on the user workspace
		$this->loginAs($user);

		// Go to user workspace
		$this->gotoWorkspace('settings');

		// I should see a section with a title Profile
		$this->waitUntilISee('.js_tabs_content h3', '/Profile/');

		// I should see the name of the user in the table info
		$this->assertElementContainsText(
			$this->find('.table-info.profile .name'),
			'Ada Lovelace'
		);

		// I should see the email of the user in the table info
		$this->assertElementContainsText(
			$this->find('.table-info.profile .email'),
			'ada@passbolt.com'
		);

		// I should see the email of the user in the table info
		$this->assertElementContainsText(
			$this->find('.table-info.profile .role'),
			'user'
		);

		// I should see the public key id
		$this->assertElementContainsText(
			$this->find('.table-info.profile .publickey_keyid'),
			'5D9B054F'
		);

		// And I should the created date in the ago format
		$this->assertElementContainsText(
			$this->find('.table-info.profile .created'),
			'/(a|[0-9]{1,2}) (minute|minutes) ago/'
		);

		// And I should see the correct modified date in the ago format
		$this->assertElementContainsText(
			$this->find('.table-info.profile .modified'),
			'/(a|[0-9]{1,2}) (minute|minutes) ago/'
		);

		// I should see the picture of Ada.
		$actualImage =  $this->find('.avatar img')->getAttribute('src');
		$expectedImage = IMG_FIXTURES . '/avatar/ada.png';
		$this->assertImagesAreSame($actualImage, $expectedImage);

		// And the picture of Ada should be the same as in the scroll down menu at the top of the page.
		$topProfileImage =  $this->find('#js_app_profile_dropdown .picture img')->getAttribute('src');
		$this->assertImagesAreSame($actualImage, $topProfileImage);
	}

	/**
	 * Scenario :   As a LU I should be able to see and use the breadcrumb of the profile section.
	 * Given        I am logged in as LU in the settings workspace, profile section.
	 * Then         I should see a breadcrumb
	 * And          I should see a breadcrumb section containing All users
	 * And          I should see a breadcrumb section containing my name
	 * And          I should see a breadcrumb section containing profile
	 * When         I click All users
	 * Then         I should be on the users workspace
	 *
	 * @throws Exception
	 */
	public function testSettingsProfileBreadcrumb() {
		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);


		// And I am logged in on the user workspace
		$this->loginAs($user);

		// Go to user workspace
		$this->gotoWorkspace('settings');

		// I should see the breadcrumb.
		$this->assertVisible('js_wsp_settings_breadcrumb');

		// I should see an element containing All users in the breadcrumb.
		$this->assertElementContainsText(
			$this->find('#js_wsp_settings_breadcrumb'),
			'/All users/'
		);

		// And I should see an element containing Ada Lovelace in the breadcrumb.
		$this->assertElementContainsText(
			$this->find('#js_wsp_settings_breadcrumb'),
			'/Ada Lovelace/'
		);

		// And I should see an element containing Profile in the breadcrumb.
		$this->assertElementContainsText(
			$this->find('#js_wsp_settings_breadcrumb'),
			'/Profile/'
		);

		// I click on All users.
		$this->clickLink('All users');

		// I should be on the users workspace.
		$this->waitUntilISee('#container.page.people');
	}

	/**
	 * Scenario :   As LU, I should be able to edit my avatar picture.
	 * Given        I am logged in as LU in the settings workspace, profile section.
	 * When         I click on upload a new picture
	 * Then         I should see a dialog window where I can select a file to upload
	 * When         I click on the close button
	 * Then         I should'nt see the dialog anymore
	 * When         I click on upload a new picture
	 * And          I click on the cancel button in the dialog
	 * Then         I should'nt see the dialog anymore
	 * When         I click on upload a new picture
	 * And          I select a file to upload from the dialog
	 * And          I click on save
	 * Then         I should see a success message
	 * And          I should see the selected photo in the profile details
	 * And          I should see the selected photo in the profile drop down
	 *
	 * @throws Exception
	 */
	public function testSettingsProfileAvatarEditOk() {
		// Given I am Ada
		$user = User::get( 'ada' );
		$this->setClientConfig( $user );

		// And I am logged in on the user workspace
		$this->loginAs( $user );

		// Go to user workspace
		$this->gotoWorkspace( 'settings' );

		// I click on the link Click here to upload a new picture.
		$this->click('.section.profile-detailed-information a.edit-avatar-action');

		// I should see a dialog with title "Edit Avatar".
		$this->waitUntilISee('.dialog', '/Edit Avatar/');

		// Close the dialog through the close button.
		$this->click('.dialog a.dialog-close');

		// I cannot see the dialog anymore.
		$this->assertNotVisible('.dialog');

		// I click on the link Click here to upload a new picture.
		$this->click('.section.profile-detailed-information a.edit-avatar-action');

		// I should see a dialog with title "Edit Avatar".
		$this->waitUntilISee('.dialog', '/Edit Avatar/');

		// I click on the cancel button.
		$this->click('.dialog a.cancel');

		// I cannot see the dialog anymore.
		$this->assertNotVisible('.dialog');

		// I click again on the link Click here to upload a new picture.
		$this->click('.section.profile-detailed-information a.edit-avatar-action');

		// I should see a dialog with title "Edit Avatar".
		$this->waitUntilISee('.dialog', '/Edit Avatar/');

		// I upload Betty's photo.
		$bettyImage = 'betty.png';
		$filebox = $this->find('js_field_avatar');
		$filebox->sendKeys(SELENIUM_IMG_FIXTURES . DS . 'avatar' . DS . $bettyImage);
		$this->click('.dialog input[type=submit]');

		// Then I should see a success message.
		$this->assertNotification('app_users_editavatar_success');

		// And I should see that the profile picture has been replaced in the profile details.
		$actualImage =  $this->find('.avatar img')->getAttribute('src');
		$expectedImage = IMG_FIXTURES . '/avatar/' . $bettyImage;
		$this->assertImagesAreSame($actualImage, $expectedImage);

		// And I should see that the profile picture has been replaced in the profile drop down.
		$topProfileImage =  $this->find('#js_app_profile_dropdown .picture img')->getAttribute('src');
		$this->assertImagesAreSame($actualImage, $topProfileImage);

		// When I Refresh the window.
		$this->refresh();

		// Then I should see that the profile drop down image is still there.
		$this->assertImagesAreSame($actualImage, $topProfileImage);

		// Database has been modified so we reset.
		$this->resetDatabase();
	}

	/**
	 * Scenario :   As LU, I shouldn't be able to upload a wrong file format as my avatar picture
	 * Given        I am logged in as LU in the settings workspace, profile section.
	 * When         I click on upload a new picture
	 * Then         I should see a dialog window where I can select a file to upload
	 * When         I click on upload a new picture
	 * And          I select a file to upload from the dialog, with a wrong file type (.xpi, .pdf)
	 * And          I click on save
	 * Then         I should see an error message
	 * And          I should see that the photo in the profile details is still the same as before
	 * And          I should see that the photo in the profile dropdown is still the same as before
	 *
	 * @throws Exception
	 */
	public function testSettingsProfileAvatarEditErrorFileType() {
		$this->resetDatabase();
		// Given I am Ada
		$user = User::get( 'ada' );
		$this->setClientConfig( $user );

		// And I am logged in on the user workspace
		$this->loginAs( $user );

		// Go to user workspace
		$this->gotoWorkspace( 'settings' );

		// I click on the link Click here to upload a new picture.
		$this->click('.section.profile-detailed-information a.edit-avatar-action');

		// I should see a dialog with title "Edit Avatar".
		$this->waitUntilISee('.dialog', '/Edit Avatar/');

		// I upload Betty's photo.
		$filebox = $this->find('js_field_avatar');
		$extensionFullUrl = SELENIUM_ROOT . $this->_browser['extensions'][0];

		$filebox->sendKeys($extensionFullUrl);
		$this->click('.dialog input[type=submit]');

		// Then I should see a success message.
		$this->assertNotification('app_users_editavatar_error');

		// And I should see that the profile picture has been replaced in the profile details.
		$adaImage = 'ada.png';
		$actualImage =  $this->find('.avatar img')->getAttribute('src');
		$expectedImage = IMG_FIXTURES . '/avatar/' . $adaImage;
		$this->assertImagesAreSame($actualImage, $expectedImage);

		// And I should see that the profile picture has been replaced in the profile drop down.
		$topProfileImage =  $this->find('#js_app_profile_dropdown .picture img')->getAttribute('src');
		$this->assertImagesAreSame($actualImage, $topProfileImage);

		// When I Refresh the window.
		$this->refresh();

		// Then I should see that the profile drop down image is still there.
		$this->assertImagesAreSame($actualImage, $topProfileImage);

		// Database has been modified so we reset.
		$this->resetDatabase();
	}

	/**
	 * Scenario :   As LU, I should be able to edit my profile and see the editable fields.
	 *
	 * Given    I am logged in as LU in the settings workspace, profile section.
	 * And      I click on the edit button
	 * Then     I can see the edit profile dialog
	 * And      I can see the title is set to "edit profile"
	 * And      I can see the close dialog button
	 * And      I can see the first name input and label is marked as mandatory
	 * And      I can see the user first name in the text input
	 * And      I can see the last name input and label is marked as mandatory
	 * And      I can see the user last name in the text input
	 * And      I can see the username text input and label marked as mandatory
	 * And      I can see the username text input is disabled
	 * And      I can see the user username in the text input
	 * And      I can see the save button
	 * And      I can see the cancel button
	 */
	public function testSettingsProfileUpdateView() {
		// Given I am Ada
		$user = User::get( 'ada' );
		$this->setClientConfig( $user );

		// And I am logged in on the user workspace
		$this->loginAs( $user );

		// Go to user workspace
		$this->gotoWorkspace( 'settings' );

		// Click on edit button.
		$this->click('js_settings_wk_menu_edition_button');

		// I should see a dialog with title "Edit User".
		$this->waitUntilISee('.dialog', '/Edit profile/');

		// And I can see the first name text input and label is marked as mandatory
		$this->assertVisible('.edit-profile-dialog input[type=text]#js_field_first_name.required');
		$this->assertVisible('.edit-profile-dialog label[for=js_field_first_name]');

		// And I can see the user first name in the text input
		$this->assertInputValue('js_field_first_name', $user['FirstName']);

		// And I can see the last name text input and label is marked as mandatory
		$this->assertVisible('.edit-profile-dialog input[type=text]#js_field_last_name.required');
		$this->assertVisible('.edit-profile-dialog label[for=js_field_last_name]');

		// And I can see the user last name in the text input
		$this->assertInputValue('js_field_last_name', $user['LastName']);

		// And I can see the last name text input and label is marked as mandatory
		$this->assertVisible('.edit-profile-dialog input[type=text]#js_field_username.required');
		$this->assertVisible('.edit-profile-dialog input[type=text][disabled]#js_field_username');
		$this->assertVisible('.edit-profile-dialog label[for=js_field_username]');

		// Assert I can't see the field role
		$this->assertNotVisible('.edit-profile-dialog #js_field_role_id');
		$this->assertNotVisible('.edit-profile-dialog #js_field_role_id input[type=checkbox]');

		// And I can see the user last name in the text input
		$this->assertInputValue('js_field_username', $user['Username']);

		// And I can see the save button
		$this->assertVisible('.edit-profile-dialog input[type=submit].button.primary');

		// And I can see the cancel button
		$this->assertVisible('.edit-profile-dialog a.cancel');
	}

	/**
	 * Scenario: As LU I can see validation error messages while editing my profile information
	 *
	 * Given    I am logged in as LU in the settings workspace, profile section.
	 * And      I click on the edit button
	 * Then     I can see the edit profile dialog
	 * And      I empty the field first name
	 * And      I empty the field last name
	 * When     I press the enter key on the keyboard
	 * Then     I see an error message saying that the first name is required
	 * And      I see an error message saying that the last name is required
	 * When     I enter '&' as a first name
	 * And      I enter '&' as a last name
	 * And      I click on the save button
	 * Then     I see an error message saying that the first name contain invalid characters
	 * And      I see an error message saying that the last name contain invalid characters
	 * When     I enter 'aa' as a first name
	 * And      I enter 'aa' as a last name
	 * Then     I see an error message saying that the length of first name should be between x and x characters
	 * And      I see an error message saying that the length of last name should be between x and x characters
	 */
	public function testSettingsProfileUpdateCanSeeErrors() {
		// Given I am Ada
		$user = User::get( 'ada' );
		$this->setClientConfig( $user );

		// And I am logged in on the user workspace
		$this->loginAs( $user );

		// Go to user workspace
		$this->gotoWorkspace( 'settings' );

		// Click on edit button.
		$this->click('js_settings_wk_menu_edition_button');

		// I should see a dialog with title "Edit User".
		$this->waitUntilISee('.dialog', '/Edit profile/');

		// And I empty the first name input field
		$this->emptyFieldLikeAUser('js_field_first_name');

		// And I empty the first name input field
		$this->emptyFieldLikeAUser('js_field_last_name');

		// And I press enter
		$this->pressEnter();

		// Then I see an error message saying that the first name is required
		$this->assertVisible('#js_field_first_name_feedback.error.message');
		$this->assertElementContainsText(
		  $this->find('js_field_first_name_feedback'), 'is required'
		);

		// Then I see an error message saying that the last name is required
		$this->assertVisible('#js_field_last_name_feedback.error.message');
		$this->assertElementContainsText(
		  $this->find('js_field_last_name_feedback'), 'is required'
		);

		// When I enter & as a first name
		$this->inputText('js_field_first_name', '&');

		// When I enter & as a last name
		$this->inputText('js_field_last_name', '&');

		// And I click save
		$this->click('.edit-profile-dialog input[type=submit]');

		// Then I see an error message saying that the first name contain invalid characters
		$this->assertVisible('#js_field_first_name_feedback.error.message');
		$this->assertElementContainsText(
		  $this->find('js_field_first_name_feedback'), 'should only contain alphabets'
		);

		// Then I see an error message saying that the first name contain invalid characters
		$this->assertVisible('#js_field_last_name_feedback.error.message');
		$this->assertElementContainsText(
		  $this->find('js_field_last_name_feedback'), 'should only contain alphabets'
		);

		// And I enter aa as a first name
		$this->inputText('js_field_first_name', 'aa');
		$this->assertVisible('#js_field_first_name_feedback.error.message');
		$this->assertElementContainsText(
		  $this->find('js_field_first_name_feedback'), 'First name should be between'
		);

		// And I enter aa as a last name
		$this->inputText('js_field_last_name', 'aa');
		$this->assertVisible('#js_field_last_name_feedback.error.message');
		$this->assertElementContainsText(
		  $this->find('js_field_last_name_feedback'), 'Last name should be between'
		);
	}

	/**
	 * Scenario: As LU I can edit my own first name
	 *
	 * Given    I am logged in as LU in the settings workspace, profile section.
	 * And      I click on the edit button
	 * Then     I can see the edit profile dialog
	 * When     I click on first name input text field
	 * And      I empty the first name input text field value
	 * And      I enter a new value
	 * And      I click save
	 * Then     I can see a success notification
	 * And      I can see that the user first name has changed in the profile details
	 * When 	I refresh
	 * And 		I go to the settings workspace, profile section
	 * Then		I can see the new first name in my name
	 *
	 */
	public function testSettingsProfileUpdateEditFirstName() {
		// Given I am Ada
		$user = User::get( 'ada' );
		$this->setClientConfig( $user );

		// And I am logged in on the user workspace
		$this->loginAs( $user );

		// Go to settings workspace
		$this->gotoWorkspace( 'settings' );

		// Click on edit button.
		$this->click('js_settings_wk_menu_edition_button');

		// I should see a dialog with title "Edit User".
		$this->waitUntilISee('.dialog', '/Edit profile/');

		// When I click on name input text field
		$this->click('js_field_first_name');

		// And I empty the name input text field value
		// And I enter a new value
		$newname = 'modifiedfirstname';
		$this->inputText('js_field_first_name', $newname);

		// And I click save
		$this->click('.edit-profile-dialog input[type=submit]');

		// Then I can see a success notification
		$this->assertNotification('app_users_edit_success');

		// I should see the new first name of the user in the table info
		$this->assertElementContainsText(
		  $this->find('.table-info.profile .name'),
		  $newname . ' Lovelace'
		);

		// Refresh the page.
		$this->refresh();

		// Go to settings.
		$this->gotoWorkspace( 'settings' );

		// I should see the new first name of the user in the table info
		$this->assertElementContainsText(
		  $this->find('.table-info.profile .name'),
		  $newname . ' Lovelace'
		);

    $this->resetDatabase();
	}

	/**
	 * Scenario: As LU I can edit my own last name
	 *
	 * Given    I am admin
	 * And      I am logged in on the user workspace
	 * And      I am editing a user
	 * When     I click on last name input text field
	 * And      I empty the last name input text field value
	 * And      I enter a new value
	 * And      I click save
	 * Then     I can see a success notification
	 * And      I can see that the user last name has changed in the profile details
	 * When     I refresh
	 * And      I go to the settings workspace, profile section
	 * Then     I can see the new last name in my name
	 */
	public function testSettingsProfileUpdateEditLastName() {
		// Given I am Ada
		$user = User::get( 'ada' );
		$this->setClientConfig( $user );

		// And I am logged in on the user workspace
		$this->loginAs( $user );

		// Go to settings workspace
		$this->gotoWorkspace( 'settings' );

		// Click on edit button.
		$this->click('js_settings_wk_menu_edition_button');

		// I should see a dialog with title "Edit User".
		$this->waitUntilISee('.dialog', '/Edit profile/');

		// When I click on name input text field
		$this->click('js_field_last_name');

		// And I empty the name input text field value
		// And I enter a new value
		$newname = 'modifiedlastname';
		$this->inputText('js_field_last_name', $newname);

		// And I click save
		$this->click('.edit-profile-dialog input[type=submit]');

		// Then I can see a success notification
		$this->assertNotification('app_users_edit_success');

		// I should see the new first name of the user in the table info
		$this->assertElementContainsText(
		  $this->find('.table-info.profile .name'),
		  'Ada ' . $newname
		);

		// Refresh the page.
		$this->refresh();

		// Go to settings.
		$this->gotoWorkspace( 'settings' );

		// I should see the new first name of the user in the table info
		$this->assertElementContainsText(
		  $this->find('.table-info.profile .name'),
		  'Ada ' . $newname
		);

    $this->resetDatabase();
	}
}