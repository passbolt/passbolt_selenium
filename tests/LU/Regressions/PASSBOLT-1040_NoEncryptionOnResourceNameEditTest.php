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
 * Bug PASSBOLT-1040 - Regression test
 */
namespace Tests\LU\Regressions;

use App\Actions\PasswordActionsTrait;
use App\Assertions\WorkspaceAssertionsTrait;
use App\PassboltTestCase;
use Data\Fixtures\User;
use Data\Fixtures\Resource;

class PASSBOLT1040 extends PassboltTestCase
{
    use PasswordActionsTrait;
    use WorkspaceAssertionsTrait;

    /**
     * Scenario: As a user editing my password encryption should not happen if do not edit the secret
     *
     * @group LU
     * @group regression
     */
    public function testNoEncryptionOnResourceNameEdit() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Ada
        // And I am logged in on the password workspace
        $this->loginAs(User::get('ada'));

        // And I am editing the name, description, uri, username of a password I own
        $resource = Resource::get(
            array(
            'user' => 'ada',
            'permission' => 'owner'
            )
        );
        $r['id'] = $resource['id'];
        $r['description'] = 'this is a new description';
        $r['name'] = 'newname';
        $r['username'] = 'newusername';
        $r['uri'] = 'http://newuri.com';

        $this->gotoEditPassword($r['id']);
        $this->inputText('js_field_name', $r['name']);
        $this->inputText('js_field_username', $r['username']);
        $this->inputText('js_field_uri', $r['uri']);
        $this->inputText('js_field_description', $r['description']);

        // And I click the submit button
        $this->click('.edit-password-dialog input[type=submit]');

        // And I wait until I'm sure the progress dialog didn't appear
        $this->waitUntilIDontSee('#passbolt-iframe-progress-dialog');

        // Then I should see a success notification message saying the password is updated.
        $this->assertNotification('app_resources_edit_success');
    }
}