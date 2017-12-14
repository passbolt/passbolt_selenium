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
 * Feature :  As a logged in used I shouldn't be able to create groups
 *
 * Scenarios :
 *  - As a logged in user I shouldn't be able to create groups from the users workspace
 */
namespace Tests\LU\Base;

use App\Actions\WorkspaceActionsTrait;
use App\PassboltTestCase;
use Data\Fixtures\User;

class GroupCreateTest extends PassboltTestCase
{
    use WorkspaceActionsTrait;

    /**
     * Scenario: As a logged in user I shouldn't be able to create groups from the users workspace
     *
     * Given I am a user
     * And   I am logged in
     * When  I go to user workspace
     * Then  I shouldn't see a button create in the users workspace
     *
     * @group LU
     * @group group
     * @group create
     * @group v2
     */
    public function testCantCreateGroup() 
    {
        // Given I am LU.
        // I am logged in as admin
        $this->loginAs(User::get('ada'));

        // Go to user workspace
        $this->gotoWorkspace('user');

        // Then I shouldn't see the create button
        $this->assertElementNotContainText(
            $this->findByCss('.main-action-wrapper'),
            'create'
        );

        $this->assertNotVisible('#js_wsp_create_button');
    }
}