<?php

/**
 * Class Color
 * Helper class for color conversion
 *
 * @copyright (c) 2017 Passbolt SARL
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class Color {
	/**
	 * Convert a string like rgba(255, 58, 58, 1) to his hexadecimal value e.g #FF3A3A
	 *
	 * @param string $rgba value
	 * @return false|string if successful
	 */
	static public function toHex($rgba) {
		if (!preg_match_all("/[0-9]{1,3}/", $rgba, $matches) || count($matches[0]) != 4) {
			return false;
		}
		$res = '#';
		for ($i = 0; $i < 3; $i++) { // we don't care about transparency
			$res .= dechex($matches[0][$i]);
		}
		return $res;
	}

	/**
	 * Convert a hexadecimal color value like #FF3A3A to its rgba value e.g rgba(255, 58, 58, 1)
	 *
	 * @param string $hex value
	 * @param int $opacity opacity
	 * @return false|string if successful
	 */
	static public function toRgba($hex, $opacity = 1) {
		$default = 'rgba(0, 0, 0, 1)';

		//Return default if no color provided
		if (empty($hex)) {
			return $default;
		}

		//Sanitize $hex if "#" is provided
		if ($hex[0] == '#') {
			$hex = substr($hex, 1);
		}

		//Check if color has 6 or 3 characters and get values
		if (strlen($hex) == 6) {
			$hex = [$hex[0] . $hex[1], $hex[2] . $hex[3], $hex[4] . $hex[5]];
		} elseif (strlen($hex) == 3) {
			$hex = [$hex[0] . $hex[0], $hex[1] . $hex[1], $hex[2] . $hex[2]];
		} else {
			return $default;
		}

		//Convert hexadec to rgb
		$rgb = array_map('hexdec', $hex);

		// Build the rgb string
		$output = 'rgba(' . implode(", ", $rgb) . ', '. $opacity . ')';

		//Return rgb(a) color string
		return $output;
	}
}
