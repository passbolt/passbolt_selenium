<?php
class Resource {
    static function get($username) {
        switch ($username) {
            case 'betty' :
                return array(
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
        }
    }
}