<?php
/**
 * Feature :  As a user I can create passwords
 *
 * Scenarios :
 * As a user I can view the create password dialog
 * As a user I can open close the password dialog
 * As a user I can see error messages when creating a password with wrong inputs
 * As a user I can view a password I just created on my list of passwords
 * As a user I can generate a random password automatically
 * As a user I can view the password I am creating in clear text
 *
 * @copyright    (c) 2015-present Bolt Software Pvt. Ltd.
 * @licence      GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
 */
class PasswordCreateTest extends PassboltTestCase
{

    /**
     * Scenario :   As a user I can view the create password dialog
     *
     * Given        I am Ada
     * And          I am logged in as Ada
     * And          I am on password workspace
     * Then			I see the create password button
     * When         I click on create button
     * Then         I see the create password dialog
     * And          I see the title is set to "create password"
     * And          I see the name input and label is marked as mandatory
     * And          I see the url text input and label
     * And          I see the username text input and label marked as mandatory
     * And          I see the password iframe
     * And          I see the iframe label
     * When         I switch to the password iframe
     * And          I see the password input
     * And          I see the security token
     * And          I see the view password button
     * And          I see the generate password button
     * And          I see the complexity meter
     * And          I see the complexity textual indicator
     * When         I switch back out of the password iframe
     * And          I see the description textarea and label
     * And          I see the save button
     * And          I see the cancel button
     * And          I see the close dialog button
     */
    public function testCreatePasswordDialogExist()
    {
        // Given I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

        // I am logged in as Carol, and I go to the user workspace
        $this->loginAs($user['Username']);

        // then I see the create password button
        $this->assertElementContainsText(
            $this->find('.main-action-wrapper'), 'create'
        );

        // When I click on create button
        $this->click('js_wsp_create_button');

        // Then I see the create password dialog
        $this->assertVisible('.create-password-dialog');

        // And I see the title is set to "create password"
        $this->assertElementContainsText(
            $this->findByCss('.dialog'), 'Create Password'
        );

        // And I see the name text input and label is marked as mandatory
        $this->assertVisible('.create-password-dialog input[type=text]#js_field_name.required');
        $this->assertVisible('.create-password-dialog label[for=js_field_name]');

        // And I see the url text input and label
        $this->assertVisible('.create-password-dialog input[type=text]#js_field_uri');
        $this->assertVisible('.create-password-dialog label[for=js_field_uri]');

        // And I see the username field marked as mandatory
        $this->assertVisible('.create-password-dialog input[type=text]#js_field_username.required');
        $this->assertVisible('.create-password-dialog label[for=js_field_username]');

        // And I see the password iframe
        $this->assertVisible('.create-password-dialog #passbolt-iframe-secret-edition');

        // And I see the iframe label
        $this->assertVisible('.create-password-dialog label[for=js_field_secret_data_0]');

        // When I switch to the password iframe
        $this->goIntoSecretIframe();

        // And I see the input field
        $this->assertVisible('input[type=password]#js_secret');

        // And I see the security token
        $this->assertSecurityToken($user);

        // And I see the view password button
        $this->assertVisible('#js_secret_view.button');

        // And I see the generate password button
        $this->assertVisible('#js_secret_generate.button');

        // And I see the complexity meter
        // And I see the complexity textual indicator
        $this->assertComplexity('very weak');

        // When I switch back out of the password iframe
        $this->goOutOfIframe();

        // And I see the description field
        $this->assertVisible('.create-password-dialog textarea#js_field_description');
        $this->assertVisible('.create-password-dialog label[for=js_field_description]');

        // And I see the save button
        $this->assertVisible('input[type=submit].button.primary');

        // And I see the cancel button
        $this->assertVisible('.create-password-dialog a.cancel');

        // And I see the close dialog button
        $this->assertVisible('.create-password-dialog a.dialog-close');
    }

