/**
 * sub page containing specific selectors and methods for a specific page
 */
class FilterResourcesByShortcutsPage {
  /**
   * define selectors using getter methods
   */
  get filterResourceByShortcutPage() {
    return $('.navigation-secondary.navigation-shortcuts');
  }

  get shareWithMe() {
    return $('.navigation-secondary.navigation-shortcuts ul').$('=Shared with me');
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to open share dialog
   */
  filterBySharedWithMe() {
    this.filterResourceByShortcutPage.waitForExist();
    this.shareWithMe.waitForClickable();
    this.shareWithMe.click();
  }
}

module.exports = new FilterResourcesByShortcutsPage();
