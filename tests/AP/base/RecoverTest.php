<?php
/**
 * Feature : Recover
 *
 * Scenarios:
 * - As an AP with a non configured plugin, I should be able to recover my account
 * - As an AP with a configured plugin, on a wrong domain, I should be able to access the account recovery page
 * - As an AP with a plugin configured for a non existing user, I should be able to access the account recovery page
 * - As AP, I should see a thank you page after I start the recovery procedure
 * - As AP, I should receive a notification email after I start the recovery procedure
 *
 * @copyright (c) 2015-present Bolt Softwares Pvt Ltd
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */

class RecoverTest extends PassboltTestCase {

	/**
	 * Scenario:    As an AP with a non configured plugin, I should be able to recover my account
	 * Given    I am AP with a non configured plugin
	 * When     I go to login page
	 * Then     I should see a link to recover my account
	 * When     I click on the recover my account link
	 * Then     I should see the recovery my account page
	 */
	public function testRecoverFromLogin() {
		$this->getUrl('login');

		$this->waitUntilISee('.information', '/Almost there, please register!/');

		$this->waitUntilISee('.actions-wrapper', '/Have an account?/');
		$this->waitUntilISee('.information .message', '/recover your account/');

		$this->clickLink('Have an account?');
		$this->waitUntilUrlMatches('recover');
		$this->waitUntilISee('.information', '/Recover an existing account!/');
	}

	/**
	 * Scenario:    As an AP with a configured plugin, on a wrong domain, I should be able to access the account recovery page.
	 * Given    I am an AP with my plugin configured, but on a wrong passbolt domain
	 * When     I go to login page
	 * Then     I should see a page telling me that I am on the wrong domain
	 * And      I should see a link to recover my account
	 * When     I click on the recover my account link
	 * Then     I should access a page where I can start the recovery procedure
	 */
	public function testRecoverFromWrongDomain() {
		$user = User::get('ada');
		$user['domain'] = 'https://custom.passbolt.com';
		$this->setClientConfig($user);

		$this->getUrl('login');
		$this->waitUntilISee('html.domain-unknown');

		// Check that I can see the option to recover an existing account.
		$this->waitUntilISee('.actions-wrapper', '/or recover an existing account/');
		$this->waitUntilISee('.information', '/recover an existing account/');

		$this->clickLink('or recover an existing account');
		$this->waitUntilUrlMatches('recover');
		$this->waitUntilISee('.information', '/Recover an existing account!/');
	}

	/**
	 * Scenario:    As an AP with a plugin configured for a non existing user, I should be able to access the account recovery page
	 * Given    I am an AP with my plugin configured for a non existing user
	 * When     I go to login page
	 * Then     I should see a page telling me that the account doesn't exist
	 * And      I should see a link to recover my account
	 * When     I click on the recover my account link
	 * Then     I should access a page where I can start the recovery procedure
	 */
	public function testRecoverFromStage0VerifyNoAccountA() {
		$user = User::get('john');
		$this->setClientConfig($user);
		$this->getUrl('login');
		$this->waitUntilISee('html.server-not-verified.server-no-user');
		$this->waitUntilISee('.actions-wrapper', '/or recover an existing account/');
		$this->clickLink('or recover an existing account');
		$this->waitUntilUrlMatches('recover');
		$this->waitUntilISee('.information', '/Recover an existing account!/');
	}

	/**
	 * Scenario:    As AP, I shouldn't be able to start an account recovery procedure for a non existing user.
	 * Given    I am AP on the recover page
	 * When     I enter a non existing email in the username field
	 * And      I click on recover
	 * Then     I should see an error message saying that the email provided doesn't belong to an existing user
	 */
	public function testRecoverNonExistingUser() {
		$this->getUrl('recover');
		$this->inputText('UserUsername', 'idontexist@passbolt.com');
		$this->pressEnter();
		$this->waitUntilISee('.error-message', '/Email provided doesn\'t belong to an existing user/');
	}

	/**
	 * Scenario:    As AP, I should see a thank you page after I start the recovery procedure
	 * Given    I am Ada on the account recovery page
	 * When     I enter my email in the email field
	 * And      I click on recover my account
	 * Then     I should see a thank you page
	 */
	public function testRecoverThankYouPage() {
		$this->getUrl('recover');
		$this->inputText('UserUsername', 'ada@passbolt.com');
		$this->pressEnter();
		$this->waitUntilISee('.page.recover.thank-you');
		$this->waitUntilISee('.information', '/See you in your mailbox!/');
		$this->assertCurrentUrl('recover' . DS . 'thankyou');

		$this->resetDatabase();
	}

	/**
	 * Scenario:    As AP, I should receive a notification email after I start the recovery procedure
	 * Given    I am Ada on the account recovery page
	 * When     I enter my email in the email field
	 * And      I click on recover my account
	 * Then     I should see a thank you page
	 * When     I check the last email sent by passbolt to me
	 * Then     I should see a notification email with an invite to recover my account
	 */
	public function testRecoverEmailNotification() {
		$this->getUrl('recover');
		$this->inputText('UserUsername', 'ada@passbolt.com');
		$this->pressEnter();
		$this->waitUntilISee('.page.recover.thank-you');

		$this->getUrl('seleniumTests/showLastEmail/' . urlencode('ada@passbolt.com'));
		$this->waitUntilISee('emailBody', '/have initiated an account recovery/');
		$this->waitUntilISee('emailBody', '/ada@passbolt.com/');
		$this->waitUntilISee('emailBody', '/Welcome back Ada,/');
		$this->waitUntilISee('.buttonContent', '/recover your account/');

		$this->resetDatabase();
	}
}