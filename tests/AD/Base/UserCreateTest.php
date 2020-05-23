<?php
/**
 * Passbolt ~ Open source password manager for teams
 * Copyright (c) Passbolt SARL (https://www.passbolt.com)
 *
 * Licensed under GNU Affero General Public License version 3 of the or any later version.
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Passbolt SARL (https://www.passbolt.com)
 * @license   https://opensource.org/licenses/AGPL-3.0 AGPL License
 * @link      https://www.passbolt.com Passbolt(tm)
 * @since     2.0.0
 */
/**
 * Feature: As a user I can create passwords
 *
 * Scenarios :
 *  - As an admin I should be able to access the user add dialog using route
 *  - As a user I can view the create user dialog
 *  - As a user I can open close the create user dialog
 *  - As a admin I can see error messages when creating a user with wrong inputs
 *  - As an admin, I cannot create a user that has a username that is already taken
 *  - As a user I can view a user I just created on my list of users
 *  - After creating a user, the given user can complete the setup and login with the chosen password
 *  - After creating a non admin user, the given user shouldn't have access to the admin functionalities
 *  - After creating an admin user, the given user shouldn have access to the admin functionalities
 *  - As admin I can see a user I have just created, but a normal user can't until the created user has completed the setup
 *  - As LU, after an admin created an account for me, I should receive a confirmation email.
 *  - As LU I should be able to create a user after I restart the browser
 *  - As LU I should be able to create a user after I close and restore the passbolt tab
 */
namespace Tests\AD\Base;

use App\Actions\SetupActionsTrait;
use App\Actions\UserActionsTrait;
use App\Actions\WorkspaceActionsTrait;
use App\Assertions\UserAssertionsTrait;
use App\Assertions\WorkspaceAssertionsTrait;
use App\PassboltTestCase;
use Data\Fixtures\User;
use Data\Fixtures\Gpgkey;

class UserCreateTest extends PassboltTestCase
{

    use UserActionsTrait;
    use UserAssertionsTrait;
    use SetupActionsTrait;
    use WorkspaceAssertionsTrait;
    use WorkspaceActionsTrait;

    /**
     * Scenario: As an admin I should be able to access the user add dialog using route
     *
     * When  I am logged in as Ada
     * And   I enter the route in the url
     * Then  I should see the user add dialog
     *
     * @group AD
     * @group user
     * @group user-create
     * @group saucelabs
     * @group v2
     */
    public function testRoute_AddUser()
    {
        $this->loginAs(User::get('admin'), ['url' => '/app/users/add?first_name=John&last_name=Doe&username=john.doe@assbolt.com']);
        $this->waitCompletion();
        $this->assertInputValue('js_field_first_name', 'John');
        $this->assertInputValue('js_field_last_name', 'Doe');
        $this->assertInputValue('js_field_username', 'john.doe@assbolt.com');
    }

    /**
     * Scenario: As an admin I can view the create user dialog
     *
     * Given I am admin
     * And   I am logged in
     * When  I go to user workspace
     * Then  I should see a button create in the actions panel
     * When  I click on the create button
     * Then  I should see a dialog with title "Create User"
     * And   I should see a first name field and label
     * And   I should see a last name field and label
     * And   I should see a username field and label
     * And   I should see a role checkbox
     * And   I should see a send password to user by email radio button
     * And   I should see a Save button
     * And   I should see a cancel button
     * And   I should see a button close to close the dialog
     *
     * @group AD
     * @group user
     * @group create
     * @group v2
     */
    public function testCreateUserDialogExist() 
    {
        // Given I am Ada
        $user = User::get('admin');
        
        // I am logged in as Carol, and I go to the user workspace
        $this->loginAs($user);

        // Go to user workspace
        $this->gotoWorkspace('user');

        // Then I see the create password button
        $this->assertElementContainsText(
            $this->findByCss('.main-action-wrapper'),
            'create'
        );

        // Create a new user
        $this->gotoCreateUser();

        // Then I see the create password dialog
        $this->assertVisibleByCss('.create-user-dialog');

        // And I see the title is set to "Add user"
        $this->assertElementContainsText(
            $this->findByCss('.dialog'), 'Add User'
        );

        // And I see the first name text input and label is marked as mandatory
        $this->assertVisibleByCss('.create-user-dialog input[type=text]#js_field_first_name.required');
        $this->assertVisibleByCss('.create-user-dialog label[for=js_field_first_name]');

        // And I see the last name text input and label is marked as mandatory
        $this->assertVisibleByCss('.create-user-dialog input[type=text]#js_field_last_name.required');
        $this->assertVisibleByCss('.create-user-dialog label[for=js_field_last_name]');

        // And I see the last name text input and label is marked as mandatory
        $this->assertVisibleByCss('.create-user-dialog input[type=text]#js_field_username.required');
        $this->assertVisibleByCss('.create-user-dialog label[for=js_field_username]');

        // And I see the role input marked as mandatory
        $this->assertVisibleByCss('.create-user-dialog .input.required #js_field_is_admin');
        $this->assertVisibleByCss('.create-user-dialog .input.required label[for=js_field_is_admin]');

        // And I see the save button
        $this->assertVisibleByCss('.create-user-dialog input[type=submit].button.primary');

        // And I see the cancel button
        $this->assertVisibleByCss('.create-user-dialog a.cancel');

        // And I see the close dialog button
        $this->assertVisibleByCss('.create-user-dialog a.dialog-close');
    }

