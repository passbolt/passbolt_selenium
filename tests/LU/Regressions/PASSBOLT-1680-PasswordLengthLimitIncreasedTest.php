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
 * Bug PASSBOLT-1680 - Regression test
 */
namespace Tests\LU\Regressions;

use App\Actions\MasterPasswordActionsTrait;
use App\Actions\PasswordActionsTrait;
use App\Assertions\ClipboardAssertions;
use App\Assertions\MasterPasswordAssertionsTrait;
use App\Assertions\PasswordAssertionsTrait;
use App\Assertions\WorkspaceAssertionsTrait;
use App\PassboltTestCase;
use Data\Fixtures\User;

class PASSBOLT1680 extends PassboltTestCase
{
    use MasterPasswordActionsTrait;
    use MasterPasswordAssertionsTrait;
    use PasswordActionsTrait;
    use PasswordAssertionsTrait;
    use WorkspaceAssertionsTrait;
    use ClipboardAssertions;

    /**
     * Scenario: As a user I can view a password I just created on my list of passwords
     *
     * Given I am Ada
     * And   I am logged in
     * When  I create a new password with a secret of 4096 characters
     * Then  I see the password I created in my password list
     * And   I see the secret match the entry I made
     *
     * @group LU
     * @group regression
     * @group v2
     */
    public function testCreatePasswordAndView() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Generate random secret
        $length = 4096;
        $secret = '';
        for($i = 0; $i < $length; $i++){
            $secret .= chr(rand(97, 122));
        }

        // Given I am Ada
        $user = User::get('ada');

        // And I am logged in
        $this->loginAs($user);

        // When I create a new password with a secret of 4096 characters
        $password = array(
            'name' => 'long_password_create',
            'username' => 'long_password_create_username',
            'password' => ''
        );
        $this->fillPasswordForm($password);
        $this->goIntoSecretIframe();
        $this->setElementValue('js_secret', $secret);
        $this->goOutOfIframe();
        $this->click('.create-password-dialog input[type=submit]');
        $this->assertNotification('app_resources_add_success');

        // Then I see the password I created in my password list
        $this->assertElementContainsText(
            $this->find('js_wsp_pwd_browser'), $password['name']
        );

        $this->click('js_wk_menu_secretcopy_button');
        $this->enterMasterPassword($user['MasterPassword']);
        $this->assertNotification('plugin_clipboard_copy_success');
        $this->assertClipboard($secret);
        $this->assertEquals(strlen($secret), $length);
    }

}
