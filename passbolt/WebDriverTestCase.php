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
 * @copyright     (c) 2015-present Bolt Software Pvt. Ltd.
 * @licence        GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
 */
class WebDriverTestCase extends PHPUnit_Framework_TestCase {

    public $driver;             // @var RemoteWebDriver $driver
    protected $_browser;
    protected $_verbose;
    protected $_log;
    protected $_quit;
    protected $_failing;

    /********************************************************************************
     * Pre/Post Tests Execution Callback
     ********************************************************************************/
    /**
     * This function is executed before a test is run
     * It setup the capabilities and browser driver
     */
    protected function setUp() {
        $this->_quit = getenv('QUIT');
        $this->_failing = null;
        $this->_setVerbose();
        $this->_setBrowserConfig();
        $this->_checkSeleniumConfig();
        $capabilities = $this->_getCapabilities();
        $this->driver = RemoteWebDriver::create(Config::read('selenium.url'), $capabilities);
        // Redirect it immediately on an empty page, so we avoid the default home page.
        $this->driver->get('');
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
            if($this->_quit === '0') {
                return;
            } else if($this->_quit === '1') {
                $this->driver->quit();
            } else if($this->_quit === '2' && isset($this->_failing) && !$this->_failing) {
                $this->driver->quit();
            }
        }

    }

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
        $this->driver->getKeyboard()->sendKeys($txt);
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
     * @param $id string id or css
     */
    public function find($id) {
        try {
            $element = $this->driver->findElement(WebDriverBy::id($id));
        } catch (NoSuchElementException $e) {
            try {
                $element = $this->driver->findElement(WebDriverBy::cssSelector($id));
            } catch (NoSuchElementException $e) {
                $this->fail('Cannot find element: ' . $id);
            }
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
     * @param $id string
	 * @throw NoSuchElementException
     */
    public function click($id) {
		try {
			$element = $this->driver->findElement(WebDriverBy::id($id));
			$element->click();
		} catch (NoSuchElementException $e) {
			$element = $this->driver->findElement(WebDriverBy::cssSelector($id));
			$element->click();
		}
    }

    /**
     * Click on a non significant element to release the focus
     */
    public function releaseFocus() {
        $elt = $this->find('.logo img');
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
            usleep(100000); // Sleep 1/10 seconds
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
        $this->driver->switchTo()->defaultContent();
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
