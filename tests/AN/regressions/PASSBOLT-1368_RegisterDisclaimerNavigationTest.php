<?php
/**
 * Feature : Navigation
 * As an AN on the registration page I can click on the legal disclaimer links
 *
 * @copyright (c) 2015-present Bolt Softwares Pvt Ltd
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class PASSBOLT1368 extends PassboltTestCase {

	/**
	 * Scenario: As an AN on the registration page I can click on the legal disclaimer links
	 * Given	I am on the register page
	 * When     I on the privacy link
	 * Then		I go the passbolt.com/privacy page
	 * When     I on the Terms of Service
	 * Then		I go the passbolt.com/terms page
	 */
	public function testLegalDisclaimerNavigation() {
		$this->getUrl('/register');
		$this->assertTitleContain('Register');
		$this->clickLink('Privacy Policy');
		$this->assertTitleContain('Privacy');

		$this->getUrl('/register');
		$this->assertTitleContain('Register');
		$this->clickLink('Terms of Service');
		$this->assertTitleContain('Terms');
	}

}