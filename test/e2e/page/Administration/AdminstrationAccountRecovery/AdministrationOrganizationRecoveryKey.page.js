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

/**
 * sub page containing specific selectors and methods for a specific page
 */
class AdministrationOrganizationRecoveryKeyPage {
  /**
   * return the textArea organization pgp public key
   */
  get textAreaOrganizationPgpKey() {
    return $("#organization-recover-form-key");
  }

  /**
   * return the generate key button
   */
  get generateKeyTab() {
    return $(".tabs-nav").$("=Generate");
  }

  /**
   * return the account recovery menu tab
   */
  get generateOrganizationKeyPage() {
    return $(".generate-organization-key");
  }

  /**
   * return the input name for the generation key form
   */
  get inputName() {
    return $("#generate-organization-key-form-name");
  }

  /**
   * return the input email for the generation key form
   */
  get inputEmail() {
    return $("#generate-organization-key-form-email");
  }

  /**
   * return the input passphrase for the generation key form
   */
  get inputPassphrase() {
    return $("#generate-organization-key-form-password");
  }

  /**
   * return the submit button for generation key
   */
  get generateButton() {
    return $(".submit-wrapper").$(".button.primary");
  }

  /**
   * return the confirm button for download popup
   */
  get confirmButton() {
    return $(".dialog-footer").$("button.primary");
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. return the dialog component
   */
  get dialogDownloadComponent() {
    return $(".organization-recover-key-download-dialog");
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. return the primary button to Apply organization pgq public key
   */
  get submitImportButton() {
    return $(".submit-wrapper").$(".button.primary");
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. click on apply button to save organization public pqp key
   */
  async clickOnApplyButton() {
    await this.submitImportButton.waitForClickable();
    await this.submitImportButton.click();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. fill input with organization pgp key
   */
  async fillInputOrganizationPgpKey(key) {
    await this.textAreaOrganizationPgpKey.waitForExist();
    await this.textAreaOrganizationPgpKey.setValue(key, {wait:10000});
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. click on generate tab into generate account recovery key
   */
  async goToGenerateTab() {
    await this.generateKeyTab.waitForClickable();
    await this.generateKeyTab.click();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. fill the form for generation key
   */
  async fillGenerateKeyForm() {
    const email = "admin@passbolt.com";
    await this.generateOrganizationKeyPage.waitForExist();
    await this.inputName.setValue("Selenium Test");
    await this.inputEmail.setValue(email);
    await this.fillInputPassphrase(email);
    await this.generateButton.waitForClickable();
    await this.generateButton.click();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. fill the passphase input for gpg
   */
  async fillInputPassphrase(email) {
    await this.inputPassphrase.setValue(email);
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. close the confirmation dialoge
   */
  async closeConfirmation() {
    await this.dialogDownloadComponent.waitForExist();
    await this.confirmButton.waitForClickable();
    await this.confirmButton.click();
  }
}

module.exports = new AdministrationOrganizationRecoveryKeyPage();
