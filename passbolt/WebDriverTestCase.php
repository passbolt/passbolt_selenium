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

	/**
	 * This function is executed before a test is run
	 * It setup the capabilities and browser driver
	 */
  protected function setUp() {

		$this->_setBrowserConfig();
		$this->_checkSeleniumConfig();

		$capabilities = $this->_getCapabilities();
		$this->driver = RemoteWebDriver::create(Config::read('selenium.url'), $capabilities, 5000);
  }

	/**
	 * This function is executed after a test is run
	 */
  protected function tearDown() {
		if(isset($this->driver)) {
    	$this->driver->quit();
		}
  }

	/**
	 * Get desired capabilities from config
	 *
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
	 *
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
	 *
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
	 *
	 * @param $msg
	 */
	private function _error($msg) {
		echo $msg . "\n";
		$this->tearDown();
		exit;
	}
}
