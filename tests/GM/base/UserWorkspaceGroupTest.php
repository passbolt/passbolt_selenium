<?php
/**
 * Feature : User Workspace and group feature
 *
 * - As a group manager I can see the list users that are part of the group in the users grid by using the group filter
 *
 * @copyright (c) 2017 Passbolt SARL
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class GMWorkspaceGroupTest extends PassboltTestCase {

	/**
	 * @group saucelabs
	 * Scenario :   As a group manager I can see the list users that are part of the group in the users grid by using the group filter
	 *
	 * Given        I am logged in as GM, and I go to the user workspace
	 * When         I click on a group name
	 * Then         I should see that the given group is selected
	 * And          I should see that the list of users display only the users that are part of this group.
	 */
	public function testFilterUsersByGroup() {
		// Given I am Irene
		$user = User::get('irene');
		$this->setClientConfig($user);

		// And I am logged in on the user workspace
		$this->loginAs($user);
		$this->gotoWorkspace('user');

		// When I click on a group name
		$group = Group::get(['id' => Uuid::get('group.id.ergonom')]);
		$this->clickGroup($group['id']);

		// Then I should see that the given group is selected
		$this->assertGroupSelected($group['id']);

		// And I should see that the list of users display only the users that are part of this group.
		$users = $this->findAllByCss('#js_wsp_users_browser .tableview-content tr');
		$this->assertEquals(1, count($users));
		$this->assertElementContainsText(
			$this->findByCss('#js_wsp_users_browser .tableview-content'),
			'irene@passbolt.com'
		);
	}
}
