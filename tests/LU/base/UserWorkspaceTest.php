<?php

/**
 * Feature : User Workspace
 *
 * - As a user I should be able to see the users workspace
 * - As a user I should be able to browse the users
 * - As a user I should be able to use the navigation filters
 * - As a user I should be able to view the user details
 * - As a user I should be able to search a user by keywords
 * - As a user when I filter the user workspace all users should be unselected
 * - As a user when I filter by keywords the user workspace the global filter "All users" should be selected
 * - As an admin user, I should have admin rights inside the user workspace
 * - As a non admin user, I should not have admin rights inside the user workspace
 * - As a logged in user, I should be able to control the sidebar visibility through the sidebar button
 *
 * @copyright (c) 2015-present Bolt Softwares Pvt Ltd
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class UserWorkspaceTest extends PassboltTestCase {
	/**
	 * Scenario :   As a user I should be able to see the user workspace
	 *
	 * Given        I am logged in as Ada, and I go to the user workspace
	 * Then			I should not see the workspace primary menu
	 * And			I should see the workspace secondary menu
	 * And 			I should see the workspace filters shortcuts
	 * And          I should see a grid and its columns
	 * And 			I should see some grid columns are sortable
	 * And			I should see the breadcrumb with the following:
	 * 				| All users
	 */
	public function testWorkspace() {
		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);

		// And I am logged in on the user workspace
		$this->loginAs($user);
		$this->gotoWorkspace('user');

		// I should not see the workspace primary menu
		$buttons = ['edit', 'delete', 'more'];
		for ($i = 0; $i < count($buttons); $i++) {
			$this->assertElementNotContainText(
				$this->findByCss('#js_wsp_primary_menu_wrapper ul'),
				$buttons[$i]
			);
		}

    // I should not see the create button in the main action wrapper
    $this->assertElementNotContainText(
      $this->findByCss('.main-action-wrapper'),
      'create'
    );

		// I should the workspace filters.
		$filters = ['All users', 'Recently modified'];
		for ($i = 0; $i < count($filters); $i++) {
			$this->assertElementContainsText(
				$this->findByCss('#js_wsp_users_filter_shortcuts'),
				$filters[$i]
			);
		}

		// I should see a grid and its columns
		$columns = ['User', 'Username', 'Modified'];
		for ($i = 0; $i < count($columns); $i++) {
			$this->assertElementContainsText(
				$this->findByCss('#js_wsp_users_browser .tableview-header'),
				$columns[$i]
			);
		}

		// I should see some grid columns are sortable
		$columnsId = ['name', 'username', 'modified', 'last_logged_in'];
		for ($i = 0; $i < count($columnsId); $i++) {
			$this->assertElementHasClass(
				$this->findByCss('#js_wsp_pwd_browser .tableview-header .js_grid_column_' . $columnsId[$i]),
				'sortable'
			);
		}

		// I should see the breadcrumb with the following:
		// 	| All users
		$this->assertBreadcrumb('users', ['All users']);
	}

	/**
	 * Scenario :   As a user I should be able to see the users using the app
	 * Given        I am logged in as Ada, and I go to the user workspace
	 * Then         I should see rows representing the users
	 */
	public function testBrowseUsers()
	{
		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);

		// And I am logged in on the user workspace
		$this->loginAs($user);
		$this->gotoWorkspace('user');

		// I should see rows representing the users
		$users = [
			'Ada Lovelace',
			'Betty Holberton',
			'Carol Shaw',
			'Dame Steve Shirley',
			'Edith Clarke',
			'Frances Allen',
			'Grace Hopper',
			'Hedy Lamarr',
			'Irene Greif',
			'Jean Bartik',
			'Kathleen Antonelli',
			'Lynne Jolitz',
			'Marlyn Wescoff'
		];
		$browserElement = $this->findByCss('#js_wsp_users_browser .tableview-content');
		for ($i = 0; $i < count($users); $i++) {
			$this->assertElementContainsText(
				$browserElement,
				$users[$i]
			);
		}

		// @todo Test de rows details


	}

	/**
	 * Scenario :   As a user I should be able to filter the users
	 *
	 * Given        I am logged in as Ada, and I go to the user workspace
     * Then         I should see the filter "All users" is selected.
	 * When			I click on the recently modified filter
	 * Then			I should see the users ordered my modification date
     * And          I should see that the filter "All users" is not selected anymore
     * And          I should see that the filter "Recently modified" is selected.
	 * And			I should see the breadcrumb with the following:
	 *					| All items
	 *					| Recently modified
	 */
	public function testFilterUsers() {
		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);

		// And I am logged in on the user workspace
		$this->loginAs($user);
		$this->gotoWorkspace('user');

        // Assert menu All users is selected.
        $this->assertFilterIsSelected('js_users_wsp_filter_all');

		// I click on the recently modified filter
		$this->clickLink("Recently modified");
		$this->waitCompletion();

        // I should not see the filter All users as selected.
        $this->assertFilterIsNotSelected('js_users_wsp_filter_all');

        // I should see the filter recently modified selected.
        $this->assertFilterIsSelected('js_users_wsp_filter_recently_modified');

		// I should see the users ordered by modification date
		// @todo Test with a case where the modified date are different
		// I should see the breadcrumb with the following:
		// 	| All users
		//	| Search : Recently modified
		$this->assertBreadcrumb('users', ['All users', 'Recently modified']);
	}

	/**
	 * Scenario :   As a user I should be able to view the user details
	 *
	 * Given        I am logged in as Ada, and I go to the user workspace
	 * When			I click on a user
	 * Then 		I should see a secondary side bar appearing
	 * And			I should the details of the selected user
	 */
	public function testUsersDetails() {
		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);

		// And I am logged in on the user workspace
		$this->loginAs($user);
		$this->gotoWorkspace('user');

		// I click on a user
		$this->click("#js_wsp_users_browser .tableview-content div[title='Betty Holberton']");
		$this->waitCompletion();

		// I should see a secondary side bar appearing
		$this->assertPageContainsElement('#js_user_details');

		// I should see the details of the selected user
		$userDetails = [
			'role' 			=> 'User',
			'modified' 		=> '7 days ago',
			'keyid' 		=> 'E61D7009',
			'type'		 	=> 'RSA',
			'created'		=> '/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/',
			'expires'		=> '/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/',
		];
		$userDetails['key'] = file_get_contents(GPG_FIXTURES . DS . 'betty_public.key' );

		// I should see the user's role
		$cssSelector = '#js_user_details .detailed-information li.role';
		$this->assertElementContainsText(
			$this->findByCss($cssSelector),
			$userDetails['role']
		);
		// I should see the user's modified time
		$cssSelector = '#js_user_details .detailed-information li.modified';
		$this->assertElementContainsText(
			$this->findByCss($cssSelector),
			$userDetails['modified']
		);
		// I should see the user's key id
		$cssSelector = '#js_user_details .key-information li.keyid';
		$this->assertElementContainsText(
			$this->findByCss($cssSelector),
			$userDetails['keyid']
		);
		// I should see the user's key type
		$cssSelector = '#js_user_details .key-information li.type';
		$this->assertElementContainsText(
			$this->findByCss($cssSelector),
			$userDetails['type']
		);
		// I should see the user's key created time
		$cssSelector = '#js_user_details .key-information li.created';
		$this->assertElementContainsText(
			$this->findByCss($cssSelector),
			$userDetails['created']
		);
		// I should see the user's key expires time
		$cssSelector = '#js_user_details .key-information li.expires';
		$this->assertElementContainsText(
			$this->findByCss($cssSelector),
			$userDetails['expires']
		);
		// I should see the user's key public key
		$cssSelector = '#js_user_details .key-information li.gpgkey';
		$this->assertElementContainsText(
			$this->findByCss($cssSelector),
			$userDetails['key']
		);
	}

	/**
	 * Scenario :   As a user I should be able to search a user by keywords
	 *
	 * Given        I am logged in as Ada, and I go to the user workspace
	 * When			I fill the "app search" field with "User Test"
	 * And			I click "search"
	 * Then 		I should see the view filtered with my search
	 * And			I should see the breadcrumb with the following:
	 *					| All users
	 *					| Search : User Test
	 */
	public function testSearchByKeywords() {
		$searchUser = 'Betty';
		$hiddenUsers = [
			'Betty Holbderton',
			'Carol Shaw',
			'Dame Steve Shirley',
			'Edith Clarke',
			'Frances Allen',
			'Grace Hopper',
			'Hedy Lamarr',
			'Irene Greif',
			'Jean Bartik',
			'Kathleen Antonelli',
			'Lynn Jolitz',
			'Marlyn Wescoff'
		];
		$breadcrumb = ['All users', 'Search : ' . $searchUser];

		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);

		// And I am logged in on the user workspace
		$this->loginAs($user);
		$this->gotoWorkspace('user');

		// I fill the "app search" field with "tetris license"
		$this->inputText('js_app_filter_keywords', $searchUser);
		$this->click("#js_app_filter_form button[value='search']");
		$this->waitCompletion();

		// I should see the view filtered with my search
		$userBrowser = $this->findByCss('#js_wsp_users_browser .tableview-content');
		$this->assertElementContainsText(
			$userBrowser,
			$searchUser
		);
		for ($i=0; $i< count($hiddenUsers); $i++) {
			$this->assertElementNotContainText(
				$userBrowser,
				$hiddenUsers[$i]
			);
		}

		// I should see the breadcrumb with the following:
		// 	| All users
		//	| Search : User Test
		$this->assertBreadcrumb('users', $breadcrumb);
	}

	/**
	 * Scenario :   As a user when I filter the user workspace all users should be unselected
	 * Given        I am logged in as Ada, and I go to the user workspace
	 * When         I select a user I own
	 * And 			I filter the workspace by keywords
	 * Then 		I should see the user unselected
	 */
	public function testSearchByKeywordsUnselectUsers() {
		$searchUser = 'Betty';
		$userId = Uuid::get('user.id.betty');

		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);

		// And I am logged in on the user workspace
		$this->loginAs($user);
		$this->gotoWorkspace('user');

		// When I select a user I own
		$this->clickUser($userId);

		// And I filter the workspace by keywords
		$this->inputText('js_app_filter_keywords', $searchUser);
		$this->click("#js_app_filter_form button[value='search']");
		$this->waitCompletion();

		// Then I should see the password unselected
		$this->assertUserNotSelected($userId);
	}

	/**
	 * Scenario :   As a user when I filter by keywords the user workspace the global filter "All users" should be selected
	 * Given        I am logged in as Ada, and I go to the user workspace
	 * When 		I click on the recently modified filter
	 * Then 		I should see that menu All users is not selected anymore
	 * When 		I fill the "app search" field with "Betty"
	 * Then 		I should see the filter "All users" is selected.
	 */
	public function testSearchByKeywordsChangesGlobalFilterToAllUsers() {
		$searchUser = 'Betty';

		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);

		// And I am logged in on the user workspace
		$this->loginAs($user);
		$this->gotoWorkspace('user');

		// When I click on the recently modified filter
		$this->clickLink("Recently modified");
		$this->waitCompletion();

		// Then I should see that menu All users is not selected anymore
		$this->assertFilterIsNotSelected('js_users_wsp_filter_all');

		// When I fill the "app search" field with "shared resource"
		$this->inputText('js_app_filter_keywords', $searchUser);
		$this->click("#js_app_filter_form button[value='search']");
		$this->waitCompletion();

		// Then I should see the filter "All items" is selected.
		$this->assertFilterIsSelected('js_users_wsp_filter_all');
	}

	/**
	 * Scenario :   As an admin user, I should have admin rights inside the user workspace
	 * Given        I am logged in as admin on the user workspace
	 * Then         I should see the create button
	 * And          I should see the edit button
	 * And          I should see the delete button
	 * when         I right click on a user in the users list
	 * Then         I should see a contextual menu
	 * And          I should see the option Copy public key inside contextual menu
	 * And          I should see the option Copy email address inside contextual menu
	 * And          I should see the option Edit inside contextual menu
	 * And          I should see the option Delete inside contextual menu
	 */
	public function testAdminUserHasAdminRights() {
		// Given I am Admin
		$user = User::get('admin');

		// And I am logged in on the user workspace
		$this->setClientConfig($user);
		$this->loginAs($user);
		$this->gotoWorkspace('user');

		// Then I should not see the create button
		$this->assertVisible('js_wsp_create_button');

		// And I should not see the edit button
		$this->assertVisible('js_user_wk_menu_edition_button');

		// And I should not see the delete button
		$this->assertVisible('js_user_wk_menu_deletion_button');

		// Right click on a user
		$betty = User::get('betty');
		$this->rightClickUser($betty['id']);

		// Then I can see the contextual menu
		$this->assertVisible('js_contextual_menu');

		// And I should see the option Copy public key
		$contextualMenu = $this->find('#js_contextual_menu');
		$this->assertElementContainsText($contextualMenu, 'Copy public key');

		// And I should see the option Copy email address
		$this->assertElementContainsText($contextualMenu, 'Copy email address');

		// And I should see the option Edit
		$this->assertElementContainsText($contextualMenu, 'Edit');

		// And I should see the option Delete
		$this->assertElementContainsText($contextualMenu, 'Delete');
	}


	/**
	 * Scenario :   As a non admin user, I should not have admin rights inside the user workspace
	 * Given        I am logged in as ada on the user workspace
	 * Then         I should not see the create button
	 * And          I should not see the edit button
	 * And          I should not see the delete button
	 * when         I right click on a user in the users list
	 * Then         I should see a contextual menu
	 * And          I should see the option Copy public key inside contextual menu
	 * And          I should see the option Copy email address inside contextual menu
	 * And          I should not see the option Edit inside contextual menu
	 * And          I should not see the option Delete inside contextual menu
	 */
	public function testNonAdminUserHasNotAdminRights() {
		// Given I am Ada
		$user = User::get('ada');

		// And I am logged in on the user workspace
		$this->setClientConfig($user);
		$this->loginAs($user);
		$this->gotoWorkspace('user');

		// Then I should not see the create button
		$this->assertNotVisible('js_wsp_create_button');

		// And I should not see the edit button
		$this->assertNotVisible('js_user_wk_menu_edition_button');

		// And I should not see the delete button
		$this->assertNotVisible('js_user_wk_menu_deletion_button');

		// Right click on a user
		$betty = User::get('betty');
		$this->rightClickUser($betty['id']);

		// Then I can see the contextual menu
		$this->assertVisible('js_contextual_menu');

		// And I should see the option Copy public key
		$contextualMenu = $this->find('#js_contextual_menu');
		$this->assertElementContainsText($contextualMenu, 'Copy public key');

		// And I should see the option Copy email address
		$this->assertElementContainsText($contextualMenu, 'Copy email address');

		// And I should not see the option Edit
		$this->assertElementNotContainText($contextualMenu, 'Edit');

		// And I should not see the option Delete
		$this->assertElementNotContainText($contextualMenu, 'Delete');
	}

	/**
	 * Scenario:    As a logged in user, I should be able to control the sidebar visibility through the sidebar button
	 * Given        I am logged in as ada
	 * And          I am on the user workspace
	 * Then         I should see that the sidebar button is pressed
	 * And          I should not see the sidebar
	 * When         I click on a user to select it
	 * Then         I should see the sidebar
	 * When         I click on the same user to deselect it
	 * Then         I should not see the sidebar anymore
	 * When         I click on the same user to select it
	 * Then         I should see that the sidebar is visible again
	 * When         I toggle off the sidebar button
	 * Then         I should see that the sidebar button is now deactivated
	 * And          I should not see the sidebar anymore
	 * When         I click on the user again to deselect it
	 * Then         I should see that the sidebar button is deactivated
	 * And          I should see that the sidebar is not visible
	 * When         I toggle in the sidebar button
	 * Then         I should see that the sidebar is not visible
	 * When         I click on the user again to select it
	 * Then         I should see that the sidebar is visible
	 * When         I click on the close button at the top of the sidebar
	 * Then         I should not see the sidebar anymore
	 */
	public function testSidebarVisibility() {
		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);

		// And I am logged in on the password workspace
		$this->loginAs($user);
		$this->gotoWorkspace('user');

		$this->assertToggleButtonStatus('js_wk_secondary_menu_view_sidebar_button', TOGGLE_BUTTON_PRESSED);

		// I should not see the sidebar
		$this->assertNotVisible('#js_user_details');

		// And I am editing a password I own
		$betty = User::get('betty');

		// Click on a password
		$this->clickUser($betty);

		// I should see a secondary side bar appearing
		$this->assertVisible('#js_user_details');

		// Click on a password to deselect it
		$this->clickUser($betty);

		// I should not see the secondary sidebar
		$this->assertNotVisible('#js_user_details');

		// Click on a password
		$this->clickUser($betty);

		// I should that the sidebar is visible again
		$this->assertVisible('#js_user_details');

		// Click on the sidebar button.
		$this->click('js_wk_secondary_menu_view_sidebar_button');

		// I should see that the button is not pressed.
		$this->assertToggleButtonStatus('js_wk_secondary_menu_view_sidebar_button', TOGGLE_BUTTON_UNPRESSED);

		// I should not see the sidebar anymore.
		$this->assertNotVisible('#js_user_details');

		// Click on the password again to deselect it.
		$this->clickUser($betty);

		// The toggle button should still be unpressed.
		$this->assertToggleButtonStatus('js_wk_secondary_menu_view_sidebar_button', TOGGLE_BUTTON_UNPRESSED);

		// I should not see the sidebar.
		$this->assertNotVisible('#js_user_details');

		// Click on the sidebar button to toggle it in.
		$this->click('js_wk_secondary_menu_view_sidebar_button');

		// The toggle button should still be pressed.
		$this->assertToggleButtonStatus('js_wk_secondary_menu_view_sidebar_button', TOGGLE_BUTTON_PRESSED);

		// I should not see the sidebar.
		$this->assertNotVisible('#js_user_details');

		// Click on the password again to select it.
		$this->clickUser($betty);

		// I should that the sidebar is visible
		$this->assertVisible('#js_user_details');

		// I click on the button close at the top of the dialogue.
		$this->click('#js_user_details .dialog-close');

		// Then I should not see the sidebar anymore.
		$this->assertNotVisible('#js_user_details');
	}

	/**
	 * Scenario :   As a user I should be able to sort the users browser by column
	 * Given        I am logged in as Ada, and I go to the user workspace
	 * When 		I sort the users browser by name
	 * Then 		I should see it sorted by name
	 * When 		I sort the users browser by username
	 * Then 		I should see it sorted by username
	 * When 		I sort the users browser by modified
	 * Then 		I should see it sorted by modified
	 * When 		I sort the users browser by lasted logged in
	 * Then 		I should see it sorted by lasted logged in
	 */
	public function testSortByColumn() {
		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);

		// And I am logged in on the password workspace
		$this->loginAs($user);
		$this->gotoWorkspace('user');

		// When I sort the users browser by name
		$columnId = 'name';
		$this->click('.js_grid_column_' . $columnId);

		// Then I should see it sorted by name
		$columnHeaderResourceElement = $this->find('#js_wsp_users_browser .tableview-header .js_grid_column_' . $columnId);
		$this->assertElementHasClass($columnHeaderResourceElement, 'sorted');
		$this->assertElementHasClass($columnHeaderResourceElement, 'sort-asc');

		// When I sort the users browser by username
		$columnId = 'username';
		$this->click('.js_grid_column_' . $columnId);

		// Then I should see it sorted by username
		$columnHeaderUsernameElement = $this->find('#js_wsp_users_browser .tableview-header .js_grid_column_' . $columnId);
		$this->assertElementHasClass($columnHeaderUsernameElement, 'sorted');
		$this->assertElementHasClass($columnHeaderUsernameElement, 'sort-asc');
		$this->assertElementHasNotClass($columnHeaderResourceElement, 'sorted');
		$this->assertElementHasNotClass($columnHeaderResourceElement, 'sort-asc');

		// When I sort the users browser by modified
		$columnId = 'modified';
		$this->click('.js_grid_column_' . $columnId);

		// Then I should see it sorted by modified
		$columnHeaderModifiedElement = $this->find('#js_wsp_users_browser .tableview-header .js_grid_column_' . $columnId);
		$this->assertElementHasClass($columnHeaderModifiedElement, 'sorted');
		$this->assertElementHasClass($columnHeaderModifiedElement, 'sort-asc');
		$this->assertElementHasNotClass($columnHeaderUsernameElement, 'sorted');
		$this->assertElementHasNotClass($columnHeaderUsernameElement, 'sort-asc');

		// When I sort the users browser by uri
		$columnId = 'last_logged_in';
		$this->click('.js_grid_column_' . $columnId);

		// Then I should see it sorted by lasted logged in
		$columnHeaderUriElement = $this->find('#js_wsp_users_browser .tableview-header .js_grid_column_' . $columnId);
		$this->assertElementHasClass($columnHeaderUriElement, 'sorted');
		$this->assertElementHasClass($columnHeaderUriElement, 'sort-asc');
		$this->assertElementHasNotClass($columnHeaderModifiedElement, 'sorted');
		$this->assertElementHasNotClass($columnHeaderModifiedElement, 'sort-asc');
	}

}
