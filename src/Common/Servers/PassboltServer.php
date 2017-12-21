<?php
/**
 * PassboltServer
 * The class to interact with passbolt server/API.
 *
 * @copyright (c) 2017 Passbolt SARL
 * @licence   GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
namespace App\Common\Servers;

use App\Common\Config;
use Exception;
use Httpful\Exception\ConnectionErrorException;
use Httpful\Mime;
use Httpful\Request;

class PassboltServer
{
    /**
     * Reset passbolt installation
     *
     * @return bool
     */
    static public function resetDatabase($url, $dummy = 'tests') 
    {
        try {
            $response = Request::get($url . '/seleniumtests/resetInstance/' . $dummy)
                ->send();
        } catch(ConnectionErrorException $exception) {
            return false;
        }
        $seeCreated = preg_match('/created/', $response->body);
        sleep(2); // Wait for database to be imported (no visible output).
        return $seeCreated;
    }

    /**
     * Add extra server configuration.
     *
     */
    static public function setExtraConfig($config = []) 
    {
        $url = Config::read('passbolt.url') . DS . '/seleniumtests/setExtraConfig';
        $request = Request::post($url, $config);
        try {
            $request->sendsType(Mime::JSON)->send();
        } catch(Exception $exception) {
            return false;
        }
        return true;
    }

    /**
     * Reset extra server configuration
     */
    static public function resetExtraConfig() 
    {
        $url = Config::read('passbolt.url') . DS . '/seleniumtests/resetExtraConfig';
        $request = Request::post($url);
        try {
            $request->sendsType(Mime::JSON)->send();
        } catch(Exception $exception) {
            return false;
        }
        return true;
    }
}
