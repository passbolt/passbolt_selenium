/**
 * sub page containing specific selectors and methods for a specific page
 */
class SeleniumPage {
  /**
   * define selectors using getter methods
   */
  get redirectButton() {
    return $('.buttonContent a');
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to reset instance default
   */
  resetInstanceDefault() {
    this.openUrl('/resetInstance/default');
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to show last email and go to the url
   */
  showLastEmailAndRedirect(username) {
    // force a wait to be sure the email has been received
    browser.pause(500);
    this.openUrl(`/showLastEmail/${username}`);
    this.redirectButton.waitForExist();
    const url = this.redirectButton.getAttribute('href');
    browser.url(url);
  }

  /**
   * Open url
   */
  openUrl(path) {
    return browser.url(`/seleniumtests${path}`);
  }
}

module.exports = new SeleniumPage();
