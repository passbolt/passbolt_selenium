<?php
/**
 * Feature :  As LU I can view group information
 *
 * Scenarios :
 *  - As a user I can see a group information from the sidebar
 *  - As user I can see the list users that are part of the group in the sidebar
 *
 * @copyright (c) 2017-present Passbolt SARL
 * @licence   GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class LUGroupViewTest extends PassboltTestCase
{

    /**
     * Scenario: As a user I can see a group information from the sidebar
     *
     * Given I am a logged-in user
     * And I am on the users workspace
     * When I select a group
     * Then  I should see the sidebar opening on the right hand side
     * And I should see a group information section
     * And I should see the created date
     * And I should see the modified date
     * And I should see the latest user who modified the group
     * And I should see the number of members
     * And I should see the number of passwords
     */
    function testViewGroupInfoFromSidebar() 
    {
        // Given that I am a logged-in user
        $user = User::get('ada');
        
        $this->loginAs($user);

        // And I am on the users workspace
        $this->gotoWorkspace('user');

        // When I select a group
        $group = Group::get(['id' => UuidFactory::uuid('group.id.accounting')]);
        $this->clickGroup($group['id']);

        // Then  I should see the sidebar opening on the right hand side
        $this->waitUntilISee('#js_group_details.ready');

        // And I should see a group information section
        $this->assertElementContainsText(
            '#js_group_details .header',
            $group['name']
        );
        $this->assertVisible('#js_group_details .detailed-information');

        // And I should see the created date
        $selector = '#js_group_details .detailed-information li.created .value';
        $elt = $this->find($selector);
        $this->assertNotEmpty($elt->getText());

        // And I should see the modified date
        $selector = '#js_group_details .detailed-information li.modified .value';
        $elt = $this->find($selector);
        $this->assertNotEmpty($elt->getText());

        // And I should see the latest user who modified the group
        $selector = '#js_group_details .detailed-information li.modified_by .value';
        $elt = $this->find($selector);
        $this->assertNotEmpty($elt->getText());

        // And I should see the number of members
        $selector = '#js_group_details .detailed-information li.members .value';
        $elt = $this->find($selector);
        $this->assertNotEmpty($elt->getText());

        // @todo
        // And I should see the number of passwords
    }

    /**
     * Scenario: As user I can see the list users that are part of the group in the sidebar
     *
     * Given I am a logged-in user
     * And I am on the users workspace
     * When I click on a group name
     * Then  I should see that the sidebar contains a member section
     * And I should see that the members sections contains the list of users that are members of this group
     * And I should see that below each user I can see his membership type
     */
    function testViewGroupMemberFromSidebar() 
    {
        // Given I am logged in as a user
        $user = User::get('ada');
        
        $this->loginAs($user);

        // And I am on the users workspace
        $this->gotoWorkspace('user');

        // Assert group members in sidebar
        $group = Group::get(['id' => UuidFactory::uuid('group.id.accounting')]);
        $groupMember = User::get('frances');
        $this->assertGroupMemberInSidebar($group['id'], $groupMember, true);
        $groupMember = User::get('grace');
        $this->assertGroupMemberInSidebar($group['id'], $groupMember, false);
    }

}
