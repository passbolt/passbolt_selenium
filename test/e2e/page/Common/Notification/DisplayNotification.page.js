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

/**
 * sub page containing specific selectors and methods for a specific page
 */
class DisplayNotificationPage {

  /**
   * return the notification container
   */
  get notificationContainer() {
    return $(".notification-container");
  }

  /**
   * return the success notification
   */
  get successNotification() {
    return this.notificationContainer.$(".success");
  }

  /**
   * return the error notification
   */
  get errorNotification() {
    return this.notificationContainer.$(".error");
  }

  /**
   * return the success notification to exist
   */
  async closeAllNotifications() {
    if(await this.notificationContainer.isDisplayed()) {
      await this.notificationContainer.click()
    }
  }
}

module.exports = new DisplayNotificationPage();
