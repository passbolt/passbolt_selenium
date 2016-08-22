<?php
// The environment constants below define where the testsuite is located in the runtime environment.
// Firefox should be able to access the files of the testsuite, to access the fixtures for instance.
define('SELENIUM_ROOT', Config::read('testsuite.path'));
define('SELENIUM_FIXTURES', SELENIUM_ROOT . DS . DATA . 'fixtures' . DS);
define('SELENIUM_IMG_FIXTURES', SELENIUM_FIXTURES . 'img');
define('SELENIUM_TMP', SELENIUM_ROOT . DS . 'tmp');

/**
 * Web Driver Test Case
 * The base class for test cases.
 *
 * @copyright (c) 2015-present Bolt Softwares Pvt Ltd
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class WebDriverTestCase extends PHPUnit_Framework_TestCase {

    public $driver;             // @var RemoteWebDriver $driver
    protected $_browser;
    protected $_verbose;
    protected $_log;
    protected $_quit;
    protected $_failing;

	// Name of the current test.
	public $testName;

	protected $_saucelabs; // boolean

	// Saucelab job.
	protected $sauceAPI;
	protected $sauceLabJob;

    /********************************************************************************
     * Pre/Post Tests Execution Callback
     ********************************************************************************/
    /**
     * This function is executed before a test is run
     * It setup the capabilities and browser driver
     */
    protected function setUp() {
        $this->_failing = null;
	    $this->testName = $this->toString();

	    $this->_saucelabs = Config::read('testserver.default') == 'saucelabs' ? true : false;

	    // Reserve instances for passbolt and selenium.
	    if ($this->__useMultiplePassboltInstances()) {
		    self::reserveInstance('passbolt');
		    self::logFile("> Reserved passbolt instance: " . Config::read('passbolt.url') . " (" . $this->testName . ")");
	    }
	    if ($this->__useMultipleSeleniumInstances()) {
		    self::reserveInstance('selenium');
		    self::logFile("> Reserved selenium instance: " . Config::read('testserver.selenium.url') . " (" . $this->testName . ")");
	    }

	    // Init browser.
		$this->initBrowser();

	    // Maximize window.
	    $this->driver->manage()->window()->maximize();

	    // SauceAPI.
	    if ($this->_saucelabs) {
		    $this->sauceAPI = new Sauce\Sausage\SauceAPI(Config::read('testserver.saucelabs.username'), Config::read('testserver.saucelabs.key'));
		    $this->sauceLabJob = $this->getSauceLabJob();
	    }
    }

	/**
	 * Get saucelab job corresponding to current test through rest API.
	 * @return array Sauce lab job object.
	 */
	public function getSauceLabJob() {
		$jobs = $this->sauceAPI->getJobs(0, null, 10)['jobs'];
		foreach ($jobs as $job) {
			if ($job['name'] == $this->testName) {
				return $job;
			}
		}
		return false;
	}

	/**
	 * Initialize the browser
	 */
	public function initBrowser() {
		$this->_setVerbose();
		$this->_setBrowserConfig();
		$this->_checkSeleniumConfig();
		$capabilities = $this->_getCapabilities();

		// TODO: move this in config.
		if ($this->_saucelabs) {
			$capabilities->setCapability('platform', 'Windows 10');
			$capabilities->setCapability('version', '47.0');
			$capabilities->setCapability('screenResolution', '1280x1024');
			$capabilities->setCapability('name', $this->testName);
			// TODO: define build number.
			$capabilities->setCapability('build', time());
			// TODO : define this in config file.
//			$capabilities->setCapability('videoUploadOnPass', false);
//			$capabilities->setCapability('recordVideo', false);
//			$capabilities->setCapability('recordScreenshots', false);
//			$capabilities->setCapability('recordLogs', false);
		}

		// Build end point url.
		$serverUrl = $this->getTestServerUrl();
		$this->driver = RemoteWebDriver::create($serverUrl, $capabilities, 120000, 120000);

		// Redirect it immediately to an empty page, so we avoid the default firefox home page.
		$this->driver->get('');
	}

	public static function logFile($str) {
		file_put_contents(ROOT . DS . 'tmp' . DS . 'logs.txt', $str . "\n", FILE_APPEND);
	}

	/**
	 * Get Test server url as per config parameters.
	 * @return array|string
	 */
	public function getTestServerUrl() {
		$testServer = Config::read('testserver.default');
		$serverUrl = Config::read("testserver.$testServer.url");
		if ($testServer == 'saucelabs') {
			$username = Config::read('testserver.saucelabs.username');
			$key = Config::read('testserver.saucelabs.key');
			$serverUrl = "http://$username:$key@$serverUrl";// Default format: http://passbolt:688b92b6-6d74-40b9-9d03-15b97124a666@ondemand.saucelabs.com:80/wd/hub
		}
		return $serverUrl;
	}

    /**
     * This function is executed after a test is run
     */
    protected function tearDown() {

        if(isset($this->driver)) {
            if($this->_quit === '0') {
                return;
            } else if(empty($this->_quit) || $this->_quit === '1') {
	            self::logFile("> Driver Quit (" . $this->testName . ")");
                $this->driver->quit();
            } else if($this->_quit === '2' && isset($this->_failing) && !$this->_failing) {
	            self::logFile("> Driver Quit (" . $this->testName . ")");
                $this->driver->quit();
            }
        }

	    // If test was running on saucelabs, we update the status.
	    if ($this->_saucelabs) {
		    $this->updateTestStatus();
	    }

	    // Display logs if any.
	    if(isset($this->_log) && !empty($this->_log)) {
		    echo "\n\n"
			    . "=== Webdriver Test Case Log ===" . "\n"
			    . $this->_log;
	    }

	    // Release instance.
	    if ($this->__useMultiplePassboltInstances()) {
		    self::releaseInstance('passbolt');
		    self::logFile("> Released passbolt instance: " . Config::read('passbolt.url') . " (" . $this->testName . ")");
	    }
	    if ($this->__useMultipleSeleniumInstances()) {
		    self::releaseInstance('selenium');
		    self::logFile("> Released selenium instance: " . Config::read('testserver.selenium.url') . " (" . $this->testName . ")");
	    }
    }

	/**
	 * Update test status on saucelabs.
	 */
	public function updateTestStatus() {
		if (isset($this->sauceLabJob) && is_array($this->sauceLabJob)) {
			$this->sauceAPI->updateJob(
				$this->sauceLabJob['id'],
				array('passed' => $this->hasFailed() ? false : true)
			);
		}
	}

	/**
	 * Init instances in DB.
	 * Create database and table if not there, and populate initial data.
	 * @param string $type
	 */
	private function __initDbInstances($type = 'passbolt') {
		$db = new SQLite3(ROOT . DS . 'tmp' . DS . 'instances.db');
		$db->busyTimeout(1000);

		// Check if table exists.
		$result = $db->query("SELECT count(*) AS exist FROM sqlite_master WHERE type='table' AND name='instances';")->fetchArray();
		$tableExists = $result['exist'] == '1';
		// If table doesn't exist, create it and populate it with instances.
		if (!$tableExists) {
			self::logFile("####### Create table");
			$db->query( 'CREATE TABLE instances (id INTEGER PRIMARY KEY, type varchar(10), address varchar(255), locked INTEGER)' );

			$db->exec('BEGIN;');
			$instancesToSave = [];
			// Insert rows in DB for instances, as per config.
			$instancesConfig = $type == 'passbolt' ? Config::read('passbolt.instances') : Config::read('testserver.selenium.instances');
			foreach ($instancesConfig as $instance) {
				$instancesToSave[] = ['type' => $type, 'address' => $instance, 'locked' => 0];
			}

			// If there is a difference, we reinitialize db with config data.
			$this->__resetDbInstances($type, $instancesToSave, false);
			$db->exec('COMMIT;');
		}
		$db->close();
	}

	/**
	 * Reset instances in DB with instances given.
	 * @param      $type
	 * @param      $instances
	 * @param bool $atomic
	 */
	private function __resetDbInstances($type, $instances, $atomic = true) {
		$db = new SQLite3(ROOT . DS . 'tmp' . DS . 'instances.db');
		$db->busyTimeout(1000);
		if ($atomic) {
			$db->exec('BEGIN;');
		}
		self::logFile("!!!!!!!! Reset DB instances");
		$db->query("DELETE FROM instances WHERE type='{$type}'");

		foreach ($instances as $instance) {
			$db->query( "INSERT INTO instances (id, type, address, locked) VALUES(NULL, '$type', '{$instance['address']}', {$instance['locked']});" );
		}
		if ($atomic) {
			$db->exec('COMMIT;');
		}
		$db->close();
	}

	private function __useMultipleSeleniumInstances() {
		$isSelenium = Config::read('testserver.default') == 'selenium';
		$seleniumInstances = Config::read('testserver.selenium.instances');
		return $isSelenium && !empty($seleniumInstances);
	}

	private function __useMultiplePassboltInstances() {
		$passboltInstances = Config::read('passbolt.instances');
		return !empty($passboltInstances);
	}

	private function __syncDbInstancesWithConfig($type) {
		$db = new SQLite3(ROOT . DS . 'tmp' . DS . 'instances.db');
		$db->busyTimeout(1000);

		$instancesConfig = [];
		if ($this->__useMultiplePassboltInstances()) {
			$instancesConfig['passbolt'] = Config::read('passbolt.instances');
		}

		// If we have multiple selenium db instances.
		if ($this->__useMultipleSeleniumInstances()) {
			$instancesConfig['selenium'] = Config::read('testserver.selenium.instances');
		}

		if (empty($instancesConfig)) {
			return;
		}

		foreach ($instancesConfig as $type => $instanceConfig) {
			// Get instances from DB.
			$res = $db->query("SELECT * FROM instances WHERE type='$type'");
			$instancesDb = [];
			while($instance = $res->fetchArray()) {
				$instancesDb[] = $instance['address'];
			}

			// Compare instances in DB, and instances in config.
			// If they are different, reset Db with config values.
			$sameInstances = sizeof($instancesDb) == sizeof($instancesConfig)
				&& count(array_diff($instancesConfig, $instancesDb)) == 0;
			if (!$sameInstances) {
				$instancesToSave = [];

				foreach($instanceConfig as $instanceAddress ) {
					$instancesToSave[] = ['type' => $type, 'address' => $instanceAddress, 'locked' => 0];
				}
				$this->__resetDbInstances($type, $instancesToSave, true);
			}
		}
		$db->close();
	}

	public function reserveInstance($type = 'passbolt') {
		$db = new SQLite3(ROOT . DS . 'tmp' . DS . 'instances.db');
		$db->busyTimeout(1000);
		if ($db === FALSE) {
			throw new Exception('SQLite: Could not open instance database');
		}

		// Init instances in DB. Set table and entries if not there.
		$this->__initDbInstances($type);
		//  Make sure that DB instances match the ones provided in config //
		$this->__syncDbInstancesWithConfig($type);

		// Get Free instance.
		$db->exec('BEGIN;');
		$debugFreeInstances = $db->query( "SELECT * FROM instances WHERE type='$type' AND locked=0" );
		while ($debugFreeInstance = $debugFreeInstances->fetchArray()) {
			self::logFile(">>>> free $type instance {$debugFreeInstance['address']}");
		}

		$freeInstance = $db->query( "SELECT * FROM instances WHERE type='$type' AND locked=0" )->fetchArray();
		if (empty($freeInstance)) {
			throw new Exception('could not find an available instance');
		}
		// Lock free instance.
		$db->query( "UPDATE  instances SET locked=1 WHERE id={$freeInstance['id']}" );
		$db->exec('COMMIT;');

		$debugFreeInstances = $db->query( "SELECT * FROM instances WHERE type='$type' AND locked=0" );
		while ($debugFreeInstance = $debugFreeInstances->fetchArray()) {
			self::logFile(">>>> free instance {$debugFreeInstance['address']}");
		}

		$db->close();

		if ($type == 'passbolt') {
			// Write instance url in config.
			self::logFile(">>>> selected $type {$freeInstance['address']}");
			Config::write('passbolt.url', $freeInstance['address']);
		}
		elseif ($type == 'selenium') {
			self::logFile(">>>> selected $type {$freeInstance['address']}");
			Config::write('testserver.selenium.url', $freeInstance['address']);
		}

		// Return it.
		return $freeInstance['address'];
	}

	/**
	 * Release an instance. (in case of parallelization).
	 */
	public static function releaseInstance($type) {
		$db = new SQLite3(ROOT . DS . 'tmp' . DS . 'instances.db');
		$db->busyTimeout(1000);

		if ($type == 'passbolt') {
			$url = Config::read('passbolt.url');
		}
		elseif ($type == 'selenium') {
			$url = Config::read('testserver.selenium.url');
		}
		$db->query( "UPDATE instances SET locked=0 WHERE address='{$url}' and type='$type'" );
		$db->close();
	}

	/**
	 * Assert post conditions.
	 */
    protected function assertPostConditions() {
        $this->_failing = false;
        parent::assertPostConditions();
    }

    /********************************************************************************
     * Protected methods
     ********************************************************************************/
    /**
     * Get desired capabilities from config
     * @return DesiredCapabilities|null
     * @throws error browser type not supported
     */
    public function _getCapabilities() {
        $capabilities = null;

        switch($this->_browser['type']) {

            default:
                $this->_error('ERROR Sorry this browser type is not supported.');
            break;

            case 'firefox':
                $profile = new FirefoxProfile();
                $capabilities = DesiredCapabilities::firefox();

                if (isset($this->_browser['extensions'])) {
                    foreach($this->_browser['extensions'] as $i => $ext_path) {
                        if (!is_file($ext_path)) {
                            $this->_error('ERROR The extension file was not found: ' . $ext_path);
                        }
                        $profile->addExtension($ext_path);
                    }
                }

                // Set download preferences for the browser.
                $profile->setPreference("browser.download.folderList", 2);
                $profile->setPreference("browser.download.dir", SELENIUM_TMP);
				$profile->setPreference("xpinstall.signatures.required", false);

                $capabilities->setCapability(FirefoxDriver::PROFILE, $profile);
            break;

            case 'chrome':
                $options = new ChromeOptions();
                $capabilities = DesiredCapabilities::chrome();
                if (isset($browser['extensions'])) {
                    $options->addExtensions($this->_browser['extensions']);
                }
                $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
            break;

        }
        return $capabilities;
    }

    /**
     * Check and get the browser config
     * @throws error No browser defined
     * @throws error No browser config found
     */
    private function _setBrowserConfig() {
        $browser = getenv('BROWSER');

        // Sanity checks
        if(empty($browser)) {
            $browser = Config::read('browsers.default');
        }
        if(!isset($browser)) {
            $this->_error('ERROR No browser defined either in testsuite or config.');
        }
        $browsers = Config::read('browsers');
        if(!isset($browsers) || !isset($browsers[$browser])) {
            $this->_error('ERROR No browser config found for: ' . $browser);
        }
        $this->_browser = $browsers[$browser];
    }

    /**
     * Check selenium config
     * @throws error No selenium config
     */
    private function _checkSeleniumConfig() {
        $s = Config::read('testserver');
	    $default = Config::read('testserver.default');
	    $url = Config::read("testserver.$default.url");
        if(!isset($s) || !isset($url)) {
            $this->_error('ERROR No testserver configuration found.');
        }
    }

    /**
     * We need a special method to handle configuration error that stops execution
     * since exceptions are catched in a phpunit context
     * @param $msg
     */
    private function _error($msg) {
        echo $msg . "\n";
        $this->tearDown();
        exit;
    }

    /**
     * Set verbose from config
     */
    private function _setVerbose() {
        $this->_verbose = getenv('VERBOSE');

        // Default is false
        if(empty($this->_verbose)) {
            $this->_verbose = 0;
        }
    }

    /********************************************************************************
     * Debug HELPERS
     ********************************************************************************/
    /**
     * Log a message if verbose is set
     * @param $msg
     */
    public function log($msg) {
        if($this->_verbose) {
            $this->_log .= $msg . "\n";
        }
    }

    /********************************************************************************
     * Driver HELPERS
     ********************************************************************************/
    /**
     * Input some text in an element
     * @param $id string an element id or selector
     * @param $txt the text to be typed on keyboard
     * @param $append boolean true if you want to keep the current value intact
     */
    public function inputText($id, $txt, $append=false) {
        $input = $this->find($id);
        $input->click();
        if(!$append) {
            $input->clear();
        }
		$input->sendKeys($txt);
    }

    /**
     * Emulate escape key press
     */
    public function pressEscape() {
        $this->driver->getKeyboard()->sendKeys(WebDriverKeys::ESCAPE);
    }

    /**
     * Check the checkbox with given id
     * @param $id
     */
    public function checkCheckbox($id) {
        $input = $this->find($id);
        $input->click();
    }

    /**
     * Press enter on keyboard
     */
    public function pressEnter() {
        $this->driver->getKeyboard()->pressKey(WebDriverKeys::ENTER);
    }

    /**
     * Press tab key
     */
    public function pressTab() {
        $this->driver->getKeyboard()->pressKey(WebDriverKeys::TAB);
    }

    /**
     * Press backtab key
     */
    public function pressBacktab() {
        $this->driver->getKeyboard()
            ->sendKeys([WebDriverKeys::SHIFT, WebDriverKeys::TAB]);
    }

    /**
     * A generic find, try by id, then css
     * @param mixed $selector Element or selector string
	 * @return Object
     */
    public function find($selector) {
		$element = null;

		// If the given selector is already an element.
		if (is_object($selector)) {
			return $selector;
		}

		// Could the selector be an identifier
		$matches = [];
		if (preg_match('/^[#]?([^\.\s]*)$/', $selector, $matches)) {
			try {
				$id = $matches[1];
				$element = $this->driver->findElement(WebDriverBy::id($id));
			} catch (Exception $e) {
				// error treated later
			}
		}

		// If the element selector looked liked an id but wasn't found
		// or was not an id like, try to search by css
		if (is_null($element)) {
			try {
				$element = $this->driver->findElement(WebDriverBy::cssSelector($selector));
			} catch (Exception $e) {
				// error treated later
			}
		}

		if (is_null($element)) {
			$this->fail('Cannot find element: ' . $selector);
		}

        return $element;
    }

    /**
     * Find an element by a CSS selector
     * @param $css
     * @return mixed
     * @throws NoSuchElementException
     */
    public function findByCss($css) {
        return $this->driver->findElement(WebDriverBy::cssSelector($css));
    }

    /**
     * Find an element by a XPath selector
     * @param $xpath
     * @return mixed
     * @throws NoSuchElementException
     */
    public function findByXpath($xpath) {
        return $this->driver->findElement(WebDriverBy::xpath($xpath));
    }

	/**
	 * Find all elements by a XPath selector
	 * @param $text
	 * @return mixed
	 * @throws NoSuchElementException
	 */
	public function findAllByXpath($xpath) {
		return $this->driver->findElements(WebDriverBy::xpath($xpath));
	}

	/**
	 * Find an element by ID
	 * @param $id
	 * @return mixed
	 * @throws NoSuchElementException
	 */
	public function findById($id) {
		return $this->driver->findElement(WebDriverBy::id($id));
	}

    /**
     * Find a link by its text
     * @param $text
     * @return mixed
     * @throws NoSuchElementException
     */
    public function findLinkByText($text) {
        return $this->driver->findElement(WebDriverBy::linkText($text));
    }

    /**
     * Follow a link url defined by a css selector. (Doesn't click on it).
     * This prevents opening the url in another tab in case of target="_blank"
     * @param $text
     *
     * @return mixed
     * @throws NoSuchElementException
     */
    public function followLink($text) {
        $linkElement = $this->findLinkByText($text);
        $url = $linkElement->getAttribute('href');
        $this->driver->get($url);
    }

    /**
     * Click on a link element defined by a text.
     * This prevents opening the url in another tab in case of target="_blank"
     * @param $text
     *
     * @return mixed
     * @throws NoSuchElementException
     */
    public function clickLink($text) {
        $linkElement = $this->findLinkByText($text);
        $linkElement->click();
    }

    /**
     * Click on an element defined by its Id or CSS selector
     * @param $selector selector|element
	 * @throw NoSuchElementException
     */
    public function click($selector) {
		$elt = $this->find($selector);
		$elt->click();
    }

    /**
     * Click on a non significant element to release the focus
     */
    public function releaseFocus() {
        $elt = $this->find('.header.second');
        $elt->click();
    }

    /**
     * Right click on something
     * @param $id
     */
    public function rightClick($id) {
        $action = new WebDriverActions($this->driver);
        $element = $this->find($id);
        $action->contextClick($element)->perform();
    }

    /**
     * Tell if an element is visible
     * @param $id | selector
     * @return boolean
     */
    public function isVisible($id) {
		$element = null;
	    try {
		    $element = $this->find($id);
	    } catch (Exception $e) {

	    }
        return (!is_null($element) && $element->isDisplayed());
    }

    /**
     * Check if an element has a given class name
     * @param $elt
     * @param $className
     * @return bool
     */
    public function elementHasClass($elt, $className) {
        if(!is_object($elt)) {
            $elt = $this->find($elt);
        }
        $eltClasses = $elt->getAttribute('class');
        $eltClasses = explode(' ', $eltClasses);
        if(in_array($className, $eltClasses)) {
            return true;
        }
        return false;
    }

    /**
     * Tell if an element is not visible or not present
     * @param $id
     * @return boolean
     */
    public function isNotVisible($id) {
        try {
            $element = $this->driver->findElement(WebDriverBy::id($id));
        } catch (NoSuchElementException $e) {
            try {
                $element = $this->driver->findElement(WebDriverBy::cssSelector($id));
            } catch (exception $e) {
               return true; // not found == not visible
            }
        }
        return (!$element->isDisplayed());
    }

    /**
     * Wait until I see.
     * @param $id string
     * @param null $regexp
     * @param int timeout timeout in seconds
     * @return bool if element is found
     * @throws Exception if element is not found after a given timeout
     */
    public function waitUntilISee($id, $regexp = null, $timeout = 10) {
        for ($i = 0; $i < $timeout * 10; $i++) {
            try {
                $elt = $this->find($id);
                if ($elt && $elt->isDisplayed()) {
                    if (is_null($regexp)) {
                        return true;
                    }
                    else {
                        if (preg_match($regexp, $elt->getText())) {
                            return true;
                        }
                    }
                }
            }
            catch (Exception $e) {
                // We do nothing
            }
            usleep(100000); // Sleep 1/2 seconds
        }
        $backtrace = debug_backtrace();
        throw new Exception( "waitUntilISee $id, $regexp : Timeout thrown by " . $backtrace[1]['class'] . "::" . $backtrace[1]['function'] . "()\n . element: $id ($regexp)");
    }

	/**
	 * Wait until I don't see an element, or an element containing a given text.
	 * @param      $id
	 * @param null $regexp
	 * @param int  $timeout
	 *
	 * @throws Exception
	 */
	public function waitUntilIDontSee($id, $regexp = null, $timeout = 10) {
		for ($i = 0; $i < $timeout * 10; $i++) {
			// Try to find the element. If not found, return true.
			try {
				$elt = $this->find( $id );
			}
			catch (Exception $e) {
				// Element was not found, we return true.
				return true;
			}

			try {
				// Element is found, but is not visible, return true.
				if (!$elt->isDisplayed()) {
					return true;
				}
				// Else if element is visible, and a regexp is provided, test if the content match the regexp.
				elseif ($regexp != null && !preg_match($regexp, $elt->getText())) {
					return true;
				}
			}
			catch(Exception $e) {
				return true;
			}


			// If none of the above was found, wait for 1/10 seconds, and try again.
			usleep(100000); // Sleep 1/10 seconds
		}

		$backtrace = debug_backtrace();
		throw new Exception( "waitUntilIDontSee $id, $regexp : Timeout thrown by " . $backtrace[1]['class'] . "::" . $backtrace[1]['function'] . "()\n . element: $id ($regexp)");
	}

	/**
	 * Select an option in a select list
	 * @param $id string an element id or selector
	 * @param $option string the label of the option
	 */
	public function selectOption($id, $option) {
		$select = new WebDriverSelect($this->driver->findElement(WebDriverBy::id($id)));
		$select->selectByVisibleText($option);
	}


	/**
	 * Scroll an element to its bottom.
	 * @param $idSelector
	 */
	public function scrollElementToBottom($idSelector) {
		$script = "
		var objDiv = document.getElementById('$idSelector');
		objDiv.scrollTop = objDiv.scrollHeight;
		";
		$this->driver->executeScript($script);
	}

    /********************************************************************************
     * ASSERT HELPERS
     ********************************************************************************/
    /**
     * Check if the given title is contain in the one of the page
     * @param $title
     */
    public function assertTitleContain($title) {
        $t = $this->driver->getTitle();
        $this->assertContains($title,$t);
    }

    /**
     * Check if the current url match the regexp given in parameter
     * @param $regexp
     */
    public function assertUrlMatch($regexp) {
        $url = $this->driver->getCurrentURL();
        $match = preg_match($regexp, $url);
        $this->assertTrue($match >= 1, sprintf("Failed asserting that url %s matches with %s", $url, $regexp));
    }

    /**
     * Check if the page contains the given text
     * @param $text
     */
    public function assertPageContainsText($text) {
        $source = $this->driver->getPageSource();
        $strippedSource = strip_tags($source);
        $contains = strpos($strippedSource, $text) !== false;
        $this->assertTrue($contains, sprintf("Failed asserting that page contains '%s'", $text));
    }

	/**
	 * Check if Meta title contains the given title.
	 * @param $title
	 */
		public function assertMetaTitleContains($title) {
			$source = $this->driver->getPageSource();
			$contains = preg_match("/<title>$title<\\/title>/", $source);
			$this->assertTrue($contains == 1, sprintf("Failed asserting that meta title contains '%s'", $title));
		}

    /**
     * Assert if the page contains the given element
     * @param $cssSelector
     */
    public function assertPageContainsElement($cssSelector) {
        // todo find by id first
        try {
            $this->findByCss($cssSelector);
        } catch (NoSuchElementException $e) {
            $this->fail(sprintf("Failed asserting that the page contains the element %s", $cssSelector));
        }
    }

    /**
     * Assert if a given element contains a given text
     * @param mixed $elt the WebDriverElement or csselector string
     * @param $needle
     */
    public function assertElementContainsText($elt, $needle) {
        if(!is_object($elt)) {
            $elt = $this->find($elt);
        }
        $eltText = $elt->getText();
        if(preg_match('/^\/.+\/[a-z]*$/i', $needle)) {
            $contains = preg_match($needle, $eltText) != false;
        } else {
            $contains = strpos($eltText, $needle) !== false;
        }
        $this->assertTrue($contains,
            sprintf("Failed asserting that element contains '%s' '%s' found instead", $needle, $eltText));
    }

    /**
     * Assert if a given element does not contain a given text
     * @param $elt
     * @param $needle
     */
    public function assertElementNotContainText($elt, $needle) {
        $eltText = $elt->getText();
        $contains = strpos($eltText, $needle) !== false;
        $this->assertFalse($contains, sprintf("Failed asserting that element does not contain '%s'", $needle));
    }

    /**
     * Assert if an element has a given class name
     * @param $elt
     * @param $className
     */
    public function assertElementHasClass($elt, $className) {
        $contains = $this->elementHasClass($elt, $className);
        $this->assertTrue($contains, sprintf("Failed asserting that element has class '%s'", $className));
    }

    /**
     * Assert that an element's attribute is equal to the one given.
     * @param $elt
     * @param $attribute
     * @param $value
     */
    public function assertElementAttributeEquals($elt, $attribute, $value) {
        $attr = $elt->getAttribute($attribute);
        $this->assertEquals($attr, $value, sprintf("Failed asserting that element attribute %s equals %s", $attribute, $value));
    }

    /**
     * Assert if an element has a given class name
     * @param $elt
     * @param $className
     */
    public function assertElementHasNotClass($elt, $className) {
        $eltClasses = $elt->getAttribute('class');
        $eltClasses = explode(' ', $eltClasses);
        $contains = in_array($className, $eltClasses);
        $this->assertFalse($contains, sprintf("Failed asserting that element has not the class '%s'", $className));
    }

    /**
     * Assert if an element has the focus.
     * @param $id
     */
    public function assertElementHasFocus($id) {
        $activeElt = $this->driver->switchTo()->activeElement();
        $activeId = $activeElt->getAttribute('id');
        $this->assertEquals($id, $activeId);
    }

    /**
     * Assert if an element identified via its id is visible
     * @param $id
     */
    public function assertVisible($id) {
        $this->assertTrue(
            $this->isVisible($id),
            'Failed to assert that the element ' . $id .' is visible'
        );
    }

    /**
     * Assert if an element identified by its id is not visible or not present
     * @param $id
     */
    public function assertNotVisible($id) {
        $this->assertTrue(
            $this->isNotVisible($id),
            'Failed to assert that the element ' . $id .' is not visible'
        );
    }

    /**
     * @param $id string id or css path
     * @param $value
     */
    public function assertInputValue($id, $value) {
        $el = $this->find($id);
        $this->assertTrue(
            ($el->getAttribute('value') == $value),
            'Failed to assert that the input: '.$id .', match value: '. $value
        );
    }

    /**
     * Assert if an input is disabled
     * @param $id
     */
    public function assertDisabled($id) {
        $a = $this->find($id)->getAttribute('disabled');
        $this->assertTrue(($a === 'true'), 'Failed to assert the element '.$id . 'is disabled');
    }
}
