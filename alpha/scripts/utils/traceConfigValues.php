<?php

chdir(__DIR__.'/../');
require_once(__DIR__ . '/../bootstrap.php');

//Trace will find the source of the map / key in the configuration sources
if($argc<3)
	die("\nUsage: $argv[0] <map name> <value name> [<host name>] \n".
		"<map name> - case sensitive full name of the map\n".
		"<value name> - case sesitive value search or '*' for entire map\n".
		"[<host name>] - optional case sesitive host name , can be used to search for the values that specific host/s sees\n"
	);

$mapName = $argv[1];
$valueName = $argv[2];
if(isset($argv[3]))
	$_SERVER["HOSTNAME"] = $argv[3];
$usageBefore = kConfCacheManager::getUsage();
print_r($usageBefore);
if($valueName=='*')
	$map = kConf::getMap($mapName);
else
	$map = kConf::get($valueName,$mapName);
if($map)
{
	$usageAfter = kConfCacheManager::getUsage();
	print_r($usageAfter);
	foreach ($usageAfter['usage'] as $key => $value)
	{
		if ($value != $usageBefore['usage'][$key]) print ("\nMap {$mapName} key {$valueName} was found at {$key}\n");
	}
}
else
	die("\nMap {$mapName} key {$valueName} was not found!\n");


