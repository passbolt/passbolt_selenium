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

trait MasterPasswordAssertionsTrait
{

    /**
     * Check if the passphrase dialog is working as expected
     */
    public function assertMasterPasswordDialog($user) 
    {
        // Get out of the previous iframe in case we are in one
        $this->goOutOfIframe();
        // Go into the react iframe.
        $this->goIntoReactAppIframe();
        // I wait until the passphrase entry dialog is displayed.
        $this->waitUntilISee('.dialog.passphrase-entry');
        // I go out of the iframe
        $this->goOutOfIframe();
    }

}