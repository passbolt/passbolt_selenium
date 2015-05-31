<?php
/**
 * Feature : Setup
 * As an anonymous user, I need to be able to see the setup page with an invitation to install the plugin.
 *
 * @copyright 	(c) 2015-present Bolt Software Pvt. Ltd.
 * @licence			GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
 */
class SetupTest extends PassboltTestCase {

	/**
	 * Scenario:  I can see the setup page with instructions to install the plugin
	 * Given      I am an anonymous user with no plugin on the registration page
	 * And        I follow the registration process and click on submit
	 * And        I click on the link get started in the email I received
	 * Then       I should reach the setup page
	 * And        the url should look like resource://passbolt-firefox-addon-at-passbolt-dot-com/passbolt-firefox-addon/data/setup.html
	 * And        I should see the text "Nice one! Firefox plugin is installed and up to date. You are good to go!"
	 * And        I should see that the domain in the url check textbox is the same as the one configured.
	 */
	public function testCanSeeSetupPageWithFirstPluginSection() {
		// Reset passbolt installation.
		$reset = $this->PassboltServer->resetDatabase();
		if (!$reset) {
			$this->fail('Could not reset installation');
		}

		// Register John Doe as a user.
		$this->getUrl('register');
		$this->inputText('ProfileFirstName','John');
		$this->inputText('ProfileLastName','Doe');
		$this->inputText('UserUsername','johndoe@passbolt.com');
		$this->pressEnter();
		$this->assertCurrentUrl('register' . DS . 'thankyou');

		// Get last email.
		$this->getUrl('seleniumTests/showLastEmail/' . urlencode('johndoe@passbolt.com'));
		// Follow the link in the email.
		$this->followLink("get started");
		// Test that the url is the plugin one.
		$this->assertUrlMatch('/resource:\/\/passbolt-firefox-addon-at-passbolt-dot-com\/passbolt-firefox-addon\/data\/setup.html/');
		// Test that the plugin confirmation message is displayed.
		$this->assertElementContainsText("div.plugin-check-wrapper .plugin-check.success", "Nice one! Firefox plugin is installed and up to date. You are good to go!");
		// Test that the domain in the url check textbox is the same as the one configured.
		$domain = $this->findById("js_setup_domain")->getAttribute('value');
		$this->assertEquals(Config::read('passbolt.url'), $domain);
	}

	public function testCanFollowSetupWithDefaultSteps() {
		// Get last email.
		$this->getUrl('seleniumTests/showLastEmail/' . urlencode('johndoe@passbolt.com'));
		// Remember setup url. (We will use it later).
		$linkElement = $this->findLinkByText('get started');
		$setupUrl = $linkElement->getAttribute('href');
		// Go to url remembered above.
		$this->driver->get($setupUrl);

		// Test that button cancel is hidden.
		$this->assertElementHasClass('#js_setup_cancel_step', 'hidden');
		// Test that button Next is disabled.
		$this->assertElementHasClass('#js_setup_submit_step', 'disabled');
		// Check box domain check.
		$this->checkCheckbox('js_setup_domain_check');
		// Test that button Next is disabled.
		$this->assertElementHasNotClass('#js_setup_submit_step', 'disabled');

		// Click Next.
		$this->clickLink("Next");
		// Test that button Next is disabled.
		$this->assertElementHasClass('#js_setup_submit_step', 'processing');
		// Wait
		sleep(1);
		// TODO : implement wait till I see.
		// Test that the text corresponding to key section is set.
		$this->assertElementContainsText("h2#js_step_title", "Create a new key or import an existing one!");

	}
}