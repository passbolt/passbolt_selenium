<?php
/**
 * Anonymous user config test
 *
 * @copyright 	(c) 2015-present Bolt Software Pvt. Ltd.
 * @licence			GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
 */
class ConfigTest extends WebDriverTestCase {

 /**
	* Given that I am on the As a user on passbolt app
  * 	I should see Passbolt in the title of the home page
	*/
	public function testGetTitle() {
		$this->driver->get(Config::read('passbolt.url'));
		$this->assertContains('Passbolt', $this->driver->getTitle());
	}

}