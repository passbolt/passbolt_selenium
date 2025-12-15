/*
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

const SeleniumPage = require('../../page/Selenium/Selenium.page');
const RecoverAuthenticationPage = require('../../page/AuthenticationRecover/RecoverAUthentication/RecoverAuthentication.page');
const {adaPrivateKey} = require('../../page/Authentication/ImportGpgKey/ImportGpgKey.data');
const DisplayUserProfileDropDownPage = require('../../page/Common/Menu/DisplayUserProfileDropDown.page');
const ThemeMenu = require('../../page/Theme/Theme.page');

describe('Theme Page', () => {
  after(async() => {
    // runs once after the last test in this block
    await SeleniumPage.switchToTopLevelFrame();
    return SeleniumPage.resetInstanceDefault();
  });

  before(async() => {
    await RecoverAuthenticationPage.recover('ada@passbolt.com', adaPrivateKey);
  });


  it('As LU, When I access Theme Menu Then I should see Default Theme Selected', async() => {
    await DisplayUserProfileDropDownPage.switchAppIframe();
    await DisplayUserProfileDropDownPage.userProfileDropDownButton.click();
    await DisplayUserProfileDropDownPage.manageAccountButton.click();
    await ThemeMenu.ThemeButton.click();

    await expect($('.panel.middle')).toMatchSnapshot();
  });

  it("As LU, When I click Selected Theme, Then I see no Success Notification", async() => {
    await ThemeMenu.DefaultTheme.click();

    await expect.soft($('.notification .success')).not.toBeDisplayed();
  });

  it("As LU, I click Another Theme, Then I see Success Notification", async() => {
    await ThemeMenu.MidgarTheme.click();

    await expect.soft($('.notification .success')).toBeDisplayed();
    await expect.soft($('.notification .success')).toHaveText('Success: The theme has been updated successfully');
  });
});
