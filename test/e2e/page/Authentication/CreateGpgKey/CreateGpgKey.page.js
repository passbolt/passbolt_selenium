/**
 * sub page containing specific selectors and methods for a specific page
 */
class CreateGpgKeyPage {
  /**
   * define selectors using getter methods
   */
  get createGpgKeyPage() {
    return $('.choose-passphrase');
  }

  get inputPassphrase() {
    return $('#passphrase-input');
  }

  get btnSubmit() {
    return $('button[type="submit"]');
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to choose passphrase
   */
  choosePassphrase(username) {
    // Choose passphrase
    this.createGpgKeyPage.waitForExist();
    this.inputPassphrase.setValue(username);
    this.btnSubmit.waitForClickable();
    this.btnSubmit.click();
  }
}

module.exports = new CreateGpgKeyPage();
