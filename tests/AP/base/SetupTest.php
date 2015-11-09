<?php
/**
 * Feature : Setup
 * As an anonymous user, I need to be able to see the setup page with an invitation to install the plugin.
 *
 * @TODO : Test a scenario where the key is not compatible with GPG on server side.
 * @TODO : Test scenario with a key that has matching information (same name and email).
 * @TODO : Test a scenario where the name of the user has to be altered.
 * @copyright (c) 2015-present Bolt Software Pvt. Ltd.
 * @licence GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
 */
class SetupTest extends PassboltSetupTestCase {

	public $sections = [
		'domain_check' => [
			'title'     => 'Welcome to passbolt! Let\'s take 5 min to setup your system.',
			'subtitle'  => 'Plugin check',
			'menu_item' => '1. Get the plugin'
		],
		'generate_key_form' => [
			'title'     => 'Create a new key or import an existing one!',
			'subtitle'  => 'Create a new key',
			'menu_item' => '2. Define your keys'

		],
		'generate_key_master_password' => [
			'title' => 'Now let\'s setup your master password!',
			'subtitle'  => 'Create a new key',
			'menu_item' => '3. Set a master password'
		],
		'generate_key_progress' => [
			'title' => 'Give us a second while we crunch them numbers!',
			'subtitle' => 'Generating the secret and public key',
			'menu_item' => '3. Set a master password'
		],
		'generate_key_done' => [
			'title' => 'Success! Your secret key is ready.',
			'subtitle' => 'Let\'s make a backup',
			'menu_item' => '3. Set a master password'
		],
		'import_key_form' => [
			'title' => 'Import an existing key or create a new one!',
			'subtitle' => 'Copy paste your public and private key below, or select it from a file.',
			'menu_item' => '2. Import your key'
		],
		'import_key_done' => [
			'title' => 'Let\'s make sure you imported the right key',
			'subtitle' => 'Information for public and secret key',
			'menu_item' => '2. Import your key'
		],
		'security_token' => [
			'title' => 'We need a visual cue to protect us from the bad guys..',
			'subtitle' => 'Set a security token',
			'menu_item' => '4. Set a security token'
		],
		'login_redirect' => [
			'title' => 'Alright sparky, it\'s time to log in!',
			'subtitle' => 'Please wait... you are being redirected to the login page',
			'menu_item' => '5. Login !'
		],

	];

	/**
	 * Wait until the requested section appears.
	 * @param $sectionName
	 *
	 * @throws Exception
	 */
	private function __waitForSection($sectionName) {
		$timeout = 10;
		if ($sectionName == 'generate_key_done') {
			$timeout = 30;
		}
		$this->waitUntilISee('#js_step_title', '/' . $this->__getSectionInfo($sectionName, 'title') . '/i', $timeout);
		$this->waitUntilISee('#js_step_content h3', '/' . $this->__getSectionInfo($sectionName, 'subtitle') . '/i', $timeout);
	}

	/**
	 * Get a section info as
	 * @param        $sectionName
	 *   name of the section
	 *
	 * @param string $info
	 *   information requested. (title, subtitle, etc..)
	 *
	 * @return mixed
	 * @throws Exception
	 */
	private function __getSectionInfo($sectionName, $info = '') {
		if (!isset($this->sections[$sectionName])) {
			throw new Exception('The section name provided doesnt exist');
		}
		if ($info != '') {
			if (!isset($this->sections[$sectionName][$info])) {
				throw new Exception('The info requested doesnt exist in that section');
			}
			return $this->sections[$sectionName][$info];
		}
		return $this->sections[$sectionName];
	}

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

