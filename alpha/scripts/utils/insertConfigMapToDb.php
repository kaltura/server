<?php
if($argc != 8)
	die ("\nUsage : $argv[0] <db user name> <db password> <map name> <host name> <ini file> <justification> <status>\n".
		"<db user name> - User with write permissions\n".
		"<db password> - Password of the ralted user\n".
		"<map name> - Name of the map \n".
		"<host name> - Host name regex (use '#' for wild card)\n".
		"<ini file> - Path to the ini file contaitning the configuration \n".
		"<justification> - Must explain why was it added and by who \n".
		"<status> - 1 enable , 0 disable\n\n");

$dbUserName 	= $argv[1];
$dbPasssword 	= $argv[2];
$rawMapName 	= $argv[3];
$hostNameRegEx 	= $argv[4];
$iniFilePath 	= $argv[5];
$justification 	= $argv[6];
$status 		= $argv[7];

if(empty($rawMapName))
{
	die("\nMap name - must have value, aborting.\n");
}
//read ini file
if(!file_exists($iniFilePath))
{
	die("File {$iniFilePath} not found.");
}

chdir(__DIR__.'/../');
require_once(__DIR__ . '/../bootstrap.php');

$iniFileStr = new Zend_Config_Ini($iniFilePath);
$iniFileArr = $iniFileStr->toArray();
$iniFileJson = json_encode($iniFileArr);

$dbConnection = getPdoConnection();

//get latest version of the map from db
$cmdLine = 'select version from conf_maps where conf_maps.map_name=\''.$rawMapName.'\' and conf_maps.host_name=\''.$hostNameRegEx.'\' order by version desc limit 1 ;';
$output1 = query($dbConnection,$cmdLine);
$version = isset($output1['version']) ? $output1['version'] : 0;
print("Found version - {$version}\r\n");
$iniFileJson = str_replace('\/','/',$iniFileJson);
$iniFileJson = str_replace('"','\"',$iniFileJson);
//insert new map to db
$version++;
$cmdLine = "insert into conf_maps (map_name,host_name,status,version,created_at,remarks,content)values('$rawMapName','$hostNameRegEx',$status,$version,'".date("Y-m-d H:i:s")."','$justification','$iniFileJson');";
$ret = execute($dbConnection,$cmdLine);
if(!$ret)
{
	die('Insert new document to DB failed.');
}

function getPdoConnection()
{
	$dbMap = kConf::getMap('db');
	if(!$dbMap)
	{
		die('Cannot get db.ini map from configuration!');
	}
	$defaultSource = $dbMap['datasources']['default'];
	$dbConfig = $dbMap['datasources'][$defaultSource]['connection'];
	$dsn = $dbConfig['dsn'];
	$user = $dbConfig['user'];
	$password = $dbConfig['password'];
	$connection = new PDO($dsn, $user, $password);
	return $connection;
}
function query($dbConnection,$commandLine)
{
	echo "executing: {$commandLine}\n";
	$statement = $dbConnection->query($commandLine);
	$output1 = $statement->fetch();
	return $output1;
}
function execute($dbConnection,$commandLine)
{
	echo "executing: {$commandLine}\n";
	$dbConnection->beginTransaction();
	$statement= $dbConnection->prepare($commandLine);
	$statement->execute();
	return $dbConnection->commit();
}