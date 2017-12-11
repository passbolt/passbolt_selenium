<?php
/**
 * Resource fixture.
 *
 * @copyright (c) 2017 Passbolt SARL
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
namespace Data\Fixtures;

use App\Lib\Uuid;

class Resource {
    /**
     * @return array
     */
    static function _get() {
        $r[] = array(
            'id' => Uuid::get('resource.id.apache'),
            'username' => 'www-data',
            'name' => 'apache',
            'password' => '_upjvh-p@wAHP18D}OmY05M',
            'uri' => 'http://www.apache.org/',
            'complexity' => 'very strong',
            'description' => 'Apache is the world\'s most used web server software.'
        );
        $r[] = array(
            'id' => Uuid::get('resource.id.april'),
            'username' => 'support',
            'name' => 'april',
            'password' => 'z"(-1s]3&Itdno:vPt',
            'uri' => 'https://www.april.org/',
            'complexity' => 'strong',
            'description' => 'L\'association pionnière du logiciel libre en France'
        );
        $r[] = array(
            'id' => Uuid::get('resource.id.bower'),
            'username' => 'bower',
            'name' => 'bower',
            'password' => 'CL]m]x(o{sA#QW',
            'uri' => 'bower.io',
            'complexity' => 'fair',
            'description' => 'A package manager for the web!'
        );
        $r[] = array(
            'id' => Uuid::get('resource.id.centos'),
            'username' => 'centos',
            'name' => 'centos',
            'password' => 'this_23-04',
            'uri' => 'centos.org',
            'complexity' => 'very weak',
            'description' => 'The CentOS Linux distribution is a platform derived from Red Hat Enterprise Linux (RHEL).'
        );
		$r[] = array(
			'id' => Uuid::get('resource.id.canjs'),
			'username' => 'yeswecan',
			'name' => 'Canjs',
			'password' => 'princess',
			'uri' => 'canjs.com',
			'complexity' => 'very weak',
			'description' => 'CanJS is a JavaScript library that makes developing complex applications simple and fast.'
		);
		$r[] = array(
            'id' => Uuid::get('resource.id.enlightenment'),
            'username' => 'efl',
            'name' => 'Enlightenment',
            'password' => 'azertyuiop',
            'uri' => 'https://www.enlightenment.org/',
            'complexity' => 'very weak',
            'description' => 'Party like it\'s 1996.'
        );
	    $r[] = array(
		    'id' => Uuid::get('resource.id.gnupg'),
		    'username' => 'gpg',
		    'name' => 'Gnupg',
		    'password' => 'iamgod',
		    'uri' => 'gnupg.org',
		    'complexity' => 'very weak',
		    'description' => 'GnuPG is a complete and free implementation of the OpenPGP standard as defined by RFC4880',
	    );
	    $r[] = array(
		    'id' => Uuid::get('resource.id.chai'),
		    'username' => 'masala',
		    'name' => 'chai',
		    'password' => 'iloveyou',
		    'uri' => 'http://chaijs.com/',
		    'complexity' => 'very weak',
		    'description' => 'Chai is a BDD / TDD assertion library for node and the browser',
	    );
		$r[] = array(
			'id' => Uuid::get('resource.id.cakephp'),
			'username' => 'cake',
			'name' => 'cakephp',
			'password' => 'admin',
			'uri' => 'cakephp.org',
			'complexity' => 'very weak',
			'description' => 'The rapid and tasty php development framework'
		);
        return $r;
    }

    /**
     * Get many resources for a given username
     * @param $username
     * @return array
     */
    static function _getByUsername($username) {
        $r = self::_get();
        switch ($username) {
			case 'ada' :
                $r[0] = array_merge($r[0], array(
                    'permission' => 'owner'
                ));
                $r[1] = array_merge($r[1], array(
                    'permission' => 'deny'
                ));
                $r[2] = array_merge($r[2], array(
                    'permission' => 'read'
                ));
                $r[3] = array_merge($r[3], array(
                    'permission' => 'read'
                ));
				$r[4] = array_merge($r[4], array(
					'permission' => 'update'
				));
				$r[5] = array_merge($r[5], array(
					'permission' => 'read'
				));
				$r[6] = array_merge($r[6], array(
					'permission' => 'read'
				));
				$r[7] = array_merge($r[7], array(
					'permission' => 'deny'
				));
				$r[8] = array_merge($r[8], array(
					'permission' => 'owner'
				));
                break;
			case 'betty' :
                $r[0] = array_merge($r[0], array(
                    'permission' => 'update'
                ));
                $r[1] = array_merge($r[1], array(
                    'permission' => 'owner'
                ));
                $r[2] = array_merge($r[2], array(
                    'permission' => 'deny'
                ));
                $r[3] = array_merge($r[3], array(
                    'permission' => 'read'
                ));
				$r[4] = array_merge($r[4], array(
					'permission' => 'read'
				));
				$r[5] = array_merge($r[5], array(
					'permission' => 'deny'
				));
				$r[6] = array_merge($r[6], array(
					'permission' => 'deny'
				));
				$r[7] = array_merge($r[7], array(
					'permission' => 'owner'
				));
				$r[8] = array_merge($r[8], array(
					'permission' => 'deny'
				));
                break;
        }
        return $r;
    }

	/**
	 * Get one resource by name.
	 * @param $name the name
	 * @return mixed
	 */
	static function _getByName($name) {
		foreach (self::_get() as $i => $resource ) {
			if ($resource['name'] == $name) {
				return $resource;
			}
		}
		return false;
	}


	/**
     * Get one resource for a given permission
     * @param $r array resources
     * @param $permission string read|share|admin|deny
     * @return mixed
     */
    static function _getByPermission($r, $permission) {
        foreach ($r as $i => $resource ) {
            if ($resource['permission'] == $permission) {
                return $resource;
            }
        }
        return false;
    }

    /**
     * Get one resource by it's UUID
     * @param $r array resources
     * @param $id
     * @return mixed
     */
    static function _getById($r, $id) {
        foreach ($r as $i => $rid) {
            if ($r[$i]['id'] == $id) {
                return $r[$i];
            }
        }
        return false;
    }

    /**
     * Return one resource based on the given conditions
     * @param array $conditions
     * @return array $resource
     * @throws exception
     */
    static function get($conditions) {

        // a user must always be specified
        if (!isset($conditions['user'])) {
            throw new Exception('a user must be specified to get access to a resource fixture');
        }
        $r = self::_getByUsername($conditions['user']);

        // filter by id if needed
        if (isset($conditions['id'])) {
            $r = self::_getById($r, $conditions['id']);
        } else {
            // filter by permission if needed
            if (!isset($conditions['permission']) && $r !== false) {
                $r = $r[0];
            } elseif ($r!==false) {
                $r = self::_getByPermission($r, $conditions['permission']);
            }
        }

        if ($r === false) {
            throw new Exception('a resource fixture could not be found for these conditions, consider adding one');
        }
        return $r;
    }

    /**
     * Get All the resources for a given user
     * @param $conditions array
     * @return array
     * @throws Exception missing user
     */
    static function getAll($conditions) {
        // a user must always be specified
        if (!isset($conditions['user'])) {
            throw new Exception('a user must be specified to get access to a resource fixture');
        }
	    $r = self::_getByUsername($conditions['user']);
	    if (isset($conditions['return_deny']) && $conditions['return_deny'] == false) {
			foreach($r as $k => $resource) {
				if (isset($resource['permission']) && $resource['permission'] == 'deny') {
					unset($r[$k]);
				}
			}
	    }
        return $r;
    }
}