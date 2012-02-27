<?php
/**
 * @package Core
 * @subpackage storage
 */
class kStorageExporter implements kObjectChangedEventConsumer, kBatchJobStatusEventConsumer, kObjectDeletedEventConsumer
{
	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::shouldConsumeChangedEvent()
	 */
	public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns)
	{
		// if changed object is entry 
		if($object instanceof entry && in_array(entryPeer::MODERATION_STATUS, $modifiedColumns) && $object->getModerationStatus() == entry::ENTRY_MODERATION_STATUS_APPROVED)
			return true;
		
		// if changed object is flavor asset
		if($object instanceof flavorAsset && !$object->getIsOriginal() && in_array(assetPeer::STATUS, $modifiedColumns) && $object->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_READY)
			return true;
			
		return false;		
	}
	
	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::objectChanged()
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
		if($object instanceof flavorAsset && !$object->getIsOriginal() && in_array(assetPeer::STATUS, $modifiedColumns) && $object->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_READY)
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
	public function exportFlavorAsset(flavorAsset $flavor, StorageProfile $externalStorage)
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
			$flavorAssets = assetPeer::retrieveReadyByEntryId($entry->getId());
		}
		else
		{
			$flavorParamsArr = explode(',', $flavorParamsIds);
			KalturaLog::log(__METHOD__ . " flavorParamsIds count [" . count($flavorParamsArr) . "]");
			$flavorAssets = assetPeer::retrieveReadyByEntryIdAndFlavorParams($entry->getId(), $flavorParamsArr);
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
			KalturaLog::log("no need to export key [$key] to externalStorage id[" . $externalStorage->getId() . "]");
			return;
		}
			
		$externalFileSync = kFileSyncUtils::createPendingExternalSyncFileForKey($key, $externalStorage);
		/* @var $fileSync FileSync */
		list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($key,true,false);
		if(!$fileSync){
			KalturaLog::err("no ready fileSync was found for key [$key]");
			return;
		}
		$parent_file_sync = kFileSyncUtils::resolve($fileSync);
		$srcFileSyncPath = $parent_file_sync->getFileRoot() . $parent_file_sync->getFilePath();
		kJobsManager::addStorageExportJob(null, $entry->getId(), $entry->getPartnerId(), $externalStorage, $externalFileSync, $srcFileSyncPath, $force, $fileSync->getDc());
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
	public function exportEntry(entry $entry, StorageProfile $externalStorage)
	{
		$checkFileSyncsKeys = $this->getEntrySyncKeys($entry, $externalStorage);
		foreach($checkFileSyncsKeys as $key)
			$this->export($entry, $externalStorage, $key);
	}
	
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::shouldConsumeJobStatusEvent()
	 */
	public function shouldConsumeJobStatusEvent(BatchJob $dbBatchJob)
	{
		if($dbBatchJob->getStatus() != BatchJob::BATCHJOB_STATUS_FINISHED)
			return false;
			
		// convert profile finished - export source flavor
		if($dbBatchJob->getJobType() == BatchJobType::CONVERT_PROFILE)
			return true;
			
		// convert collection finished - export ism and ismc files
		if($dbBatchJob->getJobType() == BatchJobType::CONVERT_COLLECTION && $dbBatchJob->getJobSubType() == conversionEngineType::EXPRESSION_ENCODER3)
			return true;
		
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::updatedJob()
	 */
	public function updatedJob(BatchJob $dbBatchJob, BatchJob $twinJob = null)
	{
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
					$sourceFlavor = assetPeer::retrieveOriginalReadyByEntryId($dbBatchJob->getEntryId());
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
	
	public function objectDeleted(BaseObject $object, BatchJob $raisedJob = null)
	{
		/* @var $object FileSync */
		$syncKey = kFileSyncUtils::getKeyForFileSync($object);
		$entryId = null;
		switch ($object->getObjectType())
		{
			case FileSyncObjectType::ENTRY:
				$entryId = $object->getObjectId();
				break;
				
			case FileSyncObjectType::BATCHJOB:
				BatchJobPeer::setUseCriteriaFilter(false);
				$batchJob = BatchJobPeer::retrieveByPK($object->getObjectId());
				if ($batchJob)
				{
					$entryId = $batchJob->getEntryId();
				}
				BatchJobPeer::setUseCriteriaFilter(true);
				break;
				
			case FileSyncObjectType::ASSET:
				assetPeer::setUseCriteriaFilter(false);
				$asset = assetPeer::retrieveByPK($object->getObjectId());
				if ($asset)
				{
					$entryId = $asset->getEntryId();
					//the next piece of code checks whether the entry to which
					//the deleted asset belongs to is a "replacement" entry
                    $entry = entryPeer::retrieveByPK($entryId);
                    if ($entry->getReplacedEntryId())
                    {
                        KalturaLog::info("Will not handle event - deleted asset belongs to replacement entry");
                        return;
                    }
					
				}
				assetPeer::setUseCriteriaFilter(true);
				break;
		}
		
		$storage = StorageProfilePeer::retrieveByPK($object->getDc());
		
		kJobsManager::addStorageDeleteJob($raisedJob, $entryId ,$storage, $syncKey);		
	}
	
	/**
	 * @param BaseObject $object
	 * @return bool true if the consumer should handle the event
	 */
	public function shouldConsumeDeletedEvent(BaseObject $object)
	{
		
		if ($object instanceof FileSync)
		{
			if ($object->getFileType() == FileSync::FILE_SYNC_FILE_TYPE_URL)
			{
				$storage = StorageProfilePeer::retrieveByPK($object->getDc());
				KalturaLog::debug("storage auto delete policy: ".$storage->getAllowAutoDelete());
				if ($storage->getStatus() == StorageProfile::STORAGE_STATUS_AUTOMATIC && $storage->getAllowAutoDelete())
				{
					return true;
				}
				KalturaLog::debug('Unable to consume deleted event; storageProfile status is not equal to '. StorageProfile::STORAGE_STATUS_AUTOMATIC );
			}
		}
		return false;
	}
}