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
namespace App;

abstract class PassboltRecoverTestCase extends PassboltTestCase
{
    /**
     * go to recover setup page.
     *
     * @param string $username email of the user
     * @param bool $checkPluginSuccess check if plugin is present
     * @return void
     */
    public function goToRecover(string $username, bool $checkPluginSuccess = true)
    {
        // Get last email.
        $this->getUrl('seleniumtests/showlastemail/' . urlencode($username));

        // Remember setup url. (We will use it later).
        $linkElement = $this->findLinkByText('start recovery');
        $setupUrl = $linkElement->getAttribute('href');

        // Go to url remembered above.
        $this->getDriver()->get($setupUrl);

        // Test that the plugin confirmation message is displayed.
        if ($checkPluginSuccess) {
            $this->waitUntilISee('.plugin-check-wrapper .plugin-check.success', '/Nice one! The plugin is installed and up to date/i');
        }
    }
}