/**
 * sub page containing specific selectors and methods for a specific page
 */
class DeleteResourcePage {
  /**
   * define selectors using getter methods
   */
  get deletePasswordPage() {
    return $('.delete-password-dialog.dialog-wrapper');
  }

  get submitButton() {
    return $('input[type=submit]');
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to delete a password
   */
  deletePassword() {
    this.deletePasswordPage.waitForExist();
    this.submitButton.waitForClickable();
    this.submitButton.click();
  }
}

module.exports = new DeleteResourcePage();
