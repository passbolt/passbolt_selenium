<?php
/**
 * Feature : Login
 * As an anonymous user without the plugin I should not be able to login
 *
 * @copyright 	(c) 2015-present Bolt Software Pvt. Ltd.
 * @licence			GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
 */
class LoginTest extends PassboltTestCase {

	/**
	 * Scenario: I can see an error message telling me I need a plugin
	 * Given 	I am an anonymous user with no plugin on the login page
	 * When 	The page is loaded
	 * Then 	I can see the title of the page contain 'Login'
	 * Then 	I can see the error message telling me an add-on is required
	 */
	public function testCanSeeErrorMsg() {
		$this->getUrl('login');
		$this->assertTitleContain('Login');

		try {
			$e = $this->findByCss('.plugin-check.firefox.error');
			$this->assertTrue($e->isDisplayed());
		} catch (NoSuchElementException $e) {
			$this->fail('Plugin check error message was not found (CSS: .plugin-check.firefox.error)');
		}

		$this->assertTrue(true);
	}

	/**
	 * Scenario: I can see a login form on the login page
	 * Given 	I am an anonymous user with no plugin on the login page
	 * When		When the page is loaded
	 * Then 	I can see login form
	 * And		I can see the username field
	 * And		I can see the password field
	 * And 		I can see the submit button
	 */
	/**
	 * @depends testCanSeeErrorMsg
	 */
	public function testCanSeeLoginForm() {
		$this->getUrl('login');

		try {
			$this->findById('UserLoginForm');
		} catch (NoSuchElementException $e) {
			$this->fail('User login form was not found');
		}

		try {
			$this->findById('UserUsername');
		} catch (NoSuchElementException $e) {
			$this->fail('Username text field not found on login form');
		}

		try {
			$this->findById('UserPassword');
		} catch (NoSuchElementException $e) {
			$this->fail('Password text field not found on login form');
		}

		try {
			$this->findByCSS('#UserLoginForm input[type=submit]');
		} catch (NoSuchElementException $e) {
			$this->fail('There is no submit button in the registration form');
		}

	}

	/**
	 * Scenario: I cannot login because I don't have the plugin
	 * Given I am an anonymous user with no plugin on the login page
	 * When	 I insert valid credentials
	 *﻿ And	 I press enter
	 * Then	 I should still be on the login page
	 * And	 My role should be guest
	 */
	/**
	 * @depends testCanSeeLoginForm
	 */
	public function testCantLogin() {
		$this->getUrl('login');

		$u = Config::read('passbolt.users.default');
		$this->inputText('UserUsername',$u['username']);
		$this->inputText('UserPassword',$u['password']);
		$this->pressEnter();

		$this->assertCurrentUrl('login');
		$this->assertCurrentRole('guest');
	}

	/**
	 * Scenario: I should not see warnings if I accept cookies and javascript is enabled
	 * Given 	I am an anonymous user with no plugin on the login page
	 * Then	 	I should not see a cookie warning
	 * Then  	I should not see a javascript warning
	 */
	public function testNoCookieBanner() {
		$this->getUrl('login');
		$this->assertNotVisible('.message.error.no-js');
		$this->assertNotVisible('.message.error.no-cookies');
	}
}