<?php

class ConfigTest extends PassboltTestCase {

	public function testGetTitle() {
		$this->driver->get(Config::read('passbolt.url'));
		$this->assertContains('Passbolt', $this->driver->getTitle());
	}

}