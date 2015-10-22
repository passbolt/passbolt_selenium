<?php
/**
 * User with configured plugin login test
 *
 * @copyright 	(c) 2015-present Bolt Software Pvt. Ltd.
 * @licence			GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
 */
class LoginTest extends PassboltTestCase {

    public function testLogin() {
        // Given I am Ada
        $user = User::get('ada');
        $this->setClientConfig($user);

        $this->getUrl('login');
        $this->assertVisible('.plugin-check.firefox.success');
    }

}