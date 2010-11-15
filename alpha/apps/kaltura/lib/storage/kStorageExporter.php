<?php

class kStorageExporter implements kObjectChangedEventConsumer, kBatchJobStatusEventConsumer
{
	/**
	 * @param BaseObject $object
	 * @param array $modifiedColumns
	 * @return bool true if should continue to the next consumer
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns)
	{
		// if changed object is entry 
		if($object instanceof entry && in_array(entryPeer::MODERATION_STATUS, $modifiedColumns) && $object->getModerationStatus() == entry::ENTRY_MODERATION_STATUS_APPROVED)
		{
			$externalStorages = StorageProfilePeer::retrieveAutomaticByPartnerId($object->getPartnerId());
			foreach($externalStorages as $externalStorage)
			{
				if($externalStorage->getTrigger() == StorageProfile::STORAGE_TEMP_TRIGGER_MODERATION_APPROVED)
					$this->exportEntry($object, $externalStorage);
			}
		}
		
		// if changed object is flavor asset
		if($object instanceof flavorAsset && !$object->getIsOriginal() && in_array(flavorAssetPeer::STATUS, $modifiedColumns) && $object->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_READY)
		{
			$entry = $object->getentry();
			
			$externalStorages = StorageProfilePeer::retrieveAutomaticByPartnerId($object->getPartnerId());
			foreach($externalStorages as $externalStorage)
			{
				if(
					$externalStorage->getTrigger() == StorageProfile::STORAGE_TEMP_TRIGGER_FLAVOR_READY
					||
						(
							$externalStorage->getTrigger() == StorageProfile::STORAGE_TEMP_TRIGGER_MODERATION_APPROVED
							&&
							$entry->getModerationStatus() == entry::ENTRY_MODERATION_STATUS_APPROVED
						)
					)
				{
					$this->exportFlavorAsset($object, $externalStorage);
				}
			}
		}
		return true;
	}

	/**
	 * @param flavorAsset $flavor
	 * @param StorageProfile $externalStorage
	 */
	protected function exportFlavorAsset(flavorAsset $flavor, StorageProfile $externalStorage)
	{
		$flavorParamsIds = $externalStorage->getFlavorParamsIds();
		KalturaLog::log(__METHOD__ . " flavorParamsIds [$flavorParamsIds]");
		
		if(!is_null($flavorParamsIds) && strlen(trim($flavorParamsIds)))
		{
			$flavorParamsArr = explode(',', $flavorParamsIds);
			if(!in_array($flavor->getFlavorParamsId(), $flavorParamsArr))
				return;
		}
			
		$key = $flavor->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$this->export($flavor->getentry(), $externalStorage, $key, !$flavor->getIsOriginal());
				
		return true;
	}
	