    /**
     * Scenario: As an admin I can open close the create user dialog
     *
     * Given I am Admin
     * And   I am logged in
     * And   I am on the user workspace
     * When  I click on the create user button
     * Then  I see the create user dialog
     * When  I click on the cancel button
     * Then  I should not see the create user dialog
     * When  I click on the create user button
     * Then  I see the create user dialog
     * When  I click on the close dialog button
     * Then  I should not see the create user dialog
     * When  I click on the create user button
     * Then  I see the create user dialog
     * When  I press the keyboard escape key
     * Then  I should not see the create user dialog
     *
     * @group AD
     * @group user
     * @group create
     * @group v2
     */
    public function testCreateUserDialogOpenClose() 
    {
        // Given that I am Admin
        // And I am logged in and on the password workspace
        $this->loginAs(User::get('admin'));

        // Go to user workspace
        $this->gotoWorkspace('user');

        // Create a new user
        $this->gotoCreateUser();

        // When I click on the cancel button
        $this->findByCss('.create-user-dialog a.cancel')->click();

        // Then I should not see the create password dialog
        $this->assertNotVisibleByCss('.create-user-dialog');

        // -- WITH X BUTTON --
        // When I click on the create password button
        $this->click('js_wsp_create_button');
        $this->waitUntilISee('.main-action-wrapper ul.dropdown-content');
        $this->click('.main-action-wrapper ul.dropdown-content li.create-user');

        // Then I see the create password dialog
        $this->assertVisibleByCss('.create-user-dialog');

        // When I click on the close dialog button
        $this->findByCss('.create-user-dialog a.dialog-close')->click();

        // Then I should not see the create password dialog
        $this->assertNotVisibleByCss('.create-user-dialog');

        // -- WITH ESCAPE --
        // When I click on the create password button
        $this->click('js_wsp_create_button');
        $this->waitUntilISee('.main-action-wrapper ul.dropdown-content');
        $this->click('.main-action-wrapper ul.dropdown-content li.create-user');

        // Then I see the create password dialog
        $this->assertVisibleByCss('.create-user-dialog');

        // When I click on the escape key
        $this->pressEscape();

        // Then I should not see the create password dialog
        $this->assertTrue($this->isNotVisible('.create-user-dialog'));
    }

