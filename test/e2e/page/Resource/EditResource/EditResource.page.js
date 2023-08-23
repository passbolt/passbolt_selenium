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

const PassphraseEntryDialogPage = require('../../AuthenticationPassphrase/InputPassphrase/InputPassphrase.page');
const DisplayNotificationPage = require('../../Common/Notification/DisplayNotification.page');
const GenerateResourcePasswordPage = require("../../ResourcePassword/GenerateResourcePassword/GenerateResourcePassword.page");

/**
 * sub page containing specific selectors and methods for a specific page
 */
class EditResourcePage {
  /**
   * define selectors using getter methods
   */
  get editPasswordPage() {
    return $('.edit-password-dialog.dialog-wrapper');
  }

  get inputName() {
    return $('#edit-password-form-name');
  }

  get inputUri() {
    return $('#edit-password-form-uri');
  }

  get inputUsername() {
    return $('#edit-password-form-username');
  }

  get inputPassword() {
    return $('#edit-password-form-password');
  }

  get openPasswordGenerator() {
    return $('.edit-password-dialog.dialog-wrapper .password-generator');
  }

  get inputDescription() {
    return $('#edit-password-form-description');
  }

  get submitButton() {
    return $('button[type=submit]');
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to edit a password
   */
  async editPassword(name, uri, username, password, description) {
    await this.editPasswordPage.waitForExist();
    await this.inputName.clearValue();
    await this.inputName.addValue(name);
    const ressourceName = this.inputName.getValue();
    await this.inputUri.setValue(uri);
    await this.inputUsername.setValue(username);
    await this.inputPassword.waitForEnabled();
    await this.inputPassword.setValue(password);
    await this.inputDescription.setValue(description);
    await this.openPasswordGenerator.waitForClickable();
    await this.openPasswordGenerator.click();
    await GenerateResourcePasswordPage.generatePassword();
    await this.submitButton.waitForClickable();
    await this.submitButton.click();
    await PassphraseEntryDialogPage.entryPassphrase(username);
    await DisplayNotificationPage.successNotification.waitForExist();
    return ressourceName;
  }
}

module.exports = new EditResourcePage();
