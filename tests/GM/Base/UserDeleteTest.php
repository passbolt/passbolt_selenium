<?php
/**
 * Feature : As a GM regarding the delete user feature.
 *
 * Scenarios :
 *  - As a GM I should receive a notification when a user who is part of one (or more) groups I manage is deleted.
 *
 * @copyright (c) 2017 Passbolt SARL
 * @licence   GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class GMUserDeleteTest extends PassboltTestCase
{

    /**
     * Scenario: As a GM I should receive a notification when a user who is part of one (or more) groups I manage is deleted.
     *
     * Given        I am logged in as an admin
     * And   I am on the users workspace
     * When  I delete a user
     * Then  I should see a success notification message
     * When  I access last email sent to one of the group manager
     * Then  I should see the expected email title
     *     And   I should see the expected email content
     * When  I access last email sent to another group manager
     * Then  I should see the expected email title
     *     And   I should see the expected email content
     */
    public function testDeleteUserEmailNotification() 
    {
        $this->resetDatabaseWhenComplete();

        // Given I am an administrator.
        $user = User::get('admin');


        // I am logged in as admin
        $this->loginAs($user);

        // And I am on the users workspace
        $this->gotoWorkspace('user');

        // When I delete a user
        $userU = User::get('ursula');
        $this->rightClickUser($userU['id']);
        $this->click('#js_user_browser_menu_delete a');
        $this->assertConfirmationDialog('Do you really want to delete user ?');
        $this->confirmActionInConfirmationDialog();

        // Then I should see a success notification message
        $this->assertNotification('app_users_delete_success');

        // When I access last email sent to one of the group manager
        $userP = User::get('ping');
        $this->getUrl('seleniumtests/showlastemail/' . $userP['Username']);

        // Then I should see the expected email title
        $this->assertMetaTitleContains(sprintf('%s deleted a user', $user['FirstName']));

        // And I should see the expected email content
        $this->assertElementContainsText('bodyTable', 'User: ' . $userU['FirstName'] . ' ' . $userU['LastName']);
        $this->assertElementContainsText('bodyTable', 'The user is now deleted on passbolt');
        $this->assertElementContainsText('bodyTable', 'IT support (Group manager)');
        $this->assertElementContainsText('bodyTable', 'Human resource (Member)');

        // When I access last email sent to another group manager
        $userT = User::get('thelma');
        $this->getUrl('seleniumtests/showlastemail/' . $userT['Username']);

        // Then I should see the expected email title
        $this->assertMetaTitleContains(sprintf('%s deleted a user', $user['FirstName']));

        // And I should see the expected email content
        $this->assertElementContainsText('bodyTable', 'User: ' . $userU['FirstName'] . ' ' . $userU['LastName']);
        $this->assertElementContainsText('bodyTable', 'The user is now deleted on passbolt');
        $this->assertElementContainsText('bodyTable', 'Human resource (Member)');
    }

}