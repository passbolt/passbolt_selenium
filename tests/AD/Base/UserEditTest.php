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
 * Feature :  As a user I can edit users
 *
 * Scenarios :
 *  - As an admin I can edit a user using the edit button in the action bar
 *  - As an admin I can edit a user using the right click contextual menu
 *  - As an admin I can open close the edit user dialog
 *  - As an admin I can see the edit user dialog
 *  - As an admin I can edit the first name of a user
 *  - As an admin I can edit the last name of a user
 *  - As an admin I can modify the role of a non admin user to admin
 *  - As an admin I can modify the role of an admin user to non admin
 *  - As an admin I should'nt be able to edit my own role
 *  - As user B I can see the changes are reflected when user A has edited a user I can see
 *  - As user I can see validation error messages while editing a user and inputting bad information
 */
namespace Tests\AD\Base;

use App\Actions\SetupActionsTrait;
use App\Actions\UserActionsTrait;
use App\Actions\WorkspaceActionsTrait;
use App\Assertions\WorkspaceAssertionsTrait;
use App\PassboltTestCase;
use Data\Fixtures\User;
use Data\Fixtures\Gpgkey;

class UserEditTest extends PassboltTestCase
{
    use UserActionsTrait;
    use SetupActionsTrait;
    use WorkspaceAssertionsTrait;
    use WorkspaceActionsTrait;

    /**
     * Scenario: As a admin I can edit a user using the edit button in the action bar
     *
     * Given I am Admin
     * And   I am logged in on the user workspace
     * Then  I can see the edit user button is disabled
     * When  I click on a user
     * Then  I can see the edit button is enabled
     * When  I click on the edit button
     * Then  I can see the edit user dialog
     *
     * @group AD
     * @group user
     * @group edit
     */
    public function testEditUserButton() 
    {
        // Given I am Ada
        $user = User::get('admin');

        // And I am logged in on the user workspace
        $this->loginAs($user);

        // Go to user workspace
        $this->gotoWorkspace('user');

        // Then I can see the edit user button is disabled
        $this->assertVisibleByCss('js_user_wk_menu_edition_button');
        $this->assertVisibleByCss('#js_user_wk_menu_edition_button.disabled');

        // Select Frances Allen
        $user = User::get('betty');
        $this->clickUser($user['id']);

        // Then I can see the edit user button is disabled
        $this->assertVisibleByCss('js_user_wk_menu_edition_button');
        $this->assertNotVisible('#js_user_wk_menu_edition_button.disabled');

        // When I click on the edit button
        $this->click('js_user_wk_menu_edition_button');

        // Then I can see the edit user dialog
        $this->assertVisibleByCss('.edit-user-dialog');
    }

    /**
     * Scenario: As an admin I can edit a user using the right click contextual menu
     *
     * Given I am Admin
     * And   I am logged in on the user workspace
     * When  I right click on the user I want to edit
     * Then  I can see the contextual menu
     * And   I can see the the edit option is enabled
     * When  I click on the edit link in the contextual menu
     * Then  I can see the edit user dialog
     *
     * @group AD
     * @group user
     * @group edit
     */
    public function testEditUserRightClick() 
    {
        // Given I am Ada
        $user = User::get('admin');

        // And I am logged in on the user workspace
        $this->loginAs($user);

        $this->gotoWorkspace('user');

        // When I right click on a user
        $user = User::get('betty');
        $this->rightClickUser($user['id']);

        // Then I can see the contextual menu
        $this->assertVisibleByCss('js_contextual_menu');

        // When I click on the edit link in the contextual menu
        $this->click('#js_user_browser_menu_edit a');

        // Then I can see the edit user dialog
        $this->assertVisibleByCss('.edit-user-dialog');
    }


