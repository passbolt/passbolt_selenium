<?php
/**
 * Feature : Register
 * As an anonymous user, I need to be able to register
 *
 * @copyright 	(c) 2015-present Bolt Software Pvt. Ltd.
 * @licence			GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
 */
class RegisterTest extends WebDriverTestCase {

	/**
	 * Scenario: I can see the registration form on the registration page
	 * Given	I am on the registration page
	 * Then 	I can see I am on the right page by looking at the title
	 * And 		I can see the registration form
	 * And 		I can see the firstname field
	 * And 		I can see the lastname field
	 * And 		I can see the username field
	 * And 		I can see the username field
	 * And 		I can see the username field
	 */
	public function testCanSeeTheForm() {
		$url = Config::read('passbolt.url') . DS . 'register';
		$this->driver->get($url);

		$title = $this->driver->getTitle();
		$this->assertContains('Passbolt',$title);
		$this->assertContains('Register',$title);

		try {
			$this->findById('UserRegisterForm');
		} catch (NoSuchElementException $e) {
			$this->fail('User registration form was not found');
		}

		try {
			$this->findById('ProfileFirstName');
		} catch (NoSuchElementException $e) {
			$this->fail('User registration profile last name not found');
		}

		try {
			$this->findById('ProfileLastName');
		} catch (NoSuchElementException $e) {
			$this->fail('User registration profile last name not found');
		}

		try {
		} catch (NoSuchElementException $e) {
			$this->fail('User registration profile username not found');
		}

		try {
			$this->findByCSS('#UserRegisterForm input[type=submit]');
		} catch (NoSuchElementException $e) {
			$this->fail('There is no submit button in the registration form');
		}

		$this->assertTrue(true);
	}

	/**
	 * Scenario: I cannot register without giving my name
	 * Given	I am on the registration page
	 * When   I provide an empty firstname
	 * And		I provide an empty lastname
	 * And 		I provide my username
	 * And 		I press enter
	 * Then 	I should not be able to see the thank you page
	 */
	/**
	 * @depends testCanSeeTheForm
	 */
	public function testCannotRegisterWithEmptyName() {
		$url = Config::read('passbolt.url') . DS . 'register';
		$this->driver->get($url);

		$this->inputText('UserUsername','test+'.time().'@passbolt.com');
		$this->pressEnter();

		$this->assertEquals($url, $this->driver->getCurrentURL());

	}

	/**
	 * Scenario: I cannot register with a wrong username
	 * Given	I am on the registration page
	 * When   I provide a firstname
	 * And		I provide a lastname
	 * And 		I provide a wrong username
	 * And 		I press enter
	 * Then 	I should not be able to see the thank you page
	 * Then		I should see an error message
	 */
	/**
	 * @depends testCannotRegisterWithEmptyName
	 */
	public function testCannotRegisterWithWrongEmail() {
		$url = Config::read('passbolt.url') . DS . 'register';
		$this->driver->get($url);

		$this->inputText('ProfileFirstName','TestFirstname');
		$this->inputText('ProfileLastName','TestLastname');
		$this->inputText('UserUsername','test*passbolt.com');
		$this->pressEnter();

		$this->assertEquals($url, $this->driver->getCurrentURL());
		try {
			$this->findByCss('#UserRegisterForm .error-message');
		} catch (NoSuchElementException $e) {
			$this->fail('There is no error message event though the email is wrong');
		}

	}

	/**
	 * Scenario: I can register
	 * Given	I am on the registration page
	 * When   I provide my firstname
	 * And		I provide my lastname
	 * And 		I provide my username
	 * And 		I press enter
	 * Then 	I should see the thank you page
	 */
	/**
	 * @depends testCannotRegisterWithWrongEmail
	 */
	public function testCanRegister() {
		$url = Config::read('passbolt.url') . DS . 'register';
		$this->driver->get($url);

		$title = $this->driver->getTitle();
		$this->assertContains('Passbolt',$title);
		$this->assertContains('Register',$title);

		$this->inputText('ProfileFirstName','TestFirstname');
		$this->inputText('ProfileLastName','TestLastname');
		$this->inputText('UserUsername','test+'.time().'@passbolt.com');
		$this->pressEnter();

		$this->assertEquals($url . DS . 'thankyou', $this->driver->getCurrentURL());

	}

}