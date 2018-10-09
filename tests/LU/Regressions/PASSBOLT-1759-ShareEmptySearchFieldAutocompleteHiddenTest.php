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

use App\Actions\PasswordActionsTrait;
use App\Actions\ShareActionsTrait;
use App\Assertions\PasswordAssertionsTrait;
use App\Assertions\ShareAssertionsTrait;
use App\PassboltTestCase;
use Data\Fixtures\User;
use Data\Fixtures\Resource;
use App\Lib\UuidFactory;

use Facebook\WebDriver\WebDriverBy;

class PASSBOLT1759 extends PassboltTestCase
{
    use PasswordActionsTrait;
    use PasswordAssertionsTrait;
    use ShareActionsTrait;
    use ShareAssertionsTrait;

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
        Resource::get(
            array(
            'user' => 'ada',
            'id' => UuidFactory::uuid('resource.id.apache')
            )
        );
        $this->gotoSharePassword(UuidFactory::uuid('resource.id.apache'));

        // And I search a user
        $this->inputText('js-search-aros-input', '@passbolt.com', true);
        $this->click('.security-token');
        $this->waitUntilISee('#js-search-aro-autocomplete.ready');

        // Then I should see results
        $listOfUsers = $this->driver->findElements(WebDriverBy::cssSelector('#js-search-aro-autocomplete ul li'));
        $this->assertNotEquals(0, count($listOfUsers));

        // When I empty the search field
        $this->emptyFieldLikeAUser('js-search-aros-input');
        $this->click('.security-token');

        // Then the autocomplete  should be hidden
        $this->waitUntilIDontSee('#js-search-aro-autocomplete');
    }

}