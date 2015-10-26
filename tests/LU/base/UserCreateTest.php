<?php
/**
 * Feature :  As a user I can create passwords
 *
 * Scenarios :
 * As a user I can view the create user dialog
 * As a user I can open close the create user dialog
 * As a admin I can see error messages when creating a user with wrong inputs
 * As a user I can view a user I just created on my list of users
 * After creating a user, the given user can complete the setup and login with the chosen password
 * After creating a non admin user, the given user shouldn't have access to the admin functionalities
 * After creating an admin user, the given user shouldn have access to the admin functionalities
 *
 * @copyright    (c) 2015-present Bolt Software Pvt. Ltd.
 * @licence      GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
 */
class UserCreateTest extends PassboltTestCase {

	/**
	 * Scenario :   As a user I can view the create user dialog
	 * Given        I am admin
	 * And          I am logged in
	 * When         I go to user workspace
	 * Then         I should see a button create in the actions panel
	 * When         I click on the create button
	 * Then         I should see a dialog with title "Create User"
	 * And          I should see a first name field and label
	 * And          I should see a last name field and label
	 * And          I should see a username field and label
	 * And          I should see a role checkbox
	 * And          I should see a send password to user by email radio button
	 * And          I should see a Save button
	 * And          I should see a cancel button
	 * And          I should see a button close to close the dialog
	 */
	public function testCreateUserDialogExist() {
		// Given I am Ada
		$user = User::get('admin');
		$this->setClientConfig($user);

		// I am logged in as Carol, and I go to the user workspace
		$this->loginAs($user['Username']);

		// Go to user workspace
		$this->gotoWorkspace('user');

		// then I see the create password button
		$this->assertElementContainsText(
			$this->find('.actions'), 'create'
		);

		// Create a new user
		$this->gotoCreateUser();

		// Then I see the create password dialog
		$this->assertVisible('.create-user-dialog');

		// And I see the title is set to "Add user"
		$this->assertElementContainsText(
			$this->findByCss('.dialog'), 'Add User'
		);

		// And I see the first name text input and label is marked as mandatory
		$this->assertVisible('.create-user-dialog input[type=text]#js_field_first_name.required');
		$this->assertVisible('.create-user-dialog label[for=js_field_first_name]');

		// And I see the last name text input and label is marked as mandatory
		$this->assertVisible('.create-user-dialog input[type=text]#js_field_last_name.required');
		$this->assertVisible('.create-user-dialog label[for=js_field_last_name]');

		// And I see the last name text input and label is marked as mandatory
		$this->assertVisible('.create-user-dialog input[type=text]#js_field_username.required');
		$this->assertVisible('.create-user-dialog label[for=js_field_username]');

		// And I see the role input marked as mandatory
		$this->assertVisible('.create-user-dialog .input.required #js_field_role_id');
		$this->assertVisible('.create-user-dialog .input.required label[for=js_field_role_id]');

		// And I see the password send by email field
		$this->assertVisible('.create-user-dialog .input.required #js_field_email_user');
		$this->assertVisible('.create-user-dialog .input.required label[for=js_field_email_user]');

		// And I see the save button
		$this->assertVisible('.create-user-dialog input[type=submit].button.primary');

		// And I see the cancel button
		$this->assertVisible('.create-user-dialog a.cancel');

		// And I see the close dialog button
		$this->assertVisible('.create-user-dialog a.dialog-close');
	}

	/**
	 * Scenario: As a user I can open close the create user dialog
	 *
	 * Given    I am Admin
	 * And      I am logged in
	 * And      I am on the user workspace
	 * When     I click on the create user button
	 * Then     I see the create user dialog
	 * When     I click on the cancel button
	 * Then     I should not see the create user dialog
	 * When     I click on the create user button
	 * Then     I see the create user dialog
	 * When     I click on the close dialog button
	 * Then     I should not see the create user dialog
	 * When     I click on the create user button
	 * Then     I see the create user dialog
	 * When     I press the keyboard escape key
	 * Then     I should not see the create user dialog
	 */
	public function testCreateUserDialogOpenClose() {
		// Given that I am Ada
		$user = User::get('admin');
		$this->setClientConfig($user);

		// And I am logged in and on the password workspace
		$this->loginAs($user['Username']);

		// Go to user workspace
		$this->gotoWorkspace('user');

		// Create a new user
		$this->gotoCreateUser();

		// When I click on the cancel button
		$this->findByCss('.create-user-dialog a.cancel')->click();

		// Then I should not see the create password dialog
		$this->assertNotVisible('.create-user-dialog');

		// -- WITH X BUTTON --
		// When I click on the create password button
		$this->click('js_user_wk_menu_creation_button');

		// Then I see the create password dialog
		$this->assertVisible('.create-user-dialog');

		// When I click on the close dialog button
		$this->findByCss('.create-user-dialog a.dialog-close')->click();

		// Then I should not see the create password dialog
		$this->assertNotVisible('.create-user-dialog');

		// -- WITH ESCAPE --
		// When I click on the create password button
		$this->click('js_user_wk_menu_creation_button');

		// Then I see the create password dialog
		$this->assertVisible('.create-user-dialog');

		// When I click on the escape key
		$this->pressEscape();

		// Then I should not see the create password dialog
		$this->assertTrue($this->isNotVisible('.create-user-dialog'));
	}

