<?php
namespace App;

define('TOGGLE_BUTTON_PRESSED', 1);
define('TOGGLE_BUTTON_UNPRESSED', 0);

use App\Common\TestTraits\SauceLabTestTrait;
use App\Common\BrowserControllers\ChromeBrowserController;
use App\Common\BrowserControllers\FirefoxBrowserController;
use App\Common\Config;
use App\Common\Servers\PassboltServer;
use App\Common\Servers\ServerRegistry;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Firefox\FirefoxDriver;
use Facebook\WebDriver\Firefox\FirefoxProfile;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Exception\WebDriverException;

use PHPUnit_Runner_BaseTestRunner;

abstract class PassboltTestCase extends AuthenticatedTestCase
{
    use SauceLabTestTrait;

    protected $_quit;
    protected $_browser;
    protected $_failing;
    protected $_build;

    // indicate if the database should be reset at the end of the test
    protected $resetDatabaseWhenComplete = false;

    /**
     * @return mixed
     */
    public function getBrowser()  
    {
        return $this->_browser;
    }

    /**
     * This function is executed before a test is run
     * It setup the capabilities and browser driver
     */
    protected function setUp() 
    {
        $this->_failing = null;

        // Bootstrap config
        try {
            Config::get();
            // Reserve instances for passbolt and selenium.
            if ($this->_useMultiplePassboltInstances()) {
                ServerRegistry::reserveInstance('passbolt');
            }
            if ($this->_useMultipleSeleniumInstances()) {
                ServerRegistry::reserveInstance('selenium');
            }
        } catch (\Exception $e) {
            $this->fail('Config not found');
        }

        // Init browser.
        $this->initBrowser();

        // Maximize window.
        $this->maximizeWindow();

        // SauceAPI.
        if ($this->isSauceLabTest()) {
            $this->setupSaucelab();
        }

        if (Config::read('testserver.selenium.videoRecord')) {
            $this->startVideo();
        }
    }

    /**
     * Check selenium config
     */
    protected function _checkSeleniumConfig() 
    {
        $s = Config::read('testserver');
        $default = Config::read('testserver.default');
        $url = Config::read("testserver.$default.url");
        if(!isset($s) || !isset($url)) {
            $this->stop('ERROR No testserver configuration found.');
        }
    }

    /**
     * Check and get the browser config
     */
    protected function _setBrowserConfig() 
    {
        $browser = getenv('BROWSER');

        // Sanity checks
        if(empty($browser)) {
            $browser = Config::read('browsers.default');
        }
        if(!isset($browser)) {
            $this->stop('ERROR No browser defined either in testsuite or config.');
        }
        $browsers = Config::read('browsers');
        if(!isset($browsers) || !isset($browsers[$browser])) {
            $this->stop('ERROR No browser config found for: ' . $browser);
        }
        $this->_browser = $browsers[$browser];
    }

    /**
     * Return the browser type
     *
     * @return string firefox|chrome
     */
    public function getBrowserType() : string
    {
        return $this->_browser['type'];
    }

    /**
     * Initialize the browser
     */
    public function initBrowser() 
    {
        $this->_setVerbose();
        $this->_setBrowserConfig();
        $this->_checkSeleniumConfig();
        try {
            $capabilities = $this->_getCapabilities();
            $capabilities = $this->_setSauceLabCapabilities($capabilities);
        } catch(\Exception $exception) {
            $this->fail('Browser capabilities not supported.');
        }

        // Build end point url.
        $serverUrl = $this->getTestServerUrl();
        $this->driver = RemoteWebDriver::create($serverUrl, $capabilities, 120000, 120000);

        // Get browser specific controllers.
        if ($this->getBrowserType() == 'firefox') {
            $this->_browserController = new FirefoxBrowserController($this->driver, $this);
        } else {
            $this->_browserController = new ChromeBrowserController($this->driver, $this);
        }
    }

    /**
     * Get Test server url as per config parameters.
     * Default format: http://passbolt:<UUID>@ondemand.saucelabs.com:80/wd/hub
     *
     * @return array|string
     */
    public function getTestServerUrl()
    {
        $testServer = Config::read('testserver.default');
        $serverUrl = Config::read("testserver.$testServer.url");
        if ($testServer == 'saucelabs') {
            $username = Config::read('testserver.saucelabs.username');
            $key = Config::read('testserver.saucelabs.key');
            $serverUrl = "http://$username:$key@$serverUrl";
        }
        return $serverUrl;
    }

