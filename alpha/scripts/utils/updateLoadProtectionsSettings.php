<?php

if ($argc < 5)
	die("Usage:\n\tphp updateLoadProtectionsSettings.php <memcache host> <memcache port> <operation (add|delete)> <key> <value> \n");

define('MEMCACHE_ADD', "add");
define('MEMCACHE_DELETE', "delete");

$memcacheOperations = array(MEMCACHE_ADD, MEMCACHE_DELETE);

$memcacheHost = $argv[1];
$memcachePort = $argv[2];
$memcacheOperation = $argv[3];

if($memcacheOperation == MEMCACHE_ADD && !isset($argv[5]))
{
	die("Value must be provided when calling the scrpt with add operation");
}

$memcacheKey = $argv[4];
$memcacheValue = $argv[5];

$memcache = new Memcache;
$connection = @$memcache->connect($memcacheHost, $memcachePort);
if (!$connection)
	die('Error: failed to connect to memcache!');

switch ($memcacheOperation)
{
	case MEMCACHE_ADD:
		if ($memcache->add($memcacheKey, $memcacheValue) === false)
		{
			die("Error: failed to set key [$memcacheKey], with value [$memcacheValue]");
		}
		break;
		
	case MEMCACHE_DELETE:
		if ($memcache->delete($memcacheKey) === false)
		{
			die("Error: failed to delete key [$memcacheKey]");
		}
		break;
		
	default:
		die("Error: operation not supported [$memcacheOperation]");
		break;
}

print("Done!\n");
