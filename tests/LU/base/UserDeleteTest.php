<?php
/**
 * Feature :  As a LU regarding the delete user feature.
 *
 * Scenarios :
 *  - As LU I should be able to get a clear feedback at login if my account has been deleted.
 *
 * @copyright (c) 2017 Passbolt SARL
 * @licence   GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class LUUserDeleteTest extends PassboltTestCase
{

    /**
     * Scenario: As LU I should be able to get a clear feedback at login if my account has been deleted.
     * Given        I am logged in as admin in the user workspace
     * And          I click on the user
     * And          I click on delete button
     * Then         I should see a confirmation dialog
     * When         I click ok in the confirmation dialog
     * Then         I should see a confirmation message
     * When         I log out
     * And          I become the user I deleted
     * And          I go to the login page
     * Then         I should see a feedback telling me that my account doesn't exist on server
     */
    public function testDeletedUserGetFeedback() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Admin
        $user = User::get('admin');
        

        // And I am logged in on the user workspace
        $this->loginAs($user);

        // Go to user workspace
        $this->gotoWorkspace('user');

        // When I right click on a user
        $userU = User::get('ursula');
        $this->clickUser($userU['id']);

        // Then  I select the delete option in the contextual menu
        $this->click('js_user_wk_menu_deletion_button');

        // Assert that the confirmation dialog is displayed.
        $this->assertConfirmationDialog('Do you really want to delete user ?');

        // Click ok in confirmation dialog.
        $this->confirmActionInConfirmationDialog();

        // Then  I should see a success notification message saying the user is deleted
        $this->assertNotification('app_users_delete_success');

        // Log out.
        $this->logout();

        // I become the user I deleted.
        $this->setClientConfig($userU);

        // When I go to login.
        $this->getUrl('login');

        // I should see a feedback telling me that the user doesn't exist on server.
        $this->waitUntilISee('html.server-not-verified.server-no-user');
        $this->waitUntilISee('.plugin-check.gpg.error', '/There is no user associated with this key/');
        $this->waitUntilISee('.users.login.form .feedback', '/The supplied account does not exist/');
    }

}