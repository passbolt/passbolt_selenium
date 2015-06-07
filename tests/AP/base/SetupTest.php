<?php
/**
 * Feature : Setup
 * As an anonymous user, I need to be able to see the setup page with an invitation to install the plugin.
 *
 * @copyright 	(c) 2015-present Bolt Software Pvt. Ltd.
 * @licence			GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
 */
class SetupTest extends PassboltSetupTestCase {

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
		$this->assertElementContainsText(
			$this->findByCss("div.plugin-check-wrapper .plugin-check.success"),
			"Nice one! Firefox plugin is installed and up to date. You are good to go!"
		);
		// Test that the domain in the url check textbox is the same as the one configured.
		$domain = $this->findById("js_setup_domain")->getAttribute('value');
		$this->assertEquals(Config::read('passbolt.url'), $domain);
	}

	public function testCancelAndMenuSelection() {
		// Get last email.
		$this->getUrl('seleniumTests/showLastEmail/' . urlencode('johndoe@passbolt.com'));
		// Remember setup url. (We will use it later).
		$linkElement = $this->findLinkByText('get started');
		$setupUrl = $linkElement->getAttribute('href');
		// Go to url remembered above.
		$this->driver->get($setupUrl);

		$this->waitUntilISee('#js_step_content h3', '/Plugin check/i');
		// Assert menu is selected.
		$this->assertMenuIsSelected('1. Get the plugin');
		// Check box domain check.
		$this->checkCheckbox('js_setup_domain_check');
		// Click Next.
		$this->clickLink("Next");
		// Wait
		$this->waitUntilISee('#js_step_content h3', '/Create a new key/i');
		// Assert menu is selected.
		$this->assertMenuIsSelected('2. Define your keys');
		// Test that Cancel button is working.
		$this->clickLink('Cancel');
		// Test that we are back at step 1.
		$this->waitUntilISee('#js_step_content h3', '/Plugin check/i');
		// Assert menu is selected.
		$this->assertMenuIsSelected('1. Get the plugin');
		// Check box domain check.
		$this->checkCheckbox('js_setup_domain_check');
		// Click Next.
		$this->clickLink("Next");
		// Wait
		$this->waitUntilISee('#js_step_title', '/Create a new key/i');
		// Click Next.
		$this->clickLink("Next");
		// Wait until we see the title Master password.
		$this->waitUntilISee('#js_step_title', '/Now let\'s setup your master password!/i');
		// Assert menu is selected.
		$this->assertMenuIsSelected('3. Set a master password');
		// Test that Cancel button is working.
		$this->clickLink('Cancel');
		// Wait
		$this->waitUntilISee('#js_step_title', '/Create a new key/i');
		// Assert menu is selected.
		$this->assertMenuIsSelected('2. Define your keys');
		// Click Next.
		$this->clickLink("Next");
		// Wait until we see the title Master password.
		$this->waitUntilISee('#js_step_title', '/Now let\'s setup your master password!/i');
		// Fill master key.
		$this->inputText('js_field_password', 'johndoemasterpassword');
		// Press Next.
		$this->clickLink("Next");
		// Wait to reach the page.
		$this->waitUntilISee('#js_step_content h3', '/Generating the secret and public key/i');
		// Wait for the key to finish generate.
		$this->waitUntilISee('#js_setup_submit_step.enabled', null, 20);
		// Assert menu is selected.
		$this->assertMenuIsSelected('3. Set a master password');
		// The key is generated, we can click Next.
		$this->clickLink("Cancel");
		// Wait until we see the title Master password.
		$this->waitUntilISee('#js_step_title', '/Now let\'s setup your master password!/i');
		// Assert menu is selected.
		$this->assertMenuIsSelected('3. Set a master password');
		// Fill master key.
		$this->inputText('js_field_password', 'johndoemasterpassword');
		// Press Next.
		$this->clickLink("Next");
		// Wait to reach the page.
		$this->waitUntilISee('#js_step_content h3', '/Generating the secret and public key/i');
		// Wait for the key to finish generate.
		$this->waitUntilISee('#js_setup_submit_step.enabled', null, 20);
		// Press Next.
		$this->clickLink("Next");
		// Wait.
		$this->waitUntilISee('#js_step_content h3', '/Set a security token/i');
		// Assert menu is selected.
		$this->assertMenuIsSelected('4. Set a security token');
		// Test that Cancel button is working.
		$this->clickLink('Cancel');
		// Wait to reach the page.
		$this->waitUntilISee('#js_step_content h3', '/Generating the secret and public key/i');
		// Wait for the key to finish generate.
		$this->waitUntilISee('#js_setup_submit_step.enabled', null, 20);
		// Assert menu is selected.
		$this->assertMenuIsSelected('3. Set a master password');
		// Press Next.
		$this->clickLink("Next");
		// Wait.
		$this->waitUntilISee('#js_step_content h3', '/Set a security token/i');
		// Press Next.
		$this->clickLink("Next");
		// Test that we are at the final step.
		$this->waitUntilISee('#js_step_content h3', '/This is your password to login in the application itself/i');
		// Assert menu is selected.
		$this->assertMenuIsSelected('5. Login !');
		// Test that Cancel button is working.
		$this->clickLink('Cancel');
		// Wait.
		$this->waitUntilISee('#js_step_content h3', '/Set a security token/i');
		// Assert menu is selected.
		$this->assertMenuIsSelected('4. Set a security token');
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
		$this->assertElementHasClass(
			$this->findByCss('#js_setup_cancel_step'),
			'hidden'
		);
		// Test that button Next is disabled.
		$this->assertElementHasClass(
			$this->findByCss('#js_setup_submit_step'),
			'disabled'
		);
		// Check box domain check.
		$this->checkCheckbox('js_setup_domain_check');
		// Test that button Next is disabled.
		$this->assertElementHasNotClass(
			$this->findById('js_setup_submit_step'),
			'disabled'
		);

		// Click Next.
		$this->clickLink("Next");
		// Test that button Next is disabled.
		$this->assertElementHasClass(
			$this->findByCss('#js_setup_submit_step'),
			'processing'
		);

		// Wait
		$this->waitUntilISee('#js_step_content h3', '/Create a new key/i');
		// Test that the text corresponding to key section is set.
		$this->assertTitleEquals( "Create a new key or import an existing one!" );


		/////////////////////////////////////////////////////////
		///////////   Enter Key information ////////////////////
		////////////////////////////////////////////////////////

		// Test that field owner name is set to John Doe.
		$this->assertElementAttributeEquals(
			$this->findById('OwnerName'),
			'value',
			'John Doe'
		);
		// Test that field owner email is set to johndoe@passbolt.com
		$this->assertElementAttributeEquals(
			$this->findById('OwnerEmail'),
			'value',
			'johndoe@passbolt.com'
		);
		// Test that email field is disabled.
		$this->assertElementAttributeEquals(
			$this->findById('OwnerEmail'),
			'disabled',
			'true'
		);

		$this->clickLink("Next");

		/////////////////////////////////////////////////////////
		///////////   Set Master password //////////////////////
		////////////////////////////////////////////////////////
		$this->waitUntilISee('#js_step_title', '/Now let\'s setup your master password!/i');

		// Fill master key.
		$this->inputText('js_field_password', 'johndoemasterpassword');
		// Test that complexity has been updated.
		$this->assertElementContainsText(
			$this->findByCss('#js_user_pwd_strength .complexity-text strong'),
			'fair'
		);
		// Test that progress bar contains class fair.
		$this->assertElementHasClass(
			$this->findByCss('#js_user_pwd_strength .progress .progress-bar'),
			'fair'
		);
		// Test that password in clear is hidden.
		$this->assertElementHasClass(
			$this->findById('js_field_password_clear'),
			'hidden'
		);
		// Test that clicking on the view button shows the password in clear.
		$this->findById('js_show_pwd_button')->click();
		$this->assertElementHasNotClass(
			$this->findById('js_field_password_clear'),
			'hidden'
		);
		$this->clickLink("Next");

		/////////////////////////////////////////////////////////
		///////////   Generate key         //////////////////////
		////////////////////////////////////////////////////////
		$this->waitUntilISee('#js_step_content h3', '/Generating the secret and public key/i');
		$this->assertTitleEquals('Give us a second while we crunch them numbers!');
		$this->assertElementHasClass(
			$this->findById('js_setup_submit_step'),
			'processing'
		);
		$this->waitUntilISee('#js_setup_submit_step.enabled', null, 20);

		// The key is generated, we can click Next.
		$this->clickLink("Next");
		$this->waitUntilISee('#js_step_content h3', '/Set a security token/i');


		/////////////////////////////////////////////////////////
		///////////   Security token      ///////////////////////
		////////////////////////////////////////////////////////
		$this->assertTitleEquals('We need a visual cue to protect us from the bad guys..');
		// Test that default values are filled by default..
		$this->assertTrue(
			$this->findById('js_security_token_text')->getAttribute('value') != '',
			'The token text should not be empty by default'
		);
		$this->assertTrue(
			$this->findById('js_security_token_background')->getAttribute('value') != '',
			'The token background should not be empty by default'
		);
		$this->assertTrue(
			$this->findById('js_security_token_color')->getAttribute('value') != '',
			'The token color should not be empty by default'
		);
		$this->clickLink("Next");


		/////////////////////////////////////////////////////////
		///////////   Set application password     //////////////
		////////////////////////////////////////////////////////
		$this->waitUntilISee('#js_step_content h3', '/This is your password to login in the application itself/i');
		$this->assertTitleEquals('Alright sparky, let\'s create your first password!');
		// Test that Name field is disabled and filled up.
		$this->assertElementAttributeEquals(
			$this->findById('PasswordName'),
			'disabled',
			'true'
		);
		$this->assertElementAttributeEquals(
			$this->findById('PasswordName'),
			'value',
			'John Doe'
		);
		// Test that username field is disabled and filled up.
		$this->assertElementAttributeEquals(
			$this->findById('PasswordUsername'),
			'value',
			'johndoe@passbolt.com'
		);
		$this->assertElementAttributeEquals(
			$this->findById('PasswordUsername'),
			'disabled',
			'true'
		);
		// Test that url field is disabled.
		$this->assertElementAttributeEquals(
			$this->findById('PasswordURL'),
			'disabled',
			'true'
		);
		// Test that button Next is disabled.
		$this->assertElementHasClass(
			$this->findById('js_setup_submit_step'),
			'disabled'
		);

		// Fill up password.
		$initialPassword = 'passwordtoapplication';
		$this->inputText('js_setup_password', $initialPassword);
		// Test that complexity has been updated.
		$this->assertElementContainsText(
			$this->findByCss('#js_user_pwd_strength .complexity-text strong'),
			'fair'
		);
		// Test that progress bar contains class fair.
		$this->assertElementHasClass(
			$this->findByCss('#js_user_pwd_strength .progress .progress-bar'),
			'fair'
		);
		// Test that password in clear is hidden.
		$this->assertElementHasClass(
			$this->findById('js_setup_password_clear'),
			'hidden'
		);
		// Test that generate password button works.
		$this->findById('js_gen_pwd_button')->click();
		$this->assertTrue (
			$this->findById('js_setup_password')->getAttribute('value') != '',
			'After password generation the password field should not be empty'
		);
		$this->assertTrue (
			$this->findById('js_setup_password')->getAttribute('value') != $initialPassword,
			'After password generation the password field should be different than the initial password'
		);
		// Test that clicking on the view button shows the password in clear.
		$this->findById('js_show_pwd_button')->click();
		// Test that show password button has the class selected.
		$this->assertElementHasClass(
			$this->findById('js_show_pwd_button'),
			'selected'
		);
		// Test that the clear password is visible.
		$this->assertElementHasNotClass(
			$this->findById('js_setup_password_clear'),
			'hidden'
		);
		// Hide password again.
		$this->findById('js_show_pwd_button')->click();
		// Test that the clear password is back to hidden state.
		$this->assertElementHasClass(
			$this->findById('js_setup_password_clear'),
			'hidden'
		);
		// Test that button Next is enabled.
		$this->assertElementHasClass(
			$this->findById('js_setup_submit_step'),
			'enabled'
		);
		// Click Next.
		$this->clickLink("Next");

		// TODO : test that the user is logged in.
	}
}