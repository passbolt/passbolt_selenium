<?php
/**
 * Bug PASSBOLT-2060 - Regression test
 *
 * @copyright (c) 2015-present Bolt Softwares Pvt Ltd
 * @licence   GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class PASSBOLT2060 extends PassboltSetupTestCase
{

    /**
     * Scenario: As a user I should be able to edit a group from the right sidebar
     *
     * Given I register an account as John Doe
     * When I complete the setup with a passphrase longer than 50 char
     * Then I am able to login
     */
    public function testSetupAndLoginWithLongPassphrase() 
    {
        $this->resetDatabaseWhenComplete();

        // Given I am logged in as a group manager
        $user = User::get('ping');
        
        $this->loginAs($user);

        // And I am on the users workspace
        $this->gotoWorkspace('user');

        // When I click a group name
        $group = Group::get(['id' => Uuid::get('group.id.it_support')]);
        $this->clickGroup($group['id']);

        // Then I should see a “edit” button next to the Information section
        $editButtonSelector = '#js_group_details #js_group_details_members #js_edit_members_button';
        $this->waitUntilISee($editButtonSelector);

        // When I press the “Edit” button
        $this->click($editButtonSelector);

        // Then I should see the Edit group dialog
        $this->waitUntilISee('.edit-group-dialog');

        // When I add a member to the group
        $ada = User::get('ada');
        $this->searchGroupUserToAdd($ada, $user);
        $this->addTemporaryGroupUser($ada);

        // Then I should see that the user is added in the list of group members
        // And I should see that his group role is “group member”
        $this->assertGroupMemberInEditDialog($group['id'], $ada);

        // And I should see a warning message saying that the changes will be applied after clicking on save
        $this->assertElementContainsText(
            $this->getTemporaryGroupUserElement($ada),
            'Will be added'
        );

        // When I press the save button
        $this->click('.edit-group-dialog a.button.primary');

        // Then I should see that the dialog disappears
        $this->waitUntilIDontSee('.edit-group-dialog');

        // And I should see a confirmation message saying that the group members have been edited
        $this->assertNotification('app_groups_edit_success');
    }
}