	/**
	 * Scenario: As a admin I can see error messages when creating a user with wrong inputs
	 *
	 * Given    I am Admin
	 * And      I am logged in
	 * And      I am on the create user dialog
	 * When     I press the enter key on the keyboard after clicking in the field first name
	 * Then     I see an error message saying that the first name is required
	 * And      I see an error message saying that the last name is required
	 * And      I see an error message saying that the username is required
	 * When     I enter '&' as a first name
	 * And      I enter '&' as a last name
	 * And      I enter '&' as a username
	 * And      I click on the save button
	 * Then     I see an error message saying that the first name contain invalid characters
	 * And      I see an error message saying that the last name contain invalid characters
	 * And      I see an error message saying that the username should be an email
	 * When     I enter 'aa' as a first name
	 * And      I enter 'aa' as a last name
	 * Then     I see an error message saying that the length of first name should be between x and x characters
	 * And      I see an error message saying that the length of first name should be between x and x characters
	 */
	public function testCreateUserErrorMessages() {
		// Given that I am Ada
		$user = User::get('admin');
		$this->setClientConfig($user);

		// And I am logged in and on the password workspace
		$this->loginAs($user['Username']);

		// Go to user workspace
		$this->gotoWorkspace('user');

		// Create a new user
		$this->gotoCreateUser();

		// When I click on the name input field
		$this->click('js_field_first_name');

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

		// Then I see an error message saying that the username is required
		$this->assertVisible('#js_field_username_feedback.error.message');
		$this->assertElementContainsText(
			$this->find('js_field_username_feedback'), 'is required'
		);

		// When I enter & as a first name
		$this->inputText('js_field_first_name', '&');

		// When I enter & as a last name
		$this->inputText('js_field_last_name', '&');

		// And I enter & as a username
		$this->inputText('js_field_username', '&');

		// And I click save
		$this->click('.create-user-dialog input[type=submit]');

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

		// Then I see an error message saying that the username should be an email
		$this->assertVisible('#js_field_username_feedback.error.message');
		$this->assertElementContainsText(
			$this->find('js_field_username_feedback'), 'Only email format is allowed'
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
	 * Scenario: As a user I can view a user I just created on my list of users
	 *
	 * Given    I am Admin
	 * And      I am logged in
	 * And      I am on the create user dialog
	 * When     I enter 'firstnametest' as the first name
	 * And      I enter 'lastnametest' as the last name
	 * And      I enter 'usernametest@passbolt.com' as the username
	 * And      I click on the save button
	 * And      I see a notice message that the operation was a success
	 * And      I see the user I created in my user list
	 */
	public function testCreateUserAndView() {
		// Given I am Ada
		$user = User::get('admin');
		$this->setClientConfig($user);

		// And I am logged in
		$this->loginAs($user['Username']);

		// Go to user workspace
		$this->gotoWorkspace('user');

		// Create a new user
		$this->gotoCreateUser();

		// Enter firstnametest in the first name field
		$this->inputText('js_field_first_name', 'firstnametest');

		// Enter lastnametest in the last name field
		$this->inputText('js_field_last_name', 'lastnametest');

		// Enter usernametest in the username field
		$this->inputText('js_field_username', 'usernametest@passbolt.com');

		// When I click on the save button
		$this->click('.create-user-dialog input[type=submit]');

		// I see a notice message that the operation was a success
		$this->assertNotification('app_users_add_success');

		// I see the password I created in my password list
		$this->assertElementContainsText(
			$this->find('js_passbolt_people_workspace_controller'), 'firstnametest'
		);
		$this->assertElementContainsText(
			$this->find('js_passbolt_people_workspace_controller'), 'lastnametest'
		);
		$this->assertElementContainsText(
			$this->find('js_passbolt_people_workspace_controller'), 'usernametest'
		);

		// Since content was edited, we reset the database
		$this->resetDatabase();
	}

	/**
	 * Scenario :   After creating a user, the given user can complete the setup and login with the chosen password
	 * Given        I am admin
	 * And          I am logged in
	 * When         I go to user workspace
	 * And          I create a user
	 * Then         I could see the user is created
	 * And          I logout
	 *
	 * Given        I am the user freshly created
	 * And          I access the email received regarding my account creation
	 * And          I click on the link inside the email
	 * Then         I should reach the setup page
	 * When         I complete the setup
	 * Then         I should be logged in with my new account
	 * When         I log out
	 * And          I login again with my username and the password I have set
	 * Then         I should be logged in with my new account
	 */
	public function testCreateUserCanLogInAfter() {
		// Given I am Ada
		$user = User::get('admin');
		$this->setClientConfig($user);

		// And I am logged in
		$this->loginAs($user['Username']);

		// Go to user workspace
		$this->gotoWorkspace('user');

		$this->createUser([
			'first_name' => 'normaluser',
			'last_name'  => 'normaluser',
			'username'   => 'normaluser@passbolt.com'
		]);

		$this->logout();

		// As AN, access the email sent after accoun creation
		$this->getUrl('seleniumTests/showLastEmail/' . urlencode('normaluser@passbolt.com'));
		// Follow the link in the email.
		$this->followLink('get started');
		// Wait until I am sure that the page is loaded.
		$this->waitUntilISee('.plugin-check-wrapper', '/Plugin check/');
		// Go to login page. we don't need to complete the setup since we just want to check the login.
		$this->completeSetupWithKeyGeneration([
			'username' => 'normaluser@passbolt.com',
			'password' => 'password',
			'masterpassword' => 'masterpassword'
		]);
		$this->logout();
		$this->loginAs('normaluser@passbolt.com');
		$this->assertElementContainsText(
			$this->find('js_app_profile_dropdown'), 'normaluser@passbolt.com'
		);
		$this->resetDatabase();
	}

	/**
	 * Scenario :   After creating a non admin user, the given user shouldn't have access to the admin functionalities
	 * Given        I am admin
	 * And          I am logged in
	 * When         I go to user workspace
	 * And          I create a non admin user
	 * Then         I could see the user is created
	 * And          I logout
	 *
	 * Given        I am the user freshly created
	 * And          I access the email received regarding my account creation
	 * And          I click on the link inside the email
	 * Then         I should reach the setup page
	 * When         I complete the setup
	 * Then         I should be logged in with my new account
	 * When         I go to user workspace
	 * Then         I should not see the create button
	 * And          I should not see the edit button
	 * And          I should not see the delete button
	 */
	public function testCreateNonAdminUserHasNotAdminRights() {
		// Given I am Ada
		$user = User::get('admin');
		$this->setClientConfig($user);

		// And I am logged in
		$this->loginAs($user['Username']);

		// Go to user workspace
		$this->gotoWorkspace('user');

		$this->createUser([
			'first_name' => 'normaluser',
			'last_name'  => 'normaluser',
			'username'   => 'normaluser@passbolt.com'
		]);

		$this->logout();
		// As AN, access the email sent after accoun creation
		$this->getUrl('seleniumTests/showLastEmail/' . urlencode('normaluser@passbolt.com'));
		// Follow the link in the email.
		$this->followLink('get started');
		// Wait until I am sure that the page is loaded.
		$this->waitUntilISee('.plugin-check-wrapper', '/Plugin check/');
		// Go to login page. we don't need to complete the setup since we just want to check the login.
		$this->completeSetupWithKeyGeneration([
			'username' => 'normaluser@passbolt.com',
			'password' => 'password',
			'masterpassword' => 'masterpassword'
		]);

		// Go to user workspace
		$this->gotoWorkspace('user');

		// Observe that create button is not visible
		$this->assertNotVisible('js_user_wk_menu_creation_button');

		// Observe that edit button is not visible
		$this->assertNotVisible('js_user_wk_menu_edition_button');

		// Observe that delete button is not visible
		$this->assertNotVisible('js_user_wk_menu_deletion_button');
	}

	/**
	 * Scenario :   After creating an admin user, the given user shouldn have access to the admin functionalities
	 * Given        I am admin
	 * And          I am logged in
	 * When         I go to user workspace
	 * And          I create a admin user
	 * Then         I could see the user is created
	 * And          I logout
	 *
	 * Given        I am the user freshly created
	 * And          I access the email received regarding my account creation
	 * And          I click on the link inside the email
	 * Then         I should reach the setup page
	 * When         I complete the setup
	 * Then         I should be logged in with my new account
	 * When         I go to user workspace
	 * Then         I should see the create button
	 * And          I should see the edit button
	 * And          I should see the delete button
	 */
	public function testCreateAdminUserHasAdminRights() {
		// Given I am Ada
		$user = User::get('admin');
		$this->setClientConfig($user);

		// And I am logged in
		$this->loginAs($user['Username']);

		// Go to user workspace
		$this->gotoWorkspace('user');

		$this->createUser([
			'first_name' => 'adminuser',
			'last_name'  => 'adminuser',
			'username'   => 'adminuser@passbolt.com',
			'admin'      => true
		]);

		$this->logout();
		// As AN, access the email sent after accoun creation
		$this->getUrl('seleniumTests/showLastEmail/' . urlencode('adminuser@passbolt.com'));
		// Follow the link in the email.
		$this->followLink('get started');
		// Wait until I am sure that the page is loaded.
		$this->waitUntilISee('.plugin-check-wrapper', '/Plugin check/');
		// Go to login page. we don't need to complete the setup since we just want to check the login.
		$this->completeSetupWithKeyGeneration([
			'username' => 'adminuser@passbolt.com',
			'password' => 'password',
			'masterpassword' => 'masterpassword'
		]);

		// Go to user workspace
		$this->gotoWorkspace('user');

		// Observe that create button is not visible
		$this->assertVisible('js_user_wk_menu_creation_button');

		// Observe that edit button is not visible
		$this->assertVisible('js_user_wk_menu_edition_button');

		// Observe that delete button is not visible
		$this->assertVisible('js_user_wk_menu_deletion_button');
	}
}