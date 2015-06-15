<?php
/**
 * Web Driver Test Case
 * The base class for test cases.
 *
 * @copyright 	(c) 2015-present Bolt Software Pvt. Ltd.
 * @licence			GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
 */
class WebDriverTestCase extends PHPUnit_Framework_TestCase {

  public $driver; 			// @var RemoteWebDriver $driver
	protected $_browser;
	protected $_verbose;
  protected $_log;

	/********************************************************************************
	 * Pre/Post Tests Execution Callback
	 ********************************************************************************/
	/**
	 * This function is executed before a test is run
	 * It setup the capabilities and browser driver
	 */
  protected function setUp() {
		$this->_setVerbose();
		$this->_setBrowserConfig();
		$this->_checkSeleniumConfig();
		$capabilities = $this->_getCapabilities();
		$this->driver = RemoteWebDriver::create(Config::read('selenium.url'), $capabilities);
  }

	/**
	 * This function is executed after a test is run
	 */
  protected function tearDown() {
		if(isset($this->_log) && !empty($this->_log)) {
			echo "\n\n"
				. "=== Webdriver Test Case Log ===" . "\n"
				. $this->_log;
		}
		if(isset($this->driver)) {
			$quit = getenv('QUIT');
			if($quit === '0') {
				return;
			} else {
				$this->driver->quit();
			}
		}
  }

	/********************************************************************************
	 * Protected methods
	 ********************************************************************************/
	/**
	 * Get desired capabilities from config
	 * @return DesiredCapabilities|null
	 * @throws error browser type not supported
	 */
	private function _getCapabilities() {
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
		$s = Config::read('selenium.url');
		if(!isset($s)) {
			$this->_error('ERROR No selenium configuration found.');
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
	 * @param $id
	 * @param $txt
	 */
	public function inputText($id, $txt) {
		$input = $this->driver->findElement(WebDriverBy::id($id));
		$input->click();
		$this->driver->getKeyboard()->sendKeys($txt);
	}

	/**
	 * Empty input element.
	 * @param $id
	 */
	public function emptyInput($id) {
		$this->driver->executeScript("document.getElementById('$id').value = ''", array());
	}

	/**
	 * Check the checkbox with given id
	 * @param $id
	 */
	public function checkCheckbox($id) {
		$input = $this->driver->findElement(WebDriverBy::id($id));
		$input->click();
	}

	/**
	 * Press enter on keyboard
	 */
	public function pressEnter() {
		$this->driver->getKeyboard()->pressKey(WebDriverKeys::ENTER);
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
	 * Click on an element defined by a css selector.
	 * This prevents opening the url in another tab in case of target="_blank"
	 * @param $cssSelector
	 *
	 * @return mixed
	 * @throws NoSuchElementException
	 */
	public function clickElement($cssSelector) {
		$elt = $this->findByCss($cssSelector);
		$elt->click();
	}

	/**
	 * Click on an element defined by its Id
	 * @param $id
	 */
	public function click($id) {
		try {
			$e = $this->driver->findElement(WebDriverBy::id($id));
			$e->click();
		} catch (NoSuchElementException $e) {
			$this->fail('Cannot click on not found element: ' . $id);
		}
	}


	/**
	 * Wait until I see.
	 * @param      $cssSelector
	 * @param null $regexp
	 * @param int timeout timeout in seconds
	 *
	 * @throws Exception
	 */
	public function waitUntilISee($cssSelector, $regexp = null, $timeout = 10) {
		$ex = null;
		for ($i = 0; $i < $timeout * 10; $i++) {
			try {
				$elt = $this->findByCss($cssSelector);
				if ($elt) {
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
				$ex = $e;
			}
			usleep(100000); // Sleep 1/10 seconds
		}
		$backtrace = debug_backtrace();
		throw new Exception( "Timeout thrown by " . $backtrace[1]['class'] . "::" . $backtrace[1]['function'] . "()\n . element: $cssSelector ($regexp)");
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
	 * Assert if the page contains the given element
	 * @param $cssSelector
	 */
	public function assertPageContainsElement($cssSelector) {
		try {
			$this->findByCss($cssSelector);
		} catch (NoSuchElementException $e) {
			$this->fail(sprintf("Failed asserting that the page contains the element %s", $cssSelector));
		}
	}

	/**
	 * Assert if a given element contains a given text
	 * @param $elt
	 * @param $needle
	 */
	public function assertElementContainsText($elt, $needle) {
		$eltText = $elt->getText();
		$contains = strpos($eltText, $needle) !== false;
		$this->assertTrue($contains, sprintf("Failed asserting that element contains '%s'", $needle));
	}

	/**
	 * Assert if an element has a given class name
	 * @param $elt
	 * @param $className
	 */
	public function assertElementHasClass($elt, $className) {
		$eltClasses = $elt->getAttribute('class');
		$eltClasses = explode(' ', $eltClasses);
		$contains = in_array($className, $eltClasses);
		$this->assertTrue($contains, sprintf("Failed asserting that element has class '%s'", $className));
	}

	/**
	 * Assert that an element's attribute is equal to the one given.
	 * @param $elt
	 * @param $attribute
	 * @param $text
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

}
