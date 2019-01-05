<?php
/**
 * Passbolt ~ Open source password manager for teams
 * Copyright (c) Passbolt SARL (https://www.passbolt.com)
 *
 * Licensed under GNU Affero General Public License version 3 of the or any later version.
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Passbolt SARL (https://www.passbolt.com)
 * @license   https://opensource.org/licenses/AGPL-3.0 AGPL License
 * @link      https://www.passbolt.com Passbolt(tm)
 * @since     2.0.0
 */
/**
 * Feature : Password Workspace
 *
 * - As a user I should be able to access the password workspace using route
 * - As a user I should be able to access the password workspace using route
 * - As a user I should be able to see the passwords workspace
 * - As a user I should be able to browse my passwords
 * - As a user I should be able to use the navigation filters
 * - As a user I should be able to view my password details
 * - As a user I should be able to fav/unfav
 * - As a user I should be able to search a password by keywords
 * - As a user when I filter the password workspace all passwords should be unselected
 * - As a user when I filter by keywords the password workspace the global filter "All items" should be selected
 * - As a user, I should be able to control the sidebar visibility through the sidebar button
 * - As a user, I should see a welcome message when I am on an empty password workspace
 * - As a user I should be able to sort the passwords browser by column
 * - As a user I should be able to filter my passwords by group
 * - As a user who doesn't belong to any group, I should'nt see the "Filter by group" section on the passwords workspace
 */
namespace Tests\LU\Base;

use App\Actions\GroupActionsTrait;
use App\Actions\MasterPasswordActionsTrait;
use App\Actions\PasswordActionsTrait;
use App\Actions\SetupActionsTrait;
use App\Actions\WorkspaceActionsTrait;
use App\Assertions\FilterAssertionsTrait;
use App\Assertions\GroupAssertionsTrait;
use App\Assertions\MasterPasswordAssertionsTrait;
use App\Assertions\PasswordAssertionsTrait;
use App\Assertions\SidebarAssertionsTrait;
use App\Assertions\WorkspaceAssertionsTrait;
use App\Lib\UuidFactory;
use App\PassboltTestCase;
use Data\Fixtures\Group;
use Data\Fixtures\User;
use Data\Fixtures\Resource;

class PasswordWorkspaceTest extends PassboltTestCase
{
    use FilterAssertionsTrait;
    use GroupActionsTrait;
    use GroupAssertionsTrait;
    use PasswordActionsTrait;
    use MasterPasswordActionsTrait;
    use MasterPasswordAssertionsTrait;
    use SetupActionsTrait;
    use SidebarAssertionsTrait;
    use WorkspaceActionsTrait;
    use WorkspaceAssertionsTrait;
    use PasswordAssertionsTrait;

    /**
     * Scenario: As a user I should be able to access the password workspace using route
     *
     * When  I am logged in as Ada
     * And   I enter the user workspace route in the url
     * Then  I should see the workspace
     *
     * @group LU
     * @group password
     * @group password-workspace
     * @group saucelabs
     * @group v2
     */
    public function testRoute()
    {
        $this->loginAs(User::get('ada'), ['url' => '/app/passwords']);
        $this->waitCompletion();
        $this->waitUntilISee('.page.password');
    }

    /**
     * Scenario: As a user I should be able to access the password workspace using route
     *
     * When  I am logged in as Ada
     * And   I enter the route in the url
     * Then  I should see the password selected
     *
     * @group LU
     * @group password
     * @group password-workspace
     * @group saucelabs
     * @group v2
     */
    public function testRoute_SelectPassword()
    {
        $resourceId = UuidFactory::uuid('resource.id.cakephp');
        $this->loginAs(User::get('ada'), ['url' => "/app/passwords/view/$resourceId"]);
        $this->waitCompletion();
        $this->assertPasswordSelected($resourceId);
    }

