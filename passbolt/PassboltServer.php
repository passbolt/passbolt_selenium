<?php
/**
 * PassboltServer
 * The class to interact with passbolt server/API.
 *
 * @copyright (c) 2015-present Bolt Software Pvt. Ltd.
 * @licence GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
 */
class PassboltServer {

	/**
	 * Reset passbolt installation
	 * @return bool
	 */
	static public function resetDatabase($url, $dummy = 'seleniumtests') {
		$response = \Httpful\Request::get($url . '/seleniumTests/resetInstance/' . $dummy)
	                   ->send();
		return preg_match('/created/', $response->body);
	}
}