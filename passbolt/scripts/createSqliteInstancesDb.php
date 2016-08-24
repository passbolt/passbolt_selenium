<?php
/**
 * @file
 *
 * This file contains a PHP script whose mission is to create a SQLite database
 * and table.
 * This table is to be used only in case of parallel tests running simultaneously,
 * to keep a track of which passbolt and selenium instances are free, and which ones are already
 * used by a running test.
 *
 * The database will be pre-populated bny the instances listed in the configuration file.
 */
require_once(__DIR__ . '/../../bootstrap.php');
Config::get();

$pathToDb = ROOT . DS . 'tmp' . DS . 'instances.db';

/**
 * Check that the table exists in the database.
 * @param $db
 *
 * @return bool
 */
function checkTableExist(&$db) {
	// Check if table exists.
	$result = $db->query("SELECT count(*) AS exist FROM sqlite_master WHERE type='table' AND name='instances';")
		->fetchArray();

	$tableExists = $result['exist'] == '1';
	return $tableExists;
}

/**
 * Create the initial "instances" table.
 * @param $db
 */
function createTable(&$db) {
	// Create table.
	$db->query(
		'CREATE TABLE instances (id INTEGER PRIMARY KEY, type varchar(10), address varchar(255), locked INTEGER)'
	);
}

/**
 * Populate table with data provided in the config file.
 * @param $db
 */
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

$nbEntries = $db->querySingle('SELECT COUNT(*) AS nbdata FROM instances');
echo "Database and table instances have been created, and populated with $nbEntries entries\n";

$db->close();