<?php
/**
 * Bug PASSBOLT-1759 - Regression test
 *
 * @copyright (c) 2017 Passbolt SARL
 * @licence   GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
use Facebook\WebDriver\WebDriverBy;

class PASSBOLT1759 extends PassboltTestCase
{

    /**
     * Scenario: As a user I can share a password with other users
     *
     * Given I am Carol
     * And I am logged in on the password workspace
     * When I go to the sharing dialog of a password I own
     * And I search a user
     * Then I should see results
     * When I empty the search field
     * Then     the autocomplete field should be hidden
     */
    public function testShareSearchUsersFiltersOnName() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Carol
        $user = User::get('ada');

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // When I go to the sharing dialog of a password I own
        Resource::get(
            array(
            'user' => 'ada',
            'id' => Uuid::get('resource.id.apache')
            )
        );
        $this->gotoSharePassword(Uuid::get('resource.id.apache'));

        // And I search a user
        $this->goIntoShareIframe();
        $this->inputText('js_perm_create_form_aro_auto_cplt', '@passbolt.com', true);
        $this->click('.security-token');
        $this->goOutOfIframe();

        // I wait the autocomplete box is loaded.
        $this->waitCompletion(10, '#passbolt-iframe-password-share-autocomplete.loaded');

        // Then I should see results
        $this->goIntoShareAutocompleteIframe();
        $listOfUsers = $this->driver->findElements(WebDriverBy::cssSelector('ul li'));
        $this->assertNotEquals(0, count($listOfUsers));
        $this->goOutOfIframe();

        // When I empty the search field
        $this->goIntoShareIframe();
        $this->emptyFieldLikeAUser('js_perm_create_form_aro_auto_cplt');
        $this->click('.security-token');
        $this->goOutOfIframe();

        // Then the autocomplete field should be hidden
        $this->waitUntilIDontSee('#passbolt-iframe-password-share-autocomplete');
    }

}