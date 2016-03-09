<?php
/**
 * Anonymous user with plugin but no config login test
 *
 * @copyright (c) 2015-present Bolt Softwares Pvt Ltd
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class LoginTest extends PassboltTestCase {

    public function testLogin() {
        $this->getUrl('login');
        $this->waitUntIlISee('.plugin-check.firefox.warning', null, 2);
    }

	/**
	 * Test that if the wrong domain is configured, we will see a page explaining that
	 * the domain is not known.
	 * @throws Exception
	 */
	public function testWrongDomain() {
		$user = User::get('ada');
		$user['domain'] = 'https://custom.passbolt.com';
		$this->setClientConfig($user);

		$this->getUrl('login');
		$this->waitUntilISee('html.domain-unknown');
		$this->waitUntilISee('a.trusteddomain', '/https:\/\/custom\.passbolt\.com/');
	}

}