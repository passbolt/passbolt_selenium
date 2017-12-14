<?php
namespace App\Common\BrowserControllers;

use App\Common\SeleniumTestCase;
use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Exception;

class BrowserController
{

    protected $driver;
    protected $testCase;

    // Tabs.
    public $tabsCount = 1;
    public $currentTabIndex = 0;

    /**
     * ChromeBrowserController constructor.
     *
     * @param $driver
     * @param $testCase
     */
    function __construct(RemoteWebDriver $driver, SeleniumTestCase $testCase)
    {
        $this->driver = $driver;
        $this->testCase = $testCase;
    }

    public function getDriver() 
    {
        return $this->driver;
    }

    /**
     * Open new tab
     * Differs for each browsers
     */
    public function openNewTab() 
    {
        $this->tabsCount++;
        $this->currentTabIndex = $this->tabsCount - 1;
    }

    /**
     * Callback when new tab is opened
     *
     * @param $initialUrl
     * @throws Exception
     */
    public function onNewTabOpened($initialUrl)
    {
        // Give the focus to the new tab.
        $windowHandles = $this->getDriver()->getWindowHandles();
        $this->getDriver()->switchTo()->window($windowHandles[$this->currentTabIndex]);

        // Check if the new tab is open.
        if ($this->getDriver()->getCurrentURL() == $initialUrl) {
            throw new Exception('Url matches');
        }
    }

    public function closeTab()
    {
        $this->tabsCount--;
        $this->currentTabIndex--;
        $this->testCase->findByCss('body')
            ->sendKeys(array(WebDriverKeys::CONTROL, 'w'));
        $windowHandles = $this->getDriver()->getWindowHandles();
        $this->getDriver()->switchTo()->window($windowHandles[$this->currentTabIndex]);
    }

    public function restoreTab()
    {
        $this->tabsCount++;
        $this->currentTabIndex++;
        $this->testCase->findByCss('body')
            ->sendKeys(array(WebDriverKeys::SHIFT, WebDriverKeys::CONTROL, 't'));
        // Give the focus to the selected tab window.
        $windowHandles = $this->getDriver()->getWindowHandles();
        $this->getDriver()->switchTo()->window($windowHandles[$this->currentTabIndex]);
    }

    public function switchToPreviousTab()
    {
        $this->currentTabIndex--;
        $this->testCase->findByCss('body')
            ->sendKeys(array(WebDriverKeys::CONTROL, WebDriverKeys::PAGE_DOWN));
        // Give the focus to the selected tab window.
        $windowHandles = $this->getDriver()->getWindowHandles();
        $this->getDriver()->switchTo()->window($windowHandles[$this->currentTabIndex]);
    }

    public function switchToNextTab()
    {
        $this->currentTabIndex++;
        $this->testCase->findByCss('body')
            ->sendKeys(array(WebDriverKeys::CONTROL, WebDriverKeys::PAGE_UP));
        // Give the focus to the selected tab window.
        $windowHandles = $this->getDriver()->getWindowHandles();
        $this->getDriver()->switchTo()->window($windowHandles[$this->currentTabIndex]);
    }

}