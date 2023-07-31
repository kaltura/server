<?php

if($argc < 2)
{
	echo "Arguments missing.\n\n";
	echo "Usage: php " . __FILE__ . " {mapping} <realrun / dryrun>" . PHP_EOL;
	exit;
}
$mapping = $argv[1];
$dryRun = ($argv[2] === "dryrun");

require_once(__DIR__ . '/../bootstrap.php');

KalturaStatement::setDryRun($dryRun);

$entryMappings = file($mapping, FILE_IGNORE_NEW_LINES);

$counter = 0;
foreach ($entryMappings as $entryMapping)
{
	list ($entryId, $userId) = explode(",", $entryMapping);

	$entry = entryPeer::retrieveByPK($entryId);
	if (!$entry)
	{
		echo "Entry id [$entryId] not found" . PHP_EOL;
		continue;
	}
	$kuser = kuserPeer::getActiveKuserByPartnerAndUid($entry->getPartnerId(), $userId);
	if(!$kuser)
	{
		echo "Kuser with puser ID [$userId] not found" . PHP_EOL;
		continue;
	}

	$entry->setCreatorKuserId($kuser->getId());
	$entry->setCreatorPuserId($userId);
	$entry->save();
	
	kEventsManager::flushEvents();
	kMemoryManager::clearMemory();
}

KalturaLog::debug('Done');
