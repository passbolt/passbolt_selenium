<?php
/**
 * Bug PASSBOLT-1039 - Regression test
 *
 * @copyright    (c) 2015-present Bolt Software Pvt. Ltd.
 * @licence      GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
 */
class PASSBOLT1039 extends PassboltTestCase
{
    /**
     * Scenario: As a user I can see the current password complexity when editing a password
     *
     * Given    I am Ada
     * And      the database is in the default state
     * And      I am logged in on the password workspace
     * When     I create a password with very strong complexity
     * And      I edit the password I just created
     * Then     I can see the complexity is set to very strong in the edit password screen
     */
    public function testEditPasswordComplexityCheck() {
        // Given I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

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
	    // Sorry for that :(
	    sleep(3);

        // When I edit the password I just created
	    $xpathSelector = "//div[contains(@class, 'tableview-content')]//tr//td[.//*[contains(text(),'" . $password['name'] . "')]]";
	    $resource = $this->findByXpath($xpathSelector);

        $resource->click();
        $this->click('js_wk_menu_edition_button');
	    // I already put 100Rs in the douchebag box for that too.
	    sleep(3);

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

      // Then I see the master password dialog
      $this->assertMasterPasswordDialog($user);

      // When I enter the master password and click submit
      $this->enterMasterPassword($user['MasterPassword']);

      $this->waitUntilIDontSee('passbolt-iframe-master-password');

      $this->goIntoSecretIframe();

	    $this->waitUntilSecretIsDecryptedInField();

      $this->assertComplexity('very strong');
      $this->goOutOfIframe();

	    // And the database is in the default state
	    $this->resetDatabase();

    }
}
