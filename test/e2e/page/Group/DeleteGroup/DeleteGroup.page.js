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

const DisplayNotificationPage = require("../../Common/Notification/DisplayNotification.page");

/**
 * sub page containing specific selectors and methods for a specific page
 */
class DeleteGroupPage {

 /**
  * return the dialog container for deletion
  */
  get dialogDeleteContainer() {
    return $('.delete-group-dialog');
  }

 /**
  * return the submit button
  */
  get submitButton() {
    return $('button[type=submit]');
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to validate deletion of group
   */
   async validationDeletion() {
    // this is necessary to avoid any issue with notifications
    await DisplayNotificationPage.closeAllNotifications();
    await this.dialogDeleteContainer.waitForExist();
    await this.submitButton.waitForClickable();
    await this.submitButton.click();
    await DisplayNotificationPage.successNotification.waitForExist();
  }
}

module.exports = new DeleteGroupPage();
