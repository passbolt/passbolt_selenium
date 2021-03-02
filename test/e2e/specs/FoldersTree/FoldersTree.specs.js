const {adaPrivateKey, adminPrivateKey} = require('../../page/Password/PasswordWorkspace/PasswordWorkspace.data');
const SeleniumPage = require('../../page/Selenium/Selenium.page');
const RecoverAuthenticationPage = require('../../page/AuthenticationRecover/RecoverAUthentication/RecoverAuthentication.page');
const ShareDialogPage = require('../../page/Share/shareDialog.page');
const DisplayMainMenuPage = require('../../page/navigation/DisplayMainMenu.page');
const PasswordWorkspacePage = require('../../page/Password/PasswordWorkspace/PasswordWorkspace.page');
const GridPage = require('../../page/Password/Grid/Grid.page');
const PasswordSidebarPage = require('../../page/Password/PasswordSidebar/PasswordSidebar.page');
const FolderSidebarPage = require('../../page/Password/FolderSidebar/FolderSidebar.page');
const FoldersTreePage = require('../../page/Password/FoldersTree/FoldersTree.page');
const PasswordSearchBarPage = require('../../page/Password/PasswordSearchBar/PasswordSearchBar.page');
const PasswordCreateDialogPage = require('../../page/Password/PasswordCreateDialog/PasswordCreateDialog.page');
const FolderCreateDialogPage = require('../../page/Folder/FolderCreateDialog/FolderCreateDialog.page');
const FolderRenameDialogPage = require('../../page/Folder/FolderRenameDialog/FolderRenameDialog.page');
const FolderDeleteDialogPage = require('../../page/Folder/FolderDeleteDialog/FolderDeleteDialog.page');
const FilterResourcesByShortcutsPage = require('../../page/Password/FilterResourcesByShortcuts/FilterResourcesByShortcuts.page');

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
    PasswordWorkspacePage.openCreateFolder();
    FolderCreateDialogPage.createFolder('folderParent');
    FoldersTreePage.selectedFolderNamed('folderParent');
  });

  it('As LU I should create a new password', () => {
    PasswordWorkspacePage.openCreatePassword();
    PasswordCreateDialogPage.createPassword('nameA', 'uri', 'ada@passbolt.com', 'secretA', 'description');
  });

  it('As LU I should create a subfolder folder', () => {
    FoldersTreePage.selectedFolderNamed('folderParent');
    PasswordWorkspacePage.openCreateFolder();
    FolderCreateDialogPage.createFolder('folderChild');
  });

  it('As LU I should create a new password', () => {
    FoldersTreePage.expandFolderSelected();
    FoldersTreePage.selectedFolderNamed('folderChild');
    PasswordWorkspacePage.openCreatePassword();
    PasswordCreateDialogPage.createPassword('nameB', 'uri', 'ada@passbolt.com', 'secretB', 'description');
  });

  it('As LU I should share a password', () => {
    PasswordSidebarPage.openShareResource();
    ShareDialogPage.shareResource('admin@passbolt.com', 'ada@passbolt.com');
  });

  it('As LU I should share a folder', () => {
    FoldersTreePage.selectedFolderNamed('folderParent');
    FolderSidebarPage.openShareResource();
    ShareDialogPage.shareResource('Accounting', 'ada@passbolt.com');
  });

  it('As LU I should see my passwords share with admin user and accounting group', () => {
    GridPage.selectedResourceNamed('nameA');
    PasswordSidebarPage.openShareSection();
    PasswordSidebarPage.getShareWithExist('Accounting');
    FoldersTreePage.selectedFolderNamed('folderChild');
    GridPage.selectedResourceNamed('nameB');
    PasswordSidebarPage.openShareSection();
    PasswordSidebarPage.getShareWithExist('Admin User (admin@passbolt.com)');
    PasswordSidebarPage.getShareWithExist('Accounting');
  });

  it('As LU I should recover admin account', () => {
    RecoverAuthenticationPage.recover('admin@passbolt.com', adminPrivateKey);
    DisplayMainMenuPage.switchAppIframe();
  });

  it('As LU I should filter my resource by shared with me', () => {
    FilterResourcesByShortcutsPage.filterBySharedWithMe();
    GridPage.selectedResourceNamed('nameB');
  });

  it('As LU I should copy the secret of my password', () => {
    GridPage.copySecretResource('admin@passbolt.com');
    PasswordSearchBarPage.pasteClipBoardToVerify('secretB');
  });

  it('As LU I should rename a folder', () => {
    FoldersTreePage.openFolderRenameDialog();
    FolderRenameDialogPage.renameFolder('rename');
  });

  it('As LU I should delete a folder', () => {
    FoldersTreePage.openFolderDeleteDialog();
    FolderDeleteDialogPage.deleteFolder();
  });
});
