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
 * Feature : As a GM regarding the delete user feature.
 *
 * Scenarios :
 *  - As a GM I should receive a notification when a user who is part of one (or more) groups I manage is deleted.
 */
namespace Tests\GM\Base;

use App\Actions\ConfirmationDialogActionsTrait;
use App\Actions\UserActionsTrait;
use App\Actions\WorkspaceActionsTrait;
use App\Assertions\ConfirmationDialogAssertionsTrait;
use App\Assertions\WorkspaceAssertionsTrait;
use App\PassboltTestCase;
use Data\Fixtures\User;

class GMUserDeleteTest extends PassboltTestCase
{
    use ConfirmationDialogActionsTrait;
    use ConfirmationDialogAssertionsTrait;
    use UserActionsTrait;
    use WorkspaceActionsTrait;
    use WorkspaceAssertionsTrait;

    /**
     * Scenario: As a GM I should receive a notification when a user who is part of one (or more) groups I manage is deleted.
     *
     * Given I am logged in as an admin
     * And   I am on the users workspace
     * When  I delete a user
     * Then  I should see a success notification message
     * When  I access last email sent to one of the group manager
     * Then  I should see the expected email title
     * And   I should see the expected email content
     * When  I access last email sent to another group manager
     * Then  I should see the expected email title
     * And   I should see the expected email content
     *
     * @group GM
     * @group group
     * @group user
     * @group delete
     * @group broken
     */
    public function testDeleteUserEmailNotification() 
    {
        $this->resetDatabaseWhenComplete();

        // Given I am an administrator.
        $user = User::get('admin');

        // I am logged in as admin
        $this->loginAs($user);

        // And I am on the users workspace
        $this->gotoWorkspace('user');

        // When I delete a user
        $userU = User::get('ursula');
        $this->rightClickUser($userU['id']);
        $this->click('#js_user_browser_menu_delete a');
        $this->assertConfirmationDialog('Do you really want to delete user ?');
        $this->confirmActionInConfirmationDialog();

        // Then I should see a success notification message
        $this->assertNotification('app_users_delete_success');

        // When I access last email sent to one of the group manager
        $userP = User::get('ping');
        $this->getUrl('seleniumtests/showlastemail/' . $userP['Username']);

        // Then I should see the expected email title
        $this->assertMetaTitleContains(sprintf('%s deleted a user', $user['FirstName']));

        // And I should see the expected email content
        $this->assertElementContainsText('bodyTable', 'User: ' . $userU['FirstName'] . ' ' . $userU['LastName']);
        $this->assertElementContainsText('bodyTable', 'The user is now deleted on passbolt');
        $this->assertElementContainsText('bodyTable', 'IT support (Group manager)');
        $this->assertElementContainsText('bodyTable', 'Human resource (Member)');

        // When I access last email sent to another group manager
        $userT = User::get('thelma');
        $this->getUrl('seleniumtests/showlastemail/' . $userT['Username']);

        // Then I should see the expected email title
        $this->assertMetaTitleContains(sprintf('%s deleted a user', $user['FirstName']));

        // And I should see the expected email content
        $this->assertElementContainsText('bodyTable', 'User: ' . $userU['FirstName'] . ' ' . $userU['LastName']);
        $this->assertElementContainsText('bodyTable', 'The user is now deleted on passbolt');
        $this->assertElementContainsText('bodyTable', 'Human resource (Member)');
    }

}