<?php
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverSelect;

/**
 * Feature :  As an administrator I can edit groups
 *
 * Scenarios :
 *  - As an administrator I can edit a group using the right click contextual menu
 *  - As an administrator I can edit the group name
 *
 * @copyright (c) 2017-present Passbolt SARL
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class AdminGroupEditTest extends PassboltTestCase {

	/**
	 * Scenario: As an administrator I can edit a group using the right click contextual menu
	 *
	 * Given	I am logged in as an administrator and I am on the users workspace
	 * When 	I click on the contextual menu button of a group on the right
	 * Then 	I should see the group contextual menu
	 * And  	I should see the “Edit group” option
	 * When		I click on “Edit group”
	 * Then		I should see the Edit group dialog
	 */
	public function testEditGroupRightClick() {
		// Given I am logged in as an administrator
		$user = User::get('admin');
		$this->setClientConfig($user);
		$this->loginAs($user);
		$this->gotoWorkspace('user');

		// When I click on the contextual menu button of a group on the right
		$groupId = Uuid::get('group.id.ergonom');
		$this->click("#group_$groupId .right-cell a");

		// Then I should see the group contextual menu
		$this->assertVisible('#js_contextual_menu');
		$this->assertVisible('js_group_browser_menu_edit');

		// When I click on “Edit group”
		$this->click("#js_contextual_menu #js_group_browser_menu_edit a");

		// Then I should see the Edit group dialog
		$this->waitUntilISee('.edit-group-dialog');
	}

	/**
	 * Scenario: As an administrator I can edit the group name
	 *
	 * Given	I am logged in as administrator
	 * And		I am editing a group
	 * When		I observe the content of the edit group dialog
	 * Then		I should see a “group name” field containing the current group name.
	 * When		I modify the group name
	 * And		I click on “save”
	 * Then		I should see that the dialog disappears
	 * And		I should see a confirmation message saying that the group has been edited
	 * And		I should see that the group name has been changed in the groups list
	 */
	public function testEditGroupName() {
		$this->resetDatabaseWhenComplete();

		// Given I am logged in as an administrator
		$user = User::get('admin');
		$this->setClientConfig($user);
		$this->loginAs($user);
		$group = Group::get(['id' => Uuid::get('group.id.ergonom')]);
		$this->gotoEditGroup($group['id']);

		// When	I observe the content of the edit group dialog
		// Then	I should see a “group name” field containing the current group name.
		$this->assertInputValue('js_field_name', $group['name']);

		// When	I modify the group name
		$groupNameUpdate = $group['name'] . ' UPDATED';
		$this->inputText('js_field_name', $groupNameUpdate);

		// And I click on “save”
		$this->click('.edit-group-dialog a.button.primary');

		// Then	I should see that the dialog disappears
		$this->waitUntilIDontSee('.edit-group-dialog');

		// And I should see a confirmation message saying that the group has been edited
		$this->assertNotification('app_groups_edit_success');

		// And I should see that the group name has been changed in the groups list
		$this->waitUntilISee('js_wsp_users_groups_list', '/' . $groupNameUpdate . '/');
	}

}