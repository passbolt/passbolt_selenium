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

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use PHPUnit_Framework_Assert;

trait FormAssertionsTrait
{

    abstract public function getDriver(): RemoteWebDriver;

    /**
     * @param string $id string id or css path
     * @param $value
     */
    public function assertInputValue($id, $value)
    {
        $input = $this->getDriver()->findElement(WebDriverBy::id($id));
        PHPUnit_Framework_Assert::assertTrue(
            ($input->getAttribute('value') == $value),
            'Failed to assert that the input: ' . $id . ', match value: ' . $value
        );
    }

    /**
     * Assert if an input is disabled
     *
     * @param $selector
     */
    public function assertDisabled($selector)
    {
        $elt = $this->find($selector);
        $this->assertElementHasClass($elt, 'disabled');
    }
}