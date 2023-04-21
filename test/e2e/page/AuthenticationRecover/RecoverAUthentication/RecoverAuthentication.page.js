/**
 * Passbolt ~ Open source password manager for teams
 * Copyright (c) Passbolt SA (https://www.passbolt.com)
 *
 * Licensed under GNU Affero General Public License version 3 of the or any later version.
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Passbolt SA (https://www.passbolt.com)
 * @license       https://opensource.org/licenses/AGPL-3.0 AGPL License
 * @link          https://www.passbolt.com Passbolt(tm)
 * @since         v3.0.0
 */

const SeleniumPage = require("../../Selenium/Selenium.page");
const ImportGpgKeyPage = require("../../Authentication/ImportGpgKey/ImportGpgKey.page");
const CheckPassphrasePage = require("../../Authentication/CheckPasphrase/CheckPassphrase.page");
const ChooseSecurityTokenPage = require("../../Authentication/ChooseSecurityToken/ChooseSecurityToken.page");
const CreateGpgKeyPage = require("../../Authentication/CreateGpgKey/CreateGpgKey.page");

/**
 * sub page containing specific selectors and methods for a specific page
 */
class RecoverAuthenticationPage {
  /**
   * define selectors using getter methods
   */
  get inputUsername() {
    return $("#username-input");
  }

  get inputAgreementTerms() {
    return $("#checkbox-terms");
  }

  get btnSubmit() {
    return $("button[type=submit]");
  }

  get iframeSelector() {
    return "#passbolt-iframe-recover";
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to recover using username and password
   */
  async recover(username, privateKey) {
    await this.goToRecover();
    // recover form
    await this.inputUsername.setValue(username);
    await this.inputAgreementTerms.click();
    await this.btnSubmit.waitForClickable();
    await this.btnSubmit.click();

    // Show last email and redirect for account recover
    await SeleniumPage.showLastEmailAndRedirect(username);

    // Go to iframe recover setup
    await SeleniumPage.switchToIframe(this.iframeSelector);

    // import gpg key
    await ImportGpgKeyPage.importGpgKey(privateKey);

    // Enter passphrase
    await CheckPassphrasePage.enterPassphrase(username);

    // Choose security token
    await ChooseSecurityTokenPage.chooseSecurityToken();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. for after the account recovery request
   */
  async recoverByAccountRecoveryRequest(username) {
    // Show last email and redirect for account recover
    await SeleniumPage.checkSubjectContent(username, "You have initiated an account recovery!")
    await SeleniumPage.clickOnRedirection();
    
    // Go to iframe recover setup
    await SeleniumPage.switchToIframe(this.iframeSelector);

    // Choose passphrase
    await CreateGpgKeyPage.choosePassphrase(username);

    // Choose security token
    await ChooseSecurityTokenPage.chooseSecurityToken();
  }

  /**
   * Go to the url
   */
  goToRecover() {
    return browser.url("recover");
  }
}

module.exports = new RecoverAuthenticationPage();
