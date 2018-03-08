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
namespace App\Actions;

use Exception;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverSelect;
use Data\Fixtures\User;

trait GroupActionsTrait
{

    /**
     * Helper to create a group
     */
    public function createGroup($group, $users, $creator) 
    {
        $this->gotoCreateGroup();

        // Fill group name
        $this->click('js_field_name');
        $this->inputText('js_field_name', $group['name']);

        // Insert group users
        foreach ($users as $userAlias) {
            $user = User::get($userAlias);
            $this->searchGroupUserToAdd($user, $creator);
            $this->addTemporaryGroupUser($user);
        }
        $this->click('.edit-group-dialog a.button.primary');
        $this->assertNotification('app_groups_add_success');
        $this->waitUntilIDontSee('.edit-group-dialog');
    }

    /**
     * Search a user to add to a group.
     *
     * @param array $userToAdd the user to add. See the User helper class.
     * @param array $user the user who request the add.
     */
    public function searchGroupUserToAdd($userToAdd, $user) 
    {
        $this->waitUntilISee('#passbolt-iframe-group-edit.ready');

        // I enter the username I want to share the password with in the autocomplete field
        $this->goIntoAddUserIframe();
        $this->assertSecurityToken($user, 'group');
        $this->inputText('js_group_edit_form_auto_cplt', strtolower($userToAdd['FirstName']), true);
        $this->click('.security-token');
        $this->goOutOfIframe();

        // I wait the autocomplete box is loaded.
        $this->waitUntilISee('#passbolt-iframe-group-edit-autocomplete.loaded');

        // I check that the user I was looking for is in the autocomplete list.
        $this->goIntoAddUserAutocompleteIframe();
        $userFullName = $userToAdd['FirstName'] . ' ' . $userToAdd['LastName'];

        try {
            $this->waitUntilISee('.autocomplete-content', '/' . $userFullName . '/i');
        } catch(Exception $e) {
            $this->goOutOfIframe();
            $this->fail("Could not find the requested user '$userFullName' in the autocomplete list");
        }

        $this->goOutOfIframe();
    }

    /**
     * Add a temporary user to a gtoup.
     *
     * @param $user
     * @return HTML element added in the list
     */
    public function addTemporaryGroupUser($user) 
    {
        $userFullName = $user['FirstName'] . ' ' . $user['LastName'];
        // I wait until I see the automplete field resolved
        $this->goIntoAddUserAutocompleteIframe();
        $this->waitUntilISee('.autocomplete-content', '/' . $userFullName . '/i');

        // I click on the username link the autocomplete field retrieved.
        $element = $this->findByXpath('//*[contains(., "' . $userFullName . '")]//ancestor::li[1]');
        $element->click();
        $this->goOutOfIframe();

        $elt = $this->getTemporaryGroupUserElement($user);
        return $elt;
    }

    /**
     * Edit temporary a group user role.
     *
     * @param $user
     * @param $isAdmin
     * @return HTML element added in the list
     */
    public function editTemporaryGroupUserRole($user, $isAdmin) 
    {
        $groupUserElement = $this->getTemporaryGroupUserElement($user);
        $select = new WebDriverSelect($groupUserElement->findElement(WebDriverBy::cssSelector('select')));
        $select->selectByVisibleText($isAdmin ? 'Group manager' : 'Member');
    }

    /**
     * Get temporary group user properties
     *
     * @param $user
     * @return array $properties
     *  bool role_disabled
     *  bool delete_disabled
     *  bool role
     */
    public function getTemporaryGroupUserProperties($user) 
    {
        $properties = [];

        $userElt = $this->getTemporaryGroupUserElement($user);

        // I should see that the user role for the group can't be changed.
        $roleSelect = $userElt->findElement(WebDriverBy::cssSelector('.js_group_user_is_admin'));
        $properties['role_disabled'] = $roleSelect->getAttribute('disabled') == 'true' ? true:false;
        $properties['role'] = $roleSelect->getAttribute('value') == '1' ? 'Group manager' : 'Member';

        // I should see that the user can't be deleted (because he is the only group manager
        $deleteBtn = $userElt->findElement(WebDriverBy::cssSelector('.js_group_user_delete'));
        $properties['delete_disabled'] = $deleteBtn->getAttribute('disabled') == 'true' ? true:false;

        return $properties;
    }

    /**
     * Get a temporary user element from the list
     *
     * @param $user
     * @return RemoteWebElement $rowElement
     */
    public function getTemporaryGroupUserElement($user) : RemoteWebElement
    {
        $userFullName = $user['FirstName'] . ' ' . $user['LastName'];

        // I can see the user has a direct entry
        $this->assertElementContainsText(
            $this->findById('js_permissions_list'),
            $userFullName
        );

        // Find the permission row element
        $rowElement = $this->findByXpath('//*[@id="js_permissions_list"]//*[.="' . $userFullName . '"]//ancestor::li[1]');

        return $rowElement;
    }

