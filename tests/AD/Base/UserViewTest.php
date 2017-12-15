<?php
/**
 * Feature: As Admin I can view user information
 *
 * Scenarios :
 * - As an admin I should see the sidebar groups section updated when I create a group
 *
 * @copyright (c) 2017-present Passbolt SARL
 * @licence   GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class ADUserViewTest extends PassboltTestCase
{

    /**
     * @group saucelabs
     * Scenario: As an admin I should be able to distinguish visually inactive users
     *
     * Given I am logged in as Admin, and I go to the user workspace
     * When  I look at Orna who is a deactivated user
     * Then  I should see that the user is shown in a different color
     * When  I click on the user Orna
     * Then  I should see that the sidebar opens
     * And   I shouldn't see the group details in the sidebar
     * And   I shouldn't see the gpg key in the sidebar
     */
    public function testViewInactiveUser() 
    {
        // Given I am Ada
        $user = User::get('admin');
        

        // And I am logged in on the user workspace
        $this->loginAs($user);
        $this->gotoWorkspace('user');

        // When I click on a user
        $userO = User::get('orna');
        $id = $userO['id'];

        // I should see that the user is shown in a different color.
        $this->assertUserInactive($id);

        // When I click on the user "Orna"
        $this->clickUser($userO);

        // I should see that the sidebar opens.
        $this->waitUntilISee('.sidebar.user');

        // I should see the detailed information in the sidebar.
        $this->waitUntilISee('.sidebar.user .detailed-information');

        // I should see the groups information in the sidebar.
        $this->assertNotVisible('.sidebar.user .groups');

        // I should see the key information in the sidebar.
        $this->assertNotVisible('.sidebar.user .key-information');
    }

    /**
     * @group saucelabs
     * Scenario: As an admin I should see the sidebar groups section updated when I create a group
     *
     * Given I am logged in as Admin, and I go to the user workspace
     * When  I click on a user
     * And   I create a group where the user I selected is member of
     * Then  I should see the groups membership list updated with the new group
     */
    public function testUpdateSidebarGroupsListWhenCreateGroup() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Ada
        $user = User::get('admin');
        

        // And I am logged in on the user workspace
        $this->loginAs($user);
        $this->gotoWorkspace('user');

        // When I click on a user
        $userF = User::get('frances');
        $this->clickUser($userF);

        // And I create a group where the user I selected is member of.
        $group = ['name' => 'New group'];
        $users = ['frances'];
        $this->createGroup($group, $users, $user);

        // Then I should see a success notification
        $this->assertNotification('app_groups_add_success');

        // I should see the groups membership list updated with the new group
        $this->assertGroupUserInSidebar('New group', true);
    }

}
