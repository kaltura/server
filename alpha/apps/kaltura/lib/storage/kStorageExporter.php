<?php
/**
 * @package Core
 * @subpackage storage
 */
class kStorageExporter implements kObjectChangedEventConsumer, kBatchJobStatusEventConsumer, kObjectDeletedEventConsumer, kObjectAddedEventConsumer
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
		if($object instanceof entry && PermissionPeer::isValidForPartner(PermissionName::FEATURE_REMOTE_STORAGE, $object->getPartnerId()) && in_array(entryPeer::MODERATION_STATUS, $modifiedColumns) && $object->getModerationStatus() == entry::ENTRY_MODERATION_STATUS_APPROVED)
			return true;
		
		// if changed object is flavor asset or thumb asset
		if(($object instanceof flavorAsset || $object instanceof thumbAsset) && PermissionPeer::isValidForPartner(PermissionName::FEATURE_REMOTE_STORAGE, $object->getPartnerId()) && in_array(assetPeer::STATUS, $modifiedColumns) && $object->isLocalReadyStatus())
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
				{
					self::tryExportEntry($object, $externalStorage);
				}
			}
		}
		
		// if changed object is flavor asset
		if ( ($object instanceof flavorAsset || $object instanceof thumbAsset) && in_array(assetPeer::STATUS, $modifiedColumns) && $object->isLocalReadyStatus())
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

	public function shouldConsumeAddedEvent(BaseObject $object)
	{
		if( $object instanceof thumbAsset && PermissionPeer::isValidForPartner(PermissionName::FEATURE_REMOTE_STORAGE, $object->getPartnerId()) && $object->isLocalReadyStatus())
			return true;
		
	}

	public function objectAdded(BaseObject $object, BatchJob $raisedJob = null)
	{
		$externalStorages = StorageProfilePeer::retrieveAutomaticByPartnerId($object->getPartnerId());
		foreach($externalStorages as $externalStorage)
		{
			if ($externalStorage->triggerFitsReadyAsset($object->getEntryId()))
			{
				self::exportFlavorAsset($object, $externalStorage);
			}
		}
		return true;
	}


	/**
	 * @param flavorAsset $flavor
	 * @param StorageProfile $externalStorage
	 */
	static public function exportFlavorAsset(asset $flavor, StorageProfile $externalStorage)
	{
	    if (!$externalStorage->shouldExportFlavorAsset($flavor)) {
		    return;
		}
			
		$exporting = false;
		$keys = array(
		    		$flavor->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET), 
		    		$flavor->getSyncKey(flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ISM), 
		    		$flavor->getSyncKey(flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ISMC));
		foreach ($keys as $key) 
		{
			if($externalStorage->shoudlExportFileSync($key))
			{		
                $exporting = self::export($flavor->getentry(), $externalStorage, $key, !$flavor->getIsOriginal());
			}			
		}
				
		return $exporting;
	}
	
	/**
	 * @param entry $entry
	 * @param FileSyncKey $key
	 */
	static protected function export(entry $entry, StorageProfile $externalStorage, FileSyncKey $key, $force = false)
	{			
		/* @var $fileSync FileSync */
		list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($key,true,false);
		if (!$fileSync || $fileSync->getFileType() == FileSync::FILE_SYNC_FILE_TYPE_URL) {
			KalturaLog::info("no ready fileSync was found for key [$key]");
			return;
		}
		
		$externalFileSync = kFileSyncUtils::createPendingExternalSyncFileForKey($key, $externalStorage, $fileSync->getIsDir());
		
		$srcFileSync = kFileSyncUtils::resolve($fileSync);
		kJobsManager::addStorageExportJob(null, $entry->getId(), $entry->getPartnerId(), $externalStorage, $externalFileSync, $srcFileSync, $force, $fileSync->getDc());
		return true;
	}
	
	/**
	 * @param entry $entry
	 * @param StorageProfile $externalStorage
	 */
	public static function tryExportEntry(entry $entry, StorageProfile $externalStorage)
	{
		try 
		{
			self::exportEntry($entry,$externalStorage);
			return true;
		}
		catch (kCoreException $e)
		{
			if ($e->getCode()==kCoreException::PROFILE_STATUS_DISABLED)
			{
				KalturaLog::info("Profile status disabled exportEntry will not be called [{$entry->getId()}]");
			}
			return false;
		}
	}
	
	/**
	 * @param entry $entry
	 * @param StorageProfile $externalStorage
	 */
	public static function exportEntry(entry $entry, StorageProfile $externalStorage)
	{
		if($externalStorage->getStatus()==StorageProfile::STORAGE_STATUS_DISABLED)
		{
			throw new kCoreException("Export entry operation failed since profile status is disabled",kCoreException::PROFILE_STATUS_DISABLED);
		}
		
        $flavorAssets = assetPeer::retrieveFlavorsByEntryIdAndStatus($entry->getId(), null, array(asset::ASSET_STATUS_READY, asset::ASSET_STATUS_EXPORTING));
		foreach ($flavorAssets as $flavorAsset) 
		{
			self::exportFlavorAsset($flavorAsset, $externalStorage);
		}
		
		$thumbFlavorAssets = assetPeer::retrieveReadyThumbnailsByEntryId($entry->getId());
		foreach ($thumbFlavorAssets as $thumbFlavorAsset)
		{
			self::exportFlavorAsset($thumbFlavorAsset, $externalStorage);
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
				self::tryExportEntry($entry, $profile);
			else 
				self::deleteExportedEntry($entry, $profile);
		}
	}
	
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::shouldConsumeJobStatusEvent()
	 */
	public function shouldConsumeJobStatusEvent(BatchJob $dbBatchJob)
	{
		if(!PermissionPeer::isValidForPartner(PermissionName::FEATURE_REMOTE_STORAGE, $dbBatchJob->getPartnerId()))
			return false;
		
		if($dbBatchJob->getStatus() != BatchJob::BATCHJOB_STATUS_FINISHED)
			return false;
						
		// convert collection finished - export ism and ismc files
		if($dbBatchJob->getJobType() == BatchJobType::CONVERT_COLLECTION && $dbBatchJob->getJobSubType() == conversionEngineType::EXPRESSION_ENCODER3)
			return true;
		
		return false;
	}
	
	public static function exportSourceAssetFromJob(BatchJob $dbBatchJob)
	{
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
			if(!PermissionPeer::isValidForPartner(PermissionName::FEATURE_REMOTE_STORAGE, $object->getPartnerId()))
				return false;
				
			if ($object->getFileType() == FileSync::FILE_SYNC_FILE_TYPE_URL)
			{
				$storage = StorageProfilePeer::retrieveByPK($object->getDc());
				if ($storage->getStatus() == StorageProfile::STORAGE_STATUS_AUTOMATIC && $storage->getAllowAutoDelete())
				{
					return true;
				}
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
			
			$key = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ISM);
			if($profile->isExported($key))
			{
				self::delete($entry, $profile, $key);
			}
			
			$key = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ISMC);
			if($profile->isExported($key))
			{
				self::delete($entry, $profile, $key);
			}			
		}
		self::deleteAdditionalEntryFilesFromStorage($entry, $profile);
	}
	
}