<?php
/**
 * Feature :  As a user I can create passwords
 *
 * Scenarios :
 * As a user I can view the create password dialog
 * As a user I can open close the password dialog
 * As a user I can see error messages when creating a password with wrong inputs
 * As a user I can view a password I just created on my list of passwords
 * As a user I can generate a password automatically
 * As a user I can view my password in clear text
 *
 * @copyright    (c) 2015-present Bolt Software Pvt. Ltd.
 * @licence      GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
 */
class PasswordCreateTest extends PassboltTestCase
{

    /**
     * Scenario :   As a user I can view the create password dialog
     *
     * Given        I am Carol
     * And          The database is in a clean state
     * And          I am logged in as Carol
     * And          I am on password workspace
     * Then			I should see the create password button
     * When         I click on create button
     * Then         I should see the create password dialog
     * And          I should see the title is set to "create password"
     * And          I should see the name input and label is marked as mandatory
     * And          I should see the url text input and label
     * And          I should see the username text input and label marked as mandatory
     * And          I should see the password iframe
     * And          I should see the iframe label
     * When         I switch to the password iframe
     * And          I should see the password input
     * And          I should see the security token
     * And          I should see the view password button
     * And          I should see the generate password button
     * And          I should see the complexity meter
     * And          I should see the complexity textual indicator
     * When         I switch back out of the password iframe
     * And          I should see the description textarea and label
     * And          I should see the save button
     * And          I should see the cancel button
     * And          I should see the close dialog button
     */
    public function testCreatePasswordDialogExist()
    {
        // Given I am Carol
        $user = User::get('carol');
        $this->setClientConfig($user);

        // And the database is in a clean state
        $this->PassboltServer->resetDatabase();

        // I am logged in as Carol, and I go to the user workspace
        $this->loginAs($user['Username']);

        // then I should see the create password button
        $this->assertElementContainsText(
            $this->findByCss('#js_wsp_primary_menu_wrapper ul'), 'create'
        );

        // When I click on create button
        $this->click('js_wk_menu_creation_button');

        // Then I should see the create password dialog
        $this->assertVisible('.create-password-dialog');

        // And I should see the title is set to "create password"
        $this->assertElementContainsText(
            $this->findByCss('.dialog'), 'Create Password'
        );

        // And I should see the name text input and label is marked as mandatory
        $this->assertVisible('.create-password-dialog input[type=text]#js_field_name.required');
        $this->assertVisible('.create-password-dialog label[for=js_field_name]');

        // And I should see the url text input and label
        $this->assertVisible('.create-password-dialog input[type=text]#js_field_uri');
        $this->assertVisible('.create-password-dialog label[for=js_field_uri]');

        // And I should see the username field marked as mandatory
        $this->assertVisible('.create-password-dialog input[type=text]#js_field_username.required');
        $this->assertVisible('.create-password-dialog label[for=js_field_username]');

        // And I should see the password iframe
        $this->assertVisible('.create-password-dialog #passbolt-iframe-secret-edition');

        // And I should see the iframe label
        $this->assertVisible('.create-password-dialog label[for=js_field_secret_data_0]');

        // When I switch to the password iframe
        $this->goIntoSecretIframe();

        // And I should see the input field
        $this->assertVisible('input[type=password]#js_secret');

        // And I should see the security token
        $this->assertSecurityToken($user);

        // And I should see the view password button
        $this->assertVisible('#js_secret_view.button');

        // And I should see the generate password button
        $this->assertVisible('#js_secret_generate.button');

        // And I should see the complexity meter
        // And I should see the complexity textual indicator
        $this->assertComplexity('very weak');

        // When I switch back out of the password iframe
        $this->goOutOfIframe();

        // And I should see the description field
        $this->assertVisible('.create-password-dialog textarea#js_field_description');
        $this->assertVisible('.create-password-dialog label[for=js_field_description]');

        // And I should see the save button
        $this->assertVisible('input[type=submit].button.primary');

        // And I should see the cancel button
        $this->assertVisible('.create-password-dialog a.cancel');

        // And I should see the close dialog button
        $this->assertVisible('.create-password-dialog a.dialog-close');
    }

