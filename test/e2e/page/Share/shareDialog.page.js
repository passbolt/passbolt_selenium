const PassphraseEntryDialogPage = require('../Passphrase/PassphraseEntryDialog/PassphraseEntryDialog.page');
/**
 * sub page containing specific selectors and methods for a specific page
 */
class ShareDialogPage {
  /**
   * define selectors using getter methods
   */
  get shareResourcePage() {
    return $('.undefined.dialog-wrapper');
  }

  get inputName() {
    return $('#share-name-input');
  }

  getAutocompleteItem(name) {
    return $('.autocomplete-content.scroll ul').$(`span*=${name}`);
  }

  get submitButton() {
    return $('.share-form input[type=submit]');
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to create a new password
   */
  shareResource(username, passphrase) {
    this.inputName.waitForClickable();
    this.inputName.setValue(username);
    this.getAutocompleteItem(username).waitForExist();
    this.getAutocompleteItem(username).click();
    this.submitButton.waitForClickable();
    this.submitButton.click();
    PassphraseEntryDialogPage.entryPassphrase(passphrase);
  }
}

module.exports = new ShareDialogPage();