    /**
     * Scenario: As an admin I can open close the edit user dialog
     *
     * Given I am Admin
     * And   I am logged in on the user workspace
     * And   I am editing a user
     * When  I click on the cancel button
     * Then  I do not see the edit user dialog
     * When  I reopen the edit user dialog
     * And   I click on the close dialog button (in the top right corner)
     * Then  I do not see the edit user dialog
     * When  I reopen the edit user dialog
     * And   I press the escape button
     * Then  I do not see the edit user dialog
     *
     * @group AD
     * @group user
     * @group edit
     */
    public function testEditUserDialogOpenClose() 
    {
        // Given I am Ada
        $user = User::get('admin');

        // And I am logged in on the user workspace
        $this->loginAs($user);
        $this->gotoWorkspace('user');

        // And I am editing a user
        $user = User::get('betty');
        $this->gotoEditUser($user['id']);

        // When I click on the cancel button
        $this->click('.edit-user-dialog .js-dialog-cancel');

        // Then I do not see the edit user dialog
        $this->assertNotVisible('.edit-user-dialog');

        // When I reopen the edit user dialog
        $this->click('js_user_wk_menu_edition_button');
        $this->waitCompletion();
        $this->assertVisibleByCss('.edit-user-dialog');

        // And I click on the close dialog button (in the top right corner)
        $this->click('.edit-user-dialog .dialog-close');

        // Then I do not see the edit user dialog
        $this->assertNotVisible('.edit-user-dialog');

        // When I reopen the edit user dialog
        $this->click('js_user_wk_menu_edition_button');
        $this->waitCompletion();
        $this->assertVisibleByCss('.edit-user-dialog');

        // And I press the escape button
        $this->pressEscape();

        // Then I do not see the edit user dialog
        $this->assertTrue($this->isNotVisible('.edit-user-dialog'));
    }

    /**
     * Scenario: As an admin I can see the edit user dialog
     *
     * Given I am Admin
     * And   I am logged on the user workspace
     * And   I am editing a user
     * Then  I can see the edit user dialog
     * And   I can see the title is set to "edit xxx"
     * And   I can see the close dialog button
     * And   I can see the first name input and label is marked as mandatory
     * And   I can see the user first name in the text input
     * And   I can see the last name input and label is marked as mandatory
     * And   I can see the user last name in the text input
     * And   I can see the username text input and label marked as mandatory
     * And   I can see the username text input is disabled
     * And   I can see the user ursername in the text input
     * And   I can see the save button
     * And   I can see the cancel button
     *
     * @group AD
     * @group user
     * @group edit
     */
    public function testEditUserDialogView() 
    {
        // Given I am Ada
        $user = User::get('admin');


        // And I am logged in on the user workspace
        $this->loginAs($user['Username']);
        $this->gotoWorkspace('user');

        // And I am editing a user
        $user = User::get('betty');
        $this->gotoEditUser($user['id']);
        // And I can see the title is set to "edit user"
        $this->assertElementContainsText(
            $this->findByCss('.edit-user-dialog h2'),
            'Edit'
        );

        // And I can see the first name text input and label is marked as mandatory
        $this->assertVisibleByCss('.edit-user-dialog input[type=text]#js_field_first_name.required');
        $this->assertVisibleByCss('.edit-user-dialog label[for=js_field_first_name]');

        // And I can see the user first name in the text input
        $this->assertInputValue('js_field_first_name', $user['FirstName']);

        // And I can see the last name text input and label is marked as mandatory
        $this->assertVisibleByCss('.edit-user-dialog input[type=text]#js_field_last_name.required');
        $this->assertVisibleByCss('.edit-user-dialog label[for=js_field_last_name]');

        // And I can see the user last name in the text input
        $this->assertInputValue('js_field_last_name', $user['LastName']);

        // And I can see the last name text input and label is marked as mandatory
        $this->assertVisibleByCss('.edit-user-dialog input[type=text]#js_field_username.required');
        $this->assertVisibleByCss('.edit-user-dialog input[type=text][disabled]#js_field_username');
        $this->assertVisibleByCss('.edit-user-dialog label[for=js_field_username]');

        // And I can see the user last name in the text input
        $this->assertInputValue('js_field_username', $user['Username']);

        // And I can see the save button
        $this->assertVisibleByCss('.edit-user-dialog input[type=submit].button.primary');

        // And I can see the cancel button
        $this->assertVisibleByCss('.edit-user-dialog a.cancel');
    }

    /**
     * Scenario: As an admin I can edit the first name of a user
     *
     * Given I am admin
     * And   I am logged in on the user workspace
     * And   I am editing a user
     * When  I click on first name input text field
     * And   I empty the first name input text field value
     * And   I enter a new value
     * And   I click save
     * Then  I can see a success notification
     * And   I can see that the user first name has changed in the overview
     * And   I can see the new first name value in the sidebar
     * When  I click edit button
     * Then  I can see the new first name in the edit user dialog
     *
     * @group AD
     * @group user
     * @group edit
     * @group saucelabs
     */
    public function testEditUserFirstName() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Ada
        $user = User::get('admin');

