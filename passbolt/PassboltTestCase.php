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

	protected function setUp() {
		parent::setUp();
		$this->PassboltServer = new PassboltServer(Config::read('passbolt.url'));
		$this->driver->manage()->window()->maximize();
	}

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
		$this->clickElement($linkCssSelector);
		$this->waitCompletion();
	}

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

	/**
	 * Use the debug screen to set the values set by the setup
	 * @param $config user config (see fixtures)
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
	 * Go to the password workspace and click on teh create password button
	 */
	public function gotoCreatePassword() {
		if(!$this->isVisible('.page.password')) {
			$this->getUrl('');
			$this->waitUntilISee('.page.password');
			$this->waitUntilISee('#js_wk_menu_creation_button');
		}
		$this->click('#js_wk_menu_creation_button');
		$this->assertTrue($this->isVisible('.create-password-dialog'));
	}

	/**
	 * Input a given string in the secret field
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

}
