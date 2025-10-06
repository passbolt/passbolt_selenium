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
class DisplayResourceActionBarPage {
  /**
   * define selectors using getter methods
   */
  get actionBar() {
    return $('.action-bar .actions');
  }

  get filterBar() {
    return $('.action-bar .actions-filter');
  }

  get shareButton() {
    return this.actionBar.$('ul li#share_action');
  }

  get editButton() {
    return this.actionBar.$('ul li#edit_action');
  }

  get deleteButton() {
    return this.actionBar.$('ul li#delete_action');
  }

  get filterButton() {
    return this.filterBar.$('button.button-dropdown');
  }

  get filterBySharedWithMeItem() {
    return $('.dropdown ul.dropdown-content').$('span=Shared with me');
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. open the share resource dialog
   */
  async openShareResourceDialog() {
    await this.actionBar.waitForExist();
    await this.shareButton.click();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. filter the resources that are shared with me.
   */
  async filterBySharedWithMe() {
    await this.filterBar.waitForExist();
    await this.filterButton.click();
    await this.filterBySharedWithMeItem.click();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. open the edit resource dialog
   */
  async openEditResourceDialog(username) {
    await this.actionBar.waitForExist();
    await this.editButton.click();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. open the delete resource dialog
   */
  async openDeleteResourceDialog(username) {
    await this.actionBar.waitForExist();
    await this.deleteButton.click();
  }

}

module.exports = new DisplayResourceActionBarPage();
