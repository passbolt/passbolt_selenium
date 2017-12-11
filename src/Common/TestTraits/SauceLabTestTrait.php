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
namespace App\Common\TestTraits;

use App\Common\Config;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Sauce\Sausage\SauceAPI;

trait SauceLabTestTrait
{

    abstract public function getTestName() : string;

    protected $_saucelabs = null;

    /**
     * @var SauceAPI $_sauceAPI
     */
    protected $_sauceAPI ;
    protected $_sauceLabJob;

    /**
     * Tell if the test are running in saucelab
     *
     * @return bool|null
     */
    public function isSauceLabTest() 
    {
        if(!isset($this->_saucelabs)) {
            $this->_saucelabs = (Config::read('testserver.default') == 'saucelabs');
        }
        return $this->_saucelabs;
    }

    public function setupSaucelab() 
    {
        $this->_sauceAPI = new SauceAPI(Config::read('testserver.saucelabs.username'), Config::read('testserver.saucelabs.key'));
        $this->_sauceLabJob = $this->getSauceLabJob();
    }

    /**
     * Update test status on saucelabs.
     */
    public function updateTestStatus()
    {
        if (isset($this->_sauceLabJob) && is_array($this->_sauceLabJob)) {
            $this->_sauceAPI->updateJob(
                $this->_sauceLabJob['id'],
                array('passed' => $this->hasFailed() ? false : true)
            );
        }
    }

    /**
     * Get saucelab job corresponding to current test through rest API.
     *
     * @return mixed array Sauce lab job object or false
     */
    public function getSauceLabJob()
    {
        $jobs = $this->_sauceAPI->getJobs(0, null, 10)['jobs'];
        foreach ($jobs as $job) {
            if ($job['name'] == $this->testName) {
                return $job;
            }
        }
        return false;
    }

    /**
     * Update capabilities with some saucelab sauce
     *
     * @param  DesiredCapabilities $capabilities
     * @return DesiredCapabilities
     */
    protected function _setSauceLabCapabilities(DesiredCapabilities $capabilities) 
    {
        if ($this->isSauceLabTest()) {
            $build = getenv('BUILD');
            if (!$build) {
                $build = time();
                putenv("BUILD=$build");
            }
            $capabilities->setCapability('build', $build);

            // Set test name.
            $capabilities->setCapability('name', $this->getTestName());

            // Define specific saucelab capabilities.
            $sauceLabCapabilities = Config::read('testserver.saucelabs.capabilities');
            if (!empty($sauceLabCapabilities)) {
                foreach($sauceLabCapabilities as $capabilityName => $capabilityValue) {
                    $capabilities->setCapability($capabilityName, $capabilityValue);
                }
            }
        }
        return $capabilities;
    }
}