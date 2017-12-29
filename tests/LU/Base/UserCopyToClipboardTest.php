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
 * Feature: As a admin I can copy users email and public key into the clipboard
 *
 * Scenarios :
 * As a admin I can see the list of copy options when clicking right on a user
 * As a admin I can copy the public key to clipboard with a right click
 * As a admin I can copy the email address to clipboard with a right click
 * As a admin I can copy the public key to clipboard with the copy button in the sidebar
 */
namespace Tests\LU\Base;

use App\Actions\UserActionsTrait;
use App\Actions\WorkspaceActionsTrait;
use App\Assertions\ClipboardAssertions;
use App\Assertions\WorkspaceAssertionsTrait;
use App\PassboltTestCase;
use Data\Fixtures\User;

class UserCopyToClipboardTest extends PassboltTestCase
{
    use WorkspaceActionsTrait;
    use WorkspaceAssertionsTrait;
    use UserActionsTrait;
    use ClipboardAssertions;

    /**
     * Scenario: As a admin I can copy the public key to clipboard with a right click
     *
     * Given I am Admin
     * And   I am logged in on the user workspace
     * When  I select the user betty@passbolt.com
     * And   I right click
     * Then  I can see the contextual menu
     * When  I click on the link 'copy email address'
     * Then  I can see a success message saying the email was 'copied to clipboard'
     * And   The content of the clipboard is valid
     *
     * @group LU
     * @group user
     * @group user-clipboard
     * @group saucelabs
     * @group v2
     */
    public function testCopyEmailToClipboardViaContextualMenu()
    {
        // Given I am Ada
        // And I am logged in on the user workspace
        $this->loginAs(User::get('admin'));
        $this->gotoWorkspace('user');

        // Get user betty
        $betty = User::get('betty');
        // When I right click on the user betty
        $this->rightClickUser($betty['id']);

        // Then I can see the contextual menu
        $this->waitUntilISee('#js_contextual_menu');

        // When I click on the link 'copy password'
        $this->click('#js_user_browser_menu_copy_email a');

        // Then I can see a success message telling me the email was copied to clipboard
        $this->assertNotification('plugin_clipboard_copy_success');

        // And the content of the clipboard is valid
        $this->assertClipboard($betty['Username']);
    }

    /**
     * Scenario: As a admin I can see the list of copy options when clicking right on a user
     *
     * Given I am Admin
     * And   I am logged in on the user workspace
     * When  I right click on the user betty.
     * Then  I can see the contextual menu
     * And   I can see the first option is 'Copy public key' and is enabled
     * And   I can see next option is 'Copy email address' and is enabled
     *
     * @group LU
     * @group user
     * @group user-clipboard
     * @group v2
     */
    function testCopyContextualMenuView()
    {
        // Given I am Ada
        // And I am logged in on the password workspace
        $this->loginAs(User::get('admin'));
        $this->gotoWorkspace('user');

        // Get user betty
        $betty = User::get('betty');

        // When I right click on the first password in the list
        $this->rightClickUser($betty['id']);

        // Then I can see the contextual menu
        $e = $this->findById('js_contextual_menu');

        // And I can see the first option is 'Copy public key' and is enabled
        $this->assertElementContainsText($e, 'Copy public key');

        // And I can see next option is 'Copy email address' and is enabled
        $this->assertElementContainsText($e, 'Copy email address');
    }

    /**
     * Scenario: As a admin I can copy the public key to clipboard with a right click
     *
     * Given I am Admin
     * And   I am logged in on the user workspace
     * When  I select the user betty@passbolt.com
     * And   I right click
     * Then  I can see the contextual menu
     * When  I click on the link 'copy public key'
     * Then  I can see a success message saying the public key was 'copied to clipboard'
     * And   The content of the clipboard is valid
     *
     * @group LU
     * @group user
     * @group user-clipboard
     * @group saucelabs
     * @group v2
     */
    public function testCopyPublicKeyToClipboardViaContextualMenu()
    {
        // Given I am Ada
        // And I am logged in on the user workspace
        $this->loginAs(User::get('admin'));
        $this->gotoWorkspace('user');

        // Get user betty
        $betty = User::get('betty');
        // When I right click on the user betty in the list
        $this->rightClickUser($betty['id']);

        // Then I can see the contextual menu
        $this->waitUntilISee('#js_contextual_menu');

        // When I click on the link 'copy public key'
        $this->click('#js_user_browser_menu_copy_key a');

        // Then I can see a success message telling me the public key was copied to clipboard
        $this->assertNotification('plugin_clipboard_copy_success');

        // And the content of the clipboard is valid
        $this->assertClipboard(file_get_contents(GPG_FIXTURES . DS . 'betty_public.key'));
    }

    /**
     * Scenario: As a admin I can copy the public key to clipboard with the copy button in the sidebar
     *
     * Given I am Admin
     * And   I am logged in on the user workspace
     * When  I click on the user betty
     * And   I click on a the copy public key link in the sidebar
     * Then  the public key is copied to clipboard
     *
     * @group LU
     * @group user
     * @group user-clipboard
     * @group saucelabs
     * @group v2
     */
    public function testCopyPublicKeyToClipboardViaSidebarCopy() 
    {
        // Given I am Ada
        // And I am logged in on the password workspace
        $this->loginAs(User::get('ada'));
        $this->gotoWorkspace('user');

        // Get user betty
        $betty = User::get('betty');
        // When I click on the user betty
        $this->clickUser($betty['id']);

        // And I click on a the copy secret password link in the sidebar
        $this->click('#js_user_details a.copy-public-key');

        // Then I can see a success message telling me the public key was copied to clipboard
        $this->assertNotification('plugin_clipboard_copy_success');

        // And the content of the clipboard is valid
        $this->assertClipboard(file_get_contents(GPG_FIXTURES . DS . 'betty_public.key'));
    }
}