    /**
     * Delete temporary group user
     *
     * @param $user
     */
    public function deleteTemporaryGroupUser($user) 
    {
        $userElt = $this->getTemporaryGroupUserElement($user);
        // I should see that the user can't be deleted (because he is the only group manager
        $deleteBtn = $userElt->findElement(WebDriverBy::cssSelector('.js_group_user_delete'));
        $deleteBtn->click();

        // The entry should have disappeared from the list.
        $elt = null;
        try {
            $elt = $this->getTemporaryGroupUserElement($user);
        }
        catch (Exception $e) {
            // Do nothing. Element will remain null.
        }
        // Make sure that the element was not returned (because it doesn't exist).
        $this->assertEquals($elt, null);
    }

    /**
     * Go to the user workspace and click on the create group button
     */
    public function gotoCreateGroup() 
    {
        if(!$this->isVisible('.page.user')) {
            $this->getUrl('');
            $this->waitUntilISee('.page.user');
            $this->waitUntilISee('#js_wsp_create_button');
        }
        $this->click('#js_wsp_create_button');
        $this->waitUntilISee('.main-action-wrapper ul.dropdown-content');

        $this->click('.main-action-wrapper ul.dropdown-content li.create-group');
        $this->waitUntilISee('.edit-group-dialog');
        $this->waitUntilISee('#passbolt-iframe-group-edit.ready');
    }

    /**
     * Put the focus inside the add user iframe
     */
    public function goIntoAddUserIframe() 
    {
        $this->getDriver()->switchTo()->frame('passbolt-iframe-group-edit');
    }

    /**
     * Put the focus inside the add user autocomplete iframe
     */
    public function goIntoAddUserAutocompleteIframe() 
    {
        $this->getDriver()->switchTo()->frame('passbolt-iframe-group-edit-autocomplete');
    }

    /**
     * Click on a group inside the user workspace.
     *
     * @param string $id uuid of the group
     * @param string $workspace name of the workspace (password or user. Default=user)
     */
    public function clickGroup($id, $workspace='user') 
    {
        if($workspace == 'user' && !$this->isVisible('.page.user')) {
            $this->getUrl('');
            $this->gotoWorkspace('user');
            $this->waitUntilISee('.page.user');
        }
        elseif($workspace == 'password' && !$this->isVisible('.page.password')) {
            $this->getUrl('');
            $this->gotoWorkspace('password');
            $this->waitUntilISee('.page.password');
        }
        $eltSelector = '#group_' . $id . ' .main-cell';
        $this->click($eltSelector);
        $this->waitCompletion();
    }

    /**
     * Goto the edit group dialog for a given group id
     *
     * @param string $id uuid of the group
     */
    public function gotoEditGroup($id) 
    {
        if(!$this->isVisible('.page.user')) {
            if(!$this->isVisible('html.passboltplugin-ready')) {
                $this->getUrl('');
                $this->waitUntilISee('html.passboltplugin-ready');
                $this->waitUntilISee('.page.password');
            }
            if($this->isVisible('.dialog')) {
                $this->click('.dialog .dialog-close');
            }
            $this->gotoWorkspace('user');
            $this->waitUntilISee('.page.user');
        }
        if(!$this->isVisible('.edit-group-dialog')) {
            $this->waitUntilISee('#js_wsp_users_groups_list.ready');
            $groupElement = $this->find("#group_$id");
            $this->driver->getMouse()->mouseMove($groupElement->getCoordinates());
            $this->click("#group_${id} .right-cell a");
            $this->click("#js_contextual_menu #js_group_browser_menu_edit a");
            $this->waitUntilISee('.edit-group-dialog');
            $this->waitUntilISee('#js_edit_group.ready');
            // If the user is group manager, the extension should inject an iframe to
            // select a new user to add to the group
            try{
                $this->findById('passbolt-iframe-group-edit');
                $this->waitUntilISee('#passbolt-iframe-group-edit.ready');
            } catch(NoSuchElementException $e) {
                // Nothing to do.
            }
        }
    }

    /**
     * Click on remove in the contextual menu of a group.
     *
     * @param string $id uuid of the group
     */
    public function goToRemoveGroup($id) 
    {
        if(!$this->isVisible('.page.user')) {
            $this->getUrl('');
            $this->waitUntilISee('.page.password');
            $this->gotoWorkspace('user');
            $this->waitUntilISee('.page.user');
        }
        $groupElement = $this->find("#group_$id");
        $this->driver->getMouse()->mouseMove($groupElement->getCoordinates());
        $this->click("#group_${id} .right-cell a");
        $this->click("#js_contextual_menu #js_group_browser_menu_remove a");
        $this->waitUntilISee('.dialog.confirm');
    }
}