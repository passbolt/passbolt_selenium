<?php
/**
 * Feature :  As a Developer use the debug screen of the addon to do a quick client setup
 *
 * @copyright    (c) 2015-present Bolt Software Pvt. Ltd.
 * @licence      GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
 */
class PluginDebugPageTest extends PassboltTestCase {

	/**
	 * Scenario: As a Developer use the debug screen to do a quick client setup
	 *
	 * Given	I am an anonymous user with the plugin installed
	 * And		I am on the login page
	 * And		I can see a message telling me I do not have the passbolt plugin configured
	 * When 	I go to the debug page
	 * And		I enter Ada information
	 * And		I enter enter the security token information
	 * And		I click save
	 * And		I upload Ada private key
	 * And		I click save key
	 * And		I go to the login page
	 * Then		I can see a message telling me the plugin is configured
	 */
	public function testSetDebug() {
		$this->getUrl();
		$this->assertCurrentRole('guest');
		$this->assertPlugin();
		$this->assertNoPluginConfig();

		$this->getUrl('debug');

		sleep(2); // plugin need some time to trigger a page change

		try {
			$this->findByCss('.page.debug.plugin');
		} catch (NoSuchElementException $e) {
			$this->fail('Page debug config not found');
		}

		$this->inputText('ProfileFirstName','Ada');
		$this->inputText('ProfileLastName','Lovelace');
		$this->inputText('UserUsername','ada@passbolt.com');

		$this->inputText('securityTokenCode','ADA');
		$this->inputText('securityTokenColor','#ff3a3a');
		$this->inputText('securityTokenTextColor','#ffffff');

		$this->click('js_save_conf');

		$key = file_get_contents(GPG_FIXTURES . DS . 'ada_private.key' );

		$this->inputText('keyAscii',$key);
		$this->click('saveKey');

		$this->getUrl('login');
		$this->assertPluginConfig();
	}
}