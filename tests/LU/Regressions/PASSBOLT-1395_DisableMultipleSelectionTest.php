<?php
/**
 * Bug PASSBOLT-1377 - Regression test
 *
 * @copyright (c) 2017 Passbolt SARL
 * @licence   GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class PASSBOLT1395 extends PassboltTestCase
{

    /**
     * Scenario: As LU I can't select multiple passwprd
     *
     * Given        I am Ada
     * And          I am logged in on the password workspace
     * When         I click on a password checkbox
     * Then            I should see the password selected
     * When         I click on another password checkbox
     * Then         I should see only the last password selected
     */
    public function testCantSelectMultiplePasswords() 
    {
        // Given I am Ada
        $user = User::get('ada');
        

        // And I am logged on the password workspace
        $this->loginAs($user);

        // When I click on a user checkbox
        $rsA = Resource::get(array('user' => 'ada', 'id' => UuidFactory::uuid('resource.id.apache')));
        $this->click('multiple_select_checkbox_' . $rsA['id']);

        // Then  I should see it selected
        $this->isPasswordSelected($rsA['id']);

        // When click on another user checkbox
        $rsG =Resource::get(array('user' => 'ada', 'id' => UuidFactory::uuid('resource.id.gnupg')));
        $this->click('multiple_select_checkbox_' . $rsG['id']);

        // Then  I should see only the last user selected
        $this->assertPasswordSelected($rsG['id']);
        $this->assertPasswordNotSelected($rsA['id']);
    }

    /**
     * Scenario: As LU I can't select multiple users
     *
     * Given        I am logged in as admin in the user workspace
     * When         I click on a user checkbox
     * Then            I should see the user selected
     * When         I click on another user checkbox
     * Then         I should see only the last user selected
     */
    public function testCantSelectMultipleUsers() 
    {
        // Given I am Admin
        $user = User::get('admin');
        

        // And I am logged in on the user workspace
        $this->loginAs($user);

        // Go to user workspace
        $this->gotoWorkspace('user');

        // When I click on a user checkbox
        $userA = User::get('ada');
        $this->click('multiple_select_checkbox_' . $userA['id']);

        // Then  I should see it selected
        $this->isUserSelected($userA['id']);

        // When click on another user checkbox
        $userB = User::get('betty');
        $this->click('multiple_select_checkbox_' . $userB['id']);

        // Then  I should see only the last user selected
        $this->isUserNotSelected($userB['id']);
        $this->isUserSelected($userB['id']);
    }

}