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
    return $('.edit-resource.dialog-wrapper');
  }

  get inputName() {
    return $('#resource-name');
  }

  get inputUri() {
    return $('#resource-uri');
  }

  get inputUsername() {
    return $('#resource-username');
  }

  get inputPassword() {
    return $('#resource-password');
  }

  get openPasswordGenerator() {
    return $('.additional-information .section-header');
  }

  get inputDescription() {
    return $('#resource-note');
  }

  get submitButton() {
    return $('button[type=submit]');
  }

  get secretNoteTab() {
    return $('#secret-note-tab');
  }

  get generatePassword() {
    return $('.password-generate.button-icon');
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
    await this.openPasswordGenerator.click();
    await this.generatePassword.click();
    await this.secretNoteTab.click()
    await this.inputDescription.setValue(description);
    await this.submitButton.click();
    await PassphraseEntryDialogPage.entryPassphrase(username, {abortConditionCallback: this.editPasswordPage.isExisting});
    await DisplayNotificationPage.successNotification.waitForExist();
    return ressourceName;
  }
}

module.exports = new EditResourcePage();
