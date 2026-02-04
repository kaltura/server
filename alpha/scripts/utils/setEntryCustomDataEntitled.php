<?php
require_once('/opt/kaltura/app/alpha/scripts/bootstrap.php');
echo "Starting script to update entitlement puser for entry\n";

define('SLEEP_INTERVAL_IN_SEC', 30);


if ($argc < 3) {
	echo ' Execute: php ' . $argv[0] . 'puser_id' . ' [ /path/to/entry_id_list || entryId_1,entryID_2,.. || entry_id ] [realrun / dryrun]' . PHP_EOL;
	die(' Error: missing entry_ids file, csv or single group ' . PHP_EOL . PHP_EOL);
}

$userId = $argv[1];

if (is_file($argv[2])) {
	$entriesIds = file($argv[2]) or die (' Error: cannot open file at: "' . $argv[2] . '"' . PHP_EOL);
} elseif (strpos($argv[2], ',')) {
	$entriesIds = explode(',', $argv[2]);
} elseif (strpos($argv[2], '_')) {
	$entriesIds[] = $argv[2];
} else {
	die (' Error: invalid input supplied at: "' . $argv[1] . '"' . PHP_EOL);
}


$dryRun = true;
if (isset($argv[3]) && $argv[3] == 'realrun')
{
	$dryRun = false;
}

KalturaStatement::setDryRun($dryRun);
KalturaLog::info($dryRun ? 'DRY RUN' : 'REAL RUN');


$counter = 0;
$totalEntries = count($entriesIds);

foreach ($entriesIds as $entryId)
{
	$entry = entryPeer::retrieveByPK($entryId);
	echo "Entry id [$entryId]";
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
	echo "start updating entitles for puser ID [$userId] on entry [$entryId] \n";

	$entitledUserPuser = array();
	$entitledUserPuser[$kuser->getId()] = $kuser->getPuserId();

	$entry->putInCustomData("entitledUserPuserEdit", serialize($entitledUserPuser) );
	$entry->putInCustomData("entitledUserPuserPublish", serialize($entitledUserPuser));
	$entry->putInCustomData("entitledUserPuserView", serialize($entitledUserPuser));

	$entry->save();

	echo "finish updating entitles for puser ID [$userId] on entry [$entryId] \n";

	kEventsManager::flushEvents();
	kMemoryManager::clearMemory();

	$counter++;
	if ($counter % 1000 === 0) {
		KalturaLog::debug('Currently at: ' . $counter . ' out of: ' . $totalEntries);
		KalturaLog::debug('Sleeping for '. SLEEP_INTERVAL_IN_SEC . ' seconds');
		sleep(SLEEP_INTERVAL_IN_SEC);
	}
}

KalturaLog::debug('Done');
