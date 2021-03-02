const SeleniumPage = require('../../Selenium/Selenium.page');
const CreateGpgKeyPage = require('../../Authentication/CreateGpgKey/CreateGpgKey.page');
const DownloadRecoveryKit = require('../../Authentication/DownloadRecoveryKit/DownloadRecoveryKit.page');
const ChooseSecurityTokenPage = require('../../Authentication/ChooseSecurityToken/ChooseSecurityToken.page');

/**
 * sub page containing specific selectors and methods for a specific page
 */
class SetupAuthenticationPage {
  /**
   * define selectors using getter methods
   */
  get container() {
    return $('#container');
  }

  get iframe() {
    return $('#passbolt-iframe-setup');
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to setup using username and password
   */
  setup(username) {
    this.container.waitForExist();
    // Show last email and redirect for account setup
    SeleniumPage.showLastEmailAndRedirect(username);

    // Go to iframe setup setup
    this.iframe.waitForExist();
    browser.switchToFrame(this.iframe);

    // Choose passphrase
    CreateGpgKeyPage.choosePassphrase(username);

    // Generate key
    DownloadRecoveryKit.generateGpgKey();

    // Choose security token
    ChooseSecurityTokenPage.chooseSecurityToken();
  }
}

module.exports = new SetupAuthenticationPage();
