<?php
/**
 * Feature :  As GM I can view user information
 *
 * Scenarios :
 * - As a Group Manager I should see the sidebar groups section updated when I update a group members
 *
 * @copyright (c) 2017-present Passbolt SARL
 * @licence   GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class GMUserViewTest extends PassboltTestCase
{

    /**
     * @group saucelabs
     * Scenario: As a Group Manager I should see the sidebar groups section updated when I update a group members
     *
     * Given I am logged in as Admin, and I go to the user workspace
     * When        I click on a user
     * And   I edit a group I am group manager
     * And   I add the selected user to the group
     * And   I click save
     * Then  I should see the groups membership list updated with the new group
     */
    public function testUpdateSidebarGroupsListWhenUpdateGroup() 
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

        // And I edit a group I am group manager
        $group = Group::get(['id' => UuidFactory::uuid('group.id.operations')]);
        $this->gotoEditGroup($group['id']);

        // And I add the selected user to the group
        $this->searchGroupUserToAdd($userF, $user);
        $this->addTemporaryGroupUser($userF);

        // And I click save
        $this->click('.edit-group-dialog a.button.primary');

        // Then I should a success notification
        $this->assertNotification('app_groups_edit_success');

        // And I should see the groups membership list updated with the new group
        $this->assertGroupUserInSidebar($group['name'], false);
    }

}
