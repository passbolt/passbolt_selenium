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

const DisplayMainMenuPage = require('../../page/Common/Menu/DisplayWorkspaceSwitcher.page');
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
const {templates} = require('../../../../lib/emailTemplates');
const DisplayUserProfileDropDownPage = require("../../page/Common/Menu/DisplayUserProfileDropDown.page");

describe('Groups', () => {
  // WARNING : execution order is very important

  const groupName = "A selenium group";
  const renamedGroupName = "#Selenium group";
  const adminUser = "admin@passbolt.com";

  after(async() => {
    // runs once after the last test in this block
    await SeleniumPage.switchToTopLevelFrame();
    await SeleniumPage.resetInstanceDefault();
  });

  it('As LU I should recover admin account', async () => {
    await RecoverAuthenticationPage.recover(adminUser, adminPrivateKey);
    await DisplayMainMenuPage.switchAppIframe();
  });

  it('As AD, I can create a group', async () => {
    await DisplayMainMenuPage.goToManageUsersAndGroupsWorkspace();
    await DisplayUserWorkspacePage.openCreateGroup();
    await CreateGroupPage.createGroup(groupName,"ada@passbolt.com", adminUser)
    await DisplayNotificationPage.closeAllNotifications();
    await SeleniumPage.checkSubjectContent("ada@passbolt.com", "Admin added you to the group A selenium group", templates.group.LU.groupUserAdded);
    await SeleniumPage.clickOnRedirection();
    await DisplayMainMenuPage.switchAppIframe();
    await DisplayMainMenuPage.goToManageUsersAndGroupsWorkspace();
    await DisplayNotificationPage.closeAllNotifications();
  });

  it('As AD, I can rename a group', async () => {
    await DisplayGroupListPage.editGroup(groupName);
    await EditGroupPage.renameGroup(renamedGroupName);
    await EditGroupPage.submitGroupUpdateWithoutPasswordCheck();
    await DisplayNotificationPage.closeAllNotifications();
  });

  it('As AD, I can add a user to a group as group manager', async () => {
    await DisplayGroupListPage.editGroup(renamedGroupName);
    await EditGroupPage.addMember("jean@passbolt.com");
    await ShareDialogPage.setRole("Group manager")
    await EditGroupPage.submitGroupUpdateWithoutPasswordCheck(adminUser);
    await DisplayNotificationPage.closeAllNotifications();
  });

  it('As AD, I can remove a user from a group', async () => {
    await DisplayGroupListPage.editGroup(renamedGroupName);
    await EditGroupPage.removeMember("ada@passbolt.com");
    await EditGroupPage.submitGroupUpdateWithoutPasswordCheck();
    //notify user
    await SeleniumPage.checkSubjectContent("ada@passbolt.com", "Admin removed you from the group #Selenium group", templates.group.LU.groupUserDeleted);
    //notify the group manager(s) 
    await SeleniumPage.checkSubjectContent("jean@passbolt.com", "Admin updated the group #Selenium group", templates.group.GM.groupUserUpdated);
    await SeleniumPage.clickOnRedirection();
    await DisplayMainMenuPage.switchAppIframe();
    await DisplayMainMenuPage.goToManageUsersAndGroupsWorkspace();
  });

  it('As AD, I can remove the group manager privilege from a group member', async () => {
    await DisplayGroupListPage.editGroup(renamedGroupName);
    await EditGroupPage.changeRole(1, "Member")
    await EditGroupPage.submitGroupUpdateWithoutPasswordCheck(adminUser);
    await SeleniumPage.checkSubjectContent("jean@passbolt.com", "Admin updated your membership in the group #Selenium group", templates.group.LU.groupUserUpdated);
    await SeleniumPage.clickOnRedirection();
    await DisplayMainMenuPage.switchAppIframe();
    await DisplayMainMenuPage.goToManageUsersAndGroupsWorkspace();
    await DisplayNotificationPage.closeAllNotifications();
  });

  it('As AD, I can delete a group', async () => {
    await DisplayGroupListPage.deleteGroup(renamedGroupName);
    await DeleteGroupPage.validationDeletion();
    await SeleniumPage.checkSubjectContent("jean@passbolt.com", "Admin deleted a group", templates.group.LU.deleted);
    await SeleniumPage.clickOnRedirection();
    await DisplayMainMenuPage.switchAppIframe();
    await DisplayUserProfileDropDownPage.signOut();
  });
});
