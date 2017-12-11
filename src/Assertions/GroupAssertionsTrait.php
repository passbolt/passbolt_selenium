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
namespace App\assertions;

trait GroupAssertionsTrait
{
    /**
     * Check if the group has already been selected
     *
     * @param  $id string
     * @return bool
     */
    public function isGroupSelected($id) 
    {
        $eltSelector = '#group_' . $id . ' .row';
        if ($this->elementHasClass($eltSelector, 'selected')) {
            return true;
        }
        return false;
    }

    /**
     * Check if the group has already been selected
     *
     * @param  $id string
     * @return bool
     */
    public function isGroupNotSelected($id) 
    {
        return !$this->isGroupSelected($id);
    }

    /**
     * Assert that I can see a group.
     *
     * @param $name
     */
    public function assertICanSeeGroup($name) 
    {
        try {
            $this->waitUntilISee("#group_" . UuidFactory::uuid('group.id.' . strtolower($name)), '/' . $name . '/i');
        }
        catch (Exception $e) {
            $this->fail('Failed asserting that group ' . $name . ' is visible');
        }
    }

    /**
     * Assert group member from edit dialog
     *
     * @param $groupId
     * @param $user
     * @param $isAdmin
     */
    public function assertGroupMemberInEditDialog($groupId, $user, $isAdmin = false) 
    {
        $this->gotoEditGroup($groupId);
        $rowElement = $this->getTemporaryGroupUserElement($user);
        $select = new WebDriverSelect($rowElement->findElement(WebDriverBy::cssSelector('select')));
        $this->assertEquals($isAdmin ? 'Group manager' : 'Member', $select->getFirstSelectedOption()->getText());
    }

    /**
     * Assert a user is member of a group from the user sidebar
     *
     * @param $groupName
     * @param $isGroupManager
     */
    public function assertGroupUserInSidebar($groupName, $isGroupManager = false) 
    {
        // Wait until the groups list is loaded. (ready state).
        $this->waitUntilISee('#js_user_groups_list.ready');

        // Retrieve the group details information
        $rowElement = $rowElement = $this->findByXpath('//*[@id="js_user_groups_list"]//*[contains(text(), "' . $groupName . '")]//ancestor::li');

        // I can see the group is in the list
        $this->assertElementContainsText(
            $rowElement->findElement(WebDriverBy::cssSelector('.name')),
            $groupName
        );

        // I can see the user has the expected role.
        $this->assertElementContainsText(
            $rowElement->findElement(WebDriverBy::cssSelector('.subinfo')),
            $isGroupManager ? 'Group manager' : 'Member'
        );
    }

    /**
     * Assert group member from sidebar
     *
     * @param $groupId
     * @param $user
     * @param $isAdmin
     */
    public function assertGroupMemberInSidebar($groupId, $user, $isAdmin = false) 
    {
        $this->gotoWorkspace('user');
        if (!$this->isGroupSelected($groupId)) {
            $this->clickGroup($groupId);
        }

        // Then  I should see that the sidebar contains a member section
        $this->waitUntilISee('#js_group_details.ready #js_group_details_members');

        // And I should see that the members sections contains the list of users that are members of this group
        $userFullName = $user['FirstName'] . ' ' . $user['LastName'];
        $rowElement = $this->findByXpath('//*[@id="js_group_details_members"]//*[contains(text(), "' . $userFullName . '")]//ancestor::li');

        // And I should see that below each user I can see his membership type
        $memberRoleElt = $rowElement->findElement(WebDriverBy::cssSelector('.subinfo'));
        $this->assertEquals($isAdmin ? 'Group manager' : 'Member', $memberRoleElt->getText());
    }

    /**
     * Assert a group is selected
     *
     * @param  $id string
     * @return bool
     */
    public function assertGroupSelected($id) 
    {
        $this->assertTrue($this->isGroupSelected($id));
    }

    /**
     * Assert a group is selected
     *
     * @param  $id string
     * @return bool
     */
    public function assertGroupNotSelected($id) 
    {
        $this->assertTrue($this->isGroupNotSelected($id));
    }
}