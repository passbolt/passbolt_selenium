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
 * As an AN on the registration page I can click on the legal disclaimer links
 *
 * @copyright (c) 2017 Passbolt SARL
 * @licence   GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
namespace Tests\AN\Regressions;

use App\PassboltTestCase;

class PASSBOLT1368 extends PassboltTestCase
{

    /**
     * Scenario: As an AN on the registration page I can click on the legal disclaimer links
     *
     * Given I am on the register page
     * When  I on the privacy link
     * Then  I go the passbolt.com/privacy page
     * When  I on the Terms of Service
     * Then  I go the passbolt.com/terms page
     *
     * @group AN
     * @group regression
     * @group v2
     */
    public function testLegalDisclaimerNavigation() 
    {
        $this->getUrl('/register');
        $this->waitUntilTitleContain('Register');
        $this->clickLink('Privacy Policy');
        $this->waitUntilTitleContain('Privacy');
        $this->getUrl('/register');
        $this->waitUntilTitleContain('Register');
        $this->clickLink('Terms of Service');
        $this->waitUntilTitleContain('Terms');
    }

}