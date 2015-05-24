<?php

class ConfigTest extends WebDriverTestCase {

	public function testGetTitle() {
		$this->driver->get(Config::read('passbolt.url'));
		self::assertEquals(
			'Login | Passbolt',
			$this->driver->getTitle()
		);
	}

}