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

const DisplayAdministrationEmailNotificationPage = require("../AdministrationEmailNotification/AdministrationEmailNotification.page");

/**
 * sub page containing specific selectors and methods for a specific page
 */
class DisplayAdministrationMenuPage {
  /**
   * return the  adminstration left menu
   */
  get administrationMenu() {
    return $("#administration_menu");
  }
  /**
   * return the account recovery menu tab
   */
  get accountRecoverySection() {
    return this.administrationMenu.$("#account_recovery_menu");
  }

  /**
   * return the email notification menu tab
   */
  get emailNotificationSection() {
    return this.administrationMenu.$("#email_notification_menu");
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to go to the account recovery section
   */
  async goToAccountRecoverySection() {
    await this.accountRecoverySection.waitForClickable();
    await this.accountRecoverySection.click();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to go to the email notification section
   */
  async goToEmailNotificationSection() {
    await this.emailNotificationSection.waitForClickable();
    await this.emailNotificationSection.click();
    await browser.switchToParentFrame();
    await DisplayAdministrationEmailNotificationPage.waitFormToBeLoaded();
  }
}

module.exports = new DisplayAdministrationMenuPage();
