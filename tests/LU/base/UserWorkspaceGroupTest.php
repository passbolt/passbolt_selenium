<?php

class UserWorkspaceGroupTest extends PassboltTestCase {
	/**
	 * @group saucelabs
	 * Scenario :   As a user I can browse the list of all the groups in the groups section of the user workspace
	 *
	 * Given        I am logged in as LU (Ada), and I go to the user workspace
	 * Then			I should see the list of groups available in the left hand sidebar
	 */
	public function testGroupIndex() {
		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);

		// And I am logged in the user workspace
		$this->loginAs($user);
		$this->gotoWorkspace('user');

		// Get list of groups.
		$groups = Group::_get();

		// Find all groups listed in the groups list.
		foreach ($groups as $group) {
			$eltGroup = $this->findByXpath("//ul[@id='js_wsp_users_groups_list']/li/div//span[text()='" . $group['name'] . "']");
			$this->assertEquals($eltGroup->getText(), $group['name']);
		}
	}

	/**
	 * @group saucelabs
	 * Scenario :   As a user I can see the list users that are part of the group in the users grid by using the group filter
	 * Given        I am logged in as Ada, and I go to the user workspace
	 * When         I click on a group name
	 * Then         I should see that the given group is selected
	 * And          I should see that the list of users display only the users that are part of this group.
	 */
	public function testFilterUsersByGroup() {
		// Given I am Ada
		$user = User::get('ada');
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
