<?php
require_once (__DIR__ . '/../../bootstrap.php');

$dbConfig = kConf::getDB();

if(!isset($dbConfig['datasources']) || !isset($dbConfig['datasources']['propel']) || !isset($dbConfig['datasources']['propel']['connection']))
{
	echo "Propel datasource not found\n";
	exit(-1);
}

$masterConfig = $dbConfig['datasources']['propel']['connection'];

if(!isset($masterConfig['database']))
{
	echo "Propel datasource database not found\n";
	exit(-1);
}
if(!isset($masterConfig['hostspec']))
{
	echo "Propel datasource hostspec not found\n";
	exit(-1);
}
if(!isset($masterConfig['user']))
{
	echo "Propel datasource user not found\n";
	exit(-1);
}
if(!isset($masterConfig['password']))
{
	echo "Propel datasource password not found\n";
	exit(-1);
}

$database = $masterConfig['database'];
$host = $masterConfig['hostspec'];
$username = $masterConfig['user'];
$password = $masterConfig['password'];
$mysqlQuery = "SELECT CONCAT('UPDATE entry SET display_in_search = ', appear_in_search, ' WHERE partner_id = ', id, ';') FROM partner WHERE id > 99 AND appear_in_search IS NOT NULL ORDER BY id;";
$mysqlExec = "mysql --skip-column-names -h$host -u$username -p$password $database";
$exec = "echo \"$mysqlQuery\" | $mysqlExec | $mysqlExec";

KalturaLog::debug("Executing: $exec");
passthru($exec);
