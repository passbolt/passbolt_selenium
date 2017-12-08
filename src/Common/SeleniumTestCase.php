<?php
/**
 * Passbolt ~ Open source password manager for teams
 * Copyright (c) Passbolt SARL (https://www.passbolt.com)
 *
 * Licensed under GNU Affero General Public License version 3 of the or any later version.
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Passbolt SARL (https://www.passbolt.com)
 * @license   https://opensource.org/licenses/AGPL-3.0 AGPL License
 * @link      https://www.passbolt.com Passbolt(tm)
 * @since     2.0.0
 */
namespace App\Common;

use App\Common\Actions\KeyboardActionsTrait;
use App\Common\Actions\BrowserActionsTrait;
use App\Common\Actions\FormActionsTrait;
use App\Common\Asserts\FormAssertionsTrait;
use App\Common\TestTraits\ElementAssertTrait;
use App\Common\TestTraits\FindHelperTrait;
use App\Common\TestTraits\MouseTestTrait;
use App\Common\TestTraits\PageTestTrait;
use App\Common\TestTraits\UrlTestTrait;
use App\Common\TestTraits\VisibilityTestTrait;
use App\Common\TestTraits\WaitTestTrait;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use PHPUnit_Framework_TestCase;

abstract class SeleniumTestCase extends PHPUnit_Framework_TestCase
{
    use BrowserActionsTrait;
    use ElementAssertTrait;
    use FindHelperTrait;
    use FormAssertionsTrait;
    use FormActionsTrait;
    use KeyboardActionsTrait;
    use MouseTestTrait;
    use PageTestTrait;
    use UrlTestTrait;
    use VisibilityTestTrait;
    use WaitTestTrait;

    /**
     * @var RemoteWebDriver $driver
     */
    public $driver;

    /**
     * @var string $testName
     */
    public $testName;

    /**
     * Get the web driver
     *
     * @return RemoteWebDriver
     */
    public function getDriver(): RemoteWebDriver
    {
        return $this->driver;
    }

    /**
     * We need a special method to handle configuration error that stops execution
     * since exceptions are catched in a phpunit context
     *
     * @param string $msg
     */
    protected function stop(string $msg)
    {
        echo $msg . "\n";
        $this->tearDown();
        exit;
    }

    /**
     * Return the current test name
     *
     * @return string
     */
    public function getTestName() : string 
    {
        return $this->toString();
    }

}