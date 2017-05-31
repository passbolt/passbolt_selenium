<?php
/**
 * Anonymous user with plugin but no config login test
 *
 * @copyright (c) 2017 Passbolt SARL
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class LoginTest extends PassboltTestCase {

	/**
	 * @group saucelabs
	 * Test that if not registered I should see a warning
	 * @throws Exception
	 */
    public function testLogin() {
        $this->getUrl('login');
        $this->waitUntIlISee('.plugin-check.' . $this->_browser['type'] . '.warning', null, 2);
	    $this->fail("yo man. it fails.");
    }

	/**
	 * @group saucelabs
	 * Test that if the wrong domain is configured, we will see a page explaining that
	 * the domain is not known.
	 * @throws Exception
	 */
	public function testWrongDomain() {
		$user = User::get('ada');
		$user['domain'] = 'https://custom.passbolt.com';
		$this->setClientConfig($user);

		$this->getUrl('login');
		$this->waitUntilISee('html.domain-unknown');
		$this->waitUntilISee('a.trusteddomain', '/https:\/\/custom\.passbolt\.com/');
	}

	/**
	 * Test that if the server verification failed, we will see a page explaining that
	 * something went wrong with a message explaining what happened
	 * @throws Exception
	 */
	public function testStage0VerifyError() {
		$user = User::get('ada');
		$this->setClientConfig($user);

		// Load a wrong public server key.
		$this->goToDebug();
		$key = file_get_contents(GPG_FIXTURES . DS . 'user_public.key');
		$this->inputText('serverKeyAscii', $key);
		$this->click('saveServerKey');
		$this->waitUntilISee('.server.key-import.feedback', '/The key has been imported successfully/');

		$this->getUrl('login');
		$this->waitUntilISee('html.server-not-verified');
		$this->assertElementContainsText('.plugin-check.gpg', 'Decryption failed');
	}

	/**
	 * Test that if the account doesn't exist on server, we get a proper feedback.
	 * @throws Exception
	 */
	public function testStage0VerifyNoAccount() {
		$user = User::get('john');
		$this->setClientConfig($user);
		$this->getUrl('login');
		$this->waitUntilISee('html.server-not-verified.server-no-user');
		$this->waitUntilISee('.plugin-check.gpg.error', '/There is no user associated with this key/');
		$this->waitUntilISee('.users.login.form .feedback', '/The supplied account does not exist/');
		$this->click('.users.login.form a.primary');
		$this->waitUntilISee('div.page.register');
	}

	/**
	 * Scenario: I can see the app version number and the plugin version number in the footer
	 * Given 	I am an anonymous user with plugin on the login page
	 * When		When the page is loaded
	 * Then 	I can see the app version number in the footer
	 * And      I can see the plugin version number
	 */
	public function testCanSeeVersionNumber() {
		$this->getUrl('login');

		$loginForm = null;

		try {
			$versionElt = $this->findByCss('#version a');
		} catch (NoSuchElementException $e) {
			$this->fail('No element wit id #version was found');
		}

		$callback = array($this, 'assertElementAttributeMatches');
		$reg_version = '[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2}(-RC[0-9]){0,1}';
		$reg = '/^' . $reg_version . ' \/ ' . $reg_version . '$/';
		$this->waitUntil($callback, array($versionElt, 'data-tooltip', $reg));
	}
}
