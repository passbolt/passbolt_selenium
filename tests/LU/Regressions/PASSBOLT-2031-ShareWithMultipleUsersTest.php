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
 * Bug PASSBOLT-2031 - Regression test
 */
namespace Tests\LU\Regressions;

use App\Actions\MasterPasswordActionsTrait;
use App\Actions\PasswordActionsTrait;
use App\Actions\ShareActionsTrait;
use App\Actions\SidebarActionsTrait;
use App\Assertions\MasterPasswordAssertionsTrait;
use App\Assertions\PasswordAssertionsTrait;
use App\Assertions\PermissionAssertionsTrait;
use App\Assertions\SidebarAssertionsTrait;
use App\PassboltTestCase;
use Data\Fixtures\User;
use Data\Fixtures\Resource;
use App\Lib\UuidFactory;

class PASSBOLT2031 extends PassboltTestCase
{
    use MasterPasswordAssertionsTrait;
    use MasterPasswordActionsTrait;
    use PasswordActionsTrait;
    use PasswordAssertionsTrait;
    use PermissionAssertionsTrait;
    use ShareActionsTrait;
    use SidebarActionsTrait;
    use SidebarAssertionsTrait;

    /**
     * Scenario: As a user I can share a password with multiple users
     *
     * Given I am Carol
     * And   I am logged in on the password workspace
     * When  I go to the sharing dialog of a password I own
     * And   I give read access to multiple users/groups
     * And   I click on the save button
     * And   I see the passphrase dialog
     * And   I enter the passphrase and click submit
     * Then  I wait until I don't see  the encryption dialog anymore.
     * And   I can see the new permissions in sidebar
     *
     * @group LU
     * @group regression
     * @group broken
     * @group PASSBOLT-2555
     */
    public function testShareWithMultipleUsers() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Carol
        $user = User::get('ada');

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // When I go to the sharing dialog of a password I own
        $resource = Resource::get([
            'user' => 'ada',
            'id' => UuidFactory::uuid('resource.id.apache')
        ]);
        $this->gotoSharePassword(UuidFactory::uuid('resource.id.apache'));

        // And I give read access to multiple users/groups
        $this->addTemporaryPermission($resource, 'Accounting', $user);
        $this->addTemporaryPermission($resource, 'Freelancer', $user);
        $this->addTemporaryPermission($resource, 'grace', $user);
        $this->addTemporaryPermission($resource, 'ping', $user);

        // And I click on the save button
        $this->click('js_rs_share_save');

        // And I see the passphrase dialog
        $this->assertMasterPasswordDialog($user);

        // And I enter the passphrase and click submit
        $this->enterMasterPassword($user['MasterPassword']);

        // Then I wait until I don't see  the encryption dialog anymore.
        $this->waitUntilIDontSee('#passbolt-iframe-progress-dialog');
        $this->waitCompletion();

        // And I can see the new permissions in sidebar
        $this->assertPermissionInSidebar('Accounting', 'can read');
        $this->assertPermissionInSidebar('Freelancer', 'can read');
        $this->assertPermissionInSidebar('grace', 'can read');
        $this->assertPermissionInSidebar('ping', 'can read');
    }

}