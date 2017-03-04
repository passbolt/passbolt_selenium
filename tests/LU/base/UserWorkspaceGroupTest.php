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
}
