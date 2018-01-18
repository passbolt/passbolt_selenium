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

trait SidebarAssertionsTrait
{
    /**
     * Assert that the toggle button is in the given status (pressed or unpressed)
     *
     * @param $id
     * @param int $status
     *
     * @return bool
     */
    public function assertToggleButtonStatus($id, $status = TOGGLE_BUTTON_PRESSED) 
    {
        $toggleButton = $this->find($id);
        $classes = $toggleButton->getAttribute('class');
        $classes = explode(' ', $classes);
        $pressed = 0;
        if (in_array('selected', $classes)) {
            $pressed = 1;
        }
        $this->assertTrue($pressed == $status);

    }

    /**
     * Check if a sidebar section is closed
     *
     * @param string $name Name of the section
     * @return bool
     */
    public function isSecondarySidebarSectionClosed($name) {
        $sectionElement = $this->find(".panel.aside .sidebar-section.$name");
        return $this->elementHasClass($sectionElement, 'closed');
    }

}