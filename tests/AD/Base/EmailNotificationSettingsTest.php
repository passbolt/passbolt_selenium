<?php
/**
 * Passbolt ~ Open source password manager for teams
 * Copyright (c) Passbolt SA (https://www.passbolt.com)
 *
 * Licensed under GNU Affero General Public License version 3 of the or any later version.
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Passbolt SA (https://www.passbolt.com)
 * @license   https://opensource.org/licenses/AGPL-3.0 AGPL License
 * @link      https://www.passbolt.com Passbolt(tm)
 * @since     2.10.0
 */

/**
 * Feature: As an administrator I can select which notifications are sent.
 *
 * Scenarios :
 *  - As an administrator I can select which information is shown in the email notifications
 *  - As an administrator I can select which notifications are sent
 *  - As an administrator I get a warning when I already use file based settings
 *  - As an administrator I get a warning when I have settings both in the config file and database
 *
 */

namespace Tests\AD\Base;

use App\Actions\ConfirmationDialogActionsTrait;
use App\Actions\GroupActionsTrait;
use App\Actions\PasswordActionsTrait;
use App\Actions\WorkspaceActionsTrait;
use App\Assertions\GroupAssertionsTrait;
use App\Assertions\PasswordAssertionsTrait;
use App\Assertions\WorkspaceAssertionsTrait;
use App\Common\Asserts\VisibilityAssertionsTrait;
use App\Common\Servers\PassboltServer;
use App\PassboltTestCase;
use Data\Fixtures\User;

class EmailNotificationSettingsTest extends PassboltTestCase
{
    use GroupAssertionsTrait;
    use GroupActionsTrait;
    use VisibilityAssertionsTrait;
    use WorkspaceActionsTrait;
    use WorkspaceAssertionsTrait;
    use PasswordAssertionsTrait;
    use PasswordActionsTrait;
    use ConfirmationDialogActionsTrait;

    /**
     * Scenario: As an administrator I can select which information is shown in the email notifications
     *
     * Given that I am logged in as an administrator
     * When I click on administration link in the top navigation bar
     * And  I click on email notifications link in the left panel
     * And  I can see the “Email content visibility” subtitle
     * And  I can see that all the toggle options are enabled by default
     * And  I can see that the save button is disabled
     * When I click on the “username” toggle
     * Then I can see the save button is enabled
     * When I click save
     * Then I can see a success notification message
     * When I go to the password workspace
     * And  I create a resource
     * And  I go to the email queue for this user
     * Then I can see username is not included in the email
     *
     * @group pro-only
     * @group AD
     * @group notification
     * @group email-notification
     * @group v2
     */
    public function testEmailNotificationsCanChangeShowSetting()
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am logged in as admin
        $user = User::get('admin');
        $this->loginAs($user);

        // When I click on administration link in the top navigation bar
        $this->gotoWorkspace('administration');

        // And I click on email notifications link in the left panel
        $linkCssSelector = '#js_app_nav_left_email_notification_link a';
        $this->waitUntilISee($linkCssSelector);
        $this->click($linkCssSelector);
        $this->waitCompletion();

        $formSelector = '#js-email-notification-settings-form';

        // Wait for the form to load
        $this->waitUntilISee($formSelector . '.ready');

        $settingsForm = $this->findByCss($formSelector);

        // And I can see the "Email content visibility" subtitle
        $this->assertElementContainsText(
            $settingsForm,
            'Email content visibility'
        );

        $checkboxes = $this->findAllByCss('.toggle-switch-checkbox');

        $expectedNoOfCheckboxes = count($this->_getDefaultConfigs());

        $this->assertEquals($expectedNoOfCheckboxes, count($checkboxes));

        // And	I can see that all the toggle options are enabled by default
        foreach ($checkboxes as $checkbox) {
            $this->assertTrue($checkbox->isSelected());
        }

        // And	I can see that the save button is disabled
        $this->waitUntilDisabled("js-email-notification-settings-save-button");

        $this->waitUntilISee('#js-show-username-toggle-button');

        // I click on the “username” toggle
        $this->click("#js-show-username-toggle-button .toggle-switch-button");

        // Then	I can see the save button is enabled
        $this->assertNotVisibleByCss('#js-email-notification-settings-save-button.disabled');
        $this->assertVisible('js-email-notification-settings-save-button');

