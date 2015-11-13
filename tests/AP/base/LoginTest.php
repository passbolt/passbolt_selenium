<?php
/**
 * Anonymous user with plugin but no config login test
 *
 * @copyright 	(c) 2015-present Bolt Software Pvt. Ltd.
 * @licence			GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
 */
class LoginTest extends PassboltTestCase {

    public function testLogin() {
        $this->getUrl('login');
        $this->waitUntIlISee('.plugin-check.firefox.warning', null, 2);
    }

}