<?php
/**
 * Feature :  As a user I should be able to delete passwords
 *
 * Scenarios
 * As a user I should be able delete a password using a right click
 * As a user I should be able delete a password using the button in the action bar
 * As user B I should be able to see a password that user A shared with me and deleted.
 * As a user I should not be able to delete a password I do not own
 *
 * @copyright    (c) 2015-present Bolt Software Pvt. Ltd.
 * @licence      GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
 */
class PasswordDeleteTest extends PassboltTestCase
{
    /**
     * Scenario: As a user I should be able delete a password using a right click
     *
     * And      I am Ada
     * And      the database is in the default state
     * And      I am logged in on the password workspace
     * When     I right click on a password I have admin right on
     * Then     I select the delete option in the contextual menu
     * Then     I should see a success notification message saying the password is deleted
     * And      I should not see the password in the list anymore
     */
    public function testDeletePasswordRightClick() {
        // And I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

        // And the database is in the default state
        $this->PassboltServer->resetDatabase(1);

        // And I am logged in on the password workspace
        $this->loginAs($user['Username']);

        // When I click a password I have admin right on
        $resource = Resource::get(array('user' => 'ada', 'permission' => 'admin'));
        $this->rightClick($resource['id']);

        // Then I select the delete option in the contextual menu
        $this->clickLink('Delete');

        // Then I should see a success notification message saying the password is deleted
        $this->assertNotificationSuccess('successfully deleted');

        // And I should not see the password in the list anymore
        $this->assertTrue($this->isNotVisible($resource['id']));
    }

    /**
     * Scenario: As a user I should be able delete a password using the button in the action bar
     *
     * And      I am Ada
     * And      the database is in the default state
     * And      I am logged in on the password workspace
     * When     I click a password I have admin right on
     * And      I click on the delete button
     * Then     I should see a success notification message saying the password is deleted
     * And      I should not see the password in the list anymore
     */
    public function testDeletePasswordButton() {
        // And I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

        // And the database is in the default state
        $this->PassboltServer->resetDatabase(1);

        // And I am logged in on the password workspace
        $this->loginAs($user['Username']);

        // When I click a password I have admin right on
        $resource = Resource::get(array('user' => 'ada', 'permission' => 'admin'));
        $this->click($resource['id']);

        // And I click on the delete button
        $this->click('js_wk_menu_deletion_button');

        // Then I should see a success notification message saying the password is deleted
        $this->assertNotificationSuccess('successfully deleted');

        // And I should not see the password in the list anymore
        $this->assertTrue($this->isNotVisible($resource['id']));
    }

    /**
     * Scenario: As a user B I should be able to see a password that user A shared with me and deleted.
     *
     * Given    I am Betty
     * And      the database is in the default state
     * And      I am logged in on the password worskpace
     * Then     I can see the password dp1-pwd1 in the list
     * When     I logout
     * And      I am Ada
     * And      I am logged in on the password workspace
     * Then     I should not see the password dp1-pwd1 in the list anymore
     * When     I select a password i can delete
     * And      I click on the delete button
     * Then     I should see a success notification message saying the password is deleted
     * And      I should not see the password in the list anymore
     * When     I logout
     * And      I am Betty
     * And      I am logged in on the password worskpace
     * Then     I cannot see the password in the list anymore
     */
    public function testDeletePasswordShared() {

    }

    /**
     * Scenario: As a user I should not be able to delete a password I do not own
     *
     * Given    I am Betty
     * And      the database is in the default state
     * And      I click on a password I have view right
     *
     */
    public function testDeletePasswordIDontOwn() {

    }
}