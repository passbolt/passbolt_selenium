<?php
/**
 * PassboltSetup Test Case
 * A specialized class to test the setup of passbolt.
 * It contains a set of functions useful to test specific elements of the setup.
 *
 * @copyright (c) 2015-present Bolt Software Pvt. Ltd.
 * @licence GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
 */
class PassboltSetupTestCase extends PassboltTestCase {

	/**
	 * Assert that the title equals the one given.
	 * @param $title
	 */
	public function assertTitleEquals($title) {
		$elt = $this->findById('js_step_title');
		$this->assertEquals($elt->getText(), $title);
	}

	/**
	 * Assert if the given menu is selected.
	 * @param $text
	 */
	public function assertMenuIsSelected($text) {
		$elt = $this->driver->findElement(
			WebDriverBy::xpath(
				"//div[@id = 'js_menu']//a[text()='$text']/.."
			)
		);
		$this->assertElementHasClass(
			$elt,
			'selected'
		);

	}

}