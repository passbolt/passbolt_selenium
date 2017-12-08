<?php
/**
 * Bug PASSBOLT-1377 - Regression test
 *
 * @copyright (c) 2017 Passbolt SARL
 * @licence   GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class PASSBOLT1377 extends PassboltTestCase
{

    /**
     * Scenario: As a user I can login & logout multiple times
     *
     * Given        I am ada
     *
     * [LOOP]
     * When         I login
     * And          I logout
     * Then         I should see the login page
     * [END_LOOP]
     */
    public function testLoginLogoutMultipleTimes() 
    {
        // Given I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

        for ($i=0; $i<5; $i++) {
            // When I am logged in on the user workspace
            $this->loginAs($user, false);

            // And I logout
            $this->logout();

            // Then  I should be redirected to the login page
            $this->waitUntilISee('.plugin-check.' . $this->_browser['type'] . '.success');
        }
    }

    /**
     * Scenario: As LU I can create a password mutliple times
     *
     * Given        I am logged in as ada in the user workspace
     *
     * [LOOP]
     * When         I am creating a password
     * Then         I should expect the password has been created with success
     * [END_LOOP]
     */
    public function testCreatePasswordMultipleTimes() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am logged in as ada in the user workspace
        $user = User::get('ada');

        $this->loginAs($user);

        for ($i=0; $i<5; $i++) {
            // And I am creating the password
            // Then  I can see a success notification
            $password = array(
            'name' => 'name_' . $i,
            'username' => 'username_' . $i,
            'password' => 'password_' . $i
            );
            $this->createPassword($password);

            // Wait until notification disappears.
            $this->waitUntilNotificationDisappears('app_resources_add_success');
        }
    }

    /**
     * @group no-saucelabs
     *
     * Scenario: As LU I can edit a password mutliple times
     *
     * Given        I am logged in as ada in the user workspace
     *
     * [LOOP]
     * When         I am editing a password I own
     * Then         I should expect the password has been edited with success
     * [END_LOOP]
     */
    public function testEditPasswordMultipleTimes() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am logged in as ada in the user workspace
        $user = User::get('ada');

        $this->loginAs($user);

        $resource = Resource::get(
            array(
            'user' => 'ada',
            'permission' => 'owner'
            )
        );

        for ($i=0; $i<5; $i++) {
            // And I am editing the secret of a password I own
            // Then  I can see a success notification
            $r['id'] = $resource['id'];
            $r['password'] = 'password_' . $i;
            $this->editPassword($r, $user);

            // Wait until notification disappears.
            $this->waitUntilNotificationDisappears('app_resources_edit_success');
        }
    }

    /**
     * Scenario: As LU I can share a password mutliple times
     *
     * Given        I am logged in as ada in the user workspace
     *
     * [LOOP]
     * When         I am sharing a password I own
     * Then         I should expect the password has been shared with success
     * [END_LOOP]
     */
    public function testSharePasswordMultipleTimes() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am logged in as ada in the user workspace
        $user = User::get('ada');

        $this->loginAs($user);

        $resource = Resource::get(
            array(
            'id' => Uuid::get('resource.id.apache'),
            'user' => 'ada',
            )
        );
        $shareWith = [
        'frances',
        'edith',
        'admin'
        ];

        for ($i=0; $i<count($shareWith); $i++) {
            // And I am editing the secret of a password I own
            // Then  I can see a success notification
            $r['id'] = $resource['id'];
            $r['password'] = 'password_' . $i;
            $this->sharePassword($resource, $shareWith[$i], $user);
            $this->waitUntilNotificationDisappears('app_share_update_success');
        }
    }
}