    /**
     * Scenario: As a user I should be able to see the passwords workspace
     *
     * Given I am logged in as Ada, and I go to the password workspace
     * Then  I should see the workspace primary menu
     * And   I should see the workspace secondary menu
     * And   I should see the workspace filters shortcuts
     * And   I should see a grid and its columns
     * And   I should see some grid columns are sortable
     * And   I should see the grid filtered by modified date
     * And   I should see the breadcrumb with the following:
     *       | All items
     *
     * @group LU
     * @group password
     * @group password-workspace
     * @group saucelabs
     * @group v2
     */
    public function testWorkspace() 
    {
        // Given I am Ada
        $user = User::get('ada');

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
                $this->findById('js_wsp_pwd_filter_shortcuts'),
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

        // I should see some grid columns are sortable
        $columnsId = ['name', 'username', 'uri', 'modified', 'owner'];
        for ($i = 0; $i < count($columnsId); $i++) {
            $this->assertElementHasClass(
                $this->findByCss('#js_wsp_pwd_browser .tableview-header .js_grid_column_' . $columnsId[$i]),
                'sortable'
            );
        }

        // I should see the grid filtered by modified date
        $columnHeaderModifiedElement = $this->findByCss('#js_wsp_pwd_browser .tableview-header .js_grid_column_modified');
        $this->assertElementHasClass($columnHeaderModifiedElement, 'sorted');
        $this->assertElementHasClass($columnHeaderModifiedElement, 'sort-desc');

        // I should see the breadcrumb with the following:
        //     | All items
        $this->assertBreadcrumb('password', ['All items']);
    }

    /**
     * Scenario: As a user I should be able to see my passwords
     *
     * Given I am logged in as Ada, and I go to the password workspace
     * Then  I should see rows representing my passwords
     *
     * @group LU
     * @group password
     * @group password-workspace
     * @group saucelabs
     * @group v2
     */
    public function testBrowsePasswords() 
    {
        // Given I am Ada
        // And I am logged in on the password workspace
        $user = User::get('ada');
        $this->loginAs($user);

        // I should see rows representing my passwords
        $passwords = Resource::getAll(array('user' => $user['name'], 'return_deny' => false));
        $this->assertVisibleByCss('#js_wsp_pwd_browser .tableview-content');
        foreach ($passwords as $password) {
            $this->assertVisibleByCss(
                '#js_wsp_pwd_browser .tableview-content tr#resource_'.$password['id'],
                'could not find password:' . $password['name']
            );
        }

        // @todo Test de rows details
    }

