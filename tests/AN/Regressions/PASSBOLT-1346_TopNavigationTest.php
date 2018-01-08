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
 * Feature : Navigation
 * - As an anonymous user I should be able to use the top navigation
 */
namespace Tests\AN\Regressions;

use App\PassboltTestCase;

class PASSBOLT1346 extends PassboltTestCase
{

    /**
     * Scenario: As AN, passbolt logo at top left should not point to passbolt.com, but to the app url
     * Given I am on the home page
     * When  I click on the home link
     * Then  I go to the login page
     * When  I click on the login link
     * Then  I go to the login page
     * When  I click on the register link
     * Then  I go to the login page
     *
     * @group AN
     * @group regression
     * @group v2
     */
    public function testTopNavigationLink() 
    {
        $this->getUrl('/');
        $this->waitUntilTitleContain('Login');
        $this->clickLink('home');
        $this->waitUntilTitleContain('Login');
        $this->clickLink('login');
        $this->waitUntilTitleContain('Login');
        $this->clickLink('register');
        $this->waitUntilTitleContain('Register');
    }

}