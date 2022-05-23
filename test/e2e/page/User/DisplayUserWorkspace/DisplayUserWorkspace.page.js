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
class DisplayUserWorkspacePage {
  /**
   * define selectors using getter methods
   */
  get usersWorkspace() {
    return $('.page.user');
  }

  get createButton() {
    return $('.button.create.primary');
  }

  get newUserButton() {
    return $('.dropdown-content.menu').$('=New user');
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to use the user workspace
   */
  async openCreateUser() {
    await this.createButton.waitForClickable();
    await this.createButton.click();
    await this.newUserButton.waitForClickable();
    await this.newUserButton.click();
  }
}

module.exports = new DisplayUserWorkspacePage();
