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
 * Feature :  As a LU I shouldn't be able to edit a group
 *
 * Scenarios :
 *  - As a LU I shouldn't be able to edit a group
 *
 * @copyright (c) 2017-present Passbolt SARL
 * @licence   GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
namespace Tests\LU\Base;

use App\Actions\GroupActionsTrait;
use App\Actions\WorkspaceActionsTrait;
use App\Lib\UuidFactory;
use App\PassboltTestCase;
use Data\Fixtures\User;
use Data\Fixtures\Group;

class LUGroupEditTest extends PassboltTestCase
{
    use GroupActionsTrait;
    use WorkspaceActionsTrait;

    /**
     * Scenario: As LU I shouldn't be able to edit groups from the users workspace
     *
     * Given I am a LU
     * And   I am on the user workspace
     * When  I select a group
     * Then  I should see that there is no dropdown button next to the groups
     *
     * @group LU
     * @group group
     * @group edit
     * @group v2
     */
    public function testCantEditGroup() 
    {
        // Given I am a group manager
        $user = User::get('ping');

        // I am logged in as admin
        $this->loginAs($user);

        // I am on the user workspace
        $this->gotoWorkspace('user');

        // When I select a group
        $group = Group::get(['id' => UuidFactory::uuid('group.id.ergonom')]);
        $this->clickGroup($group['id']);

        // Then I should see that there is no dropdown button next to the groups
        $this->assertNotVisible("#group_${group['id']} .right-cell a");
    }

}
