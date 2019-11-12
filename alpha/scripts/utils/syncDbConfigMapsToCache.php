<?php

if($argc != 3)
	die ("Usage : $argv[0] <cache port> <comma seperated cache host list> \n");

chdir(__DIR__.'/../');
require_once(__DIR__ . '/../bootstrap.php');
require_once __DIR__ . '/../../config/cache/kRemoteMemCacheConf.php';

$port = $argv[1];
$cacheHostList = explode(',',$argv[2]);
const DB_MAP_NAME = 'syncdb';
//Init all cache items
$cacheObjects = array();
foreach ($cacheHostList as $cacheHost)
{
	$cacheObject = new kInfraMemcacheCacheWrapper();
	$ret = $cacheObject->init(array('host'=>$cacheHost ,'port'=>$port));
	if(!$ret)
		die ("Failed to connect to cache host {$cacheHost} port {$port} ");
	$cacheObjects[] = $cacheObject;
}

//Load existing map
$mapListInCache = $cacheObjects[0]->get(kRemoteMemCacheConf::MAP_LIST_KEY);

$dbConnection = getPdoConnection();
//Find all exsiting map names in DB
$cmdLine = 'select map_name , host_name from conf_maps where status=1 group by map_name,host_name;';
$mapsInfo = query($dbConnection,$cmdLine);
foreach($mapsInfo as $mapInfo)
{
	$rawMapName =$mapInfo['map_name'];
	if(trim($rawMapName)=='')
	{
		continue;
	}
	$hostNameFilter = $mapInfo['host_name'];
	//get the latest version of this map
	$cmdLine = "select version,content from conf_maps where conf_maps.map_name='$rawMapName' and conf_maps.host_name='$hostNameFilter' and status=1 order by version desc limit 1 ;";

	$output2 = query($dbConnection,$cmdLine);
	$mapName = $rawMapName.kRemoteMemCacheConf::MAP_DELIMITER.$hostNameFilter;
	$version = $output2[0]['version'];
	$content = $output2[0]['content'];
	if(!isset($mapListInCache[$mapName]))
		echo("\nNOTICE - Map {$mapName} is new! adding it to the list in cache\n");
	//check if need to update version
	else if($mapListInCache[$mapName]!=$version)
		echo("\nNOTICE - Map {$mapName} version needs to be updated to {$version}\n");
	else
		echo("\nINFO - Map {$mapName} already found in cache with version {$version}\n");

	$mapListInCache[$mapName]=$version;//set version
	foreach ($cacheObjects as $cacheObject)
	{
		$cacheObject->set(kBaseConfCache::CONF_MAP_PREFIX.$mapName,$content);
	}
}

//Set map list to all cache items
$mapListInCache['UPDATED_AT']=date("Y-m-d H:i:s");
foreach ($cacheObjects as $cacheObject)
{
	$cacheObject->set(kRemoteMemCacheConf::MAP_LIST_KEY, $mapListInCache);
}

//Set key in all cache items
$chacheKey = kBaseConfCache::generateKey();
foreach ($cacheObjects as $cacheObject)
{
	$ret = $cacheObject->set(kBaseConfCache::CONF_CACHE_VERSION_KEY, $chacheKey);
	if(!$ret)
		die ("\nFailed inserting key to cache\n");
	print_r($cacheObject);
	echo("\nKey - {$chacheKey} was added to cache successfully\n");
}


function getPdoConnection()
{
	$dbMap = kConf::getMap(DB_MAP_NAME);
	if(!$dbMap)
	{
		die("Cannot get DB_MAP_NAME map from configuration!");
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
	$output1 = $statement->fetchAll();
	return $output1;
}
