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
 * @since         v3.8.3
 */

const DisplayMainMenuPage = require('../../page/Common/Menu/DisplayMainMenu.page');
const CreateGroupPage = require('../../page/Group/CreateGroup/CreateGroup.page');
const DisplayGroupListPage = require('../../page/Group/DisplayGroupList/DisplayGroupList.page');
const SeleniumPage = require('../../page/Selenium/Selenium.page');
const DisplayUserWorkspacePage = require('../../page/User/DisplayUserWorkspace/DisplayUserWorkspace.page');
const RecoverAuthenticationPage = require("../../page/AuthenticationRecover/RecoverAUthentication/RecoverAuthentication.page");
const {adminPrivateKey} = require("../../page/Authentication/ImportGpgKey/ImportGpgKey.data");
const EditGroupPage = require('../../page/Group/EditGroup/EditGroup.page');
const DeleteGroupPage = require('../../page/Group/DeleteGroup/DeleteGroup.page');
const ShareDialogPage = require('../../page/Share/ShareDialog.page');
const DisplayNotificationPage = require('../../page/Common/Notification/DisplayNotification.page');

describe('groups three', () => {
  // WARNING : execution order is very important

  const groupName = "A selenium group";
  const renamedGroupName = "#Selenium group";
  const adminUser = "admin@passbolt.com";

  after(() => {
    // runs once after the last test in this block
    return SeleniumPage.resetInstanceDefault()
  });

  it('As LU I should recover admin account', async () => {
    await RecoverAuthenticationPage.recover(adminUser, adminPrivateKey);
    await DisplayMainMenuPage.switchAppIframe();
  });

  it('As AD, I can CRUD groups - Create', async () => {
    await DisplayMainMenuPage.goToUserWorkspace();
    await DisplayUserWorkspacePage.openCreateGroup();
    await CreateGroupPage.createGroup(groupName,"ada@passbolt.com", adminUser)
  });

  it("When users are added to a group, notify them.", async () => {
    await SeleniumPage.checkSubjectContent("ada@passbolt.com", "Admin added you to the group A selenium group");
    await SeleniumPage.clickOnRedirection();
    await DisplayMainMenuPage.switchAppIframe();
    await DisplayMainMenuPage.goToUserWorkspace();
  });

  it('As AD, I can rename a group', async () => {
    await DisplayGroupListPage.editGroup(groupName);
    await EditGroupPage.renameGroup(renamedGroupName);
    await EditGroupPage.clickOnSubmitButton(adminUser);
  });

  it('As AD, I can add an user as group manager', async () => {
    await DisplayGroupListPage.editGroup(renamedGroupName);
    await EditGroupPage.addMember("jean@passbolt.com");
    await ShareDialogPage.setRole("Group manager")
    await EditGroupPage.clickOnSubmitButton(adminUser);
  });

  it('As AD, I can remove an user', async () => {
    await DisplayGroupListPage.editGroup(renamedGroupName);
    await EditGroupPage.removeMember("ada@passbolt.com");
    await EditGroupPage.clickOnSubmitButton(adminUser);
  });

  it('When users are removed from a group, notify them.', async () => {
    await SeleniumPage.checkSubjectContent("ada@passbolt.com", "Admin removed you from the group #Selenium group")
  });

  it('When members of a group change, notify the group manager(s)', async () => {
    await SeleniumPage.checkSubjectContent("jean@passbolt.com", "Admin updated the group #Selenium group")
    await SeleniumPage.clickOnRedirection();
    await DisplayMainMenuPage.switchAppIframe();
    await DisplayMainMenuPage.goToUserWorkspace();
  });

  it('As AD, I can remove the permission for group manager', async () => {
    await DisplayGroupListPage.editGroup(renamedGroupName);
    await EditGroupPage.changeRole(1, "Member")
    await EditGroupPage.clickOnSubmitButton(adminUser);
  });

  it("When user roles change in a group, notify the corresponding users. ", async () => {
    await SeleniumPage.checkSubjectContent("jean@passbolt.com", "Admin updated your membership in the group #Selenium group")
    await SeleniumPage.clickOnRedirection();
    await DisplayMainMenuPage.switchAppIframe();
    await DisplayMainMenuPage.goToUserWorkspace();
  });

  it('As AD, I can CRUD groups - Delete', async () => {
    await DisplayGroupListPage.deleteGroup(renamedGroupName);
    await DeleteGroupPage.validationDeletion();
  });

  it('When a group is deleted, notify the users who were member of it.', async () => {
    await SeleniumPage.checkSubjectContent("jean@passbolt.com", "Admin deleted a group")
    await SeleniumPage.clickOnRedirection();
    await DisplayMainMenuPage.switchAppIframe();
    await DisplayMainMenuPage.signOut();
  });
});
