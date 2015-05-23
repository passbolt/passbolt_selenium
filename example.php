<?php
require_once('lib/__init__.php');

// Check config
if (is_file('config/config.php')) {
	require_once('config/config.php');
} else {
	echo "No configuration found. Please add one as config/config.php\n";
	return;
}

$profile = new FirefoxProfile();
$capabilities = DesiredCapabilities::firefox();
$capabilities->setCapability(FirefoxDriver::PROFILE, $profile);
$driver = RemoteWebDriver::create($config['selenium']['url'], $capabilities, 5000);
$driver->get($config['passbolt']['url']);
echo "The title is " . $driver->getTitle() . "\n";
echo "The current URI is " . $driver->getCurrentURL() . "\n";
$driver->quit();
