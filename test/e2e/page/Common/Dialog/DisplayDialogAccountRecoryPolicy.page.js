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

const PassphraseEntryDialogPage = require("../../AuthenticationPassphrase/InputPassphrase/InputPassphrase.page");

/**
 * sub page containing specific selectors and methods for a specific page
 */
class DisplayDisplayDialogAccountRecoryPolicyPage {
  /**
   * return the dialog container
   */
  get recoveryAccountPolicyDialog() {
    return $(".recovery-account-policy-dialog");
  }

  /**
   * return the continue button from the dialog
   */
  get continueButton() {
    return $("button[type=submit]");
  }

  /**
   * return the continue button from the dialog
   */
  get saveButton() {
    return $("button[type=submit]");
  }
  /**
   * a method to encapsule automation code to interact with the page
   * e.g. click on continue button
   */
  async clickOnContinueButton() {
    await this.recoveryAccountPolicyDialog.waitForExist();
    await this.continueButton.waitForClickable();
    await this.continueButton.click();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. click on save button
   */
  async clickOnSaveButton(password) {
    await this.recoveryAccountPolicyDialog.waitForExist();
    await this.saveButton.waitForClickable();
    await this.saveButton.click();
    await PassphraseEntryDialogPage.entryPassphrase(password);
  }
}

module.exports = new DisplayDisplayDialogAccountRecoryPolicyPage();
