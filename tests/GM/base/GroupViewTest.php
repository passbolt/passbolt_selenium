<?php
/**
 * Feature :  As an group manager I can view group information
 *
 * Scenarios :
 *  - As group manager I can see the list users that are part of the group in the edit group dialog
 *
 * @copyright (c) 2017-present Passbolt SARL
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class GMGroupViewTest extends PassboltTestCase {

	/**
	 * Scenario: As a group manager I can see the list users that are part of the group in the edit group dialog
	 * Given that		I am logged in as a group manager
	 * And				I am on the users workspace
	 * When				I edit a group
	 * Then				I should see the list of users that are members of a given group
	 */
	function testViewGroupMemberFromEditDialog() {
		// Given I am logged in as an administrator
		$user = User::get('irene');
		$this->setClientConfig($user);
		$this->loginAs($user);

		// And I am on the users workspace
		// When I edit a group
		$group = Group::get(['id' => Uuid::get('group.id.ergonom')]);
		$this->gotoEditGroup($group['id']);

		// Then I should see the list of users that are members of a given group
		$groupMember = User::get('irene');
		$this->assertGroupMemberInEditDialog($group['id'], $groupMember, false);
	}

}
