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
class DisplayGroupListPage {

 /**
  * return the group list
  */
  get groupList() {
    return $(".navigation-secondary-tree .accordion-content ul");
  }

 /**
  * return the selected row for group
  */
  get selectedGroup() {
    return this.groupList.$(".row.selected");
  }

 /**
  * return the three dots buttons
  */
  get buttonRowActions() {
    return this.selectedGroup.$(".dropdown button");
  }

 /**
  * return the contual menu
  */
  get contextualMenu() {
    return $(".contextual-menu");
  }

 /**
  * return the delete option
  */
  get deleteOption() {
    return $("#delete-group");
  }

 /**
  * return the edit option
  */
  get editOption() {
    return $("#edit-group");
  }

 /**
  * a method to encapsule automation code to interact with the page
  * e.g. to return a specific group row
  */
  async groupRow(groupName) {
    await this.groupList.waitForExist()
    return this.groupList.$(`button[title='${groupName}']`);
  }

 /**
  * a method to encapsule automation code to interact with the page
  * e.g. to click on specific row for a group
  */
  async clickOnGroupRow(groupName) {
    const row = await this.groupRow(groupName);
    await row.waitForClickable();
    await row.click();
  }

 /**
  * a method to encapsule automation code to interact with the page
  * e.g. to click on action button for the selected row
  */
  async clickOnActionButton() {
    await this.selectedGroup.waitForExist();
    await this.buttonRowActions.waitForClickable();
    await this.buttonRowActions.click();
    await this.contextualMenu.waitForExist();
  }

 /**
  * a method to encapsule automation code to interact with the page
  * e.g. to click on delete option for the selected row
  */
  async clickOnDeleteButton() {
    await this.clickOnActionButton();
    await this.deleteOption.waitForClickable();
    await this.deleteOption.click();
  }

 /**
  * a method to encapsule automation code to interact with the page
  * e.g. to click on edit option for the selected row
  */
  async clickOnEditButton() {
    await this.clickOnActionButton();
    await this.editOption.waitForClickable();
    await this.editOption.click();
  }

 /**
  * a method to encapsule automation code to interact with the page
  * e.g. to edit user
  */
  async editGroup(groupName) {
    await this.clickOnGroupRow(groupName);
    await this.clickOnEditButton();
  }

 /**
  * a method to encapsule automation code to interact with the page
  * e.g. to delete user
  */
  async deleteGroup(groupName) {
    await this.clickOnGroupRow(groupName);
    await this.clickOnDeleteButton();
  }
}

module.exports = new DisplayGroupListPage();
