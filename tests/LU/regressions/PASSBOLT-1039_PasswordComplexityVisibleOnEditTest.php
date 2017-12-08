<?php
/**
 * Bug PASSBOLT-1039 - Regression test
 *
 * @copyright (c) 2017 Passbolt SARL
 * @licence   GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class PASSBOLT1039 extends PassboltTestCase
{
    /**
     * Scenario: As a user I can see the current password complexity when editing a password
     *
     * Given I am Ada
     * And      the database is in the default state
     * And I am logged in on the password workspace
     * When I create a password with very strong complexity
     * And I edit the password I just created
     * Then I can see the complexity is set to very strong in the edit password screen
     */
    public function testEditPasswordComplexityCheck() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Ada
        $user = User::get('ada');
        

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // When I create a password with very strong complexity
        $password = array(
            'name' => 'strongcomplexity',
            'complexity' => 'very strong',
            'username' => 'supastrong',
            'password' => 'YVhI[[gbPNt5,o{SwA:S&P]@(gdl'
        );
        $this->createPassword($password);
        $this->waitCompletion();

        // When I edit the password I just created
        $xpathSelector = "//div[contains(@class, 'tableview-content')]//tr[.//*[contains(text(),'" . $password['name'] . "')]]";
        $resource = $this->findByXpath($xpathSelector);
        $this->gotoEditPassword(str_replace('resource_', '', $resource->getAttribute('id')));

        //$this->assertVisible('.edit-password-dialog');
        $this->goIntoSecretIframe();
        // Then I can see the complexity is set to very strong in the edit password screen
        // TODO : modify this test and uncomment the line below once a solution will be found to store the strength of the passwords.
        //$this->assertComplexity('very strong');
        $this->assertComplexity('not available');

        // Click on th secret field.
        $this->click('js_secret');

        // Leave IFrame.
        $this->goOutOfIframe();

        // Then I see the passphrase dialog
        $this->assertMasterPasswordDialog($user);

        // When I enter the passphrase and click submit
        $this->enterMasterPassword($user['MasterPassword']);

        $this->waitUntilIDontSee('#passbolt-iframe-master-password');

        $this->goIntoSecretIframe();

        $this->waitUntilSecretIsDecryptedInField();

        $this->assertComplexity('very strong');
        $this->goOutOfIframe();

    }
}