        // When I click save
        $this->click('#js-email-notification-settings-save-button');

        // Then I can see a success notification message
        $this->assertNotification('app_notificationorgsettings_post_success');

        // When	I go to the password workspace
        // And	I create a resource
        $this->createPassword([
            'name' => 'Test Password',
            'username' => 'admin',
            'uri' => 'http://www.google.com',
            'password' => '@dm!n',
        ], $user);

        // And	I go to the email queue for this user
        $this->getUrl('seleniumtests/showlastemail/' . $user['Username']);

        // Then	I can see username is not included in the email
        $this->assertElementNotContainText('#bodyTable', 'Username');
    }

    /**
     * Scenario: As an administrator I can select which notifications are sent
     *
     * Given that I am logged in as an administrator
     * And I go to the email notification settings administration screen
     * Then    I can see the “Email notifications” in breadcrumb
     * And    I can see all the toggle options are enabled by default
     * And    I can see at least 12 settings items
     * When    I click on the “when you create a password” label
     * Then I can see the toggle is disabled
     * When I click on the toggle next to the “when a password is deleted” label
     * Then    I can see the toggle is disabled
     * When I click save
     * Then I can see a success notification message
     * When I go the password workspace
     * And    I select a password
     * And    I click delete
     * Then    I can see there is no related email notification in the email queue
     *
     * @group pro-only
     * @group AD
     * @group notification
     * @group email-notification
     * @group v2
     */
    public function testEmailNotificationsCanChangeSendSetting()
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given that I am logged in as an administrator
        $user = User::get('admin');
        $this->loginAs($user);

        // And I go to the email notification settings administration screen
        $this->gotoWorkspace('administration');
        $linkCssSelector = '#js_app_nav_left_email_notification_link a';
        $this->waitUntilISee($linkCssSelector);
        $this->click($linkCssSelector);
        $this->waitCompletion();

        // wait for the breadcrumbs to load
        $this->waitUntilISee('#js_wsp_administration_breadcrumb');

        $lastBreadCrumbChild = $this->findByCss("#js_wsp_administration_breadcrumb li:last-child");

        // Then	I can see the “Email Notification settings” in breadcrumb
        $this->assertElementContainsText($lastBreadCrumbChild, "Email Notification Settings");

        $formSelector = '#js-email-notification-settings-form';

        // Wait for the form to load
        $this->waitUntilISee($formSelector . '.ready');

        $checkboxes = $this->findAllByCss('.toggle-switch-checkbox');

        // And	I can see that all the toggle options are enabled by default
        foreach ($checkboxes as $checkbox) {
            $this->assertTrue($checkbox->isSelected());
        }

        $expectedNoOfCheckboxes = count($this->_getDefaultConfigs());

        // And 	I can see at least 12 settings items
        $this->assertEquals($expectedNoOfCheckboxes, count($checkboxes));

        $this->waitUntilISee('#js-send-password-create-toggle-button');

        // When	I click on the “when you create a password” label
        $this->click("#js-send-password-create-toggle-button label");

        // Then I can see the toggle is disabled
        $this->waitUntilIDontSee('#js-send-password-create-toggle-button .toggle-switch-checkbox:checked');

        // When I click on the toggle next to the “when a password is deleted” label
        $this->click("#js-send-password-delete-toggle-button label");

        // Then I can see the toggle is disabled
        $this->waitUntilIDontSee('#js-send-password-delete-toggle-button .toggle-switch-checkbox:checked');

        // When I click save
        $this->click('#js-email-notification-settings-save-button');

        // Then I can see a success notification message
        $this->assertNotification('app_notificationorgsettings_post_success');

        // When I go the password workspace
        $this->gotoWorkspace('password');

        $resourceId = $this->createPassword([
            'name' => 'Test Password',
            'username' => 'admin',
            'uri' => 'http://www.google.com',
            'password' => '@dm!n',
        ], $user);

        // And I click delete
        $this->click('js_wk_menu_more_button');
        $this->clickLink('delete');
        $this->confirmActionInConfirmationDialog();

        // And I go to the email queue for this user
        $this->getUrl('seleniumtests/showlastemail/' . $user['Username']);

        // Then	I can see there is no related email notification in the email queue
        $this->assertElementContainsText('#content', 'No email was sent to this user.');
    }

    /**
     * Scenario: As an administrator I get a warning when I already use file based settings
     *
     * Given that I am logged in as an administrator
     * And  I have already configured email notifications settings using environment variable
     * And  I go to the email notification settings administration screen
     * Then I can see a warning banner at the top
     *
     * @group pro-only
     * @group AD
     * @group notification
     * @group email-notification
     * @group v2
     */
    public function testEmailNotificationsCanDetectFileConfigExists()
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        PassboltServer::setExtraConfig([
            'passbolt' => [
                'email' => [
                    'show' => [
                        'comment' => false
                    ]
                ]
            ]
        ]);

        // Given I am logged in as admin
        $user = User::get('admin');
        $this->loginAs($user);

        // And	I go to the email notification settings administration screen
        $this->gotoWorkspace('administration');
        $linkCssSelector = '#js_app_nav_left_email_notification_link a';
        $this->waitUntilISee($linkCssSelector);
        $this->click($linkCssSelector);
        $this->waitCompletion();

        // Then I can see a warning banner at the top
        $this->waitUntilISee('#email-notification-fileconfig-exists-banner');

        $banner = $this->findByCss('#email-notification-fileconfig-exists-banner');

        $this->assertElementContainsText($banner, 'You seem to have Email Notification Settings defined in your passbolt.php (or via environment variables). Submitting the form will overwrite those settings with the ones you choose in the form below.');

        PassboltServer::resetExtraConfig();
    }

    /**
     * Scenario: As an administrator I get a warning when I have settings both in the config file and database
     *
     * Given that I am logged in as an administrator
     * And  I have already configured email notifications settings using environment variable
     * And  I have already configured email notifications settings using database
     * And  I go to the email notification settings administration screen
     * Then I can see a warning banner at the top
     *
     * @group pro-only
     * @group AD
     * @group notification
     * @group email-notification
     * @group v2
     */
    public function testEmailNotificationsCanDetectDoubleConfiguration()
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        PassboltServer::setExtraConfig([
            'passbolt' => [
                'email' => [
                    'show' => [
                        'comment' => false
                    ]
                ]
            ]
        ]);

        // Given I am logged in as admin
        $user = User::get('admin');
        $this->loginAs($user);

        // And	I go to the email notification settings administration screen
        $this->gotoWorkspace('administration');
        $linkCssSelector = '#js_app_nav_left_email_notification_link a';
        $this->waitUntilISee($linkCssSelector);
        $this->click($linkCssSelector);
        $this->waitCompletion();

        $this->assertNotVisibleByCss('#email-notification-setting-overridden-banner');

        $formSelector = '#js-email-notification-settings-form';

        // Wait for the form to load
        $this->waitUntilISee($formSelector . '.ready');

        $this->waitUntilDisabled("js-email-notification-settings-save-button");
        $this->waitUntilISee('#js-show-username-toggle-button');
        $this->click("#js-show-username-toggle-button .toggle-switch-button");
        $this->click('#js-email-notification-settings-save-button');

        // Then	I can see a warning banner at the top
        $this->waitUntilISee('#email-notification-setting-overridden-banner');

        $banner = $this->findByCss('#email-notification-setting-overridden-banner');

        $this->assertElementContainsText($banner, 'Settings have been found in your database as well as in your passbolt.php (or environment variables). The settings displayed in the form below are the one stored in your database and have precedence over others.');

        PassboltServer::resetExtraConfig();
    }

    /**
     * Get default config
     *
     * @return array
     */
    private function _getDefaultConfigs()
    {
        return [
            'show_comment' => true,
            'show_description' => true,
            'show_secret' => true,
            'show_uri' => true,
            'show_username' => true,
            'send_comment_add' => true,
            'send_password_create' => true,
            'send_password_share' => true,
            'send_password_update' => true,
            'send_password_delete' => true,
            'send_user_create' => true,
            'send_user_recover' => true,
            'send_group_delete' => true,
            'send_group_user_add' => true,
            'send_group_user_delete' => true,
            'send_group_user_update' => true,
            'send_group_manager_update' => true,
        ];
    }
}
