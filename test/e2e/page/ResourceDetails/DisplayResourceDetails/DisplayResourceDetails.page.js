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

  get activityTab() {
    return $('//button[text()="Activity"]');
  }

   get activitySelector() {
    return '.activity.accordion.sidebar-section .accordion-content .ready li';
  }

  /*
   * Returns all matching elements as an array
   */
  get activityList() {
    return $$(this.activitySelector);
  }

  /*
   * Returns first matching element
   */
  get firstActivity() {
    return $(this.activitySelector);
  }


  get moreButton() {
    return $('.accordion-content .actions .link.no-border.action-logs-load-more');
  }
  

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to open share section
   */
  async openShareSection() {
    await this.sidebarResource.waitForExist();
    await this.shareSection.click();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to open comments section
   */
  async openCommentsSection() {
    await this.sidebarResource.waitForExist();
    await this.commentSection.click();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to open comments section
   */
  async enterComment(comment) {
    await this.commentTextarea.setValue(comment);
    await this.saveCommentButton.click();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to open activitiy section
   */
  async openAcitivity() {
    await this.sidebarResource.waitForExist();
    await this.activityTab.waitForClickable({ timeout: 1000 });
    await this.activityTab.waitForEnabled({ timeout: 1000 });
    await this.activityTab.click();
    await browser.pause(1000);
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to load next page fo activity
   */
  async openNextPageActivity() {
    this.moreButton.waitForClickable();
    await this.moreButton.click();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. expect activity details to have listed
   */
  async checkActivityList(receivedActivities) {
    const expectedActivities = await this.activityList;

    for (let i=0; i < expectedActivities.length; i++) {
      const text = await expectedActivities[i].getText();
      expect(text).toMatch(receivedActivities[i]);
    }
    expect(receivedActivities.length).toEqual(expectedActivities.length);
  }
}

module.exports = new DisplayResourceDetailsPage();
