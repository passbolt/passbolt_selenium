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
                'uri' => 'http://drupal.project1.net/'
            ),
            1 => array (
                'id' => '50d77ffb-d290-49e4-ac86-1b63d7a10fce',
                'name' => 'dp2-pwd1',
                'username' => 'admin',
                'password' => 'princess',
                'uri' => 'http://drupal.project1.net/'
            )
        );
        return $r;
    }

    /**
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
     * @param $username
     * @param $permission
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

        // filter by permission if needed
        if (!isset($conditions['permission']) && $r!==false) {
            $r = $r[0];
        } elseif ($r!==false) {
            $r = self::_getByPermission($r,$conditions['permission']);
        }

        if ($r === false) {
            return $this->fail('a resource fixture could not be found for these conditions, consider adding one');
        }
        return $r;
    }
}