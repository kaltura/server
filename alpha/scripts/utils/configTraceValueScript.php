<?php

chdir(__DIR__.'/../');
require_once(__DIR__ . '/../bootstrap.php');

//values needed required  map / key / hostname
// If key is unknown - supply *
//Trace will find the source of the map / key in the configuration files or remote memcache
// Also it will indicate where it came from in the current request

$mapName = $argv[1];
$valueName = $argv[2];
if(isset($argv[3]))
	$_SERVER["HOSTNAME"] = $argv[3];
if($valueName=='*')
	kConf::getMap($mapName);
else
	kConf::get($valueName,$mapName);

$usage = kConfCacheManager::getUsage();

if($usage['usage']['localStorageConf'])
	print ("\nKey was found at localStorageConf (file system)\n");
if($usage['usage']['finalCacheSource'])
	print ("\nKey was found at finalCacheSource (remote memcache)\n");


