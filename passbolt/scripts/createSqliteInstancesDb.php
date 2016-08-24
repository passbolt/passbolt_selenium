<?php
require_once(__DIR__ . '/../Config.php');
Config::get();

$pathToDb = ROOT . DS . 'tmp' . DS . 'instances.db';

function checkTableExist(&$db) {
	// Check if table exists.
	$result = $db->query("SELECT count(*) AS exist FROM sqlite_master WHERE type='table' AND name='instances';")
		->fetchArray();

	$tableExists = $result['exist'] == '1';
	return $tableExists;
}

function createTable(&$db) {
	// Create table.
	$db->query(
		'CREATE TABLE instances (id INTEGER PRIMARY KEY, type varchar(10), address varchar(255), locked INTEGER)'
	);
}

function populateTable(&$db) {
	$db->exec("DELETE FROM instances");
	// Populate table.
	$passboltInstances = Config::read('passbolt.instances');
	$seleniumInstances = Config::read('testserver.selenium.instances');
	foreach ($passboltInstances as $instance) {
		$instancesToSave[] = ['type' => 'passbolt', 'address' => $instance, 'locked' => 0];
	}
	foreach ($seleniumInstances as $instance) {
		$instancesToSave[] = ['type' => 'selenium', 'address' => $instance, 'locked' => 0];
	}

	foreach ($instancesToSave as $instance) {
		$db->exec( "INSERT INTO instances (id, type, address, locked) VALUES(NULL, '{$instance['type']}', '{$instance['address']}', {$instance['locked']});" );
	}

}

// Open or create DB.
$db = new SQLite3($pathToDb);
$db->busyTimeout(5000);

// Check if table exists.
$tableExists = checkTableExist($db);

// If table doesn't exist, create it and populate it with instances.
if (!$tableExists) {
	createTable($db);
}

populateTable($db);

$db->close();