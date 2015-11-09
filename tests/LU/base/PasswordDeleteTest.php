<?php
/**
 * Feature :  As a user I can delete passwords
 *
 * Scenarios
 * As a user I can delete a password using a right click
 * As a user I can delete a password using the button in the action bar
 * As user B I can see a password that user A shared with me and deleted.
 * As a user I should not be able to delete a password when I have read access
 *
 * @copyright    (c) 2015-present Bolt Software Pvt. Ltd.
 * @licence      GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
 */
class PasswordDeleteTest extends PassboltTestCase
{

    /**
     * Scenario: As a user I can delete a password using a right click
     *
     * And      I am Ada
     * And      I am logged in on the password workspace
     * When     I right click on a password I have update right on
     * Then     I select the delete option in the contextual menu
     * Then     I should see a success notification message saying the password is deleted
     * And      I should not see the password in the list anymore
     */
    public function testDeletePasswordRightClick() {
        // And I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // When I click a password I have update right on
        $resource = Resource::get(array('user' => 'ada', 'permission' => 'update'));
        $this->rightClickPassword($resource['id']);

        // Then I select the delete option in the contextual menu
        $this->click('#js_password_browser_menu_delete a');

	    // Assert that the confirmation dialog is displayed.
	    $this->assertConfirmationDialog('Do you really want to delete password ?');

	    // Click ok in confirmation dialog.
	    $this->confirmActionInConfirmationDialog();

        // Then I should see a success notification message saying the password is deleted
        $this->assertNotification('app_resources_delete_success');

        // And I should not see the password in the list anymore
        $this->assertTrue($this->isNotVisible('resource_' . $resource['id']));

        // Since content was edited, we reset the database
        $this->resetDatabase();
    }

    /**
     * Scenario: As a user I can delete a password using the button in the action bar
     *
     * And      I am Ada
     * And      I am logged in on the password workspace
     * When     I click a password I have update right on
	 * And		I click on the more button
	 * And 		I click on the delete link
     * Then     I should see a success notification message saying the password is deleted
     * And      I should not see the password in the list anymore
     */
    public function testDeletePasswordButton() {
        // And I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // When I click a password I have update right on
        $resource = Resource::get(array('user' => 'ada', 'permission' => 'update'));
        $this->clickPassword($resource['id']);

        // And I click on the more button
		$this->click('js_wk_menu_more_button');

		// When I click on the delete link
		$this->clickLink('delete');

	    // Assert that the confirmation dialog is displayed.
	    $this->assertConfirmationDialog('Do you really want to delete password ?');

	    // Click ok in confirmation dialog.
	    $this->confirmActionInConfirmationDialog();

        // Then I should see a success notification message saying the password is deleted
        $this->assertNotification('app_resources_delete_success');

        // And I should not see the password in the list anymore
        $this->assertTrue($this->isNotVisible('resource_' . $resource['id']));

        // Since content was edited, we reset the database
        $this->resetDatabase();
    }

    /**
     * Scenario: As a user A I can see a password that user B shared with me and deleted.
     *
     * Given    I am Betty
     * And      I am logged in on the password worskpace
     * Then     I can see a password shared with ada in the list
     * When     I logout
     * And      I am Ada
     * And      I am logged in on the password workspace
     * When     I click on the password shared with betty
	 * And		I click on the more button
	 * And 		I click on the delete link
     * Then     I should see a success notification message saying the password is deleted
     * And      I should not see the password deleted by ada in the list anymore
     * When     I logout
     * And      I am Betty
     * And      I am logged in on the password worskpace
     * Then     I cannot see the password in the list anymore
     */
    public function testDeletePasswordShared() {
        // Given I am Ada
        $userA = User::get('ada');
        $this->setClientConfig($userA);

        // And I am logged in on the password workspace
        $this->loginAs($userA);

        // Then I can see a password shared with ada in the list
        $resource = Resource::get(array('user' => 'ada', 'id' => Uuid::get('resource.id.apache')));
        $this->assertVisible('resource_' . $resource['id']);

        // When I logout
        $this->logout();

        // And I am Betty
        $userB = User::get('betty');
        $this->setClientConfig($userB);

        // And I am logged in on the password workspace
        $this->loginAs($userB);

        // When I click on the password shared with Ada
        $this->assertVisible('resource_' . $resource['id']);
        $this->clickPassword($resource['id']);

		// And I click on the more button
		$this->click('js_wk_menu_more_button');

		// When I click on the delete link
		$this->clickLink('delete');

	    // Assert that the confirmation dialog is displayed.
	    $this->assertConfirmationDialog('Do you really want to delete password ?');

	    // Click ok in confirmation dialog.
	    $this->confirmActionInConfirmationDialog();

        // Then I should see a success notification message saying the password is deleted
        $this->assertNotification('app_resources_delete_success');

        // And I should not see the password in the list anymore
        $this->assertNotVisible('resource_' . $resource['id']);

        // When I logout
        $this->logout();

        // And I am Betty
        $this->setClientConfig($userB);

        // And I am logged in on the password worskpace
        $this->loginAs($userB);

        // And I should not see the password deleted by ada in the list anymore
        $this->assertNotVisible('resource_' . $resource['id']);

        // Since content was edited, we reset the database
        $this->resetDatabase();
    }

    /**
     * Scenario: As a user I should not be able to delete a password when I have read access
     *
     * Given    I am Betty
     * And      I am logged in on the password workspace
     * When     I click on a password I have view right
	 * And		I click on the more button
     * Then     I should see that the delete button is disabled
	 * And 		I click on the delete link
     * Then     I can still see the password in the list
     * When     I right click on the password I have view right
     * Then     I should see the delete option is disabled in the contextual dialog
     * When     I click on the delete option in the contextual dialog
     * Then     I can still see the password in the list
     */
    public function testDeletePasswordIDontOwn() {
        // Given I am Betty
        $user = User::get('betty');
        $this->setClientConfig($user);

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // I click on a password I have view right
        $resource = Resource::get(array('user' => 'betty', 'permission' => 'read'));
		$this->clickPassword($resource['id']);

		// And I click on the more button
		$this->click('js_wk_menu_more_button');

		// Then I should see that the delete button is disabled
		$this->assertVisible('#js_wk_menu_delete_action.disabled');

		// When I click on the delete link
		$this->clickLink('delete');

        // Then I can still see the password in the list
        $this->assertVisible('resource_' . $resource['id']);

        // When I right click on the password I have view right
        $this->rightClickPassword($resource['id']);

        // Then I should see the delete option is disabled in the contextual dialog
        // @TODO PASSBOLT-1028
        //$this->assertElementContainsText($this->findByCss('#js_contextual_menu .disabled'),'delete');

        // When I click on the delete option in the contextual dialog
        $this->click('#js_password_browser_menu_delete a');

        // Then I can still see the password in the list
        $this->assertVisible('resource_' . $resource['id']);
    }
}