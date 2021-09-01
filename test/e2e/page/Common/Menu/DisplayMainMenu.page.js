/**
 * sub page containing specific selectors and methods for a specific page
 */
class DisplayMainMenuPage {
  /**
   * define selectors using getter methods
   */
  get appIframe() {
    return $('#passbolt-iframe-app');
  }

  get navigationPage() {
    return $('.primary.navigation');
  }

  get userMenu() {
    return $('.primary.navigation.top ul').$('=users');
  }

  get signOutMenu() {
    return $('.primary.navigation.top .right .main-cell a span');
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to switch iframe
   */
  switchAppIframe() {
    // Switch to parent to avoid an issue on firefox
    browser.switchToParentFrame();
    this.appIframe.waitForExist();
    browser.switchToFrame(this.appIframe);
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to sign out the current user
   */
  signOut() {
    this.signOutMenu.waitForClickable();
    this.signOutMenu.click();
    // Switch to parent to avoid an issue on firefox
    browser.switchToParentFrame();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to go to the user workspace
   */
  goToUserWorkspace() {
    this.navigationPage.waitForExist();
    this.userMenu.waitForClickable();
    this.userMenu.click();
  }
}

module.exports = new DisplayMainMenuPage();
