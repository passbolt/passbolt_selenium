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
 * Feature : Settings Workspace
 *
 * - As a user I should be able to access the settings workspace using route
 * - As a user I should be able to search a user by keywords
 */
namespace Tests\LU\Base;

use App\Actions\WorkspaceActionsTrait;
use App\Assertions\WorkspaceAssertionsTrait;
use App\PassboltTestCase;
use Data\Fixtures\User;

class SettingsWorkspaceTest extends PassboltTestCase
{
    use WorkspaceActionsTrait;
    use WorkspaceAssertionsTrait;

    /**
     * Scenario: As a user I should be able to access the settings workspace using route
     *
     * When  I am logged in as Ada
     * And   I enter the user workspace route in the url
     * Then  I should see the settings profile screen
     *
     * @group LU
     * @group settings
     * @group settings-workspace
     * @group saucelabs
     * @group v2
     */
    public function testRoute()
    {
        $this->loginAs(User::get('ada'), ['url' => '/app/settings']);
        $this->waitCompletion();
        $this->waitUntilISee('.page.settings.profile');
    }

    /**
     * Scenario: As a user I should be able to search a password by keywords from the settings workspace
     *
     * Given I am logged in as Ada, and I go to the password workspace
     * When  I fill the "app search" field with "Betty"
     * And   I click "search"
     * Then  I should see the view filtered with my search
     * And   I should see the breadcrumb with the following:
     *                    | All items
     *                    | Search : shared resource
     *
     * @group LU
     * @group settings
     * @group settings-workspace
     * @group saucelabs
     * @group v2
     */
    public function testSearchByKeywords()
    {
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

        // And I am logged in on the user workspace
        $this->loginAs($user);

        // Go to user workspace
        $this->gotoWorkspace('settings');

        // I fill the "app search" field with "Betty"
        $this->inputText('js_app_filter_keywords', $searchUser);
        $this->click("#js_app_filter_form button[value='search']");
        $this->waitUntilISee('#js_passbolt_user_workspace_controller');
        $this->waitCompletion();

        // I should see the view filtered with my search
        $userBrowserSelector = '#js_wsp_users_browser .tableview-content';
        $userBrowser = $this->findByCss($userBrowserSelector);
        $this->waitUntilISee($userBrowserSelector, "/$searchUser/");
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
