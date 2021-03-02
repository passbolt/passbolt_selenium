/**
 * sub page containing specific selectors and methods for a specific page
 */
class DisplayUserWorkspacePage {
  /**
   * define selectors using getter methods
   */
  get usersWorkspace() {
    return $('.page.user');
  }

  get createButton() {
    return $('.button.create.primary.ready');
  }

  get newUserButton() {
    return $('.dropdown-content.menu').$('=New user');
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to use the user workspace
   */
  openCreateUser() {
    this.createButton.waitForClickable();
    this.createButton.click();
    this.newUserButton.waitForClickable();
    this.newUserButton.click();
  }
}

module.exports = new DisplayUserWorkspacePage();
