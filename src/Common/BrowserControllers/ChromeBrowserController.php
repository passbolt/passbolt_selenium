<?php
namespace App\Common\BrowserControllers;

use App\Common\BrowserControllers\BrowserController;
use Exception;

class ChromeBrowserController extends BrowserController
{
    /**
     * @throws Exception
     */
    public function openNewTab($url = null)
    {
        parent::openNewTab();

        // Get the current tab url, it will be used to check if the new tab is opened.
        // The constraint here is that we have the hypothesis that the new tab will have a different url.
        $initialUrl = $this->getDriver()->getCurrentURL();

        // Ensure the page is loaded.
        $this->testCase->waitUntilISee('body');

        // Open a new tab.
        // The CTL+T shortcut doesn't work with chrome on saucelabs.
        $script = "window.open('about:blank','_blank');";
        $this->getDriver()->executeScript($script);

        // Wait until tab is opened. A new tab should have a different url.
        $callback = array($this, 'onNewTabOpened');
        try {
            $this->testCase->waitUntil($callback, array($initialUrl));
        } catch (Exception $e){
            throw new Exception("Couldn't open a new tab");
        }
    }
}
