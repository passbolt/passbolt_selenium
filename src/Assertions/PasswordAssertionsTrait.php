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

trait PasswordAssertionsTrait
{

    /**
     * Check if the password has already been selected
     *
     * @param $id string
     * @return bool
     */
    public function isPasswordFavorite($id) 
    {
        $eltSelector = '#favorite_' . $id . ' i';
        if ($this->elementHasClass($eltSelector, 'unfav')) {
            return true;
        }
        return false;
    }

    /**
     * Check if the complexity indicators match a given strength (creation/edition context)
     *
     * @param $strength string
     */
    public function assertComplexity($strength) 
    {
        $class = str_replace(' ', '_', $strength);
        $this->assertVisibleByCss('#js_secret_strength.'.$class);
        $this->assertElementHasClass(
            $this->findByCss('#js_secret_strength .progress-bar'),
            $class
        );
        // We check visibility only if the strength is available.
        if ($strength != 'not available') {
            $this->assertVisibleByCss('#js_secret_strength .progress-bar.'.$class);
        }
        $this->assertVisibleByCss('#js_secret_strength .complexity-text');
        $labelStrength = $strength != 'not available' ? $strength : 'n/a';
        $this->assertElementContainsText('#js_secret_strength .complexity-text', 'complexity: '.$labelStrength);
    }

    /**
     * Assert a password is selected
     *
     * @param $id string
     * @return bool
     */
    public function assertPasswordSelected($id) 
    {
        $this->assertTrue($this->isPasswordSelected($id));
    }

    /**
     * Assert a password is not selected
     *
     * @param $id string
     * @return bool
     */
    public function assertPasswordNotSelected($id) 
    {
        $this->assertTrue($this->isPasswordNotSelected($id));
    }

    /**
     * Check if the password has already been selected
     *
     * @param $id string
     * @return bool
     */
    public function isPasswordSelected($id) 
    {
        $eltSelector = '#resource_' . $id;
        if ($this->elementHasClass($eltSelector, 'selected')) {
            return true;
        }
        return false;
    }

    /**
     * Check if the password has not been selected
     *
     * @param $id string
     * @return bool
     */
    public function isPasswordNotSelected($id) 
    {
        $eltSelector = '#resource_' . $id;
        if ($this->elementHasClass($eltSelector, 'selected')) {
            return false;
        }
        return true;
    }

    /**
     * Assert that a password is visible in the password workspace
     *
     * @param $name
     *   name of the password (lowercase)
     */
    public function assertICanSeePassword($name) 
    {
        try {
            $this->waitUntilISee('resource_' . UuidFactory::uuid('resource.id.' . $name), '/' . $name . '/i');
        }
        catch(Exception $e) {
            $this->fail("Failed to assert that the password " . $name . " is visible");
        }
    }

    /**
     * Assert that a password is not visible in the password workspace
     *
     * @param $name
     *   name of the password (lowercase)
     */
    public function assertICannotSeePassword($name) 
    {
        try {
            $this->waitUntilIDontSee('#resource_' . UuidFactory::uuid('resource.id.' . $name), '/' . $name . '/i');
        }
        catch(Exception $e) {
            $this->fail("Failed to assert that the password " . $name . " is visible");
        }
    }
}