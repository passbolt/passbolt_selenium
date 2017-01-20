<?php

class ChromeBrowserController {

    protected $driver;
    protected $testCase;

    function __construct($driver, $testCase) {
        $this->driver = $driver;
        $this->testCase = $testCase;
    }

    public function _openNewTabOpened($initialUrl) {
        // Give the focus to the new tab.
        $windowHandles = $this->driver->getWindowHandles();
        $this->driver->switchTo()->window($windowHandles[$this->testCase->currentTabIndex]);

        // Check if the new tab is open.
        if ($this->driver->getCurrentURL() == $initialUrl) {
            throw new Exception('Url matches');
        }
    }

    public function openNewTab() {
        // Get the current tab url, it will be used to check if the new tab is opened.
        // The constraint here is that we have the hypothesis that the new tab will have a different url.
        $initialUrl = $this->driver->getCurrentURL();

        // Ensure the page is loaded.
        $this->testCase->waitUntilISee('body');

        // Open a new tab.
        // The CTL+T shortcut doesn't work with chrome on saucelabs.
        $script = "window.open('about:blank','_blank');";
        $this->driver->executeScript($script);

        // Wait until tab is opened. A new tab should have a different url.
        $callback = array($this, '_openNewTabOpened');
        try {
            $this->testCase->waitUntil($callback, array($initialUrl));
        } catch (Exception $e){
            throw new Exception("Couldn't open a new tab");
        }
    }

    public function closeTab() {
		$this->testCase->findByCss('body')
			->sendKeys(array(WebDriverKeys::CONTROL, 'w'));
        $windowHandles = $this->driver->getWindowHandles();
        $this->driver->switchTo()->window($windowHandles[$this->testCase->currentTabIndex]);
    }

    public function restoreTab() {
        $this->testCase->findByCss('body')
            ->sendKeys(array(WebDriverKeys::SHIFT, WebDriverKeys::CONTROL, 't'));
        // Give the focus to the selected tab window.
        $windowHandles = $this->driver->getWindowHandles();
        $this->driver->switchTo()->window($windowHandles[$this->testCase->currentTabIndex]);
    }

    public function switchToPreviousTab() {
        $this->testCase->findByCss('body')
            ->sendKeys(array(WebDriverKeys::CONTROL, WebDriverKeys::PAGE_DOWN));
        // Give the focus to the selected tab window.
        $windowHandles = $this->driver->getWindowHandles();
        $this->driver->switchTo()->window($windowHandles[$this->testCase->currentTabIndex]);
    }

    public function switchToNextTab() {
        $this->testCase->findByCss('body')
            ->sendKeys(array(WebDriverKeys::CONTROL, WebDriverKeys::PAGE_UP));
        // Give the focus to the selected tab window.
        $windowHandles = $this->driver->getWindowHandles();
        $this->driver->switchTo()->window($windowHandles[$this->testCase->currentTabIndex]);
    }

}
