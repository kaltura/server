<?php

if($argc != 2)
{
	echo "Arguments missing." . PHP_EOL;
	echo "Usage: php " . _FILE_ . " {entry id} " . PHP_EOL;
	exit;
}
require_once(__DIR__ . '/../bootstrap.php');

$entryId = $argv[1];
$entry = entryPeer::retrieveByPK($entryId);
if(!$entry)
{
	echo "Entry id [$entryId] not found" . PHP_EOL;
	exit(-1);
}

$entry->setCacheFlavorVersion($entry->getCacheFlavorVersion() + 1);
$entry->save();

KalturaLog::debug('Done');
