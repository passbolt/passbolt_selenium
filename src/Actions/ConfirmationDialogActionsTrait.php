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
namespace App\actions;

use App\BaseTestTrait;

trait ConfirmationDialogActionsTrait
{

    use BaseTestTrait;

    /**
     * Assert the action text of the confirmation dialog.
     */
    public function assertActionNameInConfirmationDialog($text) 
    {
        $button = $this->find('confirm-button');
        $this->assertEquals($button->getAttribute('value'), $text);
    }

    /**
     * Click on the cancel button in the confirm dialog.
     */
    public function cancelActionInConfirmationDialog() 
    {
        $button = $this->findByCss('.dialog.confirm .js-dialog-cancel');
        $button->click();
    }

    /**
     * Click on the ok button in the confirm dialog.
     */
    public function confirmActionInConfirmationDialog() 
    {
        $button = $this->find('confirm-button');
        $button->click();
    }

}