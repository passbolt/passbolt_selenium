<?php
/**
 * Bug PASSBOLT-1040 - Regression test
 *
 * @copyright (c) 2017 Passbolt SARL
 * @licence   GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class PASSBOLT1041 extends PassboltTestCase
{
    /**
     * The contextual menu should close after a click / not remain open.
     *  Given I am Ada
     * And the database is in the default state
     * And I am logged in on the password workspace
     * And I right click on an item I own
     * Then  I can see the contextual menu
     * When I click on the edit link
     * Then  I cannot see the contextual menu
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

        // Then  I can see the contextual menu
        $this->assertVisible('js_contextual_menu');

        // When I click on the edit link
        $this->click('#js_password_browser_menu_edit a');

        // Then  I cannot see the contextual menu
        $this->assertNotVisible('js_contextual_menu');
    }

    /**
     * The context menu should open every time I right click
     */
    public function testContextMenuOpenOnRightClick() 
    {
        // @TODO: in selenium (level: hard :)
        // Repeat in a fast fashion:
        // Mouse right click down on an item
        // Move the mouse on top of another row
        // Mouse right click up
        // Should show the contextual menu
    }
}