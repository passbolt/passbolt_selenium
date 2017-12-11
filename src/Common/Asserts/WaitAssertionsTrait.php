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
namespace App\Common\Asserts;

use App\lib\UuidFactory;
use App\Common\Config;
use Color;
use Exception;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use PHPUnit_Framework_Assert;

trait WaitAssertionsTrait
{
    abstract public function getDriver(): RemoteWebDriver;

    /**
     * Wait until I see one id, which might contain a regexp.
     *
     * @param string $cssSelector
     * @param $regexp
     *
     * @return bool
     */
    protected function _assertISeeElement($cssSelector, $regexp)
    {
        try {
            $elt = $this->getDriver()->findElement(WebDriverBy::cssSelector($cssSelector));
            if ($elt && $elt->isDisplayed()) {
                if (is_null($regexp)) {
                    return true;
                } else {
                    if (preg_match($regexp, $elt->getText())) {
                        return true;
                    }
                }
            }
        } catch (\Exception $e) {
            // We do nothing
        }

        return false;
    }

    /**
     * Wait until all the currently operations have been completed.
     *
     * @param int timeout timeout in seconds
     * @param string $elementSelector selector
     * @return bool
     */
    public function waitCompletion($timeout = 10, $elementSelector = null) 
    {
        if (is_null($elementSelector)) {
            $elementSelector = 'html.loaded';
        }

        for ($i = 0; $i < $timeout * 10; $i++) {
            try {
                $elt = $this->getDriver()->findElement(WebDriverBy::cssSelector($elementSelector));
                if(count($elt)) {
                    return true;
                }
            }
            catch (Exception $e) {
                // Wait a bit
                usleep(100000); // Sleep 1/10 seconds
            }
        }

        $message = 'html.loaded could not be found in time';
        PHPUnit_Framework_Assert::fail($message);
    }

