<?php
ini_set("memory_limit","1024M");
if($argc != 2)
{
	die ('Path to a file containing a list of entries ids is required.' . PHP_EOL);
}
require_once(__DIR__ . '/../bootstrap.php');


$entries = file ( $argv[1] ) or die ( 'Could not read file!' );
foreach ($entries as $entryId)
	increaseEntryVersion(trim($entryId));

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