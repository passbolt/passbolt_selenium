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
 * Feature: As a user I can edit a password
 *
 * Scenarios:
 * As a user I can edit a password using the edit button in the action bar
 * As a user I can edit a password using the right click contextual menu
 * As a user I can open close the edit password dialog
 * As a user I can see the edit password dialog content
 * As a user I can edit a password by using keyboard shortcuts only
 * As a user I can edit the name of a password I have own
 * As a user I can edit the description of a password I have own
 * As a user I can edit the uri of a password I have own
 * As a user I can see the current password complexity when editing a password
 * As a user I can edit the secret of a password I have own
 * As a user editing my password I can use the button to view my secret in clear text
 * As a user editing my password I can generate a new random password automatically
 * As a user I can not edit a password I have only read access to
 * As user B I can see the changes are reflected when user A is editing a password we share
 * As a user I can see error messages when editing a password with wrong inputs
 * As a user I receive an email notification on a password update
 * As LU I can use passbolt on multiple windows and edit password
 * As LU I should be able to edit a password after I restart the browser
 * As LU I should be able to edit a password after I close and restore the passbolt tab
 */
namespace Tests\LU\Base;

use App\Actions\ConfirmationDialogActionsTrait;
use App\Actions\MasterPasswordActionsTrait;
use App\Actions\PasswordActionsTrait;
use App\Actions\SidebarActionsTrait;
use App\Actions\WorkspaceActionsTrait;
use App\Assertions\ClipboardAssertions;
use App\Assertions\ConfirmationDialogAssertionsTrait;
use App\Assertions\MasterPasswordAssertionsTrait;
use App\Assertions\PasswordAssertionsTrait;
use App\Assertions\WorkspaceAssertionsTrait;
use App\PassboltTestCase;
use Data\Fixtures\User;
use Data\Fixtures\Resource;
use Data\Fixtures\SystemDefaults;

class PasswordEditTest extends PassboltTestCase
{
    use ClipboardAssertions;
    use ConfirmationDialogActionsTrait;
    use ConfirmationDialogAssertionsTrait;
    use MasterPasswordActionsTrait;
    use MasterPasswordAssertionsTrait;
    use PasswordActionsTrait;
    use PasswordAssertionsTrait;
    use SidebarActionsTrait;
    use WorkspaceActionsTrait;
    use WorkspaceAssertionsTrait;

