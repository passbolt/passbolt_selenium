<?php
/**
 * Feature :  As a user I can edit the password description directly from the sidebar
 *
 * Scenarios
 *  - As a user I should be able to edit the description of the passwords I own in the sidebar by clicking on the edit button
 *  - As a user I should be able to edit the description of the passwords I own in the sidebar by clicking on the description
 *  - As a user I should be able to see the validation error messages for the description
 *  - As a user I should'nt be able to edit the description of a password with read access only
 *
 *
 * @copyright (c) 2017 Passbolt SARL
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class PasswordEditDescriptionTest extends PassboltTestCase {

	/**
	 * @group saucelabs
	 * Scenario :   As a user I should be able to edit the description of the passwords I own in the sidebar by clicking on the edit button
	 * Given        I am Ada and I am logged in on the password workspace
	 * Then         I should not see the sidebar and the textarea to edit the description
	 * When         I click on a password I own
	 * Then         I should see the description section in the sidebar with a link to edit the description
	 * And          I should see the current password description
	 * When         I click the edit button
	 * Then         I should see a form to edit the description
	 * And          I should see that the form should contain the description pre filled
	 * When         I enter a new description
	 * And          I click on save
	 * Then         I should not see the form anymore
	 * And          I should see the new description
	 *
	 * @throws Exception
	 */
	public function testDescriptionEditButton() {
		// Reset database at the end of test.
		$this->resetDatabaseWhenComplete();

		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);

		// And I am logged in on the password workspace
		$this->loginAs($user);

		// Make sure the description form is not visible.
		$this->assertNotVisible(".js_rs_details_edit_description textarea");

		// When I click on a password I own.
		$resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));
		$this->clickPassword($resource['id']);

		// Wait until the resource details description component is ready.
		$this->waitUntilISee('#js_pwd_details.ready');

		// I should see the edit button.
		$this->assertVisible("js_edit_description_button");

		// I should see a description.
		$this->assertElementContainsText('#js_rs_details_description', 'Apache is the world\'s most used web server software.');

		// Click on the edit button.
		$this->click("#js_edit_description_button i");

		// I should see a form to edit the description.
		$this->waitUntilISee("#js_rs_details_edit_description textarea.js_resource_description");

		// Assert that the description is correct in the textarea.
		$this->assertEquals(
			$this->find('#js_rs_details_edit_description textarea')->getAttribute('value'),
			'Apache is the world\'s most used web server software.'
		);

		// Enter a new description.
		$this->inputText("#js_rs_details_edit_description textarea.js_resource_description", 'this is a test description');

		// Click on submit.
		$this->click('#js_rs_details_edit_description input[type=submit]');

		// Assert that notification is shown.
		$this->assertNotification('app_resources_edit_success');

		// Make sure the description form is not visible anymore.
		$this->assertNotVisible("#js_rs_details_edit_description textarea.js_resource_description");

		// And check that the new description is shown.
		$this->assertElementContainsText('#js_rs_details_description', 'this is a test description');
	}

	/**
	 * Scenario :   As a user I should be able to edit the description of the passwords I own in the sidebar by clicking on the description
	 * Given        I am Ada and I am logged in on the password workspace
	 * Then         I should not see the sidebar and the textarea / form to edit the description
	 * When         I click on a password I own
	 * Then         I should see the description area in the sidebar with a link to edit the description
	 * And          I should see the current password description
	 * When         I click the description
	 * Then         I should a form to edit the description
	 * And          I should see that the form should contain the description pre filled
	 * When         I enter a new description
	 * And          I click on save
	 * Then         I should not see the form anymore
	 * And          I should see the new description
	 *
	 * @throws Exception
	 */
	public function testDescriptionEditClick() {
		// Reset database at the end of test.
		$this->resetDatabaseWhenComplete();

		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);

		// And I am logged in on the password workspace.
		$this->loginAs($user);

		// Make sure the edit description field is not visible.
		$this->assertNotVisible(".js_rs_details_edit_description textarea");

		// When I click on a password I own.
		$resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));
		$this->clickPassword($resource['id']);

		// I should see the edit description button.
		$this->assertVisible("js_edit_description_button");

		// I should see a description.
		$this->assertElementContainsText('#js_rs_details_description', 'Apache is the world\'s most used web server software.');

		// Click on the description.
		$this->click("#js_rs_details_description p.description_content");

		// Make sure password description edit field is visible
		$this->waitUntilISee("#js_rs_details_edit_description textarea.js_resource_description");

		// Assert that the description is correct in the textarea.
		$this->assertEquals(
			$this->find('#js_rs_details_edit_description textarea')->getAttribute('value'),
			'Apache is the world\'s most used web server software.'
		);

		// Fill up a new description.
		$this->inputText("#js_rs_details_edit_description textarea.js_resource_description", 'this is a test description');

		// Click on submit.
		$this->click('#js_rs_details_edit_description input[type=submit]');

		// Assert that notification is shown.
		$this->assertNotification('app_resources_edit_success');

		// Make sure the password edition form is not visible anymore.
		$this->assertNotVisible("#js_rs_details_edit_description textarea.js_resource_description");

		// And check that the new description reflects in the sidebar.
		$this->assertElementContainsText('#js_rs_details_description', 'this is a test description');
	}

	/**
	 * Scenario :   As a user I should be able to see the validation error messages for the description
	 * Given        I am Ada and I am logged in on the password workspace
	 * Then         I should not see the sidebar and the textarea to edit the description
	 * When         I click on a password I own
	 * Then         I should see the description area in the sidebar with a link to edit the description
	 * And          I should see the current password description
	 * When         I click the edit button
	 * Then         I should see a form to edit the description.
	 * When         I enter ### in the description field
	 * Then         I should see the error message Description should contain only alphabets, numbers, ....
	 * When         I click outside of the textarea
	 * Then         I should see that the form has disappeared
	 * When         I click on the edit button again
	 * Then         I should see the form without error messages
	 * And          I should see that the initial description is entered.
	 *
	 * @throws Exception
	 */
	public function testDescriptionEditErrorMessages() {
		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);

		// And I am logged in on the password workspace.
		$this->loginAs($user);

		// Make sure the password field is not visible.
		$this->assertNotVisible(".js_rs_details_edit_description textarea");

		// When I click on a password I own.
		$resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));
		$this->clickPassword($resource['id']);

		// Click on the edit button.
		$this->click("#js_edit_description_button i");

		// Make sure description field is visible.
		$this->waitUntilISee("#js_rs_details_edit_description textarea.js_resource_description");

		// I input ###
		$this->inputText("#js_rs_details_edit_description textarea.js_resource_description", '<<>');

		// Click on submit.
		$this->click('#js_rs_details_edit_description input[type=submit]');

		// I should see an error message saying that the description should only contain alphabets, numbers, etc..
		$this->assertElementContainsText('.js_resource_description_feedback', '/Description should only contain alphabets, numbers/');

		// Click somewhere else in the interface.
		$this->releaseFocus();

		// Make sure the description field is not visible.
		$this->assertNotVisible(".js_rs_details_edit_description textarea");

		// Click on the edit button.
		$this->click("#js_edit_description_button i");

		// I should see the description field again.
		$this->waitUntilISee("#js_rs_details_edit_description textarea.js_resource_description");

		// I should see the initial description.
		$this->assertEquals(
			$this->find('#js_rs_details_edit_description textarea')->getAttribute('value'),
			'Apache is the world\'s most used web server software.'
		);
	}

	/**
	 * Scenario :   As a user I should'nt be able to edit the description of a password with read access only
	 * Given        I am Ada and I am logged in on the password workspace
	 * When         I click on a password with read access only
	 * Then         I should see the description in the sidebar
	 * And          I should not see an edit button for the description
	 * When         I click on the description
	 * Then         I should not see a form to edit the description
	 *
	 * @throws Exception
	 */
	public function testEditDescriptionNotAllowed() {
		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);

		// And I am logged in on the password workspace
		$this->loginAs($user);

		// Make sure the password field is not visible
		$this->assertNotVisible(".js_rs_details_edit_description textarea");

		// When I click on a password I own
		$resource = Resource::get(array('user' => 'ada', 'permission' => 'read'));
		$this->clickPassword($resource['id']);

		// I should not see the edit button.
		$this->assertNotVisible("js_edit_description_button");

		// Click on the description
		$this->click("#js_rs_details_description p.description_content");

		// Make sure password field is not visible.
		$this->assertNotVisible("#js_rs_details_edit_description textarea");
	}

}