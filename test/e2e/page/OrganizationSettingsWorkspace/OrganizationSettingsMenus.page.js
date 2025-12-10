/**
 * Passbolt ~ Open source password manager for teams
 * Copyright (c) Passbolt SA (https://www.passbolt.com)
 *
 * Licensed under GNU Affero General Public License version 3 of the or any later version.
 * For full copyright and license information, please see the LICENSE.txt
 *redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Passbolt SA (https://www.passbolt.com)
 * @license       https://opensource.org/licenses/AGPL-3.0 AGPL License
 * @link          https://www.passbolt.com Passbolt(tm)
 * @since         v5.8.0
 */

const workspaceSwitch = require('../../page/Common/Menu/DisplayWorkspaceSwitcher.page');

/**
 * sub page containing specific selectors and methods for Organization Settings Workspace
 */
class OrganizationSettingsMenus {
  /**
   * return container for back button
   * @type {WebdriverIO.Element}
   */
  get backButton() {
    return $("button.back");
  }

  /**
   * return container for Subscription sub-menu
   * @type {WebdriverIO.Element}
   */
  get subscriptionsMenu() {
    return $("#subscription_menu");
  }

  /**
   * return container for Resource types sub-menu
   * @type {WebdriverIO.Element}
   */
  get resourceTypesMenu() {
    return $("#content-types");
  }

  /**
   * return container for Metadata key sub-menu
   * @type {WebdriverIO.Element}
   */
  get metadataKeyMenu() {
    return $("#metadata_key_menu");
  }

  /**
   * return container for Encrypted metadata sub-menu
   * @type {WebdriverIO.Element}
   */
  get encryptedMetadataMenu() {
    return $("#encrypted_metadata_menu");
  }

  /**
   * return container for Migrate metadata sub-menu
   * @type {WebdriverIO.Element}
   */
  get migrateMetaDataMenu() {
    return $("#migrate_metadata_menu");
  }

  /**
   * return container for Allow content types sub-menu
   * @type {WebdriverIO.Element}
   */
  get allowContentTypesMenu() {
    return $("#allowed_content_type_menu");
  }

  /**
   * return container for Resource policies sub-menu
   * @type {WebdriverIO.Element}
   */
  get resourcePolicies() {
    return $("#password-configuration");
  }

  /**
   * return container for Password Expiry sub-menu
   * @type {WebdriverIO.Element}
   */
  get passwordExpiryMenu() {
    return $("#password_expiry_menu");
  }

  /**
   * return container for Password Policy sub-menu
   * @type {WebdriverIO.Element}
   */
  get passwordPolicyMenu() {
    return $("#password_policy_menu");
  }

  /**
   * return container for See history sub-menu
   * @type {WebdriverIO.Element}
   */
  get secretHistoryMenu() {
    return $("#secret_history_menu");
  }

  /**
   * return container for Authentication sub-menu
   * @type {WebdriverIO.Element}
   */
  get authenticationMenu() {
    return $("#authentication");
  }

  /**
   * return container for User Passphrase Policies sub-menu
   * @type {WebdriverIO.Element}
   */
  get userPassphrasePoliciesMenu() {
    return $("#user_passphrase_policies_menu");
  }

  /**
   * return container for Account Recovery sub-menu
   * @type {WebdriverIO.Element}
   */
  get accountRecoveryMenu() {
    return $("#account_recovery_menu");
  }

  /**
   * return container for Single Sign-On sub-menu
   * @type {WebdriverIO.Element}
   */
  get singleSignOnMenu() {
    return $("#sso_menu");
  }

  /**
   * return container for MFA Policy sub-menu
   * @type {WebdriverIO.Element}
   */
  get mfaPolicyMenu() {
    return $("#mfa_policy_menu");
  }

  /**
   * return container for Multi Factor Authentication sub-menu
   * @type {WebdriverIO.Element}
   */
  get multiFactorAuthenticationMenu() {
    return $("#mfa_menu");
  }

  /**
   * return container for User provisionning sub-menu
   * @type {WebdriverIO.Element}
   */
  get userProvisioningMenu() {
    return $("#user-provisionning");
  }

  /**
   * return container for SCIM sub-menu
   * @type {WebdriverIO.Element}
   */
  get scimMenu() {
    return $("#scim_menu");
  }

  /**
   * return container for Users Directory sub-menu
   * @type {WebdriverIO.Element}
   */
  get usersDirectoryMenu() {
    return $("#user_directory_menu");
  }

  /**
   * return container for Self Registration sub-menu
   * @type {WebdriverIO.Element}
   */
  get selfRegistrationMenu() {
    return $("#self_registration_menu");
  }

  /**
   * return container for Emails sub-menu
   * @type {WebdriverIO.Element}
   */
  get emailsMenu() {
    return $("#emails");
  }

  /**
   * return container for Email server sub-menu
   * @type {WebdriverIO.Element}
   */
  get emailServerMenu() {
    return $("#smtp_settings_menu");
  }

  /**
   * return container for Email Notifications sub-menu
   * @type {WebdriverIO.Element}
   */
  get emailNotificationMenu() {
    return $("#email_notification_menu");
  }

  /**
   * return container for Role-Based Access Control
   * @type {WebdriverIO.Element}
   */
  get roleBasedAccessContolMenu() {
    return $("#rbacs_menu");
  }

  /**
   * return container for Internationalisation
   * @type {WebdriverIO.Element}
   */
  get internationalisationMenu() {
    return $("#internationalization_menu");
  }

  /**
   * return container Passbolt API Status sub-menu
   * @type {WebdriverIO.Element}
   */
  get passboltApiStatusMenu() {
    return $("#healthcheck_menu");
  }

  /**
   * return container for Save button of Organisation Settings
   * @type {WebdriverIO.Element}
   */
  get saveButton() {
    return $("button.button.primary.form");
  }

  /**
   * method to access Encrypted metadata menu from Organisation Settings
   * @returns {Promise<void>}
   */
  async accessEncryptedMetadataMenu() {
    await workspaceSwitch.workspaceSwitcher.click();
    await workspaceSwitch.organizationSettingsMenuItem.click();
    if (await $("#metadata_getting_started_menu").isExisting()) {
      await $("#metadata_getting_started_menu").click();
      await this.saveChanges();
    }
    await this.encryptedMetadataMenu.click();
  }

  /**
   * method to save changes in Organization Settings and its menus
   * @returns {Promise<void>}
   */
  async saveChanges() {
    await this.saveButton.click();
  }

  /**
   * method to go back from Organisation Settings
   * @returns {Promise<void>}
   */
  async goBack() {
    await this.backButton.click();
  }

  /**
   * method to save changes in Organisation Settings and go back
   * @returns {Promise<void>}
   */
  async saveChangesANDGoBack() {
    await this.saveButton.click();
    await this.backButton.click();
  }
}

module.exports = new OrganizationSettingsMenus();
