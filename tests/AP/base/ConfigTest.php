<?php
/**
 * Anonymous user with plugin but no config test
 *
 * @copyright 	(c) 2015-present Bolt Software Pvt. Ltd.
 * @licence			GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
 */
class ConfigTest extends PassboltTestCase {

	public function testConfig() {
		$this->getUrl();
		$this->assertCurrentRole('guest');
		$this->assertPlugin();
		$this->assertNoPluginConfig();
	}

}