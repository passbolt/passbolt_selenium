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
use Httpful\Mime;
use Httpful\Request;

class PassboltServer
{

    /**
     * Reset passbolt installation
     *
     * @throws Exception
     * @return bool
     */
    static public function resetDatabase($url, $dummy = 'tests') 
    {
        $response = Request::get($url . '/seleniumtests/resetInstance/' . $dummy)
                       ->send();
        $seeCreated = preg_match('/created/', $response->body);
        sleep(2); // Wait for database to be imported (no visible output).
        return $seeCreated;
    }

    /**
     * Add extra server configuration.
     *
     * @throws Exception
     */
    static public function setExtraConfig($config = []) 
    {
        $url = Config::read('passbolt.url') . DS . '/seleniumtests/setExtraConfig';
        $request = Request::post($url, $config);
        $request->sendsType(Mime::JSON)->send();
    }

    /**
     * Reset extra server configuration.
     *
     * @throws Exception
     */
    static public function resetExtraConfig() 
    {
        $url = Config::read('passbolt.url') . DS . '/seleniumtests/resetExtraConfig';
        $request = Request::post($url);
        $request->sendsType(Mime::JSON)->send();
    }
}
