<?php
/**
 * PassboltServer
 * The class to interact with passbolt server/API.
 *
 * @copyright 	(c) 2015-present Bolt Software Pvt. Ltd.
 * @licence			GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
 */
/**
 * Class PassboltServer
 */
class PassboltServer {
	/**
	 * @var string url
	 */
	public $url;

	/**
	 * @var array options
	 */
	public $options;

	/**
	 * Constructor.
	 * @param       $url
	 * @param array $options
	 */
	public function __construct($url, $options = array()) {
		$this->url = $url;
		$this->options = $options;
	}

	/**
	 * Reset passbolt installation
	 * @return bool
	 */
	public function resetDatabase($dummy = 1) {
		$response = \Httpful\Request::get($this->url . '/seleniumTests/resetInstance/' . $dummy)
	                   ->send();
		return preg_match('/created/', $response->body);
	}
}