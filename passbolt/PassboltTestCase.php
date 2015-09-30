<?php
/**
 * Passbolt Test Case
 * The base class for test cases related to passbolt.
 *
 * @copyright 	(c) 2015-present Bolt Software Pvt. Ltd.
 * @licence			GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
 */
class PassboltTestCase extends WebDriverTestCase {

	// PassboltServer.
	protected $PassboltServer = null;
	protected $resetDatabase = false;

	/**
	 * Executed before every tests
	 */
	protected function setUp() {
		parent::setUp();
		$this->PassboltServer = new PassboltServer(Config::read('passbolt.url'));
		$this->driver->manage()->window()->maximize();
		if ($this->resetDatabase) {
			$this->PassboltServer->resetDatabase();
		}
	}

	/********************************************************************************
	 * Passbolt Application Helpers
	 ********************************************************************************/
	/**
	 * Goto a given url
	 * @param $url
	 */
	public function getUrl($url=null) {
		$url = Config::read('passbolt.url') . DS . $url;
		$this->driver->get($url);
	}

	/**
	 * Goto workspace
	 * @param $name
	 */
	public function gotoWorkspace($name) {
		$linkCssSelector = '';
		switch ($name) {
			default:
				$linkCssSelector = '#js_app_nav_left_' . $name . '_wsp_link a';
				break;
		}
		$this->click($linkCssSelector);
		$this->waitCompletion();
	}

	/**
	 * Wait until all the currently operations have been completed.
	 * @param int timeout timeout in seconds
	 * @return bool
	 * @throws Exception
	 */
	public function waitCompletion($timeout = 10) {
		$ex = null;

		for ($i = 0; $i < $timeout * 10; $i++) {
			try {
				$elt = $this->findByCss('html.loaded');
				if(count($elt)) {
					return true;
				}
			}
			catch (Exception $e) {
				$ex = $e;
			}
			usleep(100000); // Sleep 1/10 seconds
		}

		$backtrace = debug_backtrace();
		throw new Exception( "Timeout thrown by " . $backtrace[1]['class'] . "::" . $backtrace[1]['function'] . "()\n .");
	}

	/**
	 * Login on the application with the given user.
	 * @param $email
	 */
	public function loginAs($email) {
		$this->getUrl('login');
		$this->inputText('UserUsername', $email);
		$this->inputText('UserPassword', 'password');
		$this->pressEnter();
		$this->waitCompletion();
	}

	public function logout() {
		$this->getUrl('logout');
	}

	/**
	 * Use the debug screen to set the values set by the setup
	 * @param $config array user config (see fixtures)
	 */
	public function setClientConfig($config) {
		$this->getUrl('debug');
		sleep(1); // plugin need some time to trigger a page change

		$this->inputText('ProfileFirstName',$config['FirstName']);
		$this->inputText('ProfileLastName',$config['LastName']);
		$this->inputText('UserUsername',$config['Username']);
		$this->inputText('securityTokenCode',$config['TokenCode']);
		$this->inputText('securityTokenColor',$config['TokenColor']);
		$this->inputText('securityTokenTextColor',$config['TokenTextColor']);
		$this->click('js_save_conf');

		$key = file_get_contents(GPG_FIXTURES . DS . $config['PrivateKey'] );
		$this->inputText('keyAscii',$key);
		$this->click('saveKey');
	}

	/**
	 * Go to the password workspace and click on the create password button
	 */
	public function gotoCreatePassword() {
		if(!$this->isVisible('.page.password')) {
			$this->getUrl('');
			$this->waitUntilISee('.page.password');
			$this->waitUntilISee('#js_wk_menu_creation_button');
		}
		$this->click('#js_wk_menu_creation_button');
		$this->assertVisible('.create-password-dialog');
	}

	/**
	 * Goto the edit password dialog for a given resource id
	 * @param $id string
	 * @throws Exception
	 */
	public function gotoEditPassword($id) {
		if(!$this->isVisible('.page.password')) {
			$this->getUrl('');
			$this->waitUntilISee('.page.password');
			$this->waitUntilISee('#js_wk_menu_edition_button');
		}
		$this->click('footer'); // we click somewhere in case the password is already active
		$this->click($id);
		$this->click('js_wk_menu_edition_button');
		$this->waitCompletion();
		$this->assertVisible('.edit-password-dialog');
	}

	/**
	 * Input a given string in the secret field (create only)
	 * @param string $secret
	 */
	public function inputSecret($secret) {
		$this->goIntoSecretIframe();
		$this->inputText('js_secret', $secret);
		$this->goOutOfIframe();
	}

