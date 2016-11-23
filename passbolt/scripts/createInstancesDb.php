<?php
/**
 * @file
 *
 * This file contains a PHP script whose mission is to create and populate a Mysql table in the database
 * and table.
 * This table is to be used only in case of parallel tests running simultaneously,
 * to keep a track of which passbolt and selenium instances are free, and which ones are already
 * used by a running test.
 *
 * The database will be pre-populated by the instances listed in the configuration file.
 */
require_once(__DIR__ . '/../../bootstrap.php');
Config::get();

/**
 * Check that the table exists in the database.
 * @param $db
 *
 * @return bool
 */
function checkTableExist(&$db) {
	$table = $db->real_escape_string(Config::read('database.name'));
	$sql = "show tables like '".$table."'";
	$res = $db->query($sql);
	$tableExists = ($res->num_rows > 0);
	return $tableExists;
}

/**
 * Create the initial "instances" table.
 * @param $db
 */
function createTable(&$db) {
	// Create table.
	$result = $db->query(
		'CREATE TABLE instances (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, type varchar(10), address varchar(255), locked INT(6) UNSIGNED)'
	);
}

/**
 * Populate table with data provided in the config file.
 * @param $db
 */
function populateTable(&$db) {
	$db->query("DELETE FROM instances");
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
		$db->query( "INSERT INTO instances (type, address, locked) VALUES('{$instance['type']}', '{$instance['address']}', {$instance['locked']});" );
	}

}

// Open or create DB.
$db = new mysqli(
	Config::read('database.host'),
	Config::read('database.username'),
	Config::read('database.password'),
	Config::read('database.name')
);
// Check for errors
if(mysqli_connect_errno()){
	echo mysqli_connect_error();
}

// Check if table exists.
$tableExists = checkTableExist($db);

// If table doesn't exist, create it and populate it with instances.
if (!$tableExists) {
	createTable($db);
}

populateTable($db);

$nbEntries = $db->query('SELECT COUNT(*) AS nbdata FROM instances');
$nbEntries = $nbEntries->fetch_array()['nbdata'];
echo "Database and table instances have been created, and populated with $nbEntries entries\n";

$db->close();