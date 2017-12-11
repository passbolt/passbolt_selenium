<?php
/**
 * Bug PASSBOLT-1758 - Regression test
 *
 * @copyright (c) 2017 Passbolt SARL
 * @licence   GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
use Facebook\WebDriver\WebDriverBy;

class PASSBOLT1758 extends PassboltTestCase
{

    /**
     * Scenario: As a user I can share a password with other users
     *
     * Given I am Carol
     * And I am logged in on the password workspace
     * When I go to the sharing dialog of a password I own
     * And I search a user by his lastname
     * Then  I should see only one result
     * And I should see only the user Edit Clarke in the autocomplete list
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

        // And I search a user by his lastname
        $userC = User::get('edith');
        $this->goIntoShareIframe();
        $this->inputText('js_perm_create_form_aro_auto_cplt', $userC['LastName'], true);
        $this->click('.security-token');
        $this->goOutOfIframe();

        // I wait the autocomplete box is loaded.
        $this->waitCompletion(10, '#passbolt-iframe-password-share-autocomplete.loaded');

        // Then  I should see only one result
        $this->goIntoShareAutocompleteIframe();
        $listOfUsers = $this->driver->findElements(WebDriverBy::cssSelector('ul li'));
        $this->assertEquals(1, count($listOfUsers));

        // And I should see only the user Edit Clarke in the autocomplete list
        $shareWithUserFullName = $userC['FirstName'] . ' ' . $userC['LastName'];
        $this->waitUntilISee('.autocomplete-content', '/' . $shareWithUserFullName . '/i');
    }

}