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
 * Bug PASSBOLT-2060 - Regression test
 *
 * @copyright (c) 2015-present Bolt Softwares Pvt Ltd
 * @licence   GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
namespace Tests\GM\Regressions;

use App\Actions\GroupActionsTrait;
use App\Actions\MasterPasswordActionsTrait;
use App\Actions\PasswordActionsTrait;
use App\Actions\SidebarActionsTrait;
use App\Actions\WorkspaceActionsTrait;
use App\Assertions\GroupAssertionsTrait;
use App\Assertions\MasterPasswordAssertionsTrait;
use App\Assertions\WorkspaceAssertionsTrait;
use App\Lib\UuidFactory;
use App\PassboltSetupTestCase;
use Data\Fixtures\Group;
use Data\Fixtures\User;

class PASSBOLT2060 extends PassboltSetupTestCase
{
    use GroupActionsTrait;
    use GroupAssertionsTrait;
    use MasterPasswordActionsTrait;
    use MasterPasswordAssertionsTrait;
    use PasswordActionsTrait;
    use SidebarActionsTrait;
    use WorkspaceActionsTrait;
    use WorkspaceAssertionsTrait;

    /**
     * Scenario: As a user I should be able to edit a group from the right sidebar
     *
     * Given I register an account as John Doe
     * When  I complete the setup with a passphrase longer than 50 char
     * Then  I am able to login
     *
     * @group GM
     * @group group
     * @group edit
     * @group v2
     */
    public function testEditGroupFromSidebar()
    {
        $this->resetDatabaseWhenComplete();

        // Given I am logged in as a group manager
        $user = User::get('ping');
        
        $this->loginAs($user);

        // And I am on the users workspace
        $this->gotoWorkspace('user');

        // When I click a group name
        $group = Group::get(['id' => UuidFactory::uuid('group.id.it_support')]);
        $this->clickGroup($group['id']);

        // Then I should see a “edit” button next to the Information section
        $this->clickSecondarySidebarSectionHeader('members');
        $editButtonSelector = '#js_group_details #js_group_details_members #js_edit_members_button';
        $this->waitUntilISee($editButtonSelector);

        // When I press the “Edit” button
        $this->click($editButtonSelector);

        // Then I should see the Edit group dialog
        $this->waitUntilISee('.edit-group-dialog');

        // When I add a member to the group
        $ada = User::get('ada');
        $this->searchGroupUserToAdd($ada, $user);
        $this->addTemporaryGroupUser($ada);

        // Then I should see that the user is added in the list of group members
        // And I should see that his group role is “group member”
        $this->assertGroupMemberInEditDialog($group['id'], $ada);

        // And I should see a warning message saying that the changes will be applied after clicking on save
        $this->assertElementContainsText(
            $this->getTemporaryGroupUserElement($ada),
            'Will be added'
        );

        // When I press the save button
        $this->click('.edit-group-dialog a.button.primary');
        $this->assertMasterPasswordDialog($user);
        $this->enterMasterPassword($user['MasterPassword']);

        // Then I should see that the dialog disappears
        $this->waitUntilIDontSee('.edit-group-dialog');

        // And I should see a confirmation message saying that the group members have been edited
        $this->assertNotification('app_groups_edit_success');
    }
}