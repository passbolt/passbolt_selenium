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
 * @since         v5.0.0
 */

/**
 * sub page containing specific selectors and methods for a specific page
 */
class DisplayFoldersListPage {

  /**
   * return the group list
   */
  get folderList() {
    return $(".navigation-secondary-tree.navigation-folders .accordion-content ul");
  }

  /**
   * return the selected row
   */
  get selectedFolder() {
    return this.folderList.$(".row.selected");
  }

  /**
   * return the three dots buttons
   */
  get buttonRowActions() {
    return this.selectedFolder.$(".dropdown button");
  }

  /**
   * return the contextual menu
   */
  get contextualMenu() {
    return $(".contextual-menu");
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to click on action button for the selected row
   */
  async clickOnActionButton() {
    await this.selectedFolder.waitForExist();
    await this.buttonRowActions.click();
    await this.contextualMenu.waitForExist();
  }

  /**
   * return the share contextual menu item.
   */
  get shareContextualMenuItem() {
    return $(".contextual-menu button.share");
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to open the share dialog for the selected folder
   */
  async openSelectedFolderShareDialog() {
    await this.clickOnActionButton();
    await this.shareContextualMenuItem.click();
  }
}

module.exports = new DisplayFoldersListPage();
