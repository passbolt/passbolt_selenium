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
 * @since         v5.0.0
 */

const SeleniumPage = require('../../Selenium/Selenium.page');

/**
 * sub page containing specific selectors and methods for a specific page
 */
class DisplayUserProfileDropDownPage {
  /**
   * define selectors using getter methods
   */
  get appIframeSelector() {
    return '#passbolt-iframe-app';
  }

  get userProfileDropDownButton() {
    return $('.user.profile.dropdown div.default-avatar');
  }

  get manageAccountButton() {
    return $('.dropdown-content .manage-account');
  }

  get signOutButton() {
    return $(".dropdown-content .sign-out");
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to switch iframe
   */
  async switchAppIframe() {
    await SeleniumPage.switchToIframe(this.appIframeSelector);
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to sign out the current user
   */
  async signOut() {
    await this.userProfileDropDownButton.click();
    await this.signOutButton.click();
  }
}

module.exports = new DisplayUserProfileDropDownPage();
