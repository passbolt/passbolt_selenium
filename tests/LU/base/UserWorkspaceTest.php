<?php

/**
 * Feature : User Workspace
 *
 * - As a user I should be able to see the users workspace
 * - As a user I should be able to browse the users
 * - As a user I should be able to use the navigation filters
 * - As a user I should be able to view the user details
 * - As a user I should be able to search a user by keywords
 *
 * @copyright    (c) 2015-present Bolt Software Pvt. Ltd.
 * @licence      GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
 */
class UserWorkspaceTest extends PassboltTestCase {
	/**
	 * Scenario :   As a user I should be able to see the user workspace
	 *
	 * Given        I am logged in as Carol Shaw, and I go to the user workspace
	 * Then			I should not see the workspace primary menu
	 * And			I should see the workspace secondary menu
	 * And 			I should see the workspace filters shortcuts
	 * And          I should see a grid and its columns
	 * And			I should see the breadcrumb with the following:
	 * 				| All users
	 */
	public function testWorkspace() {
		// I am logged in as Carol Shaw, and I go to the user workspace
		$this->loginAs('carol@passbolt.com');
		$this->gotoWorkspace('user');

		// I should not see the workspace primary menu
		$buttons = ['create', 'edit', 'delete', 'more'];
		for ($i = 0; $i < count($buttons); $i++) {
			$this->assertElementNotContainText(
				$this->findByCss('#js_wsp_primary_menu_wrapper ul'),
				$buttons[$i]
			);
		}

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

		// I should see the breadcrumb with the following:
		// 	| All users
		$this->assertBreadcrumb('users', ['All users']);
	}

	/**
	 * Scenario :   As a user I should be able to see the users using the app
	 * Given        I am logged in as Carol Shaw, and I go to the user workspace
	 * Then         I should see rows representing the users
	 */
	public function testBrowseUsers()
	{
		// I am logged in as Carol Shaw, and I go to the user workspace
		$this->loginAs('carol@passbolt.com');
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
			'Lynn Jolitz',
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
	 * Given        I am logged in as Carol Shaw, and I go to the user workspace
	 * When			I click on the recently modified filter
	 * Then			I should see the users ordered my modification date
	 * And			I should see the breadcrumb with the following:
	 *					| All items
	 *					| Recently modified
	 */
	public function testFilterUsers() {
		// I am logged in as Carol, and I go to the user workspace
		$this->loginAs('carol@passbolt.com');
		$this->gotoWorkspace('user');

		// I click on the recently modified filter
		$this->clickLink("Recently modified");
		$this->waitCompletion();
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
	 * Given        I am logged in as Carol, and I go to the user workspace
	 * When			I click on a user
	 * Then 		I should see a secondary side bar appearing
	 * And			I should the details of the selected user
	 */
	public function testUsersDetails() {
		// I am logged in as Carol, and I go to the user workspace
		$this->loginAs('carol@passbolt.com');
		$this->gotoWorkspace('user');

		// I click on a user
		$this->click("#js_wsp_users_browser .tableview-content div[title='Betty Holberton']");
		$this->waitCompletion();

		// I should see a secondary side bar appearing
		$this->assertPageContainsElement('#js_user_details');

		// I should see the details of the selected user
		$userDetails = [
			'role' 			=> 'User',
			'modified' 		=> 'ago',
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
	 * Given        I am logged in as Carol, and I go to the user workspace
	 * When			I fill the "app search" field with "User Test"
	 * And			I click "search"
	 * Then 		I should see the view filtered with my search
	 * And			I should see the breadcrumb with the following:
	 *					| All users
	 *					| Search : User Test
	 */
	public function testSearchByKeywords() {
		$searchUser = 'Ada';
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

		// I am logged in as Carol, and I go to the user workspace
		$this->loginAs('carol@passbolt.com');
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

}
