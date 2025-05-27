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
    return'#passbolt-iframe-app';
  }

  get userProfileDropDownButton() {
    return $('.user.profile.dropdown');
  }

  get signOutButton() {
    return this.userProfileDropDownButton.$(".dropdown-content .sign-out");
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
    await browser.waitUntil(async () => {
      await this.userProfileDropDownButton.click();
      return await this.signOutButton.isExisting();
    }, {timeout: 15000});

    await this.signOutButton.click();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to go to the user workspace
   */
  async goToManageUsersAndGroupsWorkspace() {
    await this.workspaceSwitcher.click();
    await this.manageUsersAndGroupsMenuItem.click();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to go to the adminstration workspace
   */
  async goToOrganizationSettingsWorkspace() {
    await this.workspaceSwitcher.click();
    await this.organizationSettingsMenuItem.click();
    await browser.switchToParentFrame();
  }
}

module.exports = new DisplayUserProfileDropDownPage();
