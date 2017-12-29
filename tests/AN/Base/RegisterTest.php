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
 * Feature : Register and first setup step
 * - As an anonymous user without plugin I can register and see the first setup step
 * - As a anonymous user I cannot see the setup page if user id and token are incorrect
 */
namespace Tests\AN\Base;

use App\PassboltTestCase;

class RegisterTest extends PassboltTestCase
{
    /**
     * Scenario: As an anonymous user without plugin I can register and see the first setup step
     *
     * Given I am an anonymous user on the registration page
     * When  I fill in the registration form with ada information
     * And   I press enter
     * Then  I should see an error message
     * And   I can see the form populated with previously entered data
     * When  I register with John Doe information
     * And   I click on the submit button
     * Then  I should see the thank you page
     * When  I got to my mailbox
     * And   I follow the link in the registration email.
     * Then  I can see the first setup step page
     * And   I see an error telling me I need a plugin to continue
     *
     * @group saucelabs
     * @group AN
     * @group register
     */
    public function testANCanRegister()
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given that I am an anonymous user on the registration page
        $this->getUrl('register');

        // When I fill in the registration form with ada information
        $this->inputText('profile-first-name', 'ada');
        $this->inputText('profile-last-name', 'lovelace');
        $this->inputText('username', 'ada@passbolt.com');
        $this->click('#disclaimer');

        // And press enter
        $this->click('.button.primary');

        // Then I should see an error message
        $this->waitUntilISee('#username.form-error');
        $this->assertElementContainsText(
            $this->findByCss('#username + .error.message'),
            'This username is already in use.'
        );

        // And the form populated with previously entered data
        $this->assertInputValue('profile-first-name', 'ada');
        $this->assertInputValue('profile-last-name', 'lovelace');
        $this->assertInputValue('username', 'ada@passbolt.com');

        // When I register with John Doe information
        $this->inputText('profile-first-name', 'john');
        $this->inputText('profile-last-name', 'doe');
        $this->inputText('username', 'john@passbolt.com');
        $this->click('#disclaimer');

        // And I click on the submit button
        $this->click('.button.primary.big');

        // Then I should see the thank you page
        $this->waitUntilISee('.register.thank-you.form.feedback');

        // When I got to my mailbox
        $this->getUrl('seleniumtests/showlastemail/' . urlencode('john@passbolt.com'));

        // And follow the link in the registration email.
        $this->followLink('get started');

        // Then I can see the first setup step page
        $this->waitUntilISee('.plugin-check-wrapper', '/Plugin check/');
        $this->assertUrlMatch('/\/setup\/install\/[a-z0-9\-]{36}\/[a-z0-9\-]{36}/');
        $this->assertElementContainsText(
            $this->findById('js_step_title'),
            'setup your system'
        );

        // And an error telling me I need a plugin to continue
        $this->assertElementContainsText(
            $this->findByCss('.plugin-check-wrapper .plugin-check.error'),
            'extension is required'
        );
    }

    /**
     * Scenario: As a anonymous user I cannot see the setup page if user id and token are incorrect.
     * Given I try to access the setup page with wrong information in the url
     * Then  I should reach an error page with text "Token not found"
     *
     * @group AN
     * @group register
     * @group v2
     */
    public function testCannotSeeSetupPageWithInvalidInformation() 
    {
        // Access url with wrong user id and token.
        $this->getUrl('setup/install/5569df1d-7bec-4c0c-a09d-55e2c0a895dc/d45c0bf1e00fb8db60af1e8b5482f9f3');
        $this->assertPageContainsText('The token is not valid.');
    }
}