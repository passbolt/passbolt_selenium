<?php
/**
 * Passbolt ~ Open source password manager for teams
 * Copyright (c) Passbolt SARL (https://www.passbolt.com)
 *
 * Licensed under GNU Affero General Public License version 3 of the or any later version.
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Passbolt SARL (https://www.passbolt.com)
 * @license   https://opensource.org/licenses/AGPL-3.0 AGPL License
 * @link      https://www.passbolt.com Passbolt(tm)
 * @since     2.0.0
 */
namespace App\Common\Servers;

use mysqli;
use Exception;
use App\Common\Config;

class ServerRegistry
{
    /**
     * Get a DB connection
     *
     * @return mysqli
     */
    static private function __openDb()
    {
        // Open or create DB.
        $db = new mysqli(
            Config::read('database.host'),
            Config::read('database.username'),
            Config::read('database.password'),
            Config::read('database.name')
        );
        // Check for errors
        if(mysqli_connect_errno()) {
            echo mysqli_connect_error();
        }
        return $db;
    }

    /**
     * @param string $type
     * @return mixed
     * @throws Exception
     */
    static public function reserveInstance($type = 'passbolt')
    {

        $db = self::__openDb();

        // lock table
        $db->query("LOCK TABLES instances WRITE");

        // Lock free instance.
        $lockId = rand(1, 10000);
        $db->query("SET autocommit=0");
        $query = "SELECT * FROM instances WHERE type='$type' AND locked=0 ORDER BY id LIMIT 1";
        $freeInstance = $db->query($query);
        if (!$freeInstance) {
            $db->query("UNLOCK TABLES");
            $db->close();
            throw new Exception('error in select query: ' . $freeInstance->error);
        }
        elseif($freeInstance->num_rows == 0) {
            $db->query("UNLOCK TABLES");
            $db->close();
            throw new Exception('Could not retrieve a free instance');
        }

        $freeInstance = $freeInstance->fetch_array();
        $id = $freeInstance['id'];

        $query = $db->query("UPDATE  instances SET locked=1 WHERE id=$id");
        if (!$query) {
            $db->query("UNLOCK TABLES");
            $db->close();
            throw new Exception('error with update query: ' . $db->error);
        }

        // Unlock table.
        $db->query("UNLOCK TABLES");
        $db->close();

        if ($type == 'passbolt') {
            // Write instance url in config.
            Config::write('passbolt.url', $freeInstance['address']);
        }
        elseif ($type == 'selenium') {
            Config::write('testserver.selenium.url', $freeInstance['address']);
        }

        // Return it.
        return $freeInstance['address'];
    }

    /**
     * Release an instance. (in case of parallelization).
     *
     * @param  string $type passbolt|selenium
     * @throws Exception if it cannot release the instance
     */
    static public function releaseInstance($type)
    {
        $db = self::__openDb();

        if ($type == 'passbolt') {
            $url = Config::read('passbolt.url');
        }
        elseif ($type == 'selenium') {
            $url = Config::read('testserver.selenium.url');
        }
        // lock table
        $db->query("SET autocommit=0");
        $db->query("LOCK TABLES instances WRITE");
        // Remove lock on instance.
        $query = "UPDATE instances SET locked=0 WHERE address='{$url}' and type='$type'";
        $query = $db->query($query);
        if (!$query) {
            throw new Exception('Could not release instance on Update query');
        }
        // Unlock table.
        $db->query("UNLOCK TABLES");
        $db->close();
    }

}