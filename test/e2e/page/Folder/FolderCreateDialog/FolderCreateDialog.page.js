/**
 * sub page containing specific selectors and methods for a specific page
 */
class FolderCreateDialogPage {
  /**
   * define selectors using getter methods
   */
  get createFolderPage() {
    return $('.folder-create-dialog.dialog-wrapper');
  }

  get inputName() {
    return $('#folder-name-input');
  }

  get submitButton() {
    return $('input[type=submit]');
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to create a new folder
   */
  createFolder(name) {
    this.createFolderPage.waitForExist();
    this.inputName.setValue(name);
    this.submitButton.waitForClickable();
    this.submitButton.click();
  }
}

module.exports = new FolderCreateDialogPage();
