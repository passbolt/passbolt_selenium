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
 * Feature: As a group manager I cannot delete groups
 *  - As a group manager I shouldn't be able to delete a group
 */
namespace Tests\GM\Base;

use App\Actions\WorkspaceActionsTrait;
use App\PassboltTestCase;
use Data\Fixtures\User;
use App\Lib\UuidFactory;

class GMGroupDeleteTest extends PassboltTestCase
{
    use WorkspaceActionsTrait;

    /**
     * Scenario: As a group manager I shouldn't be able to delete a group
     *
     * Given I am logged in as a group manager and I am on the users workspace
     * When  I click on the contextual menu button of a group on the right
     * Then  I should see the group contextual menu
     * And   I should see the “Edit group” option
     * And   I shouldn't see the "Delete group" option
     *
     * @group GM
     * @group group
     * @group delete
     * @group v2
     */
    public function testDeleteGroupRightClick() 
    {
        // Given I am logged in as an administrator
        $user = User::get('irene');
        
        $this->loginAs($user);
        $this->gotoWorkspace('user');

        // When I click on the contextual menu button of a group on the right
        $groupId = UuidFactory::uuid('group.id.ergonom');
        $this->click("#group_$groupId .right-cell a");

        // Then I should see the group contextual menu
        $this->assertVisible('js_contextual_menu');
        $this->assertVisible('js_group_browser_menu_edit');
        $this->assertNotVisible('js_group_browser_menu_remove');
    }
}