    /**
     * Scenario: As a admin I can see error messages when creating a user with wrong inputs
     *
     * Given I am Admin
     * And   I am logged in
     * And   I am on the create user dialog
     * When  I press the enter key on the keyboard after clicking in the field first name
     * Then  I see an error message saying that the first name is required
     * And   I see an error message saying that the last name is required
     * And   I see an error message saying that the username is required
     * When  I enter 'emoticon' as a first name
     * And   I enter 'emoticon' as a last name
     * And   I enter 'not-valid-email' as a username
     * And   I click on the save button
     * Then  I see an error message saying that the first name contain invalid characters
     * And   I see an error message saying that the last name contain invalid characters
     * And   I see an error message saying that the username should be an email
     *
     * @group AD
     * @group user
     * @group create
     * @group v2
     */
    public function testCreateUserErrorMessages() 
    {
        // Given that I am Admin
        // And I am logged in and on the user workspace
        $this->loginAs(User::get('admin'));

        // Go to user workspace
        $this->gotoWorkspace('user');

        // Create a new user
        $this->gotoCreateUser();

        // When I click on the name input field
        $this->click('js_field_first_name');

        // And I press enter
        $this->pressEnter();

        // Then I see an error message saying that the first name is required
        $this->assertVisibleByCss('#js_field_first_name_feedback.error.message');
        $this->assertElementContainsText(
            $this->find('js_field_first_name_feedback'), 'is required'
        );

        // Then I see an error message saying that the last name is required
        $this->assertVisibleByCss('#js_field_last_name_feedback.error.message');
        $this->assertElementContainsText(
            $this->find('js_field_last_name_feedback'), 'is required'
        );

        // Then I see an error message saying that the username is required
        $this->assertVisibleByCss('#js_field_username_feedback.error.message');
        $this->assertElementContainsText(
            $this->find('js_field_username_feedback'), 'is required'
        );

        // When I enter & as a first name
        $this->inputTextWithEmojis('#js_field_first_name', "🙂");

        // When I enter & as a last name
        $this->inputTextWithEmojis('#js_field_last_name', "🙂");

        // And I enter & as a username
        $this->inputText('js_field_username', 'not-valid-email');

        // And I click save
        $this->click('.create-user-dialog input[type=submit]');

        // Then I see an error message saying that the first name contain invalid characters
        $this->assertVisibleByCss('#js_field_first_name_feedback.error.message');
        $this->assertElementContainsText(
            $this->find('js_field_first_name_feedback'), 'First name should be a valid utf8 string.'
        );

        // And I see an error message saying that the first name contain invalid characters
        $this->assertVisibleByCss('#js_field_last_name_feedback.error.message');
        $this->assertElementContainsText(
            $this->find('js_field_last_name_feedback'), 'Last name should be a valid utf8 string.'
        );

        // And I see an error message saying that the username should be an email
        $this->assertVisibleByCss('#js_field_username_feedback.error.message');
        $this->assertElementContainsText(
            $this->find('js_field_username_feedback'), 'The username should be a valid email address.'
        );
    }

    /**
     * Scenario: As an admin, I cannot create a user that has a username that is already taken.
     *
     * Given I am Admin
     * And   I am logged in
     * And   I am on the create user dialog
     * When  I enter 'firstnametest' as the first name
     * And   I enter 'lastnametest' as the last name
     * And   I enter 'ada@passbolt.com' as the username
     * And   I click on the save button
     * And   I see a notice message that the username is already taken
     *
     * @group AD
     * @group user
     * @group create
     * @group v2
     */
    public function testCreateUserUsernameExist() 
    {
        // Given that I am Admin
        // And I am logged in and on the user workspace
        $this->loginAs(User::get('admin'));

        // Go to user workspace
        $this->gotoWorkspace('user');

        // Create a new user
        $this->gotoCreateUser();

        // When I enter & as a first name
        $this->inputText('js_field_first_name', 'firstnametest');

        // When I enter & as a last name
        $this->inputText('js_field_last_name', 'lastnametest');

        // And I enter & as a username
        $this->inputText('js_field_username', 'ada@passbolt.com');

        // And I click save
        $this->click('.create-user-dialog input[type=submit]');

        // Then I see a notice message that the username is already taken
        $this->waitUntilISee('#js_field_username_feedback.error.message');
        $this->assertElementContainsText(
            $this->find('js_field_username_feedback'), 'This username is already in use.'
        );
    }

    /**
     * Scenario: As a user I can view a user I just created on my list of users
     *
     * Given I am Admin
     * And   I am logged in
     * And   I am on the create user dialog
     * When  I enter 'firstnametest' as the first name
     * And   I enter 'lastnametest' as the last name
     * And   I enter 'usernametest@passbolt.com' as the username
     * And   I click on the save button
     * And   I see a notice message that the operation was a success
     * And   I see the user I created in my user list
     *
     * @group AD
     * @group user
     * @group create
     * @group saucelabs
     * @group v2
     */
    public function testCreateUserAndView() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Ada
        // And I am logged in
        $this->loginAs(User::get('admin'));

        // Go to user workspace
        $this->gotoWorkspace('user');

        // Create a new user
        $this->gotoCreateUser();

        // Enter firstnametest in the first name field
        $this->inputText('js_field_first_name', 'Firstnametest');

        // Enter lastnametest in the last name field
        $this->inputText('js_field_last_name', 'Lastnametest');

