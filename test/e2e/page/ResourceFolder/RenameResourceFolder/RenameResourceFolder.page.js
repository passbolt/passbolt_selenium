/**
 * sub page containing specific selectors and methods for a specific page
 */
class RenameResourceFolderPage {
  /**
   * define selectors using getter methods
   */
  get renameFolderPage() {
    return $('.rename-folder-dialog.dialog-wrapper');
  }

  get inputName() {
    return $('#folder-name-input');
  }

  get submitButton() {
    return $('input[type=submit]');
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to rename a folder
   */
  renameFolder(name) {
    this.renameFolderPage.waitForExist();
    this.inputName.setValue(name);
    this.submitButton.waitForClickable();
    this.submitButton.click();
  }
}

module.exports = new RenameResourceFolderPage();
