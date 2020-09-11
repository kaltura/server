<?php
require_once (dirname(__FILE__) . '/../bootstrap.php');

if ($argc < 2)
{
	echo "\n ========= Delete File Sync (change status) by Id ========= \n";
	die (" Missing required parameters:\n php " . $argv[0] . " {file_sync || file_sync,file_sync_2,file_sync_3,... || path/to/file_sync_id_list.txt} [realrun / dryrun]\n\n");
}

if(is_file($argv[1]))
{
	$fileSyncIds = file($argv[1]) or die ('Could not read file from path: ' . $argv[1] . PHP_EOL);
}
elseif (strpos($argv[1], ','))
{
	$fileSyncIds = explode(',', $argv[1]);
}
else
{
	$fileSyncIds = array($argv[1]);
}

$dryRun = true;
if ($argc == 3 && $argv[2] == 'realrun')
{
	$dryRun = false;
}

KalturaStatement::setDryRun($dryRun);
KalturaLog::debug('dryrun value: ['.$dryRun.']');
KalturaLog::info(' ========= Script Started ========= ');

$fileSyncDeletedCounter = 0;
$fileSyncIdNotFound = array();

foreach ($fileSyncIds as $fileSyncId)
{
	$fileSyncId = trim($fileSyncId);
	$fileSync = FileSyncPeer::retrieveByPK($fileSyncId);

	if ($fileSync)
	{
		$fileSync->setStatus(3);
		if (!$dryRun)
		{
			$fileSync->save();
			kEventsManager::flushEvents();
		}
		else
		{
			KalturaLog::notice(' File Sync ID: ' . $fileSync->getId() . ' will change to DELETED');
		}
		$fileSyncDeletedCounter++;
	}
	else
	{
		KalturaLog::notice(' File Sync ID: ' . $fileSyncId . ' was not found');
		$fileSyncIdNotFound[] = $fileSyncId;
	}
}

if (!$dryRun)
{
	KalturaLog::info(' ' . $fileSyncDeletedCounter . ' objects has been deleted');
}
else
{
	KalturaLog::info(' ' . $fileSyncDeletedCounter . ' objects will be deleted');
}

$countFileSyncIdNotFound = count($fileSyncIdNotFound);
if ($countFileSyncIdNotFound > 0)
{
	KalturaLog::info(' ' . $countFileSyncIdNotFound . ' file sync objects were not found, Ids:' . PHP_EOL . print_r($fileSyncIdNotFound, true));
}
KalturaLog::info(' ========= Script Ended ========= ');
