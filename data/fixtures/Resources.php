<?php
class Resource {
    /**
     * @return array
     */
    static function _get() {
        $r = array(
            0 => array(
                'id' => '50d77ffb-d254-49e4-ac86-1b63d7a10fce',
                'username' => 'admin',
                'name' => 'dp1-pwd1',
                'password' => 'sunshine',
                'uri' => 'http://drupal.project1.net/',
                'complexity' => 'very weak',
                'description' => 'dp1-pwd1 description'
            ),
            1 => array (
                'id' => '50d77ffb-d290-49e4-ac86-1b63d7a10fce',
                'name' => 'dp2-pwd1',
                'username' => 'admin',
                'password' => 'princess',
                'uri' => 'http://drupal.project1.net/',
                'complexity' => 'very weak',
                'description' => 'dp2-pwd1 description'
            )
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
            case 'betty' :
                $r[0] = array_merge($r[0], array(
                    'permission' => 'read'
                ));
                $r[1] = array_merge($r[1], array(
                    'permission' => 'read'
                ));
                break;
            case 'ada' :
                $r[0] = array_merge($r[0], array(
                    'permission' => 'admin'
                ));
                $r[1] = array_merge($r[1], array(
                    'permission' => 'admin'
                ));
                break;
        }
        return $r;
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
            if ($r[$i]['id'] = $id) {
                return $r[$i];
            }
        }
        return false;
    }

    /**
     * Return one resource based on the given conditions
     * @param array $conditions
     * @return array $resource
     */
    function get($conditions) {

        // a user must always be specified
        if (!isset($conditions['user'])) {
            return $this->fail('a user must be specified to get access to a resource fixture');
        }
        $r = self::_getByUsername($conditions['user']);

        // filter by id if needed
        if (isset($conditions['id'])) {
            $r = self::_getById($r, $conditions['id']);
        } else {
            // filter by permission if needed
            if (!isset($conditions['permission']) && $r!==false) {
                $r = $r[0];
            } elseif ($r!==false) {
                $r = self::_getByPermission($r, $conditions['permission']);
            }
        }

        if ($r === false) {
            return $this->fail('a resource fixture could not be found for these conditions, consider adding one');
        }
        return $r;
    }
}