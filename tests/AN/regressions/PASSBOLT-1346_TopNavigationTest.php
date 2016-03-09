<?php
/**
 * Feature : Navigation
 * As an anonymous user I should be able to use the top navigation
 *
 * @copyright (c) 2015-present Bolt Softwares Pvt Ltd
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class PASSBOLT1346 extends PassboltTestCase {

	/**
	 * Scenario: As AN, passbolt logo at top left should not point to passbolt.com, but to the app url
	 * Given	I am on the home page
	 * When     I click on the logo on the top right corner
	 * Then		I go to the login page
	 * When		I click on the home link
	 * Then		I go to the login page
	 * When		I click on the login link
	 * Then		I go to the login page
	 * When		I click on the register link
	 * Then		I go to the login page
	 */
	public function testTopNavigationLink() {
		$this->getUrl('/');
		$this->assertTitleContain('Login');
		$this->click('.top.navigation.primary .home a');
		$this->assertTitleContain('Login');
		$this->clickLink('home');
		$this->assertTitleContain('Login');
		$this->clickLink('login');
		$this->assertTitleContain('Login');
		$this->clickLink('register');
		$this->assertTitleContain('Register');
	}

}