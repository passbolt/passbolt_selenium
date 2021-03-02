/**
 * sub page containing specific selectors and methods for a specific page
 */
class CheckPassphrasePage {
  /**
   * define selectors using getter methods
   */
  get checkPassphrasePage() {
    return $('.enter-passphrase');
  }

  get inputPassphrase() {
    return $('#passphrase');
  }

  get btnSubmit() {
    return $('button[type="submit"]');
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to enter passphrase
   */
  enterPassphrase(username) {
    // Enter passphrase
    this.checkPassphrasePage.waitForExist();
    this.inputPassphrase.setValue(username);
    this.btnSubmit.waitForClickable();
    this.btnSubmit.click();
  }
}

module.exports = new CheckPassphrasePage();
