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
 * Scenarios :
 * - As a user I should not be affected by any xss on the passwords workspace
 * - As a user I should not be affected by any xss on the users workspace
 * - As a user I should not be affected by any xss on the settings workspace
 */
namespace Tests\LU\Security;

use App\Actions\GroupActionsTrait;
use App\Actions\PasswordActionsTrait;
use App\Actions\ShareActionsTrait;
use App\Actions\SidebarActionsTrait;
use App\Actions\UserActionsTrait;
use App\Actions\WorkspaceActionsTrait;
use App\Assertions\MasterPasswordAssertionsTrait;
use App\Assertions\PasswordAssertionsTrait;
use App\Assertions\SecurityAssertionsTrait;
use App\Assertions\UserAssertionsTrait;
use App\Lib\UuidFactory;
use App\PassboltTestCase;
use Data\Fixtures\User;

class XssTest extends PassboltTestCase
{
    use GroupActionsTrait;
    use MasterPasswordAssertionsTrait;
    use PasswordActionsTrait;
    use PasswordAssertionsTrait;
    use SecurityAssertionsTrait;
    use ShareActionsTrait;
    use SidebarActionsTrait;
    use UserActionsTrait;
    use UserAssertionsTrait;
    use WorkspaceActionsTrait;

    /**
     * Scenario: As a user I should not be affected by any xss on the passwords workspace
     *
     * Given I am logged in as Xss0, and I go to the passwords workspace
     * Then  I should not see any XSS execution
     * When  I select each password one after the other
     * Then  I should not see any XSS execution
     * When  I edit each password one after the other
     * Then  I should not see any XSS execution
     * When  I share each password one after the other
     * Then  I should not see any XSS execution
     * When  I filter the workspace by group
     * Then  I should not see any XSS execution
     *
     * @group LU
     * @group password
     * @group password-workspace
     * @group security
     * @group v2
     */
    public function testPasswordsWorkspace()
    {
        // Given I am Xss0
        // And I am logged in on the password workspace
        $user = User::get('xss0');
        $this->loginAs($user);
        $this->assertXss();

        // Retrieve the passwords in the grid.
        $rows = $this->findAllByCss('#js_wsp_pwd_browser tbody tr');

        // For each password :
        // - Select
        // - Go to edit
        // - Go to share
        // - Click uri (@todo)
        foreach($rows as $i => $row) {
            $id = UuidFactory::uuid("resource.id.xss{$i}");

            // Assert password select
            $this->clickPassword($id);
            $this->clickSecondarySidebarSectionHeader('permissions');
            $this->waitUntilISee('#js_rs_details_permissions_list.ready');
            $this->clickSecondarySidebarSectionHeader('comments');
            $this->waitUntilISee('#js_rs_details_comments_list.ready');
            $this->assertXss();

            // Assert edit password dialog
            $this->gotoEditPassword($id);
            $this->assertXss();
            $this->click('.dialog-close');
            $this->waitUntilIDontSee('.dialog');

            // Assert share password dialog
            $this->gotoSharePassword($id);
            $this->assertXss();
            $this->click('.dialog-close');
            $this->waitUntilIDontSee('.dialog');

            // Assert password uri clic
            //$this->_assertXssUri($id);
        }

        // Retrieve all the groups in the left sidebar
        $rows = $this->findAllByCss('#js_wsp_password_categories_groups_list li');

        // For each group :
        // - Select
        foreach($rows as $i => $row) {
            $id = UuidFactory::uuid("group.id.xss{$i}");

            // Assert group filter select
            $this->clickGroup($id, 'password');
        }
    }

    /**
     * Assert password uri link.
     * @param string $id The password uuid
     *
     * @todo Behavior is not as expected, it does not execute XSS when it should on the Selenium server, but locally.
     */
    private function _assertXssUri($id)
    {
        $this->click('#js_pwd_details .detailed-information li.uri a');
//            $this->click("#resource_{$id} .js_grid_column_uri a");

        // Wait that the window is open
        try {
            $linkOpened = true;
            $this->waitUntil(function() {
                $windows = $this->getDriver()->getWindowHandles();
                if (count($windows) == 1) {
                    throw new \Exception('Cannot open the link');
                }
            }, [], 1);
        } catch (\Exception $e) {
            // There is chance the
            $linkOpened = false;
        }

        $this->assertXss();

        if (!$linkOpened) {
            $this->switchToWindow(1);
            $this->closeWindow();
            $this->switchToWindow(0);
        }
    }

    /**
     * Scenario: As a user I should not be affected by any xss on the users workspace
     *
     * Given I am logged in as Xss0, and I go to the users workspace
     * Then  I should not see any XSS execution
     * When  I select each user one after the other
     * Then  I should not see any XSS execution
     * When  I edit each password one after the other
     * Then  I should not see any XSS execution
     * When  I filter the workspace by group
     * Then  I should not see any XSS execution
     *
     * @group LU
     * @group user
     * @group user-workspace
     * @group security
     * @group v2
     */
    public function testUsersWorkspace()
    {
        // Given I am Xss0
        // And I am logged in on the password workspace
        $user = User::get('xss0');
        $this->loginAs($user);
        $this->gotoWorkspace('user');
        $this->assertXss();

        // For each xss user :
        // - Select
        // - Go to edit
        $i = 0;
        while(true) {
            // Try to find the xss user in the grid.
            $id = UuidFactory::uuid("user.id.xss{$i}");
            if (!$this->elementExists("#user_{$id} .cell_name")) {
                break;
            }

            // Assert user select
            $this->clickUser($id);
            $this->waitUntilISee('#js_user_groups_list.ready');
            $this->assertXss();
            // unselect the user
            $this->clickUser($id);

            // Assert edit user dialog
            $this->gotoEditUser($id);
            $this->assertXss();
            $this->click('.dialog-close');
            $this->waitUntilIDontSee('.dialog');

            $i++;
        }

        // For each group :
        // - Select
        $i = 0;
        while(true) {
            // Try to find the xss group in the list.
            $id = UuidFactory::uuid("group.id.xss{$i}");
            if (!$this->elementExists("#js_wsp_users_groups_list #group_{$id}")) {
                break;
            }

            // Assert group filter select
            $this->clickGroup($id);

            $i++;
        }
    }

    /**
     * Scenario: As a user I should not be affected by any xss on the settings workspace
     *
     * Given I am logged in as Xss0, and I go to the settings workspace
     * Then  I should not see any XSS execution
     * When  I click manage your keys
     * Then  I should not see any XSS execution
     *
     * @group LU
     * @group settings
     * @group settings-workspace
     * @group security
     * @group v2
     */
    public function testSettingsWorkspace()
    {
        // Given I am Xss0
        // And I am logged in on the password workspace
        $user = User::get('xss0');
        $this->loginAs($user);
        $this->gotoWorkspace('settings');
        $this->assertXss();

        // When Click on Manage your keys.
        $this->clickLink('Keys inspector');
        $this->waitUntilISee('.page.settings.keys');
        $this->assertXss();
    }
}
