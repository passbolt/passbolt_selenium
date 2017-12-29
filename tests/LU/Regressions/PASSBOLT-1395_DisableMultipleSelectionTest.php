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
 * Bug PASSBOLT-1377 - Regression test
 */
namespace Tests\LU\Regressions;

use App\Actions\PasswordActionsTrait;
use App\Actions\WorkspaceActionsTrait;
use App\Assertions\PasswordAssertionsTrait;
use App\Assertions\UserAssertionsTrait;
use App\PassboltTestCase;
use App\Lib\UuidFactory;
use Data\Fixtures\User;
use Data\Fixtures\Resource;

class PASSBOLT1395 extends PassboltTestCase
{
    use PasswordActionsTrait;
    use PasswordAssertionsTrait;
    use WorkspaceActionsTrait;
    use UserAssertionsTrait;

    /**
     * Scenario: As LU I can't select multiple passwprd
     *
     * Given I am Ada
     * And   I am logged in on the password workspace
     * When  I click on a password checkbox
     * Then  I should see the password selected
     * When  I click on another password checkbox
     * Then  I should see only the last password selected
     *
     * @group LU
     * @group regression
     * @group v2
     */
    public function testCantSelectMultiplePasswords() 
    {
        // Given I am Ada
        // And I am logged on the password workspace
        $this->loginAs(User::get('ada'));

        // When I click on a user checkbox
        $rsA = Resource::get(array('user' => 'ada', 'id' => UuidFactory::uuid('resource.id.apache')));
        $this->click('multiple_select_checkbox_' . $rsA['id']);

        // Then I should see it selected
        $this->isPasswordSelected($rsA['id']);

        // When click on another user checkbox
        $rsG = Resource::get(array('user' => 'ada', 'id' => UuidFactory::uuid('resource.id.gnupg')));
        $this->click('multiple_select_checkbox_' . $rsG['id']);

        // Then I should see only the last user selected
        $this->assertPasswordSelected($rsG['id']);
        $this->assertPasswordNotSelected($rsA['id']);
    }

    /**
     * Scenario: As LU I can't select multiple users
     *
     * Given I am logged in as admin in the user workspace
     * When  I click on a user checkbox
     * Then  I should see the user selected
     * When  I click on another user checkbox
     * Then  I should see only the last user selected
     *
     * @group LU
     * @group regression
     * @group v2
     */
    public function testCantSelectMultipleUsers() 
    {
        // Given I am Admin
        // And I am logged in on the user workspace
        $this->loginAs(User::get('admin'));

        // Go to user workspace
        $this->gotoWorkspace('user');

        // When I click on a user checkbox
        $userA = User::get('ada');
        $this->click('multiple_select_checkbox_' . $userA['id']);

        // Then I should see it selected
        $this->isUserSelected($userA['id']);

        // When click on another user checkbox
        $userB = User::get('betty');
        $this->click('multiple_select_checkbox_' . $userB['id']);

        // Then I should see only the last user selected
        $this->isUserNotSelected($userB['id']);
        $this->isUserSelected($userB['id']);
    }

}