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

trait ShareAssertionsTrait
{
    /**
     * Assert that the share dialog is visible
     * @return bool
     */
    public function assertShareDialogVisible() {
        $this->waitUntilISee('#passbolt-iframe-password-share.ready');
    }

    /**
     * Assert that the permission selector is disabled for a given user
     */
    public function assertPermissionSelectDisabled($userId) {
        $this->assertDisabled("#$userId .select.rights .permission");
    }

}