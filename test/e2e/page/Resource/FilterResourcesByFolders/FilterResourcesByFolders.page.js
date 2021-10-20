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
class FilterResourcesByFoldersPage {
  /**
   * define selectors using getter methods
   */
  get firstFolder() {
    return $('.folders-tree .folder-item .main-cell a');
  }

  get caretRightFolderSelected() {
    return $('.folders-tree .row.selected .main-cell a .svg-icon.caret-right svg');
  }

  getFolderNamed(name) {
    return $('.folders-tree').$(`=${name}`);
  }

  get renameFolder() {
    return $('.contextual-menu').$('=Rename');
  }

  get deleteFolder() {
    return $('.contextual-menu').$('=Delete');
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to select a folder
   */
  async selectedFirstFolder() {
    await this.firstFolder.waitForExist();
    await this.firstFolder.waitForClickable();
    await this.firstFolder.click();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to select a folder
   */
  async selectedFolderNamed(name) {
    await this.getFolderNamed(name).waitForExist();
    await this.getFolderNamed(name).waitForClickable();
    await this.getFolderNamed(name).click();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to expand the selected folder
   */
  async expandFolderSelected() {
    await this.caretRightFolderSelected.waitForExist();
    await this.caretRightFolderSelected.waitForClickable();
    await this.caretRightFolderSelected.click();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to open folder contextual menu
   */
  async openFolderContextualMenu() {
    await this.firstFolder.waitForExist();
    await this.firstFolder.waitForClickable();
    await this.firstFolder.click({ button: 'right' });
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to open folder rename dialog
   */
  async openRenameResourceFolder() {
    await this.openFolderContextualMenu();
    await this.renameFolder.waitForClickable();
    await this.renameFolder.click();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to open folder delete dialog
   */
  async openDeleteResourceFolder() {
    await this.openFolderContextualMenu();
    await this.deleteFolder.waitForClickable();
    await this.deleteFolder.click();
  }
}

module.exports = new FilterResourcesByFoldersPage();
