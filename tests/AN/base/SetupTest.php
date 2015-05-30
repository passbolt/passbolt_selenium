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
	 * And        the url should look like /setup/install/5569df1d-7bec-4c0c-a09d-55e2c0a895dc/d45c0bf1e00fb8db60af1e8b5482f9f3
	 * And        I should see the text "Welcome to passbolt! Let's take 5 min to setup your system."
	 * And        I should see the text "An add-on is required to use Passbolt."
	 * And        I should see that the second menu item on the left is deactivated
	 */
	public function testCanSeeSetupPage() {
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
		$this->assertUrlMatch('/\/setup\/install\/[a-z0-9\-]{36}\/[a-z0-9]{32}/');
		$this->assertPageContainsText("Welcome to passbolt! Let's take 5 min to setup your system.");
		$this->assertElementContainsText("div.plugin-check-wrapper .plugin-check.error", "An add-on is required to use Passbolt.");
		// Assert that second element in menu is disabled.
		$this->assertPageContainsElement("div.navigation ul li:nth-child(2).disabled");
	}

	/**
	 * Scenario :   I cannot see the setup page if user id and token are incorrect.
	 * Given        I try to access the setup page with wrong information in the url
	 * Then         I should reach an error page with text "Token not found"
	 */
	public function testCannotSeeSetupPageWithInvalidInformation() {
		$reset = $this->PassboltServer->resetDatabase();
		if (!$reset) {
			$this->fail('Could not reset installation');
		}
		// Access url with wrong user id and token.
		$this->getUrl('setup/install/5569df1d-7bec-4c0c-a09d-55e2c0a895dc/d45c0bf1e00fb8db60af1e8b5482f9f3');
		$this->assertPageContainsText("Token not found");
	}
}