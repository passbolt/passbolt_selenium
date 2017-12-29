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
 * Feature : User Workspace and group feature
 *
 * - As a user I can browse the list of all the groups in the groups section of the user workspace
 * - As a user I can see the list users that are part of the group in the users grid by using the group filter
 */
namespace Tests\LU\Base;

use App\Actions\GroupActionsTrait;
use App\Actions\WorkspaceActionsTrait;
use App\Assertions\GroupAssertionsTrait;
use App\Lib\UuidFactory;
use App\PassboltTestCase;
use Data\Fixtures\Group;
use Data\Fixtures\User;

class UserWorkspaceGroupTest extends PassboltTestCase
{
    use WorkspaceActionsTrait;
    use GroupActionsTrait;
    use GroupAssertionsTrait;

    /**
     * Scenario: As a user I can browse the list of all the groups in the groups section of the user workspace
     *
     * Given I am logged in as LU, and I go to the user workspace
     * Then  I should see the list of groups available in the left hand sidebar
     *
     * @group LU
     * @group user
     * @group user-group
     * @group saucelabs
     * @group v2
     */
    public function testGroupIndex() 
    {
        // Given I am Ada
        $user = User::get('ada');

        // And I am logged in the user workspace
        $this->loginAs($user);
        $this->gotoWorkspace('user');

        // Get list of groups.
        $groups = Group::_get();

        // Find all groups listed in the groups list.
        foreach ($groups as $group) {
            $eltGroup = $this->findByXpath("//ul[@id='js_wsp_users_groups_list']/li/div//span[text()='" . $group['name'] . "']");
            $this->assertEquals($eltGroup->getText(), $group['name']);
        }
    }

    /**
     * Scenario: As a user I can see the list users that are part of the group in the users grid by using the group filter
     *
     * Given I am logged in as LU, and I go to the user workspace
     * When  I click on a group name
     * Then  I should see that the given group is selected
     * And   I should see that the list of users display only the users that are part of this group.
     *
     * @group LU
     * @group user
     * @group user-group
     * @group saucelabs
     * @group v2
     */
    public function testFilterUsersByGroup() 
    {
        // Given I am Ada
        // And I am logged in on the user workspace
        $this->loginAs(User::get('ada'));
        $this->gotoWorkspace('user');

        // When I click on a group name
        $group = Group::get(['id' => UuidFactory::uuid('group.id.ergonom')]);
        $this->clickGroup($group['id']);

        // Then I should see that the given group is selected
        $this->assertGroupSelected($group['id']);

        // And I should see that the list of users display only the users that are part of this group.
        $users = $this->findAllByCss('#js_wsp_users_browser .tableview-content tr');
        $this->assertEquals(1, count($users));
        $this->assertElementContainsText(
            $this->findByCss('#js_wsp_users_browser .tableview-content'),
            'irene@passbolt.com'
        );
    }
}
