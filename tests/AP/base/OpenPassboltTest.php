<?php

/**
 * Anonymous user with plugin but no config open passbolt test
 *
 * @copyright 	(c) 2015-present Bolt Software Pvt. Ltd.
 * @licence			GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
 */
class OpenPassboltTest extends PassboltSetupTestCase {

	/**
	 * Scenario :   As an AP with a plugin installed but not configured, clicking on the passbolt toolbar icon or using
	 * 				the passbolt shortcut, I should open the passbolt public page "getting started" in a new tab
	 * Given 		Wherever I am on the web
	 * And			The passbolt plugin is installed
	 * And			The passbolt plugin is not configured
	 * When			I click on the passbolt toolbar icon or I compose the passbolt shortcut
	 * Then			I should reach the passbolt public page "getting started" in a new tab
	 */
	public function testOpenPassboltNoConfig() {
		$this->findByCss('body')->sendKeys(array(WebDriverKeys::CONTROL, WebDriverKeys::SHIFT, WebDriverKeys::ALT, 'p'));
		sleep(1);
		$this->assertEquals('https://www.passbolt.com/start', $this->driver->getCurrentURL());
	}

	/**
	 * Scenario :   As an AP with a plugin installed and partially configured, clicking on the passbolt toolbar icon or using
	 * 				the passbolt shortcut, I should open the setup in a new tab
	 * Given 		Wherever I am on the web
	 * And			The passbolt plugin is installed
	 * And			The passbolt plugin is partially configured
	 * When			I click on the passbolt toolbar icon or I compose the passbolt shortcut
	 * Then			I should reach the setup in a new tab
	 */
	public function testOpenPassboltPartialConfig() {
		$key = Gpgkey::get(['name' => 'johndoe']);

		// Register John Doe as a user.
		$this->registerUser('John', 'Doe', $key['owner_email']);

		// Go to setup page.
		$this->goToSetup($key['owner_email']);

		// Simulate click on the passbolt toolbar icon
		$this->findByCss('body')->sendKeys(array(WebDriverKeys::CONTROL, WebDriverKeys::SHIFT, WebDriverKeys::ALT, 'p'));
		sleep(1);

		// Test that the url is the plugin one.
		$this->assertUrlMatch('/resource:\/\/passbolt-at-passbolt-dot-com\/data\/setup.html/');

		// Since content was edited, we reset the database
		$this->resetDatabase();
	}

	/**
	 * Scenario :   As an AP with a plugin installed and configured, clicking on the passbolt toolbar icon or using
	 * 				the passbolt shortcut, I should open the passbolt application in a new tab
	 * Given 		Wherever I am on the web
	 * And			The passbolt plugin is installed
	 * And			The passbolt plugin is partially configured
	 * When			I click on the passbolt toolbar icon or I compose the passbolt shortcut
	 * Then			I should reach the passbolt application in a new tab
	 */
	public function testOpenPassboltConfig() {
		$key = Gpgkey::get(['name' => 'johndoe']);

		// Register John Doe as a user.
		$this->registerUser('John', 'Doe', $key['owner_email']);

		// Go to setup page.
		$this->goToSetup($key['owner_email']);
		$this->completeRegistration();

		// Simulate click on the passbolt toolbar icon
		$this->findByCss('body')->sendKeys(array(WebDriverKeys::CONTROL, WebDriverKeys::SHIFT, WebDriverKeys::ALT, 'p'));
		sleep(1);

		// I should be on the login page.
		$this->waitUntilISee('.information h2', '/Welcome back!/');

		// Since content was edited, we reset the database
		$this->resetDatabase();
	}

}