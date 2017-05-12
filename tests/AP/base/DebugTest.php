<?php
/**
 * As a user with plugin but no config I should be able to use the debug screen test
 *
 * Scenarios:
 * As a user with a non configured plugin I should be recognized as such on the login page
 * As a user using the client in debug mode the information I enter should be persistent
 *
 * @copyright (c) 2017 Passbolt SARL
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class DebugTest extends PassboltTestCase {

    /**
     * Scenario: As a user with a non configured plugin I should be recognized as such on the login page
     *
     * Given    I am a AP on the login page
     * Then     I can see that my current role is guest
     * And      I can see that the plugin was detected
     * And      I can see that no plugin configuration was found
     */
    public function  testHealthCheck() {
        // Given I am a AP on the login page
        $this->getUrl();
        // Then I can see that my current role is guest
        $this->assertCurrentRole('guest');
        // And I can see that the plugin was detected
        $this->assertPlugin();
        // And I can see that no plugin configuration was found
        $this->assertNoPluginConfig();
    }

    /**
     * Scenario: As a user using the client in debug mode the information I enter should be persistent
     *
     * Given    I am Ada
     * When     I set my client config using the debug screen
     * When     I go to the debug screen again
     * Then     I should see the information I previously entered are still there
     */
    public function testDebugConfigIsPersistent() {
        $user = User::get('betty');
        $this->setClientConfig($user);
        $this->goToDebug();
        $this->assertInputValue('baseUrl', Config::read('passbolt.url'));
        $this->assertInputValue('UserId', $user['id']);
        $this->assertInputValue('ProfileFirstName', $user['FirstName']);
        $this->assertInputValue('ProfileLastName', $user['LastName']);
        $this->assertInputValue('UserUsername', $user['Username']);
        $this->assertInputValue('securityTokenCode', $user['TokenCode']);
        $this->assertInputValue('securityTokenColor', $user['TokenColor']);
        $this->assertInputValue('securityTokenTextColor', $user['TokenTextColor']);

        $key = file_get_contents(GPG_FIXTURES . DS . $user['PrivateKey'] );
        $this->assertInputValue('myKeyAscii', $key);
    }

    /**
     * Scenario: As a user on a the debug screen I should see error messages when I enter wrong inputs
     *
     * Given    I am Ada on the debug screen
     * And      I press the save button in the profile and settings section
     * Then     I should see a message saying the user id cannot be empty
     * When     I enter a user ID that is not a UUID
     * And      I press the save button in the profile and settings section
     * Then     I should see an error message saying that the user is not a valid uuid
     * When     I enter a correct userid
     * And      I press the save button in the profile and settings section
     * Then     I should an error message saying that the username cannot be empty
     * When     I enter a username that is not a valid email
     * And      I press the save button in the profile and settings section
     * Then     I should see an error message saying that username is not a valid email address
     * When     I enter a correct username
     * And      I press the save button in the profile and settings section
     * etc...
     */
    public function testDebugUserAndSettingsValidation() {
        $user = User::get('ada');
        $this->goToDebug();

        // Check empty user id
        $this->click('js_save_conf');
        $feedback = '.user.settings.feedback .message.error';
        $this->waitUntilISee($feedback);
        $this->assertElementContainsText($feedback, 'The user id cannot be empty');

        // Check user id is not uuid
        $this->inputText('UserId', 'test');
        $this->click('js_save_conf');
        $this->assertElementContainsText($feedback, 'The user id should be a valid UUID');

        // Check empty username name
        $this->inputText('UserId', $user['id']);
        $this->click('js_save_conf');
        $this->assertElementContainsText($feedback, 'The username cannot be empty');

        // Check empty username name
        $this->inputText('UserUsername', 'notanemail');
        $this->click('js_save_conf');
        $this->assertElementContainsText($feedback, 'The username should be a valid email address');

        // Check empty first name
        $this->inputText('UserUsername', $user['Username']);
        $this->click('js_save_conf');
        $this->assertElementContainsText($feedback, 'The first name cannot be empty');
        $this->click('js_save_conf');
        $this->assertElementContainsText($feedback, 'The first name cannot be empty');

        // Check first name is not alphanumeric
        $this->inputText('ProfileFirstName', '?');
        $this->click('js_save_conf');
        $this->assertElementContainsText($feedback, 'The first name should only contain alphabetical and numeric characters');

        // Check empty last name
        $this->inputText('ProfileFirstName', $user['FirstName']);
        $this->click('js_save_conf');
        $this->assertElementContainsText($feedback, 'The last name cannot be empty');

        // Check last name is not alphanumeric
        $this->inputText('ProfileLastName', '?');
        $this->click('js_save_conf');
        $this->assertElementContainsText($feedback, 'The last name should only contain alphabetical and numeric characters');

        // Check empty security token
        $this->inputText('ProfileLastName', $user['LastName']);
        $this->click('js_save_conf');
        $this->assertElementContainsText($feedback, 'A token code cannot be empty');

        // Check security token code is not alpha numeric
        $this->inputText('securityTokenCode', '?');
        $this->click('js_save_conf');
        $this->assertElementContainsText($feedback, 'The token code should only contain alphabetical and numeric characters');

        // Check security token code length is exactly 3 characters in length
        $this->inputText('securityTokenCode', '12');
        $this->click('js_save_conf');
        $this->assertElementContainsText($feedback, 'The token code should only contain 3 characters');
        $this->inputText('securityTokenCode', '1234');
        $this->click('js_save_conf');
        $this->assertElementContainsText($feedback, 'The token code should only contain 3 characters');

        // Check empty token color
        $this->inputText('securityTokenCode', $user['TokenCode']);
        $this->click('js_save_conf');
        $this->assertElementContainsText($feedback, 'The token color cannot be empty');

        // Check wrong token color
        $this->inputText('securityTokenColor', '#acolor');
        $this->click('js_save_conf');
        $this->assertElementContainsText($feedback, 'This is not a valid token color');

        // Check empty token text color
        $this->inputText('securityTokenColor', $user['TokenColor']);
        $this->click('js_save_conf');
        $this->assertElementContainsText($feedback, 'The token text color cannot be empty');

        // Check wrong token text color
        $this->inputText('securityTokenTextColor', '#acolor');
        $this->click('js_save_conf');
        $this->assertElementContainsText($feedback, 'This is not a valid token text color');

        // @TODO Domain
        // @TODO success
    }
}