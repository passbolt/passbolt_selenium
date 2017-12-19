<?php
/**
 * Passbolt ~ Open source password manager for teams
 * Copyright (c) Passbolt SARL (https://www.passbolt.com)
 *
 * Licensed under GNU Affero General Public License version 3 of the or any later version.
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Passbolt SARL (https://www.passbolt.com)
 * @license   https://opensource.org/licenses/AGPL-3.0 AGPL License
 * @link      https://www.passbolt.com Passbolt(tm)
 * @since     2.0.0
 */
namespace App;

use App\Actions\SetupActionsTrait;
use App\Common\Config;
use Facebook\WebDriver\WebDriverBy;
use PHPUnit_Framework_Assert;
use Data\Fixtures\User;

abstract class PassboltSetupTestCase extends PassboltTestCase
{
    use SetupActionsTrait;

    /**
     * @var array various info to check for each section of the setup
     */
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
            'title' => 'Now let\'s setup your passphrase!',
            'subtitle'  => 'Set your passphrase',
            'menu_item' => '3. Set a passphrase'
        ],
        'generate_key_progress' => [
            'title' => 'Give us a second while we crunch them numbers!',
            'subtitle' => 'Generating the secret and public key',
            'menu_item' => '3. Set a passphrase'
        ],
        'generate_key_done' => [
            'title' => 'Success! Your secret key is ready.',
            'subtitle' => 'Let\'s make a backup',
            'menu_item' => '3. Set a passphrase'
        ],
        'import_key_form' => [
            'title' => 'Import an existing key or create a new one!',
            'subtitle' => 'Copy paste your private key below',
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
            'subtitle' => 'Setup is complete',
            'menu_item' => '5. Login !'
        ]
    ];

    /**
     * Assert that the title equals the one given.
     *
     * @param $title
     */
    public function assertTitleEquals($title)
    {
        $elt = $this->findById('js_step_title');
        $this->assertEquals($elt->getText(), $title);
    }

    /**
     * Assert if the given menu is selected.
     *
     * @param $text
     */
    public function assertMenuIsSelected($text)
    {
        $elt = $this->getDriver()->findElement(
            WebDriverBy::xpath(
                "//div[@id = 'js_menu']//a[text()='$text']/.."
            )
        );
        $this->assertElementHasClass(
            $elt,
            'selected'
        );
    }

    /**
     * Wait until the requested section appears.
     *
     * @param $sectionName
     */
    protected function waitForSection($sectionName)
    {
        $timeout = 10;
        if ($sectionName == 'generate_key_done') {
            $timeout = 60;
        }

        try {
            // Wait for section login_redirect.
            $this->waitUntilISee(
                '#js_step_title',
                '/' . $this->getSectionInfo($sectionName, 'title') . '/i',
                $timeout
            );
            $this->waitUntilISee(
                '#js_step_content h3',
                '/' . $this->getSectionInfo($sectionName, 'subtitle') . '/i',
                $timeout
            );
        } catch (Exception $e) {
            // If session is not there, check if we are on the exception page.
            try {
                $this->waitUntilISee(
                    '#js_step_title',
                    '/' . 'Damn' . '/i',
                    $timeout
                );
            }
            catch (Exception $e) {
                PHPUnit_Framework_Assert::fail("Section $sectionName could not be found, and debug couldn't be retrieved");
            }

            // Retrieve debug info.
            $this->waitUntilISee('#show-debug-info');
            $this->click('#show-debug-info');
            $this->waitUntilISee('#debug-info');
            $debug = $this->findById('debug-info')->getText();

            $msg = "Section $sectionName could not be reached. \n Debug: ". print_r($debug, true);
            PHPUnit_Framework_Assert::fail($msg);
        }
    }

    /**
     * Get a section info as
     *
     * @param $sectionName
     *   name of the section
     *
     * @param string $info
     *   information requested. (title, subtitle, etc..)
     *
     * @return mixed
     */
    protected function getSectionInfo($sectionName, $info = '') 
    {
        if (!isset($this->sections[$sectionName])) {
            PHPUnit_Framework_Assert::fail('The section name provided doesnt exist');
        }
        if ($info != '') {
            if (!isset($this->sections[$sectionName][$info])) {
                PHPUnit_Framework_Assert::fail('The info requested doesnt exist in that section');
            }
            return $this->sections[$sectionName][$info];
        }
        return $this->sections[$sectionName];
    }

    /**
     * Scenario: As an AP I should be able to use the domain verification step of the setup
     *
     * Given I am an anonymous user with the plugin on the first page of the setup
     * Then  the button Cancel should not be visible
     * And   The button Next should be disabled
     * And   The domain value should be same as the domain I enter initially
     * When  I check the domain validation checkbox
     * Then  the button Next should be enabled
     */
    protected function completeStepDomainVerification() 
    {
        // Test that button cancel is hidden.
        $this->assertElementHasClass(
            $this->findById('js_setup_cancel_step'),
            'hidden'
        );
        // Test that button Next is disabled.
        $this->assertElementHasClass(
            $this->findById('js_setup_submit_step'),
            'disabled'
        );
        // Test that the domain in the url check textbox is the same as the one configured.
        $domain = $this->findById("js_setup_domain")->getAttribute('value');
        $this->assertEquals(Config::read('passbolt.url'), $domain);

        // Give it time to load the server key.
        $this->waitUntilISee('.why-plugin-wrapper', '/I\'ve checked/i');

        // Test that the server key fingerprint is correct.
        $serverKey = $this->findById("js_setup_key_fingerprint")->getAttribute('value');
        $this->assertEquals(Config::read('passbolt.server_key.fingerprint'), $serverKey);

        // Click on more to read information about the key.
        $this->clickLink('More');

        // Assert that the dialog window is opened.
        $this->waitUntilISee('#dialog-server-key-info');

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
        $this->assertNotVisibleByCss('dialog-server-key-info');

        // If I open the dialog again.
        $this->clickLink('More');

        // And I click the close icon in the dialog.
        $this->findByCss('.dialog-wrapper a.dialog-close')
            ->click();

        // Then I should not see the dialog anymore.
        $this->assertNotVisibleByCss('dialog-server-key-info');

        // Check box domain check.
        $this->checkCheckbox('js_setup_domain_check');
        // Test that button Next is disabled.
        $this->assertElementHasNotClass(
            $this->findById('js_setup_submit_step'),
            'disabled'
        );

    }

    /**
     * Scenario: As an AP I should be able to prepare the creation of my keys
     *
     * Given I am on the step 2 "Create a new key" of the setup
     * And   I should see the step 2 : create a new key
     * And   I should see "John Doe" in the field Owner name
     * And   I should see "johndoe@passbolt.com" in the field email
     * And   I should see that the field email is disabled
     * When  I enter a comment in the comment field of the page
     */
    protected function completeStepPrepareCreateKey($user) 
    {
        // Wait
        $this->waitForSection('generate_key_form');
        // Test that the text corresponding to key section is set.
        $this->assertTitleEquals($this->getSectionInfo('generate_key_form', 'title'));
        // Test that field owner name is set to John Doe.
        $this->assertElementAttributeEquals(
            $this->findById('OwnerName'),
            'value',
            $user['FirstName'] . ' ' . $user['LastName']
        );
        // Test that field owner email is set to johndoe@passbolt.com
        $this->assertElementAttributeEquals(
            $this->findById('OwnerEmail'),
            'value',
            $user['Username']
        );
        // Test that email field is disabled.
        $this->assertElementAttributeEquals(
            $this->findById('OwnerEmail'),
            'disabled',
            'true'
        );

        // Fill master key.
        $this->inputText('KeyComment', 'This is a comment for ' . strtolower($user['FirstName'] . ' ' . $user['LastName']) . ' key');
    }

    /**
     * Scenario: As an AP using the setup, I should be able to enter my passphrase for the protected key.
     * Given I am at the step asking me to enter my passphrase.
     * When  I fill up a passphrase
     * Then  I should see that the strength is getting updated
     * And   I should see that the strength progress bar is getting updated
     * And   I should not see the passphrase in clear
     * When  I click on the show password button
     * Then  I should see the password in clear
     */
    protected function completeStepEnterMasterPassword($user) 
    {
        // Wait until section appears.
        $this->waitForSection('generate_key_master_password');

        // Fill master key.
        $this->inputText('js_field_password', $user['MasterPassword']);

        // Test that complexity has been updated.
        $expectedComplexity = isset($user['PasswordStrength']) ? $user['PasswordStrength'] : 'fair';
        $this->waitUntilISee('#js_user_pwd_strength .complexity-text', "/$expectedComplexity/", 1);

        // Test that progress bar contains class fair.
        $this->assertElementHasClass(
            $this->findByCss('#js_user_pwd_strength .progress .progress-bar'),
            isset($user['PasswordStrength']) ? str_replace(' ', '_', $user['PasswordStrength']) : 'fair'
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
    }

    /**
     * Scenario: As an AP using the setup I should be able to import my own key.
     * Given I am at the step 2 and I select import my key, instead of generating one
     * Then  I should see a textarea to put the key content in it.
     * And   the Next button should be disabled
     * When  I insert a random text in the key field
     * Then  The next button should be enabled
     * When  I click "Next"
     * Then  I should see an error message saying that the key has an invalid format
     * When  I delete the random text from the textarea
     * And   I replace it with a protected key in a proper format
     * And   I click "Next"
     * Then  I should see a different confirmation page with my key information
     * When  I observe this confirmation page
     * Then  I should retrieve my key information
     */
    protected function completeStepImportKey($key = []) 
    {
        // Get the Gpgkey.
        if (empty($key)) {
            $this->fail('The function should be provided a key as argument');
        }

        // Wait until section appears.
        $this->waitForSection('import_key_form');
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
            'Unknown ASCII armor type'
        );
        // Emtpy value.
        $this->findById('js_setup_import_key_text')->clear();
        // Paste a correct key.
        $keyData = file_get_contents($key['filepath']);
        $this->inputText('js_setup_import_key_text', $keyData);
        // Click Next
        $this->clickLink('Next');

        // Wait until section appears.
        $this->waitForSection('import_key_done');

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
     * Scenario: As an AP using the setup I should be able to generate and download the key.
     * Given I am on the step that generates a private key
     * Then  I should see that the key is getting generated, and that the Next button is in processing state
     * When  The key has finished generating
     * Then  I should see that the next button is enabled
     * And   I should see that the title says the key is ready
     * And   There should be a confirmation message
     * And   There should be a download button
     */
    protected function completeStepGenerateAndDownloadKey() 
    {

        // Assert that button submit is in processing state. (generating).
        // The line below breaks for the time being because selenium is too slow.
        // TODO : fix it whenever possible.
        //$this->waitUntilISee('#js_setup_submit_step.processing');

        // Assert I am back to normal state.
        $this->waitUntilISee('#js_setup_submit_step.enabled', null, 20);

        // Wait till the key is generated.
        $this->waitForSection('generate_key_done');

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
            $this->findById('js_setup_submit_step'),
            'enabled'
        );
    }

    /**
     * Scenario: As an AP using the setup, I should be able to choose a security token
     * Given I am at the security token step
     * Then  I should see that a security token code has been chosen for me
     * And   I should see that a security token bg color has been chosen for me
     * And   I should see that a security token text color has been chosen for me
     */
    protected function completeStepSecurityToken() 
    {
        // Wait for section
        $this->waitForSection('security_token');

        // I should see the title.
        $this->assertTitleEquals($this->getSectionInfo('security_token', 'title'));

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
    }

    /**
     * Scenario: As an AP using the setup, I should be redirected to the login page at the end of the setup.
     *
     * Given I am at the last step
     * Then  I should see a message telling me that I am being redirected.
     * And   I should see the login form after I am redirected.
     */
    protected function completeStepLoginRedirection() 
    {
        $this->waitForSection('login_redirect');

        // I should see the subtitle.
        $this->assertTitleEquals($this->getSectionInfo('login_redirect', 'title'));

        // Test that a button is processing.
        $this->assertElementHasClass(
            $this->findById('js_spinner'),
            'processing'
        );

        // I should be on the login page.
        sleep(5);
        $this->getDriver()->switchTo()->activeElement();
        $this->waitUntilISee('.information h2', '/Welcome back!/');

        try{
            $this->findByCss('.users.login.form');
        } catch(Exception $e) {
            $msg = 'At the end of setup there should have been a redirection to the login page';
            PHPUnit_Framework_Assert::fail($msg);
        }
    }

    /**
     * Register steps
     */
    protected function completeRegistration($user = null) 
    {
        if ($user == null) {
            $user = User::get('john');
        }
        // Test step domain verification.
        $this->completeStepDomainVerification();

        // Click Next.
        $this->clickLink("Next");
        // Test that button Next is disabled.
        $this->assertElementHasClass(
            $this->findById('js_setup_submit_step'),
            'processing'
        );
        // test step that prepares key creation.
        $this->completeStepPrepareCreateKey($user);
        // Fill comment.
        $this->clickLink("Next");
        // Test enter passphrase step.
        $this->completeStepEnterMasterPassword($user);
        // Next.
        $this->clickLink("Next");
        // Test step generate and download key.
        $this->completeStepGenerateAndDownloadKey();
        // We cannot test that it is possible to download the key physically due to driver limitations.
        // Click Next.
        $this->clickLink("Next");
        // Test security token step.
        $this->completeStepSecurityToken();
        // Click Next.
        $this->clickLink("Next");
        // Test enter application password step.
        $this->completeStepLoginRedirection();
    }

}