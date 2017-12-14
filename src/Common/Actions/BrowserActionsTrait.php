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
namespace App\Common\Actions;

use App\Common\BrowserControllers\BrowserController;

use Facebook\WebDriver\Remote\DriverCommand;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverKeys;

trait BrowserActionsTrait
{

    // Browser specific controller.
    protected $_browserController;

    abstract public function getDriver() : RemoteWebDriver;

    // the cookies used to log the different user.
    protected static $loginCookies = array();


    public function getBrowserController() : BrowserController
    {
        return $this->_browserController;
    }

    /**
     * Refresh page.
     */
    public function refresh()
    {
        $this->getDriver()->execute(DriverCommand::REFRESH);
        $this->waitCompletion();
    }

    /**
     * Maximize the window.
     */
    public function maximizeWindow()
    {
        $this->getDriver()->manage()->window()->maximize();

        // @deprecated, but keep it as example if it happens again.
        // We set the dimension manually because the maximize function doesn't work anymore with the current unbranded
        // version of FF we use (54.0.1) and geckodriver 0.19..
        // $this->getDriver()->manage()->window()->setSize(new WebDriverDimension(1920, 1080));
    }

    /**
     * Put the focus back to the normal context
     */
    public function goOutOfIframe()
    {
        $this->getDriver()->switchTo()->defaultContent();
    }

    /**
     * Restart the browser.
     *
     * We mimic the following behavior :
     * * The user quits the browser;
     * * The user restarts it.
     *
     * We expect the cookies to be as they were before quitting the browser. So if the user was logged-in on the
     * application before quitting the browser, he should be logged-in after the browser is restarted.
     * -> We store and reload manually the cookies
     *
     * The application pagemod is started after a successful authentication or when the plugin is started for the first
     * time with a user already logged-in. However we can't load the cookies before starting the plugin.
     * -> We implements a workaround to start the application pagemod manually, see the debug page.
     *
     * @param $options
     *     waitBeforeRestart : Should the browser be restarted after a sleep in seconds
     */
    public function restartBrowser($options = array())
    {
        $options = $options ? $options : array();
        $waitBeforeRestart = isset($options['waitBeforeRestart']) ? $options['waitBeforeRestart'] : 0;

        // Quit the browser.
        $this->getDriver()->quit();
        // Reset the addon url, as for firefox it will change after a browser restart.
        $this->addonUrl = '';

        // If a wait before restart option has been given.
        sleep($waitBeforeRestart);

        // Restart the brower
        $this->initBrowser();
        $this->maximizeWindow();

        // As the browser local storage has been cleaned.
        // Set the client config has it was before quitting.
        if (!is_null($this->currentUser)) {
            $this->setClientConfig($this->currentUser);
        }

        // Same for the cookies.
        if (!empty(self::$loginCookies[$this->currentUser['Username']])) {
            $this->getUrl('/auth/login');
            foreach(self::$loginCookies[$this->currentUser['Username']] as $cookie) {
                $this->getDriver()->manage()->addCookie($cookie);
            }
        }

        // The application page mode needs to be restarted manually.
        $this->goToDebug();
        $this->click('initAppPagemod');

        // Go to the application
        sleep(2);
        $this->getUrl('');
    }


    /**
     * Open a new window.
     *
     * @param string $url url
     * @return object WindowHandle
     */
    public function openNewWindow($url = '')
    {
        $windowsCount = sizeof($this->getDriver()->getWindowHandles());

        // Use driver keyboard to open a new window.
        $this->waitUntilISee('body');
        $this->findByCss('body')
            ->sendKeys([WebDriverKeys::CONTROL, 'n']);

        // Wait until window is opened.
        // Number of loops to do.
        $loops = 50;
        // Timeout in seconds.
        $timeout = 10;
        $i = 0;
        while (!(sizeof($this->getDriver()->getWindowHandles()) > $windowsCount)) {
            if ($i > $loops) {
                $this->fail("Couldn't open a new window");
            }
            $second = 1000000;
            usleep(($second * $timeout) / $loops);
            $i++;
        }

        // Switch to new window.
        $windowHandles = $this->getDriver()->getWindowHandles();
        $this->getDriver()->switchTo()->window($windowHandles[sizeof($windowHandles) - 1]);

        $this->getUrl($url);

        return $windowHandles[sizeof($windowHandles) - 1];
    }

    /**
     * Switch to window.
     *
     * @param $windowId
     */
    public function switchToWindow($windowId)
    {
        $windowHandles = $this->getDriver()->getWindowHandles();
        if (!isset($windowHandles[$windowId])) {
            $this->fail("Couldn't switch to window/tab " . $windowId);
        }
        $this->getDriver()->switchTo()->window($windowHandles[$windowId]);
    }

    /**
     * Switch to next tab.
     */
    public function switchToNextTab()
    {
        $this->getBrowserController()->switchToNextTab();
    }

    /**
     * Switch to previous tab.
     */
    public function switchToPreviousTab()
    {
        $this->getBrowserController()->switchToPreviousTab();
    }

    /**
     * Open a new tab in browser, and go to given url.
     */
    public function openNewTab($url = '')
    {
        $this->getBrowserController()->openNewTab();
        $this->getUrl($url);
    }

    /**
     * Close the current tab.
     */
    public function closeTab()
    {
        $this->getBrowserController()->closeTab();
    }

    /**
     * Restore the latest closed tab.
     */
    public function restoreTab()
    {
        $this->getBrowserController()->restoreTab();
    }

    /**
     * Close and restore the current tab.
     * Ensure the test run already on a second tab.
     *
     * Note :
     * PASSBOLT-2263 close and restore doesn't work with the latest chrome driver
     * PASSBOLT-2419 close and restore doesn't work with the latest firefox driver
     *
     * @param array $options (optional)
     *                        waitBeforeRestore : Should the tab be restored after a sleep in seconds
     * @return void
     */
    public function closeAndRestoreTab($options = array())
    {
        $options = $options ? $options : array();
        $waitBeforeRestore = isset($options['waitBeforeRestore']) ? $options['waitBeforeRestore'] : 0;

        // Close the current tab.
        $this->closeTab();

        sleep($waitBeforeRestore);

        // Restore closed tab.
        $this->restoreTab();
    }

}