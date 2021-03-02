/**
 * sub page containing specific selectors and methods for a specific page
 */
class PassphraseEntryDialogPage {
  /**
   * define selectors using getter methods
   */
  get entryPassphrasePage() {
    return $('.dialog.passphrase-entry');
  }

  get inputPassphrase() {
    return $('#passphrase-entry-form-passphrase');
  }

  get btnSubmit() {
    return $('.dialog.passphrase-entry input[type="submit"]');
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to entry passphrase
   */
  entryPassphrase(username) {
    // Entry passphrase
    this.entryPassphrasePage.waitForExist();
    this.inputPassphrase.setValue(username);
    this.btnSubmit.waitForClickable();
    this.btnSubmit.click();
  }
}

module.exports = new PassphraseEntryDialogPage();
