/**
 * sub page containing specific selectors and methods for a specific page
 */
class ImportGpgKeyPage {
  /**
   * define selectors using getter methods
   */
  get importGpgKeyTextarea() {
    return $('textarea[name=private-key]');
  }

  get btnSubmit() {
    return $('button[type="submit"]');
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to import gpg key
   */
  importGpgKey(privateKey) {
    // import gpg key
    this.importGpgKeyTextarea.waitForExist();
    // to add value faster than setValue
    browser.executeScript("arguments[0].value=arguments[1];", [this.importGpgKeyTextarea, privateKey]);
    // to force a change event for react component (doesn't work with dispatch event method) (add 2 spaces for firefox)
    this.importGpgKeyTextarea.addValue("  ");
    this.btnSubmit.waitForClickable();
    this.btnSubmit.click();
  }
}

module.exports = new ImportGpgKeyPage();
