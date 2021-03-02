/**
 * sub page containing specific selectors and methods for a specific page
 */
class CreateUserDialogPage {
  /**
   * define selectors using getter methods
   */
  get createUserPage() {
    return $('.user-create-dialog.dialog-wrapper');
  }

  get inputFirstname() {
    return $('#user-first-name-input');
  }

  get inputLastname() {
    return $('#user-last-name-input');
  }

  get inputUsername() {
    return $('#user-username-input');
  }

  get submitButton() {
    return $('input[type=submit]');
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to create a new user
   */
  createUser(firstname, lastname, username) {
    this.inputFirstname.setValue(firstname);
    this.inputLastname.setValue(lastname);
    this.inputUsername.setValue(username);
    this.submitButton.waitForClickable();
    this.submitButton.click();
  }
}

module.exports = new CreateUserDialogPage();
