/**
 * sub page containing specific selectors and methods for a specific page
 */
class PasswordSidebarPage {
  /**
   * define selectors using getter methods
   */
  get sidebarResource() {
    return $('.sidebar.resource');
  }

  get shareSection() {
    return $('.sharedwith.accordion.sidebar-section');
  }

  get shareEditIcon() {
    return $('.sharedwith.accordion.sidebar-section .accordion-content .section-action');
  }

  get shareList() {
    return $('.sharedwith.accordion.sidebar-section .accordion-content .shared-with');
  }

  getShareWithExist(name) {
    return $('.sharedwith.accordion.sidebar-section .accordion-content .shared-with').$(`div=${name}`).waitForExist();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to open share section
   */
  openShareSection() {
    this.sidebarResource.waitForExist();
    this.shareSection.waitForClickable();
    this.shareSection.click();
  }

  /**
   * a method to encapsule automation code to interact with the page
   * e.g. to open share dialog
   */
  openShareResource() {
    this.openShareSection();
    this.shareEditIcon.waitForClickable();
    this.shareEditIcon.click();
  }
}

module.exports = new PasswordSidebarPage();