		// Test that the plugin confirmation message is displayed.
		$this->waitUntilISee('.plugin-check-wrapper .plugin-check.success', '/Firefox plugin is installed and up to date/i');

	}

	/**
	 * Scenario :   As an AP I should be able to use the domain verification step of the setup
	 * Given        I am an anonymous user with the plugin on the first page of the setup
	 * Then         the button Cancel should not be visible
	 * And          The button Next should be disabled
	 * And          The domain value should be same as the domain I enter initially
	 * When         I check the domain validation checkbox
	 * Then         the button Next should be enabled
	 */
	private function __testStepDomainVerification() {
		// Test that button cancel is hidden.
		$this->assertElementHasClass(
			$this->find('js_setup_cancel_step'),
			'hidden'
		);
		// Test that button Next is disabled.
		$this->assertElementHasClass(
			$this->find('js_setup_submit_step'),
			'disabled'
		);
		// Test that the domain in the url check textbox is the same as the one configured.
		$domain = $this->findById("js_setup_domain")->getAttribute('value');
		$this->assertEquals(Config::read('passbolt.url'), $domain);

		// Give it time to load the server key.
		sleep(2);

		// Test that the server key fingerprint is correct.
		$serverKey = $this->findById("js_setup_key_fingerprint")->getAttribute('value');
		$this->assertEquals(Config::read('passbolt.server_key.fingerprint'), $serverKey);

		// Click on more to read information about the key.
		$this->clickLink('More');

		// Assert that the dialog window is opened.
		$this->assertVisible('dialog-server-key-info');

		// I should see the title "Please verify the server key"
		$this->assertElementContainsText(
			$this->findByCss('.dialog-header'),
			'Please verify the server key'
		);

		// I should see the Owner name
		$this->assertElementContainsText(
			$this->findByCss('.dialog-wrapper .owner-name'),
			'Passbolt Server Test Key'
		);

		// I should see the Owner email
		$this->assertElementContainsText(
			$this->findByCss('.dialog-wrapper .owner-email'),
			'no-reply@passbolt.com'
		);

		// I should see the key id
		$this->assertElementContainsText(
			$this->findByCss('.dialog-wrapper .keyid'),
			'573EE67E'
		);

		// I should see the key fingerprint
		$this->assertElementContainsText(
			$this->findByCss('.dialog-wrapper .fingerprint'),
			$serverKey
		);

		// I should see the length
		$this->assertElementContainsText(
			$this->findByCss('.dialog-wrapper .length'),
			'4096'
		);

		// I should see the algorithm
		$this->assertElementContainsText(
			$this->findByCss('.dialog-wrapper .algorithm'),
			'RSA'
		);

		// If I click ok.
		$this->findById('key-info-ok')
			->click();

		// Then I should not see the dialog anymore.
		$this->assertNotVisible('dialog-server-key-info');

		// If I open the dialog again.
		$this->clickLink('More');

		// And I click the close icon in the dialog.
		$this->findByCss('.dialog-wrapper a.dialog-close')
		     ->click();

		// Then I should not see the dialog anymore.
		$this->assertNotVisible('dialog-server-key-info');

		// Check box domain check.
		$this->checkCheckbox('js_setup_domain_check');
		// Test that button Next is disabled.
		$this->assertElementHasNotClass(
			$this->findById('js_setup_submit_step'),
			'disabled'
		);

	}

	/**
	 * Scenario :   As an AP I should be able to prepare the creation of my keys
	 * Given        I am on the step 2 "Create a new key" of the setup
	 * And          I should see the step 2 : create a new key
	 * And          I should see "John Doe" in the field Owner name
	 * And          I should see "johndoe@passbolt.com" in the field email
	 * And          I should see that the field email is disabled
	 * When         I enter a comment in the comment field of the page
	 *
	 * @throws Exception
	 */
	private function __testStepPrepareCreateKey() {
		// Wait
		$this->__waitForSection('generate_key_form');
		// Test that the text corresponding to key section is set.
		$this->assertTitleEquals( $this->__getSectionInfo('generate_key_form', 'title') );
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
	}

	/**
	 * Scenario :      As an AP using the setup, I should be able to enter my master password for the private key.
	 * Given           I am at the step asking me to enter my master password.
	 * When            I fill up a master password
	 * Then            I should see that the strength is getting updated
	 * And             I should see that the strength progress bar is getting updated
	 * And             I should not see the master password in clear
	 * When            I click on the show password button
	 * Then            I should see the password in clear
	 * @throws Exception
	 */
	private function __testStepEnterMasterPassword() {
		// Wait until section appears.
		$this->__waitForSection('generate_key_master_password');

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
			$this->find('js_field_password_clear'),
			'hidden'
		);
		// Test that clicking on the view button shows the password in clear.
		$this->find('js_show_pwd_button')->click();
		$this->assertElementHasNotClass(
			$this->find('js_field_password_clear'),
			'hidden'
		);
	}

	/**
	 * Scenario :   As an AP using the setup I should be able to import my own key.
	 * Given        I am at the step 2 and I select import my key, instead of generating one
	 * Then         I should see a textarea to put the key content in it.
	 * And          the Next button should be disabled
	 * When         I insert a random text in the key field
	 * Then         The next button should be enabled
	 * When         I click "Next"
	 * Then         I should see an error message saying that the key has an invalid format
	 * When         I delete the random text from the textarea
	 * And          I replace it with a private key in a proper format
	 * And          I click "Next"
	 * Then         I should see a different confirmation page with my key information
	 * When         I observe this confirmation page
	 * Then         I should retrieve my key information
	 *
	 * @throws Exception
	 */
	private function __testStepImportKey($key = []) {
		// Get the Gpgkey.
		if (empty($key)) {
			$this->fail('The function should be provided a key as argument');
		}

		// Wait until section appears.
		$this->__waitForSection('import_key_form');
		// Test that button next is disabled by default.
		$this->assertElementHasClass(
			$this->find('js_setup_submit_step'),
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
			$this->find('js_setup_submit_step'),
			'enabled'
		);
		// Click Next
		$this->clickLink('Next');
		// Find element.
		$this->assertElementHasNotClass(
			$this->find('KeyErrorMessage'),
			'hidden'
		);
		// Assert that error message contains the right text.
		$this->assertElementContainsText(
			$this->find('KeyErrorMessage'),
			'The key selected has an invalid format.'
		);
		// Emtpy value.
		$this->find('js_setup_import_key_text')->clear();
		// Paste a correct key.
		$keyData = file_get_contents($key['filepath']);
		$this->inputText('js_setup_import_key_text', $keyData);
		// Click Next
		$this->clickLink('Next');

		// Wait until section appears.
		$this->__waitForSection('import_key_done');

		// I should see a success message.
		$this->assertElementContainsText(
			$this->findByCss('.message.success'),
			'Success'
		);

		$this->assertElementContainsText(
			$this->findByCss('#js_step_content .table-info .owner_name'),
			$key['owner_name']
		);
		$this->assertElementContainsText(
			$this->findByCss('#js_step_content .table-info .owner_email'),
			$key['owner_email']
		);
		$this->assertElementContainsText(
			$this->findByCss('#js_step_content .table-info .keyid'),
			$key['keyid']
		);
		$this->assertElementContainsText(
			$this->findByCss('#js_step_content .table-info .fingerprint'),
			$key['fingerprint']
		);
	}

	/**
	 * Scenario :   As an AP using the setup I should be able to generate and download the key.
	 * Given        I am on the step that generates a private key
	 * Then         I should see that the key is getting generated, and that the Next button is in processing state
	 * When         The key has finished generating
	 * Then         I should see that the next button is enabled
	 * And          I should see that the title says the key is ready
	 * And          There should be a confirmation message
	 * And          There should be a download button
	 * @throws Exception
	 */
	private function __testStepGenerateAndDownloadKey() {
		$this->assertElementHasClass(
			$this->find('js_setup_submit_step'),
			'processing'
		);
		// Wait until section appears.
		$this->__waitForSection('generate_key_progress');

		$this->waitUntilISee('#js_setup_submit_step.enabled', null, 20);

		// Wait till the key is generated.
		$this->__waitForSection('generate_key_done');

		$this->assertElementHasClass(
			$this->findByCss('.plugin-check-wrapper .message'),
			'success'
		);
		// Test that download button is available.
		$this->assertElementContainsText(
			$this->findByCss('.plugin-check-wrapper #js_backup_key_download'),
			'download'
		);
		$this->assertElementHasClass(
			$this->find('js_setup_submit_step'),
			'enabled'
		);
	}

	/**
	 * Scenario :   As an AP using the setup, I should be able to choose a security token
	 * Given        I am at the security token step
	 * Then         I should see that a security token code has been chosen for me
	 * And          I should see that a security token bg color has been chosen for me
	 * And          I should see that a security token text color has been chosen for me
	 * @throws Exception
	 */
	private function __testStepSecurityToken() {
		// Wait for section
		$this->__waitForSection('security_token');

		// I should see the title.
		$this->assertTitleEquals($this->__getSectionInfo('security_token', 'title'));

		// Test that default values are filled by default..
		$this->assertTrue(
			$this->find('js_security_token_text')->getAttribute('value') != '',
			'The token text should not be empty by default'
		);
		$this->assertTrue(
			$this->find('js_security_token_background')->getAttribute('value') != '',
			'The token background should not be empty by default'
		);
		$this->assertTrue(
			$this->find('js_security_token_color')->getAttribute('value') != '',
			'The token color should not be empty by default'
		);
	}

	/**
	 * Scenario :   As an AP using the setup, I should be redirected to the login page at the end of the setup.
	 * Given        I am at the last step
	 * Then         I should see a message telling me that I am being redirected.
	 * And          I should see the login form after I am redirected.
	 * @throws Exception
	 */
	private function __testStepLoginRedirection() {
		// Wait for section.
		$this->__waitForSection('login_redirect');

		// I should see the subtitle.
		$this->assertTitleEquals($this->__getSectionInfo('login_redirect', 'title'));

		// Test that button Next is enabled.
		$this->assertElementHasClass(
			$this->find('js_setup_submit_step'),
			'processing'
		);

		// I should be on the login page.
		$this->waitUntilISee('.information h2', '/Welcome back!/');

		try{
			$this->findByCss('.users.login.form');
		} catch(Exception $e) {
			$this->fail('At the end of setup there should have been a redirection to the login page');
		}
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
		// Register John Doe as a user.
		$this->registerUser('John', 'Doe', 'johndoe@passbolt.com');

		// We check below that we can read the invitation email and click on the link get started.
		// Get last email.
		$this->getUrl('seleniumTests/showLastEmail/' . urlencode('johndoe@passbolt.com'));
		// Follow the link in the email.
		$this->followLink("get started");
		// Test that the url is the plugin one.
		$this->assertUrlMatch('/resource:\/\/passbolt-firefox-addon-at-passbolt-dot-com\/data\/setup.html/');

		// Test that the plugin confirmation message is displayed.
		$this->waitUntilISee('.plugin-check.success', '/Firefox plugin is installed and up to date/i');

		// Test that the domain in the url check textbox is the same as the one configured.
		$domain = $this->findById("js_setup_domain")->getAttribute('value');
		$this->assertEquals(Config::read('passbolt.url'), $domain);

		// Since content was edited, we reset the database
		$this->resetDatabase();
	}

	/**
	 * Scenario :   I go through the setup and I make sure the navigation buttons and menu items are working properly.
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
	 * When         I click "Import"
	 * Then         I should see a page where I can import my keys
	 * When         I click "Create"
	 * Then         I should be back on the page to generate a key
	 * When         I click "Next" again
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
	 * Then         I should reach the final step where I am being redirected
	 * And          The "Login !" menu should be selected
	 *
	 * @throws Exception
	 */
	public function testNavigation() {
		// Register John Doe as a user.
		$this->registerUser('John', 'Doe', 'johndoe@passbolt.com');

		// Go to Setup page.
		$this->__goToSetup('johndoe@passbolt.com');
        // Wait until I see the first page of setup.
		$this->__waitForSection('domain_check');
		// Assert menu is selected.
		$this->assertMenuIsSelected($this->__getSectionInfo('domain_check', 'menu_item'));
		sleep(2);
		// Check box domain check.
		$this->checkCheckbox('js_setup_domain_check');
		// Click Next.
		$this->clickLink("Next");
		// Wait
		$this->__waitForSection('generate_key_form');
		// Assert menu is selected.
		$this->assertMenuIsSelected($this->__getSectionInfo('generate_key_form', 'menu_item'));
		// Test that Cancel button is working.
		$this->clickLink('Cancel');
		// Test that we are back at step 1.
		$this->__waitForSection('domain_check');
		// Assert menu is selected.
		$this->assertMenuIsSelected($this->__getSectionInfo('domain_check', 'menu_item'));
		sleep(2);
		// Check box domain check.
		$this->checkCheckbox('js_setup_domain_check');
		// Click Next.
		$this->clickLink("Next");
		// Wait
		$this->__waitForSection('generate_key_form');
		// Click on import.
		$this->clickLink('import');
		// Wait
		$this->__waitForSection('import_key_form');
		// Click on create.
		$this->clickLink('create');
		// Wait
		$this->__waitForSection('generate_key_form');
		// Click Next.
		$this->clickLink("Next");
		// Wait until we see the title Master password.
		$this->__waitForSection('generate_key_master_password');
		// Assert menu is selected.
		$this->assertMenuIsSelected($this->__getSectionInfo('generate_key_master_password', 'menu_item'));
		// Test that Cancel button is working.
		$this->clickLink('Cancel');
		// Wait
		$this->waitUntilISee('#js_step_title', '/Create a new key/i');
		// Assert menu is selected.
		$this->assertMenuIsSelected($this->__getSectionInfo('generate_key_form', 'menu_item'));
		// Click Next.
		$this->clickLink("Next");
		// Wait until we see the title Master password.
		$this->__waitForSection('generate_key_master_password');
		// Fill master key.
		$this->inputText('js_field_password', 'johndoemasterpassword');
		// Press Next.
		$this->clickLink("Next");
		// Wait to reach the page.
		$this->__waitForSection('generate_key_progress');
		// Wait until the key is generated.
		$this->__waitForSection('generate_key_done');
		// Assert menu is selected.
		$this->assertMenuIsSelected($this->__getSectionInfo('generate_key_done', 'menu_item'));
		// The key is generated, we can click Next.
		$this->clickLink("Cancel");
		// Wait until we see the title Master password.
		$this->__waitForSection('generate_key_master_password');
		// Assert menu is selected.
		$this->assertMenuIsSelected($this->__getSectionInfo('generate_key_master_password', 'menu_item'));
		// Fill master key.
		$this->inputText('js_field_password', 'johndoemasterpassword');
		// Press Next.
		$this->clickLink("Next");
		// Wait to reach the page.
		$this->__waitForSection('generate_key_progress');
		// Wait until we see the title Master password.
		$this->__waitForSection('generate_key_done');
		// Assert menu is selected.
		$this->assertMenuIsSelected($this->__getSectionInfo('generate_key_done', 'menu_item'));
		// Press Next.
		$this->clickLink("Next");
		// Wait.
		$this->__waitForSection('security_token');
		// Assert menu is selected.
		$this->assertMenuIsSelected($this->__getSectionInfo('security_token', 'menu_item'));
		// Test that Cancel button is working.
		$this->clickLink('Cancel');
		// Wait until we see the title Your secret key is ready.
		$this->__waitForSection('generate_key_done');
		// Press Next.
		$this->clickLink("Next");
		// Wait.
		$this->__waitForSection('security_token');
		// Press Next.
		$this->clickLink("Next");
		// Test that we are at the final step.
		$this->__waitForSection('login_redirect');
		// Assert menu is selected.
		$this->assertMenuIsSelected($this->__getSectionInfo('login_redirect', 'menu_item'));
		// Since content was edited, we reset the database
		$this->resetDatabase();
	}


	/**
	 * Scenario     As an AP using the setup, I should be able to go through all the steps of the setup
	 * Given        I am registered and on the first page of the setup
	 * Then         I should be able to verify the domain
	 * When         I click "Next"
	 * Then         I should be able to prepare the generation of my key
	 * When         I click "Next"
	 * Then         I should be able to enter a master password
	 * When         I click "Next"
	 * Then         The key should be generated and I should be able to download it
	 * When         I click "Next"
	 * Then         I should be able to choose a security token
	 * When         I click "Next"
	 * Then         I should be able to enter a password for my account
	 * When         I click "Next"
	 * Then         I should observe that I am logged in inside the app
	 * And          I should see my name and email in the account section
	 * @throws Exception
	 */
	public function testCanFollowSetupWithDefaultSteps() {
		// Register John Doe as a user.
		$this->registerUser('John', 'Doe', 'johndoe@passbolt.com');

		// Go to setup page and register
		$this->__goToSetup('johndoe@passbolt.com');
		$this->__register();

		$this->loginAs([
			'Username' => 'johndoe@passbolt.com',
			'MasterPassword' => 'johndoemasterpassword'
		]);
		// Check we are logged in.
		$this->waitCompletion();
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

		// Since content was edited, we reset the database
		$this->resetDatabase();
	}

	/**
	 * Scenario :  As an AP I should be able to import my own key during the setup
	 * Given       I am registered as John Doe, and I go to the setup
	 * When        I go through the setup until the import key step
	 * And         I test that I can import my key
	 * Then        I should see that the setup behaves as it should (defined in function __testStepImportKey)
	 * When        I complete the setup
	 * Then        I should be logged in inside the app
	 * And         I should be able to visually confirm my account information
	 * @throws Exception
	 */
	public function testFollowSetupWithImportKey() {
		$key = Gpgkey::get(['name' => 'johndoe']);

		// Register John Doe as a user.
		$this->registerUser('John', 'Doe', $key['owner_email']);

		// Go to setup page.
		$this->__goToSetup($key['owner_email']);
		// Wait
		$this->__waitForSection('domain_check');
		// Wait for the server key to be retrieved.
		sleep(2);
		// Check box domain check.
		$this->checkCheckbox('js_setup_domain_check');
		// Click Next.
		$this->clickLink("Next");
		// Wait
		$this->__waitForSection('generate_key_form');
		// Click on import.
		$this->clickLink('import');
		// Wait
		$this->__waitForSection('import_key_form');
		// Test step import key.
		$this->__testStepImportKey($key);
		// Click Next
		$this->clickLink('Next');
		// Wait until next step.
		$this->__waitForSection('security_token');
		// Click Next.
		$this->clickLink("Next");
		// Wait until sees next step.
		$this->__waitForSection('login_redirect');
		// Wait until I reach the login page
		$this->waitUntilISee('.information h2', '/Welcome back!/');

		// Login as john doe
		$this->loginAs([
			'Username' => $key['owner_email'],
			'MasterPassword' => $key['masterpassword']
		]);

		$this->waitCompletion();
		// Check we are logged in.
		$this->waitUntilISee('.page.password', null, 20);
		// Check that the name is ok.
		$this->assertElementContainsText(
			$this->findByCss('.header .user.profile .details .name'),
			$key['owner_name']
		);
		// Check that the email is ok.
		$this->assertElementContainsText(
			$this->findByCss('.header .user.profile .details .email'),
			$key['owner_email']
		);

		// Since content was edited, we reset the database
		$this->resetDatabase();
	}

	/**
	 * Scenario :   As an AP, I should not be able to do the setup after my account has been activated
	 * Given I click again on the link in the invitation email
	 * Then  I should not see the setup again
	 * And   I should see a page with a "Token not found" error
	 * @throws Exception
	 */
	public function testSetupNotAccessibleAfterAccountValidation() {
		// Register John Doe as a user.
		$this->registerUser('John', 'Doe', 'johndoe@passbolt.com');

		// Get last email.
		$this->getUrl('seleniumTests/showLastEmail/' . urlencode('johndoe@passbolt.com'));
		// Remember setup url. (We will use it later).
		$linkElement = $this->findLinkByText('get started');
		$setupUrl = $linkElement->getAttribute('href');

		// Go to setup page.
		$this->__goToSetup('johndoe@passbolt.com');
		$this->__register();

		// Go to url remembered above.
		$this->driver->get($setupUrl);
		$this->waitUntilISee('h2', '/Token not found/');

		// Since content was edited, we reset the database
		$this->resetDatabase();
	}

	/**
	 * Register steps
	 * @throws Exception
	 */
	public function __register() {
		// Test step domain verification.
		$this->__testStepDomainVerification();

		// Click Next.
		$this->clickLink("Next");
		// Test that button Next is disabled.
		$this->assertElementHasClass(
			$this->find('js_setup_submit_step'),
			'processing'
		);
		// test step that prepares key creation.
		$this->__testStepPrepareCreateKey();
		// Fill comment.
		$this->clickLink("Next");
		// Test enter master password step.
		$this->__testStepEnterMasterPassword();
		// Next.
		$this->clickLink("Next");
		// Test step generate and download key.
		$this->__testStepGenerateAndDownloadKey();
		// We cannot test that it is possible to download the key physically due to driver limitations.
		// Click Next.
		$this->clickLink("Next");
		// Test security token step.
		$this->__testStepSecurityToken();
		// Click Next.
		$this->clickLink("Next");
		// Test enter application password step.
		$this->__testStepLoginRedirection();
	}
}