<?php
/**
 * Bug PASSBOLT-1040 - Regression test
 *
 * @copyright    (c) 2015-present Bolt Software Pvt. Ltd.
 * @licence      GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
 */
class PASSBOLT1040 extends PassboltTestCase
{
    /**
     * Scenario: As a user editing my password encryption should not happen if do not edit the secret
     * @throws Exception
     */
    public function testNoEncryptionOnResourceNameEdit() {
        // Given I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

        // And the database is in the default state
        $this->PassboltServer->resetDatabase();

        // And I am logged in on the password workspace
        $this->loginAs($user['Username']);

        // And I am editing the name, description, uri, username of a password I own
        $resource = Resource::get(array(
            'user' => 'ada',
            'permission' => 'owner'
        ));
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

        // Then I should not see the encryption in progress dialog
        try {
            $this->waitUntilISee('passbolt-iframe-progress-dialog',null,3);
        } catch(exception $e){};
        $this->assertNotVisible('passbolt-iframe-progress-dialog');

    }
}