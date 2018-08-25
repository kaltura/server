<?php
chdir(__DIR__.'/../');
require_once(__DIR__ . '/../bootstrap.php');
require_once __DIR__ . '/../../config/cache/remoteMemCacheConf.php';

if($argc != 3)
	die ("Usage : $argv[0] <db user name> <db password>\n");

$dbUserName = $argv[1];
$dbPasssword = $argv[2];

//get map list from cache
include (kEnvironment::getConfigDir().'/configCacheParams.php');
if(!isset($cacheConfigParams))
	die("\nRemote cache cofiguration is no accessible");

$cache = new kMemcacheCacheWrapper;
if(!$cache->init(array('host'=>$cacheConfigParams['remoteMemCacheConf']['host'],
	'port'=>$cacheConfigParams['remoteMemCacheConf']['port'])))
	die ("Fail to connect to cache host {$cacheConfigParams['remoteMemCacheConf']['host']} port {$cacheConfigParams['remoteMemCacheConf']['port']} ");
$mapListInCache = $cache->get(remoteMemCacheConf::MAP_LIST_KEY);

//Find all exsiting map names in DB
$cmdLine = 'mysql -u'.$dbUserName.' -p'.$dbPasssword.' kaltura -e "select map_name , host_name from conf_maps where status=1 group by map_name,host_name;"';
echo "executing: {$cmdLine}\n";
exec($cmdLine, $output);
$mapNames = array();
for($i = 1 ; $i < count($output) ; $i++)
{
	$mapInfo = $output[$i];
	$mapInfoArr = explode("\t", $mapInfo);
	$rawMapName = $mapInfoArr[0];
	$hostNameFilter = isset($mapInfoArr[1]) ?  $mapInfoArr[1] : '';
	//get the latest version of this map
	$cmdLine = 'mysql -u'.$dbUserName.' -p'.$dbPasssword.' kaltura -e "select version,content from conf_maps where conf_maps.map_name=\''.$rawMapName.'\' and conf_maps.host_name=\''.$hostNameFilter.'\' and status=1 order by version desc limit 1 ;"';
	echo "executing: {$cmdLine}\n";
	$output2=array();
	exec($cmdLine, $output2);
	$mapName = $rawMapName.'_'.$hostNameFilter;
	list($version, $content) = explode("\t", $output2[1]);
	if(!isset($mapListInCache[$mapName]))
		echo("\nMap {$mapName} not found in map list in cache\n");
	//check if need to update version
	else if($mapListInCache[$mapName]!=$version)
		echo("\nMap {$mapName} version needs to be updated to {$version}\n");
	else
		echo("\nMap {$mapName} already found in cache with version {$version}\n");

	echo ("\r\nContent - $content.\r\n");

	$mapListInCache[$mapName]=$version;//set version
	$cache->set($mapName,$content);
}
$mapListInCache['UPDATED_AT']=date("Y-m-d H:i:s");
$cache->set(remoteMemCacheConf::MAP_LIST_KEY,$mapListInCache);
//todo reset the generarted key
$chacheKey = baseConfCache::generateKey();
$ret = $cache->set(baseConfCache::CONF_CACHE_VERSION_KEY,$chacheKey);
if($ret)
	echo("\nKey - {$chacheKey} was added to cache successfully\n");
else
	die("\nFailed inserting key to cache\n");