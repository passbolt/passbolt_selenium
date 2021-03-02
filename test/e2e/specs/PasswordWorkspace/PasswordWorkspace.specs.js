const {adminPrivateKey} = require('../../page/Password/PasswordWorkspace/PasswordWorkspace.data');
const SeleniumPage = require('../../page/Selenium/Selenium.page');
const RecoverAuthenticationPage = require('../../page/AuthenticationRecover/RecoverAUthentication/RecoverAuthentication.page');
const CreateUserDialogPage = require('../../page/User/CreateUser/CreateUserDialog.page');
const ShareDialogPage = require('../../page/Share/shareDialog.page');
const DisplayMainMenuPage = require('../../page/navigation/DisplayMainMenu.page');
const PasswordWorkspacePage = require('../../page/Password/PasswordWorkspace/PasswordWorkspace.page');
const GridPage = require('../../page/Password/Grid/Grid.page');
const PasswordSidebarPage = require('../../page/Password/PasswordSidebar/PasswordSidebar.page');
const PasswordSearchBarPage = require('../../page/Password/PasswordSearchBar/PasswordSearchBar.page');
const DisplayUserWorkspacePage = require('../../page/User/DisplayUserWorkspace/DisplayUserWorkspace.page');
const SetupAuthenticationPage = require('../../page/AuthenticationSetup/SetupAuthentication/SetupAuthentication.page');
const PasswordCreateDialogPage = require('../../page/Password/PasswordCreateDialog/PasswordCreateDialog.page');
const PasswordEditDialogPage = require('../../page/Password/PasswordEditDialog/PasswordEditDialog.page');
const PasswordDeleteDialogPage = require('../../page/Password/PasswordDeleteDialog/PasswordDeleteDialog.page');

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
    PasswordWorkspacePage.openCreatePassword();
    PasswordCreateDialogPage.createPassword('name', 'uri', 'test@passbolt.com', 'secret', 'description');
  });

  it('As LU I should copy the secret of my password', () => {
    GridPage.copySecretResource('test@passbolt.com');
    PasswordSearchBarPage.pasteClipBoardToVerify('secret');
  });

  it('As LU I should share my password created', () => {
    GridPage.selectedResourceNamed('name');
    PasswordSidebarPage.openShareResource();
    ShareDialogPage.shareResource('admin@passbolt.com', 'test@passbolt.com');
  });

  it('As LU I should edit my password', () => {
    PasswordWorkspacePage.openEditPassword('test@passbolt.com');
    PasswordEditDialogPage.editPassword('Updated', 'Updated', 'test@passbolt.com', 'Updated', 'Updated');
  });

  it('As LU I should delete my password', () => {
    PasswordWorkspacePage.openDeletePassword();
    PasswordDeleteDialogPage.deletePassword();
    DisplayMainMenuPage.signOut();
  });
});
