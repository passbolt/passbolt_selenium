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
    	$this->driver->quit();
		}
  }

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

	/**
	 * Log a message if verbose is set
	 * @param $msg
	 */
	public function log($msg) {
		if($this->_verbose) {
			$this->_log .= $msg . "\n";
		}
	}

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
	 * Click on an element defined by a css selector.
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
	 * @param $cssSelector
	 * @param $needle
	 */
	public function assertElementContainsText($cssSelector, $needle) {
		$elt = null;
		try {
			$elt = $this->findByCss($cssSelector);
		} catch (NoSuchElementException $e) {
			$this->fail(sprintf("Could not locate element %s", $cssSelector));
		}
		$eltText = $elt->getText();
		echo $eltText;
		$contains = strpos($eltText, $needle) !== false;
		$this->assertTrue($contains, sprintf("Failed asserting that element %s contains '%s'", $cssSelector, $needle));
	}

	/**
	 * Assert if an element has a given class name
	 * @param $cssSelector
	 * @param $className
	 */
	public function assertElementHasClass($cssSelector, $className) {
		$elt = null;
		try {
			$elt = $this->findByCss($cssSelector);
		} catch (NoSuchElementException $e) {
			$this->fail(sprintf("Could not locate element %s", $cssSelector));
		}
		$eltClasses = $elt->getAttribute('class');
		$eltClasses = explode(' ', $eltClasses);
		$contains = in_array($className, $eltClasses);
		$this->assertTrue($contains, sprintf("Failed asserting that element %s has class '%s'", $cssSelector, $className));
	}

	/**
	 * Assert if an element has a given class name
	 * @param $cssSelector
	 * @param $className
	 */
	public function assertElementHasNotClass($cssSelector, $className) {
		$elt = null;
		try {
			$elt = $this->findByCss($cssSelector);
		} catch (NoSuchElementException $e) {
			$this->fail(sprintf("Could not locate element %s", $cssSelector));
		}
		$eltClasses = $elt->getAttribute('class');
		$eltClasses = explode(' ', $eltClasses);
		$contains = in_array($className, $eltClasses);
		$this->assertFalse($contains, sprintf("Failed asserting that element %s has not the class '%s'", $cssSelector, $className));
	}

}
