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
 * @since         v5.8.0
 */

class ThemePage {
  /**
   * return  button to create user
   */
  get ProfileOptionsList() {
    return $(".navigation-profile ul");
  }

  get ThemeButton() {
    return $(".navigation-profile ul li:nth-child(5)");
  }

  get DefaultTheme() {
    return $("button=Default");
  }

  get MidgarTheme() {
    return $("button=Midgar");
  }
}

module.exports = new ThemePage();
