<?php
/**
 * Feature : Recover
 *
 * @copyright (c) 2015-present Bolt Softwares Pvt Ltd
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */

class RecoverTest extends PassboltTestCase {

	/**
	 * Scenario: As AN trying to recover my account, I should see a page informing me that I need the plugin.
	 * Given	I am an anonymous user on the recover page
	 * When		I input my email address in the form
	 * And		I press enter
	 * Then		I should see a thank you page
	 * When		I click on the link in the email
	 * Then		I should be on the setup page first step
	 * And		I should see an error message telling me an add-on is required to use passbolt
	 */
	public function testRecoverNoPlugin() {
		$this->resetDatabaseWhenComplete();
		$this->getUrl('recover');
		$this->inputText('UserUsername', 'ada@passbolt.com');
		$this->pressEnter();
		$this->waitUntilISee('.page.recover.thank-you');
		$this->goToRecover('ada@passbolt.com', false);
		$this->waitUntilISee('.error .message', '/An add-on is required to use passbolt/');
	}

}