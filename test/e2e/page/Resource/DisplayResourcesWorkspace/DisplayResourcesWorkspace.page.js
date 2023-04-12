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
    return $('.button.create.primary');
  }

  get newPasswordButton() {
    return $('.dropdown-content.menu #password_action');
  }

  get newFolderButton() {
    return $('.dropdown-content.menu #folder_action');
  }

  get editButton() {
    return $('.header.third  .col2_3.actions-wrapper #edit_action a');
  }

  get moreButton() {
    return $('.header.third  .col2_3.actions-wrapper .dropdown a.button');
  }

  get deleteButton() {
    return $('.header.third  .col2_3.actions-wrapper .dropdown-content.menu #delete_action a');
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to open create password
   */
  async openCreatePassword() {
    await this.passwordWorkspace.waitForExist();
    await this.createButton.waitForClickable();
    await this.createButton.click();
    await this.newPasswordButton.waitForClickable();
    await this.newPasswordButton.click();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to open create folder
   */
  async openCreateFolder() {
    await this.passwordWorkspace.waitForExist();
    await this.createButton.waitForClickable();
    await this.createButton.click();
    await this.newFolderButton.waitForClickable();
    await this.newFolderButton.click();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to open edit password
   */
  async openEditPassword(username) {
    await this.passwordWorkspace.waitForExist();
    await this.editButton.waitForClickable();
    await this.editButton.click();
    await PassphraseEntryDialogPage.entryPassphrase(username);
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to open delete password
   */
  async openDeletePassword() {
    await this.passwordWorkspace.waitForExist();
    await this.moreButton.waitForClickable();
    await this.moreButton.click();
    await this.deleteButton.waitForClickable();
    await this.deleteButton.click();
  }
}

module.exports = new DisplayResourcesWorkspacePage();
