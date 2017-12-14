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
 * Bug PASSBOLT-1103 - Regression test
 */
namespace Tests\LU\Regressions;

use App\Actions\PasswordActionsTrait;
use App\Actions\UserActionsTrait;
use App\Actions\WorkspaceActionsTrait;
use App\PassboltTestCase;
use Data\Fixtures\User;
use Data\Fixtures\Resource;

class PASSBOLT1103 extends PassboltTestCase
{
    use PasswordActionsTrait;
    use WorkspaceActionsTrait;
    use UserActionsTrait;

    /**
     * Scenario: The contextual menu should disappear after changing workspace
     *
     * Given I am logged in as Ada on the password workspace
     * And   I right click on a password
     * Then  I should see the contextual menu
     * When  I go to user workspace
     * Then  I should not see the contextual menu anymore
     * When  I right click on a user
     * Then  I should see the contextual menu
     * When  I go to password workspace
     * Then  I should not see the contextual menu
     * When  I right click again on the previous password where I had clicked.
     * Then  I should see again the contextual menu
     *
     * @group LU
     * @group regression
     */
    public function testContextualMenuDisappearAfterChangingWorkspace() 
    {
        // Given I am Ada
        $user = User::get('ada');

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // When I right click on a password I own
        $resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));
        $this->rightClickPassword($resource['id']);

        // Then I can see the contextual menu
        $this->assertVisibleByCss('js_contextual_menu');

        // When I change workspace
        $this->gotoWorkspace('user');

        // Then I shouldn't see the contextual menu anymore
        $this->assertNotVisible('js_contextual_menu');

        // And I right click on user betty
        $betty = User::get(array( 'user' => 'betty' ));
        $this->rightClickUser($betty['id']);

        // Then I can see the contextual menu
        $this->assertVisibleByCss('js_contextual_menu');

        // When I change workspace
        $this->gotoWorkspace('password');

        // Then I shouldn't see the contextual menu anymore
        $this->assertNotVisible('js_contextual_menu');

        // And I right click on the password I clicked before.
        $this->rightClickPassword($resource['id']);

        // Then I can see the contextual menu
        $this->assertVisibleByCss('js_contextual_menu');
    }
}