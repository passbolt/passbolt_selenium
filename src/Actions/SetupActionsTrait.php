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
namespace App\Actions;

trait SetupActionsTrait
{
    /**
     * Register a user using the registration form.
     *
     * @param $firstname
     * @param $lastname
     * @param $username
     */
    public function registerUser($firstname, $lastname, $username)
    {
        // Register user.
        $this->getUrl('register');
        $this->inputText('profile-first-name', $firstname);
        $this->inputText('profile-last-name', $lastname);
        $this->inputText('username', $username);
        $this->click('#disclaimer');
        $this->click('.submit-wrapper input');
        $this->waitUntilISee('.page.register.thank-you');
    }

    /**
     * Complete the setup with the data given in parameter
     *
     * @param $data
     *  - username
     *  - masterpassword
     */
    public function completeSetupWithKeyGeneration($data) 
    {
        // Check that we are on the setup page.
        $this->waitUntilISee('.plugin-check-wrapper', '/Plugin check/');
        // Wait for the checkbox to appear.
        $this->waitUntilISee('#js_setup_domain_check');
        // Check box domain check.
        $this->checkCheckbox('js_setup_domain_check');
        // Click Next.
        $this->clickLink("Next");
        // Wait
        $this->waitUntilISee('#js_step_content h3', '/Create a new key/i');
        // Fill master key.
        $this->inputText('KeyComment', 'This is a comment for john doe key');
        // Click Next.
        $this->clickLink("Next");
        // Check that we are now on the passphrase page
        $this->waitUntilISee('#js_step_title', '/Now let\'s setup your passphrase!/i');
        // Fill master key.
        $this->inputText('js_field_password', $data['masterpassword']);
        // Click Next.
        $this->waitUntilISee('#js_setup_submit_step.enabled');
        $this->clickLink("Next");
        // Wait until we see the title Master password.
        $this->waitUntilISee('#js_step_title', '/Success! Your secret key is ready./i', 20);
        // Press Next.
        $this->clickLink("Next");
        // Wait.
        $this->waitUntilISee('#js_step_content h3', '/Set a security token/i');
        // Press Next.
        $this->clickLink("Next");
        // Fill up password.
        $this->waitUntilISee('#js_step_content h3', '/Setup is complete/i');
        $this->getUrl('auth/login');
        // Wait until I see the login page.
        $this->waitUntilISee('.information h2', '/Welcome back!/i');
    }

    /**
     * Complete the setup with key import
     *
     * @param $data
     *  - private_key
     */
    public function completeSetupWithKeyImport($data) 
    {
        // Check that we are on the setup page.
        $this->waitUntilISee('.plugin-check-wrapper', '/Plugin check/');
        // Wait for the checkbox to appear.
        $this->waitUntilISee('#js_setup_domain_check');
        // Check box domain check.
        $this->checkCheckbox('js_setup_domain_check');
        // Click Next.
        $this->clickLink("Next");
        // Wait
        $this->waitUntilISee('#js_step_content h3', '/Create a new key/i');
        // Click on import.
        $this->clickLink('import');
        // Wait until section is displayed.
        $this->waitUntilISee('#js_step_title', '/Import an existing key or create a new one!/i');
        // Enter key in the field.
        $this->inputText('js_setup_import_key_text', $data['private_key']);
        // Click Next
        $this->clickLink('Next');
        // Wait until the key is imported
        $this->waitUntilISee('#js_step_title', '/Let\'s make sure you imported the right key/i');
        // Press Next.
        $this->clickLink("Next");
        // Wait.
        $this->waitUntilISee('#js_step_content h3', '/Set a security token/i');
        // Press Next.
        $this->clickLink("Next");
        // Fill up password.
        $this->waitUntilISee('#js_step_content h3', '/Setup is complete/i');
        $this->getUrl('login');
        // Wait until I see the login page.
        $this->waitUntilISee('.information h2', '/Welcome back!/i');
    }

    /**
     * go To Setup page.
     *
     * @param string $username
     * @param string $pluginCheck The plugin state. Can be success, error or warning. Default success.
     */
    public function goToSetup($username, $pluginCheck = 'success')
    {
        // Get last email.
        $this->getUrl('seleniumtests/showlastemail/' . urlencode($username));

        // Remember setup url. (We will use it later).
        $linkElement = $this->findLinkByText('get started');
        $setupUrl = $linkElement->getAttribute('href');

        // Go to url remembered above.
        $this->getUrl($setupUrl);

        // Assert the plugin check section
        switch($pluginCheck) {
            case 'success':
                // Wait for the redirection from setup/install (API) to data/setup.html (plugin)
                $this->waitUntilUrlMatches('data/setup.html');
                $this->waitUntilISee('.plugin-check-wrapper .plugin-check.success', '/Nice one! The plugin is installed and up to date/i');
                break;
            case 'warning':
                // Wait for the redirection from setup/install (API) to data/setup.html (plugin)
                $this->waitUntilUrlMatches('data/setup.html');
                $this->waitUntilISee('.plugin-check-wrapper .plugin-check.warning', '/Warning: The plugin is already configured/i');
                break;
            case 'error':
                $this->waitUntilISee('.plugin-check-wrapper .plugin-check.warning', '/A web extension is required to use passbolt./i');
                break;
        }
    }

}