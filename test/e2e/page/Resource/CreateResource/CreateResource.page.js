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

/**
 * sub page containing specific selectors and methods for a specific page
 */
class CreateResourcePage {
  /**
   * define selectors using getter methods
   */
  get createPasswordPage() {
    return $('.create-resource.dialog-wrapper');
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

  get inputDescription() {
    return $('#resource-note');
  }

  get submitButton() {
    return $('button[type=submit]');
  }

  get secretNoteTab() {
    return $('#secret-note-tab');
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to create a new password
   */
  async createPassword(name, uri, username, password, description) {
    await this.createPasswordPage.waitForExist();
    await this.inputName.setValue(name);
    await this.inputUri.setValue(uri);
    await this.inputUsername.setValue(username);
    await this.inputPassword.setValue(password);
    await this.secretNoteTab.click()
    await this.inputDescription.setValue(description);
    await this.submitButton.click();
    await PassphraseEntryDialogPage.entryPassphrase(username, {abortConditionCallback: this.createPasswordPage.isExisting});
  }
}

module.exports = new CreateResourcePage();
