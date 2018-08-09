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
 * Feature: As LU I can view group information
 *
 * Scenarios :
 *  - As a user I can see a group information from the sidebar
 *  - As user I can see the list users that are part of the group in the sidebar
 */
namespace Tests\LU\Base;

use App\Actions\GroupActionsTrait;
use App\Actions\SidebarActionsTrait;
use App\Actions\WorkspaceActionsTrait;
use App\Assertions\GroupAssertionsTrait;
use App\Assertions\SidebarAssertionsTrait;
use App\PassboltTestCase;
use Data\Fixtures\User;
use Data\Fixtures\Group;
use App\Lib\UuidFactory;
use Facebook\WebDriver\WebDriverBy;

class GroupViewTest extends PassboltTestCase
{
    use GroupActionsTrait;
    use GroupAssertionsTrait;
    use SidebarActionsTrait;
    use SidebarAssertionsTrait;
    use WorkspaceActionsTrait;

    /**
     * Scenario: As a user I should be able to see the group detail using route
     *
     * When  I am logged in as Ada
     * And   I enter the route in the url
     * Then  I should see the group detail
     *
     * @group LU
     * @group group
     * @group group-view
     * @group saucelabs
     * @group v2
     */
    public function testRoute_ViewGroup()
    {
        $this->loginAs(User::get('admin'), ['url' => '/app/groups/view/36563004-3f25-50c0-b22e-6554c3ccc4e7']);
        $this->waitCompletion();
        $this->waitUntilISee('#js_group_details.ready');
    }

    /**
     * Scenario: As a user I should be able to see the group memberships using route
     *
     * When  I am logged in as Ada
     * And   I enter the route in the url
     * Then  I should see the group memberships
     *
     * @group LU
     * @group group
     * @group group-view
     * @group saucelabs
     * @group v2
     */
    public function testRoute_ViewGroupMemberships()
    {
        $this->loginAs(User::get('admin'), ['url' => '/app/groups/view/36563004-3f25-50c0-b22e-6554c3ccc4e7/membership']);
        $this->waitCompletion();
        $this->waitUntilISee('#js_group_details.ready #js_group_details_members #js_group_details_group_members_list.ready');
    }

    /**
     * Scenario: As a user I can see a group information from the sidebar
     *
     * Given I am a logged-in user
     * And   I am on the users workspace
     * When  I select a group
     * Then  I should see the sidebar opening on the right hand side
     * And   I should see a group information section
     * And   I should see the created date
     * And   I should see the modified date
     * And   I should see the latest user who modified the group
     * And   I should see the number of members
     * And   I should see the number of passwords
     *
     * @group LU
     * @group group
     * @group group-view
     * @group v2
     */
    function testViewGroupInfoFromSidebar() 
    {
        // Given that I am a logged-in user
        $this->loginAs(User::get('ada'));

        // And I am on the users workspace
        $this->gotoWorkspace('user');

        // When I select a group
        $group = Group::get(['id' => UuidFactory::uuid('group.id.accounting')]);
        $this->clickGroup($group['id']);

        // Then I should see the sidebar opening on the right hand side
        $this->waitUntilISee('#js_group_details.ready');

        // And I should see a group information section
        $this->assertElementContainsText(
            '#js_group_details .sidebar-header',
            $group['name']
        );
        $this->assertVisibleByCss('#js_group_details .detailed-information');

        // And I should see the created date
        $selector = '#js_group_details .detailed-information li.created .value';
        $elt = $this->find($selector);
        $this->assertNotEmpty($elt->getText());

        // And I should see the modified date
        $selector = '#js_group_details .detailed-information li.modified .value';
        $elt = $this->find($selector);
        $this->assertNotEmpty($elt->getText());

        // And I should see the latest user who modified the group
        $selector = '#js_group_details .detailed-information li.modified_by .value';
        $elt = $this->find($selector);
        sleep(10);
        $this->assertNotEmpty($elt->getText());

        // And I should see the number of members
        $selector = '#js_group_details .detailed-information li.members .value';
        $elt = $this->find($selector);
        $this->assertNotEmpty($elt->getText());

        // @todo
        // And I should see the number of passwords
    }

    /**
     * Scenario: As user I can see the list users that are part of the group in the sidebar
     *
     * Given I am a logged-in user
     * And   I am on the users workspace
     * When  I click on a group name
     * Then  I should see that the sidebar contains a member section
     * And   I should see that the members sections contains the list of users that are members of this group
     * And   I should see that below each user I can see his membership type
     *
     * @group LU
     * @group group
     * @group group-view
     * @group v2
     */
    function testViewGroupMemberFromSidebar() 
    {
        // Given I am logged in as a user
        $this->loginAs(User::get('ada'));

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
