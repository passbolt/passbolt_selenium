const PassphraseEntryDialogPage = require('../../AuthenticationPassphrase/InputPassphrase/InputPassphrase.page');
/**
 * sub page containing specific selectors and methods for a specific page
 */
class CreateResourcePage {
  /**
   * define selectors using getter methods
   */
  get createPasswordPage() {
    return $('.create-password-dialog.dialog-wrapper');
  }

  get inputName() {
    return $('#create-password-form-name');
  }

  get inputUri() {
    return $('#create-password-form-uri');
  }

  get inputUsername() {
    return $('#create-password-form-username');
  }

  get inputPassword() {
    return $('#create-password-form-password');
  }

  get inputDescription() {
    return $('#create-password-form-description');
  }

  get submitButton() {
    return $('input[type=submit]');
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to create a new password
   */
  createPassword(name, uri, username, password, description) {
    this.createPasswordPage.waitForExist();
    this.inputName.setValue(name);
    this.inputUri.setValue(uri);
    this.inputUsername.setValue(username);
    this.inputPassword.setValue(password);
    this.inputDescription.setValue(description);
    this.submitButton.waitForClickable();
    this.submitButton.click();
    PassphraseEntryDialogPage.entryPassphrase(username);
  }
}

module.exports = new CreateResourcePage();
