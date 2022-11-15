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
  async importGpgKey(privateKey) {
    // import gpg key
    await this.importGpgKeyTextarea.waitForExist();
    // to add value faster than setValue
    // await browser.executeScript("arguments[0].value=arguments[1];", [this.importGpgKeyTextarea, privateKey]);
    // to force a change event for react component (doesn't work with dispatch event method) (add 2 spaces for firefox)
    await this.importGpgKeyTextarea.setValue(privateKey, {wait:10000});
    await this.btnSubmit.waitForClickable();
    await this.btnSubmit.click();
  }
}

module.exports = new ImportGpgKeyPage();
