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
class RenameResourceFolderPage {
  /**
   * define selectors using getter methods
   */
  get renameFolderPage() {
    return $('.rename-folder-dialog.dialog-wrapper');
  }

  get inputName() {
    return $('#folder-name-input');
  }

  get submitButton() {
    return $('input[type=submit]');
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to rename a folder
   */
  async renameFolder(name) {
    await this.renameFolderPage.waitForExist();
    await this.inputName.setValue(name);
    await this.submitButton.waitForClickable();
    await this.submitButton.click();
  }
}

module.exports = new RenameResourceFolderPage();
