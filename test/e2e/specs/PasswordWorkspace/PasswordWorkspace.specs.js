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
    SeleniumPage.resetInstanceDefault()
  });

  it('As LU I should recover admin account', () => {
    RecoverAuthenticationPage.recover('admin@passbolt.com', adminPrivateKey);
    DisplayMainMenuPage.switchAppIframe();
  });

  it('As AD I should create a new user', () => {
    DisplayMainMenuPage.goToUserWorkspace();
    DisplayUserWorkspacePage.openCreateUser();
    CreateUserDialogPage.createUser('firstname', 'lastname', 'test@passbolt.com');
    DisplayMainMenuPage.signOut();
  });

  it('As U I should setup a new account', () => {
    SetupAuthenticationPage.setup('test@passbolt.com');
    DisplayMainMenuPage.switchAppIframe();
  });

  it('As LU I should create a new password', () => {
    DisplayResourcesWorkspacePage.openCreatePassword();
    CreateResourcePage.createPassword('name', 'uri', 'test@passbolt.com', 'secret', 'description');
  });

  it('As LU I should copy the secret of my password', () => {
    DisplayResourcesListPage.copySecretResource('test@passbolt.com');
    FilterResourcesByTextPage.pasteClipBoardToVerify('secret');
  });

  it('As LU I should share my password created', () => {
    DisplayResourcesListPage.selectedResourceNamed('name');
    DisplayResourceDetailsPage.openShareResource();
    ShareDialogPage.shareResource('admin@passbolt.com', 'test@passbolt.com');
  });

  it('As LU I should edit my password', () => {
    DisplayResourcesWorkspacePage.openEditPassword('test@passbolt.com');
    EditResourcePage.editPassword('Updated', 'Updated', 'test@passbolt.com', 'Updated', 'Updated');
  });

  it('As LU I should delete my password', () => {
    DisplayResourcesWorkspacePage.openDeletePassword();
    DeleteResourcePage.deletePassword();
    DisplayMainMenuPage.signOut();
  });
});
