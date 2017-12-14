<?php
/**
 * Feature :  As a group manager I shouldn't be able to create groups
 *
 * Scenarios :
 *  - As a group manager I shouldn't be able to create groups from the users workspace
 *
 * @copyright (c) 2017-present Passbolt SARL
 * @licence   GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class GMGroupCreateTest extends PassboltTestCase
{

    /**
     * Scenario: As a group manager I shouldn't be able to create groups from the users workspace
     * Given        I am a group manager
     * And   I am logged in
     * When  I go to user workspace
     * Then  I shouldn't see a button create in the users workspace
     */
    public function testCantCreateGroup() 
    {
        // Given I am a group manager
        $user = User::get('ping');
        

        // I am logged in as admin
        $this->loginAs($user);

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