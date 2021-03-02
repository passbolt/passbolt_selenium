/**
 * sub page containing specific selectors and methods for a specific page
 */
class ChooseSecurityTokenPage {
  /**
   * define selectors using getter methods
   */
  get chooseSecurityTokenPage() {
    return $('.choose-security-token');
  }

  get btnSubmit() {
    return $('button[type="submit"]');
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to choose security token
   */
  chooseSecurityToken() {
    // Choose security token
    this.chooseSecurityTokenPage.waitForExist();
    this.btnSubmit.waitForClickable();
    this.btnSubmit.click();
  }
}

module.exports = new ChooseSecurityTokenPage();
