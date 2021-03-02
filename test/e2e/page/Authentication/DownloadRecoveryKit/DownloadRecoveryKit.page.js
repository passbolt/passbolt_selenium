/**
 * sub page containing specific selectors and methods for a specific page
 */
class DownloadRecoveryKitPage {
  /**
   * define selectors using getter methods
   */
  get downloadRecoveryKitPage() {
    return $('.generate-key-feedback');
  }

  get btnSubmit() {
    return $('button[type="submit"]');
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to generate gpg key
   */
  generateGpgKey() {
    // generate gpg key
    this.downloadRecoveryKitPage.waitForExist();
    this.btnSubmit.waitForClickable();
    this.btnSubmit.click();
  }
}

module.exports = new DownloadRecoveryKitPage();
