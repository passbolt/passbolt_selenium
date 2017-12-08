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
 * Feature: Account Recovery
 * - As an anonymous user without plugin trying to recover my account I should see an error message.
 */
namespace Tests\AN\base;

use App\PassboltRecoverTestCase;

class RecoverTest extends PassboltRecoverTestCase
{
    /**
     * Scenario: As an anonymous user without plugin trying to recover my account I should see an error message.
     *
     * Given I am an anonymous user on the recover page
     * When  I enter the email of an non existing user in the form
     * Then  I can see an error message
     * When  I input ada email address in the form
     * And   I press enter
     * Then  I should see a thank you page
     * When  I click on the link in the email
     * Then  I should be on the setup page first step
     * And   I should see an error message telling me an add-on is required to use passbolt
     *
     * @group saucelabs
     * @group AN
     * @group recover
     */
    public function testANRecoverNoPlugin()
    {
        // Given I am an anonymous user on the recover page
        $this->resetDatabaseWhenComplete();
        $this->getUrl('recover');

        // When I enter the email of an non existing user in the form
        $this->inputText('username', 'nope@passbolt.com');

        // And I press enter
        $this->pressEnter();

        // Then I should see an error message
        $this->waitUntilISee('#username.form-error');
        $this->assertElementContainsText(
            $this->findByCss('#username + .error.message'),
            'This user does not exist'
        );

        // When I enter ada email address
        $this->inputText('username', 'ada@passbolt.com');

        // And I click on the submit button
        $this->click('.button.primary.big');

        // Then I can see the thank you page
        $this->waitUntilISee('.page.recover.thank-you');

        // When  I click on the link in the email
        // Then  I should be on the setup page first step
        $this->goToRecover('ada@passbolt.com', false);

        // And I should see an error message telling me an add-on is required to use passbolt
        $this->waitUntilISee('.error .message', '/extension is required/');
    }
}
