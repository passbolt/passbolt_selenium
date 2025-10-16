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
 * @since         v3.0.0
 */

const {adaPrivateKey} = require('../../page/Authentication/ImportGpgKey/ImportGpgKey.data');
const SeleniumPage = require('../../page/Selenium/Selenium.page');
const RecoverAuthenticationPage = require('../../page/AuthenticationRecover/RecoverAUthentication/RecoverAuthentication.page');
const DisplayMainMenuPage = require('../../page/Common/Menu/DisplayWorkspaceSwitcher.page');
const DisplayResourcesListPage = require('../../page/Resource/DisplayResourcesList/DisplayResourcesList.page');
const DisplayResourceDetailsPage = require('../../page/ResourceDetails/DisplayResourceDetails/DisplayResourceDetails.page');
const DisplayNotificationPage = require('../../page/Common/Notification/DisplayNotification.page');

describe('Comments', () => {
  // WARNING : execution order is very important  
  after(async() => {
    // runs once after the last test in this block
    await SeleniumPage.switchToTopLevelFrame();
    return SeleniumPage.resetInstanceDefault()
  });

  it('As LU I should recover admin account', async() => {
    await RecoverAuthenticationPage.recover('ada@passbolt.com', adaPrivateKey);
    await DisplayMainMenuPage.switchAppIframe();
    await DisplayNotificationPage.closeAllNotifications();
  });

  it('As LU I can comment a resource', async() => {
    await DisplayResourcesListPage.selectedFirstResource();
    await DisplayResourceDetailsPage.openCommentsSection()
    await DisplayResourceDetailsPage.enterComment("Selenium test");
    await DisplayNotificationPage.closeAllNotifications();
  });
});