        // Enter usernametest in the username field
        $this->inputText('js_field_username', 'usernametest@passbolt.com');

        // When I click on the save button
        $this->click('.create-user-dialog input[type=submit]');

        // I see a notice message that the operation was a success
        $this->assertNotification('app_users_addPost_success');
        $this->waitCompletion();

        // I see the password I created in my password list
        $this->assertElementContainsText(
            $this->find('js_passbolt_user_workspace_controller'), 'Firstnametest'
        );
        $this->assertElementContainsText(
            $this->find('js_passbolt_user_workspace_controller'), 'Lastnametest'
        );
        $this->assertElementContainsText(
            $this->find('js_passbolt_user_workspace_controller'), 'usernametest'
        );
    }

    /**
     * Scenario: After creating a user, the given user can complete the setup and login with the chosen password
     *
     * Given I am admin
     * And   I am logged in
     * When  I go to user workspace
     * And   I create a user
     * Then  I could see the user is created
     * And   I logout
     *
     * Given I am the user freshly created
     * And   I access the email received regarding my account creation
     * And   I click on the link inside the email
     * Then  I should reach the setup page
     * When  I complete the setup
     * Then  I should be logged in with my new account
     * When  I log out
     * And   I login again with my username and the password I have set
     * Then  I should be logged in with my new account
     *
     * @group AD
     * @group user
     * @group create
     * @group saucelabs
     * @group v2
     */
    public function testCreateUserCanLogInAfter() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Ada
        $user = User::get('admin');
        

        // And I am logged in
        $this->loginAs($user);

        // Go to user workspace
        $this->gotoWorkspace('user');

        // Create a user
        $this->createUser(
            [
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'username'   => 'johndoe@passbolt.com'
            ]
        );

        $this->logout();

        // As a new user, I start the passbolt plugin setup
        $this->goToSetup('johndoe@passbolt.com', 'warning');

        // Go to login page. we don't need to complete the setup since we just want to check the login.
        $this->completeSetupWithKeyImport(
            [
            'private_key'=>file_get_contents(Gpgkey::get(['name' => 'johndoe'])['filepath'])
            ]
        );
        $this->loginAs(User::get('john'));
        $this->assertElementContainsText(
            $this->find('js_app_profile_dropdown'), 'johndoe@passbolt.com'
        );
    }

    /**
     * Scenario: After creating a non admin user, the given user shouldn't have access to the admin functionalities
     * Given I am admin
     * And   I am logged in
     * When  I go to user workspace
     * And   I create a non admin user
     * Then  I could see the user is created
     * And   I logout
     *
     * Given I am the user freshly created
     * And   I access the email received regarding my account creation
     * And   I click on the link inside the email
     * Then  I should reach the setup page
     * When  I complete the setup
     * Then  I should be logged in with my new account
     * When  I go to user workspace
     * Then  I should not see the create button
     * And   I should not see the edit button
     * And   I should not see the delete button
     *
     * @group AD
     * @group user
     * @group create
     * @group v2
     */
    public function testCreateNonAdminUserHasNotAdminRights() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Ada
        // And I am logged in
        $this->loginAs(User::get('admin'));

        // Go to user workspace
        $this->gotoWorkspace('user');

        $this->createUser(
            [
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'username'   => 'johndoe@passbolt.com'
            ]
        );

        $this->logout();

        // As a new user, I start the passbolt plugin setup
        $this->goToSetup('johndoe@passbolt.com', 'warning');

        // Go to login page. we don't need to complete the setup since we just want to check the login.
        $this->completeSetupWithKeyImport(
            [
            'private_key'=>file_get_contents(Gpgkey::get(['name' => 'johndoe'])['filepath'])
            ]
        );

        $this->loginAs(User::get('john'));
        $this->assertElementContainsText(
            $this->find('js_app_profile_dropdown'), 'johndoe@passbolt.com'
        );

        // Go to user workspace
        $this->gotoWorkspace('user');

        // Observe that create button is not visible
        $this->assertNotVisibleByCss('#js_wsp_create_button');

        // Observe that edit button is not visible
        $this->assertNotVisibleByCss('#js_user_wk_menu_edition_button');

        // Observe that delete button is not visible
        $this->assertNotVisibleByCss('#js_user_wk_menu_deletion_button');
    }

    /**
     * Scenario: After creating an admin user, the given user should have access to the admin functionalities
     * Given I am admin
     * And   I am logged in
     * When  I go to user workspace
     * And   I create a admin user
     * Then  I could see the user is created
     * And   I logout
     *
     * Given I am the user freshly created
     * And   I access the email received regarding my account creation
     * And   I click on the link inside the email
     * Then  I should reach the setup page
     * When  I complete the setup
     * Then  I should be logged in with my new account
     * When  I go to user workspace
     * Then  I should see the create button
     * And   I should see the edit button
     * And   I should see the delete button
     *
     * @group AD
     * @group user
     * @group create
     * @group v2
     */
    public function testCreateAdminUserHasAdminRights() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Ada
        $user = User::get('admin');
        

        // And I am logged in
        $this->loginAs($user);

        // Go to user workspace
        $this->gotoWorkspace('user');

        $this->createUser(
            [
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'username'   => 'johndoe@passbolt.com',
            'admin'      => true
            ]
        );

        $this->logout();

        // As a new user, I start the passbolt plugin setup
        $this->goToSetup('johndoe@passbolt.com', 'warning');

        // Go to login page. we don't need to complete the setup since we just want to check the login.
        $this->completeSetupWithKeyImport(
            [
            'private_key'=>file_get_contents(Gpgkey::get(['name' => 'johndoe'])['filepath'])
            ]
        );

        // Log in.
        $this->loginAs(User::get('john'));

        // Assert user is logged in.
        $this->assertElementContainsText(
            $this->find('js_app_profile_dropdown'), 'johndoe@passbolt.com'
        );

        // Go to user workspace
        $this->gotoWorkspace('user');

        // Observe that create button is visible
        $this->assertVisible('js_wsp_create_button');

        // Observe that edit button is visible
        $this->assertVisible('js_user_wk_menu_edition_button');

        // Observe that delete button is visible
        $this->assertVisible('js_user_wk_menu_deletion_button');
    }

    /**
     * Scenario: As admin I can see a user I have just created, but a normal user can't until the created user has completed the setup
     *
     * Given I am Admin
     * And   I am logged in
     * And   I am on the create user dialog
     * When  I enter 'firstnametest' as the first name
     * And   I enter 'lastnametest' as the last name
     * And   I enter 'usernametest@passbolt.com' as the username
     * And   I click on the save button
     * And   I see a notice message that the operation was a success
     * And   I see the user I created in my user list
     * When  I logout
     * And   I login again as a normal user (Ada)
     * And   I go to user workspace
     * Then  I should not see the new user in the users list
     * When  I complete the setup as the new created user
     * And   I log out after being logged in at the end of setup
     * Then  I should be logged out
     * When  I log in again as normal user (Ada)
     * And   I go to user workspace
     * Then  I should see the new user in the users list
     *
     * @group AD
     * @group user
     * @group create
     * @group v2
     */
    public function testCreateUserAdminCanViewNotUserUntilFirstLogin() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Admin
        // And I am logged in
        $this->loginAs(User::get('admin'));

        // Go to user workspace
        $this->gotoWorkspace('user');

        // Create user
        $newUser = [
            'first_name' => 'Firstnametest',
            'last_name'  => 'Lastnametest',
            'username'   => 'usernametest@passbolt.com',
            'admin'      => false
        ];
        $this->createUser($newUser);

        // I see the user I created in my users list
        $this->assertElementContainsText(
            $this->find('js_passbolt_user_workspace_controller'), $newUser['first_name']
        );
        $this->assertElementContainsText(
            $this->find('js_passbolt_user_workspace_controller'), $newUser['last_name']
        );
        $this->assertElementContainsText(
            $this->find('js_passbolt_user_workspace_controller'), $newUser['username']
        );

        $this->logout();

        // Given I am Ada
        $user = User::get('betty');

        // And I am logged in
        $this->loginAs($user);

        // Go to user workspace
        $this->gotoWorkspace('user');

        // I don't see the user that has been created by admin
        $this->assertElementNotContainText(
            $this->find('js_passbolt_user_workspace_controller'), $newUser['first_name']
        );
        $this->assertElementNotContainText(
            $this->find('js_passbolt_user_workspace_controller'), $newUser['last_name']
        );
        $this->assertElementNotContainText(
            $this->find('js_passbolt_user_workspace_controller'), $newUser['username']
        );

        // Logout
        $this->logout();

        // As a new user, I start the passbolt plugin setup
        $this->goToSetup($newUser['username'], 'warning');

        // Go to login page. we don't need to complete the setup since we just want to check the login.
        $this->completeSetupWithKeyGeneration([
            'username' => $newUser['username'],
            'password' => 'password',
            'masterpassword' => 'masterpassword'
        ]);

        // Given I am Ada
        $user = User::get('betty');
        // And plugin is configured to use Ada
        
        // And I am logged in
        $this->loginAs($user);

        // Go to user workspace
        $this->gotoWorkspace('user');

        // I can now see the user that was created
        $this->assertElementContainsText(
            $this->find('js_passbolt_user_workspace_controller'), $newUser['first_name']
        );
        $this->assertElementContainsText(
            $this->find('js_passbolt_user_workspace_controller'), $newUser['last_name']
        );
        $this->assertElementContainsText(
            $this->find('js_passbolt_user_workspace_controller'), $newUser['username']
        );
    }

    /**
     * Scenario: As LU, after an admin created an account for me, I should receive a confirmation email.
     *
     * Given I am Admin
     * And   I am logged in
     * And   I am on the create user dialog
     * When  I enter 'John' as the first name
     * And   I enter 'Doe' as the last name
     * And   I enter 'johndoe@passbolt.com' as the username
     * And   I click on the save button
     * And   I see a notice message that the operation was a success
     * Given I log out
     * And   I check the email box of johndoe@passbolt.com
     * Then  I should see an invitation email with subject "Admin created an account for you!"
     *
     * @group AD
     * @group user
     * @group create
     * @group v2
     */
    public function testCreateUserEmail() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Admin
        // And I am logged in
        $this->loginAs(User::get('admin'));

        // Go to user workspace
        $this->gotoWorkspace('user');

        // Create user
        $newUser = [
            'first_name' => 'john',
            'last_name'  => 'doe',
            'username'   => 'johndoe@passbolt.com',
            'admin'      => false
        ];
        $this->createUser($newUser);

        // Check email.
        $this->getUrl('seleniumtests/showlastemail/' . urlencode('johndoe@passbolt.com'));

        // Assert the title of the email is "Admin created an account for you!'"
        $this->assertMetaTitleContains('Welcome to passbolt, John!');
        // Assert I can see the text "Admin just invited you yo join"
        $this->assertPageContainsText('Admin just invited you to join');
    }

    /**
     *
     * Scenario: As LU I should be able to create a user after I restart the browser
     *
     * Given I am Ada
     * And   I am logged in on the users workspace
     * When  I restart the browser
     * Then  I should be able to create a user
     *
     * @group AD
     * @group user
     * @group create
     * @group no-saucelabs
     * @group v2
     * @group skip
     */
    public function testRestartBrowserAndCreateUser() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Admin
        // And I am logged in
        $this->loginAs(User::get('admin'));

        // Go to user workspace
        $this->gotoWorkspace('user');

        // When restart the browser
        $this->restartBrowser();
        $this->waitCompletion();
        $this->gotoWorkspace('user');

        // Then I should be able to create a user
        $newUser = [
            'first_name' => 'john',
            'last_name'  => 'doe',
            'username'   => 'johndoe@passbolt.com',
            'admin'      => false
        ];
        $this->createUser($newUser);
    }

    /**
     *Scenario: As LU I should be able to create a user after I close and restore the passbolt tab
     *
     * Given I am Ada
     * And   I am on second tab
     * And   I am logged in on the users workspace
     * When  I close and restore the tab
     * Then  I should be able to create a user
     *
     * @group AD
     * @group user
     * @group create
     * @group skip
     * PASSBOLT-2263 close and restore doesn't work with the latest chrome driver
     * PASSBOLT-2419 close and restore doesn't work with the latest firefox driver
     * @group broken
     */
    public function testCloseRestoreTabAndCreateUser() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Admin
        $user = User::get('admin');

        // And I am on second tab
        $this->openNewTab('');

        // And I am logged in on the user workspace
        $this->loginAs($user);
        $this->gotoWorkspace('user');

        // When I close and restore the tab
        $this->closeAndRestoreTab();
        $this->waitCompletion();
        $this->gotoWorkspace('user');

        // Then I should be able to create a user
        $newUser = [
            'first_name' => 'john',
            'last_name'  => 'doe',
            'username'   => 'johndoe@passbolt.com',
            'admin'      => false
        ];
        $this->createUser($newUser);
    }
}