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
	 * go To Setup page.
	 * @throws Exception
	 */
	private function __goToSetup($username) {
		// Get last email.
		$this->getUrl('seleniumTests/showLastEmail/' . urlencode($username));
		// Remember setup url. (We will use it later).
		$linkElement = $this->findLinkByText('get started');
		$setupUrl = $linkElement->getAttribute('href');
		// Go to url remembered above.
		$this->driver->get($setupUrl);
		// Wait until I see the first page of setup.
		$this->waitUntilISee('#js_step_content h3', '/Plugin check/i');
	}

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

	/**
	 * Scenario :   I go through the setup and I make sure the next / cancel buttons and menu items are working properly.
	 * Given        I am an anonymous user with the plugin on the first page of the setup
	 * Then         the menu "1. get the plugin" should be selected
	 * When         I check the domain validation checkbox
	 * And          I click on the link "Next"
	 * Then         I should see a page with a title "Create a new key"
	 * And          the menu "2. Define your keys" should be selected
	 * When         I click on the link "Cancel"
	 * Then         I should be back on the 1st step.
	 * When         I check the domain validation checkbox.
	 * And          I click "Next"
	 * And          I click "Next" again
	 * Then         I should be at the step 3
	 * And          I should see a page with title "Now let's setup your master password"
	 * And          The menu "3. Set a master password" should be selected
	 * When         I click "Cancel"
	 * Then         I should be back at step 2
	 * And          the menu "2. Define your keys should be selected"
	 * When         I click "Next"
	 * Then         I should be back at step 3
	 * When         I fill up a master password in the password field
	 * And          I click "Next"
	 * Then         I should reach a page saying that the secret and public key is generating
	 * And          I should wait until the key is generated
	 * And          I should reach the next step saying that the secret key is ready.
	 * And          I should see that the menu "3. Set a master password" is selected
	 * When         I click "Cancel"
	 * Then         I should be back at the step "enter master password"
	 * When         I enter the master password and click Next
	 * Then         I should see that the key generates again
	 * When         The key is generated and I reach the next step "Success! Your secret key is ready"
	 * And          I click "Next"
	 * Then         I should reach the next step
	 * And          I should see "Set a security token" as the title
	 * When         I click "Next"
	 * Then         I should reach the final step where I have to set the application password
	 * And          The "Login !" menu should be selected
	 * When         I click "Cancel"
	 * Then         I should be back to the previous step with the security token
	 *
	 * @throws Exception
	 */
	public function testCancelAndMenuSelection() {
		// Go to Setup page.
		$this->__goToSetup('johndoe@passbolt.com');
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
		// Wait until the key is generated.
		$this->waitUntilISee('#js_step_title', '/Success! Your secret key is ready./i', 20);
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
		// Wait until we see the title Master password.
		$this->waitUntilISee('#js_step_title', '/Success! Your secret key is ready./i');
		// Assert menu is selected.
		$this->assertMenuIsSelected('3. Set a master password');
		// Press Next.
		$this->clickLink("Next");
		// Wait.
		$this->waitUntilISee('#js_step_content h3', '/Set a security token/i');
		// Assert menu is selected.
		$this->assertMenuIsSelected('4. Set a security token');
		// Test that Cancel button is working.
		$this->clickLink('Cancel');
		// Wait until we see the title Your secret key is ready.
		$this->waitUntilISee('#js_step_title', '/Success! Your secret key is ready./i');
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

	/**
	 * Scenario :   As an AP I go through the Setup and I make sure that every step is working properly
	 * Given        I am an anonymous user with the plugin on the first page of the setup
	 * Then         the button Cancel should not be visible
	 * And          The button Next should be disabled
	 * When         I check the domain validation checkbox
	 * Then         the button Next should be enabled
	 * When         I click "Next"
	 * Then         I should see that the button "Next" is processing
	 * And          I should see the step 2 : create a new key
	 * And          I should see "John Doe" in the field Owner name
	 * And          I should see "johndoe@passbolt.com" in the field email
	 * And          I should see that the field email is disabled
	 * When         I click "Next"
	 * Then         I should reach the step 3
	 * And          I should see the title "Now let's setup your master password"
	 * When         I fill up "johndoemasterpassword" for the master password
	 * Then         I should see the password strength updated to "fair"
	 * And          I should see the password strength progress bar reflecting the fair strength
	 * And          I should not see the password in clear
	 * When         I click on the "show password" button (with a eye icon)
	 * Then         I should see the password in clear
	 * When         I click "Next"
	 * Then         I should see a page generating my key set
	 * And          I should see that the "Next" button is processing
	 * And          I should wait till the generation is over
	 * And          I should see the next step with a message saying that my keys have been successfuly generated
	 * And          I should see a download button
	 * When         I click "Next"
	 * Then         I should reach the next step "Security Token"
	 * And          I should see that colors and text tokens have been selected by default
	 * When         I click "Next"
	 * Then         I should reach the next step to set the first password
	 * And          I should see that the Name field is disabled
	 * And          I should see that the Name field contains "John Doe"
	 * And          I should see that the Email field is disabled
	 * And          I should see that the Email field contains "johndoe@passbolt.com"
	 * And          I should see that the url field is disabled
	 * And          I should see that the "Next" button is disabled
	 * When         I fill up a password for the password field
	 * Then         I should see that the strength of the password is updated to fair
	 * And          I should see the strength progress bar reflecting the fair status
	 * And          I should not see the password in clear
	 * When         I click on the password generation button
	 * Then         A different password should appear in the password field
	 * When         I click on show password button
	 * Then         I should see the generated password
	 * And          I should see the "show password button" pressed
	 * When         I click again on the show password button
	 * Then         I should not see the password in clear anymore
	 * And          I should see that the button Next is enabled
	 *
	 * @throws Exception
	 *
	 */
	public function testCanFollowSetupWithDefaultSteps() {
		// Go to setup page.
		$this->__goToSetup('johndoe@passbolt.com');

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

		// Fill master key.
		$this->inputText('KeyComment', 'This is a comment for john doe key');

		// Fill comment.
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

		/////////////////////////////////////////////////////////
		///////////   Download key        //////////////////////
		////////////////////////////////////////////////////////

		$this->waitUntilISee('#js_setup_submit_step.enabled', null, 20);
		$this->waitUntilISee('#js_step_title', '/Success! Your secret key is ready./i');
		$this->assertElementHasClass(
			$this->findByCss('div.plugin-check-wrapper div.message'),
			'success'
		);
		// Test that download button is available.
		$this->assertElementContainsText(
			$this->findByCss('div.plugin-check-wrapper #js_backup_key_download'),
			'download'
		);
		$this->assertElementHasClass(
			$this->findById('js_setup_submit_step'),
			'enabled'
		);
		// We cannot test that it is possible to download the key due to driver limitations.
		// Click Next.
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
		// Do not remove the line below. Without it the test gets stuck without a reason.
		sleep(5);
		// Check we are logged in.
		$this->waitUntilISee('#js_app_controller.ready');
			// Check that the name is ok.
		$this->assertElementContainsText(
			$this->findByCss('.header .user.profile .details .name'),
			'John Doe'
		);
			// Check that the email is ok.
		$this->assertElementContainsText(
			$this->findByCss('.header .user.profile .details .email'),
			'johndoe@passbolt.com'
		);
	}

	public function testFollowSetupWithImportKey() {
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
		// Wait
		$this->waitUntilISee('#js_step_content h3', '/Plugin check/i');

		// Check box domain check.
		$this->checkCheckbox('js_setup_domain_check');

		// Click Next.
		$this->clickLink("Next");
		// Wait
		$this->waitUntilISee('#js_step_title', '/Create a new key or import an existing one!/i');
		// Click on import.
		$this->clickLink('import');
		// Wait
		$this->waitUntilISee('#js_step_title', '/Import an existing key or create a new one!/i');
		// Click on import.
		$this->clickLink('create');
		// Wait
		$this->waitUntilISee('#js_step_title', '/Create a new key or import an existing one!/i');
		// Click on import.
		$this->clickLink('import');
		// Wait
		$this->waitUntilISee('#js_step_title', '/Import an existing key or create a new one!/i');
		// Test that button next is disabled by default.
		$this->assertElementHasClass(
			$this->findById('js_setup_submit_step'),
			'disabled'
		);
		// Enter an invalid key.
		$this->inputText('js_setup_import_key_text', 'This is a fake key');
		// Assert that error message is hidden.
		$this->assertElementHasClass(
			$this->findById('KeyErrorMessage'),
			'hidden'
		);
		// Test that button next is disabled by default.
		$this->assertElementHasClass(
			$this->findById('js_setup_submit_step'),
			'enabled'
		);
		// Click Next
		$this->clickLink('Next');
		// Find element.
		$this->assertElementHasNotClass(
			$this->findById('KeyErrorMessage'),
			'hidden'
		);
		// Assert that error message contains the right text.
		$this->assertElementContainsText(
			$this->findById('KeyErrorMessage'),
			'The key selected has an invalid format.'
		);
		// Emtpy value.
		$this->emptyInput('js_setup_import_key_text');
		$key = '-----BEGIN PGP PRIVATE KEY BLOCK-----
Version: GnuPG/MacGPG2 v2.0.22 (Darwin)
Comment: GPGTools - https://gpgtools.org

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
GFq/vw==
=YcG9
-----END PGP PRIVATE KEY BLOCK-----';
		// Paste a correct key.
		$this->inputText('js_setup_import_key_text', $key);
		// Click Next
		$this->clickLink('Next');
		// Wait
		$this->waitUntilISee('#js_step_title', '/Let\'s make sure you imported the right key/i');
		// Assert that there is a warning message
		$this->assertElementHasClass(
			$this->findByCss('#js_step_content div.message'),
			'warning'
		);
		$this->assertElementContainsText(
			$this->findByCss('#js_step_content .table-info'),
			'Passbolt PGP'
		);
		$this->assertElementContainsText(
			$this->findByCss('#js_step_content .table-info'),
			'passbolt@passbolt.com'
		);
		$this->assertElementContainsText(
			$this->findByCss('#js_step_content .table-info'),
			'4f8194025fd2d92c'
		);
		$this->assertElementContainsText(
			$this->findByCss('#js_step_content .table-info'),
			'120f87dde5a438de89826d464f8194025fd2d92c'
		);
		// Click Next
		$this->clickLink('Next');
		// Wait until next step.
		$this->waitUntilISee('#js_step_content h3', '/Set a security token/i');
		$this->clickLink("Next");

		$this->waitUntilISee('#js_step_content h3', '/This is your password to login in the application itself/i');

		// Fill up password.
		$initialPassword = 'passwordtoapplication';
		$this->inputText('js_setup_password', $initialPassword);
		// Test that button Next is enabled.
		$this->assertElementHasClass(
			$this->findById('js_setup_submit_step'),
			'enabled'
		);
//		// Click Next.
//		$this->clickLink("Next");
//		// Check we are logged in.
//		$this->waitUntilISee('#container.page.password', null, 20);
//		// Check that the name is ok.
//		$this->assertElementContainsText(
//			$this->findByCss('.header .user.profile .details .name'),
//			'John Doe'
//		);
//		// Check that the email is ok.
//		$this->assertElementContainsText(
//			$this->findByCss('.header .user.profile .details .email'),
//			'johndoe@passbolt.com'
//		);

	}
	// TODO : fix plugin so the scenario 2 also works.
	// TODO : test that we cannot access the setup again.


	// TODO : Test scenario with a key that has matching information (same name and email).
	// TODO : Test a scenario where the name of the user has to be altered.
}