	/**
	 * Put the focus inside the secret iframe
	 */
	public function goIntoSecretIframe() {
		$this->driver->switchTo()->frame('passbolt-iframe-secret-edition');
	}

	/**
	 * Put the focus back to the normal context
	 */
	public function goOutOfIframe() {
		$this->driver->switchTo()->defaultContent();
	}

	/**
	 * Dig into the master password iframe
	 */
	public function goIntoMasterPasswordIframe() {
		$this->driver->switchTo()->frame('passbolt-iframe-master-password');
	}

	/**
	 * Helper to create a password
	 */
	public function createPassword($password) {
		$this->gotoCreatePassword();
		$this->inputText('js_field_name', $password['name']);
		$this->inputText('js_field_username', $password['username']);
		if (isset($password['uri'])) {
			$this->inputText('js_field_uri', $password['uri']);
		}
		$this->inputSecret($password['password']);
		if (isset($password['description'])) {
			$this->inputText('js_field_description', $password['description']);
		}
		$this->click('.create-password-dialog input[type=submit]');
		$this->waitUntilISee('.notification-container', '/successfully saved/i');
	}

	/**
	 * Edit a password helper
	 * @param $password
	 * @throws Exception
	 */
	public function editPassword($password) {
		$this->gotoEditPassword($password['id']);

		if (isset($password['name'])) {
			$this->inputText('js_field_name', $password['name']);
		}
		if (isset($password['username'])) {
			$this->inputText('js_field_username', $password['username']);
		}
		if (isset($password['uri'])) {
			$this->inputText('js_field_uri', $password['uri']);
		}
		if (isset($password['password'])) {
			$this->inputSecret($password['password']);
		}
		if (isset($password['description'])) {
			$this->inputText('js_field_description', $password['description']);
		}
		$this->click('.edit-password-dialog input[type=submit]');
		$this->assertNotification('app_resources_edit_success');
	}

	/**
	 * Enter the password in the master password iframe
	 * @param $pwd
	 */
	public function enterMasterPassword($pwd) {
		$this->goIntoMasterPasswordIframe();
		$this->inputText('js_master_password', $pwd);
		$this->click('master-password-submit');
		$this->goOutOfIframe();
	}

	/**
	 * Copy a password to clipboard
	 * @param $resource
	 * @param $user
	 */
	public function copyToClipboard($resource, $user) {
		$this->rightClick($resource['id']);
		$this->clickLink('Copy password');
		$this->assertMasterPasswordDialog($user);
		$this->enterMasterPassword($user['MasterPassword']);
	}

	/********************************************************************************
	 * Passbolt Application Asserts
	 ********************************************************************************/
	/**
	 * Check if the current url match the one given in parameter
	 * @param $url
	 */
	public function assertCurrentUrl($url) {
		$url = Config::read('passbolt.url') . DS . $url;
		$this->assertEquals($url, $this->driver->getCurrentURL());
	}

	/**
	 * Check if the given role is matching the one advertised on the app side
	 * @param $role
	 */
	public function assertCurrentRole($role) {
		try {
			$e = $this->findByCSS('html.' . $role);
			if(count($e)) {
				$this->assertTrue(true);
			} else {
				$this->fail('The current user role is not ' . $role);
			}
		} catch (NoSuchElementException $e) {
			$this->fail('The current user role is not ' . $role);
		}
	}

	/**
	 * Check that there is no plugin
	 */
	public function assertNoPlugin() {
		try {
			$e = $this->findByCSS('html.no-passboltplugin');
			$this->assertTrue(count($e) === 1);
		} catch (NoSuchElementException $e) {
			$this->fail('A passbolt plugin was found');
		}
	}

	/**
	 * Check that there is a plugin
	 */
	public function assertPlugin() {
		try {
			$e = $this->findByCSS('html.passboltplugin');
			$this->assertTrue(count($e) === 1);
		} catch (NoSuchElementException $e) {
			$this->fail('A passbolt plugin was not found');
		}
	}

	/**
	 * Check that there is a plugin
	 */
	public function assertNoPluginConfig() {
		try {
			$e = $this->findByCSS('html.passboltplugin.no-passboltconfig');
			$this->assertTrue(count($e) === 0);
		} catch (NoSuchElementException $e) {
			$this->assertTrue(true);
		}
	}