    /**
     * Scenario: As a user I can open close the create password dialog
     *
     * Given    I am Carol
     * And      I am logged in
     * And      I am on the password workspace
     * When     I click on the create password button
     * Then     I should see the create password dialog
     * When     I click on the cancel button
     * Then     I should not see the create password dialog
     * When     I click on the create password button
     * Then     I should see the create password dialog
     * When     I click on the close dialog button
     * Then     I should not see the create password dialog
     * When     I click on the create password button
     * Then     I should see the create password dialog
     * When     I press the keyboard escape key
     * Then     I should not see the create password dialog
     */
    public function testCreatePasswordDialogOpenClose() {
        // Given that I am Carol
        $user = User::get('carol');
        $this->setClientConfig($user);

        // And I am logged in and on the password workspace
        $this->loginAs($user['Username']);

        // When I click on the create password button
        $this->click('js_wk_menu_creation_button');

        // Then I should see the create password dialog
        $this->assertVisible('.create-password-dialog');

        // When I click on the cancel button
        $this->findByCss('.create-password-dialog a.cancel')->click();

        // Then I should not see the create password dialog
        $this->assertNotVisible('.create-password-dialog');

        // -- WITH X BUTTON --
        // When I click on the create password button
        $this->click('js_wk_menu_creation_button');

        // Then I should see the create password dialog
        $this->assertVisible('.create-password-dialog');

        // When I click on the close dialog button
        $this->findByCss('.create-password-dialog a.dialog-close')->click();

        // Then I should not see the create password dialog
        $this->assertNotVisible('.create-password-dialog');

        // -- WITH ESCAPE --
        // When I click on the create password button
        $this->click('js_wk_menu_creation_button');

        // Then I should see the create password dialog
        $this->assertVisible('.create-password-dialog');

        // When I click on the escape key
        $this->pressEscape();

        // Then I should not see the create password dialog
        $this->assertTrue($this->isNotVisible('.create-password-dialog'));

    }

    /**
     * Scenario: As a user I can see error messages when creating a password with wrong inputs
     *
     * Given    I am Carol
     * And      I am logged in
     * And      I am on the create password dialog
     * When     I press the enter key on the keyboard
     * Then     I should see an error message saying that the name is required
     * Then     I should see an error message saying that the username is required
     * When     I enter '&' as a name
     * And      I enter '&' as a username
     * And      I enter '&' as a url
     * And      I enter '&' as a description
     * And      I click on the save button
     * Then     I should see an error message saying that the name contain invalid characters
     * Then     I should see an error message saying that the username contain invalid characters
     * Then     I should see an error message saying that the url is not valid
     * Then     I should see an error message saying that the description contain invalid characters
     */
    public function testCreatePasswordErrorMessages() {
        // Given I am Carol
        $user = User::get('carol');
        $this->setClientConfig($user);

        // And I am logged in
        $this->loginAs($user['Username']);

        // And I am on the create password dialog
        $this->gotoCreatePassword();

        // When I click on the name input field
        $this->click('#js_field_name');

        // And I press enter
        $this->pressEnter();

        // Then I should see an error message saying that the name is required
        $this->assertVisible('#js_field_name_feedback.error.message');
        $this->assertElementContainsText(
            $this->findByCss('#js_field_name_feedback'), 'is required'
        );

        // Then I should see an error message saying that the username is required
        $this->assertVisible('#js_field_username_feedback.error.message');
        $this->assertElementContainsText(
            $this->findByCss('#js_field_username_feedback'), 'is required'
        );

        // @TODO PASSBOLT-1023 it should pass if we remove this
        $this->pressEscape();
        $this->click('#js_wk_menu_creation_button');
        // @TODO PASSBOLT-1023 ends

        // When I enter & as a name
        $this->inputText('js_field_name', '&');

        // And I enter & as a username
        $this->inputText('js_field_username', '&');

        // And I enter & as a url
        $this->inputText('js_field_uri', '&');

        // And I enter & as a description
        $this->inputText('js_field_description', '&');

        // And I click save
        $this->click('.create-password-dialog input[type=submit]');

        // Then I should see an error message saying that the name contain invalid characters
        $this->assertVisible('#js_field_name_feedback.error.message');
        $this->assertElementContainsText(
            $this->findByCss('#js_field_name_feedback'), 'should only contain alphabets, numbers'
        );

        // Then I should see an error message saying that the username contain invalid characters
        $this->assertVisible('#js_field_username_feedback.error.message');
        $this->assertElementContainsText(
            $this->findByCss('#js_field_username_feedback'), 'should only contain alphabets, numbers'
        );

        // Then I should see an error message saying that the url is not valid
        $this->assertVisible('#js_field_uri_feedback.error.message');
        $this->assertElementContainsText(
            $this->findByCss('#js_field_uri_feedback'), 'The format of the uri is not valid'
        );

        // Then I should see an error message saying that the description contain invalid characters
        $this->assertVisible('#js_field_description_feedback.error.message');
        $this->assertElementContainsText(
            $this->findByCss('#js_field_description_feedback'), 'should only contain alphabets, numbers'
        );

    }

