<?php

/**
 * Feature : Password Workspace
 *
 * - As a user I should be able to see the passwords workspace
 * - As a user I should be able to browse my passwords
 * - As a user I should be able to use the navigation filters
 * - As a user I should be able to view my password details
 * - As a user I should be able to fav/unfav
 * - As a user I should be able to search by name
 *
 * @copyright    (c) 2015-present Bolt Software Pvt. Ltd.
 * @licence            GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
 */
class PasswordWorkspaceTest extends PassboltTestCase
{

	protected function setUp()
	{
		parent::setUp();
		// Reset passbolt installation with dummies.
		//$this->PassboltServer->resetDatabase(1);
	}

	/**
	 * Scenario :   As a user I should be able to see the passwords workspace
	 * Given        I am logged in as Cedric Alfonsi, and I go to the password workspace
	 * Then			I should see the workspace primary menu
	 * And			I should see the workspace secondary menu
	 * And 			I should see the workspace filters shortcuts
	 * And          I should see a grid and its columns
	 */
	public function testWorkspace()
	{
		// I am logged in as Cedric Alfonsi, and I go to the password workspace
		$this->getUrl('login');
		$this->inputText('UserUsername', 'cedric@passbolt.com');
		$this->inputText('UserPassword', 'password');
		$this->pressEnter();

		// I wait the current operation has been completed.
		$this->waitCompletion();

		// I should the workspace primary menu.
		$buttons = ['create', 'edit', 'delete', 'share', 'more'];
		for ($i = 0; $i < count($buttons); $i++) {
			$this->assertElementContainsText(
				$this->findByCss('#js_wsp_primary_menu_wrapper ul'),
				$buttons[$i]
			);
		}

		// I should the workspace filters.
		$filters = ['All items', 'Favorite', 'Recently modified', 'Shared with me', 'Items I own'];
		for ($i = 0; $i < count($filters); $i++) {
			$this->assertElementContainsText(
				$this->findByCss('#js_wsp_pwd_rs_shortcuts'),
				$filters[$i]
			);
		}

		// I should see a grid and its columns
		$columns = ['Resource', 'Username', 'Password', 'URI', 'Modified'];
		for ($i = 0; $i < count($columns); $i++) {
			$this->assertElementContainsText(
				$this->findByCss('#js_wsp_pwd_browser .tableview-header'),
				$columns[$i]
			);
		}

		$this->assertTrue(true);
	}

	/**
	 * Scenario :   As a user I should be able to see my passwords
	 * Given        I am logged in as Cedric Alfonsi, and I go to the password workspace
	 * Then         I should see rows representing my passwords
	 */
	public function testBrowsePasswords()
	{
		// I am logged in as Cedric Alfonsi, and I go to the password workspace
		$this->getUrl('login');
		$this->inputText('UserUsername', 'cedric@passbolt.com');
		$this->inputText('UserPassword', 'password');
		$this->pressEnter();

		// I wait the current operation has been completed.
		$this->waitCompletion();

		// I should see rows representing my passwords
		$passwords = ['shared resource', 'salesforce account', 'tetris license', 'facebook account'];
		for ($i = 0; $i < count($passwords); $i++) {
			$this->assertElementContainsText(
				$this->findByCss('#js_wsp_pwd_browser .tableview-content'),
				$passwords[$i]
			);
		}

		// @todo Test de rows details

		$this->assertTrue(true);
	}

	/**
	 * Scenario :   As a user I should be able to filter my passwords
	 * Given        I am logged in as Cedric Alfonsi, and I go to the password workspace
	 * When			I click on the favorite filter
	 * Then			I should only see my favorite passwords
	 * When			I click on the recently modified filter
	 * Then			I should see my passwords ordered my modification date
	 * When			I click on the shared with me filter
	 * Then			I should only see the passwords that have been share with me
	 * When			I click on the items I own filter
	 * Then			I should only see the passwords I own
	 */
	public function testFilterPasswords()
	{
		// I am logged in as Cedric Alfonsi, and I go to the password workspace
		$this->getUrl('login');
		$this->inputText('UserUsername', 'cedric@passbolt.com');
		$this->inputText('UserPassword', 'password');
		$this->pressEnter();

		// I wait the current operation has been completed.
		$this->waitCompletion();

		// I click on the favorite filter
		$this->clickLink("Favorite");
		$this->waitCompletion();
		// I should only see my favorite passwords
		// @todo Test with a case which already has favorites

		// I click on the recently modified filter
		$this->clickLink("Recently modified");
		$this->waitCompletion();
		// I should see my passwords ordered my modification date
		// @todo Test with a case where the modified date are different

		// I click on the shared with me filter
		$this->clickLink("Shared with me");
		$this->waitCompletion();
		// I should only see the passwords that have been share with me
		$passwords = ['shared resource', 'salesforce account', 'tetris license', 'facebook account'];
		for ($i = 0; $i < count($passwords); $i++) {
			$this->assertElementContainsText(
				$this->findByCss('#js_wsp_pwd_browser .tableview-content'),
				$passwords[$i]
			);
		}

		// I click on the items I own filter
		$this->clickLink("Shared with me");
		$this->waitCompletion();
		// I should only see the passwords I own
		// @todo Test with a case which owns some passwords

		$this->assertTrue(true);
	}

