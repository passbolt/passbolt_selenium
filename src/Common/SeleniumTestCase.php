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
use App\Common\Actions\UrlActionsTrait;
use App\Common\Actions\MouseActionsTrait;
use App\Common\Asserts\FormAssertionsTrait;
use App\Common\Asserts\ElementAssertionsTrait;
use App\Common\Asserts\PageAssertionsTrait;
use App\Common\Asserts\UrlAssertionsTrait;
use App\Common\Asserts\VisibilityAssertionsTrait;
use App\Common\Asserts\WaitAssertionsTrait;
use App\Common\TestTraits\FindHelperTrait;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use PHPUnit_Framework_TestCase;
use PHPUnit_Runner_BaseTestRunner;

abstract class SeleniumTestCase extends PHPUnit_Framework_TestCase
{
    use BrowserActionsTrait;
    use ElementAssertionsTrait;
    use FindHelperTrait;
    use FormAssertionsTrait;
    use FormActionsTrait;
    use KeyboardActionsTrait;
    use MouseActionsTrait;
    use PageAssertionsTrait;
    use UrlActionsTrait;
    use UrlAssertionsTrait;
    use VisibilityAssertionsTrait;
    use WaitAssertionsTrait;

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
        $name = $this->toString();
	    // Replace unwanted parts of name to have something more readable.
	    // Example: Tests\LU\Base\PasswordCommentTest::testExample => LU::PasswordCommentTest::testExample
	    $name = str_replace(['Tests\\', 'Base\\', '\\'], ['', '', '::'], $name);
	    $name = trim($name, ':');
	    return $name;
    }

    /**
     * Tell a test if they must leave browser window open
     *
     * @return bool|mixed
     */
    protected function mustCloseBrowser() {
        // Always quit if config is missing
        $quit = Config::read('testserver.selenium.quitOnFailure');
        if (!isset($quit)) {
            return true;
        }

        // Quit if config says to quit on failure (or error)
        if ($this->getStatus() >= PHPUnit_Runner_BaseTestRunner::STATUS_FAILURE) {
            return $quit;
        }

        return true;
    }
}