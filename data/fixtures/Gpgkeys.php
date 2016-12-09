<?php
/**
 * GPGKey fixture.
 *
 * @copyright (c) 2015-present Bolt Softwares Pvt Ltd
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class Gpgkey {
	/**
	 * @return array
	 */
	static function _get() {
		$gpg[] = array(
			'name'          => 'johndoe',
			'owner_name'    => 'John Doe',
			'owner_email'   => 'johndoe@passbolt.com',
			'created'       => '9 Nov 2015 13:00',
			'expires'       => '9 Nov 2019 13:00',
			'comment'       => 'John Doe\'s key',
			'algorithm'     => 'rsa',
			'masterpassword'=> 'johndoe@passbolt.com',
			'fingerprint'   => '9F96977324F1E8F2EBCFC6973F19B8AA952C7941',
			'keyid'         => '952C7941',
			'filepath'      =>  GPG_DUMMY . DS . 'johndoe.key',
			'description'   => 'John Doe default key. Nothing special here.'
		);
		$gpg[] = array(
			'name'          => 'chien-shiung',
			'owner_name'    => 'Chien-Shiung Wu',
			'owner_email'   => 'chien-shiung@passbolt.com',
			'created'       => '9 Dec 2016 13:34',
			'expires'       => '9 Dec 2016 13:34',
			'comment'       => 'Chien-Shiung Wue\'s key',
			'algorithm'     => 'rsa',
			'masterpassword'=> 'chien-shiung@passbolt.com',
			'fingerprint'   => '2AE4A4AA3C8F8EDCA302E5B7B290FE4509AF67AF',
			'keyid'         => '09AF67AF',
			'filepath'      =>  GPG_DUMMY . DS . 'chien-shiung.key',
			'description'   => 'Chien-Shiung Wu default key. Nothing special here.'
		);

		return $gpg;
	}

	/**
	 * Return one gpg key based on the given conditions
	 * @param array $conditions
	 * @return array $resource
	 * @throws exception
	 */
	static function get($conditions) {
		$gpg = self::_get();
		$g = false;
		// filter by id if needed
		if (isset($conditions['name'])) {
			$g = self::_getByName($gpg, $conditions['name']);
		}

		if ($g === false) {
			throw new Exception('a gpg key fixture could not be found for these conditions, consider adding one');
		}
		return $g;
	}

	/**
	 * Return a Gpg key by name.
	 *
	 * @param $name
	 *   name of the key.
	 *
	 * @return bool
	 */
	static function _getByName($gpg, $name) {
		foreach($gpg as $key) {
			if ($key['name'] == $name) {
				return $key;
			}
		}
		return false;
	}
}