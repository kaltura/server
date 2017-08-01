<?php
ini_set("memory_limit","1024M");
if($argc != 2)
{
	die ('Path to a file containing a list of entries ids is required.\n');
}
require_once(__DIR__ . '/../bootstrap.php');

$entriesFilePath = $argv[1];
$entries = file ( $entriesFilePath ) or die ( 'Could not read file!' );

foreach ($entries as $deletedEntryId) {
	increaseEntryVersion($deletedEntryId);
}
KalturaLog::debug('Done');



function increaseEntryVersion($entryId)
{
	$entry = entryPeer::retrieveByPK($entryId);
	if(!$entry)
	{
		KalturaLog::debug("Entry id [$entryId] not found" . PHP_EOL);
		return;
	}
	$entry->setCacheFlavorVersion($entry->getCacheFlavorVersion() + 1);
	$entry->save();
}
?>