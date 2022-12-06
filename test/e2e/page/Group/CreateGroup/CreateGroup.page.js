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

/**
 * sub page containing specific selectors and methods for a specific page
 */
class CreateGroupPage {
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
   * return submit button
   */
  get submitButton() {
    return $('button[type=submit]');
  }

  /**
   * return input for user name
   */
  async createGroup(groupName, user) {
    // this is necessary to avoid any issue with notifications
    await DisplayNotificationPage.closeAllNotifications();
    await this.inputGroupName.setValue(groupName);
    if(user) {
      await this.inputUserName.setValue(user)
      await AutoCompletePage.getAutocompleteItem(user).waitForExist();
      await AutoCompletePage.getAutocompleteItem(user).click();
    }
    await this.submitButton.waitForClickable();
    await this.submitButton.click();
    await DisplayNotificationPage.successNotification.waitForExist();  
  }
}

module.exports = new CreateGroupPage();
