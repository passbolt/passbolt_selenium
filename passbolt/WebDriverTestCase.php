<?php
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
	protected $_build;

	// Name of the current test.
	public $testName;

	protected $_saucelabs; // boolean

	// Saucelab job.
	protected $_sauceAPI;
	protected $_sauceLabJob;

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

	    // Get Build ID.
	    // Set it if not provided.
	    $build = getenv('BUILD');
	    if (!$build) {
		    $build = time();
		    putenv("BUILD=$build");
	    }
	    $this->_build =$build;


	    $this->_saucelabs = Config::read('testserver.default') == 'saucelabs' ? true : false;

	    // Reserve instances for passbolt and selenium.
	    if ($this->__useMultiplePassboltInstances()) {
		    $this->reserveInstance('passbolt');
	    }
	    if ($this->__useMultipleSeleniumInstances()) {
		    $this->reserveInstance('selenium');
	    }

	    // Init browser.
		$this->initBrowser();

	    // Maximize window.
	    $this->driver->manage()->window()->maximize();

	    // SauceAPI.
	    if ($this->_saucelabs) {
		    $this->_sauceAPI = new Sauce\Sausage\SauceAPI(Config::read('testserver.saucelabs.username'), Config::read('testserver.saucelabs.key'));
		    $this->_sauceLabJob = $this->getSauceLabJob();
	    }
    }

	/**
	 * Get saucelab job corresponding to current test through rest API.
	 * @return array Sauce lab job object.
	 */
	public function getSauceLabJob() {
		$jobs = $this->_sauceAPI->getJobs(0, null, 10)['jobs'];
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

		if ($this->_saucelabs) {

			// Set build name.
			$capabilities->setCapability('build', $this->_build);

			// Set test name.
			$capabilities->setCapability('name', $this->testName);

			// Define specific saucelab capabilities.
			$sauceLabCapabilities = Config::read('testserver.saucelabs.capabilities');
			if (!empty($sauceLabCapabilities)) {
				foreach($sauceLabCapabilities as $capabilityName => $capabilityValue) {
					$capabilities->setCapability($capabilityName, $capabilityValue);
				}
			}
		}

		// Build end point url.
		$serverUrl = $this->getTestServerUrl();
		$this->driver = RemoteWebDriver::create($serverUrl, $capabilities, 120000, 120000);

		// Redirect it immediately to an empty page, so we avoid the default firefox home page.
		if ($this->_browser['type'] == 'firefox') {
			$this->driver->get('');
		}
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
                $this->driver->quit();
            } else if($this->_quit === '2' && isset($this->_failing) && !$this->_failing) {
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
		    $this->releaseInstance('passbolt');
	    }
	    if ($this->__useMultipleSeleniumInstances()) {
		    $this->releaseInstance('selenium');
	    }
    }

	/**
	 * Update test status on saucelabs.
	 */
	public function updateTestStatus() {
		if (isset($this->_sauceLabJob) && is_array($this->_sauceLabJob)) {
			$this->_sauceAPI->updateJob(
				$this->_sauceLabJob['id'],
				array('passed' => $this->hasFailed() ? false : true)
			);
		}
	}

	private function __openSqlite3Db($flags = null) {
		$pathToDb = ROOT . DS . 'tmp' . DS . 'instances.db';

		if ($flags == null) {
			$db = new SQLite3($pathToDb);
		}
		else {
			$db = new SQLite3($pathToDb, $flags);
		}
		$db->busyTimeout(5000);

		if ($db === FALSE) {
			throw new Exception('SQLite: Could not open instance database');
		}

		return $db;
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

	public function reserveInstance($type = 'passbolt') {

		$db = $this->__openSqlite3Db(SQLITE3_OPEN_READWRITE);

		// Lock free instance.
		$lockId = rand(1, 10000);
		$db->exec( "
			UPDATE  instances SET locked=$lockId
			WHERE id=(
				SELECT id FROM instances WHERE type='$type' AND locked=0 ORDER BY id LIMIT 1
			)" );

		$freeInstance = $db->query( "SELECT * FROM instances WHERE type='$type' AND locked=$lockId" )->fetchArray();
		if (empty($freeInstance)) {
			throw new Exception('could not retrieve the free instance');
		}

		$db->close();

		if ($type == 'passbolt') {
			// Write instance url in config.
			Config::write('passbolt.url', $freeInstance['address']);
		}
		elseif ($type == 'selenium') {
			Config::write('testserver.selenium.url', $freeInstance['address']);
		}

		// Return it.
		return $freeInstance['address'];
	}

	/**
	 * Release an instance. (in case of parallelization).
	 */
	public function releaseInstance($type) {
		$db = $this->__openSqlite3Db();

		if ($type == 'passbolt') {
			$url = Config::read('passbolt.url');
		}
		elseif ($type == 'selenium') {
			$url = Config::read('testserver.selenium.url');
		}
		$db->exec( "UPDATE instances SET locked=0 WHERE address='{$url}' and type='$type'" );
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
				$profile->setPreference("xpinstall.signatures.required", false);

                $capabilities->setCapability(FirefoxDriver::PROFILE, $profile);
            break;

            case 'chrome':
                $options = new ChromeOptions();
                $capabilities = DesiredCapabilities::chrome();

			    if (isset($this->_browser['extensions'])) {
			        foreach($this->_browser['extensions'] as $i => $ext_path) {
			            if (!is_file($ext_path)) {
				            $this->_error('ERROR The extension file was not found: ' . $ext_path);
			            }
			            $options->addExtensions($this->_browser['extensions']);
			        }
			    }

				// TODO: set options for auto-download if required. (check firefox preferences above).

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
	 * Wait until the callback function validates.
	 * @param Callback $callback The function that will do the assertion
	 * @param array $args An array of arguments to pass the callback function
	 * @param int $timeout
	 * @return bool
	 * @throws
	 */
	public function waitUntil($callback, $args = array(), $timeout = 10) {
		// Number of loops to do.
		$loops = 50;
		// The last exception caught.
		$caughtException = null;

		for ($i = 0; $i < $loops; $i++) {
			try {
				call_user_func_array($callback, $args);
				return true;
			} catch (Exception $e) {
				$caughtException = $e;
			}
			$second = 1000000;
			usleep(($second * $timeout) / $loops);
		}
		throw $caughtException;
	}

    /**
     * Wait until I see.
     * @param $ids array of ids (success if only one of them is found), or string representing one id
     * @param array or string $regexps (follows $ids)
     * @param int timeout timeout in seconds
     * @return bool if element is found
     * @throws Exception if element is not found after a given timeout
     */
    public function waitUntilISee($ids, $regexps = null, $timeout = 10) {
        // Number of loops to do.
	    $loops = 50;

	    for ($i = 0; $i < $loops; $i++) {
	        if (is_array($ids)) {
		        foreach($ids as $k => $id) {
			        $regexp = null;
			        if (!is_null($regexps) && is_string($regexps)) {
						$regexp = $regexps;
			        }
			        elseif (!is_null($regexps) && is_array($regexps)) {
				        $regexp = $regexps[$k];
			        }
			        $visible = $this->_assertISeeElement($id, $regexp);
			        if ($visible === true) {
				        return true;
			        }
		        }
	        }
	        else {
		        $visible = $this->_assertISeeElement($ids, $regexps);
		        if ($visible === true) {
			        return true;
		        }
	        }
	        $second = 1000000;
	        usleep(($second * $timeout) / $loops);
        }
        $backtrace = debug_backtrace();
	    $id = is_array($ids) ? implode(",", $ids) : $ids;
	    $regexp = is_array($regexps) ? implode (",", $regexps) : $regexps;
        throw new Exception( "waitUntilISee $id, $regexp\nTimeout thrown by " . $backtrace[1]['class'] . "::" . $backtrace[1]['function'] . "()\n . element(s): $id ($regexp)");
    }

	/**
	 * Wait until the expired dialog appears.
	 * @throws Exception
	 */
	public function assertSessionExpiredDialog() {
		// Assert I can see the confirm dialog.
		$this->waitUntilISee('.session-expired-dialog', null, 120);
		// Then I can see the close dialog button
		$this->assertNotVisible('.session-expired-dialog a.dialog-close');
		// Then I can see the cancel link.
		$this->assertNotVisible('.session-expired-dialog a.cancel');
		// Then I can see the Ok button.
		$this->assertVisible('.session-expired-dialog input#confirm-button');
		// Then I can see the title
		$this->assertElementContainsText('.session-expired-dialog', 'Session expired');
	}

	/**
	 * Wait until I see one id, which might contain a regexp.
	 * @param $id
	 * @param $regexp
	 *
	 * @return bool
	 */
	protected function _assertISeeElement($id, $regexp) {
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

		return false;
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

	/**
	 * Open a new window.
	 * @throws Exception
	 */
	public function openNewWindow($url = '') {
		$windowsCount = sizeof($this->driver->getWindowHandles());

		// User driver keyboard to open a new tab.
		$this->waitUntilISee('body');
		$this->findByCss('body')
			->sendKeys([WebDriverKeys::CONTROL, 'n']);

		// Wait until window is opened.
		// Number of loops to do.
		$loops = 50;
		// Timeout in seconds.
		$timeout = 10;
		$i = 0;
		while (!(sizeof($this->driver->getWindowHandles()) > $windowsCount)) {
			if ($i > $loops) {
				throw new Exception("Couldn't open a new window");
			}
			$second = 1000000;
			usleep(($second * $timeout) / $loops);
			$i++;
		}

		// Switch to new window.
		$windowHandles = $this->driver->getWindowHandles();
		$this->driver->switchTo()->window($windowHandles[sizeof($windowHandles) - 1]);

		$this->getUrl($url);

		return $windowHandles[sizeof($windowHandles) - 1];
	}

	/**
	 * Switch to window.
	 * @param $windowId
	 * @throws Exception
	 *   if the window doesn't exist.
	 */
	public function switchToWindow($windowId) {
		$windowHandles = $this->driver->getWindowHandles();
		if (!isset($windowHandles[$windowId])) {
			throw new Exception("Couldn't switch to tab " . $windowId);
		}
		$this->driver->switchTo()->window($windowHandles[$windowId]);
	}

	/**
	 * Switch to next tab.
	 */
	public function switchToNextTab() {
		$this->findByCss('body')
			->sendKeys(array(WebDriverKeys::CONTROL, WebDriverKeys::PAGE_UP));
		$windowHandles = $this->driver->getWindowHandles();
		$this->driver->switchTo()->window($windowHandles[sizeof($windowHandles) - 1]);
	}

	/**
	 * Switch to previous tab.
	 */
	public function switchToPreviousTab() {
		$this->findByCss('body')
			->sendKeys(array(WebDriverKeys::CONTROL, WebDriverKeys::PAGE_DOWN));
		$windowHandles = $this->driver->getWindowHandles();
		$this->driver->switchTo()->window($windowHandles[sizeof($windowHandles) - 1]);
	}

	/**
	 * Open a new tab in browser, and go to given url.
	 */
	public function openNewTab($url = '') {
		$this->waitUntilISee('body');

		// Get initial url.
		// We will use it to know when the new tab is opened.
		$initialUrl = $this->driver->getCurrentURL();

		// User driver keyboard shortcut to open a new tab.
		$this->findByCss('body')
			->sendKeys([WebDriverKeys::CONTROL, 't']);

		// Wait until tab is opened. A new tab should have a differnt url.
		// Number of loops to do.
		$loops = 50;
		// Timeout in seconds.
		$timeout = 10;
		$i = 0;
		while ($this->driver->getCurrentURL() == $initialUrl) {
			if ($i > $loops) {
				throw new Exception("Couldn't open a new tab");
			}
			$second = 1000000;
			usleep(($second * $timeout) / $loops);
			$i++;

			// Give the focus to the new tab.
			$windowHandles = $this->driver->getWindowHandles();
			$this->driver->switchTo()->window($windowHandles[sizeof($windowHandles) - 1]);
		}

		// Get url.
		$this->getUrl($url);
	}

	/**
	 * Close the current tab.
	 */
	public function closeTab() {
		$this->findByCss('body')
			->sendKeys(array(WebDriverKeys::CONTROL, 'w'));
		$windowHandles = $this->driver->getWindowHandles();
		$this->driver->switchTo()->window($windowHandles[sizeof($windowHandles) - 1]);
	}

	/**
	 * Restore the latest closed tab.
	 */
	public function restoreTab() {
		$this->findByCss('body')
			->sendKeys(array(WebDriverKeys::SHIFT, WebDriverKeys::CONTROL, 't'));
		$windowHandles = $this->driver->getWindowHandles();
		$this->driver->switchTo()->window($windowHandles[sizeof($windowHandles) - 1]);
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
	 * Assert that an element's attribute matches the given regex.
	 * @param $elt
	 * @param $attribute
	 * @param $regex
	 */
	public function assertElementAttributeMatches($elt, $attribute, $regex) {
		$attributeValue = $elt->getAttribute($attribute);
		$this->assertRegExp($regex, $attributeValue);
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