    /**
     * Scenario: As a user I should be able to filter my passwords
     * Given        I am logged in as Ada, and I go to the password workspace
     * Then  I should see the filter "All items" is selected.
     * When  I click on the favorite filter
     * Then  I should only see my favorite passwords
     * And   I should see the filter "All items" is not selected anymore
     * And   I should see the filter "Favorites" is selected.
     * And   I should see the breadcrumb with the following:
     *                    | All items
     *                    | Favorite
     * When  I click on the recently modified filter
     * Then  I should see my passwords ordered my modification date
     * And   I should see the filter "Recently modified" is selected.
     * And   I should see the breadcrumb with the following:
     *                    | All items
     *                    | Recently modified
     * When  I click on the shared with me filter
     * Then  I should only see the passwords that have been share with me
     * And   I should see the filter "Shared with me" is selected.
     * And   I should see the breadcrumb with the following:
     *                    | All items
     *                    | Shared with me
     * When  I click on the items I own filter
     * Then  I should only see the passwords I own
     * And   I should see the filter "Items I own" is selected.
     * And   I should see the breadcrumb with the following:
     *                    | All items
     *                    | Items I own
     *
     * @group LU
     * @group password
     * @group password-workspace
     * @group saucelabs
     * @group v2
     */
    public function testFilterPasswords() 
    {
        // Given I am Ada
        // And I am logged in on the password workspace
        $this->loginAs(User::get('ada'));

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
     * Scenario: As a user I should be able to view my password details
     *
     * Given I am logged in as Ada, and I go to the password workspace
     * When  I click on a password
     * Then  I should see a secondary side bar appearing
     * And   I should see the password's username
     * And   I should see the password's url
     * And   I should see the password's modified time
     * And   I should see the password's creator
     * And   I should see the password's modifier
     *
     * @group LU
     * @group password
     * @group password-workspace
     * @group saucelabs
     * @group v2
     */
    public function testPasswordDetails() 
    {
        // Given I am Ada
        // And I am logged in on the password workspace
        $this->loginAs(User::get('ada'));

        // When I click on a password
        $this->click("#js_wsp_pwd_browser .tableview-content div[title='Inkscape']");
        $this->waitCompletion();

        // And I should see a secondary side bar appearing
        $this->assertPageContainsElement('#js_pwd_details');

        // I should the details of the selected password
        $pwdDetails = [
            'username'        => 'vector',
            'url'             => 'https://inkscape.org/',
            'created'         => '/(a|an|[0-9]{1,2}) (minute|minutes|hour|hours|days) ago/',
            'modified'        => '/(a|an|[0-9]{1,2}) (minute|minutes|hour|hours|days) ago/',
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
     * Scenario: As a user I should be able to fav/unfav
     *
     * Given I am Ada
     * And   I go to the password workspace
     * When  I click on the favorite star located before the password (the password shouldn't be a favorite)
     * Then  I should see a confirmation of my action in the notification area
     * And   I should see the star in red
     * When  I click on the favorite filter
     * Then  I should see the password I just added to my favorites in the list of passwords
     * When  I go Back to All items
     * And   I click on the favorite red star located before the password (the password has to be a favorite)
     * Then  I should see a confirmation of my action in the notification area
     * And   I should see the star is white
     * When  I click on the favorite filter
     * Then  I shouldn't see anymore the password in my list of favorite passwords
     *
     * @group LU
     * @group password
     * @group password-workspace
     * @group saucelabs
     * @group v2
     */
    public function testFavorite() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        $resourceId = UuidFactory::uuid('resource.id.cakephp');
        $resource = Resource::get(['user' => 'betty', 'id' => $resourceId]);

        // Given I am Ada
        // And I am logged in on the password workspace
        $this->loginAs(User::get('ada'));

        // When I click on the favorite star located before the password (the password shouldn't be a favorite)
        $this->clickPasswordFavorite($resourceId);
        $this->waitCompletion();

        // Then I should see a confirmation of my action in the notification area
        $this->assertNotification('app_favorites_add_success');

        // And I should see the star becoming red
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
        $this->waitCompletion();

        // And I click on the favorite red star located before the password (the password has to be a favorite)
        $this->clickPasswordFavorite($resourceId);
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
    }

    /**
     * Scenario: As a user I should be able to search a password by keywords
     * Given        I am logged in as Ada, and I go to the password workspace
     * When  I fill the "app search" field with "shared resource"
     * And   I click "search"
     * Then  I should see the view filtered with my search
     * And   I should see the breadcrumb with the following:
     *                    | All items
     *                    | Search : shared resource
     * When  I make a search to get an empty result
     * Then  I should see an empty workspace with the text relative to the empty search filter
     *
     * @group LU
     * @group password
     * @group password-workspace
     * @group saucelabs
     * @group v2
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
        // And I am logged in on the password workspace
        $this->loginAs(User::get('ada'));

        // I fill the "app search" field with "shared resource"
        $this->inputText('js_app_filter_keywords', $searchPwd);
        $this->click("#js_app_filter_form button[value='search']");
        $this->waitUntilISee('#js_wsp_password_breadcrumb', "/Search : $searchPwd/");

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

        // When I make a search to get an empty result
        $this->inputText('js_app_filter_keywords', '011011100110111101110100001000000110011001101111011101010110111001100100');

        // I should see an empty workspace with the text relative to the empty search filter
        $this->waitUntilISee('#js_wsp_pwd_browser .empty-content');
        $this->assertElementContainsText('#js_wsp_pwd_browser .empty-content', 'None of your passwords matched this search.');
    }

    /**
     * Scenario: As a user when I filter the password workspace all passwords should be unselected
     *
     * Given I am logged in as Ada, and I go to the password workspace
     * When  I select a password I own
     * And   I filter the workspace by keywords
     * Then  I should see the password unselected
     *
     * @group LU
     * @group password
     * @group password-workspace
     * @group v2
     */
    public function testSearchByKeywordsUnselectPasswords() 
    {
        $searchPwd = 'Apache';
        $resourceId = UuidFactory::uuid('resource.id.apache');

        // Given I am Ada
        // And I am logged in on the password workspace
        $this->loginAs(User::get('ada'));

        // When I select a password I own
        $this->clickPassword($resourceId);

        // And I filter the workspace by keywords
        $this->inputText('js_app_filter_keywords', $searchPwd);
        $this->click("#js_app_filter_form button[value='search']");
        $this->waitUntilISee('#js_wsp_password_breadcrumb', "/Search : $searchPwd/");

        // Then I should see the password unselected
        $this->assertPasswordNotSelected($resourceId);
    }

    /**
     * Scenario: As a user when I filter by keywords the password workspace the global filter "All items" should be selected
     *
     * Given I am logged in as Ada, and I go to the password workspace
     * When  I click on the recently modified filter
     * Then  I should see that menu All items is not selected anymore
     * When  I fill the "app search" field with "shared resource"
     * Then  I should see the filter "All items" is selected.
     *
     * @group LU
     * @group password
     * @group password-workspace
     * @group v2
     */
    public function testSearchByKeywordsChangesGlobalFilterToAllItems() 
    {
        $searchPwd = 'Apache';

        // Given I am Ada
        // And I am logged in on the password workspace
        $this->loginAs(User::get('ada'));

        // When I click on the recently modified filter
        $this->clickLink("Favorite");
        $this->waitCompletion();

        // Then I should see that menu All items is not selected anymore
        $this->assertFilterIsNotSelected('js_pwd_wsp_filter_all');

        // When I fill the "app search" field with "shared resource"
        $this->inputText('js_app_filter_keywords', $searchPwd);
        $this->click("#js_app_filter_form button[value='search']");
        $this->waitUntilISee('#js_wsp_password_breadcrumb', "/Search : $searchPwd/");

        // Then I should see the filter "All items" is selected.
        $this->assertFilterIsSelected('js_pwd_wsp_filter_all');
    }

    /**
     * Scenario: As a logged in user, I should be able to control the sidebar visibility through the sidebar button
     *
     * Given I am logged in as ada
     * And   I am on the password workspace
     * Then  I should see that the sidebar button is pressed
     * And   I should not see the sidebar
     * When  I click on a resource to select it
     * Then  I should see the sidebar
     * When  I click on the same resource to deselect it
     * Then  I should not see the sidebar anymore
     * When  I click on the same resource to select it
     * Then  I should see that the sidebar is visible again
     * When  I toggle off the sidebar button
     * Then  I should see that the sidebar button is now deactivated
     * And   I should not see the sidebar anymore
     * When  I click on the resource again to deselect it
     * Then  I should see that the sidebar button is deactivated
     * And   I should see that the sidebar is not visible
     * When  I toggle in the sidebar button
     * Then  I should see that the sidebar is not visible
     * When  I click on the resource again to select it
     * Then  I should see that the sidebar is visible
     * When  I click on the close button at the top of the sidebar
     * Then  I should see that the sidebar button is deactivated
     * And   I should not see the sidebar anymore
     *
     * @group LU
     * @group password
     * @group password-workspace
     * @group v2
     */
    public function testSidebarVisibility() 
    {
        // Given I am Ada
        // And I am logged in on the password workspace
        $this->loginAs(User::get('ada'));

        $this->assertToggleButtonStatus('js_wk_secondary_menu_view_sidebar_button', TOGGLE_BUTTON_PRESSED);

        // I should not see the sidebar
        $this->assertNotVisibleByCss('#js_pwd_details');

        // And I am editing a password I own
        $resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));

        // Click on a password
        $this->clickPassword($resource['id']);

        // I should see a secondary side bar appearing
        $this->assertVisible('js_pwd_details');

        // Click on a password to deselect it
        $this->clickPassword($resource['id']);

        // I should not see the secondary sidebar
        $this->assertNotVisibleByCss('#js_pwd_details');

        // Click on a password
        $this->clickPassword($resource['id']);

        // I should that the sidebar is visible again
        $this->assertVisible('js_pwd_details');

        // Click on the sidebar button.
        $this->click('js_wk_secondary_menu_view_sidebar_button');

        // I should see that the button is not pressed.
        $this->assertToggleButtonStatus('js_wk_secondary_menu_view_sidebar_button', TOGGLE_BUTTON_UNPRESSED);

        // I should not see the sidebar anymore.
        $this->assertNotVisibleByCss('#js_pwd_details');

        // Click on the password again to deselect it.
        $this->clickPassword($resource['id']);

        // The toggle button should still be unpressed.
        $this->assertToggleButtonStatus('js_wk_secondary_menu_view_sidebar_button', TOGGLE_BUTTON_UNPRESSED);

        // I should not see the sidebar.
        $this->assertNotVisibleByCss('#js_pwd_details');

        // Click on the sidebar button to toggle it in.
        $this->click('js_wk_secondary_menu_view_sidebar_button');

        // The toggle button should still be pressed.
        $this->assertToggleButtonStatus('js_wk_secondary_menu_view_sidebar_button', TOGGLE_BUTTON_PRESSED);

        // I should not see the sidebar.
        $this->assertNotVisibleByCss('#js_pwd_details');

        // Click on the password again to select it.
        $this->clickPassword($resource['id']);

        // I should that the sidebar is visible
        $this->assertVisible('js_pwd_details');

        // I click on the button close at the top of the dialogue.
        $this->click('#js_pwd_details .js_sidebar_close');

        // I should see that the sidebar button is deactivated
        $this->assertToggleButtonStatus('js_wk_secondary_menu_view_sidebar_button', TOGGLE_BUTTON_UNPRESSED);

        // Then I should not see the sidebar anymore.
        $this->assertNotVisibleByCss('#js_pwd_details');
    }

    /**
     * Scenario: As a logged in user, I should see a welcome message when I am on an empty password workspace
     *
     * Given I create a new account as John Doe, and I proceed with the setup.
     * When  I log in as John
     * Then  I should see the password workspace with the text 'Welcome to passbolt!)
     * And   I should see an illustration (not testable here)
     * When  I click on the filter "favorites"
     * Then  I should not see the welcome message anymore
     * When  I click on all items filter
     * Then  I should see the welcome message again
     * When  I create a new password
     * Then  I should see the new password in my password list
     * And   I should not see the welcome message anymore
     * When  I delete the newly created password
     * Then  I should not see any password in my password list anymore
     * And   I should see the welcome message again
     *
     * @group LU
     * @group password
     * @group password-workspace
     * @group v2
     * @group broken
     */
    public function testPasswordWorkspaceEmptyState() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        $john = User::get('john');
        // Register John Doe as a user.
        $this->registerUser($john['FirstName'], $john['LastName'], $john['Username']);

        // Go to setup page and register
        $this->goToSetup($john['Username']);
        $this->completeSetupWithKeyGeneration(
            [
            'username' => $john['Username'],
            'masterpassword' =>  $john['MasterPassword']
            ]
        );

        $this->loginAs(
            ['Username' => $john['Username'], 'MasterPassword' => $john['MasterPassword']], ['setConfig' => false]
        );
        // Check we are logged in.
        $this->waitCompletion();
        $this->waitUntilISee('#js_app_controller.ready');

        // I should see an empty workspace with the text Welcome to passbolt!
        $this->waitUntilISee('.empty-content');
        $this->assertElementContainsText('.empty-content', 'Welcome to passbolt!');

        // The password browser should have the class empty.
        $this->assertElementHasClass($this->findById('js_wsp_pwd_browser'), 'empty');

        // When I click on the favorite filter.
        $this->clickLink("Favorite");
        $this->waitCompletion();
        sleep(2);
        // The layer empty content with the text relative to the empty favorite filter
        $this->waitUntilISee('.empty-content');
        $this->assertElementContainsText('.empty-content', 'None of your passwords are yet marked as favorite.');

        // When I click on the recently modified filter
        $this->clickLink("Recently modified");
        $this->waitCompletion();
        sleep(2);
        // I should see an empty workspace with the text relative to the empty recently modified filter
        $this->waitUntilISee('.empty-content');
        $this->assertElementContainsText('.empty-content', 'Welcome to passbolt!');

        // When I click on the shared with me filter.
        $this->clickLink("Shared with me");
        $this->waitCompletion();
        sleep(2);
        // I should see an empty workspace with the text relative to the empty shared with me filter
        $this->waitUntilISee('.empty-content');
        $this->assertElementContainsText('.empty-content', 'No passwords are shared with you yet.');

        // When I click on the owner filter.
        $this->clickLink("Items I own");
        $this->waitCompletion();
        sleep(2);
        // I should see an empty workspace with the text relative to the empty owner filter
        $this->waitUntilISee('.empty-content');
        $this->assertElementContainsText('.empty-content', 'You do not own any passwords yet.');

        // When I go Back to All items.
        $this->clickLink("All items");
        $this->waitCompletion();
        sleep(2);
        // I should see an empty workspace with the text Welcome to passbolt!
        $this->waitUntilISee('.empty-content');
        $this->assertElementContainsText('.empty-content', 'Welcome to passbolt!');

        // When I create a password.
        $this->createPassword(
            [
            'name' => 'passwordtest',
            'username' => 'test',
            'uri' => '',
            'password' => 'testpassword',
            'description' => ''
            ]
        );

        // The layer empty content with text should not be visible.
        $this->assertNotVisibleByCss('.empty-content');
        $this->assertElementNotContainText($this->findById('js_wsp_pwd_browser'), 'Welcome to passbolt!');
    }

