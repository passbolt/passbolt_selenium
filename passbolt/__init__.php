<?php
/**
 * Passbolt Functional Test Framework
 *
 * @copyright 	(c) 2015-present Bolt Software Pvt. Ltd.
 * @licence			GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
 */
if(!defined ('ROOT')) {
	echo 'Boostrap missing... exiting.'."\n";
	die;
}

// Utility clases
require_once('String.php');
require_once('Hash.php');
require_once('Color.php');
require_once('Config.php');
Config::get();

// Vendor dependencies.
require_once(ROOT . '/lib/__init__.php');
require_once('vendor/autoload.php');

// Test case redefinition
require_once('WebDriverTestCase.php');
require_once('PassboltServer.php');
require_once('PassboltTestCase.php');
require_once('PassboltSetupTestCase.php');

// Fixtures classes
require_once(FIXTURES . 'Users.php');
require_once(FIXTURES . 'Resources.php');
require_once(FIXTURES . 'SystemDefaults.php');