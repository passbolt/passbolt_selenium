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
namespace App\Assertions;

use Facebook\WebDriver\Exception\StaleElementReferenceException;

trait SecurityAssertionsTrait
{
	/**
	 * Assert that I don't see any XSS execution
	 */
	public function assertXss()
	{
		// If not on a passbolt page
		try {
			$pageElement = $this->findByCss('html');
			if ($pageElement && !$this->elementHasClass($pageElement, 'passbolt')) {
				// Check if the page has been modified by an XSS vulnerability
				$text = $pageElement->getText();
				if(preg_match('/^XSS/i', $text)) {
					$this->fail('XSS vulnerability found : ' . $text);
				}
			}
		} catch (StaleElementReferenceException $e) {
			$this->assertXss();
		}
	}

}