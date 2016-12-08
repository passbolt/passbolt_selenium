<?php
/**
 * Bug PASSBOLT-1680 - Regression test
 *
 * @copyright (c) 2015-present Bolt Softwares Pvt Ltd
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class PASSBOLT1680 extends PassboltTestCase {

    /**
     * Scenario: As a user I can view a password I just created on my list of passwords
     *
     * Given    I am Ada
     * And      I am logged in
     * When     I create a new password with a secret of 4096 characters
     * Then     I see the password I created in my password list
     * And      I see the secret match the entry I made
     */
    public function testCreatePasswordAndView() {
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
        $this->setClientConfig($user);

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

        // And I see the password secret match the secret I entered
        $elt = $this->find('#js_wsp_pwd_browser div[title="' . $password['name'] . '"]');
        $elt->click();
        $this->click('js_wk_menu_secretcopy_button');
        $this->enterMasterPassword($user['MasterPassword']);
        $this->assertNotification('plugin_secret_copy_success');
        $this->assertClipboard($secret);
        $this->assertEquals(strlen($secret), $length);
    }

}
