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
 * @since         v5.4.0
 */

const {adaPrivateKey} = require('../../page/Authentication/ImportGpgKey/ImportGpgKey.data');
const SeleniumPage = require('../../page/Selenium/Selenium.page');
const RecoverAuthenticationPage = require('../../page/AuthenticationRecover/RecoverAUthentication/RecoverAuthentication.page');
const ShareDialogPage = require('../../page/Share/ShareDialog.page');
const DisplayMainMenuPage = require('../../page/Common/Menu/DisplayWorkspaceSwitcher.page');
const DisplayResourcesWorkspacePage = require('../../page/Resource/DisplayResourcesWorkspace/DisplayResourcesWorkspace.page');
const DisplayResourcesListPage = require('../../page/Resource/DisplayResourcesList/DisplayResourcesList.page');
const DisplayResourceDetailsPage = require('../../page/ResourceDetails/DisplayResourceDetails/DisplayResourceDetails.page');
const FilterResourcesByTextPage = require('../../page/Resource/FilterResourcesByText/FilterResourcesByText.page');
const CreateResourcePage = require('../../page/Resource/CreateResource/CreateResource.page');
const EditResourcePage = require('../../page/Resource/EditResource/EditResource.page');
const {templates} = require('../../../../lib/emailTemplates');
const DisplayNotificationPage = require('../../page/Common/Notification/DisplayNotification.page');
const DisplayResourceActionBarPage = require("../../page/Resource/DisplayResourceActionBar/DisplayResourceActionBar.page");

describe('Activity', () => {
  // WARNING : execution order is very important
  let ressourceName = null;

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

    it('As LU I should see create, share and secret read activity details in resource activity section', async() => {
        /**
         * 1. Resource
         *    a. created
         *    b. Shared
         *    c. Read
         * 2. Check activities list
         */

        // create a resource
        await DisplayResourcesWorkspacePage.openCreatePassword();
        await CreateResourcePage.createPassword('name', 'uri', 'admin@passbolt.com', 'RSS5j8AQrmZK3mAQqx', 'description');
        await SeleniumPage.checkSubjectContent("ada@passbolt.com", "You have saved a new password", templates.resource.LU.created)
        await SeleniumPage.clickOnRedirection();
        await DisplayMainMenuPage.switchAppIframe();

        // share secret
        await DisplayResourceActionBarPage.openShareResourceDialog();
        await ShareDialogPage.shareResource('admin@passbolt.com', 'ada@passbolt.com');
        await SeleniumPage.checkSubjectContent("admin@passbolt.com", "Ada shared a password with you", templates.resource.LU.shared)
        await SeleniumPage.clickOnRedirection();
        await DisplayMainMenuPage.switchAppIframe();
        await DisplayNotificationPage.closeAllNotifications();

        // Copy Secret
        await DisplayResourcesListPage.copySecretResource('ada@passbolt.com');
        await DisplayNotificationPage.closeAllNotifications();
        await FilterResourcesByTextPage.pasteClipBoardToVerify('secret');
        await DisplayMainMenuPage.switchAppIframe(); 
        await DisplayNotificationPage.closeAllNotifications();

        const receivedActivities = [
            /^Ada Lovelace accessed secret of item name\n(1 second|\d+ seconds) ago$/,
            /^Ada Lovelace updated secret of item name\n(1 second|\d+ seconds) ago$/,
            /^Ada Lovelace changed permissions of item name with\nAdmin User can read\nnew\n(1 second|\d+ seconds) ago$/,
            /^Admin User can read\nnew$/,
            /^Ada Lovelace accessed secret of item name\n(1 second|\d+ seconds) ago$/,
            /^Ada Lovelace created item name\n(1 second|\d+ seconds) ago$/
        ]

        await DisplayResourceDetailsPage.openAcitivity();
        await DisplayResourceDetailsPage.checkActivityList(receivedActivities);
    });

    it('As LU I should access all details on clicking see more button', async() => {
        /**
         * Edit resource,
         * click on "More" and check the updated activities list
         */
        await DisplayResourceActionBarPage.openEditResourceDialog('ada@passbolt.com');
        ressourceName = await EditResourcePage.editPassword('Updated', 'Updated', 'ada@passbolt.com', 'Updated', 'Updated');
        await SeleniumPage.checkSubjectContent("admin@passbolt.com", `Ada edited the resource ${ressourceName}`, templates.resource.LU.updated);
        await SeleniumPage.checkSubjectContent("ada@passbolt.com", `You edited the resource ${ressourceName}`, templates.resource.LU.updated);
        await SeleniumPage.clickOnRedirection();
        await DisplayMainMenuPage.switchAppIframe();

        await DisplayResourceDetailsPage.openAcitivity();
        await DisplayResourceDetailsPage.openNextPageActivity();

        const receivedActivities = [
            /^Ada Lovelace updated item Updated\n(1 second|\d+ seconds) ago$/,
            /^Ada Lovelace updated secret of item Updated\n(1 second|\d+ seconds) ago$/,
            /^Ada Lovelace accessed secret of item Updated\n(1 second|\d+ seconds) ago$/,
            /^Ada Lovelace accessed secret of item Updated\n(1 second|\d+ seconds) ago$/,
            /^Ada Lovelace updated secret of item Updated\n(1 second|\d+ seconds) ago$/,
            /^Ada Lovelace changed permissions of item Updated with\nAdmin User can read\nnew\n(1 second|\d+ seconds) ago$/,
            /^Admin User can read\nnew$/,
            /^Ada Lovelace accessed secret of item Updated\n(1 second|\d+ seconds) ago$/,
        ]
        await DisplayResourceDetailsPage.checkActivityList(receivedActivities);
    });
});