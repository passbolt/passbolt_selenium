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

const PassphraseEntryDialogPage = require('../AuthenticationPassphrase/InputPassphrase/InputPassphrase.page');

/**
 * sub page containing specific selectors and methods for a specific page
 */
class ShareDialogPage {
  /**
   * define selectors using getter methods
   */
  get shareResourcePage() {
    return $('.undefined.dialog-wrapper');
  }

  get inputName() {
    return $('#share-name-input');
  }

  getAutocompleteItem(name) {
    return $('.autocomplete-content.scroll ul').$(`span*=${name}`);
  }

  get submitButton() {
    return $('.share-form button[type=submit]');
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to create a new password
   */
  async shareResource(username, passphrase) {
    await this.inputName.waitForClickable();
    await this.inputName.setValue(username);
    await this.getAutocompleteItem(username).waitForExist();
    await this.getAutocompleteItem(username).click();
    await this.submitButton.waitForClickable();
    await this.submitButton.click();
    await PassphraseEntryDialogPage.entryPassphrase(passphrase);
  }
}

module.exports = new ShareDialogPage();
