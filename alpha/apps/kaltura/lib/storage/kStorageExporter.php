<?php
/**
 * @package Core
 * @subpackage storage
 */
class kStorageExporter implements kObjectChangedEventConsumer, kBatchJobStatusEventConsumer, kObjectDeletedEventConsumer
{
	/**
	 * per session cache of kRule->fulfilled result per storage profile and entry id
	 * @var array of kContextDataResult per storage profile and entry id
	 */
	public static $entryContextDataResult = array();
	
	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::shouldConsumeChangedEvent()
	 */
	public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns)
	{
		// if changed object is entry 
		if($object instanceof entry && in_array(entryPeer::MODERATION_STATUS, $modifiedColumns) && $object->getModerationStatus() == entry::ENTRY_MODERATION_STATUS_APPROVED)
			return true;
		
		// if changed object is flavor asset
		if($object instanceof flavorAsset && !$object->getIsOriginal() && in_array(assetPeer::STATUS, $modifiedColumns) && $object->isLocalReadyStatus())
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
					self::exportEntry($object, $externalStorage);
			}
		}
		
		// if changed object is flavor asset
		if($object instanceof flavorAsset && !$object->getIsOriginal() && in_array(assetPeer::STATUS, $modifiedColumns) && $object->isLocalReadyStatus())
		{
			$entry = $object->getentry();
			
			$externalStorages = StorageProfilePeer::retrieveAutomaticByPartnerId($object->getPartnerId());
			foreach($externalStorages as $externalStorage)
			{
				if ($externalStorage->triggerFitsReadyAsset($entry->getId()))
				{
					self::exportFlavorAsset($object, $externalStorage);
				}
			}
		}
		return true;
	}

	/**
	 * @param flavorAsset $flavor
	 * @param StorageProfile $externalStorage
	 */
	static public function exportFlavorAsset(flavorAsset $flavor, StorageProfile $externalStorage)
	{
	    if (!$externalStorage->shouldExportFlavorAsset($flavor)) {
		    return;
		}
			
		$key = $flavor->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$exporting = self::export($flavor->getentry(), $externalStorage, $key, !$flavor->getIsOriginal());
				
		return $exporting;
	}
	
	/**
	 * @param entry $entry
	 * @param FileSyncKey $key
	 */
	static protected function export(entry $entry, StorageProfile $externalStorage, FileSyncKey $key, $force = false)
	{			
		$externalFileSync = kFileSyncUtils::createPendingExternalSyncFileForKey($key, $externalStorage);
		/* @var $fileSync FileSync */
		list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($key,true,false);
		if (!$fileSync || $fileSync->getFileType() == FileSync::FILE_SYNC_FILE_TYPE_URL) {
			KalturaLog::err("no ready fileSync was found for key [$key]");
			return;
		}
		$parent_file_sync = kFileSyncUtils::resolve($fileSync);
		$srcFileSyncPath = $parent_file_sync->getFileRoot() . $parent_file_sync->getFilePath();
		kJobsManager::addStorageExportJob(null, $entry->getId(), $entry->getPartnerId(), $externalStorage, $externalFileSync, $srcFileSyncPath, $force, $fileSync->getDc());
		return true;
	}
	
	/**
	 * @param entry $entry
	 * @param StorageProfile $externalStorage
	 */
	public static function exportEntry(entry $entry, StorageProfile $externalStorage)
	{
		$flavorAssets = assetPeer::retrieveFlavorsByEntryIdAndStatus($entry->getId(), null, array(asset::ASSET_STATUS_READY, asset::ASSET_STATUS_EXPORTING));
		foreach ($flavorAssets as $flavorAsset) 
		{
			self::exportFlavorAsset($flavorAsset, $externalStorage);
		}
		self::exportAdditionalEntryFiles($entry, $externalStorage);		
	}
	
	/**
	 * for each storage profile check if it still fulfills the export rules and decide if it should be exported or deleted
	 * 
	 * @param entry $entry
	 * 
	 */
	public static function reExportEntry(entry $entry)
	{
		if(!PermissionPeer::isValidForPartner(PermissionName::FEATURE_REMOTE_STORAGE_RULE, $entry->getPartnerId()))
			return;
		if($entry->getStatus() == entryStatus::NO_CONTENT)
			return;
				
		$storageProfiles = StorageProfilePeer::retrieveExternalByPartnerId($entry->getPartnerId());
		foreach ($storageProfiles as $profile) 
		{			
			/* @var $profile StorageProfile */
			KalturaLog::debug('Checking entry ['.$entry->getId().']re-export to storage ['.$profile->getId().']');
			$scope = $profile->getScope();
			$scope->setEntryId($entry->getId());
			if($profile->triggerFitsReadyAsset($entry->getId()) && $profile->fulfillsRules($scope))
				self::exportEntry($entry, $profile);
			else 
				self::deleteExportedEntry($entry, $profile);
		}
	}
	
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::shouldConsumeJobStatusEvent()
	 */
	public function shouldConsumeJobStatusEvent(BatchJob $dbBatchJob)
	{
		if($dbBatchJob->getStatus() != BatchJob::BATCHJOB_STATUS_FINISHED)
			return false;
						
		// convert collection finished - export ism and ismc files
		if($dbBatchJob->getJobType() == BatchJobType::CONVERT_COLLECTION && $dbBatchJob->getJobSubType() == conversionEngineType::EXPRESSION_ENCODER3)
			return true;
		
		if($dbBatchJob->getJobType() == BatchJobType::CONVERT_PROFILE)
			return true;
		
		return false;
	}
	
	public static function exportSourceAssetFromJob(BatchJob $dbBatchJob)
	{
		// convert profile finished - export source flavor
		if($dbBatchJob->getJobType() == BatchJobType::CONVERT_PROFILE)
		{
			$externalStorages = StorageProfilePeer::retrieveAutomaticByPartnerId($dbBatchJob->getPartnerId());
			$sourceFlavor = assetPeer::retrieveOriginalByEntryId($dbBatchJob->getEntryId());
			if (!$sourceFlavor) 
			{
			    KalturaLog::debug('Cannot find source flavor for entry id ['.$dbBatchJob->getEntryId().']');
			}
			else if (!$sourceFlavor->isLocalReadyStatus()) 
			{
			    KalturaLog::debug('Source flavor id ['.$sourceFlavor->getId().'] has status ['.$sourceFlavor->getStatus().'] - not ready for export');
			}
			else
			{
    			foreach($externalStorages as $externalStorage)
    			{
    				if ($externalStorage->triggerFitsReadyAsset($dbBatchJob->getEntryId()))
    				{
    				    self::exportFlavorAsset($sourceFlavor, $externalStorage);
    				}
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
				if($externalStorage->triggerFitsReadyAsset($entry->getId()))
				{
					$ismKey = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_ISM);
					if(kFileSyncUtils::fileSync_exists($ismKey))
						self::export($entry, $externalStorage, $ismKey);
					
					$ismcKey = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_ISMC);
					if(kFileSyncUtils::fileSync_exists($ismcKey))
						self::export($entry, $externalStorage, $ismcKey);
				}
			}
		}
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::updatedJob()
	 */
	public function updatedJob(BatchJob $dbBatchJob)
	{
		// convert profile finished - export source flavor
		if ($dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FINISHED)
		{
    		return self::exportSourceAssetFromJob($dbBatchJob);
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
				$asset = assetPeer::retrieveById($object->getId());
				if ($asset)
				{
					$entryId = $asset->getEntryId();
					//the next piece of code checks whether the entry to which
					//the deleted asset belongs to is a "replacement" entry
                    $entry = entryPeer::retrieveByPKNoFilter($entryId);
                    if (!$entry) 
                    {
                    	KalturaLog::alert("No entry found by the ID of [$entryId]");
                    }
                    
                    else if ($entry->getReplacedEntryId())
                    {
                        KalturaLog::info("Will not handle event - deleted asset belongs to replacement entry");
                        return;
                    }
					
				}
				assetPeer::setUseCriteriaFilter(true);
				break;
		}
		
		$storage = StorageProfilePeer::retrieveByPK($object->getDc());
		
		kJobsManager::addStorageDeleteJob($raisedJob, $entryId ,$storage, $object);		
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
	
	/**
	 * export ISM, ISMC and Data files on the entry
	 * 
	 * @param entry $entry
	 * @param StorageProfile $profile
	 */
	protected static function exportAdditionalEntryFiles(entry $entry, StorageProfile $profile)
	{
		$additionalFileSyncKeys = array(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA, entry::FILE_SYNC_ENTRY_SUB_TYPE_ISM, entry::FILE_SYNC_ENTRY_SUB_TYPE_ISMC);
		foreach ($additionalFileSyncKeys as $subType) 
		{
			$key = $entry->getSyncKey($subType);
			if($profile->isValidFileSync($key))
			{
				self::export($entry, $profile, $key);
			}
		}	
	}
	
	/**
	 * delete ISM, ISMC and Data files on the entry from remote storage
	 * 
	 * @param entry $entry
	 * @param StorageProfile $profile
	 */
	protected static function deleteAdditionalEntryFilesFromStorage(entry $entry, StorageProfile $profile)
	{
		$additionalFileSyncKeys = array(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA, entry::FILE_SYNC_ENTRY_SUB_TYPE_ISM, entry::FILE_SYNC_ENTRY_SUB_TYPE_ISMC);
		foreach ($additionalFileSyncKeys as $subType) 
		{
			$key = $entry->getSyncKey($subType);
			if($profile->isExported($key))
			{
				self::delete($entry, $profile, $key);
			}
		}	
	}
	
	/**
	 * 
	 * add DeleteStorage job for key
	 * 
	 * @param entry $entry
	 * @param StorageProfile $profile
	 * @param FileSyncKey $key
	 */
	protected static function delete(entry $entry, StorageProfile $profile, FileSyncKey $key)
	{
		KalturaLog::debug('Start delete storage export');
		
		$externalFileSync = kFileSyncUtils::getReadyPendingExternalFileSyncForKey($key, $profile->getId());
		if(!$externalFileSync)
			return;
			
		$c = new Criteria();
		$c->add ( BatchJobPeer::OBJECT_ID , $externalFileSync->getId() );
		$c->add ( BatchJobPeer::OBJECT_TYPE , BatchJobObjectType::FILE_SYNC );
		$c->add ( BatchJobPeer::JOB_TYPE , BatchJobType::STORAGE_EXPORT );
		$c->add ( BatchJobPeer::JOB_SUB_TYPE , $profile->getProtocol() );
		$c->add ( BatchJobPeer::ENTRY_ID , $entry->getId());
		$c->add (BatchJobPeer::STATUS, array(BatchJob::BATCHJOB_STATUS_RETRY, BatchJob::BATCHJOB_STATUS_PENDING), Criteria::IN);		
		$exportJobs = BatchJobPeer::doSelect( $c );

		if(!$exportJobs)
		{
			kJobsManager::addStorageDeleteJob(null, $entry->getId(), $profile, $externalFileSync);
		}
		else
		{
			foreach ($exportJobs as $exportJob) 
			{
				kJobsManager::abortDbBatchJob($exportJob);
			}
		}
	}
	
	/**
	 * 
	 * for each one of the assets and additional entry files check if it was exported to the external storage
	 * and add DeleteStorage job
	 * 
	 * @param entry $entry
	 * @param StorageProfile $profile
	 */
	public static function deleteExportedEntry(entry $entry, StorageProfile $profile)
	{
		$flavorAssets = assetPeer::retrieveFlavorsByEntryIdAndStatus($entry->getId(), null, array(asset::ASSET_STATUS_READY, asset::ASSET_STATUS_EXPORTING));
		foreach ($flavorAssets as $flavorAsset) 
		{
			$key = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			if($profile->isExported($key))
			{
				self::delete($entry, $profile, $key);
			}
		}
		self::deleteAdditionalEntryFilesFromStorage($entry, $profile);
	}
}