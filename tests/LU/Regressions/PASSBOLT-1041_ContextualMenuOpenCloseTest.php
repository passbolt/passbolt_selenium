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
 * Bug PASSBOLT-1040 - Regression test
 */
namespace Tests\LU\Regressions;

use App\Actions\PasswordActionsTrait;
use App\PassboltTestCase;
use Data\Fixtures\User;
use Data\Fixtures\Resource;

class PASSBOLT1041 extends PassboltTestCase
{
    use PasswordActionsTrait;

    /**
     * The contextual menu should close after a click / not remain open.
     *
     * Given I am Ada
     * And   the database is in the default state
     * And   I am logged in on the password workspace
     * And   I right click on an item I own
     * Then  I can see the contextual menu
     * When  I click on the edit link
     * Then  I cannot see the contextual menu
     *
     * @group LU
     * @group regression
     */
    public function testContextualMenuMustCloseAfterClick() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Ada
        $user = User::get('ada');
        $resource = Resource::get(array('user' => 'ada'));

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // And I right click on an item I own
        $this->rightClickPassword($resource['id']);

        // Then I can see the contextual menu
        $this->assertVisibleByCss('js_contextual_menu');

        // When I click on the edit link
        $this->click('#js_password_browser_menu_edit a');

        // Then I cannot see the contextual menu
        $this->assertNotVisible('js_contextual_menu');
    }

    /**
     * The context menu should open every time I right click
     */
    public function testContextMenuOpenOnRightClick() 
    {
        $this->markTestIncomplete();
        // @TODO: in selenium (level: hard :)
        // Repeat in a fast fashion:
        // Mouse right click down on an item
        // Move the mouse on top of another row
        // Mouse right click up
        // Should show the contextual menu
    }
}