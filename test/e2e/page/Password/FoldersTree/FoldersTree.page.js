/**
 * sub page containing specific selectors and methods for a specific page
 */
class FoldersTreePage {
  /**
   * define selectors using getter methods
   */
  get firstFolder() {
    return $('.folders-tree .folder-item .main-cell a');
  }

  get caretRightFolderSelected() {
    return $('.folders-tree .row.selected .main-cell a .svg-icon.caret-right svg');
  }

  getFolderNamed(name) {
    return $('.folders-tree').$(`=${name}`);
  }

  get renameFolder() {
    return $('.contextual-menu').$('=Rename');
  }

  get deleteFolder() {
    return $('.contextual-menu').$('=Delete');
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to select a folder
   */
  selectedFirstFolder() {
    this.firstFolder.waitForExist();
    this.firstFolder.waitForClickable();
    this.firstFolder.click();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to select a folder
   */
  selectedFolderNamed(name) {
    this.getFolderNamed(name).waitForExist();
    this.getFolderNamed(name).waitForClickable();
    this.getFolderNamed(name).click();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to expand the selected folder
   */
  expandFolderSelected() {
    this.caretRightFolderSelected.waitForExist();
    this.caretRightFolderSelected.waitForClickable();
    this.caretRightFolderSelected.click();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to open folder contextual menu
   */
  openFolderContextualMenu() {
    this.firstFolder.waitForExist();
    this.firstFolder.waitForClickable();
    this.firstFolder.click({ button: 'right' });
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to open folder rename dialog
   */
  openFolderRenameDialog() {
    this.openFolderContextualMenu();
    this.renameFolder.waitForClickable();
    this.renameFolder.click();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to open folder delete dialog
   */
  openFolderDeleteDialog() {
    this.openFolderContextualMenu();
    this.deleteFolder.waitForClickable();
    this.deleteFolder.click();
  }
}

module.exports = new FoldersTreePage();