    /**
     * Scenario: As a user I should be able to sort the passwords browser by column
     *
     * Given I am logged in as Ada, and I go to the password workspace
     * When  I sort the passwords browser by resource name
     * Then  I should see it sorted by resource name
     * When  I sort the passwords browser by username
     * Then  I should see it sorted by username
     * When  I sort the passwords browser by uri
     * Then  I should see it sorted by uri
     * When  I sort the passwords browser by modified
     * Then  I should see it sorted by modified
     *
     * @group LU
     * @group password
     * @group password-workspace
     * @group saucelabs
     * @group v2
     */
    public function testSortByColumn()
    {
        // Given I am Ada
        // And I am logged in on the password workspace
        $this->loginAs(User::get('ada'));

        // When I sort the passwords browser by resource name
        $columnId = 'name';
        $this->click('.js_grid_column_' . $columnId);
        $this->waitCompletion();

        // Then I should see it sorted by resource name
        $columnHeaderResourceElement = $this->find('#js_wsp_pwd_browser .tableview-header .js_grid_column_' . $columnId);
        $this->assertElementHasClass($columnHeaderResourceElement, 'sorted');
        $this->assertElementHasClass($columnHeaderResourceElement, 'sort-asc');

        // When I sort the passwords browser by username
        $columnId = 'username';
        $this->click('.js_grid_column_' . $columnId);
        $this->waitCompletion();

        // Then I should see it sorted by username
        $columnHeaderUsernameElement = $this->find('#js_wsp_pwd_browser .tableview-header .js_grid_column_' . $columnId);
        $this->assertElementHasClass($columnHeaderUsernameElement, 'sorted');
        $this->assertElementHasClass($columnHeaderUsernameElement, 'sort-asc');
        $this->assertElementHasNotClass($columnHeaderResourceElement, 'sorted');
        $this->assertElementHasNotClass($columnHeaderResourceElement, 'sort-asc');

        // When I sort the passwords browser by uri
        $columnId = 'uri';
        $this->click('.js_grid_column_' . $columnId);
        $this->waitCompletion();

        // Then I should see it sorted by uri
        $columnHeaderUriElement = $this->find('#js_wsp_pwd_browser .tableview-header .js_grid_column_' . $columnId);
        $this->assertElementHasClass($columnHeaderUriElement, 'sorted');
        $this->assertElementHasClass($columnHeaderUriElement, 'sort-asc');
        $this->assertElementHasNotClass($columnHeaderUsernameElement, 'sorted');
        $this->assertElementHasNotClass($columnHeaderUsernameElement, 'sort-asc');

        // When I sort the passwords browser by modified
        $columnId = 'modified';
        $this->click('.js_grid_column_' . $columnId);
        $this->waitCompletion();

        // Then I should see it sorted by modified
        $columnHeaderModifiedElement = $this->find('#js_wsp_pwd_browser .tableview-header .js_grid_column_' . $columnId);
        $this->assertElementHasClass($columnHeaderModifiedElement, 'sorted');
        $this->assertElementHasClass($columnHeaderModifiedElement, 'sort-asc');
        $this->assertElementHasNotClass($columnHeaderUriElement, 'sorted');
        $this->assertElementHasNotClass($columnHeaderUriElement, 'sort-asc');
    }

