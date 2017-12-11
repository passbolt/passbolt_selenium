<?php
/**
 * Bug PASSBOLT-1620 - Regression test
 *
 * @copyright (c) 2017 Passbolt SARL
 * @licence   GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class PASSBOLT1620 extends PassboltTestCase
{

    /**
     * Scenario: As LU I can't select multiple passwprd
     *
     * Given        I am Ada
     * And          I am logged in on the users workspace
     * When         I select a user
     * And            I click on the recently modified filter
     * And             I click on the all users filter
     * Then         I shouldn't see duplicated users in the list
     */
    public function testNoDuplicateAfterSelectionAndFilterUserWorkspace() 
    {
        // Given I am Ada
        $user = User::get('ada');


        // And I am logged in on the password workspace
        $this->loginAs($user);
        $this->gotoWorkspace('user');

        // When I select a user
        $betty = User::get('betty');
        $this->clickUser($betty);

        // And I click on the recently modified filter
        $this->clickLink("Recently modified");
        $this->waitCompletion();

        // And I click on the all users filter
        $this->clickLink("All users");
        $this->waitCompletion();

        // Then  I shouldn't see duplicated users in the list
        $carol = User::get('carol');

        $duplicatesCarolUsername = $this->findAllByXpath('//*[@id="js_wsp_users_browser"]//*[contains(text(), "' . $carol['Username'] . '")]');
        $this->assertEquals(1, count($duplicatesCarolUsername));
    }

}