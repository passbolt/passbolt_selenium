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

use App\Actions\MasterPasswordActionsTrait;
use App\Actions\PasswordActionsTrait;
use App\Actions\ShareActionsTrait;
use App\Assertions\MasterPasswordAssertionsTrait;
use App\Assertions\PasswordAssertionsTrait;
use App\Assertions\WorkspaceAssertionsTrait;
use App\PassboltTestCase;
use Data\Fixtures\User;
use Data\Fixtures\Resource;
use App\Lib\UuidFactory;

class PASSBOLT1377 extends PassboltTestCase
{
    use PasswordAssertionsTrait;
    use PasswordActionsTrait;
    use ShareActionsTrait;
    use WorkspaceAssertionsTrait;
    use MasterPasswordAssertionsTrait;
    use MasterPasswordActionsTrait;

    /**
     * Scenario: As a user I can login & logout multiple times
     *
     * Given I am ada
     * [LOOP]
     * When  I login
     * And   I logout
     * Then  I should see the login page
     * [END_LOOP]
     *
     * @group LU
     * @group regression
     * @group v2
     */
    public function testLoginLogoutMultipleTimes() 
    {
        // Given I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

        for ($i=0; $i<3; $i++) {
            // When I am logged in on the user workspace
            $this->loginAs($user, ['setConfig' => false]);

            // And I logout
            $this->logout();

            // Then I should be redirected to the login page
            $this->waitUntilISee('.plugin-check.' . $this->_browser['type'] . '.success');
        }
    }

    /**
     * Scenario: As LU I can create a password mutliple times
     *
     * Given I am logged in as ada in the user workspace
     * [LOOP]
     * When  I am creating a password
     * Then  I should expect the password has been created with success
     * [END_LOOP]
     *
     * @group LU
     * @group regression
     * @group v2
     */
    public function testCreatePasswordMultipleTimes() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am logged in as ada in the user workspace
        $user = User::get('ada');

        $this->loginAs($user);

        for ($i=0; $i<3; $i++) {
            // And I am creating the password
            // Then I can see a success notification
            $password = array(
            'name' => 'name_' . $i,
            'username' => 'username_' . $i,
            'password' => 'password_' . $i
            );
            $this->createPassword($password);

            // Wait until notification disappears.
            $this->waitUntilNotificationDisappears('app_resources_add_success');
        }
    }

    /**
     * Scenario: As LU I can edit a password mutliple times
     *
     * Given I am logged in as ada in the user workspace
     * [LOOP]
     *   When  I am editing a password I own
     *   Then  I should expect the password has been edited with success
     * [END_LOOP]
     *
     * @group no-saucelabs
     * @group LU
     * @group regression
     * @group v2
     */
    public function testEditPasswordMultipleTimes() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am logged in as ada in the user workspace
        $user = User::get('ada');

        $this->loginAs($user);

        $resource = Resource::get([
            'user' => 'ada',
            'permission' => 'owner'
        ]);

        for ($i=0; $i<3; $i++) {
            // And I am editing the secret of a password I own
            // Then I can see a success notification
            $r['id'] = $resource['id'];
            $r['password'] = 'password_' . $i;
            $this->editPassword($r, $user);

            // Wait until notification disappears.
            $this->waitUntilNotificationDisappears('app_resources_update_success');
        }
    }

    /**
     * Scenario: As LU I can share a password mutliple times
     *
     * Given I am logged in as ada in the user workspace
     * [LOOP]
     * When  I am sharing a password I own
     * Then  I should expect the password has been shared with success
     * [END_LOOP]
     *
     * @group LU
     * @group regression
     * @group v2
     */
    public function testSharePasswordMultipleTimes() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am logged in as ada in the user workspace
        $user = User::get('ada');

        $this->loginAs($user);

        $resource = Resource::get([
            'id' => UuidFactory::uuid('resource.id.apache'),
            'user' => 'ada'
        ]);
        $shareWith = [
            'frances',
            'edith',
            'admin'
        ];

        for ($i=0; $i<count($shareWith); $i++) {
            // And I am editing the secret of a password I own
            // Then I can see a success notification
            $r['id'] = $resource['id'];
            $r['password'] = 'password_' . $i;
            $this->sharePassword($resource, $shareWith[$i], $user);
            $this->waitUntilNotificationDisappears('app_share_share_success');
        }
    }
}