    /**
     * Scenario: As a user I should be able to filter my passwords by group
     *
     * Given I am logged in as Irene on the password workspace
     * When  I edit the group "ergonom"
     * And   I add "kathleen" as a member of the group
     * And   I save the changes
     * Then  I should see a notification message saying that the changes have been taken into account
     * When  I log out
     * And   I log in again as Kathleen
     * Then  I should see a section called "Filter by groups" in the sidebar
     * And   I should see that there is a group called "ergonom" in this section
     * And   I should see that there is a group called "freelancer" in this section
     * When  I click on the group "freelancer"
     * And   I wait the the password workspace to reload
     * Then  I should see the password "framasoft" in the list
     * And   I shouldn't see the password "debian" in the list
     * When  I click on the group "ergonom"
     * And   I wait the the password workspace to reload
     * Then  I should see the password "debian" in the list
     * And   I shouldn't see the password "framasoft" in the list
     *
     * @group LU
     * @group password
     * @group password-workspace
     * @group v2
     */
    public function testFilterByGroups() 
    {
        $this->resetDatabaseWhenComplete();

        // Log in as Irene (group manager of ergonom) and add Kathleen to the group "marketing".
        // Given I am an administrator.
        $user = User::get('irene');

        // I am logged in as irene.
        $this->loginAs($user);

        // I edit the group "ergonom".
        $group = Group::get(['id' => UuidFactory::uuid('group.id.ergonom')]);
        $this->gotoEditGroup($group['id']);

        // When I add a the user "kathleen" to the group.
        $kathleen = User::get('kathleen');
        $this->searchGroupUserToAdd($kathleen, $user);
        $this->addTemporaryGroupUser($kathleen);

        // And I save the changes.
        $this->click('.edit-group-dialog a.button.primary');

        // And I enter my master password.
        $this->assertMasterPasswordDialog($user);
        $this->enterMasterPassword($user['MasterPassword']);

        // Then I should see that the dialog disappears
        $this->waitUntilIDontSee('.edit-group-dialog');

        // And I should see a confirmation message saying that the group members changes have been saved.
        $this->assertNotification('app_groups_edit_success');

        // When I log out.
        $this->logout();

        // And I log in again as Kathleen.
        $user = User::get('kathleen');

        $this->loginAs($user);

        // I should see a section called "Filter by groups".
        $this->waitUntilISee("#js_wsp_pwd_password_categories", '/Filter by groups/');

        // I should see a group "Ergonom".
        $this->assertICanSeeGroup('Ergonom');

        // And I should see a group "Freelancer".
        $this->assertICanSeeGroup('Freelancer');

        // Assert that the filters function properly.
        // In freelancer group, debian shouldn't be there but framasoft should be
        // In ergonom group, debian should be there but not framasoft

        // I Click on the group "freelancer".
        $this->clickGroup(UuidFactory::uuid('group.id.freelancer'), 'password');
        $this->waitCompletion();

        // I should see the password "framasoft".
        $this->assertICanSeePassword('framasoft');
        // And I shouldn't see the password "debian".
        $this->assertICannotSeePassword('Debian');

        // When I click on the group "ergonom".
        $this->clickGroup(UuidFactory::uuid('group.id.ergonom'), 'password');
        $this->waitCompletion();

        // I should see the password "debian".
        $this->assertICanSeePassword('Debian');

        // And I shouldn't see the password "framasoft".
        $this->assertICannotSeePassword('framasoft');
    }

    /**
     * Scenario: As a user who doesn't belong to any group, I should'nt see the "Filter by group" section on the passwords workspace
     *
     * Given I am logged in as Ada on the passwords workspace
     * Then  I should'nt see the "Filter by groups" section
     *
     * @group LU
     * @group password
     * @group password-workspace
     * @group v2
     */
    public function testFilterByGroupsNoGroups() 
    {
        // Given I am logged in as Ada.
        $this->loginAs(User::get('ada'));

        // Force wait.
        sleep(3);

        // I should see a section called "Filter by groups".
        $this->waitUntilIDontSee("#js_wsp_pwd_password_categories", '/Filter by groups/');
    }
}
