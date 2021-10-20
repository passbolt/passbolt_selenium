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
class CheckPassphrasePage {
  /**
   * define selectors using getter methods
   */
  get checkPassphrasePage() {
    return $('.enter-passphrase');
  }

  get inputPassphrase() {
    return $('#passphrase');
  }

  get btnSubmit() {
    return $('button[type="submit"]');
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to enter passphrase
   */
  async enterPassphrase(username) {
    // Enter passphrase
    await this.checkPassphrasePage.waitForExist();
    await this.inputPassphrase.setValue(username);
    await this.btnSubmit.waitForClickable();
    await this.btnSubmit.click();
  }
}

module.exports = new CheckPassphrasePage();
