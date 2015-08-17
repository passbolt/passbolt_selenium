<?php
/**
 * Anonymous user without plugin config test
 *
 * @copyright 	(c) 2015-present Bolt Software Pvt. Ltd.
 * @licence			GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
 */
class ConfigTest extends PassboltTestCase {

 /**
	* Given that I am on the As a user on passbolt app
  * 	I should see Passbolt in the title of the home page
	*/
	public function testCheckConfig() {
		$this->getUrl();
		$this->assertCurrentRole('guest');
		$this->assertNoPlugin();
	}

}