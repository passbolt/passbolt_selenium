<?php

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;

class FirefoxBrowserController {
    protected $driver;
    protected $testCase;

    function __construct($driver, $testCase) {
        $this->driver = $driver;
        $this->testCase = $testCase;
    }

    public function _openNewTabOpened($initialUrl) {
        // Give the focus to the new tab.
        $this->driver->switchTo()->defaultContent();

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
        $this->driver->findElement(WebDriverBy::cssSelector('body'))
            ->sendKeys([WebDriverKeys::CONTROL, 't']);

        // Wait until tab is opened. A new tab should have a different url.
        $callback = [$this, '_openNewTabOpened'];
        try {
            $this->testCase->waitUntil($callback, [$initialUrl]);
        } catch (Exception $e){
            throw new Exception("Couldn't open a new tab");
        }
    }

    public function closeTab() {
        $this->testCase->findByCss('body')
            ->sendKeys(array(WebDriverKeys::CONTROL, 'w'));
        $this->driver->switchTo()->defaultContent();
    }

    public function restoreTab() {
        $this->testCase->findByCss('body')
            ->sendKeys(array(WebDriverKeys::SHIFT, WebDriverKeys::CONTROL, 't'));
        $this->driver->switchTo()->defaultContent();
    }

    public function switchToPreviousTab() {
        $this->testCase->findByCss('body')
            ->sendKeys([WebDriverKeys::CONTROL, WebDriverKeys::PAGE_DOWN]);
        $this->driver->switchTo()->defaultContent();
    }

    public function switchToNextTab() {
        $this->testCase->findByCss('body')
            ->sendKeys([WebDriverKeys::CONTROL, WebDriverKeys::PAGE_UP]);
        $this->driver->switchTo()->defaultContent();
    }

}