    /**
     * Scenario: As a user I can open close the create password dialog
     *
     * Given    I am Ada
     * And      I am logged in
     * And      I am on the password workspace
     * When     I click on the create password button
     * Then     I see the create password dialog
     * When     I click on the cancel button
     * Then     I should not see the create password dialog
     * When     I click on the create password button
     * Then     I see the create password dialog
     * When     I click on the close dialog button
     * Then     I should not see the create password dialog
     * When     I click on the create password button
     * Then     I see the create password dialog
     * When     I press the keyboard escape key
     * Then     I should not see the create password dialog
     */
    public function testCreatePasswordDialogOpenClose() {
        // Given that I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

        // And I am logged in and on the password workspace
        $this->loginAs($user['Username']);

        // When I click on the create password button
        $this->click('js_wsp_create_button');

        // Then I see the create password dialog
        $this->assertVisible('.create-password-dialog');

        // When I click on the cancel button
        $this->findByCss('.create-password-dialog a.cancel')->click();

        // Then I should not see the create password dialog
        $this->assertNotVisible('.create-password-dialog');

        // -- WITH X BUTTON --
        // When I click on the create password button
        $this->click('js_wsp_create_button');

        // Then I see the create password dialog
        $this->assertVisible('.create-password-dialog');

        // When I click on the close dialog button
        $this->findByCss('.create-password-dialog a.dialog-close')->click();

        // Then I should not see the create password dialog
        $this->assertNotVisible('.create-password-dialog');

        // -- WITH ESCAPE --
        // When I click on the create password button
        $this->click('js_wsp_create_button');

        // Then I see the create password dialog
        $this->assertVisible('.create-password-dialog');

        // When I click on the escape key
        $this->pressEscape();

        // Then I should not see the create password dialog
        $this->assertTrue($this->isNotVisible('.create-password-dialog'));

    }

    /**
     * Scenario: As a user I can see error messages when creating a password with wrong inputs
     *
     * Given    I am Ada
     * And      I am logged in
     * And      I am on the create password dialog
     * When     I press the enter key on the keyboard
     * Then     I see an error message saying that the name is required
     * Then     I see an error message saying that the username is required
     * When     I enter '&' as a name
     * And      I enter '&' as a username
     * And      I enter '&' as a url
     * And      I enter '&' as a description
     * And      I click on the save button
     * Then     I see an error message saying that the name contain invalid characters
     * Then     I see an error message saying that the username contain invalid characters
     * Then     I see an error message saying that the url is not valid
     * Then     I see an error message saying that the description contain invalid characters
     */
    public function testCreatePasswordErrorMessages() {
        // Given I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

        // And I am logged in
        $this->loginAs($user['Username']);

        // And I am on the create password dialog
        $this->gotoCreatePassword();

        // When I click on the name input field
        $this->click('js_field_name');

        // And I press enter
        $this->pressEnter();

        // Then I see an error message saying that the name is required
        $this->assertVisible('#js_field_name_feedback.error.message');
        $this->assertElementContainsText(
            $this->find('js_field_name_feedback'), 'is required'
        );

        // Then I see an error message saying that the username is required
        $this->assertVisible('#js_field_username_feedback.error.message');
        $this->assertElementContainsText(
            $this->find('js_field_username_feedback'), 'is required'
        );

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

        // Then I see an error message saying that the name contain invalid characters
        $this->assertVisible('#js_field_name_feedback.error.message');
        $this->assertElementContainsText(
            $this->find('js_field_name_feedback'), 'should only contain alphabets, numbers'
        );

        // Then I see an error message saying that the username contain invalid characters
        $this->assertVisible('#js_field_username_feedback.error.message');
        $this->assertElementContainsText(
            $this->find('js_field_username_feedback'), 'should only contain alphabets, numbers'
        );

        // Then I see an error message saying that the url is not valid
        $this->assertVisible('#js_field_uri_feedback.error.message');
        $this->assertElementContainsText(
            $this->find('js_field_uri_feedback'), 'The format of the uri is not valid'
        );

        // Then I see an error message saying that the description contain invalid characters
        $this->assertVisible('#js_field_description_feedback.error.message');
        $this->assertElementContainsText(
            $this->find('js_field_description_feedback'), 'should only contain alphabets, numbers'
        );

    }

