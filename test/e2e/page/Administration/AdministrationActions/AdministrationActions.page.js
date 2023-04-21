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

/**
 * sub page containing specific selectors and methods for a specific page
 */
class AdministrationActionsPage {
  /**
   * return the save settings button
   */
  get actionsBar() {
    return $(".actions-wrapper li");
  }

  /**
   * return the save settings button
   */
  get saveSettingsButton() {
    return this.actionsBar.$("span=Save settings");
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. save settings button
   */
  async clickOnSaveSettings() {
    await this.saveSettingsButton.waitForClickable();
    await this.saveSettingsButton.click();
  }
}

module.exports = new AdministrationActionsPage();
