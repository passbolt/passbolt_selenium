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

const PassphraseEntryDialogPage = require('../AuthenticationPassphrase/InputPassphrase/InputPassphrase.page');
const AutoCompletePage = require('../Common/AutoComplete/AutoComplete.page');
const DisplayNotificationPage = require('../Common/Notification/DisplayNotification.page');

/**
 * sub page containing specific selectors and methods for a specific page
 */
class ShareDialogPage {
  /**
   * define selectors using getter methods
   */
  get shareResourcePage() {
    return $('.dialog-wrapper');
  }
  
  /**
   * return the selected user
   */
  get newUserShared() {
    return $(".permission-updated");
  }

  /**
  * return the input name
  */
  get inputName() {
    return $('#share-name-input');
  }

  /**
  * return the submit button
  */
  get submitButton() {
    return $('.share-form button[type=submit]');
  }

  /**
  * return the select items
  */
  get selectItems() {
    return $(".select-items.visible ul");
  }

  /**
  * return the groups permission array
  */
  async userPermission(){
    await this.newUserShared.waitForExist();
    return this.newUserShared.$(".permission");
  }

  /**
  * return the select items
  */
  async permissionItem(permission) {
    await this.selectItems.waitForExist();
    return this.selectItems.$(`li*=${permission}`);
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to create a new password
   */
  async shareResource(username, passphrase, role) {
    await this.inputName.waitForClickable();
    await this.inputName.setValue(username);
    await AutoCompletePage.getAutocompleteItem(username).waitForExist();
    await AutoCompletePage.getAutocompleteItem(username).click();
    if(role) {
      await this.setRole(role)
    }
    await this.submitButton.waitForClickable();
    await this.submitButton.click();
    await PassphraseEntryDialogPage.entryPassphrase(passphrase);
    await DisplayNotificationPage.successNotification.waitForExist();
  }

  /**
  * a method to encapsule automation code to interact with the page
  * e.g. to edit user
  */
  async setRole(role) {
    const permission = await this.userPermission();
    await permission.click();
    const roleItem = await this.permissionItem(role);
    await roleItem.click();
  }
}

module.exports = new ShareDialogPage();
