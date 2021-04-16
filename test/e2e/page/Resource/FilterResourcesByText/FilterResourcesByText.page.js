/**
 * sub page containing specific selectors and methods for a specific page
 */
class FilterResourcesByTextPage {
  /**
   * define selectors using getter methods
   */
  get inputSearch() {
    return $('#app .input.search.required input[type=search]');
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to copy the secret of a resource in search
   */
  pasteClipBoardToVerify(passwordToVerify) {
    this.inputSearch.waitForClickable();
    this.inputSearch.click();
    const keyControl = this._getControlKey();
    browser.keys([keyControl, 'v']);
    expect(this.inputSearch).toHaveValue(passwordToVerify);
    this.resetSearchValue();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to reset the value of the search
   */
  resetSearchValue() {
    // To force a change event for the search bar and remove the value
    const keyControl = this._getControlKey();
    browser.keys([keyControl, 'a']);
    browser.keys([keyControl, 'x']);
    expect(this.inputSearch).toHaveValue('');
    // wait until debounce search is finish
    browser.pause(300);
  }

  _getControlKey() {
    if (browser.capabilities.platformName === 'mac os x') {
      return 'Command';
    } else {
      return 'Control';
    }
  }
}

module.exports = new FilterResourcesByTextPage();
