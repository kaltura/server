<?php
ini_set("memory_limit","1024M");

if ($argc < 2)
{
	die ('Path to a file containg a list of deleted entries ids is required.\n');
}

$entriesFilePath = $argv[1];
$entries = file ( $entriesFilePath ) or die ( 'Could not read file!' );

chdir(__DIR__.'/../');
require_once(__DIR__ . '/../bootstrap.php');

foreach ($entries as $deletedEntryId){
	$deletedEntryId = trim($deletedEntryId);
	$deletedEntry = entryPeer::retrieveByPKNoFilter($deletedEntryId);
	/* @var $deletedEntry entry */
	
	if (!$deletedEntry){
		KalturaLog::debug ('ERROR: couldn\'t find entry id ['.$deletedEntryId.']'); 
		continue;
	}

    if ($deletedEntry->getStatus() != entryStatus::DELETED){
        KalturaLog::debug ('ERROR: entry id ['.$deletedEntryId.'] is not deleted');
        continue;
    }

	KalturaLog::debug('undeleting entry id ['. $deletedEntry->getID().']');

    $entryDeleteTime = new DateTime ($deletedEntry->getUpdatedAt());
    $deletedEntry->setStatus(entryStatus::READY);
  	$deletedEntry->setThumbnail($deletedEntry->getFromCustomData("deleted_original_thumb"), true);
	$deletedEntry->setData($deletedEntry->getFromCustomData("deleted_original_data"),true); //data should be resotred even if it's NULL
    $deletedEntry->save();

	//undelete all entry's file syncs sub types 
	$entryFileSyncKeys = array (); 
	if ($key = $deletedEntry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA)){
		$entryFileSyncKeys[] = $key; 
	}
	if ($key = $deletedEntry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA_EDIT)){
		$entryFileSyncKeys[] = $key; 
	}
	if ($key = $deletedEntry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB)){
		$entryFileSyncKeys[] = $key; 
	}
	if ($key = $deletedEntry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_ARCHIVE)){
		$entryFileSyncKeys[] = $key; 
	}
	if ($key = $deletedEntry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DOWNLOAD)){
		$entryFileSyncKeys[] = $key; 
	}
	if ($key = $deletedEntry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_OFFLINE_THUMB)){
		$entryFileSyncKeys[] = $key; 
	}
	if ($key = $deletedEntry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_ISM)){
		$entryFileSyncKeys[] = $key; 
	}
	if ($key = $deletedEntry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_ISMC)){
		$entryFileSyncKeys[] = $key; 
	}
	if ($key = $deletedEntry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_CONVERSION_LOG)){
		$entryFileSyncKeys[] = $key; 
	}
	foreach ($entryFileSyncKeys as $entryFileSyncKey)
		restoreFileSyncByKey($entryFileSyncKey);

    //Restore cuePoints
    $cuePointCrit = new Criteria();
    $cuePointCrit->add(CuePointPeer::ENTRY_ID, $deletedEntry->getID(), Criteria::EQUAL);
    $cuePointCrit->add(CuePointPeer::STATUS, CuePointStatus::DELETED, Criteria::EQUAL);
    CuePointPeer::setUseCriteriaFilter(false);
    $deletedCuePoints = CuePointPeer::doSelect($cuePointCrit);
    CuePointPeer::setUseCriteriaFilter(true);

    foreach($deletedCuePoints as $deletedCuePoint)
    {
        /* @var $deletedCuePoint cuePoint */
        $cuePointDeleteTime = new DateTime ($deletedCuePoint->getUpdatedAt());
        $timeDiff = $entryDeleteTime->diff($cuePointDeleteTime);

        //we allow a time difference of at most 59 seconds between the entry deletion time and the cue point deletion time
        //because we want to restore only the cue points that were deleted due to the entry deletion
        if ($timeDiff->format('%Y-%m-%d %H:%i') == '00-0-0 00:0') {
            echo('changing status of cuePoint to ready [' . $deletedCuePoint->getId() . ']');
            $deletedCuePoint->setStatus(CuePointStatus::READY);
            $deletedCuePoint->save();
        }
    }

	//Restore assets
	$assetCrit = new Criteria();
	$assetCrit->add(assetPeer::ENTRY_ID, $deletedEntry->getID(), Criteria::EQUAL);
	assetPeer::setUseCriteriaFilter(false);
	$deletedAssets = assetPeer::doSelect($assetCrit);
	assetPeer::setUseCriteriaFilter(true);

	foreach($deletedAssets as $deletedAsset)
	{
		/* @var $deletedAsset asset */
		if ($deletedAsset->getStatus() == asset::ASSET_STATUS_DELETED){
			echo('changing status of asset to ready ['. $deletedAsset->getId().']');
			$deletedAsset->setStatus(asset::ASSET_STATUS_READY);
			$deletedAsset->save();
		}
		
		$assetSyncKey = $deletedAsset->getSyncKey(asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
		restoreFileSyncByKey($assetSyncKey);
		
		$assetConvertLogSyncKey = $deletedAsset->getSyncKey(asset::FILE_SYNC_ASSET_SUB_TYPE_CONVERT_LOG);
		restoreFileSyncByKey($assetConvertLogSyncKey);
	}
	kEventsManager::flushEvents();
	kMemoryManager::clearMemory(); 
}

function restoreFileSyncByKey(FileSyncKey $fileSyncKey){
	KalturaLog::debug("file sync key: $fileSyncKey");
	/* @var $entryFileSyncKey FileSyncKey */
	FileSyncPeer::setUseCriteriaFilter(false);
	$fileSyncs = FileSyncPeer::retrieveAllByFileSyncKey($fileSyncKey);
	FileSyncPeer::setUseCriteriaFilter(true);
	foreach ($fileSyncs as $fileSync){
		if ($fileSync->getStatus() == FileSync::FILE_SYNC_STATUS_DELETED){
			$shouldUnDelete = false; 
			if ($fileSync->getFileType() == FileSync::FILE_SYNC_FILE_TYPE_FILE || $fileSync->getFileType() == FileSync::FILE_SYNC_FILE_TYPE_URL){
				if (file_exists($fileSync->getFullPath()))
					$shouldUnDelete = true;
			}
			else if ($fileSync->getFileType() == FileSync::FILE_SYNC_FILE_TYPE_LINK){
				$linkedId = $fileSync->getLinkedId();
				FileSyncPeer::setUseCriteriaFilter(false);      
				$linkedFileSync = FileSyncPeer::retrieveByPK($linkedId);
				FileSyncPeer::setUseCriteriaFilter(true);		 
				if ($linkedFileSync->getStatus() == FileSync::FILE_SYNC_STATUS_DELETED && file_exists($linkedFileSync->getFullPath()))
					$shouldUnDelete = true; 
			}
			else if ($fileSync->getFileType() == FileSync::FILE_SYNC_FILE_TYPE_CACHE){
				$shouldUnDelete = false;
			}

			if ($shouldUnDelete)
				$fileSync->setStatus(FileSync::FILE_SYNC_STATUS_READY);
			else
				$fileSync->setStatus(FileSync::FILE_SYNC_STATUS_ERROR);
		}
		$fileSync->save(); 
	}
}
