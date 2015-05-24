<?php
$config = array (
	// the base url to use to access this app
	'testsuite' => array(
		'url' => 'http://192.168.0.102/passbolt_selenium'
	),
	// the url of your passbolt app
	'passbolt' => array (
		'url' => 'http://192.168.0.102/passbolt',
		'registration' => array(
			'public' => TRUE
		)
	),
	// the base url of your selenium server
	'selenium' => array (
		'url' => 'http://192.168.0.102:4444/wd/hub'
	),
	'browsers' => array (
		'default' => 'firefox_with_passbolt_extension',
		'firefox_with_passbolt_extension' => array(
			'name' => 'Firefox with Passbolt extension',
			'type' => 'firefox',
			'extensions' => array(
				'data/extensions/passbolt-firefox-addon.xpi'
			)
		),
		'firefox_vanilla' => array(
			'name' => 'Firefox with no extensions',
			'type' => 'firefox'
		),
		'chrome_vanilla' => array(
			'name' => 'Chrome with no extensions',
			'type' => 'Chrome'
		)
	)
);