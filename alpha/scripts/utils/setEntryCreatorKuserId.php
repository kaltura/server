<?php

if($argc < 2)
{
	echo "Arguments missing.\n\n";
	echo "Usage: php " . __FILE__ . " {mapping} <realrun / dryrun> \n";
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
	list ($entryId,$userId) = explode(",", $entryMapping);

    $entry = entryPeer::retrieveByPK($entryId);
    $kuser = kuserPeer::getActiveKuserByPartnerAndUid($entry->getPartnerId(), $userId);
    if(!$entry || !$kuser)
	{
		echo "Entry id [$entryId] or kuser with puser ID [$userId] not found\n";
		continue;
	}

    $entry->setCreatorKuserId($kuser->getId());
	$entry->setCreatorPuserId($userId);
	$entry->save();
	
	kEventsManager::flushEvents();
	kMemoryManager::clearMemory();
}

KalturaLog::debug('Done');
