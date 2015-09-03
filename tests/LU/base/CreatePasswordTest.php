<?php

/**
 * Feature :  As a user I should be able to create passwords
 *
 * Scenarios :
 * - As a user I should be able to see the create password dialog
 * - As a user I should be able to open close the password dialog
 * - As a user I should be able to see error messages when creating a password with wrong inputs
 * - As a user I should be able to generate a password automatically
 * - As a user I should see the complexity of the password I am creating
 *
 * @copyright    (c) 2015-present Bolt Software Pvt. Ltd.
 * @licence      GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
 */
class CreatePasswordTest extends PassboltTestCase
{

    protected function setUp()
    {
        parent::setUp();
        // Reset passbolt installation with dummies.
    }

    /**
     * Scenario :   As a user I should be able to view the create password dialog
     * Given        I am Carol Shaw
     * And          The database is in a clean state
     * When         I am logged in as Carol Shaw, I go to the user workspace
     * Then			I should see the create password button
     * When         I click on create button
     * Then         I should see the create password dialog
     * And          I should see the title is set to "create password"
     * And          I should see the name input and label is marked as mandatory
     * And          I should see the url text input and label
     * And          I should see the username text input and label marked as mandatory
     * And          I should see the password iframe
     * And          I should see the input field inside the iframe
     * And          I should see the view password button
     * And          I should see the generate password button
     * And          I should see the complexity meter
     * And          I should see the complexity textual indicator
     * And          I should see the description textarea and label
     * And          I should see the save button
     * And          I should see the cancel button
     * And          I should see the close dialog button
     */
    public function testCreatePasswordDialogExist()
    {
        // Given The database is in a clean state
        $this->PassboltServer->resetDatabase(1);

        // Given I am Carol Shaw
        $user = User::get('carol');
        $this->setClientConfig($user);

        // I am logged in as Carol Shaw, and I go to the user workspace
        $this->loginAs($user['Username']);

        // then I should see the create password button
        $this->assertElementContainsText(
            $this->findByCss('#js_wsp_primary_menu_wrapper ul'), 'create'
        );

        // When I click on create button
        $this->click('js_wk_menu_creation_button');

        // Then I should see the create password dialog
        $this->assertTrue($this->isVisible('.mad_controller_component_dialog_controller'));

        // And I should see the title is set to "create password"
        $this->assertElementContainsText(
            $this->findByCss('.mad_controller_component_dialog_controller'), 'Create Password'
        );

        // And I should see the name text input and label is marked as mandatory
        $this->assertTrue($this->isVisible('input[type=text]#js_field_name.required'));
        $this->assertTrue($this->isVisible('label[for=js_field_name]'));

        // And I should see the url text input and label
        $this->assertTrue($this->isVisible('input[type=text]#js_field_uri'));
        $this->assertTrue($this->isVisible('label[for=js_field_uri]'));

        // And I should see the username field marked as mandatory
        $this->assertTrue($this->isVisible('input[type=text]#js_field_username.required'));
        $this->assertTrue($this->isVisible('label[for=js_field_username]'));

        // And I should see the password iframe
        // And I should see the input field inside the iframe
        // And I should see the view password button
        // And I should see the generate password button
        // And I should see the complexity meter
        // And I should see the complexity textual indicator

        // And I should see the description field[type=te
        $this->assertTrue($this->isVisible('textarea#js_field_description'));
        $this->assertTrue($this->isVisible('label[for=js_field_description]'));

        // And I should see the save button
        // And I should see the cancel button
        // And I should see the close dialog button


    }

}