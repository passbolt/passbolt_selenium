<?php
/**
 * Bug PASSBOLT-1417 - Regression test
 *
 * @copyright (c) 2015-present Bolt Softwares Pvt Ltd
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class PASSBOLT1417 extends PassboltTestCase {

	public function testSetupMultipleTimes() {
		// Register John Doe as a user.
		$this->registerUser('John', 'Doe', 'johndoe@passbolt.com');

		// Go to setup page.
		$this->goToSetup('johndoe@passbolt.com');
		$this->completeRegistration();

		// Register John Doe as a user.
		$this->registerUser('John', 'Doe', 'johndoe1@passbolt.com');

		// Go to setup page.
		$this->goToSetup('johndoe1@passbolt.com');

		sleep(10);

	}
}