    /**
     * Wait until the callback function validates.
     *
     * @param Callback $callback The function that will do the assertion
     * @param array $args An array of arguments to pass the callback function
     * @param int $timeout
     * @return bool
     * @throws Exception
     */
    public function waitUntil($callback, $args = array(), $timeout = 15) 
    {
        // Number of loops to do.
        $loops = 50;
        // The last exception caught.
        $caughtException = null;
        // Args to be an array.
        if(is_null($args)) {
            $args = array();
        }

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
     * Wait until the css value is equal
     *
     * @param string $selector
     * @param string $name
     * @param string $expectedValue
     * @param int $timeout
     * @throws Exception
     * @return void
     */
    public function waitUntilCssValueEqual($selector, $name, $expectedValue, $timeout = 10) 
    {
        $this->waitUntil(
            function () use (&$selector, &$name, &$expectedValue) {
                $e = $this->getDriver()->findElement(WebDriverBy::cssSelector($selector));
                $value = $e->getCssValue($name);
                $rgba = Color::rgbToRgba($value);
                if ($rgba !== $expectedValue) {
                    $message = 'The colors do not match. ';
                    $message .= 'Expected: ' . $expectedValue;
                    $message .= ' and and got ' . $rgba;
                    PHPUnit_Framework_Assert::fail($message);
                }
            }, null, $timeout
        );
    }

    /**
     * Wait until I don't see an element, or an element containing a given text.
     *
     * @param string $cssSelector
     * @param null $regexp
     * @param int $timeout
     * @return bool true
     */
    public function waitUntilIDontSee($cssSelector, $regexp = null, $timeout = 10)
    {
        for ($i = 0; $i < $timeout * 10; $i++) {
            // Try to find the element. If not found, return true.
            try {
                $elt = $this->getDriver()->findElement(WebDriverBy::cssSelector($cssSelector));
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
        $message = "waitUntilIDontSee $cssSelector, $regexp : Timeout thrown by " . $backtrace[1]['class'];
        $message .= "::" . $backtrace[1]['function'] . "()\n . element: $cssSelector ($regexp)";
        PHPUnit_Framework_Assert::fail($message);
    }

    /**
     * Wait until the element has focus
     *
     * @param string $id
     * @param int $timeout
     * @return bool
     * @throws Exception
     */
    public function waitUntilElementHasFocus($id, $timeout = 10) 
    {
        for ($i = 0; $i < $timeout * 10 * 10; $i++) {
            try {
                $activeElt = $this->getDriver()->switchTo()->activeElement();
                $activeId = $activeElt->getAttribute('id');
                if ($id !== $activeId) {
                    $message = 'Could not get focus for ' . $id;
                    PHPUnit_Framework_Assert::fail($message);
                }
                return true;
            }
            catch (Exception $e) {
            }

            // If none of the above was found, wait for 1/10 seconds, and try again.
            usleep(100000);
        }

        $backtrace = debug_backtrace();
        $message = "waitUntilElementHasFocus $id Timeout thrown by " . $backtrace[1]['class'] . "::" . $backtrace[1]['function'] . "() \n";
        PHPUnit_Framework_Assert::fail($message);
    }

    /**
     * Wait until I see.
     *
     * @param mixed $ids array of cssSelector, or string representing one id
     * @param array or string $regexps (follows $ids)
     * @param int timeout timeout in seconds
     * @return mixed true if at least one element is found
     */
    public function waitUntilISee($ids, $regexps = null, $timeout = 15) 
    {
        // Test internal clock, test every maximum clock second.
        $clock = 0.500;
        // When we go over the timeout, change the state of this variable.
        $continue = true;
        // Start time.
        $testStart = microtime(true);

        do {
            // Store the loop start time.
            $loopStart = microtime(true);

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

            // Store the loop end time.
            $loopEnd = microtime(true);
            // Should we wait more ?
            $loopElapsed = $loopStart - $loopEnd;
            if ($loopElapsed < $clock) {
                usleep(($clock - $loopElapsed) * 1000000);
            }
            // Does the timeout overlapped.
            if (($loopEnd - $testStart) > $timeout) {
                $continue = false;
            }
        } while ($continue);

        $backtrace = debug_backtrace();
        $id = is_array($ids) ? implode(",", $ids) : $ids;
        $regexp = is_array($regexps) ? implode(",", $regexps) : $regexps;

        // Fail if not found.
        $message = "waitUntilISee $id, $regexp\nTimeout thrown by " . $backtrace[1]['class'];
        $message .= "::" . $backtrace[1]['function'] . "()\n . element(s): $id ($regexp)";
        PHPUnit_Framework_Assert::fail($message);
    }

    /**
     * Wait until the title contains.
     *
     * @param  $title
     * @throws Exception
     */
    public function waitUntilTitleContain($title) 
    {
        $callback = array($this, 'assertTitleContain');
        $this->waitUntil($callback, array($title));
    }

    /**
     * Wait until the url match a pattern
     *
     * @param  string $url
     * @param  bool   $addBase
     * @param  int    $timeout
     * @throws Exception
     * @return void
     */
    public function waitUntilUrlMatches($url, $addBase = true, $timeout = 10) 
    {
        $this->waitUntil(
            function () use ($url, $addBase) {
                if ($addBase) {
                    $uri = Config::read('passbolt.url') . DS . $url;
                } else {
                    $uri = $url;
                }
                if($uri !== $this->getDriver()->getCurrentURL()) {
                    $message = 'The url do not match. ';
                    $message .= 'expected: ' . $uri;
                    $message .= ' and got ' . $this->getDriver()->getCurrentURL();
                    PHPUnit_Framework_Assert::fail($message);
                }
            }, null, $timeout
        );
    }

    /**
     * Wait until an HTML Element has the attribute disabled
     *
     * @param  $id
     * @throws Exception
     */
    public function waitUntilDisabled($id) 
    {
        $this->waitUntil(array($this, 'assertDisabled'), array($id));
    }

    /**
     * Wait until a notification disappears.
     *
     * @param  $notificationId
     * @throws Exception
     */
    public function waitUntilNotificationDisappears($notificationId) 
    {
        $notificationId = 'notification_' . UuidFactory::uuid($notificationId);
        $this->waitUntilIDontSee($notificationId);
    }

    /**
     * Wait until the secret is decrypted and inserted in the secret field.
     *
     * @throws Exception
     */
    public function waitUntilSecretIsDecryptedInField() 
    {
        $this->waitUntilIDontSee('#js_secret.decrypting');
    }

}