	/**
	 * @param entry $entry
	 * @return array<FileSyncKey>
	 */
	protected function getEntrySyncKeys(entry $entry, StorageProfile $externalStorage)
	{
		$exportFileSyncsKeys = array();
		
		$exportFileSyncsKeys[] = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA);
		$exportFileSyncsKeys[] = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_ISM);
		$exportFileSyncsKeys[] = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_ISMC);
		
		$flavorAssets = array();
		$flavorParamsIds = $externalStorage->getFlavorParamsIds();
		KalturaLog::log(__METHOD__ . " flavorParamsIds [$flavorParamsIds]");
		if(is_null($flavorParamsIds) || !strlen(trim($flavorParamsIds)))
		{
			$flavorAssets = flavorAssetPeer::retreiveReadyByEntryId($entry->getId());
		}
		else
		{
			$flavorParamsArr = explode(',', $flavorParamsIds);
			KalturaLog::log(__METHOD__ . " flavorParamsIds count [" . count($flavorParamsArr) . "]");
			$flavorAssets = flavorAssetPeer::retreiveReadyByEntryIdAndFlavorParams($entry->getId(), $flavorParamsArr);
		}
		
		foreach($flavorAssets as $flavorAsset)
			$exportFileSyncsKeys[] = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		
		return $exportFileSyncsKeys;
	}
	
	/**
	 * @param entry $entry
	 * @param FileSyncKey $key
	 */
	protected function export(entry $entry, StorageProfile $externalStorage, FileSyncKey $key, $force = false)
	{
		if(!$this->shouldExport($key, $externalStorage))
		{
			KalturaLog::log(__METHOD__ . " no need to export key [$key] to externalStorage id[" . $externalStorage->getId() . "]");
			return;
		}
			
		$fileSync = kFileSyncUtils::createPendingExternalSyncFileForKey($key, $externalStorage);
		$srcFileSyncLocalPath = kFileSyncUtils::getLocalFilePathForKey($key, true);
		kJobsManager::addStorageExportJob(null, $entry->getId(), $entry->getPartnerId(), $externalStorage, $fileSync, $srcFileSyncLocalPath, $force);
	}
	
	/**
	 * @param FileSyncKey $key
	 * @return bool
	 */
	protected function shouldExport(FileSyncKey $key, StorageProfile $externalStorage)
	{
		KalturaLog::log(__METHOD__ . " - key [$key], externalStorage id[" . $externalStorage->getId() . "]");
		
		list($kalturaFileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($key, false, false);
		if(!$kalturaFileSync) // no local copy to export from
		{
			KalturaLog::log(__METHOD__ . " key [$key] not found localy");
			return false;
		}
		
		KalturaLog::log(__METHOD__ . " validating file size [" . $kalturaFileSync->getFileSize() . "] is between min [" . $externalStorage->getMinFileSize() . "] and max [" . $externalStorage->getMaxFileSize() . "]");
		if($externalStorage->getMaxFileSize() && $kalturaFileSync->getFileSize() > $externalStorage->getMaxFileSize()) // too big
		{
			KalturaLog::log(__METHOD__ . " key [$key] file too big");
			return false;
		}
			
		if($externalStorage->getMinFileSize() && $kalturaFileSync->getFileSize() < $externalStorage->getMinFileSize()) // too small
		{
			KalturaLog::log(__METHOD__ . " key [$key] file too small");
			return false;
		}
		
		$storageFileSync = kFileSyncUtils::getReadyExternalFileSyncForKey($key, $externalStorage->getId());
		if($storageFileSync) // already exported
		{
			KalturaLog::log(__METHOD__ . " key [$key] already exported");
			return false;
		}
			
		return true;
	}
	
	/**
	 * @param entry $entry
	 * @param StorageProfile $externalStorage
	 */
	protected function exportEntry(entry $entry, StorageProfile $externalStorage)
	{
		$checkFileSyncsKeys = $this->getEntrySyncKeys($entry, $externalStorage);
		foreach($checkFileSyncsKeys as $key)
			$this->export($entry, $externalStorage, $key);
	}
	
	/**
	 * @param BatchJob $dbBatchJob
	 * @param unknown_type $entryStatus
	 * @param BatchJob $twinJob
	 * @return bool true if should continue to the next consumer
	 */
	public function updatedJob(BatchJob $dbBatchJob, $entryStatus, BatchJob $twinJob = null)
	{
		if($dbBatchJob->getStatus() != BatchJob::BATCHJOB_STATUS_FINISHED)
			return true;
			
		// convert profile finished - export source flavor
		if($dbBatchJob->getJobType() == BatchJobType::CONVERT_PROFILE)
		{
			$externalStorages = StorageProfilePeer::retrieveAutomaticByPartnerId($dbBatchJob->getPartnerId());
			foreach($externalStorages as $externalStorage)
			{
				if(
					$externalStorage->getTrigger() == StorageProfile::STORAGE_TEMP_TRIGGER_FLAVOR_READY
					||
						(
							$externalStorage->getTrigger() == StorageProfile::STORAGE_TEMP_TRIGGER_MODERATION_APPROVED
							&&
							$dbBatchJob->getEntry()->getModerationStatus() == entry::ENTRY_MODERATION_STATUS_APPROVED
						)
					)
				{
					$sourceFlavor = flavorAssetPeer::retrieveOriginalReadyByEntryId($dbBatchJob->getEntryId());
					if($sourceFlavor)
						$this->exportFlavorAsset($sourceFlavor, $externalStorage);
				}
			}
		}
			
		// convert collection finished - export ism and ismc files
		if($dbBatchJob->getJobType() == BatchJobType::CONVERT_COLLECTION && $dbBatchJob->getJobSubType() == conversionEngineType::EXPRESSION_ENCODER3)
		{
			$entry = $dbBatchJob->getEntry();
			$externalStorages = StorageProfilePeer::retrieveAutomaticByPartnerId($dbBatchJob->getPartnerId());
			foreach($externalStorages as $externalStorage)
			{
				if(
					$externalStorage->getTrigger() == StorageProfile::STORAGE_TEMP_TRIGGER_FLAVOR_READY
					||
						(
							$externalStorage->getTrigger() == StorageProfile::STORAGE_TEMP_TRIGGER_MODERATION_APPROVED
							&&
							$entry->getModerationStatus() == entry::ENTRY_MODERATION_STATUS_APPROVED
						)
					)
				{
					$ismKey = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_ISM);
					if(kFileSyncUtils::fileSync_exists($ismKey))
						$this->export($entry, $externalStorage, $ismKey);
					
					$ismcKey = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_ISMC);
					if(kFileSyncUtils::fileSync_exists($ismcKey))
						$this->export($entry, $externalStorage, $ismcKey);
				}
			}
		}
		return true;
	}
}