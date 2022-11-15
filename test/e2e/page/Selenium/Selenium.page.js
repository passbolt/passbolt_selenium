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

/**
 * sub page containing specific selectors and methods for a specific page
 */
class SeleniumPage {
  /**
   * define selectors using getter methods
   */
  get redirectButton() {
    return $(".buttonContent a");
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to reset instance default
   */
  async resetInstanceDefault() {
    await browser.url("seleniumtests/resetInstance/default");
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to show last email and go to the url
   */
  async showLastEmailAndRedirect(username) {
    // force a wait to be sure the email has been received
    await browser.pause(500);
    await this.openUrl(`showLastEmail/${username}`);
    await this.redirectButton.waitForExist();
    const url = await this.redirectButton.getAttribute("href");
    return browser.url(url);
  }

  /**
   * Open url
   */
  openUrl(path) {
    return browser.url(`seleniumtests/${path}`);
  }

  /**
   * Switch to iframe
   */
  async switchToIframe(cssSelector) {
    const iframe = $(cssSelector);
    await iframe.waitForExist({ timeout: 15000 });
    await iframe.waitForClickable({ timeout: 15000 });
    // $(cssSelector) cannot be use for the switch iframe with the current version of wdio for no apparent reason.
    await browser.pause(500);
    const iframeWithFindElement = await browser.findElement(
      "css selector",
      cssSelector
    );
    await browser.switchToFrame(iframeWithFindElement);
  }
}

module.exports = new SeleniumPage();
