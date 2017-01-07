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
 * - As AP, I should be able to recover my account and log in.
 * - As AP, I should not be able to recover my account with a key that doesn't exist on server
 *
 * @copyright (c) 2015-present Bolt Softwares Pvt Ltd
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */

class RecoverTest extends PassboltSetupTestCase {

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
		$this->waitUntilISee('.error.message', '/This user does not exist/');
	}

	/**
	 * Scenario:    As AP, I shouldn't be able to start an account recovery procedure for a user who didn't complete setup
	 * Given    I am AP on the recover page
	 * When     I enter the email of a user that registered but did not complete setup in the username field
	 * And      I click on recover
	 * Then     I should see an error message saying that I should complete the setup first
	 */
	public function testRecoverNonActiveUser() {
		$this->getUrl('recover');
		$this->inputText('UserUsername', 'orna@passbolt.com');
		$this->pressEnter();
		$this->waitUntilISee('.error.message', '/Please complete the setup first/');
	}

	/**
	 * Scenario:    As AP, I should see a thank you page after I start the recovery procedure
	 * Given    I am Ada on the account recovery page
	 * When     I enter my email in the email field
	 * And      I click on recover my account
	 * Then     I should see a thank you page
	 */
	public function testRecoverThankYouPage() {
		// Reset database at the end of test.
		$this->resetDatabaseWhenComplete();

		// Go to recover start page.
		$this->getUrl('recover');

		// Enter Ada's email.
		$this->inputText('UserUsername', 'ada@passbolt.com');

		// Submit form.
		$this->pressEnter();

		// I should see a thank you page.
		$this->waitUntilISee('.page.recover.thank-you');
		$this->waitUntilISee('.information', '/See you in your mailbox!/');
		$this->assertCurrentUrl('recover' . DS . 'thankyou');
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
		// Reset database at the end of test.
		$this->resetDatabaseWhenComplete();

		// Go to recover start page.
		$this->getUrl('recover');

		// Enter Ada's email.
		$this->inputText('UserUsername', 'ada@passbolt.com');

		// Submit form.
		$this->pressEnter();

		// I should see a thank you page.
		$this->waitUntilISee('.page.recover.thank-you');

		// I should receive a recovery email.
		$this->getUrl('seleniumTests/showLastEmail/' . urlencode('ada@passbolt.com'));

		$this->waitUntilISee('emailBody', '/have initiated an account recovery/');
		$this->waitUntilISee('emailBody', '/ada@passbolt.com/');
		$this->waitUntilISee('emailBody', '/Welcome back Ada,/');
		$this->waitUntilISee('.buttonContent', '/start recovery/');
	}

	/**
	 * Scenario:    As AP, I should be able to recover my account and log in.
	 * Given    I am Ada
	 * When     start a recovery procedure, and click on the link provided in the email
	 * Then     I should see a domain validation step
	 * When     I check the domain validation checkbox
	 * And      I click on the link "Next"
	 * Then     I should see the key import step
	 * When     I Import the key that belongs to my user
	 * And      I click on the link "Next"
	 * Then     I should see a security token generation step
	 * When     I click on the link "Next"
	 * Then     I should see a login redirection page
	 * And      I should be redirected to the login page after a few seconds.
	 * When     I try to login as Ada
	 * Then     I should be logged in as Ada
	 * And      I should see Ada Lovelace in the profile drop down menu
	 */
	public function testRecoverDefaultSteps() {
		// Reset database at the end of test.
		$this->resetDatabaseWhenComplete();

		// Go to recover page.
		$this->getUrl('recover');

		// Enter the username ada@passbolt.com
		$this->inputText('UserUsername', 'ada@passbolt.com');

		// Press enter to submit the form.
		$this->pressEnter();

		// I should see a thank you page.
		$this->waitUntilISee('.page.recover.thank-you');

		// Go to recovery procedure by clicking on notification email.
		$this->goToRecover('ada@passbolt.com', true);

		// Wait until I see the first page of the recovery.
		$this->waitUntilISee('#js_step_title', '/Account recovery/i');

		// Wait for the server key to be retrieved.
		$this->waitUntilISee('.why-plugin-wrapper', '/I\'ve checked/i');

		// Check box domain check.
		$this->checkCheckbox('js_setup_domain_check');

		// Click Next.
		$this->clickLink("Next");

		// I should see the import key page.
		$this->waitUntilISee('#js_step_title', '/Import your existing key/i');

		// Insert Ada's key.
		$keyData = file_get_contents(GPG_FIXTURES . DS .  'ada_private.key');
		$this->inputText('js_setup_import_key_text', $keyData);

		// Click Next
		$this->clickLink('Next');

		// I should see the token generation page.
		$this->waitUntilISee('#js_step_title', '/We need a visual cue to protect us from the bad guys/i');

		// Click Next
		$this->clickLink('Next');

		// I should be redirected to login page.
		$this->completeStepLoginRedirection();

		// Attempt to log in as ada.
		$user = User::get('ada');
		$this->loginAs($user);

		// Assert I am logged in as Ada.
		$this->assertElementContainsText(
			$this->findByCss('.header .user.profile .details .name'),
			'Ada Lovelace'
		);
	}

	/**
	 * Scenario:    As AP, I should not be able to recover my account with a key that doesn't exist on server.
	 * Given    I am Ada
	 * When     start a recovery procedure, and click on the link provided in the email
	 * Then     I should see a domain validation step
	 * When     I check the domain validation checkbox
	 * And      I click on the link "Next"
	 * Then     I should see the key import step
	 * When     I Import the key that doesn't belong to my user, nor is used by anybody else.
	 * And      I click on the link "Next"
	 * Then     I should see an error message informing me that the key is not associated to a user.
	 */
	public function testRecoverKeyDoesntExist() {
		// Reset database at the end of test.
		$this->resetDatabaseWhenComplete();

		// Go to recover page.
		$this->getUrl('recover');

		// Enter the username ada@passbolt.com
		$this->inputText('UserUsername', 'ada@passbolt.com');

		// Press enter to submit the form.
		$this->pressEnter();

		// I should see a thank you page.
		$this->waitUntilISee('.page.recover.thank-you');

		// Go to recovery procedure by clicking on notification email.
		$this->goToRecover('ada@passbolt.com', true);

		// Wait until I see the first page of the recovery.
		$this->waitUntilISee('#js_step_title', '/Account recovery/i');

		// Wait for the server key to be retrieved.
		$this->waitUntilISee('.why-plugin-wrapper', '/I\'ve checked/i');

		// Check box domain check.
		$this->checkCheckbox('js_setup_domain_check');

		// Click Next.
		$this->clickLink("Next");

		// I should see the import key page.
		$this->waitUntilISee('#js_step_title', '/Import your existing key/i');

		// Insert a key that is not already used on server.
		$keyData = file_get_contents(GPG_FIXTURES . DS .  'test_private.key');
		$this->inputText('js_setup_import_key_text', $keyData);

		// Click Next
		$this->clickLink('Next');

		// I should see an error message.
		$this->waitUntilISee('#KeyErrorMessage', '/This key doesn\'t match any account/');
	}


	/**
	 * Scenario:    As AP, I should not be able to recover my account with a key that is no valid.
	 * Given    I am Ada
	 * When     Start a recovery procedure, and click on the link provided in the email
	 * Then     I should see a domain validation step
	 * When     I check the domain validation checkbox
	 * And      I click on the link "Next"
	 * Then     I should see the key import step
	 * When     I import my public key.
	 * And      I click on the link "Next"
	 * Then     I should see an error message informing me that the key is not a valid private key.
	 * When 	I import a not valid key.
	 * And 		I click on the link "Next"
	 * Then 	I should see an error message informing me that the format of the key is not known.
	 */
	public function testRecoverKeyNotValid() {
		// Reset database at the end of test.
		$this->resetDatabaseWhenComplete();

		// Start a recovery procedure, and click on the link provided in the email
		$this->getUrl('recover');

		// Enter the username ada@passbolt.com
		$this->inputText('UserUsername', 'ada@passbolt.com');

		// Press enter to submit the form.
		$this->pressEnter();

		// I should see a thank you page.
		$this->waitUntilISee('.page.recover.thank-you');

		// Go to recovery procedure by clicking on notification email.
		$this->goToRecover('ada@passbolt.com', true);

		// I should see a domain validation step
		$this->waitUntilISee('#js_step_title', '/Account recovery/i');

		// Wait for the server key to be retrieved.
		$this->waitUntilISee('.why-plugin-wrapper', '/I\'ve checked/i');

		// I check the domain validation checkbox
		$this->checkCheckbox('js_setup_domain_check');

		// I click on the link "Next"
		$this->clickLink("Next");

		// I should see the key import step
		$this->waitUntilISee('#js_step_title', '/Import your existing key/i');

		// When I import my public key.
		$keyData = file_get_contents(GPG_FIXTURES . DS .  'ada_public.key');
		$this->inputText('js_setup_import_key_text', $keyData);

		// And I click on the link "Next"
		$this->clickLink('Next');

		// Then I should see an error message informing me that the key is not a valid private key.
		$this->waitUntilISee('#KeyErrorMessage', '/This key is not a valid private key/');

		// When I import a not valid key.
		$this->inputText('js_setup_import_key_text', 'Not Valid Key');

		// And I click on the link "Next"
		$this->clickLink('Next');

		// Then I should see an error message informing me that the format of the key is not known.
		$this->waitUntilISee('#KeyErrorMessage', '/Unknown ASCII armor type/');
	}
}