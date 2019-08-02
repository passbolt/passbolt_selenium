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

use App\Common\Actions\DebugActionsTrait;
use Facebook\WebDriver\WebDriverBy;

class LoggableTestCase extends SeleniumTestCase
{
    use DebugActionsTrait;

    /**
     * @var string $testName name of the current test
     */
    public $testName;

    /**
     * @var string logs
     */
    protected $_log;

    /**
     * @var
     */
    protected $_verbose;

    /**
     * Set verbose from config
     */
    protected function _setVerbose()
    {
        $this->_verbose = getenv('VERBOSE');

        // Default is false
        if(empty($this->_verbose)) {
            $this->_verbose = 0;
        }
    }

    /**
     * Write logs on file
     */
    public function getLogs() 
    {
        // If the log folder doesn't exist yet.
        $logPath = Config::read('testserver.selenium.logs.path');
        if (!file_exists($logPath)) {
            mkdir($logPath);
        }
        // Retrieve the logs.
        $this->goToDebug();
        $logsElt = $this->getDriver()->findElement(WebDriverBy::id('logsContent'));
        $logs = $logsElt->getText();
        // Store the logs on the server.
        $filePath = "$logPath/{$this->getTestName()}_plugin.json";
        file_put_contents($filePath, $logs);
    }

    /**
     * Display logs if any
     */
    public function displayLogs() 
    {
        if(isset($this->_log) && !empty($this->_log)) {
            echo "\n\n"
            . "=== Webdriver Test Case Log ===" . "\n"
            . $this->_log;
        }
    }

    /**
     * Log a message if verbose is set
     *
     * @param $msg
     */
    public function log($msg)
    {
        if($this->_verbose) {
            $this->_log .= $msg . "\n";
        }
    }

    /**
     * Display message in the console
     * @param $myDebugVar
     */
    public function consoleLog($myDebugVar) {
        fwrite(STDERR, print_r($myDebugVar, TRUE));
    }
}