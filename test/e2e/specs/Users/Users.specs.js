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

const {adminPrivateKey} = require('../../page/Authentication/ImportGpgKey/ImportGpgKey.data');
const SeleniumPage = require('../../page/Selenium/Selenium.page');
const RecoverAuthenticationPage = require('../../page/AuthenticationRecover/RecoverAUthentication/RecoverAuthentication.page');
const CreateUserDialogPage = require('../../page/User/CreateUser/CreateUserDialog.page');
const DisplayMainMenuPage = require('../../page/Common/Menu/DisplayWorkspaceSwitcher.page');
const DisplayUserWorkspacePage = require('../../page/User/DisplayUserWorkspace/DisplayUserWorkspace.page');
const SetupAuthenticationPage = require('../../page/AuthenticationSetup/SetupAuthentication/SetupAuthentication.page');
const {templates} = require('../../../../lib/emailTemplates');
const DisplayNotificationPage = require('../../page/Common/Notification/DisplayNotification.page');
const DisplayUserProfileDropDownPage = require("../../page/Common/Menu/DisplayUserProfileDropDown.page");

describe('Users', () => {
  // WARNING : execution order is very important
  
  after(async() => {
    // runs once after the last test in this block
    await SeleniumPage.switchToTopLevelFrame();
    return SeleniumPage.resetInstanceDefault()
  });

  it('As LU I should recover admin account', async() => {
    await RecoverAuthenticationPage.recover('admin@passbolt.com', adminPrivateKey);
    await DisplayMainMenuPage.switchAppIframe();
    await DisplayNotificationPage.closeAllNotifications();
  });

  it('As AD I should create a new user', async() => {
    await DisplayMainMenuPage.goToManageUsersAndGroupsWorkspace();
    await DisplayUserWorkspacePage.openCreateUser();
    await CreateUserDialogPage.createUser('firstname', 'lastname', 'test@passbolt.com');
    await DisplayUserProfileDropDownPage.signOut();
    await SeleniumPage.checkSubjectContent("test@passbolt.com", "Admin just created an account for you on passbolt!", templates.register.AN.registered)
    await SeleniumPage.clickOnRedirection();
  });


  it('As guest user I can setup my account', async() => {
    await SetupAuthenticationPage.setup('test@passbolt.com');
    await DisplayMainMenuPage.switchAppIframe();
  });
});


