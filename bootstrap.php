<?php
/**
 * Test bootstrap process
 * Used in phpunit.xml config to add our additional components
 *
 * @copyright (c) 2017 Passbolt SARL
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
define('ROOT', dirname(__FILE__));
define('DS', DIRECTORY_SEPARATOR);
define('DATA', 'data' . DS);
define('FIXTURES', ROOT . DS . DATA . 'fixtures' . DS);
define('GPG_FIXTURES', FIXTURES . 'gpg');
define('GPG_DUMMY', FIXTURES . 'gpg-dummy');
define('GPG_SERVER', FIXTURES . 'gpg-server');
define('IMG_FIXTURES', FIXTURES . 'img');

require_once ROOT . DS . 'vendor' . DS . 'autoload.php';
