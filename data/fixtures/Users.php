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
                    'id' => Uuid::get('user.id.ada'),
	                'name' => 'ada',
                    'FirstName' => 'Ada',
                    'LastName' => 'Lovelace',
                    'Username' => 'ada@passbolt.com',
                    'MasterPassword' => 'ada@passbolt.com',
                    'TokenCode' => 'ADA',
                    'PrivateKey' => 'ada_private.key',
                    'PublicKey'  => 'ada_public.key',
                ));
                break;
            case 'betty':
                return array_merge($conf,array(
                    'id' => Uuid::get('user.id.betty'),
	                'name' => 'betty',
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
                    'id' => Uuid::get('user.id.carol'),
	                'name' => 'carol',
                    'FirstName' => 'Carol',
                    'LastName' => 'Shaw',
                    'Username' => 'carol@passbolt.com',
                    'MasterPassword' => 'carol@passbolt.com',
                    'TokenCode' => 'CAR',
                    'PrivateKey' => 'carol_private.key'
                ));
                break;
	        case 'admin':
		        return array_merge($conf,array(
			        'id' => Uuid::get('user.id.admin'),
			        'name' => 'admin',
			        'FirstName' => 'Admin',
			        'LastName' => 'User',
			        'Username' => 'admin@passbolt.com',
			        'MasterPassword' => 'admin@passbolt.com',
			        'TokenCode' => 'ADM',
			        'PrivateKey' => 'admin_private.key'
		        ));
		        break;

	        /***************************************************
	         *  Definition of non existing users we can
	         *  reuse to create predictive data
	         ***************************************************/

	        case 'john':
		        return array_merge($conf,array(
			        'id' => Uuid::get('johndoe@passbolt.com'),
			        'name' => 'john',
			        'FirstName' => 'John',
			        'LastName' => 'Doe',
			        'Username' => 'johndoe@passbolt.com',
			        'MasterPassword' => 'johndoe@passbolt.com',
			        'TokenCode' => 'JON',
			        'PrivateKey' => Gpgkey::get(['name' => 'johndoe'])['filepath']
		        ));
		        break;
        }
    }
}