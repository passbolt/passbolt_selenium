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
class CreateResourceFolderPage {
  /**
   * define selectors using getter methods
   */
  get createFolderPage() {
    return $('.folder-create-dialog.dialog-wrapper');
  }

  get inputName() {
    return $('#folder-name-input');
  }

  get submitButton() {
    return $('button[type=submit]');
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to create a new folder
   */
  async createFolder(name) {
    await this.createFolderPage.waitForExist();
    await this.inputName.setValue(name);
    await this.submitButton.waitForClickable();
    await this.submitButton.click();
  }
}

module.exports = new CreateResourceFolderPage();
