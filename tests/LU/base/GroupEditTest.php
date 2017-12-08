<?php
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverSelect;

/**
 * Feature :  As a LU I shouldn't be able to edit a group
 *
 * Scenarios :
 *  - As a LU I shouldn't be able to edit a group
 *
 * @copyright (c) 2017-present Passbolt SARL
 * @licence   GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class LUGroupEditTest extends PassboltTestCase
{

    /**
     * Scenario :   As LU I shouldn't be able to edit groups from the users workspace
     *
     * Given        I am a LU
     * And          I am on the user workspace
     * When         I select a group
     * Then         I should see that there is no dropdown button next to the groups
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
        $group = Group::get(['id' => Uuid::get('group.id.ergonom')]);
        $this->clickGroup($group['id']);

        // Then I should see that there is no dropdown button next to the groups
        $this->assertNotVisible("#group_${group['id']} .right-cell a");
    }

}
