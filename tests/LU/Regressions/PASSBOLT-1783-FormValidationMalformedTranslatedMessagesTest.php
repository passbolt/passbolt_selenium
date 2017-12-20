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
 * Bug PASSBOLT-1783 - Regression test
 */
namespace Tests\LU\Regressions;

use App\Actions\UserActionsTrait;
use App\Actions\WorkspaceActionsTrait;
use App\PassboltTestCase;
use Data\Fixtures\User;

class PASSBOLT1783 extends PassboltTestCase
{
    use WorkspaceActionsTrait;
    use UserActionsTrait;

    /**
     * Scenario: After creating a user, the given user can complete the setup and login with the chosen password
     *
     * Given I am admin
     * And   I am logged in
     * When  I go to user workspace
     * And   I create a user with a first name of 1 character
     * Then  I should a well formed error message
     *
     * @group LU
     * @group regression
     */
    public function testCreateUserWrongDataWellformedErrorFeedback() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Ada
        $user = User::get('admin');

        // And I am logged in
        $this->loginAs($user);

        // When Go to user workspace
        $this->gotoWorkspace('user');

        // And I create a user with a first name of 1 character
        $this->gotoCreateUser();
        $this->inputText('js_field_first_name', 'a');
        $this->inputText('js_field_last_name', 'a');
        $this->inputText('js_field_username', 'a');
        if (isset($user['admin']) && $user['admin'] === true) {
            // Check box admin
            $this->checkCheckbox('js_field_role_id');
        }
        $this->click('.create-user-dialog input[type=submit]');

        // Then I should a well formed error message
        $this->assertElementContainsText(
            $this->find('js_field_first_name_feedback'),
            'First name should be between 2 and 64 characters long'
        );
    }

}