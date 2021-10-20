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
class FilterResourcesByTextPage {
  /**
   * define selectors using getter methods
   */
  get inputSearch() {
    return $('#app .search .input.search.required input[type=search]');
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to copy the secret of a resource in search
   */
  async pasteClipBoardToVerify(passwordToVerify) {
    await this.inputSearch.waitForClickable();
    await this.inputSearch.click();
    const keyControl = this._getControlKey();
    await browser.keys([keyControl, 'v']);
    expect(this.inputSearch).toHaveValue(passwordToVerify);
    await this.resetSearchValue();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to reset the value of the search
   */
  async resetSearchValue() {
    // To force a change event for the search bar and remove the value
    const keyControl = this._getControlKey();
    await browser.keys([keyControl, 'a']);
    await browser.keys([keyControl, 'x']);
    expect(this.inputSearch).toHaveValue('');
    // wait until debounce search is finish
    await browser.pause(300);
  }

  _getControlKey() {
    if (browser.capabilities.platformName.indexOf('mac') !== -1) {
      return 'Command';
    } else {
      return 'Control';
    }
  }
}

module.exports = new FilterResourcesByTextPage();
