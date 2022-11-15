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

const {
  adminPrivateKey,
} = require("../../page/Authentication/ImportGpgKey/ImportGpgKey.data");
const SeleniumPage = require("../../page/Selenium/Selenium.page");
const RecoverAuthenticationPage = require("../../page/AuthenticationRecover/RecoverAUthentication/RecoverAuthentication.page");
const DisplayMainMenuPage = require("../../page/Common/Menu/DisplayMainMenu.page");
const DisplayAdministrationMenuPage = require("../../page/Administration/AdministrationMenu/AdministrationMenu.page");
const DisplayAdministrationAccountRecoveryPage = require("../../page/Administration/AdminstrationAccountRecovery/AdministrationAccountRecovery.page");
const DisplayDisplayDialogAccountRecoryPolicyPage = require("../../page/Common/Dialog/DisplayDialogAccountRecoryPolicy.page");
const DisplayAdministrationEmailNotificationPage = require("../../page/Administration/AdministrationEmailNotification/AdministrationEmailNotification.page");
const LoginPage = require("../../page/Authentication/Login/Login.page");
const DisplayUserWorkspacePage = require("../../page/User/DisplayUserWorkspace/DisplayUserWorkspace.page");
const {
  organizationPublicKeyAlternative,
  organizationPublicKey,
  organizationPrivateKey,
  organizationPassphrase,
  organizationPrivateKeyAlternative,
  organizationPassphraseAlternative,
} = require("../../page/Authentication/ImportGpgKey/ImportGpgOrganizationKey.data");
const AdministrationActionsPage = require("../../page/Administration/AdministrationActions/AdministrationActions.page");
const PassphraseEntryDialogPage = require("../../page/AuthenticationPassphrase/InputPassphrase/InputPassphrase.page");

describe("password workspace", () => {
  const admin = "admin@passbolt.com";
  const adminName = "Admin User";
  // WARNING : execution order is very important
  after(async () => {
    // runs once after the last test in this blockx
    await SeleniumPage.resetInstanceDefault();
  });

  it("As AD I should recover admin account", async () => {
    await RecoverAuthenticationPage.recover(admin, adminPrivateKey);
    await DisplayMainMenuPage.switchAppIframe();
  });

  it("As AD, I can enable account recovery", async () => {
    await DisplayMainMenuPage.goToAdminstrationWorkspace();
    await DisplayAdministrationMenuPage.goToAccountRecoverySection();
    await DisplayMainMenuPage.switchAppIframe();
    await DisplayAdministrationAccountRecoveryPage.clickOnMandatoryPolicy();
    await DisplayAdministrationAccountRecoveryPage.clickOnRecoveryKeyAction();
    await DisplayAdministrationAccountRecoveryPage.importAccountRecoveryPublicKeyAndSave(
      organizationPublicKey
    );
    await PassphraseEntryDialogPage.entryPassphrase(admin);
    // We disable admin email notifications to avoid a confusion into mails
    await DisplayAdministrationMenuPage.goToEmailNotificationSection();
    await DisplayAdministrationEmailNotificationPage.disableAdministratorNotification();
    await DisplayMainMenuPage.signOut();
  });

  it("As LU, I can manage my enrollment to account recovery", async () => {
    await LoginPage.login(admin);
    await DisplayMainMenuPage.switchAppIframe();
    await DisplayDisplayDialogAccountRecoryPolicyPage.clickOnContinueButton();
    await DisplayDisplayDialogAccountRecoryPolicyPage.clickOnSaveButton(admin);
    await DisplayMainMenuPage.signOut();
  });

  it("As an user I can initiate an account recovery", async () => {
    await requestAccountRecovery(admin);
  });

  it("As AD, I can approve a user account recovery request", async () => {
    await LoginPage.goToLogin();
    await LoginPage.login(admin);
    await DisplayMainMenuPage.switchAppIframe();
    await DisplayMainMenuPage.goToUserWorkspace();
    await DisplayUserWorkspacePage.searchUser(adminName);
    await DisplayUserWorkspacePage.clickOnUserRaw(adminName);
    await DisplayUserWorkspacePage.reviewAccountRecoveryRequest(
      adminName,
      true
    );
    await DisplayMainMenuPage.signOut();
  });

  it("As an user I can initiate an account recovery a second time", async () => {
    await requestAccountRecovery(admin);
  });

  it("As AD, I can reject a user account recovery request", async () => {
    await LoginPage.goToLogin();
    await LoginPage.login(admin);
    await DisplayMainMenuPage.switchAppIframe();
    await DisplayMainMenuPage.goToUserWorkspace();
    await DisplayUserWorkspacePage.searchUser(adminName);
    await DisplayUserWorkspacePage.clickOnUserRaw(adminName);
    await DisplayUserWorkspacePage.reviewAccountRecoveryRequest(
      adminName,
      false
    );
  });

  it("As AD, I can rotate the organization key without changing the organization policy type", async () => {
    await DisplayMainMenuPage.goToAdminstrationWorkspace();
    await DisplayAdministrationMenuPage.goToAccountRecoverySection();
    await DisplayMainMenuPage.switchAppIframe();
    await DisplayAdministrationAccountRecoveryPage.clickOnRecoveryKeyAction();
    await DisplayAdministrationAccountRecoveryPage.importAccountRecoveryPublicKeyAndSave(
      organizationPublicKeyAlternative
    );
    await DisplayAdministrationAccountRecoveryPage.importAccountRecoveryPrivateKeyAndSave(
      organizationPrivateKey,
      organizationPassphrase
    );
  });

  it("As AD, I can disable account recovery", async () => {
    await DisplayAdministrationAccountRecoveryPage.clickDisablePolicy();
    await AdministrationActionsPage.clickOnSaveSettings();
    await DisplayAdministrationAccountRecoveryPage.clickOnDialogSubmitButton();
    await DisplayAdministrationAccountRecoveryPage.importAccountRecoveryPrivateKeyAndSave(
      organizationPrivateKeyAlternative,
      organizationPassphraseAlternative
    );
  });
});

const requestAccountRecovery = async (admin) => {
  await LoginPage.clickOnLostPrivateKeyLink();
  await LoginPage.clickOnRecoverAccountButton();
  // Show last email and redirect for account recovery
  await SeleniumPage.showLastEmailAndRedirect(admin);
  await RecoverAuthenticationPage.recoverByAccountRecoveryRequest(admin);
};
