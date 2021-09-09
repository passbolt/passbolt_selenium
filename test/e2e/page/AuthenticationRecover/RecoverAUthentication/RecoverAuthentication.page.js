const SeleniumPage = require('../../Selenium/Selenium.page');
const ImportGpgKeyPage = require('../../Authentication/ImportGpgKey/ImportGpgKey.page');
const CheckPassphrasePage = require('../../Authentication/CheckPasphrase/CheckPassphrase.page');
const ChooseSecurityTokenPage = require('../../Authentication/ChooseSecurityToken/ChooseSecurityToken.page');

/**
 * sub page containing specific selectors and methods for a specific page
 */
class RecoverAuthenticationPage {
  /**
   * define selectors using getter methods
   */
  get inputUsername() {
    return $('#username-input');
  }

  get inputAgreementTerms() {
    return $('label[for=checkbox-terms] span');
  }

  get btnSubmit() {
    return $('input[type=submit]');
  }

  get iframe() {
    return $('#passbolt-iframe-recover');
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to recover using username and password
   */
  recover(username, privateKey) {
    this.goToRecover();
    // recover form
    this.inputUsername.setValue(username);
    this.inputAgreementTerms.click();
    this.btnSubmit.waitForClickable();
    this.btnSubmit.click();

    // Show last email and redirect for account recover
    SeleniumPage.showLastEmailAndRedirect(username);

    // Go to iframe recover setup
    this.iframe.waitForExist();
    browser.switchToFrame(this.iframe);

    // import gpg key
    ImportGpgKeyPage.importGpgKey(privateKey);

    // Enter passphrase
    CheckPassphrasePage.enterPassphrase(username);

    // Choose security token
    ChooseSecurityTokenPage.chooseSecurityToken();
  }

  /**
   * Go to the url
   */
  goToRecover() {
    return browser.url('/recover');
  }
}

module.exports = new RecoverAuthenticationPage();