    /**
     * Scenario: As a user I can view a password I just created on my list of passwords
     *
     * Given    I am Carol
     * And      the database is in a clean state
     * And      I am logged in
     * And      I am on the create password dialog
     * When     I enter 'localhost ftp' as the name
     * And      I enter 'test' as the username
     * And      I enter 'ftp://passbolt.com' as the uri
     * And      I enter 'localhost ftp test account' as the description
     * And      I enter 'ftp-password-test' as password
     * And      I click on the save button
     * Then     I should see a dialog telling me encryption is in progress
     * And      I should see a notice message that the operation was a success
     * And      I should see the password I created in my password list
     */
    public function testCreatePasswordAndView() {
        // Given I am Carol
        $user = User::get('carol');
        $this->setClientConfig($user);

        // And the database is in a clean state
        $this->PassboltServer->resetDatabase();

        // And I am logged in
        $this->loginAs($user['Username']);

        // And I am on the create password dialog
        $this->gotoCreatePassword();

        // And I enter 'localhost ftp' as the name
        $this->inputText('js_field_name', 'localhost ftp');

        // And I enter 'test' as the username
        $this->inputText('js_field_username', 'test');

        // And I enter 'ftp://localhost' as the uri
        $this->inputText('js_field_uri', 'ftp://passbolt.com');

        // I enter 'ftp-password-test' as password
        $this->inputSecret('ftp-password-test');

        // And I enter 'localhost ftp test account' as the description
        $this->inputText('js_field_description', 'localhost ftp test account');

        // When I click on the save button
        $this->click('.create-password-dialog input[type=submit]');

        // Then I should see a dialog telling me encryption is in progress
        $this->assertVisible('passbolt-iframe-progress-dialog');

        // I should see a notice message that the operation was a success
        $this->waitUntilISee('.notification-container', '/The resource was successfully saved/i');

        // I should see the password I created in my password list
        $this->assertElementContainsText(
            $this->findByCss('#js_wsp_pwd_browser'), 'ftp://passbolt.com'
        );
        $this->assertElementContainsText(
            $this->findByCss('#js_wsp_pwd_browser'), 'localhost ftp'
        );
    }

    /**
     * Scenario: As a user I can generate a password automatically
     *
     * Given    I am carol
     * And      I am logged in
     * And      I am on the create password dialog
     * When     I click on the password generation button
     * Then     I should see the secret field populated
     * And      I should see that the password complexity is set to fair
     */
    public function testCreatePasswordGenerateButton() {
        // Given I am Carol
        $user = User::get('carol');
        $this->setClientConfig($user);

        // And the database is in a clean state
        $this->PassboltServer->resetDatabase();

        // And I am logged in
        $this->loginAs($user['Username']);

        // And I am on the create password dialog
        $this->gotoCreatePassword();

        // When I click on the password generation button
        $this->goIntoSecretIframe();
        $this->click('js_secret_generate');

        // Then I should see the secret field populated
        $s = $this->findById('js_secret')->getAttribute('value');
        $this->assertNotEmpty($s);

        // And I should see that the password complexity is set to fair
        $this->assertTrue(strlen($s) == SystemDefaults::$AUTO_PASSWORD_LENGTH);
        $this->assertElementContainsText(
            $this->findByCss('#js_secret_strength .complexity-text'),
            SystemDefaults::$AUTO_PASSWORD_STRENGTH
        );
    }

    /**
     * Scenario: As a user I can view my password in clear text
     *
     * Given I am carol
     * And I am logged in
     * And I am on the create password dialog
     * When I enter a password value
     * Then I should not see the input field with the password in clear text
     * When I click on the show password
     * Then I should see the input field with the password in clear text
     * When I click on the show password
     * Then I should not see the input field with the password in clear text
     */
    public function testCreatePasswordViewButton() {
        // Given I am Carol
        $user = User::get('carol');
        $this->setClientConfig($user);

        // And I am logged in
        $this->loginAs($user['Username']);

        // And I am on the create password dialog
        $this->gotoCreatePassword();

        // When I enter a password value
        $this->inputSecret('ftp-password-test');

        // Then I should not see the input field with the password in clear text
        $this->goIntoSecretIframe();
        $this->assertTrue($this->isNotVisible('#js_secret_clear'));

        // When I click on the view password
        $this->click('js_secret_view');

        // Then I should see the input field with the password in clear text
        $this->assertNotVisible('#js_secret');
        $this->assertVisible('#js_secret_clear');
        $this->assertTrue($this->findById('js_secret_clear')->getAttribute('value') == 'ftp-password-test');

        // When I click on the view password
        $this->click('js_secret_view');

        // Then I should not see the input field with the password in clear text
        $this->assertNotVisible('#js_secret_clear');
    }

}