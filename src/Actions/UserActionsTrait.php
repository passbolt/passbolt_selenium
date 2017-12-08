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
namespace App\actions;

trait UserActionsTrait
{
    /**
     * Go to the user workspace and click on the create user button
     */
    public function gotoCreateUser() 
    {
        if(!$this->isVisible('.page.people')) {
            $this->getUrl('');
            $this->waitUntilISee('.page.people');
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
            $this->checkCheckbox('#js_field_role_id .role-admin input[type=checkbox]');
        }
        $this->click('.create-user-dialog input[type=submit]');
        $this->assertNotification('app_users_add_success');
    }

    /**
     * Click on a user in the user workspace
     *
     * @param  array $user array containing either id, or first name and last name or directly a uuid
     * @throws Exception if not on the right workspace
     */
    public function clickUser($user) 
    {
        if(!$this->isVisible('.page.people')) {
            $this->fail("click user requires to be on the user workspace");
        }
        // if user is not an array, then it is a uuid.
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
     *
     * @throws Exception
     */
    public function rightClickUser($id) 
    {
        if(!$this->isVisible('.page.people')) {
            $this->fail("right click user requires to be on the user workspace");
        }
        $eltSelector = '#user_' . $id . ' .cell_name';
        $this->getDriver()->executeScript(
            "
			jQuery('$eltSelector').trigger({
				type:'mousedown',
				which:3
			});
		"
        );
        // Without this little interval, the menu doesn't have time to open.
        $this->waitUntilISee('#js_contextual_menu.ready');
    }

    /**
     * Goto the edit user dialog for a given user id
     *
     * @param  $id string
     * @throws Exception
     */
    public function gotoEditUser($id) 
    {
        if(!$this->isVisible('.page.people')) {
            $this->getUrl('');
            $this->waitUntilISee('.page.password');
            $this->gotoWorkspace('user');
            $this->waitUntilISee('.page.people');
            $this->waitUntilISee('#js_user_wk_menu_edition_button');
        }
        $this->releaseFocus(); // we click somewhere in case the user is already active
        $this->clickUser($id);
        $this->click('js_user_wk_menu_edition_button');
        $this->waitCompletion();
        $this->assertVisible('.edit-user-dialog');
    }

    /**
     * Edit a user helper
     *
     * @param  $user
     * @throws Exception
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
            // Get current state of admin
            $el = null;
            try {
                $el = $this->find('#js_field_role_id .role-admin input[type=checkbox][checked=checked]');
            }
            catch(Exception $e) {
            }
            // if el was found, admin checkbox is already checked.
            $isAdmin = ($el == null ? false : true);
            if ($isAdmin != $user['admin']) {
                $this->checkCheckbox('#js_field_role_id .role-admin input[type=checkbox]');
            }
        }

        $this->click('.edit-user-dialog input[type=submit]');
        $this->assertNotification('app_users_edit_success');
    }

}