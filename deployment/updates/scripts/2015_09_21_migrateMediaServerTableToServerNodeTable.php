<?php
require_once (__DIR__ . '/../../bootstrap.php');

define("HOST_NAME_INDEX", "hostname");
define("DC_INDEX", "dc");

function mysqlConnect()
{
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
	
	$link = mysqli_connect($host, $username, $password, $database);
	
	if (!$link) {
		echo "Error: Unable to connect to MySQL." . PHP_EOL;
		echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
		echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
		exit;
	}
	
	return $link;
}

function getWowzaServerNodeDynamicType($link)
{
	$mysqli_result = mysqli_query($link, "select * from dynamic_enum where plugin_name = \"wowza\" and value_name = \"WOWZA_MEDIA_SERVER\" and enum_name = \"serverNodeType\"");
	if($mysqli_result->num_rows > 1)
	{
		echo "wowza media server node dynamic type not found\n";
		exit(-1);
	}
	
	$row = $mysqli_result->fetch_assoc();
	$mysqli_result->free();
	return $row['id'];
}

function validateHostNameDoesNotExist($link, $wowzaHostName, $wowzaServerNodeType)
{
	$res = false;
	
	$mysqli_result = mysqli_query($link, "select * from server_node where host_name = \"$wowzaHostName\" and type = $wowzaServerNodeType");
	
	if($mysqli_result->num_rows > 0)
		$res = true;
	
	$mysqli_result->free();
	
	return $res;
}

function getInsertCommand($wowzaHostName, $wowzaDc, $wowzaServerNodeType)
{
	$t = time();
	$date = date("Y-m-d",$t);
	
	$insertCommand = "insert into server_node set created_at = \"$date\", updated_at = \"$date\", dc = $wowzaDc, name = \"$wowzaHostName\", host_name = \"$wowzaHostName\", type = $wowzaServerNodeType, partner_id = -5, status = 1";
	
	$custom_data = array();
	$custom_data["application_name"] = "kLive";
	
	$serializedCustomData = serialize($custom_data);
	
	$insertCommand .= ", custom_data = '$serializedCustomData'";
	
	return $insertCommand;
}

$link = mysqlConnect();
$wowzaServerNodeType = getWowzaServerNodeDynamicType($link);

$mysqli_result = mysqli_query($link, "SELECT * from media_server");

while ($row = $mysqli_result->fetch_assoc()) 
{
	$wowzaDc = $row[DC_INDEX];
	$wowzaHostName = $row[HOST_NAME_INDEX];
	
	if(!validateHostNameDoesNotExist($link, $wowzaHostName, $wowzaServerNodeType))
	{
		$insertCommand = getInsertCommand($wowzaHostName, $wowzaDc, $wowzaServerNodeType);
		$result = mysqli_query($link, $insertCommand);

		if (!$result) {
			$message  = 'Invalid query: ' . mysql_error() . "\n";
			$message .= 'Whole query: ' . $insertCommand;
			die($message);
		}
	}
}

$mysqli_result->free();
mysqli_close($link);