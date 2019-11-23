<?php
/**
 * Passbolt ~ Open source password manager for teams
 * Copyright (c) 2019 Passbolt SA (https://www.passbolt.com)
 *
 * Licensed under GNU Affero General Public License version 3 of the or any later version.
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) 2019 Passbolt SA (https://www.passbolt.com)
 * @license       https://opensource.org/licenses/AGPL-3.0 AGPL License
 * @link          https://www.passbolt.com Passbolt(tm)
 * @since         2.0.0
 */
namespace Tests\LU\Base;

use App\Actions\PasswordActionsTrait;
use App\Actions\WorkspaceActionsTrait;
use App\Assertions\PasswordAssertionsTrait;
use App\Assertions\WorkspaceAssertionsTrait;
use App\PassboltTestCase;
use Data\Fixtures\User;

class PasswordCreateTest extends PassboltTestCase
{
    use WorkspaceActionsTrait;
    use WorkspaceAssertionsTrait;
    use PasswordAssertionsTrait;
    use PasswordActionsTrait;

    /**
     * Scenario: As a logged in user I can view the create password dialog
     *
     * Given I am Ada
     * And   I am logged in
     * And   I am on the create password dialog
     * When  I enter 'localhost ftp' as the name
     * And   I enter 'test' as the username
     * And   I enter 'ftp://passbolt.com' as the uri
     * And   I enter 'localhost ftp test account' as the description
     * And   I enter 'ftp-password-test' as password
     * And   I click on the save button
     * Then  I should see the passphrase entry dialog.
     * And   I enter 'ada@passbolt.com' as password
     * When  I click on the OK button
     * Then  I see a notice message that the operation was a success
     * And   I see the password I created in my password list
     * When  I access last email sent to Ada.
     * Then  I should an email that has been sent to me with the title: You added the password selenium name password
     * And   I should see the password details
     *
     * @group LU
     * @group password
     * @group password-create
     * @group v2
     */
    public function testCreatePassword()
    {
        // Given I am Ada
        $user = User::get('ada');

        // I am logged in as Carol, and I go to the user workspace
        $this->loginAs($user);

        // Then I see the create password button
        $this->assertElementContainsText(
            $this->find('.main-action-wrapper'), 'create'
        );

        // When I click on create button
        $this->click('js_wsp_create_button');

        // Then I see the create password dialog
        $this->goIntoReactAppIframe();
        $this->assertVisibleByCss('.create-password-dialog');

        // And I see the title is set to "create password"
        $this->assertElementContainsText(
            $this->find('.create-password-dialog.dialog'), 'Create a password'
        );

        // And I enter 'localhost ftp' as the name
        $this->inputText('.create-password-dialog input[name="name"]', 'selenium name password');
        
        // And I enter 'test' as the username
        $this->inputText('.create-password-dialog input[name="username"]', 'selenium username password');

        // And I enter 'ftp://localhost' as the uri
        $this->inputText('.create-password-dialog input[name="uri"]', 'ftp://selenium.passbolt.com');

        // I enter 'ftp-password-test' as password
        $this->inputText('.create-password-dialog input[name="password"]', 'azerty');

        // And I enter 'localhost ftp test account' as the description
        $this->inputText('.create-password-dialog textarea[name="description"]', 'selenium description password');

        // When I click on the save button
        $this->click('.create-password-dialog input[type=submit]');

        // Then I should see the passphrase entry dialog.
        $this->waitUntilISee('.dialog.passphrase-entry');

        // And I enter 'ada@passbolt.com' as password
        $this->inputText('.passphrase-entry input[name="passphrase"]', 'ada@passbolt.com');

        // When I click on the OK button
        $this->click('.passphrase-entry input[type=submit]');
        $this->goOutOfIframe();

        // I see a notice message that the operation was a success
        $this->assertNotificationMessage('The password has been added successfully');

        // I see the password I created in my password list
        $grid = $this->find('js_wsp_pwd_browser');
        $this->assertElementContainsText($grid, 'selenium name password');
        $this->assertElementContainsText($grid, 'selenium username password');
        $this->assertElementContainsText($grid, 'ftp://selenium.passbolt.com');

        // When I access last email sent to Ada.
        $this->getUrl('seleniumtests/showlastemail/' . $user['Username']);

        // Then I should an email that has been sent to me with the title: You added the password selenium name password
        $this->assertMetaTitleContains('You added the password selenium name password');

        // And I should see the password details
        $bodyTable = $this->find('bodyTable');
        $this->assertElementContainsText($bodyTable, 'You have saved a new password');
        $this->assertElementContainsText($bodyTable, 'selenium name password');
        $this->assertElementContainsText($bodyTable, 'selenium username password');
        $this->assertElementContainsText($bodyTable, 'ftp://selenium.passbolt.com');
        $this->assertElementContainsText($bodyTable, 'selenium description password');
    }
}