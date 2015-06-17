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
 * @licence            GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
 */
class UserWorkspaceTest extends PassboltTestCase
{

	protected function setUp()
	{
		parent::setUp();
		// Reset passbolt installation with dummies.
		//$this->PassboltServer->resetDatabase(1);
	}

	/**
	 * Scenario :   As a user I should be able to see the user workspace
	 * Given        I am logged in as Cedric Alfonsi, and I go to the user workspace
	 * Then			I should not see the workspace primary menu
	 * And			I should see the workspace secondary menu
	 * And 			I should see the workspace filters shortcuts
	 * And          I should see a grid and its columns
	 * And			I should see the breadcrumb with the following:
	 * 				| All users
	 */
	public function testWorkspace()
	{
		// I am logged in as Cedric Alfonsi, and I go to the user workspace
		$this->loginAs('cedric@passbolt.com');
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
	 * Given        I am logged in as Cedric Alfonsi, and I go to the user workspace
	 * Then         I should see rows representing the users
	 */
	public function testBrowseUsers()
	{
		// I am logged in as Cedric Alfonsi, and I go to the user workspace
		$this->loginAs('cedric@passbolt.com');
		$this->gotoWorkspace('user');

		// I should see rows representing the users
		$users = [
			'Cédric Alfonsi',
			'Jean René Bergamotte',
			'Remy Bertot',
			'Userone Company A',
			'Myriam Djerouni',
			'Aurelie Gherards',
			'Ismail Guennouni',
			'Admin Istrator',
			'User Lambda',
			'Frank Leboeuf',
			'Great Manager',
			'Kevin Muller',
			'User Test',
			'User b Test',
			'Darth Vader',
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
	 * Given        I am logged in as Cedric Alfonsi, and I go to the user workspace
	 * When			I click on the recently modified filter
	 * Then			I should see the users ordered my modification date
	 * And			I should see the breadcrumb with the following:
	 *					| All items
	 *					| Recently modified
	 */
	public function testFilterUsers()
	{
		// I am logged in as Cedric Alfonsi, and I go to the user workspace
		$this->loginAs('cedric@passbolt.com');
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
	 * Given        I am logged in as Cedric Alfonsi, and I go to the user workspace
	 * When			I click on a user
	 * Then 		I should see a secondary side bar appearing
	 * And			I should the details of the selected user
	 */
	public function testUsersDetails()
	{
		// I am logged in as Cedric Alfonsi, and I go to the user workspace
		$this->loginAs('cedric@passbolt.com');
		$this->gotoWorkspace('user');

		// I click on a user
		$this->clickElement("#js_wsp_users_browser .tableview-content div[title='User Test']");
		$this->waitCompletion();

		// I should see a secondary side bar appearing
		$this->assertPageContainsElement('#js_user_details');

		// I should the details of the selected user
		$userDetails = [
			'role' 			=> 'User',
			'modified' 		=> '6 days ago',
			'keyid' 		=> '5FD2D92C',
			'type'		 	=> 'RSA',
			'created'		=> '2014-11-19 19:33:51',
			'expires'		=> '2018-11-19 19:33:51',
			'key'			=> '-----BEGIN PGP PUBLIC KEY BLOCK-----
Version: GnuPG/MacGPG2 v2.0.22 (Darwin)
Comment: GPGTools - https://gpgtools.org

mQENBFRso0cBCAC+J/b4LoML0L9/xlIs3/TZKC9CSVTQ2xljs3hdawvGi/+p210M
doXev6optgaDPj0q61HaCR1XhrCa7gK9jEC54M91LwrRzm5nBT9Fez/wezXn2I0v
56RIAn42k3OcDwWUDdPenzZS+/4/efJPyb/XO7sZMiD+OjjpXwNNu9ezqSvNZ1uo
/VcMHBTkQ0NqETO5Yt5KX9JkrKP2Q0BR2BVHGHp7K/PJiWnN+T8dTFr6RsiZsVWs
dD/5IPSkNAsi8E8fguuWecQtMftled/36QjlaXYgZ/U1kVi2mDUebd6oxTvB85fm
pCvIekFRNqs6TAd4de+pDBsbYY+vsE1tCsxvABEBAAG0JFBhc3Nib2x0IFBHUCA8
cGFzc2JvbHRAcGFzc2JvbHQuY29tPokBPQQTAQoAJwUCVGyjRwIbAwUJB4YfgAUL
CQgHAwUVCgkICwUWAgMBAAIeAQIXgAAKCRBPgZQCX9LZLAk6CACop+n6hgaCrFWU
m5EaT2+XBBw9rEbcISCH8Zeh2Xk1RmLOiTLSYRka8qnUcEBbSq8EOoJsfNdWEK8d
QwhearHZjRCUjrQMPsMwwKhKrkG7RR7VI+hN+7H7Joyq3UDE7S+55vvWd7hSZbPl
buhPWBirviN1Lovk2tZbI7ClW1+Cx9uK3lad1LywlPsxkCKbRfDcWrnLFKk1UnYi
229ZXCYjuJbzfPRWx039nVVt6IoOZnLCil5G9d5AFt5Ro7WFdormTsfP+EehLI7q
szrEVD2ZQgn+rSF8P97DLABDa28+JfTsnivVQn5cyLR6x+XTJp96SSprm5nY0C3+
ybog/dDFuQENBFRso0cBCAC50ryBhhesYxrJEPDvlK8R0E8zCxv7I6fXXgORNyAW
PAsZBUsaQizTUsP9VpO6Y0gOPGxvcGP9xSc+01n1stM9S7/+utCfm8yD4UtP9Ric
mkq/T/w/l9iLFypo6al47HW28mQlMvbUWSkMoK9JXRpB2c2VPmN8UXVQX4cQ++ad
YQNnRgSo3n+VdvIKgSW3rkcQIriGX3P79cciqAA/NzkivNyZSQaVBLJioO+kDkYu
Q+oIstvEusmHIon0Ltggi8B6LM5vAQpBRwQ9dfUgAbpQpfzm8VUkCGmsUr5hnOO3
tmaWOTKZcpXiF5+rW2NrqiAhRhm44s+JipmTE++u/6X9ABEBAAGJASUEGAEKAA8F
AlRso0cCGwwFCQeGH4AACgkQT4GUAl/S2Sx2LQgAoXOxfA5pOCm9UP2f2pQA7hyv
DEppROxkBLVcnZdpVFw4yrVQh/IWHSxcX0rcrTPlBjjFpTos+ACOZ5EKSRCHjIqF
biraG5/2YjKa5cqc7z/W9bSuhmWizPBpXlQk6MohG6jXlw7OyVosisbHGobFa5CW
hF+Kc8tb0mvk9vmqn/eDYnGYcSftapyGB3lq7w4qtKzlvn2g2FlnxJCdnrG3zGtO
Kqusl1GcnrNFuDDtDwZS1G+3T8Y8ZH8tRnTwrSeO3I7hw/cdzCEDg4isqFw371vz
UghWsISL244Umc6ZmTufAs+7/6sNNzFAb5SzwVmpLla1x3jth4bwLcJTGFq/vw==
=GG/Z
-----END PGP PUBLIC KEY BLOCK-----'
		];
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
	 * Given        I am logged in as Cedric Alfonsi, and I go to the user workspace
	 * When			I fill the "app search" field with "User Test"
	 * And			I click "search"
	 * Then 		I should see the view filtered with my search
	 * And			I should see the breadcrumb with the following:
	 *					| All users
	 *					| Search : User Test
	 */
	public function testSearchByKeywords()
	{
		$searchUser = 'Test';
		$hiddenUsers = [
			'Cédric Alfonsi',
//			'Jean René Bergamotte',
			'Remy Bertot',
			'Userone Company A',
			'Myriam Djerouni',
			'Aurelie Gherards',
			'Ismail Guennouni',
			'Admin Istrator',
			'User Lambda',
			'Frank Leboeuf',
			'Great Manager',
			'Kevin Muller',
//			'User Test',
//			'User b Test',
			'Darth Vader',
		];
		$breadcrumb = ['All users', 'Search : ' . $searchUser];

		// I am logged in as Cedric Alfonsi, and I go to the user workspace
		$this->loginAs('cedric@passbolt.com');
		$this->gotoWorkspace('user');

		// I fill the "app search" field with "tetris license"
		$this->inputText('js_app_filter_keywords', $searchUser);
		$this->clickElement("#js_app_filter_form button[value='search']");
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
