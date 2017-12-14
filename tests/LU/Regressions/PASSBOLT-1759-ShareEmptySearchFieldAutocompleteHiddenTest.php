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
/**
 * Bug PASSBOLT-1759 - Regression test
 */
namespace Tests\LU\Regressions;

use App\Actions\ShareActionsTrait;
use App\PassboltTestCase;
use Data\Fixtures\User;
use Data\Fixtures\Resource;
use App\Lib\UuidFactory;

use Facebook\WebDriver\WebDriverBy;

class PASSBOLT1759 extends PassboltTestCase
{
    use ShareActionsTrait;

    /**
     * Scenario: As a user I can share a password with other users
     *
     * Given I am Carol
     * And   I am logged in on the password workspace
     * When  I go to the sharing dialog of a password I own
     * And   I search a user
     * Then  I should see results
     * When  I empty the search field
     * Then  the autocomplete field should be hidden
     *
     * @group LU
     * @group regression
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
            'id' => UuidFactory::uuid('resource.id.apache')
            )
        );
        $this->gotoSharePassword(UuidFactory::uuid('resource.id.apache'));

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