        // And I am logged in on the user workspace
        $this->loginAs($user['Username']);
        $this->gotoWorkspace('user');

        // And I am editing a user
        $user = User::get('betty');
        $this->gotoEditUser($user['id']);

        // When I click on name input text field
        $this->click('js_field_first_name');

        // And I empty the name input text field value
        // And I enter a new value
        $newname = 'modifiedname';
        $this->inputText('js_field_first_name', $newname);

        // And I click save
        $this->click('.edit-user-dialog input[type=submit]');

        // Then I can see a success notification
        $this->assertNotification('app_users_edit_success');

        // And I can see that the user first name have changed in the overview
        $this->assertElementContainsText('#js_wsp_users_browser .tableview-content', $newname);

        // And I can see the user first name has changed in the sidebar
        $this->assertElementContainsText('#js_user_details .name', $newname);

        // When I click edit button
        $this->click('js_user_wk_menu_edition_button');

        // Then I can see the new name in the edit user dialog
        $this->assertInputValue('js_field_first_name', $newname);
    }

    /**
     * Scenario: As an admin I can edit the last name of a user
     *
     * Given I am admin
     * And   I am logged in on the user workspace
     * And   I am editing a user
     * When  I click on last name input text field
     * And   I empty the last name input text field value
     * And   I enter a new value
     * And   I click save
     * Then  I can see a success notification
     * And   I can see that the user last name has changed in the overview
     * And   I can see the new last name value in the sidebar
     * When  I click edit button
     * Then  I can see the new last name in the edit user dialog
     *
     * @group AD
     * @group user
     * @group edit
     * @group saucelabs
     */
    public function testEditUserLastName() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Admin
        $user = User::get('admin');

        // And I am logged in on the user workspace
        $this->loginAs($user['Username']);
        $this->gotoWorkspace('user');

        // And I am editing a user
        $user = User::get('betty');
        $this->gotoEditUser($user['id']);

        // When I click on name input text field
        $this->click('js_field_last_name');

        // And I empty the name input text field value
        // And I enter a new value
        $newname = 'modifiedlastname';
        $this->inputText('js_field_last_name', $newname);

        // And I click save
        $this->click('.edit-user-dialog input[type=submit]');

        // Then I can see a success notification
        $this->assertNotification('app_users_edit_success');

        // And I can see that the user last name have changed in the overview
        $this->assertElementContainsText('#js_wsp_users_browser .tableview-content', $newname);

        // and I can see that the name has changed in the sidebar
        $this->assertElementContainsText('#js_user_details .name', $newname);

        // When I click edit button
        $this->click('js_user_wk_menu_edition_button');

        // Then I can see the new name in the edit user dialog
        $this->assertInputValue('js_field_last_name', $newname);
    }

    /**
     * Scenario: As an admin I can modify the role of a non admin user to admin
     *
     * Given I am admin
     * And   I am logged in on the user workspace
     * And   I am editing a user who is not an admin
     * When  I check the admin role
     * And   I click save
     * Then  I can see a success notification
     * And   I can see the admin role is visible in the sidebar
     * When  I click edit button
     * Then  I can see the new last name in the edit user dialog
     *
     * @group AD
     * @group user
     * @group edit
     */
    public function testEditUserRoleChangeToAdmin() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Admin
        $user = User::get('admin');

        // And I am logged in on the user workspace
        $this->loginAs($user['Username']);
        $this->gotoWorkspace('user');

        // And I am editing a user
        $betty = User::get('betty');

        // And I edit the user betty
        $u1 = array(
        'id' => $betty['id'],
        'admin' => true
        );
        $this->editUser($u1);

        // I should see that the user is marked as admin in the sidebar
        $this->assertElementContainsText('#js_user_details .role', 'Admin');

        // When I click edit button
        $this->click('js_user_wk_menu_edition_button');

        // Then I can see the admin role checked in the role checkboxes
        $this->find('#js_field_role_id .role-admin input[type=checkbox][checked=checked]');

        // When I log out
        $this->logout();

        // And I log in as the user who has been edited
        $user = User::get('betty');

        $this->loginAs($user['Username']);

        // And go to the user workspace
        $this->gotoWorkspace('user');

        // Assert that the user has admin capabilities.
        // Observe that create button is visible
        $this->assertVisibleByCss('js_wsp_create_button');

        // Observe that edit button is visible
        $this->assertVisibleByCss('js_user_wk_menu_edition_button');

        // Observe that delete button is visible
        $this->assertVisibleByCss('js_user_wk_menu_deletion_button');
    }

    /**
     * Scenario: As an admin I can modify the role of an admin user to non admin
     *
     * Given I am admin
     * And   I am logged in on the user workspace
     * And   I created a new admin user
     * And   I logout
     * And   I follow the setup procedure as the new user
     * And   I am logged in automatically
     * And   I logout
     * When  I login as admin
     * And   I go to user workspace
     * And   I edit the newly created user
     * And   I uncheck the admin role
     * And   I click save
     * Then  I can see a success notification
     * And   I can see the user role is visible in the sidebar, and not admin role
     * When  I log out
     * And   I log in as the newly created user
     * And   I go to user workspace
     * Then  I should not see the create button
     * And   I should not see the edit button
     * And   I should not see the delete button
     *
     * @group AD
     * @group user
     * @group edit
     */
    public function testEditUserRoleChangeToNonAdmin() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Admin
        $user = User::get('admin');

        // And I am logged in on the user workspace
        $this->loginAs($user['Username']);
        $this->gotoWorkspace('user');

        // Create user
        $newUser = [
        'first_name' => 'John',
        'last_name'  => 'Doe',
        'username'   => 'johndoe@passbolt.com',
        'admin'      => true
        ];
        $this->createUser($newUser);

        // Log out.
        $this->logout();

        // As new user, access the email sent after accoun creation
        $this->getUrl('seleniumtests/showlastemail/' . urlencode($newUser['username']));
        // Follow the link in the email.
        $this->followLink('get started');
        // Wait until I am sure that the page is loaded.
        $this->waitUntilISee('.plugin-check-wrapper', '/Plugin check/');
        // Go to login page. we don't need to complete the setup since we just want to check the login.
        $this->completeSetupWithKeyImport(
            [
            'private_key'=>file_get_contents(Gpgkey::get(['name' => 'johndoe'])['filepath'])
            ]
        );

        // Given I am Admin
        $user = User::get('admin');

        // And I am logged in on the user workspace
        $this->loginAs($user['Username']);
        $this->gotoWorkspace('user');

        // When I edit the new user
        $this->goToEditUser($newUser);

        // And I unselect the admin role
        $this->checkCheckbox('#js_field_role_id .role-admin input[type=checkbox]');

        // And I submit the changes
        $this->click('.edit-user-dialog input[type=submit]');

        // Then I should see a success message
        $this->assertNotification('app_users_edit_success');

        // And I should see that the user is marked as admin in the sidebar
        $this->assertElementContainsText('#js_user_details .role', 'User');

        // When I logout
        $this->logout();

        // Through the dummies, we can predict the user that was created (predictible uuid).
        $user = User::get('john');

        // And I login again as the newly created user
        $this->loginAs($newUser['username']);

        // And go to the user workspace
        $this->gotoWorkspace('user');

        // Assert that the user doesn't have admin capabilities
        // Observe that create button is not visible
        $this->assertNotVisible('js_wsp_create_button');

        // Observe that edit button is not visible
        $this->assertNotVisible('js_user_wk_menu_edition_button');

        // Observe that delete button is not visible
        $this->assertNotVisible('js_user_wk_menu_deletion_button');
    }

    /**
     * Scenario: As a admin I shouldn't be able to edit my own role
     *
     * Given I am logged in as admin in the user workspace
     * When  I click on my own name in the users list
     * And   I click on the edit button
     * Then  I should see a field first name
     * And   I should see a field last name
     * And   I should see that the checkbox role is disabled
     *
     * @group AD
     * @group user
     * @group edit
     */
    public function testEditUserOwnAdminRole() 
    {
        // Given I am Admin
        $user = User::get('admin');

        // And I am logged in on the user workspace
        $this->loginAs($user);

        // And I go to user workspace
        $this->gotoWorkspace('user');

        // Edit admin
        $this->GoToEditUser($user['id']);

        // Check that admin role checkbox is disabled.
        $this->assertElementAttributeEquals(
            $this->find('#js_field_role_id .role-admin input[type=checkbox]'),
            'disabled',
            'true'
        );
    }

    /**
     * Scenario: As user B I can see the changes are reflected when user A has edited a user I can see
     *
     * Given I am Admin
     * And   I am logged in on the user workspace
     * And   I edit a user that is active, so ada can see it
     * And   I logout
     * And   I am Ada
     * And   I am logged in on the user workspace
     * Then  I can see the user with the new name
     *
     * @group AD
     * @group user
     * @group edit
     */
    public function testEditUserUserAEditUserBCanSee() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Ada
        $user = User::get('admin');

        // And I am logged in on the user workspace
        $this->loginAs($user);

        // And I go to user workspace
        $this->gotoWorkspace('user');

        $betty = User::get('betty');
        // And I edit the user betty
        $u1 = array(
        'id' => $betty['id'],
        'first_name' => 'thisisnotbetty'
        );
        $this->editUser($u1);

        // And I logout
        $this->getUrl('logout');

        // And I am Ada
        $user = User::get('carol');

        // And I am logged in on the user workspace
        $this->loginAs($user);

        // And I go to user workspace
        $this->gotoWorkspace('user');

        // Then I could see
        $this->assertElementContainsText('#js_wsp_users_browser .tableview-content', $u1['first_name'] . ' ' . $betty['LastName']);
    }

    /**
     * Scenario: As user I can see validation error messages while editing a user and inputting bad information
     *
     * Given I am Admin
     * And   I am logged in on the user workspace
     * And   I edit a user
     * And   I empty the field first name
     * And   I empty the field last name
     * When  I press the enter key on the keyboard
     * Then  I see an error message saying that the first name is required
     * And   I see an error message saying that the last name is required
     * When  I enter '&' as a first name
     * And   I enter '&' as a last name
     * And   I click on the save button
     * Then  I see an error message saying that the first name contain invalid characters
     * And   I see an error message saying that the last name contain invalid characters
     * When  I enter 'aa' as a first name
     * And   I enter 'aa' as a last name
     * Then  I see an error message saying that the length of first name should be between x and x characters
     * And   I see an error message saying that the length of last name should be between x and x characters
     *
     * @group AD
     * @group user
     * @group edit
     */
    public function testEditUserErrorMessages() 
    {
        // Given I am Ada
        $user = User::get('admin');

        // And I am logged in on the user workspace
        $this->loginAs($user);

        // And I go to user workspace
        $this->gotoWorkspace('user');

        // Edit betty
        $betty = User::get('betty');
        $this->GoToEditUser($betty['id']);

        // And I empty the first name input field
        $this->emptyFieldLikeAUser('js_field_first_name');

        // And I empty the first name input field
        $this->emptyFieldLikeAUser('js_field_last_name');

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

        // When I enter & as a first name
        $this->inputText('js_field_first_name', '&');

        // When I enter & as a last name
        $this->inputText('js_field_last_name', '&');

        // And I click save
        $this->click('.edit-user-dialog input[type=submit]');

        // Then I see an error message saying that the first name contain invalid characters
        $this->assertVisibleByCss('#js_field_first_name_feedback.error.message');
        $this->assertElementContainsText(
            $this->find('js_field_first_name_feedback'), 'should only contain alphabets'
        );

        // Then I see an error message saying that the first name contain invalid characters
        $this->assertVisibleByCss('#js_field_last_name_feedback.error.message');
        $this->assertElementContainsText(
            $this->find('js_field_last_name_feedback'), 'should only contain alphabets'
        );

        // And I enter aa as a first name
        $this->inputText('js_field_first_name', 'a');
        $this->assertVisibleByCss('#js_field_first_name_feedback.error.message');
        $this->assertElementContainsText(
            $this->find('js_field_first_name_feedback'), 'First name should be between'
        );

        // And I enter aa as a last name
        $this->inputText('js_field_last_name', 'a');
        $this->assertVisibleByCss('#js_field_last_name_feedback.error.message');
        $this->assertElementContainsText(
            $this->find('js_field_last_name_feedback'), 'Last name should be between'
        );
    }
}