    /**
     * Scenario: As a user I can view a password I just created on my list of passwords
     *
     * Given    I am Ada
     * And      I am logged in
     * And      I am on the create password dialog
     * When     I enter 'localhost ftp' as the name
     * And      I enter 'test' as the username
     * And      I enter 'ftp://passbolt.com' as the uri
     * And      I enter 'localhost ftp test account' as the description
     * And      I enter 'ftp-password-test' as password
     * And      I click on the save button
     * Then     I see a dialog telling me encryption is in progress
     * And      I see a notice message that the operation was a success
     * And      I see the password I created in my password list
     */
    public function testCreatePasswordAndView() {
        // Given I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

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

        // Then I see a dialog telling me encryption is in progress
        $this->assertVisible('passbolt-iframe-progress-dialog');

        // I see a notice message that the operation was a success
        $this->assertNotification('app_resources_add_success');

        // I see the password I created in my password list
        $this->assertElementContainsText(
            $this->find('js_wsp_pwd_browser'), 'ftp://passbolt.com'
        );
        $this->assertElementContainsText(
            $this->find('js_wsp_pwd_browser'), 'localhost ftp'
        );

        // Since content was edited, we reset the database
        $this->resetDatabase();
    }

    /**
     * Scenario: As a user I can generate a random password automatically
     *
     * Given    I am Ada
     * And      I am logged in
     * And      I am on the create password dialog
     * When     I click the button to generate a new random password automatically
     * Then     I see the secret field populated
     * And      I see that the password complexity is set to fair
     */
    public function testCreatePasswordGenerateButton() {
        // Given I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

        // And I am logged in
        $this->loginAs($user['Username']);

        // And I am on the create password dialog
        $this->gotoCreatePassword();

        // When I click the button to generate a new random password automatically
        $this->goIntoSecretIframe();
        $this->click('js_secret_generate');

        // Then I see the secret field populated
        $s = $this->findById('js_secret')->getAttribute('value');
        $this->assertNotEmpty($s);

        // And I see that the password complexity is set to fair
        $this->assertTrue(strlen($s) == SystemDefaults::$AUTO_PASSWORD_LENGTH);
        $this->assertComplexity(SystemDefaults::$AUTO_PASSWORD_STRENGTH);
    }

    /**
     * Scenario: As a user I can view the password I am creating in clear text
     *
     * Given I am carol
     * And I am logged in
     * And I am on the create password dialog
     * When I enter a password value
     * Then I should not see the input field with the password in clear text
     * When I click on the show password
     * Then I see the input field with the password in clear text
     * When I click on the show password
     * Then I should not see the input field with the password in clear text
     */
    public function testCreatePasswordViewButton() {
        // Given I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

        // And I am logged in
        $this->loginAs($user['Username']);

        // And I am on the create password dialog
        $this->gotoCreatePassword();

        // When I enter a password value
        $this->inputSecret('ftp-password-test');

        // Then I should not see the input field with the password in clear text
        $this->goIntoSecretIframe();
        $this->assertTrue($this->isNotVisible('js_secret_clear'));

        // When I click on the view password
        $this->click('js_secret_view');

        // Then I see the input field with the password in clear text
        $this->assertNotVisible('js_secret');
        $this->assertVisible('js_secret_clear');
        $this->assertTrue($this->findById('js_secret_clear')->getAttribute('value') == 'ftp-password-test');

        // When I click on the view password
        $this->click('js_secret_view');

        // Then I should not see the input field with the password in clear text
        $this->assertNotVisible('js_secret_clear');
    }

}