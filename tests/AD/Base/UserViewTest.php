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
 * Feature: As Admin I can view user information
 *
 * Scenarios :
 * - As an admin I should see the sidebar groups section updated when I create a group
 */
namespace Tests\AD\Base;

use App\Actions\GroupActionsTrait;
use App\Actions\UserActionsTrait;
use App\Actions\WorkspaceActionsTrait;
use App\Assertions\GroupAssertionsTrait;
use App\Assertions\UserAssertionsTrait;
use App\Assertions\WorkspaceAssertionsTrait;
use App\PassboltTestCase;
use Data\Fixtures\User;

class ADUserViewTest extends PassboltTestCase
{
    use UserActionsTrait;
    use UserAssertionsTrait;
    use GroupActionsTrait;
    use GroupAssertionsTrait;
    use WorkspaceActionsTrait;
    use WorkspaceAssertionsTrait;

    /**
     * Scenario: As an admin I should be able to distinguish visually inactive users
     *
     * Given I am logged in as Admin, and I go to the user workspace
     * When  I look at Orna who is a deactivated user
     * Then  I should see that the user is shown in a different color
     * When  I click on the user Orna
     * Then  I should see that the sidebar opens
     * And   I shouldn't see the group details in the sidebar
     * And   I shouldn't see the gpg key in the sidebar
     *
     * @group AD
     * @group user
     * @group view
     * @group saucelabs
     */
    public function testViewInactiveUser() 
    {
        // Given I am Ada
        // And I am logged in on the user workspace
        $this->loginAs(User::get('admin'));
        $this->gotoWorkspace('user');

        // When I click on a user
        $userO = User::get('orna');
        $id = $userO['id'];

        // I should see that the user is shown in a different color.
        $this->assertUserInactive($id);

        // When I click on the user "Orna"
        $this->clickUser($userO);

        // I should see that the sidebar opens.
        $this->waitUntilISee('.sidebar.user');

        // I should see the detailed information in the sidebar.
        $this->waitUntilISee('.sidebar.user .detailed-information');

        // I should see the groups information in the sidebar.
        $this->assertNotVisibleByCss('.sidebar.user .groups');

        // I should see the key information in the sidebar.
        $this->assertNotVisibleByCss('.sidebar.user .key-information');
    }

    /**
     * Scenario: As an admin I should see the sidebar groups section updated when I create a group
     *
     * Given I am logged in as Admin, and I go to the user workspace
     * When  I click on a user
     * And   I create a group where the user I selected is member of
     * Then  I should see the groups membership list updated with the new group
     *
     * @group AD
     * @group user
     * @group view
     * @group saucelabs
     */
    public function testUpdateSidebarGroupsListWhenCreateGroup() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Ada
        $user = User::get('admin');

        // And I am logged in on the user workspace
        $this->loginAs($user);
        $this->gotoWorkspace('user');

        // When I click on a user
        $userF = User::get('frances');
        $this->clickUser($userF);

        // And I create a group where the user I selected is member of.
        $group = ['name' => 'New group'];
        $users = ['frances'];
        $this->createGroup($group, $users, $user);

        // Then I should see a success notification
        $this->assertNotification('app_groups_add_success');

        // I should see the groups membership list updated with the new group
        $this->assertGroupUserInSidebar('New group', true);
    }
}
