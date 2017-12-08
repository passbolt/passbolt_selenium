<?php
/**
 * UUID convenience functions
 *
 * @copyright (c) 2017 Passbolt SARL
 * @licence   GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
namespace App\lib;

class Uuid
{

    /**
     * Return a UUID
     *
     * @param  string $seed, used to create deterministic UUID
     * @return string UUID
     */
    public static function get($seed = null) 
    {
        $pattern = '/^(.{8})(.{4})(.{1})(.{3})(.{1})(.{3})(.{12})$/';
        if (isset($seed)) {
            $string = substr(sha1($seed), 0, 32);
            $replacement = '${1}-${2}-3${4}-a${6}-${7}'; // v5
        } else {
            $string = bin2hex(openssl_random_pseudo_bytes(16));
            $replacement = '${1}-${2}-4${4}-a${6}-${7}'; // v4
        }
        return preg_replace($pattern, $replacement, $string);
    }

    /**
     * Return true if a given string is a UUID
     *
     * @param  string $str
     * @return boolean
     */
    public static function isUuid($str) 
    {
        return is_string($str) && preg_match('/^[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[0-5][a-fA-F0-9]{3}-[089aAbB][a-fA-F0-9]{3}-[a-fA-F0-9]{12}$/', $str);
    }

}
