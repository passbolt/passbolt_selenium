<?php
/**
 * Feature: As a user I can edit a password
 *
 * Scenarios:
 * As a user I can edit a password using the edit button in the action bar
 * As a user I can edit a password using the right click contextual menu
 * As a user I can open close the edit password dialog
 *
 * As a user I can see the edit password dialog content
 * As a user I can edit a password I have ownership of
 * As a user I can view a password I just edited on my list of passwords
 * As a user I can see error messages when editing a password with wrong inputs
 * As a user I can generate a password automatically
 * As a user I can view my password in clear text
 * As a user I can not edit a password I have only read access to
 *
 * @copyright    (c) 2015-present Bolt Software Pvt. Ltd.
 * @licence      GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
 */
class PasswordEditTest extends PassboltTestCase
{
    /**
     * Scenario: As a user I can edit a password using the edit button in the action bar
     *
     * Given    I am Ada
     * And      the database is in the default state
     * And      I am logged in on the password workspace
     * Then     I can see the edit password button is disabled
     * When     I click on a password I own
     * Then     I should see the edit button is enabled
     * When     I click on the edit button
     * Then     I can see the edit password dialog
     */
    public function testEditPasswordButton() {
        // Given I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

        // And the database is in the default state
        $this->PassboltServer->resetDatabase(1);

        // And I am logged in on the password workspace
        $this->loginAs($user['Username']);

        // Then I can see the edit password button is disabled
        $this->assertVisible('js_wk_menu_edition_button');
        $this->assertVisible('#js_wk_menu_edition_button.disabled');

        // When I click on a password I own
        $resource = Resource::get(array('user' => 'ada', 'permission' => 'admin'));
        $this->click($resource['id']);

        // Then I should see the edit button is enabled
        $this->assertNotVisible('#js_wk_menu_edition_button.disabled');
        $this->assertVisible('js_wk_menu_edition_button');

        // When I click on the edit button
        $this->click('js_wk_menu_edition_button');

        // Then I can see the edit password dialog
        $this->assertVisible('.edit-password-dialog');
    }

    /**
     * Scenario: As a user I can edit a password using the right click contextual menu
     *
     * Given    I am Ada
     * And      I am logged in on the password workspace
     * When     I right click on a password I own
     * Then     I can see the contextual menu
     * And      I can see the the edit option is enabled
     * When     I click on the edit link in the contextual menu
     * Then     I can see the edit password dialog
     */
    public function testEditPasswordRightClick() {
        // Given I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

        // And I am logged in on the password workspace
        $this->loginAs($user['Username']);

        // When I right click on a password I own
        $resource = Resource::get(array('user' => 'ada', 'permission' => 'admin'));
        $this->rightClick($resource['id']);

        // Then I can see the contextual menu
        $this->assertVisible('js_contextual_menu');

        // And I should see the the edit option is enabled
        // @TODO PASSBOLT-1028

        // When I click on the edit link in the contextual menu
        $this->clickLink('Edit');

        // Then I can see the edit password dialog
        $this->assertVisible('.edit-password-dialog');
    }

    /**
     * Scenario: As a user I can open close the edit password dialog
     *
     * Given    I am Ada
     * And      I am logged in on the password workspace
     * And      I am editing a password I own
     * When     I click on the cancel button
     * Then     I do not see the edit password dialog
     * When     I reopen the edit password dialog
     * And      I click on the close dialog button (in the top right corner)
     * Then     I do not see the edit password dialog
     * When     I reopen the edit password dialog
     * And      I press the escape button
     * Then     I do not see the edit password dialog
     */
    public function testEditPasswordDialogOpenClose() {
        // Given I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

        // And I am logged in on the password workspace
        $this->loginAs($user['Username']);

        // And I am editing a password I own
        $resource = Resource::get(array('user' => 'ada', 'permission' => 'admin'));
        $this->assertEditPasswordDialog($resource['id']);

        // When I click on the cancel button
        $this->click('.edit-password-dialog .js-dialog-cancel');

        // Then I do not see the edit password dialog
        $this->assertTrue($this->isNotVisible('.edit-password-dialog'));

        // When I reopen the edit password dialog
        $this->assertEditPasswordDialog($resource['id']);

        // And I click on the close dialog button (in the top right corner)
        $this->click('.edit-password-dialog .dialog-close');

        // Then I do not see the edit password dialog
        $this->assertTrue($this->isNotVisible('.edit-password-dialog'));

        // When I reopen the edit password dialog
        $this->assertEditPasswordDialog($resource['id']);

        // And I press the escape button
        $this->pressEscape();

        // Then I do not see the edit password dialog
        $this->assertTrue($this->isNotVisible('.edit-password-dialog'));
    }

