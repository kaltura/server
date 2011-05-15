<?php

// Invalidation keys table
$INVALIDATION_KEYS = array(
	array('table' => "flavor_asset", 		'key' => "concat('flavorAsset:entryId=',@OBJ@.entry_id)"),
	array('table' => "kuser", 				'key' => "concat('kuser:partnerId=',@OBJ@.partner_id,',puserid=',@OBJ@.puser_id)"),
	array('table' => "entry", 				'key' => "concat('entry:id=',@OBJ@.id)"),
	array('table' => "access_control", 		'key' => "concat('accessControl:id=',@OBJ@.id)"),
	array('table' => "permission", 			'key' => "concat('permission:partnerId=',@OBJ@.partner_id)"),
	array('table' => "kuser_to_user_role", 	'key' => "concat('kuserToUserRole:kuserId=',@OBJ@.kuser_id)"),
	array('table' => "category", 			'key' => "concat('category:partnerId=',@OBJ@.partner_id)"),
	array('table' => "file_sync", 			'key' => "concat('fileSync:objectId=',@OBJ@.object_id)"),
);

// Default parameters
$ACTION = 'create';
$HOST_NAME = '127.0.0.1';
$USER_NAME = 'root';
$PASSWORD = '';

// Parse command line
if ($argc > 1)
	$ACTION = $argv[1];

if ($ACTION == 'help')
	die("Usage:\n\tphp query_cache_triggers [<action> [<hostname> [<username> [<password>]]]]\n");

if (!in_array($ACTION, array('create', 'remove')))
	die("Error: Invalid action $ACTION\n");
	
if ($argc > 2)
	$HOST_NAME = $argv[2];
if ($argc > 3)
	$USER_NAME = $argv[3];
if ($argc > 4)
	$PASSWORD = $argv[4];

// Connect to database
$link = mysql_connect($HOST_NAME, $USER_NAME, $PASSWORD)
    or die('Error: Could not connect: ' . mysql_error() . "\n");

// Make sure 'Memcached Functions for MySQL' is installed
mysql_select_db('mysql') or die("Error: Could not select 'mysql' database\n");
$query = "SELECT * FROM func WHERE name='memc_server_count'";
$result = mysql_query($query) or die('Error: Select from func table query failed: ' . mysql_error() . "\n");

if (!mysql_fetch_array($result, MYSQL_ASSOC))
{
	die("Error: 'Memcached Functions for MySQL' not installed\nNote: this script should only be run on multi-datacenter environments.\n");
}

mysql_free_result($result);

// Change database to kaltura
mysql_select_db('kaltura') or die("Error: Could not select 'kaltura' database\n");

// Make sure the memcache server is configured
$query = "SELECT memc_server_count()";
$result = mysql_query($query) or die('Error: Select memc_server_count query failed: ' . mysql_error() . "\n");

$line = mysql_fetch_array($result, MYSQL_NUM);
if (!$line)
{
	die("Unexpected: memc_server_count returned nothing\n");
}

if ($line[0] <= 0)
{
	die("Error: Memcached server not configured\n");
}

mysql_free_result($result);

// Install / remove triggers
foreach ($INVALIDATION_KEYS as $invalidationKey)
{
	$tableName = $invalidationKey['table'];
	$insertUpdateKey = str_replace('@OBJ@', 'NEW', $invalidationKey['key']);
	$deleteKey = str_replace('@OBJ@', 'OLD', $invalidationKey['key']);
	$sqlCommands = array(
		"DROP TRIGGER IF EXISTS {$tableName}_insert_memcache;",
		"DROP TRIGGER IF EXISTS {$tableName}_update_memcache;",
		"DROP TRIGGER IF EXISTS {$tableName}_delete_memcache;",
		);
	if ($ACTION == 'create')
	{
		$sqlCommands[] = "CREATE TRIGGER {$tableName}_insert_memcache AFTER INSERT ON {$tableName} FOR EACH ROW DO memc_set($insertUpdateKey, now());";
		$sqlCommands[] = "CREATE TRIGGER {$tableName}_update_memcache AFTER UPDATE ON {$tableName} FOR EACH ROW DO memc_set($insertUpdateKey, now());";
		$sqlCommands[] = "CREATE TRIGGER {$tableName}_delete_memcache AFTER DELETE ON {$tableName} FOR EACH ROW DO memc_set($deleteKey, now());";
		
		print "Creating triggers on {$tableName}...\n";
	}
	else
	{
		print "Removing triggers on {$tableName}...\n";
	}
	foreach ($sqlCommands as $sqlCommand)
	{
		$result = mysql_query($sqlCommand) or die('Error: Trigger query failed: ' . mysql_error() . "\n");
		if ($result !== true)
		{
			die("Error: Unexpected result returned from mysql_query\n");
		}
	}
}

// Close database connection
mysql_close($link);

print "Done !\n";
?>
