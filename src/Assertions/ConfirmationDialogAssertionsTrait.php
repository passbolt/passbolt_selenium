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

trait ConfirmationDialogAssertionsTrait
{
    /**
     * Check if confirmation dialog is displayed as it should.
     *
     * @param string $title
     */
    public function assertConfirmationDialog($title = '') 
    {
        // Assert I can see the confirm dialog.
        $this->waitUntilISee('.dialog.confirm');
        // Then  I can see the close dialog button
        $this->assertVisible('.dialog.confirm a.dialog-close');
        // Then  I can see the cancel link.
        $this->assertVisible('.dialog.confirm a.cancel');
        // Then  I can see the Ok button.
        $this->assertVisible('.dialog.confirm input#confirm-button');
        if ($title !== '') {
            // Then  I can see the title
            $this->assertElementContainsText('.dialog.confirm', $title);
        }
    }

    /**
     * Wait until the expired dialog appears.
     *
     * @throws Exception
     */
    public function assertSessionExpiredDialog() 
    {
        // Assert I can see the confirm dialog.
        $this->waitUntilISee('.session-expired-dialog', null, 120);
        // Then  I can see the close dialog button
        $this->assertNotVisible('.session-expired-dialog a.dialog-close');
        // Then  I can see the cancel link.
        $this->assertNotVisible('.session-expired-dialog a.cancel');
        // Then  I can see the Ok button.
        $this->assertVisible('.session-expired-dialog input#confirm-button');
        // Then  I can see the title
        $this->assertElementContainsText('.session-expired-dialog', 'Session expired');
    }
}