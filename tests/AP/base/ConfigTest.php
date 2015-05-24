<?php

class ConfigTest extends WebDriverTestCase {

	public function testGetTitle() {
		$this->driver->get(Config::read('passbolt.url'));
		$this->assertContains('Passbolt', $this->driver->getTitle());
	}

}