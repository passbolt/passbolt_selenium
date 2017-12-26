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
 * Bug PASSBOLT-1758 - Regression test
 */
namespace Tests\LU\Regressions;

use App\Actions\PasswordActionsTrait;
use App\Actions\ShareActionsTrait;
use App\Assertions\PasswordAssertionsTrait;
use App\Lib\UuidFactory;
use App\PassboltTestCase;
use Data\Fixtures\User;
use Data\Fixtures\Resource;
use Facebook\WebDriver\WebDriverBy;

class PASSBOLT1758 extends PassboltTestCase
{
    use PasswordActionsTrait;
    use PasswordAssertionsTrait;
    use ShareActionsTrait;

    /**
     * Scenario: As a user I can share a password with other users
     *
     * Given I am Carol
     * And   I am logged in on the password workspace
     * When  I go to the sharing dialog of a password I own
     * And   I search a user by his lastname
     * Then  I should see only one result
     * And   I should see only the user Edit Clarke in the autocomplete list
     *
     * @group LU
     * @group regression
     * @group v2
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
        Resource::get([
            'user' => 'ada',
            'id' => UuidFactory::uuid('resource.id.apache')
        ]);
        $this->gotoSharePassword(UuidFactory::uuid('resource.id.apache'));

        // And I search a user by his lastname
        $userC = User::get('edith');
        $this->goIntoShareIframe();
        $this->inputText('js_perm_create_form_aro_auto_cplt', $userC['LastName'], true);
        $this->click('.security-token');
        $this->goOutOfIframe();

        // I wait the autocomplete box is loaded.
        $this->waitCompletion(10, '#passbolt-iframe-password-share-autocomplete.loaded');

        // Then I should see only one result
        $this->goIntoShareAutocompleteIframe();
        $listOfUsers = $this->driver->findElements(WebDriverBy::cssSelector('ul li'));
        $this->assertEquals(1, count($listOfUsers));

        // And I should see only the user Edit Clarke in the autocomplete list
        $shareWithUserFullName = $userC['FirstName'] . ' ' . $userC['LastName'];
        $this->waitUntilISee('.autocomplete-content', '/' . $shareWithUserFullName . '/i');
    }

}