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

const SeleniumPage = require('../../Selenium/Selenium.page');

/**
 * sub page containing specific selectors and methods for a specific page
 */
class DisplayMainMenuPage {
  /**
   * define selectors using getter methods
   */
  get appIframeSelector() {
    return'#passbolt-iframe-app';
  }

  get navigationPage() {
    return $('.primary.navigation');
  }

  get userMenu() {
    return $('.primary.navigation.top ul').$('span=users');
  }

  get administrationMenu() {
    return $('.primary.navigation.top ul').$('span=administration');
  }

  get signOutMenu() {
    return $('.primary.navigation.top .right .main-cell button span');
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
    await this.signOutMenu.waitForClickable({timeout: 15000});
    await this.signOutMenu.click();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to go to the user workspace
   */
  async goToUserWorkspace() {
    await this.navigationPage.waitForExist();
    await this.userMenu.waitForClickable();
    await this.userMenu.click();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to go to the adminstration workspace
   */
  async goToAdminstrationWorkspace() {
    await this.navigationPage.waitForExist();
    await this.administrationMenu.waitForClickable();
    await this.administrationMenu.click();
    await browser.switchToParentFrame();
  }
}

module.exports = new DisplayMainMenuPage();