    /**
     * Executed after every tests
     */
    protected function tearDown() 
    {
        try {
            // Reset the database if requested
            if ($this->resetDatabaseWhenComplete) {
                PassboltServer::resetDatabase(Config::read('passbolt.url'));
            }

            if ($this->getStatus() == PHPUnit_Runner_BaseTestRunner::STATUS_FAILURE) {
                // Take a screenshot.
                if(Config::read('testserver.selenium.screenshotOnFail')) {
                    $this->takeScreenshot();
                }
                // Retrieve the plugin logs.
                if(!empty($this->_browser['extensions']) && Config::read('testserver.selenium.logs.plugin')) {
                    $this->getLogs();
                }
            }

            // Retrieve the recorded video.
            if (Config::read('testserver.selenium.videoRecord')) {
                $this->stopVideo($this->getStatus());
            }



            if($this->_quit === '0') {
                return;
            } else if(empty($this->_quit)
                || ($this->_quit === '1')
                || ($this->_quit === '2' && isset($this->_failing) && !$this->_failing)
            ) {
                /**
                 * It can happen that the quit function throw a curl exception.
                 * In that case the selenium node crashed, and to avoid the parallel execution
                 * to be a total failure. We :
                 * - catch the exception to avoid the parallel process to crash without finishing the tearDown
                 * - complete the tearDown to release the selenium server instance and make it available for
                 *   another execution.
                 * - Don't forget to add the environment following variables to your docker run :
                 *   > -e MAX_INSTANCES=5 -e MAX_SESSIONS=5
                 *   It will allow the selenium server to accept more than one call, so even if one crash it does
                 *   not lock the server.
                 */
                try {
                    $this->getDriver()->quit();
                } catch(\Exception $e) {
                    // Do nothing
                }
            }

            // If test was running on saucelabs, we update the status.
            if ($this->isSauceLabTest()) {
                $this->updateTestStatus();
            }

            // Display logs if any.
            $this->displayLogs();

            try {
                // Release instance.
                if ($this->_useMultiplePassboltInstances()) {
                    ServerRegistry::releaseInstance('passbolt');
                }
                if ($this->_useMultipleSeleniumInstances()) {
                    ServerRegistry::releaseInstance('selenium');
                }
            } catch (\Exception $exception) {
                // TODO How do we recover from this?
            }

        } catch (\Exception $e) {
            $this->consoleLog($e->getMessage());
            $logPath = Config::read('testserver.selenium.logs.path');
            $log = $e->getMessage() . "\n" . $e->getTraceAsString();
            $filePath = "$logPath/{$this->testName}_tear_down_exception.log";
            file_put_contents($filePath, $log);
        }
    }

    /**
     * Mark the database to be reset at the end of the test
     */
    public function resetDatabaseWhenComplete() 
    {
        $this->resetDatabaseWhenComplete = true;
    }

    /**
     * Does the test use multiple selenium nodes?
     *
     * @return bool
     */
    protected function _useMultipleSeleniumInstances() 
    {
        $isSelenium = (Config::read('testserver.default') === 'selenium');
        $seleniumInstances = Config::read('testserver.selenium.instances');
        return ($isSelenium && !empty($seleniumInstances));
    }

    /**
     * Does the test use multiple passsbolt isntances?
     *
     * @return bool
     */
    protected function _useMultiplePassboltInstances() 
    {
        $passboltInstances = Config::read('passbolt.instances');
        return (!empty($passboltInstances));
    }

    /**
     * Assert post conditions.
     */
    protected function assertPostConditions() 
    {
        $this->_failing = false;
        parent::assertPostConditions();
    }

    /************************************************
     * BROWSER OPTIONS AND CAPABILITIES
     ************************************************/
    /**
     * Get desired capabilities from config
     *
     * @return DesiredCapabilities|null
     * @throws WebDriverException is preference does not exist
     */
    public function _getCapabilities()
    {
        $capabilities = null;
        switch($this->_browser['type']) {
        case 'firefox':
            $capabilities = $this->setFirefoxOptions();
            break;
        case 'chrome':
            $capabilities = $this->setChromeOptions();
            break;
        default:
            $this->stop('ERROR Sorry this browser type is not supported.');
            break;
        }
        return $capabilities;
    }

    /**
     * Set firefox options
     *
     * @throws WebDriverException is preference does not exist
     * @return DesiredCapabilities
     */
    protected function setFirefoxOptions() 
    {
        $profile = new FirefoxProfile();
        $capabilities = DesiredCapabilities::firefox();

        if (isset($this->_browser['extensions'])) {
            foreach($this->_browser['extensions'] as $i => $ext_path) {
                if (!is_file($ext_path)) {
                    $this->stop('ERROR The extension file was not found: ' . $ext_path);
                }
                $profile->addExtension($ext_path);
            }
        }

        // Set download preferences for the browser.
        $profile->setPreference("browser.download.folderList", 2);
        $profile->setPreference("browser.helperApps.neverAsk.saveToDisk", "text/plain");
        $profile->setPreference("xpinstall.signatures.required", false);
        $profile->setPreference("browser.startup.page", 0); // Empty start page
        $profile->setPreference("browser.startup.homepage_override.mstone", "ignore"); // Suppress the "What's new" page

        $capabilities->setCapability(FirefoxDriver::PROFILE, $profile);

        // If custom firefox binary to use.
        $binaryPath = Config::read('browsers.firefox_common.binary_path');
        if (!empty($binaryPath)) {
            $capabilities->setCapability(
                'moz:firefoxOptions', array(
                    'binary' => $binaryPath
                )
            );
        }

        // Accept insecure connections.
        $capabilities->setCapability('acceptInsecureCerts', true);
        return $capabilities;
    }

    /**
     * Set chrome options
     *
     * @return DesiredCapabilities
     */
    protected function setChromeOptions() 
    {
        $options = new ChromeOptions();
        $options->addArguments(array('enable-extensions'));
        $capabilities = DesiredCapabilities::chrome();

        if (isset($this->_browser['extensions'])) {
            foreach($this->_browser['extensions'] as $i => $ext_path) {
                if (!is_file($ext_path)) {
                    $this->stop('ERROR The extension file was not found: ' . $ext_path);
                }
                $options->addExtensions($this->_browser['extensions']);
            }
        }
        // TODO: set options for auto-download if required. (check firefox preferences above).
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
        return $capabilities;
    }

}
