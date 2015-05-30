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

require_once ROOT . '/lib/__init__.php';

require_once('String.php');
require_once('Hash.php');
require_once('Config.php');
Config::get();

require_once('WebDriverTestCase.php');
require_once('PassboltTestCase.php');
