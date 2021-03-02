const PassphraseEntryDialogPage = require('../../Passphrase/PassphraseEntryDialog/PassphraseEntryDialog.page');

/**
 * sub page containing specific selectors and methods for a specific page
 */
class PasswordWorkspacePage {
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
    return $('.dropdown-content.menu').$('=New password');
  }

  get newFolderButton() {
    return $('.dropdown-content.menu').$('=New folder');
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
  openCreatePassword() {
    this.passwordWorkspace.waitForExist();
    this.createButton.waitForClickable();
    this.createButton.click();
    if (browser.config.passbolt.edition === 'pro') {
      this.newPasswordButton.waitForClickable();
      this.newPasswordButton.click();
    }
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to open create folder
   */
  openCreateFolder() {
    this.passwordWorkspace.waitForExist();
    this.createButton.waitForClickable();
    this.createButton.click();
    this.newFolderButton.waitForClickable();
    this.newFolderButton.click();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to open edit password
   */
  openEditPassword(username) {
    this.passwordWorkspace.waitForExist();
    this.editButton.waitForClickable();
    this.editButton.click();
    PassphraseEntryDialogPage.entryPassphrase(username);
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to open delete password
   */
  openDeletePassword() {
    this.passwordWorkspace.waitForExist();
    this.moreButton.waitForClickable();
    this.moreButton.click();
    this.deleteButton.waitForClickable();
    this.deleteButton.click();
  }
}

module.exports = new PasswordWorkspacePage();