    /**
     * Scenario: As a user I can see the edit password dialog
     *
     * Given    I am Ada
     * And      the database is in a clean state
     * And      I am logged on the password workspace
     * And      I am editing a password I own
     * Then     I should see the edit password dialog
     * And      I should see the title is set to "edit password"
     * And      I should see the name of the resource after the title
     * And      I should see the close dialog button
     * And      I should see the edit tab is selected
     * And      I should see the share tab is not selected
     * And      I should see the name input and label is marked as mandatory
     * And      I should see the resource name in the text input
     * And      I should see the url text input and label
     * And      I should see the resource url in the text input
     * And      I should see the username text input and label marked as mandatory
     * And      I should see the resource ursername in the text input
     * And      I should see the password iframe
     * And      I should see the iframe label
     * When     I switch to the password iframe
     * And      I should see the password input
     * And      I should not see the password in cleartext
     * And      I should see the security token
     * And      I should see the view password button
     * And      I should see the generate password button
     * And      I should see the complexity meter
     * And      I should see the complexity textual indicator
     * When     I switch back out of the password iframe
     * And      I should see the description textarea and label
     * And      I should see the resource description in the textarea
     * And      I should see the save button
     * And      I should see the cancel button
     */
    public function testEditPasswordDialogView() {
        // Given I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

        // And I am logged in on the password workspace
        $this->loginAs($user['Username']);

        // And I am editing a password I own
        $resource = Resource::get(array('user' => 'ada', 'permission' => 'admin'));
        $this->assertEditPasswordDialog($resource['id']);

        // Then I should see the edit password dialog
        $this->assertVisible('.edit-password-dialog');

        // And I should see the title is set to "edit password"
        $this->assertElementContainsText(
            $this->findByCss('.edit-password-dialog h2'),
            'Edit'
        );

        // And I should see the name of the resource after the title
        $this->assertElementContainsText(
            $this->findByCss('.edit-password-dialog h2 .dialog-header-subtitle'),
            $resource['name']
        );

        // And I should see the close dialog button
        $this->assertVisible('.edit-password-dialog .dialog-close');

        // And I should see the edit tab is selected
        $this->assertElementContainsText(
            $this->findByCss('.edit-password-dialog #js_tab_nav_js_rs_edit a.selected'),
            'Edit'
        );

        // And I should see the share tab is not selected
        $this->assertElementContainsText(
            $this->findByCss('.edit-password-dialog #js_tab_nav_js_rs_permission a'),
            'Share'
        );
        $this->assertNotVisible('.edit-password-dialog #js_tab_nav_js_rs_permission a.selected');

        // And I should see the name text input and label is marked as mandatory
        $this->assertVisible('.edit-password-dialog input[type=text]#js_field_name.required');
        $this->assertVisible('.edit-password-dialog label[for=js_field_name]');

        // And I should see the resource name in the text input
        $this->assertInputValue('js_field_name', $resource['name']);

        // And I should see the url text input and label
        $this->assertVisible('.edit-password-dialog input[type=text]#js_field_uri');
        $this->assertVisible('.edit-password-dialog label[for=js_field_uri]');

        // And  I should see the resource url in the text input
        $this->assertInputValue('js_field_uri', $resource['uri']);

        // And I should see the username field marked as mandatory
        $this->assertVisible('.edit-password-dialog input[type=text]#js_field_username.required');
        $this->assertVisible('.edit-password-dialog label[for=js_field_username]');

        // And I should see the resource ursername in the text input
        $this->assertInputValue('js_field_username', $resource['username']);

        // And I should see the password iframe
        $this->assertVisible('.edit-password-dialog #passbolt-iframe-secret-edition');

        // And I should see the iframe label
        $this->assertVisible('.edit-password-dialog label[for=js_field_secret_data_0]');

        // When I switch to the password iframe
        $this->goIntoSecretIframe();

        // And I should see the password input
        $this->assertVisible('input[type=password]#js_secret');

        // And I should not see the password in cleartext
        $this->assertNotVisible('input[type=password]#js_secret_clear');
        $this->assertInputValue('js_secret_clear','');
        $this->assertInputValue('js_secret','');

        // And I should see the security token
        $this->assertSecurityToken($user);

        // And I should see the view password button
        $this->assertVisible('js_secret_view');
        // And I should see the generate password button
        $this->assertVisible('js_secret_generate');

        // And I should see the complexity meter
        // And I should see the complexity textual indicator
        $this->assertComplexity('very weak');

        // When I switch back out of the password iframe
        $this->goOutOfIframe();

        // And I should see the description field and label
        $this->assertVisible('.edit-password-dialog textarea#js_field_description');
        $this->assertVisible('.edit-password-dialog label[for=js_field_description]');

        // And  I should see the resource description in the textarea
        $this->assertInputValue('js_field_description', $resource['description']);

        // And I should see the save button
        $this->assertVisible('.edit-password-dialog input[type=submit].button.primary');

        // And I should see the cancel button
        $this->assertVisible('.edit-password-dialog a.cancel');

    }

    /**
     * Scenario: As a user I can edit a password I have ownership of
     */
    public function testEditPasswordIOwn() {

    }

    /**
     * Scenario: As a user I can view a password I just edited on my list of passwords
     */
    public function testViewEditedPassword() {

    }

    /**
     * Scenario: As a user I can see error messages when editing a password with wrong inputs
     */
    public function testEditPasswordValidationErrors() {

    }

    /**
     * Scenario: As a user I can generate a password automatically
     */
    public function testEditPasswordGenerateAuto() {

    }

    /**
     * Scenario: As a user I can view my password in clear text
     */
    public function testEditPasswordViewInClear() {

    }

    /**
     * Scenario: As a user I can not edit a password I have only read access to
     */
    public function testCannotEditPasswordWithReadAccess() {

    }

    /**
     * Scenario: As a user I can see the current password complexity when editing a password
     */
    public function testEditPasswordComplexityCheck() {
        // I create a password with very strong complexity
        // I edit the password
        // I can see the complexity is set to very strong in the edit password screen
    }
}