<?php
/**
 * User with configured plugin login test
 *
 * @copyright 	(c) 2015-present Bolt Software Pvt. Ltd.
 * @licence			GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
 */
class LoginTest extends PassboltTestCase {

    public function testLogin() {
        $this->getUrl('login');
        $this->assertVisible('.plugin-check.firefox.warning');

        $user = User::get('ada');
        $this->setClientConfig($user);

        $this->getUrl('login');

        $this->waitUntilISee('.plugin-check.firefox.success');
        $this->waitUntilISee('.plugin-check.gpg.success');

        $this->assertVisible('passbolt-iframe-login-form');
        $this->goIntoLoginIframe();

        $this->assertVisible('.login-form.master-password');
        $this->assertInputValue('UserUsername', $user['Username']);

        $this->inputText('js_master_password', 'somethingwrong');
        $this->click('loginSubmit');

        $this->waitUntilISee('#loginMessage.error');
        $this->inputText('js_master_password', $user['MasterPassword']);

        $this->click('loginSubmit');
        $this->assertElementContainsText('loginMessage','Please wait');
        $this->waitCompletion();
        $this->assertElementContainsText('loginMessage','You are now logged in!');

        // wait for redirection trigger
        sleep(1);
        $this->waitCompletion();
    }

}