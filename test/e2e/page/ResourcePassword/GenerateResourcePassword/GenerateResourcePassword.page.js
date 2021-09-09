/**
 * sub page containing specific selectors and methods for a specific page
 */
class GenerateResourcePasswordPage {
  /**
   * define selectors using getter methods
   */
  get generateResourcePasswordPage() {
    return $('.generate-resource-password-dialog.dialog-wrapper');
  }

  get submitButton() {
    return $('.generate-resource-password-dialog.dialog-wrapper .dialog-content input[type=submit]');
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to generate a new password
   */
  generatePassword() {
    this.generateResourcePasswordPage.waitForExist();
    this.submitButton.waitForClickable();
    this.submitButton.click();
  }
}

module.exports = new GenerateResourcePasswordPage();
