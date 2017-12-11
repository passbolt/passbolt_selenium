<?php
/**
 * Test Suite Config
 * A singleton utility class to help access config value
 * Works pretty much like Cakephp Configure class
 *
 * @copyright (c) 2017 Passbolt SARL
 * @licence   GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
namespace App\Common;

use App\lib\Cakephp\Hash;
use InvalidArgumentException;

class Config
{

    protected static $_default_path = '/config/config.php';
    protected static $_path;     // the config file path
    protected static $_config; // the config object

    /**
     * Bootstrap the singleton and read the config from file
     *
     * @var    path to config file
     * @throws \Exception config object not found if $config is not set in config file
     * @throws \Exception config file not found if $path is leading nowhere
     */
    private static function bootstrap($path = null) 
    {
        if(!isset($path)) {
            $path = ROOT . self::$_default_path;
        }
        self::$_path = $path;

        if (is_file(self::$_path)) {
            include self::$_path;
            if(!isset($config)) {
                throw new \Exception('ERROR The config object was not found in '. self::$_path);
            } else {
                self::$_config = $config;
            }
        } else {
            throw new \Exception('ERROR The config file not found at' . self::$_path);
        }
    }

    /**
     * Read a configuration value
     *
     * @throws InvalidArgumentException
     * @return mixed config array or string
     */
    public static function read($path) 
    {
        return Hash::get(self::$_config, $path);
    }

    /**
     * Write a configuration value
     *
     * @return array
     */
    public static function write($path, $value) 
    {
        self::$_config = Hash::insert(self::$_config, $path, $value);
        return self::$_config;
    }

    /**
     * Returns the config path if set
     *
     * @return path.
     */
    public static function getConfigPath() 
    {
        return self::$_path;
    }

    /**
     * Returns the Singleton instance of this class.
     *
     * @var       path to config file
     * @staticvar Singleton $instance The Singleton instances of this class
     * @throws    \Exception
     * @return    Config The Singleton instance.
     */
    public static function get($path = null) 
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new static();
            $instance::bootstrap($path);
        }
        return $instance;
    }

    /**
     * Protected constructor to prevent creating a new instance of the
     * Singleton via the `new` operator from outside of this class.
     */
    protected function __construct() 
    {
    }

    /**
     * Private clone method to prevent cloning of the instance of the
     * Singleton instance.
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Private unserialize method to prevent unserializing of the Singleton
     * instance.
     *
     * @return void
     */
    private function __wakeup()
    {
    }
}