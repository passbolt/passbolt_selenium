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
 * Feature: As a user I can edit the password description directly from the sidebar
 *
 * Scenarios
 *  - As a user I should be able to edit the description of the passwords I own in the sidebar by clicking on the edit button
 *  - As a user I should be able to edit the description of the passwords I own in the sidebar by clicking on the description
 *  - As a user I should be able to see the validation error messages for the description
 *  - As a user I should'nt be able to edit the description of a password with read access only
 */
namespace Tests\LU\Base;

use App\Actions\PasswordActionsTrait;
use App\Actions\SidebarActionsTrait;
use App\Assertions\WorkspaceAssertionsTrait;
use App\PassboltTestCase;
use Data\Fixtures\User;
use Data\Fixtures\Resource;

class PasswordEditDescriptionTest extends PassboltTestCase
{
    use PasswordActionsTrait;
    use SidebarActionsTrait;
    use WorkspaceAssertionsTrait;

    /**
     * Scenario: As a user I should be able to edit the description of the passwords I own in the sidebar by clicking on the edit button
     *
     * Given I am Ada and I am logged in on the password workspace
     * Then  I should not see the sidebar and the textarea to edit the description
     * When  I click on a password I own
     * Then  I should see the description section in the sidebar with a link to edit the description
     * And   I should see the current password description
     * When  I click the edit button
     * Then  I should see a form to edit the description
     * And   I should see that the form should contain the description pre filled
     * When  I enter a new description
     * And   I click on save
     * Then  I should not see the form anymore
     * And   I should see the new description
     *
     * @group LU
     * @group password
     * @group password-edit
     * @group v2
     * @group saucelabs
     */
    public function testDescriptionEditButton() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Ada
        // And I am logged in on the password workspace
        $this->loginAs(User::get('ada'));

        // Make sure the description form is not visible.
        $this->assertNotVisibleByCss('.js_rs_details_edit_description textarea');

        // When I click on a password I own.
        $resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));
        $this->clickPassword($resource['id']);

        // Wait until the resource details description component is ready.
        $this->waitUntilISee('#js_pwd_details.ready');

        // I should see the edit button.
        $this->clickSecondarySidebarSectionHeader('description');
        $this->assertVisible('js_edit_description_button');

        // I should see a description.
        $this->assertElementContainsText('#js_rs_details_description', 'Apache is the world\'s most used web server software.');

        // Click on the edit button.
        $this->click('#js_edit_description_button i');

        // I should see a form to edit the description.
        $this->waitUntilISee('#js_rs_details_edit_description textarea.js_resource_description');

        // Assert that the description is correct in the textarea.
        $this->assertEquals(
            $this->find('#js_rs_details_edit_description textarea')->getAttribute('value'),
            'Apache is the world\'s most used web server software.'
        );

        // Enter a new description.
        $this->inputTextByCss('#js_rs_details_edit_description textarea.js_resource_description', 'this is a test description');

        // Click on submit.
        $this->click('#js_rs_details_edit_description input[type=submit]');

        // Assert that notification is shown.
        $this->assertNotification('app_resources_update_success');

        // Make sure the description form is not visible anymore.
        $this->clickSecondarySidebarSectionHeader('description');
        $this->assertNotVisibleByCss('#js_rs_details_edit_description textarea.js_resource_description');

        // And check that the new description is shown.
        $this->assertElementContainsText('#js_rs_details_description', 'this is a test description');
    }

    /**
     * Scenario: As a user I should be able to edit the description of the passwords I own in the sidebar by clicking on the description
     *
     * Given I am Ada and I am logged in on the password workspace
     * Then  I should not see the sidebar and the textarea / form to edit the description
     * When  I click on a password I own
     * And   I open the description section
     * Then  I should see the description area in the sidebar with a link to edit the description
     * And   I should see the current password description
     * When  I click the description
     * Then  I should a form to edit the description
     * And   I should see that the form should contain the description pre filled
     * When  I enter a new description
     * And   I click on save
     * Then  I should not see the form anymore
     * And   I should see the new description
     *
     * @group LU
     * @group password
     * @group password-edit
     * @group v2
     */
    public function testDescriptionEditClick() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Ada
        // And I am logged in on the password workspace.
        $this->loginAs(User::get('ada'));

        // Make sure the edit description field is not visible.
        $this->assertNotVisibleByCss('.js_rs_details_edit_description textarea');

        // When I click on a password I own.
        $resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));
        $this->clickPassword($resource['id']);

        // And I open the description section
        $this->clickSecondarySidebarSectionHeader('description');

        // I should see the edit description button.
        $this->assertVisible('js_edit_description_button');

        // I should see a description.
        $this->assertElementContainsText('#js_rs_details_description', 'Apache is the world\'s most used web server software.');

        // Click on the description.
        $this->click('#js_rs_details_description p.description_content');

        // Make sure password description edit field is visible
        $this->waitUntilISee('#js_rs_details_edit_description textarea.js_resource_description');

        // Assert that the description is correct in the textarea.
        $this->assertEquals(
            $this->find('#js_rs_details_edit_description textarea')->getAttribute('value'),
            'Apache is the world\'s most used web server software.'
        );

        // Fill up a new description.
        $this->inputTextByCss('#js_rs_details_edit_description textarea.js_resource_description', 'this is a test description');

        // Click on submit.
        $this->click('#js_rs_details_edit_description input[type=submit]');

        // Assert that notification is shown.
        $this->assertNotification('app_resources_update_success');

        // Make sure the password edition form is not visible anymore.
        $this->clickSecondarySidebarSectionHeader('description');
        $this->assertNotVisibleByCss('#js_rs_details_edit_description textarea.js_resource_description');

        // And check that the new description reflects in the sidebar.
        $this->assertElementContainsText('#js_rs_details_description', 'this is a test description');
    }

    /**
     * Scenario: As a user I should'nt be able to edit the description of a password with read access only
     *
     * Given I am Ada and I am logged in on the password workspace
     * When  I click on a password with read access only
     * And   I open the description section
     * Then  I should see the description in the sidebar
     * And   I should not see an edit button for the description
     * When  I click on the description
     * Then  I should not see a form to edit the description
     *
     * @group LU
     * @group password
     * @group password-edit
     * @group v2
     */
    public function testEditDescriptionNotAllowed() 
    {
        // Given I am Ada
        // And I am logged in on the password workspace
        $this->loginAs(User::get('ada'));

        // When I click on a password I own
        $resource = Resource::get(array('user' => 'ada', 'permission' => 'read'));
        $this->clickPassword($resource['id']);

        // And I open the description section
        $this->clickSecondarySidebarSectionHeader('description');

        // I should not see the edit button.
        $this->assertNotVisibleByCss('#js_edit_description_button');

        // Click on the description
        $this->click('#js_rs_details_description p.description_content');

        // Make sure password field is not visible.
        $this->assertNotVisibleByCss('#js_rs_details_edit_description textarea');
    }
}