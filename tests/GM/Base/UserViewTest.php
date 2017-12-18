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
 * Feature: As GM I can view user information
 * - As a Group Manager I should see the sidebar groups section updated when I update a group members
 */
namespace Tests\GM\Base;

use App\Actions\GroupActionsTrait;
use App\Actions\PasswordActionsTrait;
use App\Actions\UserActionsTrait;
use App\Actions\WorkspaceActionsTrait;
use App\Assertions\GroupAssertionsTrait;
use App\Assertions\WorkspaceAssertionsTrait;
use App\PassboltTestCase;
use Data\Fixtures\User;
use Data\Fixtures\Group;
use App\Lib\UuidFactory;

class GMUserViewTest extends PassboltTestCase
{
    use GroupActionsTrait;
    use GroupAssertionsTrait;
    use PasswordActionsTrait;
    use UserActionsTrait;
    use WorkspaceActionsTrait;
    use WorkspaceAssertionsTrait;

    /**
     * Scenario: As a Group Manager I should see the sidebar groups section updated when I update a group members
     *
     * Given I am logged in as Admin, and I go to the user workspace
     * When  I click on a user
     * And   I edit a group I am group manager
     * And   I add the selected user to the group
     * And   I click save
     * Then  I should see the groups membership list updated with the new group
     *
     * @group GM
     * @group group
     * @group view
     * @group saucelabs
     * @group broken
     */
    public function testUpdateSidebarGroupsListWhenUpdateGroup() 
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

        // And I edit a group I am group manager
        $group = Group::get(['id' => UuidFactory::uuid('group.id.operations')]);
        $this->gotoEditGroup($group['id']);

        // And I add the selected user to the group
        $this->searchGroupUserToAdd($userF, $user);
        $this->addTemporaryGroupUser($userF);

        // And I click save
        $this->click('.edit-group-dialog a.button.primary');

        // Then I should a success notification
        $this->assertNotification('app_groups_edit_success');

        // And I should see the groups membership list updated with the new group
        $this->assertGroupUserInSidebar($group['name'], false);
    }

}
