<?php
/**
 * Feature : As AN on the login or setup stage 0 page I can download the plugin
 *
 * @copyright (c) 2015-present Bolt Softwares Pvt Ltd
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class PASSBOLT1380_Login extends PassboltSetupTestCase {

	/**
	 * @group firefox-only
	 *
	 * Scenario: As AN on the login page I can download the plugin
	 * Given	I am an AN on the login page
	 * Then		I can see a block saying that I need an addon
	 * Then		I can see "Download it here" link in the plugin check box
	 * When		I click on the link
	 * Then		I am redirected to https://addons.mozilla.org
	 */
	public function testFirefoxDownloadLinkOnLogin() {
		$this->getUrl('/');
		$this->assertTitleContain('Login');
		$this->assertVisible('.plugin-check.firefox.error');
		$this->followLink('Download it here');
		$this->waitUntilUrlMatches('https://addons.mozilla.org/en-US/firefox/addon/passbolt/', false);
		$this->assertTitleContain('Add-ons for Firefox');
	}

	/**
	 * @group firefox-only
	 *
	 * Scenario: As AN on the stage 0 of the setup I can download the plugin
	 * Given	I am an AN on the login page
	 * When		I register as John Doe
	 * And		I follow the link to the setup in confirmation email
	 * Then		I can see a block saying that I need an addon
	 * Then		I can see "Download it here" link in the plugin check box
	 * When		I click on the link
	 * Then		I am redirected to https://addons.mozilla.org
	 */
	public function testFirefoxDownloadLinkOnSetup() {
		// Reset database at the end of test.
		$this->resetDatabaseWhenComplete();

		// Register John Doe as a user.
		$this->registerUser('John', 'Doe', 'johndoe@passbolt.com');

		// Go to setup page and register
		$this->goToSetup('johndoe@passbolt.com', false);

		// Then I can see a block saying that I need an addon
		$this->assertVisible('.plugin-check.firefox.error');

		// ThenI can see "Download it here" link in the plugin check box
		// When I click on the link
		$this->followLink('Download it here');

		// Then I am redirected to https://addons.mozilla.org
		$this->waitUntilUrlMatches('https://addons.mozilla.org/en-US/firefox/addon/passbolt/', false);
		$this->assertTitleContain('Add-ons for Firefox');
	}
}