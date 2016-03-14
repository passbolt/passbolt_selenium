<?php
/**
 * Feature : Password Workspace
 *
 * - As a user I should be able to see the passwords workspace
 * - As a user I should be able to browse my passwords
 * - As a user I should be able to use the navigation filters
 * - As a user I should be able to view my password details
 * - As a user I should be able to fav/unfav
 * - As a user I should be able to search a password by keywords
 * - As a user, I should be able to control the sidebar visibility through the sidebar button
 *
 * @copyright (c) 2015-present Bolt Softwares Pvt Ltd
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class PasswordWorkspaceTest extends PassboltTestCase
{

    /**
     * Scenario :   As a user I should be able to see the passwords workspace
     * Given        I am logged in as Ada, and I go to the password workspace
     * Then         I should see the workspace primary menu
     * And          I should see the workspace secondary menu
     * And          I should see the workspace filters shortcuts
     * And          I should see a grid and its columns
     * And          I should see the breadcrumb with the following:
     *                 | All items
     */
    public function testWorkspace() {
        // Given I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // I should see the workspace primary menu
        $buttons = ['copy', 'edit', 'share', 'more'];
        for ($i = 0; $i < count($buttons); $i++) {
            $this->assertElementContainsText(
                $this->findByCss('#js_wsp_primary_menu_wrapper ul'),
                $buttons[$i]
            );
        }

        // I should see the workspace filters shortcuts
        $filters = ['All items', 'Favorite', 'Recently modified', 'Shared with me', 'Items I own'];
        for ($i = 0; $i < count($filters); $i++) {
            $this->assertElementContainsText(
                $this->findByCss('#js_wsp_pwd_filter_shortcuts'),
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

        // I should see the breadcrumb with the following:
        //     | All items
        $this->assertBreadcrumb('password', ['All items']);
    }

    /**
     * Scenario :   As a user I should be able to see my passwords
     * Given        I am logged in as Ada, and I go to the password workspace
     * Then         I should see rows representing my passwords
     */
    public function testBrowsePasswords() {
        // Given I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // I should see rows representing my passwords
        $passwords = Resource::getAll(array('user' => $user['name'], 'return_deny' => false));
        $this->assertVisible('#js_wsp_pwd_browser .tableview-content');
        foreach ($passwords as $password) {
            $this->assertVisible('#js_wsp_pwd_browser .tableview-content tr#resource_'.$password['id'],
                'could not find password:' . $password['name']);
        }

        // @todo Test de rows details
    }

    /**
     * Scenario :   As a user I should be able to filter my passwords
     * Given        I am logged in as Ada, and I go to the password workspace
     * Then         I should see the filter "All items" is selected.
     * When         I click on the favorite filter
     * Then         I should only see my favorite passwords
     * And          I should see the filter "All items" is not selected anymore
     * And          I should see the filter "Favorites" is selected.
     * And          I should see the breadcrumb with the following:
     *                    | All items
     *                    | Favorite
     * When         I click on the recently modified filter
     * Then         I should see my passwords ordered my modification date
     * And          I should see the filter "Recently modified" is selected.
     * And          I should see the breadcrumb with the following:
     *                    | All items
     *                    | Recently modified
     * When         I click on the shared with me filter
     * Then         I should only see the passwords that have been share with me
     * And          I should see the filter "Shared with me" is selected.
     * And          I should see the breadcrumb with the following:
     *                    | All items
     *                    | Shared with me
     * When         I click on the items I own filter
     * Then         I should only see the passwords I own
     * And          I should see the filter "Items I own" is selected.
     * And          I should see the breadcrumb with the following:
     *                    | All items
     *                    | Items I own
     */
    public function testFilterPasswords() {
        // Given I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // Assert menu All items is selected.
        $this->assertFilterIsSelected('js_pwd_wsp_filter_all');

        // I click on the favorite filter
        $this->clickLink("Favorite");
        $this->waitCompletion();
        // I should only see my favorite passwords
        // @todo Test with a case which already has favorites
        // I should see the breadcrumb with the following:
        //     | All items
        //    | Search : Favorite
        $this->assertBreadcrumb('password', ['All items', 'Favorite']);

        // I should see that menu favorite is selected.
        $this->assertFilterIsSelected('js_pwd_wsp_filter_favorite');

        // And I should see that menu All items is not selected anymore.
        $this->assertFilterIsNotSelected('js_pwd_wsp_filter_all');

        // I click on the recently modified filter
        $this->clickLink("Recently modified");
        $this->waitCompletion();

        // I should see the filter item Recently modified selected.
        $this->assertFilterIsSelected('js_pwd_wsp_filter_modified');

        // I should see my passwords ordered by modification date
        // @todo Test with a case where the modified date are different
        // I should see the breadcrumb with the following:
        //     | All items
        //    | Search : Recently modified
        $this->assertBreadcrumb('password', ['All items', 'Recently modified']);

        // I click on the shared with me filter
        $this->clickLink("Shared with me");
        $this->waitCompletion();

        // I should see the filter item Shared with me selected.
        $this->assertFilterIsSelected('js_pwd_wsp_filter_share');

        // I should only see the passwords that have been shared with me
        $passwords = [
	        'Inkscape',
	        'free software foundation europe',
	        'bower',
	        'ftp',
	        'Docker',
	        'Canjs',
	        'Debian',
	        'centos',
	        'framasoft',
	        'Gnupg',
	        'composer',
	        'Git',
        ];
        for ($i = 0; $i < count($passwords); $i++) {
            $this->assertElementContainsText(
                $this->findByCss('#js_wsp_pwd_browser .tableview-content'),
                $passwords[$i]
            );
        }
        // I should see the breadcrumb with the following:
        //     | All items
        //    | Search : Shared with me
        $this->assertBreadcrumb('password', ['All items', 'Shared with me']);

        // I click on the items I own filter
        $this->clickLink("Items I own");
        $this->waitCompletion();

        // I should see the filter items I own selected.
        $this->assertFilterIsSelected('js_pwd_wsp_filter_own');

        // I should only see the passwords I own
        // @todo Test with a case which owns some passwords
        // I should see the breadcrumb with the following:
        //     | All items
        //    | Search : Items I own
        $this->assertBreadcrumb('password', ['All items', 'Items I own']);
    }

    /**
     * Scenario :   As a user I should be able to view my password details
     * Given        I am logged in as Ada, and I go to the password workspace
     * When         I click on a password
     * Then         I should see a secondary side bar appearing
     * And          I should see the password's username
     * And          I should see the password's url
     * And          I should see the password's modified time
     * And          I should see the password's creator
     * And          I should see the password's modifier
     */
    public function testPasswordDetails() {
        // Given I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // When I click on a password
        $this->click("#js_wsp_pwd_browser .tableview-content div[title='Inkscape']");
        $this->waitCompletion();

        // And I should see a secondary side bar appearing
        $this->assertPageContainsElement('#js_pwd_details');

        // I should the details of the selected password
        $pwdDetails = [
            'username'        => 'vector',
            'url'             => 'https://inkscape.org/',
            'modified'        => '/([0-9]{1}|a) (minute)[s]? ago/',
            'created-by'      => 'edith@passbolt.com',
            'modified-by'     => 'edith@passbolt.com',
        ];
        // And I should see the password's username
        $cssSelector = '#js_pwd_details .detailed-information li.username';
        $this->assertElementContainsText(
            $this->findByCss($cssSelector),
            $pwdDetails['username']
        );
        // And I should see the password's url
        $cssSelector = '#js_pwd_details .detailed-information li.uri';
        $this->assertElementContainsText(
            $this->findByCss($cssSelector),
            $pwdDetails['url']
        );
        // And I should see the password's modified time
        $cssSelector = '#js_pwd_details .detailed-information li.modified';
        $this->assertElementContainsText(
            $this->findByCss($cssSelector),
            $pwdDetails['modified']
        );
        // And I should see the password's creator
        $cssSelector = '#js_pwd_details .detailed-information li.created-by';
        $this->assertElementContainsText(
            $this->findByCss($cssSelector),
            $pwdDetails['created-by']
        );
        // And I should see the password's modifier
        $cssSelector = '#js_pwd_details .detailed-information li.modified-by';
        $this->assertElementContainsText(
            $this->findByCss($cssSelector),
            $pwdDetails['modified-by']
        );
    }

    /**
     * Scenario :   As a user I should be able to fav/unfav
     * Given        I am Ada
	 * And			I go to the password workspace
     * When         I click on the favorite star located before the password (the password shouldn't be a favorite)
     * Then         I should see the star becoming red
	 * When 		the favorite request is completed
     * Then			I should see a confirmation of my action in the notification area
	 * And 			I should see the star in red
     * When         I click on the favorite filter
     * Then         I should see the password I just added to my favorites in the list of passwords
     * When         I go Back to All items
     * And          I click on the favorite red star located before the password (the password has to be a favorite)
     * Then         I should see the star becoming white
	 * When			the favorite request is completed
     * And          I should see a confirmation of my action in the notification area
	 * And			I should see the star is white
     * When         I click on the favorite filter
     * Then         I shouldn't see anymore the password in my list of favorite passwords
     */
    public function testFavorite() {
		$resourceId = Uuid::get('resource.id.apache');
		$resource = Resource::get(array(
			'user' => 'betty',
			'id' => $resourceId
		));

        // Given I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // When I click on the favorite star located before the password (the password shouldn't be a favorite)
        $this->clickPasswordFavorite($resourceId);

		// Then I should see the star becoming red
		$this->assertTrue($this->isPasswordFavorite($resourceId));

        // When the favorite request is completed
		$this->waitCompletion();

		// Then I should see a confirmation of my action in the notification area
		$this->assertNotification('app_favorites_add_success');

		// And I should see the star in red
		$this->assertTrue($this->isPasswordFavorite($resourceId));

        // When I click on the favorite filter
        $this->clickLink("Favorite");
        $this->waitCompletion();

        // Then I should see the password I just added to my favorites in the list of passwords
        $this->assertElementContainsText(
            $this->findByCss('#js_wsp_pwd_browser .tableview-content'),
			$resource['name']
        );

	    // When I go Back to All items.
	    $this->clickLink("All items");

        // And I click on the favorite red star located before the password (the password has to be a favorite)
		$this->clickPasswordFavorite($resourceId);

        // Then I should see the star becoming white
		$this->assertFalse($this->isPasswordFavorite($resourceId));

		// When the favorite request is completed
		$this->waitCompletion();

	    // Then I should see a confirmation of my action in the notification area
	    $this->assertNotification('app_favorites_delete_success');

		// And I should see the star is white
		$this->assertFalse($this->isPasswordFavorite($resourceId));

		// When I click on the favorite filter
	    $this->clickLink("Favorite");
		$this->waitCompletion();

        // Then I shouldn't see anymore the password in my list of favorite passwords
        $this->assertElementNotContainText(
            $this->findByCss('#js_wsp_pwd_browser .tableview-content'),
			$resource['name']
        );

        // Since content was edited, we reset the database
        $this->resetDatabase();
    }

    /**
     * Scenario :   As a user I should be able to search a password by keywords
     * Given        I am logged in as Ada, and I go to the password workspace
     * When         I fill the "app search" field with "shared resource"
     * And          I click "search"
     * Then         I should see the view filtered with my search
     * And          I should see the breadcrumb with the following:
     *                    | All items
     *                    | Search : shared resource
     */
    public function testSearchByKeywords()
    {
        $searchPwd = 'Enlightenment';
        $hiddenPwd = [
	        'Inkscape',
	        'free software foundation europe',
	        'bower',
	        'ftp',
	        'Docker',
	        'Canjs',
	        'Debian',
	        'centos',
	        'framasoft',
	        'Gnupg',
	        'composer',
	        'Git',
        ];
        $breadcrumb = ['All items', 'Search : ' . $searchPwd];

        // Given I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // I fill the "app search" field with "shared resource"
        $this->inputText('js_app_filter_keywords', $searchPwd);
        $this->click("#js_app_filter_form button[value='search']");
        $this->waitCompletion();

        // I should see the view filtered with my search
        $this->assertElementContainsText(
            $this->findByCss('#js_wsp_pwd_browser .tableview-content'),
            $searchPwd
        );
        for ($i=0; $i< count($hiddenPwd); $i++) {
            $this->assertElementNotContainText(
                $this->findByCss('#js_wsp_pwd_browser .tableview-content'),
                $hiddenPwd[$i]
            );
        }

        // I should see the breadcrumb with the following:
        //    | All items
        //    | Search : shared resource
        $this->assertBreadcrumb('password', $breadcrumb);
    }


	/**
	 * Scenario:    As a logged in user, I should be able to control the sidebar visibility through the sidebar button
	 * Given        I am logged in as ada
	 * And          I am on the password workspace
	 * Then         I should see that the sidebar button is pressed
	 * And          I should not see the sidebar
	 * When         I click on a resource to select it
	 * Then         I should see the sidebar
	 * When         I click on the same resource to deselect it
	 * Then         I should not see the sidebar anymore
	 * When         I click on the same resource to select it
	 * Then         I should see that the sidebar is visible again
	 * When         I toggle off the sidebar button
	 * Then         I should see that the sidebar button is now deactivated
	 * And          I should not see the sidebar anymore
	 * When         I click on the resource again to deselect it
	 * Then         I should see that the sidebar button is deactivated
	 * And          I should see that the sidebar is not visible
	 * When         I toggle in the sidebar button
	 * Then         I should see that the sidebar is not visible
	 * When         I click on the resource again to select it
	 * Then         I should see that the sidebar is visible
	 * When         I click on the close button at the top of the sidebar
	 * Then         I should see that the sidebar button is deactivated
	 * And			I should not see the sidebar anymore
	 */
	public function testSidebarVisibility() {
		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);

		// And I am logged in on the password workspace
		$this->loginAs($user);

		$this->assertToggleButtonStatus('js_wk_secondary_menu_view_sidebar_button', TOGGLE_BUTTON_PRESSED);

		// I should not see the sidebar
		$this->assertNotVisible('#js_pwd_details');

		// And I am editing a password I own
		$resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));

		// Click on a password
		$this->clickPassword($resource['id']);

		// I should see a secondary side bar appearing
		$this->assertVisible('#js_pwd_details');

		// Click on a password to deselect it
		$this->clickPassword($resource['id']);

		// I should not see the secondary sidebar
		$this->assertNotVisible('#js_pwd_details');

		// Click on a password
		$this->clickPassword($resource['id']);

		// I should that the sidebar is visible again
		$this->assertVisible('#js_pwd_details');

		// Click on the sidebar button.
		$this->click('js_wk_secondary_menu_view_sidebar_button');

		// I should see that the button is not pressed.
		$this->assertToggleButtonStatus('js_wk_secondary_menu_view_sidebar_button', TOGGLE_BUTTON_UNPRESSED);

		// I should not see the sidebar anymore.
		$this->assertNotVisible('#js_pwd_details');

		// Click on the password again to deselect it.
		$this->clickPassword($resource['id']);

		// The toggle button should still be unpressed.
		$this->assertToggleButtonStatus('js_wk_secondary_menu_view_sidebar_button', TOGGLE_BUTTON_UNPRESSED);

		// I should not see the sidebar.
		$this->assertNotVisible('#js_pwd_details');

		// Click on the sidebar button to toggle it in.
		$this->click('js_wk_secondary_menu_view_sidebar_button');

		// The toggle button should still be pressed.
		$this->assertToggleButtonStatus('js_wk_secondary_menu_view_sidebar_button', TOGGLE_BUTTON_PRESSED);

		// I should not see the sidebar.
		$this->assertNotVisible('#js_pwd_details');

		// Click on the password again to select it.
		$this->clickPassword($resource['id']);

		// I should that the sidebar is visible
		$this->assertVisible('#js_pwd_details');

		// I click on the button close at the top of the dialogue.
		$this->click('#js_pwd_details .js_sidebar_close');

		// I should see that the sidebar button is deactivated
		$this->assertToggleButtonStatus('js_wk_secondary_menu_view_sidebar_button', TOGGLE_BUTTON_UNPRESSED);

		// Then I should not see the sidebar anymore.
		$this->assertNotVisible('#js_pwd_details');
	}
}
