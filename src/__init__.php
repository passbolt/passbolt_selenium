<?php
/**
 * Passbolt Functional Test Framework
 *
 * @copyright (c) 2017 Passbolt SARL
 * @licence   GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
if(!defined('ROOT')) {
    echo 'Boostrap missing... exiting.'."\n";
    die;
}

// Vendor dependencies.
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Utility clases
require_once 'lib/Cakephp/CakeText.php';
require_once 'lib/Cakephp/Hash.php';
require_once 'lib/Color.php';
require_once 'lib/Uuid.php';
require_once 'lib/Config.php';

// ImageCompare
require_once 'lib/ImageCompare/src/point.php';
require_once 'lib/ImageCompare/src/boundary.php';
require_once 'lib/ImageCompare/src/color.php';
require_once 'lib/ImageCompare/src/crawler.php';
require_once 'lib/ImageCompare/src/crawleroutline.php';
require_once 'lib/ImageCompare/src/crawleroutlinecollection.php';
require_once 'lib/ImageCompare/src/image.php';
require_once 'lib/ImageCompare/src/imagecollection.php';
require_once 'lib/ImageCompare/src/imagepixelmatrix.php';
require_once 'lib/ImageCompare/src/pixel.php';

// Saucelabs
//require_once dirname(__DIR__) . '/vendor/sauce/sausage/src/Sauce/Sausage/SauceMethods.php';
//require_once dirname(__DIR__) . '/vendor/sauce/sausage/src/Sauce/Sausage/SauceAPI.php';

// Test case redefinition
require_once 'WebDriverTestCase.php';
require_once 'lib/PassboltServer.php';
require_once 'PassboltTestCase.php';
require_once 'PassboltSetupTestCase.php';
//
//// Browser specific controllers.
//require_once 'ChromeBrowserController.php';
//require_once 'FirefoxBrowserController.php';

// Fixtures classes
require_once FIXTURES . 'User.php';
require_once FIXTURES . 'Resource.php';
require_once FIXTURES . 'Group.php';
require_once FIXTURES . 'Gpgkey.php';
require_once FIXTURES . 'SystemDefaults.php';