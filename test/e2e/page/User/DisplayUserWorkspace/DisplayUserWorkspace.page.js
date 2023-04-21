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

const {
  organizationPrivateKey,
  organizationPassphrase,
} = require("../../Authentication/ImportGpgKey/ImportGpgOrganizationKey.data");
const DisplayAdministrationAccountRecoveryPage = require("../../Administration/AdminstrationAccountRecovery/AdministrationAccountRecovery.page");
/**
 * sub page containing specific selectors and methods for a specific page
 */
class DisplayUserWorkspacePage {
  /**
   * define selectors using getter methods
   */
  get usersWorkspace() {
    return $(".page.user");
  }

  /**
   * return  button to create user
   */
  get createButton() {
    return $("button.create.primary");
  }

  /**
   * define selectors using getter methods
   */
  get inputSearch() {
    return $("input[type=search]");
  }

  /**
   * return the option to select new user creation
   */
  get newUserButton() {
    return $(".dropdown-content.menu").$("span=New user");
  }

  /**
   * return the option to select new group creation
   */
   get newGroupButton() {
    return $(".dropdown-content.menu").$("span=New group");
  }


  /**
   * return the user table
   */
  get userTable() {
    return $(".tableview");
  }

  /**
   * return the user table content
   */
  get userTableContent() {
    return $(".tableview-content");
  }

  /**
   * return the account recovery detail sidebar
   */
  get accountRecoveryDetail() {
    return $(".detailed-account-recovery");
  }

  /**
   * return the account recovery content
   */
  get accountRevoryContent() {
    return $(".accordion-content");
  }

  /**
   * return the account recovery button
   */
  get accountRecoveryReviewButton() {
    return $(".pending-request-status button");
  }

  /**
   * return the account recovery review dialog
   */
  get accountRecoveryReviewDialog() {
    return $(".review-account-recovery-dialog");
  }

  /**
   * return the account recovery reject option
   */
  get accountRecoveryReviewRejectOption() {
    return $("label[for='statusRecoverAccountReject']");
  }

  /**
   * return the account recovery validate option
   */
  get accountRecoveryReviewValidateOption() {
    return $("label[for='statusRecoverAccountAccept']");
  }

  /**
   * dialog footer buttons action
   */
  get dialogFooter() {
    return $(".submit-wrapper");
  }

  /**
   * dialog footer buttons action
   */
  get submitButton() {
    return this.dialogFooter.$("button[type='submit']");
  }

  /**
   * return the cell name
   */
  get cellName() {
    return $(".cell-name");
  }

  /**
   * return the breadcrumbs
   */
  get breadcrumbs() {
    return $(".breadcrumbs .menu").$("button*=Search: Admin User");
  }

  /**
   * return the user table raw
   */
  userTableRaw(user) {
    return this.userTableContent.$(`div[title="${user}"]`);
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to search specific user
   */
  async searchUser(username) {
    await this.inputSearch.setValue(username);
    await this.breadcrumbs.waitForExist();
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

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. open the group user option
   */
   async openCreateGroup() {
    await this.createButton.waitForClickable();
    await this.createButton.click();
    await this.newGroupButton.waitForClickable();
    await this.newGroupButton.click();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to click on a specific table raw
   */
  async clickOnUserRaw(user) {
    await this.userTable.waitForExist();
    await this.userTableRaw(user).waitForClickable();
    await this.userTableRaw(user).click();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to click on the review request account recovery
   */
  async clickOnReviewAccountRecovery() {
    await this.accountRecoveryDetail.click();
    await this.accountRecoveryReviewButton.waitForExist();
    await this.accountRecoveryReviewButton.click();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to validate the account recovery request
   */
  async validateAccountRecoveryRequest() {
    await this.accountRecoveryReviewValidateOption.waitForClickable();
    await this.accountRecoveryReviewValidateOption.click();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to reject the account recovery request
   */
  async rejectAccountRecoveryRequest() {
    await this.accountRecoveryReviewRejectOption.waitForClickable();
    await this.accountRecoveryReviewRejectOption.click();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to submit the account recovery review
   */
  async submitReviewAccountRecovery() {
    await this.submitButton.waitForClickable();
    await this.submitButton.click();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. sort by name
   */
  async sortByName() {
    await this.cellName.waitForClickable();
    await this.cellName.click();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to review the account recovery request
   */
  async reviewAccountRecoveryRequest(user, allow) {
    await this.clickOnReviewAccountRecovery();
    if (allow) {
      await this.validateAccountRecoveryRequest();
      await this.submitReviewAccountRecovery();
      await DisplayAdministrationAccountRecoveryPage.importAccountRecoveryPrivateKeyAndSave(
        organizationPrivateKey,
        organizationPassphrase
      );
    } else {
      await this.rejectAccountRecoveryRequest();
      await this.submitReviewAccountRecovery();
    }
  }
}

module.exports = new DisplayUserWorkspacePage();
