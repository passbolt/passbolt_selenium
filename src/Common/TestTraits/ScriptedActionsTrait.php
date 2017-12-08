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
namespace App\Common\TestTraits;

use Facebook\WebDriver\Remote\RemoteWebDriver;

trait ScriptedActionsTrait
{
    abstract public function getDriver() : RemoteWebDriver;

    /**
     * Set an element value.
     * Doesn't mimic user behavior, DOM Events are not all triggered.
     *
     * @param $id string an element id or selector
     * @param $txt the text to be typed on keyboard
     */
    public function setElementValue($id, $text) 
    {
        $script = "
		var element = document.getElementById('$id');
		element.value = '" . addslashes($text) . "';
		var evt = document.createEvent('HTMLEvents');
		evt.initEvent('change', false, true);
		element.dispatchEvent(evt);
		";
        $this->getDriver()->executeScript($script);
    }

    /**
     * Trigger an event on a page.
     *
     * @param $eventName
     */
    public function triggerEvent($eventName, $data = array()) 
    {
        $jsonData = '';
        if (!empty($data)) {
            $jsonData = ', ' . json_encode($data);
        }
        $fireEvent = 'function fireEvent(obj, evt, data){
		     var fireOnThis = obj;
		     if( document.createEvent ) {
		       var evObj = document.createEvent("CustomEvent");
		       evObj.initEvent( evt, true, false, data );
		       evObj.details = data;
		       fireOnThis.dispatchEvent( evObj );
		     }
		      else if( document.createEventObject ) { //IE
		       var evObj = document.createEventObject();
		       fireOnThis.fireEvent( "on" + evt, evObj );
		     }
		}
		fireEvent(window, "' . $eventName . '" ' . $jsonData . ');';
        $this->getDriver()->executeScript($fireEvent);
    }

    /**
     * Scroll an element to its bottom.
     *
     * @param $idSelector
     */
    public function scrollElementToBottom($idSelector) 
    {
        $script = "
		var objDiv = document.getElementById('$idSelector');
		objDiv.scrollTop = objDiv.scrollHeight;
		";
        $this->getDriver()->executeScript($script);
    }

}