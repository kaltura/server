<?php
ini_set('memory_limit','1024M');

if ($argc < 2)
{
	echo PHP_EOL . ' ---- Restore Deleted Entries ---- ' . PHP_EOL;
	echo ' Execute: php ' . $argv[0] . ' [ /path/to/entries_id_list || entryId_1,entryId_2,.. || entry_id ] [realrun / dryrun]' . PHP_EOL;
	die(' Error: missing entry_ids file, csv or single entry ' . PHP_EOL . PHP_EOL);
}

if (is_file($argv[1]))
{
	$entriesIds = file($argv[1]) or die (' Error: cannot open file at: "' . $argv[1] .'"' . PHP_EOL);
}
elseif (strpos($argv[1], ','))
{
	$entriesIds = explode(',', $argv[1]);
}
elseif (strpos($argv[1],'_'))
{
	$entriesIds[] = $argv[1];
}
else
{
	die (' Error: invalid input supplied at: "' . $argv[1] . '"' . PHP_EOL);
}

require_once (dirname(__FILE__) . '/../bootstrap.php');

$dryRun = true;
if (isset($argv[2]) && $argv[2] == 'realrun')
{
	$dryRun = false;
}

KalturaStatement::setDryRun($dryRun);
KalturaLog::info($dryRun ? 'DRY RUN' : 'REAL RUN');

$count = 0;
$totalEntries = count($entriesIds);

foreach ($entriesIds as $deletedEntryId)
{
	$deletedEntryId = trim($deletedEntryId);
	$deletedEntry = entryPeer::retrieveByPKNoFilter($deletedEntryId);
	/* @var $deletedEntry entry */
	
	if (!$deletedEntry)
	{
		KalturaLog::debug ('ERROR: couldn\'t find entry id ['.$deletedEntryId.']'); 
		continue;
	}
	
	KalturaLog::debug('undeleting entry id ['. $deletedEntry->getID().']');
	if ($deletedEntry->getStatus() == entryStatus::DELETED)
	{
		$deletedEntry->setStatus(entryStatus::READY);
		$deletedEntry->setDefaultModerationStatus();
	}
	
	$deletedEntry->setThumbnail($deletedEntry->getFromCustomData("deleted_original_thumb"), true);
	$deletedEntry->setData($deletedEntry->getFromCustomData("deleted_original_data"),true); //data should be resotred even if it's NULL
	$deletedEntry->save();
	
	//undelete all entry's file syncs sub types
	$syncSubTypes = $deletedEntry::getEntryFileSyncSubTypes();
	$entryFileSyncKeys = array ();
	foreach($syncSubTypes as $syncSubType)
	{
		$key = $deletedEntry->getSyncKey($syncSubType);
		if ($key)
		{
			$entryFileSyncKeys[] = $key;
		}
	}

	foreach ($entryFileSyncKeys as $entryFileSyncKey)
	{
		restoreFileSyncByKey($entryFileSyncKey);
	}
	
	//Restore assets
	$assetCrit = new Criteria();
	$assetCrit->add(assetPeer::ENTRY_ID, $deletedEntry->getID(), Criteria::EQUAL);
	assetPeer::setUseCriteriaFilter(false);
	$deletedAssets = assetPeer::doSelect($assetCrit);
	assetPeer::setUseCriteriaFilter(true);
	
	foreach($deletedAssets as $deletedAsset)
	{
		// CaptionAsset has a check for content in the 'preUpdate' at plugins/content/caption/base/lib/model/CaptionAsset.php:118
		// due to that, before saving we try to fetch ready file_sync (but the script restore file_syncs after we at line #102 below)
		// this will cause caption assets to turn into error (-1) status (but the file_sync will be restored)
		// so for CaptionAsset - skip set asset status ready (-2) before we restored the file_sync
		
		/* @var $deletedAsset asset */
		if ($deletedAsset->getStatus() == asset::ASSET_STATUS_DELETED && !($deletedAsset instanceof CaptionAsset))
		{
			$deletedAsset->setStatus(asset::ASSET_STATUS_READY);
			$deletedAsset->save();
			KalturaLog::debug('Asset id: ' . $deletedAsset->getId() . ' set to READY');
		}

		$assetSyncKey = $deletedAsset->getSyncKey(asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
		restoreFileSyncByKey($assetSyncKey);

		$assetConvertLogSyncKey = $deletedAsset->getSyncKey(asset::FILE_SYNC_ASSET_SUB_TYPE_CONVERT_LOG);
		restoreFileSyncByKey($assetConvertLogSyncKey);
		
		if ($deletedAsset instanceof CaptionAsset)
		{
			$deletedAsset->setStatus(asset::ASSET_STATUS_READY);
			$deletedAsset->save();
			KalturaLog::debug('Asset id: ' . $deletedAsset->getId() . ' set to READY');
		}
	}
	
	kEventsManager::flushEvents();
	kMemoryManager::clearMemory();

	$count++;
	if ($count % 1000 === 0)
	{
		KalturaLog::debug('Currently at: ' . $count . ' out of: ' . $totalEntries);
		KalturaLog::debug('Sleeping for 30 seconds');
		sleep(30);
	}
}

KalturaLog::debug('Script Finished');

function restoreFileSyncByKey(FileSyncKey $fileSyncKey)
{
	KalturaLog::debug('File sync key: ' . $fileSyncKey);

	/* @var $entryFileSyncKey FileSyncKey */
	FileSyncPeer::setUseCriteriaFilter(false);
	$fileSyncs = FileSyncPeer::retrieveAllByFileSyncKey($fileSyncKey);
	FileSyncPeer::setUseCriteriaFilter(true);

	foreach ($fileSyncs as $fileSync)
	{
		/* @var FileSync $fileSync */
		if ($fileSync->getStatus() == FileSync::FILE_SYNC_STATUS_DELETED)
		{
			$shouldUnDelete = false; 
			if ($fileSync->getFileType() == FileSync::FILE_SYNC_FILE_TYPE_FILE || $fileSync->getFileType() == FileSync::FILE_SYNC_FILE_TYPE_URL)
			{
				if (kFile::checkFileExists($fileSync->getFullPath()))
				{
					$shouldUnDelete = true;
				}
			}
			else if ($fileSync->getFileType() == FileSync::FILE_SYNC_FILE_TYPE_LINK)
			{
				$linkedId = $fileSync->getLinkedId();
				FileSyncPeer::setUseCriteriaFilter(false);      
				$linkedFileSync = FileSyncPeer::retrieveByPK($linkedId);
				FileSyncPeer::setUseCriteriaFilter(true);		 
				if ($linkedFileSync->getStatus() == FileSync::FILE_SYNC_STATUS_DELETED && kFile::checkFileExists($linkedFileSync->getFullPath()))
				{
					$shouldUnDelete = true;
				}
			}
			else if ($fileSync->getFileType() == FileSync::FILE_SYNC_FILE_TYPE_CACHE)
			{
				$shouldUnDelete = false;
			}

			if ($shouldUnDelete)
			{
				$fileSync->setStatus(FileSync::FILE_SYNC_STATUS_READY);
			}
			else
			{
				$fileSync->setStatus(FileSync::FILE_SYNC_STATUS_ERROR);
			}
		}
		$fileSync->save(); 
	}
}
