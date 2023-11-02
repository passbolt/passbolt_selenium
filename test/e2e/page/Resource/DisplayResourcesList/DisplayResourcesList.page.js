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
class DisplayResourcesListPage {
  /**
   * define selectors using getter methods
   */
  get gridPage() {
    return $('.tableview');
  }

  get secretResource() {
    return $('.tableview-content tbody .cell-password div button');
  }

  get firstResource() {
    return $('.tableview-content tbody .cell-name');
  }

  getResourceNamed(name) {
    return $('.tableview-content tbody .cell-name').$(`div=${name}`);
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to copy the secret of a resource
   */
  async copySecretResource(username) {
    await this.gridPage.waitForExist();
    await this.secretResource.waitForClickable();
    await this.secretResource.click();
    await PassphraseEntryDialogPage.entryPassphrase(username);
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to select a resource
   */
  async selectedFirstResource() {
    await this.gridPage.waitForExist();
    await this.firstResource.waitForExist();
    await this.firstResource.waitForClickable();
    await this.firstResource.click();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to select a resource named
   */
  async selectedResourceNamed(name) {
    await this.gridPage.waitForExist();
    await this.getResourceNamed(name).waitForExist();
    await this.getResourceNamed(name).waitForClickable({timeout: 15000});
    await this.getResourceNamed(name).click();
  }
}

module.exports = new DisplayResourcesListPage();
