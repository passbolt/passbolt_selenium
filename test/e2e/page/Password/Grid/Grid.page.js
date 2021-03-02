const PassphraseEntryDialogPage = require('../../Passphrase/PassphraseEntryDialog/PassphraseEntryDialog.page');

/**
 * sub page containing specific selectors and methods for a specific page
 */
class GridPage {
  /**
   * define selectors using getter methods
   */
  get gridPage() {
    return $('.tableview');
  }

  get secretResource() {
    return $('.tableview-content.scroll tbody .cell-secret.m-cell.password div a');
  }

  get firstResource() {
    return $('.tableview-content.scroll tbody .cell-name');
  }

  getResourceNamed(name) {
    return $('.tableview-content').$(`div=${name}`);
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to copy the secret of a resource
   */
  copySecretResource(username) {
    this.gridPage.waitForExist();
    this.secretResource.waitForClickable();
    this.secretResource.click();
    PassphraseEntryDialogPage.entryPassphrase(username);
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to select a resource
   */
  selectedFirstResource() {
    this.gridPage.waitForExist();
    this.firstResource.waitForExist();
    this.firstResource.waitForClickable();
    this.firstResource.click();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to select a resource named
   */
  selectedResourceNamed(name) {
    this.gridPage.waitForExist();
    this.getResourceNamed(name).waitForExist();
    this.getResourceNamed(name).waitForClickable();
    this.getResourceNamed(name).click();
  }
}

module.exports = new GridPage();
