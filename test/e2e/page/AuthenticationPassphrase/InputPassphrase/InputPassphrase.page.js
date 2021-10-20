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
class InputPassphrasePage {
  /**
   * define selectors using getter methods
   */
  get entryPassphrasePage() {
    return $('.dialog.passphrase-entry');
  }

  get inputPassphrase() {
    return $('#passphrase-entry-form-passphrase');
  }

  get btnSubmit() {
    return $('.dialog.passphrase-entry input[type="submit"]');
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to entry passphrase
   */
  async entryPassphrase(username) {
    // Entry passphrase
    await this.entryPassphrasePage.waitForExist();
    await this.inputPassphrase.setValue(username);
    await this.btnSubmit.waitForClickable();
    await this.btnSubmit.click();
  }
}

module.exports = new InputPassphrasePage();
