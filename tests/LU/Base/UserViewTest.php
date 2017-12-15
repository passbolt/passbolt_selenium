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
 * Feature: As LU I can view user information
 *
 * Scenarios :
 * - As a user I should be able to view the user details
 */
namespace Tests\LU\Base;

use App\Actions\UserActionsTrait;
use App\Actions\WorkspaceActionsTrait;
use App\Assertions\GroupAssertionsTrait;
use App\PassboltTestCase;
use Data\Fixtures\User;

class UserViewTest extends PassboltTestCase
{
    use WorkspaceActionsTrait;
    use UserActionsTrait;
    use GroupAssertionsTrait;

    /**
     * Scenario: As a user I should be able to view the user details
     *
     * Given I am logged in as Ada, and I go to the user workspace
     * When  I click on a user
     * Then I should see a secondary side bar appearing
     * And   I should see the details of the selected user
     * And   I should see the user's role
     * And   I should see the user's modified time
     * And   I should see the user's groups membership section
     * And   I should see the user's groups name and user roles
     * And   I should see the user's key id
     * And   I should see the user's key type
     * And   I should see the user's key created time
     * And   I should see the user's key expires time
     * And   I should see the user's key public key
     *
     * @group LU
     * @group user
     * @group view
     * @group saucelabs
     */
    public function testUsersDetails() 
    {
        // Given I am Ada
        $user = User::get('ada');

        // And I am logged in on the user workspace
        $this->loginAs($user);
        $this->gotoWorkspace('user');

        // When I click on a user
        $userF = User::get('frances');
        $this->clickUser($userF);

        // Then I should see a secondary side bar appearing
        $this->assertPageContainsElement('#js_user_details');

        // And I should see the details of the selected user
        $userDetails = [
        'role'             => 'User',
        'modified'         => '/ago$/',
        'keyid'         => '477FB14C',
        'type'             => 'RSA',
        'created'        => '/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/',
        'expires'        => '/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/',
        ];
        $userDetails['key'] = trim(file_get_contents(GPG_FIXTURES . DS . 'frances_public.key'));

        // And I should see the user's role
        $cssSelector = '#js_user_details .detailed-information li.role';
        $this->assertElementContainsText(
            $this->findByCss($cssSelector),
            $userDetails['role']
        );
        // And I should see the user's modified time
        $cssSelector = '#js_user_details .detailed-information li.modified';
        $this->assertElementContainsText(
            $this->findByCss($cssSelector),
            $userDetails['modified']
        );
        // And I should see the user's groups membership section
        $cssSelector = '#js_user_details #js_user_groups h4';
        $this->assertElementContainsText(
            $this->findByCss($cssSelector),
            'Groups'
        );
        // And I should see the user's groups name and user roles
        $this->assertGroupUserInSidebar('Accounting', true);
        // And I should see the user's key id
        $cssSelector = '#js_user_details .key-information li.keyid';
        $this->assertElementContainsText(
            $this->findByCss($cssSelector),
            $userDetails['keyid']
        );
        // And I should see the user's key type
        $cssSelector = '#js_user_details .key-information li.type';
        $this->assertElementContainsText(
            $this->findByCss($cssSelector),
            $userDetails['type']
        );
        // And I should see the user's key created time
        $cssSelector = '#js_user_details .key-information li.created';
        $this->assertElementContainsText(
            $this->findByCss($cssSelector),
            $userDetails['created']
        );
        // And I should see the user's key expires time
        $cssSelector = '#js_user_details .key-information li.expires';
        $this->assertElementContainsText(
            $this->findByCss($cssSelector),
            $userDetails['expires']
        );
        // And I should see the user's key public key
        $cssSelector = '#js_user_details .key-information li.gpgkey';
        $this->assertElementContainsText(
            $this->findByCss($cssSelector),
            $userDetails['key']
        );
    }

}
