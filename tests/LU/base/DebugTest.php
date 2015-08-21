<?php

class DebugTest extends PassboltTestCase {
	public function testSetDebug() {
		$this->getUrl();
		$this->assertCurrentRole('guest');
		$this->assertPlugin();
		$this->assertNoPluginConfig();

		$this->getUrl('debug');

		sleep(2); // plugin need some time to trigger a page change

		try {
			$this->findByCss('.page.debug.plugin');
		} catch (NoSuchElementException $e) {
			$this->fail('Page debug config not found');
		}

		$this->inputText('ProfileFirstName','Ada');
		$this->inputText('ProfileLastName','Lovelace');
		$this->inputText('UserUsername','ada@passbolt.com');

		$this->inputText('securityTokenCode','ADA');
		$this->inputText('securityTokenColor','#ff3a3a');
		$this->inputText('securityTokenTextColor','#ffffff');

		$this->click('js_save_conf');

		$key = file_get_contents(GPG_FIXTURES . DS . 'ada_private.key' );

		$this->inputText('keyAscii',$key);
		$this->click('saveKey');

		$this->getUrl('login');

	}
}