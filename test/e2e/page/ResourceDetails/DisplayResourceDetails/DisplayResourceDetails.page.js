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

/**
 * sub page containing specific selectors and methods for a specific page
 */
class DisplayResourceDetailsPage {
  /**
   * define selectors using getter methods
   */
  get sidebarResource() {
    return $('.sidebar.resource');
  }

  get shareSection() {
    return $('.detailed-permission.accordion.sidebar-section');
  }

  get commentSection() {
    return $('.detailed-comments.accordion.sidebar-section');
  }

  get commentTextarea() {
    return $('.comment textarea');
  }

  get saveCommentButton() {
    return $('.comment .actions button');
  }

  getShareWithExist(name) {
    return this.shareSection.$('.accordion-content').$(`div=${name}`).waitForExist();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to open share section
   */
  async openShareSection() {
    await this.sidebarResource.waitForExist();
    await this.shareSection.waitForClickable();
    await this.shareSection.click();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to open comments section
   */
  async openCommentsSection() {
    await this.sidebarResource.waitForExist();
    await this.commentSection.waitForClickable();
    await this.commentSection.click();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to open comments section
   */
  async enterComment(comment) {
    await this.commentTextarea.waitForExist();
    await this.commentTextarea.setValue(comment);
    await this.saveCommentButton.waitForClickable();
    await this.saveCommentButton.click();
  }
}

module.exports = new DisplayResourceDetailsPage();
