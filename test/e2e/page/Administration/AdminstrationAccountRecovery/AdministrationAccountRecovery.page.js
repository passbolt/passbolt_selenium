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
 * @since         v3.8.0
 */

const AdministrationActionsPage = require("../AdministrationActions/AdministrationActions.page");
const AdministrationOrganizationRecoveryKeyPage = require("./AdministrationOrganizationRecoveryKey.page");
const PassphraseEntryDialogPage = require("../../AuthenticationPassphrase/InputPassphrase/InputPassphrase.page");

/**
 * sub page containing specific selectors and methods for a specific page
 */
class DisplayAdministrationAccountRecoveryPage {
  /**
   * return the account recovery page
   */
  get administrationRecoveryPage() {
    return ".recover-account-settings";
  }

  /**
   * return the account recovery mandatory policy
   */
  get mandatoryPolicy() {
    return $("#accountRecoveryPolicyMandatory");
  }

  /**
   * return the account recovery disbale policy
   */
  get disablePolicy() {
    return $("#accountRecoveryPolicyDisable");
  }

  /**
   * return the recovery key details
   */
  get recoveryKeyDetails() {
    return $(".recovery-key-details");
  }

  /**
   * return the recovery key table
   */
  get recoveryKeyTable() {
    return this.recoveryKeyDetails.$(".table-info");
  }


  /**
   * return the generate key button
   */
  get organizationRecoveryKeyButton() {
    return this.recoveryKeyTable.$("button.primary.medium");
  }
  /**
   * return the save settings button
   */
  get dialogSaveConfirmation() {
    return $(".save-recovery-account-settings-dialog");
  }

  /**
   * return the save button from dialog confirmation
   */
  get dialogSubmitButton() {
    return this.dialogSaveConfirmation
      .$(".submit-wrapper")
      .$("button.primary");
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. select mandatory policy on the screen
   */
  async clickOnMandatoryPolicy() {
    await this.mandatoryPolicy.waitForClickable();
    await this.mandatoryPolicy.click();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. select disable policy on the screen
   */
  async clickDisablePolicy() {
    await this.disablePolicy.waitForClickable();
    await this.disablePolicy.click();
  }

  async clickOnDialogSubmitButton() {
    await this.dialogSubmitButton.waitForClickable()
    await this.dialogSubmitButton.click()
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. click on button to add a recovery key
   */
  async clickOnRecoveryKeyAction() {
    await this.organizationRecoveryKeyButton.waitForClickable();
    await this.organizationRecoveryKeyButton.click();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. fill form the form to generate an account key
   */
  async generateAccountRecoveryKey() {
    await AdministrationOrganizationRecoveryKeyPage.goToGenerateTab();
    await AdministrationOrganizationRecoveryKeyPage.fillGenerateKeyForm();
    await AdministrationOrganizationRecoveryKeyPage.closeConfirmation();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. import and save pgp public key
   */
  async importAccountRecoveryKey(key) {
    await AdministrationOrganizationRecoveryKeyPage.fillInputOrganizationPgpKey(
      key
    );
    await AdministrationOrganizationRecoveryKeyPage.clickOnApplyButton();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. import and save pgp public key adn save it
   */
  async importAccountRecoveryPublicKeyAndSave(key) {
    await this.importAccountRecoveryKey(key);
    await AdministrationActionsPage.clickOnSaveSettings();
    await this.clickOnDialogSubmitButton();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. import and save pgp public key adn save it
   */
  async importAccountRecoveryPrivateKeyAndSave(privateKey, passphrase) {
    await AdministrationOrganizationRecoveryKeyPage.fillInputOrganizationPgpKey(
      privateKey
    );
    await AdministrationOrganizationRecoveryKeyPage.fillInputPassphrase(
      passphrase
    );
    await AdministrationOrganizationRecoveryKeyPage.submitImportButton.click();
    await PassphraseEntryDialogPage.entryPassphrase("admin@passbolt.com");
  }
}

module.exports = new DisplayAdministrationAccountRecoveryPage();
