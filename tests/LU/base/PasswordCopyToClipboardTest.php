<?php
/**
 * Feature :  As a user I can copy my password info to clipboard
 *
 * Scenarios :
 * As a user I can see the list of copy options when clicking right on a password
 * As a user I can copy my password to clipboard with a right click
 * As a user I can copy the URI of one resource to clipboard with a right click
 * As a user I can copy the username of one resource to clipboard with a right click
 *
 *
 * @TODO Missing scenarios
 * As a user I should see errors when entering the wrong master key while trying to copy my password to clipboard
 * I can close the master password dialog using cancel or escape or the close button
 * Using more > copy to clipboard button
 * I can open the url of a resource in a new tab
 *
 * @copyright    (c) 2015-present Bolt Software Pvt. Ltd.
 * @licence      GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
 */
class PasswordCopyToClipboardTest extends PassboltTestCase
{

    /**
     * Scenario : As a user I can see the list of copy options when clicking right on a password
     *
     * Given    I am Betty
     * And      I am logged in on the password workspace
     * When     I right click on the first password in the list
     * Then     I can see the contextual menu
     * And      I can see the first option is 'Copy username' and is enabled
     * And      I can see next option is 'Copy password' and is enabled
     * And      I can see next option is 'Copy URI' and is enabled
     */
    function testCopyContextualMenu() {
        // Given I am Betty
        $user = User::get('betty');
        $resource = Resource::get(array('user' => 'betty'));
        $this->setClientConfig($user);

        // And I am logged in on the password workspace
        $this->loginAs($user['Username']);

        // When I right click on the first password in the list
        $this->rightClick($resource['id']);

        // Then I can see the contextual menu
        $e = $this->findById('js_contextual_menu');

        // And I can see the first option is 'Copy username' and is enabled
        $this->assertElementContainsText($e, 'Copy username');

        // And I can see next option is 'Copy password' and is enabled
        $this->assertElementContainsText($e, 'Copy password');

        // And I can see next option is 'Copy URI' and is enabled
        $this->assertElementContainsText($e, 'Copy URI');
    }

    /**
     * Scenario : As a user I can copy a password to clipboard
     *
     * Given    I am Betty
     * And      The database is in a clean state
     * And      I am logged in on the password workspace
     * When     I select the first password in the list
     * And      I right click
     * Then     I can see the contextual menu
     * When     I click on the link 'copy password'
     * Then     I can see the master key dialog
     * When     I enter my master password
     * Then     I can see a success message saying the password was 'copied to clipboard'
     * When     I copy paste the password in search input field
     * Then     I can see it is the right one
     */
    public function testCopyPasswordToClipboard() {
        // Given I am Betty
        $user = User::get('betty');
        $resource = Resource::get(array('user' => 'betty'));
        $this->setClientConfig($user);

        // And the database is in a clean state
        $this->PassboltServer->resetDatabase(1);

        // And I am logged on the password workspace
        $this->loginAs($user['Username']);

        // When I select the first password in the list
        $this->click('multiple_select_checkbox_' . $resource['id']);

        // And I right click
        $this->rightClick($resource['id']);

        // Then I can see the contextual menu
        $this->assertVisible('js_contextual_menu');

        // When I click on the link 'copy password'
        $this->clickLink('Copy password');

        // Then I can see the master key dialog
        $this->assertVisible('passbolt-iframe-master-password');
        $this->goIntoMasterPasswordIframe();

        // When I enter my master password
        $this->inputText('js_master_password', $user['MasterPassword']);
        // And click on save
        $this->click('master-password-submit');
        $this->goOutOfIframe();

        // Then I can see a success message telling me the password was copied to clipboard
        $this->isVisible('.notification-container .message.success');
        $this->assertElementContainsText('.notification-container .message.success','copied to clipboard');

        // When I copy paste the password in search input field
        $e = $this->findById('js_app_filter_keywords');
        $e->click();
        $action = new WebDriverActions($this->driver);
        $action->sendKeys($e, array(WebDriverKeys::CONTROL,'v'))->perform();

        // Then I can see it is the right one
        $this->assertTrue($e->getAttribute('value') == $resource['password']);
    }

    /**
     * Scenario : As a user I can copy the URI of one resource to clipboard
     *
     * Given    I am Betty
     * And      I am logged in on the password workspace
     * When     I right click on the first password in the list
     * And      I click on the 'Copy URI' in the contextual menu
     * Then     I can see a success message saying the URI was copied to clipboard
     * When     I click in the search input field and paste the clipboard content
     * Then     I can see it is the right URI
     */
    function testCopyURIToClipboard () {
        // Given I am Betty
        $user = User::get('betty');
        $resource = Resource::get(array('user' => 'betty'));
        $this->setClientConfig($user);

        // And I am logged in on the password workspace
        $this->loginAs($user['Username']);

        // When I right click on the first password in the list
        $this->rightClick($resource['id']);

        // When I click on the 'Copy URI' in the contextual menu
        $this->clickLink('Copy URI');

        // Then I can see a success message saying the URI was copied to clipboard
        $this->isVisible('.notification-container .message.success');
        $this->assertElementContainsText('.notification-container .message.success','copied to clipboard');

        // When I click in the search input field and paste the clipboard content
        $e = $this->findById('js_app_filter_keywords');
        $e->click();
        $action = new WebDriverActions($this->driver);
        $action->sendKeys($e, array(WebDriverKeys::CONTROL,'v'))->perform();

        // Then I can see it is the right one
        $this->assertTrue($e->getAttribute('value') == $resource['uri']);
    }

    /**
     * Scenario : As a user I can copy the username of one resource to clipboard
     *
     * Given    I am Betty
     * And      I am logged in on the password workspace
     * When     I right click on the first password in the list
     * And      I click on the 'Copy username' in the contextual menu
     * Then     I can see a success message saying the username was copied to clipboard
     * When     I click in the search input field and paste the clipboard content
     * Then     I can see it is the right username
     */
    function testCopyUsernameToClipboard() {
        // Given I am Betty
        $user = User::get('betty');
        $resource = Resource::get(array('user' => 'betty'));
        $this->setClientConfig($user);

        // And I am logged in on the password workspace
        $this->loginAs($user['Username']);

        // When I right click on the first password in the list
        $this->rightClick($resource['id']);

        // When I click on the link 'copy URI' in the contextual menu
        $this->clickLink('Copy username');

        // Then I can see a success message saying the username was copied to clipboard
        $this->isVisible('.notification-container .message.success');
        $this->assertElementContainsText('.notification-container .message.success','copied to clipboard');

        // When I click in the search input field and paste the clipboard content
        $e = $this->findById('js_app_filter_keywords');
        $e->click();
        $action = new WebDriverActions($this->driver);
        $action->sendKeys($e, array(WebDriverKeys::CONTROL,'v'))->perform();

        // Then I can see it is the right one
        $this->assertTrue($e->getAttribute('value') == $resource['username']);
    }

}