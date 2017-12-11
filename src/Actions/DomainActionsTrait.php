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
namespace App\Actions;

use App\Common\Config;
use App\Common\Servers\PassboltServer;

trait DomainActionsTrait
{

    /**
     * Switch config to use secondary domain (for multi domain testing).
     *
     * @throw Exception
     */
    public function switchToSecondaryDomain() 
    {
        Config::write('passbolt.url_primary', Config::read('passbolt.url'));
        Config::write('passbolt.url', Config::read('passbolt.url_secondary'));
        PassboltServer::setExtraConfig(
            [
            'App' => [
                'fullBaseUrl' => Config::read('passbolt.url_secondary')
            ]
            ]
        );
    }

    /**
     * Switch config to use primary domain (for multi domain testing).
     *
     * Switch will happen only if a first switch to secondary domain was done first.
     */
    public function switchToPrimaryDomain() 
    {
        // Switch needs to be done only if a switch to secondary domain was done first.
        if (Config::read('passbolt.url_primary')) {
            // Reset the config with the base url.
            Config::write('passbolt.url', Config::read('passbolt.url_primary'));
            PassboltServer::resetExtraConfig();
        }
    }

}