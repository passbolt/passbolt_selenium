/**
 * sub page containing specific selectors and methods for a specific page
 */
class DeleteResourceFolderPage {
  /**
   * define selectors using getter methods
   */
  get deleteFolderPage() {
    return $('.folder-create-dialog.dialog-wrapper');
  }

  get submitButton() {
    return $('input[type=submit]');
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to delete a folder
   */
  deleteFolder() {
    this.deleteFolderPage.waitForExist();
    this.submitButton.waitForClickable();
    this.submitButton.click();
  }
}

module.exports = new DeleteResourceFolderPage();
