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
namespace App\Common;

class RecordableTestCase extends LoggableTestCase
{
    /**
     * @var string $videoPid video data stream
     */
    public $videoPid;

    /**
     * Get IP address of the current selenium server.
     *
     * @return mixed
     */
    private function __getSeleniumServerIp() 
    {
        $seleniumServerUrl = Config::read('testserver.selenium.url');
        preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $seleniumServerUrl, $ip);
        return $ip[0];
    }

    /**
     * Start recording a video of a test through vnc.
     */
    public function startVideo() 
    {
        $ip = $this->__getSeleniumServerIp();
        $videoPath = Config::read('testserver.selenium.videos.path');

        $cmd = "flvrec.py -o $videoPath/{$this->getTestName()}.flv $ip";
        $outputFile = "/tmp/flvrec_{$this->getTestName()}_output.log";
        $pidFile = "/tmp/flvrec_{$this->getTestName()}_pid.txt";

        exec(sprintf("%s > %s 2>&1 & echo $! > %s", $cmd, $outputFile, $pidFile));
        $pid = file_get_contents("/tmp/flvrec_{$this->getTestName()}_pid.txt");

        $this->videoPid = $pid;
    }

    /**
     * Stop video recording.
     */
    public function stopVideo(string $status)
    {
        if (!isset($this->videoPid)) {
            return;
        }
        $pid = $this->videoPid;
        $outputFile = "/tmp/flvrec_{$this->getTestName()}_output.log";
        $pidFile = "/tmp/flvrec_{$this->getTestName()}_pid.txt";

        exec("kill -9 $pid");
        if (file_exists($outputFile)) {
            unlink($outputFile);
        }
        if (file_exists($pidFile)) {
            unlink($pidFile);
        }

        // Delete video if test is not a failure
        if (Config::read('testserver.selenium.videos.when') == 'onFail' && $status != PHPUnit_Runner_BaseTestRunner::STATUS_FAILURE) {
            // If test is not a failure, we delete the video. We don't need to keep it.
            $videoPath = Config::read('testserver.selenium.videos.path');
            $filePath = "$videoPath/{$this->getTestName()}.flv";
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }

    /**
     * Take screenshot of what's happening on the vnc console of the selenium server.
     */
    public function takeScreenshot() 
    {
        $ip = $this->__getSeleniumServerIp();
        $vncSnapshotBin = Config::read('testserver.selenium.screenshots.binary');
        $screenshotPath = Config::read('testserver.selenium.screenshots.path');

        // Execute command 2 times. The first time, the screen is always blank.
        // I know...
        exec("$vncSnapshotBin $ip $screenshotPath/{$this->getTestName()}.jpg > /dev/null 2>&1");
        exec("$vncSnapshotBin $ip $screenshotPath/{$this->getTestName()}.jpg > /dev/null 2>&1");
    }

    /**
     * Should the test take a screenshot?
     * @return bool
     */
    protected function mustScreenshot() {
        $screenshot = Config::read('testserver.selenium.screenshotOnFail');
        if (!isset($screenshot) || $screenshot === false) {
            return false;
        }
        if ($this->getStatus() >= \PHPUnit_Runner_BaseTestRunner::STATUS_FAILURE) {
            return true;
        }
        return false;
    }

    /**
     * Is a video recording going on?
     * @return bool|mixed
     */
    protected function isVideoRecording() {
        $video = Config::read('testserver.selenium.videoRecord');
        if (!isset($video)) {
            return false;
        }
        return $video;
    }
}