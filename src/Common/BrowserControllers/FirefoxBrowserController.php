<?php
namespace App\Common\BrowserControllers;

use App\Common\BrowserControllers\BrowserController;
use Exception;

class FirefoxBrowserController extends BrowserController
{

    public function openNewTab() 
    {
        parent::openNewTab();

        // Get the current tab url, it will be used to check if the new tab is opened.
        // The constraint here is that we have the hypothesis that the new tab will have a different url.
        $initialUrl = $this->getDriver()->getCurrentURL();

        // Send an event to the extension to open a new tab.
        $this->testCase->triggerEvent('passbolt.addon.debug.open_tab');

        // Wait until tab is opened. A new tab should have a different url.
        $callback = array($this, 'onNewTabOpened');
        try {
            $this->testCase->waitUntil($callback, array($initialUrl));
        } catch (Exception $e){
            throw new Exception("Couldn't open a new tab");
        }
    }

}