	/**
	 * Scenario :   As a user I should be able to view my password details
	 * Given        I am logged in as Cedric Alfonsi, and I go to the password workspace
	 * When			I click on a password
	 * Then 		I should see a secondary side bar appearing
	 * And			I should the details of the selected password
	 */
	public function testPasswordDetails()
	{
		// I am logged in as Cedric Alfonsi, and I go to the password workspace
		$this->getUrl('login');
		$this->inputText('UserUsername', 'cedric@passbolt.com');
		$this->inputText('UserPassword', 'password');
		$this->pressEnter();

		// I wait the current operation has been completed.
		$this->waitCompletion();

		// I click on a password
		$this->clickElement("#js_wsp_pwd_browser .tableview-content div[title='shared resource']");
		$this->waitCompletion();

		// I should see a secondary side bar appearing
		$this->assertPageContainsElement('#js_pwd_details');

		// I should the details of the selected password
		$pwdDetails = [
			'username' 		=> 'admin',
			'url' 			=> 'http://shared.resource.net/',
			'modified' 		=> '2 years ago',
			'created-by' 	=> 'anonymous@passbolt.com',
			'modified-by' 	=> 'anonymous@passbolt.com',
		];
		// I should see the password's username
		$cssSelector = '#js_pwd_details .detailed-information li.username';
		$this->assertElementContainsText(
			$this->findByCss($cssSelector),
			$pwdDetails['username']
		);
		// I should see the password's url
		$cssSelector = '#js_pwd_details .detailed-information li.url';
		$this->assertElementContainsText(
			$this->findByCss($cssSelector),
			$pwdDetails['url']
		);
		// I should see the password's modified time
		$cssSelector = '#js_pwd_details .detailed-information li.modified';
		$this->assertElementContainsText(
			$this->findByCss($cssSelector),
			$pwdDetails['modified']
		);
		// I should see the password's creator
		$cssSelector = '#js_pwd_details .detailed-information li.created-by';
		$this->assertElementContainsText(
			$this->findByCss($cssSelector),
			$pwdDetails['created-by']
		);
		// I should see the password's modifier
		$cssSelector = '#js_pwd_details .detailed-information li.modified-by';
		$this->assertElementContainsText(
			$this->findByCss($cssSelector),
			$pwdDetails['modified-by']
		);
	}

	/**
	 * Scenario :   As a user I should be able to fav/unfav
	 * Given        I am logged in as Cedric Alfonsi, and I go to the password workspace
	 * When			I click on the favorite star located before the password (the password shouldn't be a favorite)
	 * Then 		I should see the star becoming red
	 * When 		I click on the favorite filter
	 * Then			I should see the password I just favoritize in the liste of filtered passwords
	 */
	public function testFavorite()
	{
		return;
		// I am logged in as Cedric Alfonsi, and I go to the password workspace
		$this->getUrl('login');
		$this->inputText('UserUsername', 'cedric@passbolt.com');
		$this->inputText('UserPassword', 'password');
		$this->pressEnter();

		// I wait the current operation has been completed.
		$this->waitCompletion();

		// I click on a password
		$this->clickElement("#js_wsp_pwd_browser .tableview-content div[title='shared resource']");
		$this->waitCompletion();

		// I should see a secondary side bar appearing
		$this->assertPageContainsElement('#js_pwd_details');

		// I should the details of the selected password
		$pwdDetails = [
			'username' 		=> 'admin',
			'url' 			=> 'http://shared.resource.net/',
			'modified' 		=> '2 years ago',
			'created-by' 	=> 'anonymous@passbolt.com',
			'modified-by' 	=> 'anonymous@passbolt.com',
		];
		// I should see the password's username
		$cssSelector = '#js_pwd_details .detailed-information li.username';
		$this->assertElementContainsText(
			$this->findByCss($cssSelector),
			$pwdDetails['username']
		);
		// I should see the password's url
		$cssSelector = '#js_pwd_details .detailed-information li.url';
		$this->assertElementContainsText(
			$this->findByCss($cssSelector),
			$pwdDetails['url']
		);
		// I should see the password's modified time
		$cssSelector = '#js_pwd_details .detailed-information li.modified';
		$this->assertElementContainsText(
			$this->findByCss($cssSelector),
			$pwdDetails['modified']
		);
		// I should see the password's creator
		$cssSelector = '#js_pwd_details .detailed-information li.created-by';
		$this->assertElementContainsText(
			$this->findByCss($cssSelector),
			$pwdDetails['created-by']
		);
		// I should see the password's modifier
		$cssSelector = '#js_pwd_details .detailed-information li.modified-by';
		$this->assertElementContainsText(
			$this->findByCss($cssSelector),
			$pwdDetails['modified-by']
		);
	}

}