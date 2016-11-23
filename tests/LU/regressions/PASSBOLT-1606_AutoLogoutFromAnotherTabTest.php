<?php
/**
 * Bug PASSBOLT-1606 - Regression test
 *
 * @copyright (c) 2015-present Bolt Softwares Pvt Ltd
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class PASSBOLT1606 extends PassboltTestCase {

    /**
     * Scenario: As LU I can't select multiple passwprd
     *
     * Given        I am Ada
     * And          I am logged in on the users workspace
     * When         I select a user
     * And			I click on the recently modified filter
     * And 			I click on the all users filter
     * Then         I shouldn't see duplicated users in the list
     *
     */
    public function testAutoLogoutFromAnotherTab() {
        // Given I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

        // And I am on second tab
        $this->openNewTab();

        // Reduce the session timeout to accelerate the test
        PassboltServer::setExtraConfig([
            'Session' => [
                'timeout' => 0.10
            ]
        ]);

        // When I am logged in on the password workspace
        $this->loginAs($user);

        // Then I should see the session expired dialog
        $this->assertSessionExpiredDialog();

        // And I switch to the previous
        $this->switchToPreviousTab();

        // And I wait until the expired dialog redirect the user to the login page
        sleep(10);

        // And I switch to the passbolt tab
        $this->switchToNextTab();

        // Then I should see the login page
        $this->waitUntilISee('.plugin-check.' . $this->_browser['type'] . '.success');
    }

}