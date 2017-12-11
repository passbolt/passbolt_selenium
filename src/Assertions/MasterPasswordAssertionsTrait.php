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
namespace App\assertions;

trait MasterPasswordAssertionsTrait
{

    /**
     * Check if the passphrase dialog is working as expected
     */
    public function assertMasterPasswordDialog($user) 
    {
        // Get out of the previous iframe in case we are in one
        $this->goOutOfIframe();
        // Given I can see the iframe
        $this->waitUntilISee('#passbolt-iframe-master-password.ready');
        // When I can go into the iframe
        $this->goIntoMasterPasswordIframe();
        // Then  I can see the security token is valid
        $this->assertSecurityToken($user, 'master');
        // Then  I can see the title
        $this->assertElementContainsText('.master-password.dialog', 'Please enter your passphrase');
        // Then  I can see the close dialog button
        $this->assertVisible('a.dialog-close');
        // Then  I can see the OK button
        $this->assertVisible('master-password-submit');
        // Then  I can see the cancel button
        $this->assertVisible('a.js-dialog-close.cancel');
        // Then  I go out of the iframe
        $this->goOutOfIframe();
    }

}