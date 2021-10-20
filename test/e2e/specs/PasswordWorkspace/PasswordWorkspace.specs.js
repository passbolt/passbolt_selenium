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

const {adminPrivateKey} = require('../../page/Authentication/ImportGpgKey/ImportGpgKey.data');
const SeleniumPage = require('../../page/Selenium/Selenium.page');
const RecoverAuthenticationPage = require('../../page/AuthenticationRecover/RecoverAUthentication/RecoverAuthentication.page');
const CreateUserDialogPage = require('../../page/User/CreateUser/CreateUserDialog.page');
const ShareDialogPage = require('../../page/Share/ShareDialog.page');
const DisplayMainMenuPage = require('../../page/Common/Menu/DisplayMainMenu.page');
const DisplayResourcesWorkspacePage = require('../../page/Resource/DisplayResourcesWorkspace/DisplayResourcesWorkspace.page');
const DisplayResourcesListPage = require('../../page/Resource/DisplayResourcesList/DisplayResourcesList.page');
const DisplayResourceDetailsPage = require('../../page/ResourceDetails/DisplayResourceDetails/DisplayResourceDetails.page');
const FilterResourcesByTextPage = require('../../page/Resource/FilterResourcesByText/FilterResourcesByText.page');
const DisplayUserWorkspacePage = require('../../page/User/DisplayUserWorkspace/DisplayUserWorkspace.page');
const SetupAuthenticationPage = require('../../page/AuthenticationSetup/SetupAuthentication/SetupAuthentication.page');
const CreateResourcePage = require('../../page/Resource/CreateResource/CreateResource.page');
const EditResourcePage = require('../../page/Resource/EditResource/EditResource.page');
const DeleteResourcePage = require('../../page/Resource/DeleteResource/DeleteResource.page');

describe('password workspace', () => {
  // WARNING : execution order is very important

  after(() => {
    // runs once after the last test in this block
    return SeleniumPage.resetInstanceDefault()
  });

  it('As LU I should recover admin account', async () => {
    await RecoverAuthenticationPage.recover('admin@passbolt.com', adminPrivateKey);
    await DisplayMainMenuPage.switchAppIframe();
  });

  it('As AD I should create a new user', async () => {
    await DisplayMainMenuPage.goToUserWorkspace();
    await DisplayUserWorkspacePage.openCreateUser();
    await CreateUserDialogPage.createUser('firstname', 'lastname', 'test@passbolt.com');
    await DisplayMainMenuPage.signOut();
  });

  it('As U I should setup a new account', async () => {
    await SetupAuthenticationPage.setup('test@passbolt.com');
    await DisplayMainMenuPage.switchAppIframe();
  });

  it('As LU I should create a new password', async () => {
    await DisplayResourcesWorkspacePage.openCreatePassword();
    await CreateResourcePage.createPassword('name', 'uri', 'test@passbolt.com', 'secret', 'description');
  });

  it('As LU I should copy the secret of my password', async () => {
    await DisplayResourcesListPage.copySecretResource('test@passbolt.com');
    await FilterResourcesByTextPage.pasteClipBoardToVerify('secret');
  });

  it('As LU I should share my password created', async () => {
    await DisplayResourceDetailsPage.openShareResource();
    await ShareDialogPage.shareResource('admin@passbolt.com', 'test@passbolt.com');
  });

  it('As LU I should edit my password', async () => {
    await DisplayResourcesWorkspacePage.openEditPassword('test@passbolt.com');
    await EditResourcePage.editPassword('Updated', 'Updated', 'test@passbolt.com', 'Updated', 'Updated');
  });

  it('As LU I should delete my password', async () => {
    await DisplayResourcesWorkspacePage.openDeletePassword();
    await DeleteResourcePage.deletePassword();
    await DisplayMainMenuPage.signOut();
  });
});
