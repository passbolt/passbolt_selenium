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
 * Feature: As an administrator I can view group information
 *
 * Scenarios :
 *  - As administrator I can see the list users that are part of the group in the edit group dialog
 *  - As administrator I can see the list users that are part of the group in the sidebar
 *
 * @copyright (c) 2017-present Passbolt SARL
 * @licence   GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
namespace Tests\AD\Base;

use App\Actions\GroupActionsTrait;
use App\Actions\SidebarActionsTrait;
use App\Actions\WorkspaceActionsTrait;
use App\Assertions\GroupAssertionsTrait;
use App\Assertions\SidebarAssertionsTrait;
use App\PassboltTestCase;
use App\Lib\UuidFactory;
use Data\Fixtures\User;
use Data\Fixtures\Group;

class ADGroupViewTest extends PassboltTestCase
{
    use GroupActionsTrait;
    use GroupAssertionsTrait;
    use SidebarActionsTrait;
    use SidebarAssertionsTrait;
    use WorkspaceActionsTrait;

    /**
     * Scenario: As administrator I can see the list users that are part of the group in the edit group dialog
     *
     * Given I am logged in as an administrator
     * And   I am on the users workspace
     * When  I edit a group
     * Then  I should see the list of users that are members of a given group
     *
     * @group AD
     * @group group
     * @group view
     * @group v2
     */
    function testViewGroupMemberFromEditDialog() 
    {
        // Given I am logged in as an administrator
        $user = User::get('admin');

        $this->loginAs($user);

        // And I am on the users workspace
        // When I edit a group
        $group = Group::get(['id' => UuidFactory::uuid('group.id.ergonom')]);
        $this->gotoEditGroup($group['id']);

        // Then I should see the list of users that are members of a given group
        $groupMember = User::get('irene');
        $this->assertGroupMemberInEditDialog($group['id'], $groupMember, true);
    }

    /**
     * Scenario: As administrator I can see the list users that are part of the group in the sidebar
     *
     * Given I am a logged-in user
     * And   I am on the users workspace
     * When  I click on a group name
     * Then  I should see that the sidebar contains a member section
     * And   I should see that the members sections contains the list of users that are members of this group
     * And   I should see that below each user I can see his membership type
     *
     * @group AD
     * @group group
     * @group view
     * @group v2
     */
    function testViewGroupMemberFromSidebar() 
    {
        // Given I am logged in as an administrator
        $user = User::get('admin');

        $this->loginAs($user);

        // And I am on the users workspace
        $this->gotoWorkspace('user');

        // Assert group members in sidebar
        $group = Group::get(['id' => UuidFactory::uuid('group.id.accounting')]);
        $groupMember = User::get('frances');
        $this->assertGroupMemberInSidebar($group['id'], $groupMember, true);
        $groupMember = User::get('grace');
        $this->assertGroupMemberInSidebar($group['id'], $groupMember, false);
    }

}
