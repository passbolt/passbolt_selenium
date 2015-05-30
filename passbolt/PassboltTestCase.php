<?php
/**
 * Passbolt Test Case
 * The base class for test cases related to passbolt.
 *
 * @copyright 	(c) 2015-present Bolt Software Pvt. Ltd.
 * @licence			GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
 */
class PassboltTestCase extends WebDriverTestCase {

	/**
	 * Goto a given url
	 * @param $url
	 */
	public function getUrl($url=null) {
		$url = Config::read('passbolt.url') . DS . $url;
		$this->driver->get($url);
	}

	/**
	 * Check if the given title is contain in the one of the page
	 * @param $title
	 */
	public function assertTitleContain($title) {
		$t = $this->driver->getTitle();
		$this->assertContains($title,$t);
	}

	/**
	 * Check if the current url match the one given in parameter
	 * @param $url
	 */
	public function assertCurrentUrl($url) {
		$url = Config::read('passbolt.url') . DS . $url;
		$this->assertEquals($url, $this->driver->getCurrentURL());
	}

	/**
	 * Check if the given role is matching the one advertised on the app side
	 * @param $role
	 */
	public function assertCurrentRole($role) {
		try {
			$e = $this->findByCSS('html.' . $role);
			if(count($e)) {
				$this->assertTrue(true);
			} else {
				$this->fail('The current user role is not ' . $role);
			}
		} catch (NoSuchElementException $e) {
			$this->fail('The current user role is not ' . $role);
		}
	}

	/**
	 * Check that there is no plugin
	 */
	public function assertNoPlugin() {
		try {
			$this->findByCSS('html.no-passboltplugin');
		} catch (NoSuchElementException $e) {
			$this->fail('A passbolt plugin was found');
		}
		$this->assertTrue(true);
	}

	/**
	 * Check that there is a plugin
	 */
	public function assertPlugin() {
		try {
			$this->findByCSS('html.passboltplugin');
		} catch (NoSuchElementException $e) {
			$this->fail('A passbolt plugin was not found');
		}
		$this->assertTrue(true);
	}


}
