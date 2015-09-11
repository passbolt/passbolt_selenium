<?php
class User {

    /**
     * @param $name
     * @return array
     */
    static function get($name) {
        $conf = array(
            'TokenColor' => '#ff3a3a',
            'TokenTextColor' => '#ffffff'
        );
        switch ($name) {
            default:
            case 'ada':
                return array_merge($conf,array(
                    'FirstName' => 'Ada',
                    'LastName' => 'Lovelace',
                    'Username' => 'ada@passbolt.com',
                    'MasterPassword' => 'ada@passbolt.com',
                    'TokenCode' => 'ADA',
                    'PrivateKey' => 'ada_private.key'
                ));
                break;
            case 'betty':
                return array_merge($conf,array(
                    'FirstName' => 'Betty',
                    'LastName' => 'Holberton',
                    'Username' => 'betty@passbolt.com',
                    'MasterPassword' => 'betty@passbolt.com',
                    'TokenCode' => 'BET',
                    'PrivateKey' => 'betty_private.key'
                ));
                break;
            case 'carol':
                return array_merge($conf,array(
                    'FirstName' => 'Carol',
                    'LastName' => 'Shaw',
                    'Username' => 'carol@passbolt.com',
                    'MasterPassword' => 'carol@passbolt.com',
                    'TokenCode' => 'CAR',
                    'PrivateKey' => 'carol_private.key'
                ));
                break;
        }
    }
}