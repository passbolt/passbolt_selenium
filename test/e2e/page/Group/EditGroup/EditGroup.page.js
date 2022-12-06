/**
 * Passbolt ~ Open source password manager for teams
 * Copyright (c) Passbolt SA (https://www.passbolt.com)
 *
 * Licensed under GNU Affero General Public License version 3 of the or any later version.
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Passbolt SA (https://www.passbolt.com)
 * @license       https://opensource.org/licenses/AGPL-3.0 AGPL License
 * @link          https://www.passbolt.com Passbolt(tm)
 * @since         v3.0.0
 */

const AutoCompletePage = require('../../Common/AutoComplete/AutoComplete.page');
const DisplayNotificationPage = require('../../Common/Notification/DisplayNotification.page');
const PassphraseEntryDialogPage = require("../../AuthenticationPassphrase/InputPassphrase/InputPassphrase.page");

 /**
  * sub page containing specific selectors and methods for a specific page
  */
class EditGroupPage {
  /**
   * return the dialog container for group
   */
  get groupList() {
   return $(".edit-group-dialog");
  }
 
  /**
   * return the user name input
   */
  get inputUserName() {
   return $("#user-name-input");
  }
 
  /**
   * return input for group name
   */
  get inputGroupName() {
   return $("#group-name-input");
  }
 
  /**
   * return the selected autocomplete row
   */
  get inputGroupName() {
   return $("#group-name-input");
  }

  /**
   * return the remove member button
   */
  get removeMemberButton() {
    return $(".remove-item");
  }
 
  /**
   * return submit button
   */
  get submitButton() {
   return $('button[type=submit]');
  }


  /**
  * return the select items
  */
  get selectItems() {
    return $(".select-items.visible")
  }

  /**
  * return the select items
  */
  permissionItem(permission) {
    return this.selectItems.$('ul').$(`li*=${permission}`);
  }

  /**
  * return the groups members array
  */
  async groupsMembers(index){
    await $(".groups_users").waitForExist();
    return $$(".groups_users .row")[index];
  }

  /**
  * return the groups permission array
  */
  async userPermission(index){
    const user = await this.groupsMembers(index);
    return user.$(".permission");
  }
 
  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to click on delete option for the selected row
   */
  async renameGroup(groupName) {
   await this.inputGroupName.setValue(groupName);
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. add an user to a group
   */
  async addMember(user) {
   await this.inputUserName.setValue(user)
   await AutoCompletePage.getAutocompleteItem(user).waitForExist();
   await AutoCompletePage.getAutocompleteItem(user).click();
  }


  /**
   * a method to encapsule automation code to interact with the page
   * e.g. remove an user to a group
   */
  async removeMember() {
   await this.removeMemberButton.waitForClickable()
   await this.removeMemberButton.click()
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to click on the submit button
   */
  async clickOnSubmitButton(passphrase) {
   await DisplayNotificationPage.closeAllNotifications();
   await this.submitButton.waitForClickable();
   await this.submitButton.click();
   await PassphraseEntryDialogPage.entryPassphrase(passphrase);
   await DisplayNotificationPage.successNotification.waitForExist();
  }

  /**
  * a method to encapsule automation code to interact with the page
  * e.g. to edit user
  */
   async changeRole(index, role) {
    const permission = await this.userPermission(index);
    await permission.click();
    await this.permissionItem(role).waitForClickable();
    await this.permissionItem(role).click();
  }
 }
 
 module.exports = new EditGroupPage();
 