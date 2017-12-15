<?php
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverSelect;

/**
 * Feature: As an administrator I can create groups
 *
 * Scenarios :
 *  - As an administrator I can click on create group and see that the create group dialog exists.
 *  - As an administrator I can open close the create group dialog
 *  - As an administrator I should not be able to create a group with an incorrect name
 *  - As an administrator I can't create a group with a name already used by another group
 *  - As an administrator I can edit the group members while creating a group.
 *  - As an administrator I should be able to delete a group user while creating a group
 *  - As an administrator I should be able to create a group successfully.
 *  - As an administrator while creating a group I can't choose inactive users as new members
 *  - As a user I should receive a notification when I am added to a group
 *
 * @copyright (c) 2017-present Passbolt SARL
 * @licence   GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
namespace Tests\AD\Base;

use App\PassboltTestCase;
use Data\Fixtures\User;
class ADGroupCreateTest extends PassboltTestCase
{

    /**
     * Scenario: As an administrator I can click on create group and see that the create group dialog exists.
     * Given        I am admin
     * And   I am logged in
     * When  I go to user workspace
     * Then  I should see a button create in the actions panel
     * When  I click on the create button
     * Then  I should see a submenu
     * And   I should see an item create group in the submeny
     * When  I click on create group
     * Then  I should see a dialog with title "Create group"
     * And   I should see a name field and label
     * And   I should see that the name field is required
     * And   I should see a Group members section
     * And   I should see a warning message saying that "The group is empty, please add a group manager."
     * And   I should see an iframe
     * And   I should see that this iframe contains the Add people label
     * And   I should see a text field with a security token inside this iframe
     * And   I should see a Save button
     * And   I should see a cancel button
     * And   I should see a button close to close the dialog
     */
    public function testCreateGroupDialogExist() 
    {
        // Given I am an administrator.
        $user = User::get('admin');

        // I am logged in as admin
        $this->loginAs($user);

        // Go to user workspace
        $this->gotoWorkspace('user');

        // Then I see the create button
        $this->assertElementContainsText(
            $this->findByCss('.main-action-wrapper'),
            'create'
        );

        // Create a new group
        $this->gotoCreateGroup();

        // Then I see the create group dialog
        $this->assertVisibleByCss('.edit-group-dialog');

        // And I see the title is set to "Create group"
        $this->assertElementContainsText(
            $this->findByCss('.dialog'), 'Create group'
        );

        // And I see the name text input and label is marked as mandatory
        $this->assertVisibleByCss('.edit-group-dialog input[type=text]#js_field_name.required');
        $this->assertVisibleByCss('.edit-group-dialog label[for=js_field_name]');

        // And I see a group members section.
        $this->assertVisible('js_group_members');
        $this->assertElementContainsText('#js_group_members .input.required label', 'Group members');

        // And I see a warning message saying that the group is empty
        $this->assertElementContainsText('#js_group_members .message.warning', 'The group is empty, please add a group manager.');

        // And I see a section add members
        $this->assertVisibleByCss('js_group_members_add');

        // And I see the Add people iframe
        $this->assertVisibleByCss('#js_group_members_add #passbolt-iframe-group-edit');

        // When I switch to the add people iframe
        $this->goIntoAddUserIframe();

        // I should see a section inside the iframe to add people
        $this->assertVisibleByCss('.user-add');
        $this->assertElementContainsText('.user-add', 'Add people');

        // And I see the security token on the Add people textfield
        $this->assertSecurityToken($user, 'group');

        // When I switch back out of the password iframe
        $this->goOutOfIframe();

        // I should see the save button
        // And I should see that it is disabled by default
        // And I should see that the save button is still disabled
        $saveButton = $this->find('.edit-group-dialog a.button.primary');
        $this->assertElementAttributeEquals(
            $saveButton,
            'disabled',
            'true'
        );

        // And I see the cancel button
        $this->assertVisibleByCss('.edit-group-dialog a.cancel');

        // And I see the close dialog button
        $this->assertVisibleByCss('.edit-group-dialog a.dialog-close');
    }

    /**
     * Scenario: As an admin I can open close the create group dialog
     *
     * Given I am an Admin
     * And   I am logged in
     * And   I am on the people workspace
     * When  I click on the create group button
     * Then  I see the create group dialog
     * When  I click on the cancel button
     * Then  I should not see the create group dialog
     * When  I click on the create group button
     * Then  I see the create group dialog
     * When  I click on the close dialog button
     * Then  I should not see the create group dialog
     * When  I click on the create group button
     * Then  I see the create group dialog
     * When  I press the keyboard escape key
     * Then  I should not see the create group dialog
     */
    public function testCreateGroupDialogOpenClose() 
    {
        // Given that I am Ada
        $user = User::get('admin');
        

        // And I am logged in and on the people workspace
        $this->loginAs($user);

        // Go to user workspace
        $this->gotoWorkspace('user');

        // Create a new group
        $this->gotoCreateGroup();

        // Then I see the create group dialog
        $this->assertVisibleByCss('.edit-group-dialog');

        // When I click on the cancel button
        $this->findByCss('.edit-group-dialog a.cancel')->click();

        // Then I should not see the create group dialog
        $this->assertNotVisible('.edit-group-dialog');

        // Create a new group
        $this->gotoCreateGroup();

        // Then I see the create group dialog
        $this->assertVisibleByCss('.edit-group-dialog');

        // When I click on the close dialog button
        $this->findByCss('.edit-group-dialog a.dialog-close')->click();

        // Then I should not see the create group dialog
        $this->assertNotVisible('.edit-group-dialog');

        // -- WITH ESCAPE --
        // Create a new group
        $this->gotoCreateGroup();

        // Then I see the create group dialog
        $this->assertVisibleByCss('.edit-group-dialog');

        // When I click on the escape key
        $this->pressEscape();

        // Then I should not see the create group dialog
        $this->assertTrue($this->isNotVisible('.edit-group-dialog'));

    }

    /**
     * Scenario: As an administrator I should not be able to create a group with an incorrect name
     * Given that   I am a logged in administrator
     *   And I am on the create group dialog
     * When  I click on the group name field
     *   And I press enter
     * Then  I should see an error message saying that the field name is required
     * When  I enter & as a field name
     *  And         I press enter
     * Then  I should see an error message saying that it should be alphanumeric only
     *  And         I should see that the submit button is disabled
     */
    public function testCreateGroupNameValidation() 
    {
        // Given I am an administrator.
        $user = User::get('admin');
        

        // I am logged in as admin
        $this->loginAs($user);

        // Go to user workspace
        $this->gotoWorkspace('user');

        // Create a new group
        $this->gotoCreateGroup();

        // When I click on the name input field
        $this->click('js_field_name');

        // And I press enter
        $this->pressEnter();

        // Then I see an error message saying that the name is required
        $this->assertVisibleByCss('#js_field_name_feedback.error.message');
        $this->assertElementContainsText(
            $this->find('js_field_name_feedback'), 'is required'
        );

        // When I enter & as a name
        $this->inputText('js_field_name', '&');
        // And I press enter
        $this->pressEnter();
        // Then I see an error message saying that the name contain invalid characters
        $this->assertVisibleByCss('#js_field_name_feedback.error.message');
        $this->assertElementContainsText(
            $this->find('js_field_name_feedback'), 'Alphanumeric only'
        );

        // And I should see that the save button is still disabled
        $this->assertElementAttributeEquals(
            $this->find('.edit-group-dialog a.button.primary'),
            'disabled',
            'true'
        );
    }

    /**
     * Scenario: As an administrator I can't create a group with a name already used by another group
     * Given that   I am logged in as an administrator
     * And   I am on the users workspace
     * When  I create a group
     *  And         I fill 'Accounting' for name
     *  And         I add Ada as group manager
     *  And         I click on submit
     * Then  I should see a notification saying that the group couldn't be created
     *  And         I should see an error message under the name field
     *  And         I should see that this error message says that the group name is already in use
     */
    public function testCreateGroupNameAlreadyExists() 
    {
        // Given I am an administrator.
        $user = User::get('admin');
        

        // I am logged in as admin
        $this->loginAs($user);

        // Go to user workspace
        $this->gotoWorkspace('user');

        // Create a new group
        $this->gotoCreateGroup();

        // When I enter & as a name
        $this->inputText('js_field_name', 'Accounting');

        // Add ada as a group member.
        $ada = User::get('ada');
        $this->searchGroupUserToAdd($ada, $user);
        $this->addTemporaryGroupUser($ada);

        // And I click submit.
        $this->click('.edit-group-dialog a.button.primary');

        // Then I should see a success notification message saying the group couldn't be created
        $this->assertNotification('app_groups_add_error');

        // Then I see an error message saying that the name contain invalid characters
        $this->waitUntilISee('#js_field_name_feedback.error.message');

        $this->assertElementContainsText(
            $this->find('js_field_name_feedback'), 'The group name provided is already used by another group'
        );
    }

    /**
     * Scenario: As an administrator I can edit the group members while creating a group
     * Given that   I am logged in as an administrator
     *  And         I am on the users workspace
     * When  I create a new group
     * Then  I should see the create group dialog
     *  And         I should see the Add Members section
     * When  I add ada as a first group member
     * Then  I should see a new entry in the group members list
     *  And         I should see that the user is not available anymore in the autocomplete list
     *  And         I should see that the user has been added as a group manager
     *  And         I should see that I can't edit his membership role
     *  And         I should see that I can't remove him from the group members
     *  And         I should see that the save button is now enabled
     * When  I add carol as a second group member
     * Then  I should see a new entry in the group members list for carol
     *  And         I should see that carol has been added as a Member, and not a group manager
     *  And         I should see that I can edit Carol's role
     *  And         I should see that I can delete carole entry in the members list
     * When  I set carol's role as Group manager
     * Then  I should see that ada's role is now editable
     *  And         I should see that Ada's entry can now be removed
     * When  I set ada's role as a Member
     * Then  I should see that carol's role can't be edited anymore
     *  And         I should see that carol's entry can't be removed anymore
     *  And         I should see that the save button is still enabled
     */
    public function testCreateGroupGroupMembersValidation() 
    {
        // Given I am an administrator.
        $user = User::get('admin');
        

        // I am logged in as admin
        $this->loginAs($user);

        // Go to user workspace
        $this->gotoWorkspace('user');

        // Create a new group
        $this->gotoCreateGroup();

        // And I should see that the save button is still disabled
        $this->assertElementAttributeEquals(
            $this->find('.edit-group-dialog a.button.primary'),
            'disabled',
            'true'
        );

        $ada = User::get('ada');
        $this->searchGroupUserToAdd($ada, $user);
        $this->addTemporaryGroupUser($ada);

        // Make sure that the user is not available anymore in the autocomplete list.
        $available = true;
        try {
            $this->searchGroupUserToAdd($ada, $user);
        }
        catch (Exception $e) {
            $available = false;
        }
        $this->assertFalse($available, "The user Ada shouldn't be available in the autocomplete list");

        // Empty text typed in the box.
        $this->goIntoAddUserIframe();
        $this->emptyFieldLikeAUser('js_group_edit_form_auto_cplt');
        $this->click('.security-token');
        $this->goOutOfIframe();


        $props = $this->getTemporaryGroupUserProperties($ada);
        // The user should be set automatically as group manager.
        $this->assertEquals($props['role'], 'Group manager');
        // It should not be possible to change the role of the user
        $this->assertTrue($props['role_disabled']);
        // It should not be possible to delete the entry
        $this->assertTrue($props['delete_disabled']);


        // And I should see that the save button is now enabled
        $this->assertElementAttributeEquals(
            $this->find('.edit-group-dialog a.button.primary'),
            'disabled',
            null
        );

        // Add another user to group.
        $carol = User::get('carol');
        $this->searchGroupUserToAdd($carol, $user);
        $this->addTemporaryGroupUser($carol);


        $props = $this->getTemporaryGroupUserProperties($carol);
        // The user should be set automatically as member.
        $this->assertEquals($props['role'], 'Member');
        // It should be possible to change the role of the user
        $this->assertFalse($props['role_disabled']);
        // It should be possible to delete the entry
        $this->assertFalse($props['delete_disabled']);

        // The first user should still be disabled for edit.
        $props = $this->getTemporaryGroupUserProperties($ada);
        // It should not be possible to change the role of the user
        $this->assertTrue($props['role_disabled']);
        // It should not be possible to delete the entry
        $this->assertTrue($props['delete_disabled']);

        // When I set carol as a group manager
        $carolElt = $this->getTemporaryGroupUserElement($carol);
        $select = new WebDriverSelect($carolElt->findElement(WebDriverBy::cssSelector('.js_group_user_is_admin')));
        $select->selectByVisibleText('Group manager');

        // Then Ada should now become available for edit
        $props = $this->getTemporaryGroupUserProperties($ada);
        // It should be possible to change the role of the user
        $this->assertFalse($props['role_disabled']);
        // It should be possible to delete the entry
        $this->assertFalse($props['delete_disabled']);


        // When I set Ada as a simple Member.
        $adaElt = $this->getTemporaryGroupUserElement($ada);
        $select = new WebDriverSelect($adaElt->findElement(WebDriverBy::cssSelector('.js_group_user_is_admin')));
        $select->selectByVisibleText('Member');

        // Then Carol should become not editable
        $props = $this->getTemporaryGroupUserProperties($carol);
        // It should be possible to change the role of the user
        $this->assertTrue($props['role_disabled']);
        // It should be possible to delete the entry
        $this->assertTrue($props['delete_disabled']);

        // And I should see that the save button is still enabled
        $this->assertElementAttributeEquals(
            $this->find('.edit-group-dialog a.button.primary'),
            'disabled',
            null
        );
    }

    /**
     * Scenario: As an administrator I should be able to delete a group user while creating a group
     * Given that   I am logged in as an administrator
     * When  I create a new group
     *  And         I add ada as a first member
     * Then  I should see that ada can't be removed from the group (the delete button is disabled)
     * When  I add Carol as a second member
     *  And         I click on the delete button to remove Carol entry
     * Then  I should see that carol has disappeared from the list
     *  And         I should see that carol is available again from the autocomplete list
     */
    public function testCreateGroupGroupMembersDelete() 
    {
        // Given I am an administrator.
        $user = User::get('admin');
        

        // I am logged in as admin
        $this->loginAs($user);

        // Go to user workspace
        $this->gotoWorkspace('user');

        // Create a new group
        $this->gotoCreateGroup();

        // And I should see that the save button is still disabled
        $this->assertElementAttributeEquals(
            $this->find('.edit-group-dialog a.button.primary'),
            'disabled',
            'true'
        );

        $ada = User::get('ada');
        $this->searchGroupUserToAdd($ada, $user);
        $this->addTemporaryGroupUser($ada);

        // Then Ada should now become available for edit
        $props = $this->getTemporaryGroupUserProperties($ada);
        // It should not be possible to delete the entry
        $this->assertTrue($props['delete_disabled']);

        $carol = User::get('carol');
        $this->searchGroupUserToAdd($carol, $user);
        $this->addTemporaryGroupUser($carol);

        $props = $this->getTemporaryGroupUserProperties($carol);
        // It should be possible to delete the entry
        $this->assertFalse($props['delete_disabled']);

        $carolElt = $this->getTemporaryGroupUserElement($carol);
        // I should see that the user can't be deleted (because he is the only group manager
        $deleteBtn = $carolElt->findElement(WebDriverBy::cssSelector('.js_group_user_delete'));
        $deleteBtn->click();

        // The entry should have disappeared.
        $elt = null;
        try {
            $elt = $this->getTemporaryGroupUserElement($carol);
        }
        catch (Exception $e) {
            // Do nothing. Element will remain null.
        }
        // Make sure that the element was not returned (because it doesn't exist).
        $this->assertEquals($elt, null);

        // And Carol should be available again in the autocomplete list.
        $this->searchGroupUserToAdd($carol, $user);
    }

    /**
     * Scenario: As an administrator I should be able to create a group successfully
     * Given that   I am a logged in administrator
     * When  I click on create button
     *  And         I select create a group
     * Then  I should see the create group dialog
     * When  I enter "jeankevin" for name
     *  And         I add ada as the group manager
     *  And         I click on save
     * Then  I should see a success notification message
     *  And         I should see that the dialog disappears
     *  And         I should see that the group has been created in the groups list (on the left)
     */
    public function testCreateGroupSuccess() 
    {
        $this->resetDatabaseWhenComplete();

        // Given I am an administrator.
        $user = User::get('admin');
        

        // I am logged in as admin
        $this->loginAs($user);

        // Go to user workspace
        $this->gotoWorkspace('user');

        // Create a new group
        $this->gotoCreateGroup();

        // When I click on the name input field
        $this->click('js_field_name');

        // When I enter & as a name
        $this->inputText('js_field_name', 'jeankevin');

        // Add Ada to the group list
        $ada = User::get('ada');
        $this->searchGroupUserToAdd($ada, $user);
        $this->addTemporaryGroupUser($ada);

        // And I click save.
        $this->click('.edit-group-dialog a.button.primary');

        // Assert that I see a notification
        $this->assertNotification('app_groups_add_success');

        // And I shouldn't see the create group dialog anymore.
        $this->waitUntilIDontSee('.edit-group-dialog');

        // Wait until I see a group called jean kevin in the list.
        $this->waitUntilISee("js_wsp_users_groups_list", '/jeankevin/');
    }

    /**
     * Scenario: As an administrator while creating a group I can't choose inactive users as new members
     * Given that   I am logged in as an administrator
     * And   I am on the user workspace
     * When  I am creating a new group
     *  And         I enter the name of an inactive user
     * Then  I shouldn't see it in the list of proposed users
     */
    public function testCreateGroupInactiveUsers() 
    {
        // Given I am logged in as an administrator
        $user = User::get('admin');
        
        $this->loginAs($user);

        // And I am on the user workspace
        $this->gotoWorkspace('user');

        // When I am creating a new group
        $this->gotoCreateGroup();

        // And I enter the name of an inactive user
        $userO = User::get('orna');
        $this->goIntoAddUserIframe();
        $this->assertSecurityToken($user, 'group');
        $this->inputText('js_group_edit_form_auto_cplt', strtolower($userO['FirstName']), true);
        $this->click('.security-token');
        $this->goOutOfIframe();

        // Then I shouldn't see it in the list of proposed users
        $this->goIntoAddUserAutocompleteIframe();
        $this->waitUntilISee('.autocomplete-content', '/No user found/i');
        $this->goOutOfIframe();
    }

    /**
     * Scenario: As a user I should receive a notification when I am added to a group
     *
     * Given that   I am a logged in administrator
     * When  I click on create button
     *  And         I select create a group
     * Then  I should see the create group dialog
     * When  I enter a group name
     *  And         I add Ada as group manager
     *  And         I add Betty as member
     *  And         I click on save
     * Then  I should see a success notification message
     * When  I access last email sent to the group manager
     * Then  I should see the expected email title
     *     And   I should see the expected email content
     * When  I access last email sent to the group member
     * Then  I should see the expected email title
     *     And   I should see the expected email content
     */
    public function testCreateGroupAddUserEmailNotification() 
    {
        $this->resetDatabaseWhenComplete();

        // Given I am an administrator.
        $user = User::get('admin');
        

        // I am logged in as admin
        $this->loginAs($user);

        // Go to user workspace
        $this->gotoWorkspace('user');

        // Create a new group
        $this->gotoCreateGroup();

        // When I click on the name input field
        $this->click('js_field_name');

        // When I enter a group name
        $groupName = 'World citizen';
        $this->inputText('js_field_name', $groupName);

        // And I add Ada as group manager
        $ada = User::get('ada');
        $this->searchGroupUserToAdd($ada, $user);
        $this->addTemporaryGroupUser($ada);

        // And I add Betty as member
        $betty = User::get('betty');
        $this->searchGroupUserToAdd($betty, $user);
        $this->addTemporaryGroupUser($betty);

        // And I click save.
        $this->click('.edit-group-dialog a.button.primary');

        // And I should see a success notification message
        $this->assertNotification('app_groups_add_success');

        // When I access last email sent to the group manager.
        $this->getUrl('seleniumtests/showlastemail/' . $ada['Username']);

        // Then I should see the expected email title
        $this->assertMetaTitleContains(sprintf('%s added you to the group %s', $user['FirstName'], $groupName));

        // And I should see the expected email content
        $this->assertElementContainsText('bodyTable', 'Name: World citizen');
        $this->assertElementContainsText('bodyTable', 'Your role: Group manager');
        $this->assertElementContainsText('bodyTable', 'As member of the group');
        $this->assertElementContainsText('bodyTable', 'And as group manager');

        // When I access last email sent to the group manager.
        $this->getUrl('seleniumtests/showlastemail/' . $betty['Username']);

        // Then I should see the expected email title
        $this->assertMetaTitleContains(sprintf('%s added you to the group %s', $user['FirstName'], $groupName));

        // And I should see the expected email content
        $this->assertElementContainsText('bodyTable', 'Name: World citizen');
        $this->assertElementContainsText('bodyTable', 'Your role: Member');
        $this->assertElementContainsText('bodyTable', 'As member of the group');
        $this->assertElementNotContainText('bodyTable', 'And as group manager');
    }
}