    /**
     * Scenario: As a user I can edit a password using the edit button in the action bar
     *
     * Given I am Ada
     * And   I am logged in on the password workspace
     * Then  I can see the edit password button is disabled
     * When  I click on a password I own
     * Then  I can see the edit button is enabled
     * When  I click on the edit button
     * Then  I can see the edit password dialog
     *
     * @group LU
     * @group password
     * @group password-edit
     * @group v2
     * @group saucelabs
     */
    public function testEditPasswordButton() 
    {
        // Given I am Ada
        // And I am logged in on the password workspace
        $this->loginAs(User::get('ada'));

        // Then I can see the edit password button is disabled
        $this->assertVisible('js_wk_menu_edition_button');
        $this->assertVisibleByCss('#js_wk_menu_edition_button.disabled');

        // When I click on a password I own
        $resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));
        $this->clickPassword($resource['id']);

        // Then I can see the edit button is enabled
        $this->assertNotVisibleByCss('#js_wk_menu_edition_button.disabled');
        $this->assertVisible('js_wk_menu_edition_button');

        // When I click on the edit button
        $this->click('js_wk_menu_edition_button');

        // Then I can see the edit password dialog
        $this->assertVisibleByCss('.edit-password-dialog');
    }

    /**
     * Scenario: As a user I can edit a password using the right click contextual menu
     *
     * Given I am Ada
     * And   I am logged in on the password workspace
     * When  I right click on a password I own
     * Then  I can see the contextual menu
     * And   I can see the the edit option is enabled
     * When  I click on the edit link in the contextual menu
     * Then  I can see the edit password dialog
     *
     * @group LU
     * @group password
     * @group password-edit
     * @group v2
     */
    public function testEditPasswordRightClick() 
    {
        // Given I am Ada
        // And I am logged in on the password workspace
        $this->loginAs(User::get('ada'));

        // When I right click on a password I own
        $resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));
        $this->rightClickPassword($resource['id']);

        // Then I can see the contextual menu
        $this->assertVisible('js_contextual_menu');

        // And I can see the the edit option is enabled
        // @TODO PASSBOLT-1028

        // When I click on the edit link in the contextual menu
        $this->click('#js_password_browser_menu_edit a');

        // Then I can see the edit password dialog
        $this->assertVisibleByCss('.edit-password-dialog');
    }

    /**
     * Scenario: As a user I can open close the edit password dialog
     *
     * Given I am Ada
     * And   I am logged in on the password workspace
     * And   I am editing a password I own
     * When  I click on the cancel button
     * Then  I do not see the edit password dialog
     * When  I reopen the edit password dialog
     * And   I click on the close dialog button (in the top right corner)
     * Then  I do not see the edit password dialog
     * When  I reopen the edit password dialog
     * And   I press the escape button
     * Then  I do not see the edit password dialog
     *
     * @group LU
     * @group password
     * @group password-edit
     * @group v2
     */
    public function testEditPasswordDialogOpenClose() 
    {
        // Given I am Ada
        // And I am logged in on the password workspace
        $this->loginAs(User::get('ada'));

        // And I am editing a password I own
        $resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));

        $this->gotoEditPassword($resource['id']);

        // When I click on the cancel button
        $this->click('.edit-password-dialog .js-dialog-cancel');

        // Then I do not see the edit password dialog
        $this->assertNotVisibleByCss('.edit-password-dialog');

        // When I reopen the edit password dialog
        $this->click('js_wk_menu_edition_button');
        $this->waitCompletion();
        $this->assertVisibleByCss('.edit-password-dialog');

        // And I click on the close dialog button (in the top right corner)
        $this->click('.edit-password-dialog .dialog-close');

        // Then I do not see the edit password dialog
        $this->assertNotVisibleByCss('.edit-password-dialog');

        // When I reopen the edit password dialog
        $this->click('js_wk_menu_edition_button');
        $this->waitCompletion();
        $this->assertVisibleByCss('.edit-password-dialog');

        // And I press the escape button
        $this->pressEscape();

        // Then I do not see the edit password dialog
        $this->assertTrue($this->isNotVisible('.edit-password-dialog'));
    }

    /**
     * Scenario: As a user I can see the edit password dialog
     *
     * Given I am Ada
     * And   I am logged on the password workspace
     * And   I am editing a password I own
     * Then  I can see the edit password dialog
     * And   I can see the title is set to "edit password"
     * And   I can see the name of the resource after the title
     * And   I can see the close dialog button
     * And   I can see the edit tab is selected
     * And   I can see the share tab is not selected
     * And   I can see the name input and label is marked as mandatory
     * And   I can see the resource name in the text input
     * And   I can see the url text input and label
     * And   I can see the resource url in the text input
     * And   I can see the username text input and label marked as mandatory
     * And   I can see the resource ursername in the text input
     * And   I can see the password iframe
     * And   I can see the iframe label
     * When  I switch to the password iframe
     * And   I can see the password input
     * And   I can not see the password in cleartext
     * And   I can see the security token
     * And   I can see the view password button
     * And   I can see the generate password button
     * And   I can see the complexity meter
     * And   I can see the complexity textual indicator
     * When  I switch back out of the password iframe
     * And   I can see the description textarea and label
     * And   I can see the resource description in the textarea
     * And   I can see the save button
     * And   I can see the cancel button
     *
     * @group LU
     * @group password
     * @group password-edit
     * @group v2
     */
    public function testEditPasswordDialogView() 
    {
        // Given I am Ada
        // And I am logged in on the password workspace
        $user = User::get('ada');
        $this->loginAs($user);

        // And I am editing a password I own
        $resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));
        $this->gotoEditPassword($resource['id']);

        // Then I can see the edit password dialog
        $this->assertVisibleByCss('.edit-password-dialog');

        // And I can see the title is set to "edit password"
        $this->assertElementContainsText(
            $this->findByCss('.edit-password-dialog h2'),
            'Edit'
        );

        // And I can see the name of the resource after the title
        $this->assertElementContainsText(
            $this->findByCss('.edit-password-dialog h2 .dialog-header-subtitle'),
            $resource['name']
        );

        // And I can see the close dialog button
        $this->assertVisibleByCss('.edit-password-dialog .dialog-close');

        // And I can see the edit tab is selected
        $this->assertElementContainsText(
            $this->findByCss('.edit-password-dialog #js_tab_nav_js_rs_edit a.selected'),
            'Edit'
        );

        // And I can see the share tab is not selected
        $this->assertElementContainsText(
            $this->findByCss('.edit-password-dialog #js_tab_nav_js_rs_permission a'),
            'Share'
        );
        $this->assertNotVisibleByCss('.edit-password-dialog #js_tab_nav_js_rs_permission a.selected');

        // And I can see the name text input and label is marked as mandatory
        $this->assertVisibleByCss('.edit-password-dialog input[type=text]#js_field_name.required');
        $this->assertVisibleByCss('.edit-password-dialog label[for=js_field_name]');

        // And I can see the resource name in the text input
        $this->assertInputValue('js_field_name', $resource['name']);

        // And I can see the url text input and label
        $this->assertVisibleByCss('.edit-password-dialog input[type=text]#js_field_uri');
        $this->assertVisibleByCss('.edit-password-dialog label[for=js_field_uri]');

        // And   I can see the resource url in the text input
        $this->assertInputValue('js_field_uri', $resource['uri']);

        // And I can see the username field
        $this->assertVisibleByCss('.edit-password-dialog input[type=text]#js_field_username');
        $this->assertVisibleByCss('.edit-password-dialog label[for=js_field_username]');

        // And I can see the resource ursername in the text input
        $this->assertInputValue('js_field_username', $resource['username']);

        // And I can see the password iframe
        $this->assertVisibleByCss('.edit-password-dialog #passbolt-iframe-secret-edition');

        // And I can see the iframe label
        $this->assertVisibleByCss('.edit-password-dialog label[for=js_field_secret_data_0]');

        // When I switch to the password iframe
        $this->goIntoSecretIframe();

        // And I can see the password input
        $this->assertVisibleByCss('input[type=password]#js_secret');

        // And I can not see the password in cleartext
        $this->assertNotVisibleByCss('input[type=password]#js_secret_clear');
        $this->assertInputValue('js_secret_clear', '');
        $this->assertInputValue('js_secret', '');

        // And I can see the security token
        $this->assertSecurityToken($user, 'has_encrypted_secret');

        // And I can see the view password button
        $this->assertVisible('js_secret_view');
        // And I can see the generate password button
        $this->assertVisible('js_secret_generate');

        // And I can see the complexity meter
        // And I can see the complexity textual indicator
        $this->assertComplexity('not available');

        // When I switch back out of the password iframe
        $this->goOutOfIframe();

        // And I can see the description field and label
        $this->assertVisibleByCss('.edit-password-dialog textarea#js_field_description');
        $this->assertVisibleByCss('.edit-password-dialog label[for=js_field_description]');

        // And   I can see the resource description in the textarea
        $this->assertInputValue('js_field_description', $resource['description']);

        // And I can see the save button
        $this->assertVisibleByCss('.edit-password-dialog input[type=submit].button.primary');

        // And I can see the cancel button
        $this->assertVisibleByCss('.edit-password-dialog a.cancel');

    }

    /**
     * Scenario: As a user I can edit a password by using keyboard shortcuts only
     *
     * Given I am Ada
     * And   I am logged in
     * And   I am on the edit password dialog
     * Then  I can see that the field name has the focus
     * When  I type a new name on the keyboard to modify the current one already selected
     * And   I press the tab key
     * Then  I should see that the field username has the focus
     * When  I press the tab key
     * Then  I should see that the field uri has the focus
     * When  I press the tab key
     * Then  I should see the passphrase dialog opening
     * When  I type the passphrase on keyboard (without clicking anywhere first)
     * And   I press enter
     * And   I wait for a few seconds
     * Then  I should see the password field populated with my password
     * And   I should see that the password field has the focus
     * When  I press the tab key
     * Then  I should see that the field description has the focus
     * When  I press tab
     * And   I press enter
     * Then  I should see a notice message saying that the password was edited succesfully
     * And   I see the password updated with the new name I just entered in my password list
     *
     * @group LU
     * @group password
     * @group password-edit
     * @group v2
     */
    public function testEditPasswordWithKeyboardShortcutAndView() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Ada
        $user = User::get('ada');

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // And I am editing a password I own
        $resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));
        $this->gotoEditPassword($resource['id']);

        // Then I can see the edit password dialog
        $this->assertVisibleByCss('.edit-password-dialog');

        // The field name should have the focus
        $this->assertElementHasFocus('js_field_name');

        // I type a new name using keyboard only
        $this->typeTextLikeAUser('keyboardupdate');

        // Press tab key.
        $this->pressTab();

        // Then the field uri should have the focus.
        $this->assertElementHasFocus('js_field_uri');

        // Press tab key.
        $this->pressTab();

        // Then the field username should have the focus.
        $this->assertElementHasFocus('js_field_username');

        // Press tab key.
        $this->pressTab();

        // Then I see the passphrase dialog
        // Given I can see the iframe
        $this->waitUntilISee('#passbolt-iframe-master-password.ready');

        // I type the passphrase using keyboard only
        $this->goIntoMasterPasswordIframe();
        $this->typeMasterPasswordLikeAUser($user['MasterPassword']);

        // And I press enter
        $this->pressEnter();
        $this->goOutOfIframe();

        // Then the passphrase dialog should disappear
        $this->waitUntilIDontSee('#passbolt-iframe-master-password');

        // Then I can see the password edit dialog
        $this->assertVisibleByCss('.edit-password-dialog');

        // The field password should have the focus (inside the iframe).
        $this->goIntoSecretIframe();
        $this->waitUntilSecretIsDecryptedInField();
        $this->assertElementHasFocus('js_secret');

        // Press tab key.
        $this->pressTab();
        $this->goOutOfIframe();

        // Then the field description should have the focus.
        $this->assertElementHasFocus('js_field_description');

        // Press tab key.
        $this->pressTab();

        // Press enter.
        $this->pressEnter();

        // Then I can see a success notification
        $this->assertNotification('app_resources_update_success');

        // And I can see that the password name have changed in the overview
        $this->assertElementContainsText('#js_wsp_pwd_browser .tableview-content', 'keyboardupdate');
    }

    /**
     * Scenario: As a user I can go to next / previous field in the edit password form by using the keyboard tabs
     *
     * Given I am Ada
     * And   I am logged in
     * And   I am on the edit password dialog
     * Then  I can see that the field name has the focus
     * When  I press the tab key
     * Then  I should see that the field username has the focus
     * When  I press the tab key
     * Then  I should see that the field uri has the focus
     * When  I press the tab key
     * Then  I should see the passphrase dialog opening
     * When  I type the passphrase on keyboard (without clicking anywhere first)
     * And   I press enter
     * And   I wait for a few seconds
     * Then  I should see the password field populated with my password
     * And   I should see that the password field has the focus
     * When  I press the tab key
     * Then  I should see that the field description has the focus
     * When  I press backtab key
     * Then  I should see that the password field has the focus
     * When  I press the backtab key
     * Then  I should see that the uri field has the focus
     * When  I press the backtab key
     * Then  I should see that the username field has the focus
     * When  I press the backtab key
     * Then  I should see that the name field has the focus.
     *
     * @group LU
     * @group password
     * @group password-edit
     * @group v2
     */
    public function testEditPasswordKeyboardShortcuts() 
    {
        // Given I am Ada
        $user = User::get('ada');

        // And I am logged in
        $this->loginAs($user);

        // And I am editing a password I own
        $resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));
        $this->gotoEditPassword($resource['id']);

        // I should see that the field name has the focus.
        $this->assertElementHasFocus('js_field_name');

        // Press tab key.
        $this->pressTab();

        // I should see that the field name has the focus.
        $this->assertElementHasFocus('js_field_uri');

        // Press tab key.
        $this->pressTab();

        // I should see that the field name has the focus.
        $this->assertElementHasFocus('js_field_username');

        // Press tab key.
        $this->pressTab();

        // Then I see the passphrase dialog
        // Given I can see the iframe
        $this->waitUntilISee('#passbolt-iframe-master-password.ready');

        // I type the passphrase using keyboard only
        $this->goIntoMasterPasswordIframe();
        $this->typeMasterPasswordLikeAUser($user['MasterPassword']);

        // And I press enter
        $this->pressEnter();
        $this->goOutOfIframe();

        // Then the passphrase dialog should disappear
        $this->waitUntilIDontSee('#passbolt-iframe-master-password');

        // The field password should have the focus (inside the iframe).
        $this->goIntoSecretIframe();
        $this->waitUntilSecretIsDecryptedInField();
        $this->assertElementHasFocus('js_secret');

        // Press tab key.
        $this->pressTab();
        $this->goOutOfIframe();

        // Then the field description should have the focus.
        $this->assertElementHasFocus('js_field_description');

        // Press backtab.
        $this->pressBacktab();

        // The field password should have the focus (inside the iframe).
        $this->goIntoSecretIframe();
        $this->assertElementHasFocus('js_secret');

        // Press tab key.
        // TODO (PASSBOLT-1295) : fix the below part of the test.
        // Backtab doesn't seem to be done properly. Tab is received by the plugin, but shiftKey in the event
        // is set to false.
        //$this->pressBacktab();
        $this->goOutOfIframe();

        // I should see that the field name has the focus.
        //        $this->assertElementHasFocus('js_field_username');
        //
        //        // Press backtab key.
        //        $this->pressBacktab();
        //
        //        // I should see that the field name has the focus.
        //        $this->assertElementHasFocus('js_field_uri');
        //
        //        // Press backtab key.
        //        $this->pressBacktab();
        //
        //        // I should see that the field name has the focus.
        //        $this->assertElementHasFocus('js_field_name');
    }

    /**
     * Scenario: As a user I should be notified I will lose my changes on the edit password dialog after editing a field
     *
     * Given I am Ada
     * And   I am logged in on the password workspace
     * And   I am editing a password I own
     * When  I click on name input text field
     * And   I empty the name input text field value
     * And   I switch to the share screen
     * Then  I should see a confirmation dialog notifying me regarding the changes I'm going to lose
     * When  I click cancel in confirmation dialog
     * Then  I should stay on the edit dialog
     * When  I switch to the share screen
     * And   I click ok in confirmation dialog
     * Then  I should leave the edit dialog for the share dialog
     *
     * @group LU
     * @group password
     * @group password-edit
     * @group v2
     */
    public function testEditPasswordLoseChanges() 
    {
        // Given I am Ada
        // And I am logged in on the password workspace
        $this->loginAs(User::get('ada'));

        // And I am editing a password I own
        $resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));
        $this->gotoEditPassword($resource['id']);

        // When I click on name input text field
        $this->click('js_field_name');

        // And I change the name input text field value
        $newname = 'New password name';
        $this->inputText('js_field_name', $newname);

        // And I switch to the share screen
        $this->findByCss('#js_tab_nav_js_rs_permission a')->click();

        // Then I should see a confirmation dialog notifying me regarding the changes I'm going to lose
        $this->assertConfirmationDialog('Do you really want to leave ?');

        // When I click cancel in confirmation dialog
        $this->cancelActionInConfirmationDialog();

        // Then	I should stay on the edit dialog
        $this->assertVisible('js_rs_edit');

        // When	I switch to the share dialog
        $this->findByCss('#js_tab_nav_js_rs_permission a')->click();
        $this->assertConfirmationDialog('Do you really want to leave ?');

        // And I click ok in confirmation dialog
        $this->confirmActionInConfirmationDialog();

        // Then I should leave the edit dialog for the share dialog
        $this->assertVisible('js_rs_permission');
    }

    /**
     * Scenario: As a user I should be notified I will lose my changes on the edit password dialog after editing the secret
     *
     * Given I am Ada
     * And   I am logged in on the password workspace
     * And   I am editing a password I own
     * When  I edit the secret
     * And   I switch to the share screen
     * Then  I should see a confirmation dialog notifying me regarding the changes I'm going to lose
     * When  I click cancel in confirmation dialog
     * Then  I should stay on the edit dialog
     * When  I switch to the share screen
     * And   I click ok in confirmation dialog
     * Then  I should leave the edit dialog for the share dialog
     *
     * @group LU
     * @group password
     * @group password-edit
     * @group v2
     */
    public function testEditPasswordSecretLoseChanges() 
    {
        // Given I am Ada
        $user = User::get('ada');

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // And I am editing a password I own
        $resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));
        $this->gotoEditPassword($resource['id']);

        // When edit the secret
        $this->goIntoSecretIframe();
        $this->click('js_secret');
        $this->goOutOfIframe();
        $this->assertMasterPasswordDialog($user);
        $this->enterMasterPassword($user['MasterPassword']);
        $this->waitUntilIDontSee('#passbolt-iframe-master-password');

        // Wait until the password is decrypted and displayed in the field.
        $this->goIntoSecretIframe();
        $this->waitUntilSecretIsDecryptedInField();
        $this->goOutOfIframe();

        $this->assertVisibleByCss('.edit-password-dialog');
        $this->inputSecret('My new password');

        // And I switch to the share screen
        $this->findByCss('#js_tab_nav_js_rs_permission a')->click();

        // Then I should see a confirmation dialog notifying me regarding the changes I'm going to lose
        $this->assertConfirmationDialog('Do you really want to leave ?');

        // When I click cancel in confirmation dialog
        $this->cancelActionInConfirmationDialog();

        // Then	I should stay on the edit dialog
        $this->assertVisible('js_rs_edit');

        // When	I switch to the share dialog
        $this->findByCss('#js_tab_nav_js_rs_permission a')->click();
        $this->assertConfirmationDialog('Do you really want to leave ?');

        // And I click ok in confirmation dialog
        $this->confirmActionInConfirmationDialog();

        // Then I should leave the edit dialog for the share dialog
        $this->assertVisible('js_rs_permission');
    }

    /**
     * Scenario: As a user I can edit the name of a password I have own
     *
     * Given I am Ada
     * And   I am logged in on the password workspace
     * And   I am editing a password I own
     * When  I click on name input text field
     * And   I empty the name input text field value
     * And   I enter a new value
     * And   I click save
     * Then  I can see a success notification
     * And   I can see that the password name have changed in the overview
     * And   I can see the new name value in the sidebar
     * When  I click edit button
     * Then  I can see the new name in the edit password dialog
     *
     * @group LU
     * @group password
     * @group password-edit
     * @group v2
     */
    public function testEditPasswordName() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Ada
        $user = User::get('ada');

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // And I am editing a password I own
        $resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));
        $this->gotoEditPassword($resource['id']);

        // When I click on name input text field
        $this->click('js_field_name');

        // And I empty the name input text field value
        // And I enter a new value
        $newname = 'New password name';
        $this->inputText('js_field_name', $newname);

        // And I click save
        $this->click('.edit-password-dialog input[type=submit]');

        // Then I can see a success notification
        $this->assertNotification('app_resources_update_success');

        // And I can see that the password name have changed in the overview
        $this->assertElementContainsText('#js_wsp_pwd_browser .tableview-content', $newname);

        // And I can see the new name value in the sidebar
        $this->assertVisible('js_pwd_details');
        $this->assertElementContainsText('js_pwd_details', $newname);

        // When I click edit button
        $this->click('js_wk_menu_edition_button');

        // Then I can see the new name in the edit password dialog
        $this->assertInputValue('js_field_name', $newname);
    }

    /**
     * Scenario: As a user I can edit the description of a password I have own
     *
     * Given I am Ada
     * And   I am logged in on the password workspace
     * And   I am editing the description of a password I own
     * Then  I can see the success notification
     * When  I open the description section
     * Then   I can see the new description in the sidebar
     * When  I click edit button
     * Then  I can see the new description in the edit password dialog
     *
     * @group LU
     * @group password
     * @group password-edit
     * @group v2
     */
    public function testEditPasswordDescription() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Ada
        $user = User::get('ada');
        

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // And I am editing the description of a password I own
        // Then I can see a success notification
        $resource = Resource::get(
            array(
            'user' => 'ada',
            'permission' => 'owner'
            )
        );
        $r['id'] = $resource['id'];
        $r['description'] = 'this is a new description';
        $this->editPassword($r);

        // When I open the description section
        $this->clickSecondarySidebarSectionHeader('description');

        // Then I can see the new description in the sidebar
        $this->assertElementContainsText('#js_pwd_details .description_content', $r['description']);

        // When I click edit button
        $this->click('js_wk_menu_edition_button');

        // Then I can see the new description in the edit password dialog
        $this->assertInputValue('js_field_description', $r['description']);
    }

    /**
     * Scenario: As a user I can edit the uri of a password I have own
     *
     * Given I am Ada
     * And   I am logged in on the password workspace
     * And   I am editing the uri of a password I own
     * Then  I can see the success notification
     * And   I can see the new uri in the sidebar
     * When  I click edit button
     * Then  I can see the new uri in the edit password dialog
     *
     * @group LU
     * @group password
     * @group password-edit
     * @group v2
     */
    public function testEditPasswordUri() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Ada
        $user = User::get('ada');
        

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // And I am editing the uri of a password I own
        // Then I can see a success notification
        $resource = Resource::get(
            array(
            'user' => 'ada',
            'permission' => 'owner'
            )
        );
        $r['id'] = $resource['id'];
        $r['uri'] = 'https://newurl.com/checkonetwo';
        $this->editPassword($r);

        // And I can see that the password url have changed in the overview
        $this->assertElementContainsText('#js_wsp_pwd_browser .tableview-content', $r['uri']);

        // And I can see the new uri in the sidebar
        $this->assertElementContainsText('#js_pwd_details .uri', $r['uri']);

        // When I click edit button
        $this->click('js_wk_menu_edition_button');

        // Then I can see the new uri in the edit password dialog
        $this->assertInputValue('js_field_uri', $r['uri']);
    }

    /**
     * Scenario: As a user I can edit the secret of a password I have own
     *
     * Given I am Ada
     * And   I am logged in on the password workspace
     * And   I am editing a password I own
     * When  I click on the secret password field
     * Then  I see the passphrase dialog
     * When  I enter the passphrase and click submit
     * Then  I can see the password edit dialog
     * When  I enter a new password
     * And   I press the submit button
     * Then  I can see the encryption in progress dialog
     * Then  I can see the success notification
     * When  I copy the password to clipboard
     * Then  I can see that password have been updated
     *
     * @group LU
     * @group password
     * @group password-edit
     * @group v2
     * @group saucelabs
     */
    public function testEditPasswordSecret() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Ada
        $user = User::get('ada');
        

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // And I am editing a password I own
        $r1 = Resource::get([
            'user' => 'ada',
            'permission' => 'owner'
        ]);
        $r2 = [
            'id' => $r1['id'],
            'password' => 'our_brand_new_password'
        ];
        $this->gotoEditPassword($r1['id']);

        // When I click on the secret password field
        $this->goIntoSecretIframe();
        $this->click('js_secret');
        $this->goOutOfIframe();

        // Then I see the passphrase dialog
        $this->assertMasterPasswordDialog($user);

        // When I enter the passphrase and click submit
        $this->enterMasterPassword($user['MasterPassword']);

        $this->waitUntilIDontSee('#passbolt-iframe-master-password');

        $this->goIntoSecretIframe();
        $this->waitUntilSecretIsDecryptedInField();
        $this->goOutOfIframe();

        // Then I can see the password edit dialog
        $this->assertVisibleByCss('.edit-password-dialog');

        // When I enter a new password
        $this->inputSecret($r2['password']);

        // And I press the submit button
        $this->click('.edit-password-dialog input[type=submit]');

        // Then wait until I don't see  the encryption dialog anymore.
        $this->waitUntilIDontSee('#passbolt-iframe-progress-dialog');

        // Then I can see the success notification
        $this->assertNotification('app_resources_update_success');

        // When I copy the password to clipboard
        $this->copyToClipboard($r2, $user);

        // Then I can see that password have been updated
        $this->assertClipboard($r2['password']);
    }

    /**
     * Scenario: As a user editing my password I can use the button to view my secret in clear text
     *
     * Given I am Ada
     * And   I am logged in on the password workspace
     * And   I am editing a password I own
     * When  I click the button to view my password in clear text
     * Then  I see the passphrase dialog
     * When  I enter the passphrase in the input field
     * And   I press the submit button
     * Then  I can see the password in clear text
     * When  I press the same button to hide my password again
     * Then  I do not see the password in clear text
     * When  I press the button to view my password in cleartext
     * Then  I do not the passphrase dialog
     * Then  I can see the password in clear text
     *
     * @group LU
     * @group password
     * @group password-edit
     * @group v2
     * @group saucelabs
     */
    public function testEditPasswordViewClearText() 
    {
        // Given I am Ada
        $user = User::get('ada');

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // And I am editing a password I own
        $r1 = Resource::get([
            'user' => 'ada',
            'permission' => 'owner'
        ]);
        $this->gotoEditPassword($r1['id']);

        // When I click the button to view my password in clear text
        $this->goIntoSecretIframe();
        $this->click('js_secret_view');
        $this->goOutOfIframe();

        // Then I see the passphrase dialog
        $this->assertMasterPasswordDialog($user);

        // When I enter the passphrase and I press the submit button
        $this->enterMasterPassword($user['MasterPassword']);

        // Wait until I don't see the passphrase window anymore.
        $this->waitUntilIDontSee('#passbolt-iframe-master-password');

        // Then I should see the input field with the password in clear text
        $this->goIntoSecretIframe();
        $this->waitUntilSecretIsDecryptedInField();
        $this->assertNotVisibleByCss('#js_secret');
        $this->assertVisible('js_secret_clear');
        $this->assertTrue($this->findById('js_secret_clear')->getAttribute('value') == $r1['password']);

        // When I press the same button to hide my password again
        $this->click('js_secret_view');

        // Then I should not see the input field with the password in clear text
        $this->assertNotVisibleByCss('#js_secret_clear');

        // When I press the button to view my password in cleartext
        $this->click('js_secret_view');

        // Then I do not the passphrase dialog
        $this->assertNotVisibleByCss('#passbolt-iframe-master-password');

        // Then I can see the password in clear text
        $this->assertVisible('js_secret_clear');

        $this->goOutOfIframe();
    }

    /**
     * Scenario: As a user editing my password I can generate a new random password automatically
     *
     * Given I am Ada
     * And   I am logged in on the password workspace
     * And   I am editing a password I own
     * And   I can see the generate button is not active
     * When  I click on the secret password field
     * Then  I see the passphrase dialog
     * And   I enter the passphrase in the input field and press the submit button
     * And   I can see the secret field populated
     * And   I can see the generate button is now active
     * When  I click the button the generate a new random password button
     * And   I click the button to view my password in clear text
     * Then  I can see the secret is different than the previous one
     * And   I can see that the password complexity is set to fair
     *
     * @group LU
     * @group password
     * @group password-edit
     * @group v2
     * @group saucelabs
     */
    public function testEditPasswordGenerateRandom() 
    {
        // TODO : fix corresponding feature in plugin. #PASSBOLT-1060
        // Given I am Ada
        $user = User::get('ada');

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // And I am editing a password I own
        $r1 = Resource::get([
            'user' => 'ada',
            'permission' => 'owner'
        ]);
        $this->gotoEditPassword($r1['id']);
        $this->goIntoSecretIframe();

        // And I can see the generate button is not active
        $this->assertDisabled('js_secret_generate');

        // When I click on the secret password field
        $this->click('js_secret');

        // Then I see the passphrase dialog
        $this->assertMasterPasswordDialog($user);

        // When I enter the passphrase in the input field
        $this->enterMasterPassword($user['MasterPassword']);

        // Wait until I don't see the passphrase window anymore.
        $this->waitUntilIDontSee('#passbolt-iframe-master-password');

        // Then I should see the secret field populated
        $this->goIntoSecretIframe();

        $this->waitUntilSecretIsDecryptedInField();

        $s = $this->findById('js_secret')->getAttribute('value');
        $this->assertNotEmpty($s);

        // When I click the button to generate a new random password automatically
        $this->click('js_secret_generate');
        $s = $this->findById('js_secret')->getAttribute('value');

        // And I click the button to view my password in clear text
        $this->click('js_secret_view');

        // Then I can see the secret is different than the previous one
        $this->assertTrue(($s != $r1['password']));

        // And I should see that the password complexity is set to fair
        $this->assertTrue(strlen($s) == SystemDefaults::$AUTO_PASSWORD_LENGTH);
        $this->assertComplexity(SystemDefaults::$AUTO_PASSWORD_STRENGTH);

    }

    /**
     * Scenario: As a user I can not edit a password I have only read access to
     *
     * Given I am Ada
     * And   I am logged in on the password workspace
     * And   I click on a password I cannot edit
     * Then  I can see the edit button is not active
     * When  I right click on a password I cannot edit
     * Then  I can see the contextual menu
     * And   I can see the edit option is disabled
     *
     * @group LU
     * @group password
     * @group password-edit
     * @group v2
     */
    public function testEditPasswordNoRightNoEdit() 
    {
        // Given I am Ada
        $user = User::get('ada');
        

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // And I click on a password I cannot edit
        $r = Resource::get([
            'user' => 'ada',
            'permission' => 'read'
        ]);
        $this->clickPassword($r['id']);

        // Then I can see the edit button is not active
        $this->assertDisabled('js_wk_menu_edition_button');

        // When I right click on a password I cannot edit
        $this->rightClickPassword($r['id']);

        // Then I can see the contextual menu
        $this->findById('js_contextual_menu');

        // And I can see the edit option is disabled
        $this->click('#js_password_browser_menu_edit a');
        $this->assertNotVisibleByCss('.edit-password-dialog');
    }

    /**
     * Scenario: As user B I can see the changes are reflected when user A is editing a password we share
     *
     * Given I am Ada
     * And   I am logged in on the password workspace
     * And   I edit a password that I share with betty
     * And   I logout
     * And   I am Betty
     * And   I am logged in on the password workspace
     * And   I copy the password Ada edited to clipboard
     * Then  I can see the new password
     *
     * @group LU
     * @group password
     * @group password-edit
     * @group v2
     */
    public function testEditPasswordUserAEditUserBCanSee() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Ada
        $user = User::get('ada');

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // And I edit a password that I share with betty
        $r1 = Resource::get([
            'user' => 'betty',
            'permission' => 'update'
        ]);
        $r2 = [
            'id' => $r1['id'],
            'password' => 'our_brand_new_password'
        ];
        $this->editPassword($r2, $user);

        // And I logout
        $this->getUrl('logout');

        // And I am Betty
        $user = User::get('betty');

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // And I copy the password Ada edited to clipboard
        $this->copyToClipboard($r1, $user);

        // Then I can see the new password
        $this->assertClipboard($r2['password']);
    }

    /**
     * Scenario: As a user I can see error messages when editing a password with wrong inputs
     *
     * Given I am Ada
     * And   I am logged in on the password workspace
     * And   I edit a password I own
     * When  I click on the name input field
     * And   I empty the name input field
     * And   I empty the username input field
     * And   I empty the password input field
     * And   I click save
     * Then  I see an error message saying that the name is required
     * And   I see an error message saying that the password is required
     * Then  I don't see an error message saying that the username is required
     * When  I enter < as in name, uri and description
     * And   I click save
     * Then  I see an error message for all 3 fields
     *
     * @group LU
     * @group password
     * @group password-edit
     * @group v2
     */
    public function testEditPasswordErrorMessages() 
    {
        // Given I am Ada
        $user = User::get('ada');

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // And I edit a password I own
        $r1 = Resource::get([
            'user' => 'ada',
            'permission' => 'owner'
        ]);
        $this->gotoEditPassword($r1['id']);

        // And I empty the name input field
        $this->emptyFieldLikeAUser('js_field_name');

        // And I empty the username input field
        $this->emptyFieldLikeAUser('js_field_username');

        // I empty the password
        $this->goIntoSecretIframe();
        $this->click('js_secret');
        $this->goOutOfIframe();

        $this->assertMasterPasswordDialog($user);
        $this->enterMasterPassword($user['MasterPassword']);
        $this->waitUntilIDontSee('#passbolt-iframe-master-password');
        $this->goIntoSecretIframe();
        $this->waitUntilSecretIsDecryptedInField();
        $this->goOutOfIframe();
        $this->inputSecret('');

        // And I click on the submit button
        $this->click('.edit-password-dialog input[type=submit]');

        // Then I see an error message saying that the name is required
        $this->assertVisibleByCss('#js_field_name_feedback.error.message');
        $this->assertElementContainsText(
            $this->find('js_field_name_feedback'), 'is required'
        );

        // Then I see an error message saying that the username is required
        $this->assertNotVisibleByCss('#js_field_username_feedback.error.message');

        // And I see an error message saying that the password is required
        $this->goIntoSecretIframe();
        $this->assertVisibleByCss('#js_field_password_feedback.error.message');
        $this->assertElementContainsText(
            $this->find('js_field_password_feedback'), 'This information is required'
        );
        $this->goOutOfIframe();

        // When I enter < in all fields
        $this->inputText('js_field_name', '<');
        $this->inputText('js_field_uri', '<');
        $this->inputText('js_field_description', '<');

        // And I click save
        $this->click('.edit-password-dialog input[type=submit]');

        // I Should see an error message
        $this->assertVisibleByCss('#js_field_name_feedback.error.message');
        $this->assertVisibleByCss('#js_field_uri_feedback.error.message');
        $this->assertVisibleByCss('#js_field_description_feedback.error.message');
    }

    /**
     * Scenario: As a user I receive an email notification on a password update.
     *
     * Given I am Ada
     * And   I am logged in on the password workspace
     * And   I am editing a password I own
     * When  I click on name input text field
     * And   I empty the name input text field value
     * And   I enter a new value
     * And   I click save
     * Then  I can see a success notification
     * And   I can see that the password name have changed in the overview
     * When  I access my last email notification
     * Then  I should see that I received an email informing me that the password has been updated
     * And   I should see that the email title should be the old name (not the changed one)
     * And   I should see that the email contains You (ada.lovelace@passbolt.com)
     *
     * Given I am betty, and the password was shared with me
     * When  I access my last email notification
     * Then  I should see that I received an email informing me that the password has been updated
     * And   I should see that the email contains Ada Lovelace (ada.lovelace@passbolt.com)
     *
     * @group LU
     * @group password
     * @group password-edit
     * @group v2
     */
    public function testEditPasswordEmailNotification() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Ada
        $user = User::get('ada');

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // And I am editing a password I own
        $resource = Resource::get(['user' => 'ada', 'permission' => 'owner']);
        $this->gotoEditPassword($resource['id']);

        // When I click on name input text field
        $this->click('js_field_name');

        // And I empty the name input text field value
        // And I enter a new value
        $newName = 'MyNewName';
        $this->inputText('js_field_name', $newName);

        // And I click save
        $this->click('.edit-password-dialog input[type=submit]');

        // Then I can see a success notification
        $this->assertNotification('app_resources_update_success');

        // And I can see that the password name have changed in the overview
        $this->assertElementContainsText('#js_wsp_pwd_browser .tableview-content', $newName);

        // Access last email sent to Betty.
        $this->getUrl('seleniumtests/showlastemail/' . $user['Username']);

        // The email title should be:
        $this->assertMetaTitleContains('Ada edited the password MyNewName');

        // I should see the resource name in the email.
        $this->assertElementContainsText(
            'bodyTable',
            $newName
        );

        // I should my name in the email.
        $this->assertElementContainsText(
            'bodyTable',
            'Ada (' . $user['Username'] . ')'
        );

        $betty = User::get('betty');

        // Access last email sent to Betty.
        $this->getUrl('seleniumtests/showlastemail/' . $betty['Username']);

        // The email title should be:
        $this->assertMetaTitleContains('Ada edited the password MyNewName');

        // I should see the resource name in the email.
        $this->assertElementContainsText(
            'bodyTable',
            $newName
        );

        // I should see the user name in the email.
        $this->assertElementContainsText(
            'bodyTable',
            $user['FirstName'] . ' (' . $user['Username'] . ')'
        );
    }

    /**
     * Scenario: As LU I can use passbolt on multiple tabs and edit password
     *
     * Given I am Ada
     * And   I am logged in
     * When  I open a new tab and go to passbolt url
     * And   I switch back to the first tab
     * And   I edit a password
     * Then  I should see the password has been edited
     * When  I switch to the second tab
     * And   I edit a password
     * Then  I should see the password has been edited
     * When  I refresh the second tab
     * Then  I should see the password I edited on the first tab updated on the second tab
     * When  I switch to the first tab and I refresh it
     * Then  I should see the password I edited on the second tab updated on the first tab
     *
     * @group LU
     * @group password
     * @group password-edit
     * @group v2
     */
    public function testMultipleTabEditPassword() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Ada
        // And I am logged in
        $this->loginAs(User::get('ada'));

        // When I open a new tab and go to passbolt url
        $this->openNewTab('');

        // And I switch back to the first tab
        $this->switchToPreviousTab();

        // And I edit a password
        $resource1 = Resource::get([
            'user' => 'ada',
            'permission' => 'owner'
        ]);
        $resource1UpdateData['id'] = $resource1['id'];
        $resource1UpdateData['name'] = $resource1['name'] . ' updated';
        $this->editPassword($resource1UpdateData);

        // Then I should see the password has been edited
        $this->assertElementContainsText(
            $this->find('js_wsp_pwd_browser'), $resource1UpdateData['name']
        );

        // When I switch to the second tab
        $this->switchToNextTab();

        // And I edit a password
        $resource2 = Resource::get([
            'user' => 'ada',
            'permission' => 'update'
        ]);
        $resource2UpdateData['id'] = $resource2['id'];
        $resource2UpdateData['name'] = $resource2['name'] . ' updated';
        $this->editPassword($resource2UpdateData);

        // Then I should see the password has been edited
        $this->assertElementContainsText(
            $this->find('js_wsp_pwd_browser'), $resource2UpdateData['name']
        );

        // When I refresh the second tab
        $this->driver->navigate()->refresh();
        $this->waitCompletion();

        // Then I should see the password I edited on the first tab updated on the second tab
        $this->assertElementContainsText(
            $this->find('js_wsp_pwd_browser'), $resource1UpdateData['name']
        );

        // When I switch to the first tab and I refresh it
        $this->switchToPreviousTab();
        $this->driver->navigate()->refresh();
        $this->waitCompletion();

        // Then I should see the password I edited on the second tab updated on the first tab
        $this->assertElementContainsText(
            $this->find('js_wsp_pwd_browser'), $resource2UpdateData['name']
        );
    }

    /**
     * Scenario: As LU I should be able to edit a password after I restart the browser
     *
     * Given I am Ada
     * And   I am logged in on the passwords workspace
     * When  I restart the browser
     * Then  I should be able to edit a password
     *
     * @group LU
     * @group password
     * @group password-edit
     * @group no-saucelabs
     * @group skip
     */
    public function testRestartBrowserAndEditPassword() 
    {
        $this->markTestSkipped();

        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Ada
        $user = User::get('ada');

        // And I am logged in on the password workspace
        $this->loginAs($user);

        // When restart the browser
        $this->restartBrowser();
        $this->waitCompletion();

        // Then I should be able to edit a password
        $r1 = Resource::get(['user' => 'betty', 'permission' => 'update']);
        $r2 = ['id' => $r1['id'], 'password' => 'our_brand_new_password'];
        $this->editPassword($r2, $user);
    }

    /**
     * Scenario: As LU I should be able to edit a password after I close and restore the passbolt tab
     *
     * Given I am Ada
     * And   I am on second tab
     * And   I am logged in on the passwords workspace
     * When  I close and restore the tab
     * Then  I should be able to edit a password
     *
     * @group LU
     * @group password
     * @group password-edit
     * @group v2
     * @group firefox-only
     * PASSBOLT-2263 close and restore doesn't work with the latest chrome driver
     */
    public function testCloseRestoreTabAndEditPassword() 
    {
        // Reset database at the end of test.
        $this->resetDatabaseWhenComplete();

        // Given I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

        // And I am on second tab
        $this->openNewTab();

        // And I am logged in on the password workspace
        $this->loginAs($user, false);

        // When I close and restore the tab
        $this->closeAndRestoreTab();
        $this->waitCompletion();

        // Then I should be able to edit a password
        $r1 = Resource::get(['user' => 'betty', 'permission' => 'update']);
        $r2 = ['id' => $r1['id'], 'password' => 'our_brand_new_password'];
        $this->editPassword($r2, $user);
    }
}
