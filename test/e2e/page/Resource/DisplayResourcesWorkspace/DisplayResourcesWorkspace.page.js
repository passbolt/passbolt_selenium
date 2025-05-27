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
class DisplayResourcesWorkspacePage {
  /**
   * define selectors using getter methods
   */
  get passwordWorkspace() {
    return $('.page.password');
  }

  get createButton() {
    return $('button.create.primary');
  }

  get newPasswordButton() {
    return $('.dropdown-content.menu button#password_action');
  }

  get newFolderButton() {
    return $('.dropdown-content.menu button#folder_action');
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to open create password
   */
  async openCreatePassword() {
    await this.passwordWorkspace.waitForExist();
    await this.createButton.click();
    await this.newPasswordButton.click();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to open create folder
   */
  async openCreateFolder() {
    await this.passwordWorkspace.waitForExist();
    await this.createButton.click();
    await this.newFolderButton.click();
  }
}

module.exports = new DisplayResourcesWorkspacePage();
