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

use Facebook\WebDriver\Exception\NoSuchElementException;

trait UserActionsTrait
{
    /**
     * Go to the user workspace and click on the create user button
     */
    public function gotoCreateUser() 
    {
        if(!$this->isVisible('.page.user')) {
            $this->getUrl('');
            $this->waitUntilISee('.page.user');
            $this->waitUntilISee('#js_wsp_create_button');
        }
        $this->click('#js_wsp_create_button');
        $this->waitUntilISee('.main-action-wrapper ul.dropdown-content');
        $this->click('.main-action-wrapper ul.dropdown-content li.create-user');
        $this->waitUntilISee('.create-user-dialog');
    }

    /**
     * Helper to create a user
     */
    public function createUser($user) 
    {
        $this->gotoCreateUser();
        $this->inputText('js_field_first_name', $user['first_name']);
        $this->inputText('js_field_last_name', $user['last_name']);
        $this->inputText('js_field_username', $user['username']);
        if (isset($user['admin']) && $user['admin'] === true) {
            // Check box admin
            $this->checkCheckbox('js_field_is_admin_checkbox');
        }
        $this->click('.create-user-dialog input[type=submit]');
        $this->assertNotification('app_users_addPost_success');
    }

    /**
     * Click on a user in the user workspace
     *
     * @param mixed $user array containing either id, or first name and last name or directly a uuid
     */
    public function clickUser($user) 
    {
        if(!$this->isVisible('.page.user')) {
            $this->fail("click user requires to be on the user workspace");
        }
        // if user is not an array, Then  It is a uuid.
        if (!is_array($user)) {
            $user = ['id' => $user];
        }
        if (isset($user['first_name']) && isset($user['last_name'])) {
            $elt = $this->find('.tableview-content div[title="' . $user['first_name'] . ' ' . $user['last_name'] . '"]');
            $elt->click();
        }
        else {
            $this->click('#user_' . $user['id'] . ' .cell_name');
        }
    }

    /**
     * Right click on a user with a given id.
     *
     * @param string $id
     */
    public function rightClickUser($id) 
    {
        if(!$this->isVisible('.page.user')) {
            $this->fail("right click user requires to be on the user workspace");
        }
        $eltSelector = '#user_' . $id . ' .cell_name';
        $this->getDriver()->executeScript(
        "
            var element = jQuery('$eltSelector')[0];
            var rect = element.getBoundingClientRect();
            jQuery('.tableview-content')[0].scrollTo(rect.left, rect.top);
            var mouseDownEvent = new MouseEvent('mousedown', {view: window, clientX:rect.left, clientY:rect.top, bubbles: true, cancelable: true, button:2});
			element.dispatchEvent(mouseDownEvent);
		"
        );
        // Without this little interval, the menu doesn't have time to open.
        $this->waitUntilISee('#js_contextual_menu.ready');
    }

    /**
     * Goto the edit user dialog for a given user id
     *
     * @param mixed $id user array or uuid
     */
    public function gotoEditUser($id) 
    {
        if(!$this->isVisible('.page.user')) {
            $this->getUrl('');
            $this->waitUntilISee('.page.password');
            $this->gotoWorkspace('user');
            $this->waitUntilISee('.page.user');
            $this->waitUntilISee('#js_user_wk_menu_edition_button');
        }
        $this->releaseFocus(); // we click somewhere in case the user is already active
        $this->clickUser($id);
        $this->click('js_user_wk_menu_edition_button');
        $this->waitCompletion();
        $this->assertVisibleByCss('.edit-user-dialog');
    }

    /**
     * Edit a user helper
     *
     * @param $user
     */
    public function editUser($user) 
    {
        $this->gotoEditUser($user);

        if (isset($user['first_name'])) {
            $this->inputText('js_field_first_name', $user['first_name']);
        }
        if (isset($user['last_name'])) {
            $this->inputText('js_field_last_name', $user['last_name']);
        }
        if (isset($user['admin'])) {
            $isAdmin = $this->find('#js_field_is_admin_checkbox')->isSelected();
            if ($isAdmin != $user['admin']) {
                $this->checkCheckbox('js_field_is_admin_checkbox');
            }
        }

        $this->click('.edit-user-dialog input[type=submit]');
        $this->assertNotification('app_users_editPost_success');
    }

}