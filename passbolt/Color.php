<?php
/**
 * Class Color
 * Helper class for color conversion
 *
 * @copyright (c) 2015-present Bolt Softwares Pvt Ltd
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class Color {
    /**
     * Convert a string like rgba(255, 58, 58, 1) to his hexadecimal value e.g #FF3A3A
     * @param string $rgba value
     * @return false|string if successful
     */
    static public function toHex($rgba) {
        if(!preg_match_all("/[0-9]{1,3}/",$rgba, $matches) || count($matches[0]) != 4) {
            return false;
        }
        $res = '#';
        for($i = 0; $i < 3; $i++) { // we don't care about transparency
            $res .= dechex($matches[0][$i]);
        }
        return $res;
    }
}
