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

        // And the database is in the default state
        $this->PassboltServer->resetDatabase();

        // And I am logged in on the password workspace
        $this->loginAs($user['Username']);

        // When I create a password with very strong complexity
        $password = array(
            'name' => 'strongcomplexity',
            'complexity' => 'very strong',
            'username' => 'supastrong',
            'password' => 'YVhI[[gbPNt5,o{SwA:S&P]@(gdl'
        );
        $this->createPassword($password);

        // When I edit the password I just created
        $elt = $this->driver->findElement(WebDriverBy::xpath("//*[contains(text(),'" . $password['name'] . "')]"));
        $elt->click();
        $this->click('js_wk_menu_edition_button');

        // Then I can see the complexity is set to very strong in the edit password screen
        $this->assertVisible('.edit-password-dialog');
        $this->assertComplexity('very strong');
    }
}
