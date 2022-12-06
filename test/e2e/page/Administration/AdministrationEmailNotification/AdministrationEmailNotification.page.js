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
 * @since         v3.8.0
 */

const AdministrationActionsPage = require("../AdministrationActions/AdministrationActions.page");
const DisplayNotificationPage = require('../../Common/Notification/DisplayNotification.page');

/**
 * sub page containing specific selectors and methods for a specific page
 */
class DisplayAdministrationEmailNotificationPage {
  /**
   * return the email notification form
   */
  get emailNotificationForm() {
    return $(".email-notification-settings");
  }

  /**
   * return the account recovery request radio
   */
  get accountRecoveryRequestedRadio() {
    return $("#account-recovery-request-admin-toggle-button");
  }

  /**
   * return the account recovery response notify administrator radio
   */
  get accountRecoveryResponseAdministratiorRadio() {
    return $("#account-recovery-response-created-admin-toggle-button");
  }

  /**
   * return the account recovery response notify all administrator radio
   */
  get accountRecoveryResponseAllAdministratorRadio() {
    return $("#account-recovery-response-created-all-admin-toggle-button");
  }

  /**
   * return the account recovery response notify when rejected
   */
  get accountRecoveryResponseRejectedRadio() {
    return $("#account-recovery-response-user-rejected-toggle-button");
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. select mandatory policy on the screen
   */
  async toggleAdministratorAccountRecoveryNotification() {
    // this is necessary to avoid any issue with notifications
    await DisplayNotificationPage.closeAllNotifications();
    await this.accountRecoveryRequestedRadio.waitForClickable({timeout: 15000});
    await this.accountRecoveryRequestedRadio.click();
    await this.accountRecoveryResponseAdministratiorRadio.click();
    await this.accountRecoveryResponseAllAdministratorRadio.click();
    await AdministrationActionsPage.clickOnSaveSettings();
    await DisplayNotificationPage.successNotification.waitForExist();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. select mandatory policy on the screen fo user
   */
  async toggleUserAccountRecoveryNotification() {
    await this.accountRecoveryResponseRejectedRadio.waitForClickable();
    await this.accountRecoveryResponseRejectedRadio.click();
    await AdministrationActionsPage.clickOnSaveSettings();
    await DisplayNotificationPage.successNotification.waitForExist();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. wait for form to be loaded
   */
  async waitFormToBeLoaded() {
    await this.emailNotificationForm.waitForExist();
  }
}

module.exports = new DisplayAdministrationEmailNotificationPage();
