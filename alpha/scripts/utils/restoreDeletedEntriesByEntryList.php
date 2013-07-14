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
	
	KalturaLog::debug('undeleting entry id ['. $deletedEntry->getID().']');
	if ($deletedEntry->getStatus() == entryStatus::DELETED){		
		$deletedEntry->setStatus(entryStatus::READY);
	}
	
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