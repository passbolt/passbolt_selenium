<?php
/**
 * Bug PASSBOLT-2031 - Regression test
 *
 * @copyright (c) 2017 Passbolt SARL
 * @licence   GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
use Facebook\WebDriver\WebDriverBy;

class PASSBOLT2031 extends PassboltTestCase
{

    /**
     * Scenario: As a user I can share a password with multiple users
     *
     * Given I am Carol
     * And I am logged in on the password workspace
     * When I go to the sharing dialog of a password I own
     * And I give read access to multiple users/groups
     * And I click on the save button
     * And I see the passphrase dialog
     * And I enter the passphrase and click submit
     * Then  I wait until I don't see  the encryption dialog anymore.
     * And I can see the new permissions in sidebar
     */
    public function testShareWithMultipleUsers() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Carol
        $user = User::get('ada');


        // And I am logged in on the password workspace
        $this->loginAs($user);

        // When I go to the sharing dialog of a password I own
        $resource = Resource::get(
            array(
            'user' => 'ada',
            'id' => Uuid::get('resource.id.apache')
            )
        );
        $this->gotoSharePassword(Uuid::get('resource.id.apache'));

        // And I give read access to multiple users/groups
        $this->addTemporaryPermission($resource, 'Accounting', $user);
        $this->addTemporaryPermission($resource, 'Freelancer', $user);
        $this->addTemporaryPermission($resource, 'grace', $user);
        $this->addTemporaryPermission($resource, 'ping', $user);

        // And I click on the save button
        $this->click('js_rs_share_save');

        // And I see the passphrase dialog
        $this->assertMasterPasswordDialog($user);

        // And I enter the passphrase and click submit
        $this->enterMasterPassword($user['MasterPassword']);

        // Then  I wait until I don't see  the encryption dialog anymore.
        $this->waitUntilIDontSee('#passbolt-iframe-progress-dialog');
        $this->waitCompletion();

        // And I can see the new permissions in sidebar
        $this->assertPermissionInSidebar('Accounting', 'can read');
        $this->assertPermissionInSidebar('Freelancer', 'can read');
        $this->assertPermissionInSidebar('grace', 'can read');
        $this->assertPermissionInSidebar('ping', 'can read');
    }

}