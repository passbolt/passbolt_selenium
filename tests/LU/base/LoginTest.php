<?php
/**
 * User with configured plugin login test
 *
 * @copyright (c) 2015-present Bolt Softwares Pvt Ltd
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class LoginTest extends PassboltTestCase {

    public function testLogin() {
        $this->getUrl('login');
	    sleep(1);
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
        $this->goOutOfIframe();

        $this->waitUntilISee('.login.form .feedback');
        $this->assertElementContainsText('.login.form .feedback','Logging in');
        $this->waitCompletion();

        // wait for redirection trigger
        sleep(1);
        $this->waitCompletion();

	    $this->assertElementContainsText(
		    $this->findByCss('.header .user.profile .details .name'),
		    'Ada Lovelace'
	    );
    }

	public function testLoginWithEnterKey() {
		$this->getUrl('login');
		sleep(1);
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

		$this->click('js_master_password');
		$this->typeTextLikeAUser($user['MasterPassword']);
		$this->pressEnter();

		$this->assertElementContainsText('loginMessage','Please wait');
		$this->goOutOfIframe();

		$this->waitUntilISee('.login.form .feedback');
		$this->assertElementContainsText('.login.form .feedback','Logging in');
		$this->waitCompletion();

		// wait for redirection trigger
		sleep(1);
		$this->waitCompletion();

		$this->assertElementContainsText(
			$this->findByCss('.header .user.profile .details .name'),
			'Ada Lovelace'
		);
	}

}