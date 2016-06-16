<?php
/**
 * Feature : Recover
 *
 * @copyright (c) 2015-present Bolt Softwares Pvt Ltd
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */

class RecoverTest extends PassboltTestCase {

	/**
	 * Scenario:    As AN trying to recover my account, I should see a page informing me that I need the plugin.
	 */
	public function testRecoverNoPlugin() {
		$this->getUrl('recover');
		$this->inputText('UserUsername', 'ada@passbolt.com');
		$this->pressEnter();
		$this->waitUntilISee('.page.recover.thank-you');

		$this->goToRecover('ada@passbolt.com', false);

		$this->waitUntilISee('.error .message', '/An add-on is required to use passbolt/');

		$this->resetDatabase();
	}

}