const PassphraseEntryDialogPage = require('../../AuthenticationPassphrase/InputPassphrase/InputPassphrase.page');
const GenerateResourcePasswordPage = require("../../ResourcePassword/GenerateResourcePassword/GenerateResourcePassword.page");
/**
 * sub page containing specific selectors and methods for a specific page
 */
class EditResourcePage {
  /**
   * define selectors using getter methods
   */
  get editPasswordPage() {
    return $('.edit-password-dialog.dialog-wrapper');
  }

  get inputName() {
    return $('#edit-password-form-name');
  }

  get inputUri() {
    return $('#edit-password-form-uri');
  }

  get inputUsername() {
    return $('#edit-password-form-username');
  }

  get inputPassword() {
    return $('#edit-password-form-password');
  }

  get openPasswordGenerator() {
    return $('.edit-password-dialog.dialog-wrapper .password-generator');
  }

  get inputDescription() {
    return $('#edit-password-form-description');
  }

  get submitButton() {
    return $('input[type=submit]');
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to edit a password
   */
  editPassword(name, uri, username, password, description) {
    this.editPasswordPage.waitForExist();
    this.inputName.setValue(name);
    this.inputUri.setValue(uri);
    this.inputUsername.setValue(username);
    this.inputPassword.waitForEnabled();
    this.inputPassword.setValue(password);
    this.inputDescription.setValue(description);
    this.openPasswordGenerator.waitForClickable();
    this.openPasswordGenerator.click();
    GenerateResourcePasswordPage.generatePassword();
    this.submitButton.waitForClickable();
    this.submitButton.click();
    PassphraseEntryDialogPage.entryPassphrase(username);
  }
}

module.exports = new EditResourcePage();