	/**
	 * Check that there is a plugin with a config set
	 */
	public function assertPluginConfig() {
		try {
			$e = $this->findByCSS('html.passboltplugin-config');
			$this->assertTrue((isset($e)));
		} catch (NoSuchElementException $e) {
			$this->fail('Passbolt plugin config html header not found');
		}
	}

	/**
	 * Check that the breadcumb contains the given crumbs
	 * @param $wspName The workspace name
	 * @param $crumbs The crumbs to check
	 */
	public function assertBreadcrumb($wspName, $crumbs) {
		// Find the breadcrumb element.
		$breadcrumbElement = $this->findById('js_wsp_' . $wspName . '_breadcrumb');
		// Check that the breadcrumb element contains the given crumbs.
		for ($i=0; $i< count($crumbs); $i++) {
			$this->assertElementContainsText(
				$breadcrumbElement,
				$crumbs[$i]
			);
		}
	}

	/**
	 * Check if a notification is displayed
	 * @see in passbolt/app/webroot/js/app/config/notification.json for notification uuid seed
	 * 		example: Uuid::get('app_resources_index_success') is how to create the id from the seed
	 * @param $notificationId
	 * @param string $msg
	 */
	public function assertNotification($notificationId, $msg = null) {
		$notificationId = 'notification_' . Uuid::get($notificationId);
		$this->waitUntilISee($notificationId);
		$text = $this->find($notificationId)->getText();
		if (isset($msg)) {
			$contain = false;
			if(preg_match('/^\/.+\/[a-z]*$/i', $msg)) {
				$contain = preg_match($msg, $text) != false;
			} else {
				$contain = strpos($text, $msg) !== false;
			}
			$this->assertTrue(($contain !== false), 'fail to find the notification message ' . $msg);
		}
	}

	/**
	 * Assert if a security token match user parameters
	 * @param $user array see fixtures
	 * @param $context where is the security token (master or else)
	 */
	public function assertSecurityToken($user, $context = null)
	{
		$this->assertVisible('.security-token');

		// check base color
		$t = $this->findByCss('.security-token');
		$this->assertElementContainsText($t, $user['TokenCode']);
		$this->assertEquals(Color::toHex($t->getCssValue("background-color")), $user['TokenColor']);
		$this->assertEquals(Color::toHex($t->getCssValue("color")), $user['TokenTextColor']);

		// check color switch when input is selected
		if (isset($context) && $context == 'master') {
			$this->click('js_master_password');
		} else {
			$this->click('js_secret');
		}
		$t = $this->findByCss('.security-token');
		$this->assertEquals(Color::toHex($t->getCssValue("background-color")), $user['TokenTextColor']);
		$this->assertEquals(Color::toHex($t->getCssValue("color")), $user['TokenColor']);

		// back to normal
		$this->click('.security-token');
	}

	/**
	 * Check if the complexity indicators match a given strength (creation/edition context)
	 * @param $strength string
	 */
	public function assertComplexity($strength) {
		$class = str_replace(' ','_',$strength);
		$this->assertVisible('#js_secret_strength .progress-bar.'.$class);
		$this->assertVisible('#js_secret_strength .complexity-text');
		$this->assertElementContainsText('#js_secret_strength .complexity-text', 'complexity: '.$strength);
	}

	/**
	 * Check if the master password dialog is working as expected
	 */
	public function assertMasterPasswordDialog($user) {
		// Get out of the previous iframe in case we are in one
		$this->goOutOfIframe();
		// Given I can see the iframe
		$this->waitUntilISee('passbolt-iframe-master-password');
		// When I can go into the iframe
		$this->goIntoMasterPasswordIframe();
		// Then I can see the security token is valid
		$this->assertSecurityToken($user, 'master');
		// Then I can see the title
		$this->assertElementContainsText('.master-password.dialog','Please enter your master password');
		// Then I can see the close dialog button
		$this->assertVisible('a.dialog-close');
		// Then I can see the OK button
		$this->assertVisible('master-password-submit');
		// Then I can see the cancel button
		$this->assertVisible('a.js-dialog-close.cancel');
		// Then I go out of the iframe
		$this->goOutOfIframe();
	}

	/**
	 * Assert that the content content of the clipboard match what is given
	 * @param $content
	 */
	public function assertClipboard($content) {
		// trick: we copy the content in the search field
		// and check its content match the content given
		$e = $this->findById('js_app_filter_keywords');
		$e->click();
		$action = new WebDriverActions($this->driver);
		$action->sendKeys($e, array(WebDriverKeys::CONTROL,'v'))->perform();
		$this->assertTrue($e->getAttribute('value') == $content);
	}
}
