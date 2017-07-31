<?php

if($argc != 2)
{
	echo "Arguments missing.\n\n";
	echo "Usage: php " . _FILE_ . " {entry id} \n";
	exit;
}
require_once(__DIR__ . '/../bootstrap.php');

$entryId = $argv[1];
$entry = entryPeer::retrieveByPK($entryId);
if(!$entry)
{
	echo "Entry id [$entryId] not found\n";
	exit(-1);
}

$entry->setCacheFlavorVersion($entry->getCacheFlavorVersion() + 1);
$entry->save();

KalturaLog::debug('Done');
?>