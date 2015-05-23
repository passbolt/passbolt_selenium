<?php
/**
 * Passbolt Functional Test Framework
 *
 * @copyright 	(c) 2015-present Bolt Software Pvt. Ltd.
 * @licence			GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
 */
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__FILE__));

require_once ROOT . '/../lib/__init__.php';

require_once('String.php');
require_once('Hash.php');
require_once('Config.php');
Config::get();

require_once('WebDriverTestCase.php');
