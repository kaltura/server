<?php

if($argc < 3)
{
	echo "Arguments missing.\n\n";
	echo "Usage: php " . __FILE__ . " {mapping} {isDateString} <realrun / dryrun> \n";
	exit;
}
$mapping = $argv[1];
$isDateString = ($argv[2] === "true");
$dryRun = ($argv[3] === "dryrun");

require_once(__DIR__ . '/../bootstrap.php');

KalturaStatement::setDryRun($dryRun);

$entryMappings = file($mapping, FILE_IGNORE_NEW_LINES);

$counter = 0;
foreach ($entryMappings as $entryMapping)
{
	list ($entryId,$createdAt) = explode(",", $entryMapping);
	$createdAt = $isDateString ? strtotime($createdAt) : $createdAt;
	
	$entry = entryPeer::retrieveByPK($entryId);
	if(!$entry)
	{
		echo "Entry id [$entryId] not found\n";
		continue;
	}
	
	$entry->setOriginalCreationDate($entry->getCreatedAt());
	$entry->setCreatedAt($createdAt);
	$entry->setAvailableFrom($createdAt);
	$entry->save();
	
	kEventsManager::flushEvents();
	kMemoryManager::clearMemory();
}

KalturaLog::debug('Done');
