const {adaPrivateKey, adminPrivateKey} = require('../../page/Authentication/ImportGpgKey/ImportGpgKey.data');
const SeleniumPage = require('../../page/Selenium/Selenium.page');
const RecoverAuthenticationPage = require('../../page/AuthenticationRecover/RecoverAUthentication/RecoverAuthentication.page');
const ShareDialogPage = require('../../page/Share/ShareDialog.page');
const DisplayMainMenuPage = require('../../page/Common/Menu/DisplayMainMenu.page');
const DisplayResourcesWorkspacePage = require('../../page/Resource/DisplayResourcesWorkspace/DisplayResourcesWorkspace.page');
const DisplayResourcesListPage = require('../../page/Resource/DisplayResourcesList/DisplayResourcesList.page');
const DisplayResourceDetailsPage = require('../../page/ResourceDetails/DisplayResourceDetails/DisplayResourceDetails.page');
const DisplayResourceFolderDetailsPage = require('../../page/ResourceFolderDetails/DisplayResourceFolderDetails/DisplayResourceFolderDetails.page');
const FilterResourcesByFoldersPage = require('../../page/Resource/FilterResourcesByFolders/FilterResourcesByFolders.page');
const FilterResourcesByTextPage = require('../../page/Resource/FilterResourcesByText/FilterResourcesByText.page');
const CreateResourcePage = require('../../page/Resource/CreateResource/CreateResource.page');
const CreateResourceFolderPage = require('../../page/ResourceFolder/CreateResourceFolder/CreateResourceFolder.page');
const RenameResourceFolderPage = require('../../page/ResourceFolder/RenameResourceFolder/RenameResourceFolder.page');
const DeleteResourceFolderPage = require('../../page/ResourceFolder/DeleteResourceFolder/DeleteResourceFolder.page');
const FilterResourcesByShortcutsPage = require('../../page/Resource/FilterResourcesByShortcuts/FilterResourcesByShortcuts.page');

describe('password workspace', () => {
  // WARNING : execution order is very important

  after(() => {
    // runs once after the last test in this block
    SeleniumPage.resetInstanceDefault()
  });

  it('As LU I should recover ada account', () => {
    RecoverAuthenticationPage.recover('ada@passbolt.com', adaPrivateKey);
    DisplayMainMenuPage.switchAppIframe();
  });

  it('As LU I should create a new folder', () => {
    DisplayResourcesWorkspacePage.openCreateFolder();
    CreateResourceFolderPage.createFolder('folderParent');
    FilterResourcesByFoldersPage.selectedFolderNamed('folderParent');
  });

  it('As LU I should create a new password', () => {
    DisplayResourcesWorkspacePage.openCreatePassword();
    CreateResourcePage.createPassword('nameA', 'uri', 'ada@passbolt.com', 'secretA', 'description');
  });

  it('As LU I should create a subfolder folder', () => {
    FilterResourcesByFoldersPage.selectedFolderNamed('folderParent');
    DisplayResourcesWorkspacePage.openCreateFolder();
    CreateResourceFolderPage.createFolder('folderChild');
  });

  it('As LU I should create a new password', () => {
    FilterResourcesByFoldersPage.expandFolderSelected();
    FilterResourcesByFoldersPage.selectedFolderNamed('folderChild');
    DisplayResourcesWorkspacePage.openCreatePassword();
    CreateResourcePage.createPassword('nameB', 'uri', 'ada@passbolt.com', 'secretB', 'description');
  });

  it('As LU I should share a password', () => {
    DisplayResourceDetailsPage.openShareResource();
    ShareDialogPage.shareResource('admin@passbolt.com', 'ada@passbolt.com');
  });

  it('As LU I should share a folder', () => {
    FilterResourcesByFoldersPage.selectedFolderNamed('folderParent');
    DisplayResourceFolderDetailsPage.openShareResource();
    ShareDialogPage.shareResource('Accounting', 'ada@passbolt.com');
  });

  it('As LU I should see my passwords share with admin user and accounting group', () => {
    DisplayResourcesListPage.selectedResourceNamed('nameA');
    DisplayResourceDetailsPage.openShareSection();
    DisplayResourceDetailsPage.getShareWithExist('Accounting');
    FilterResourcesByFoldersPage.selectedFolderNamed('folderChild');
    DisplayResourcesListPage.selectedResourceNamed('nameB');
    DisplayResourceDetailsPage.openShareSection();
    DisplayResourceDetailsPage.getShareWithExist('Admin User (admin@passbolt.com)');
    DisplayResourceDetailsPage.getShareWithExist('Accounting');
  });

  it('As LU I should recover admin account', () => {
    RecoverAuthenticationPage.recover('admin@passbolt.com', adminPrivateKey);
    DisplayMainMenuPage.switchAppIframe();
  });

  it('As LU I should filter my resource by shared with me', () => {
    FilterResourcesByShortcutsPage.filterBySharedWithMe();
    DisplayResourcesListPage.selectedResourceNamed('nameB');
  });

  it('As LU I should copy the secret of my password', () => {
    DisplayResourcesListPage.copySecretResource('admin@passbolt.com');
    FilterResourcesByTextPage.pasteClipBoardToVerify('secretB');
  });

  it('As LU I should rename a folder', () => {
    FilterResourcesByFoldersPage.openRenameResourceFolder();
    RenameResourceFolderPage.renameFolder('rename');
  });

  it('As LU I should delete a folder', () => {
    FilterResourcesByFoldersPage.openDeleteResourceFolder();
    DeleteResourceFolderPage.deleteFolder();
  });
});
