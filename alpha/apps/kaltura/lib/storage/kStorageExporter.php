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

	const ALL_PARTNERS_WILD_CHAR = "*";
	const NULL_STR = 'NULL';
	
	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::shouldConsumeChangedEvent()
	 */
	public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns)
	{
		if(!($object instanceof entry) && !($object instanceof flavorAsset) && !($object instanceof FileSync))
		{
			return false;
		}

		if(!PermissionPeer::isValidForPartner(PermissionName::FEATURE_REMOTE_STORAGE, $object->getPartnerId()))
		{
			return false;
		}

		// if changed object is entry
		if($object instanceof entry && in_array(entryPeer::MODERATION_STATUS, $modifiedColumns) && $object->getModerationStatus() == entry::ENTRY_MODERATION_STATUS_APPROVED)
			return true;

		// if changed object is flavor asset / thumb asset / caption asset
		if(self::shouldHandleAssetObjectChanged($object, $modifiedColumns))
			return true;

		// if changed object is file sync
		if (self::shouldHandleFileSyncObjectChanged($object, $modifiedColumns))
		{
			return true;
		}
			
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

		// if changed object is flavor asset / thumb asset / caption asset
		if (self::shouldHandleAssetObjectChanged($object, $modifiedColumns))
		{
			self::handleAssetStorageExports($object);
		}

		// if changed object is file sync
		if (self::shouldHandleFileSyncObjectChanged($object, $modifiedColumns))
		{
			self::handleFileSyncStorageExports($object);
		}

		return true;
	}

	public function shouldConsumeAddedEvent(BaseObject $object)
	{
		if( ($object instanceof thumbAsset || $object instanceof captionAsset) && $object->isLocalReadyStatus())
			return true;


		if( ($object instanceof FileSync) && ($object->getStatus() == FileSync::FILE_SYNC_STATUS_READY) )
		{
			return true;
		}

		return false;
	}

	public function objectAdded(BaseObject $object, BatchJob $raisedJob = null)
	{
		if($object instanceof thumbAsset || $object instanceof captionAsset)
		{
			self::handleAssetStorageExports($object);
		}

		if ($object instanceof FileSync)
		{
			self::handleFileSyncStorageExports($object);
		}
	}

	static protected function shouldExportToPeriodicStorage(FileSync $object)
	{
		$exportToPeriodicStorage = false;
		$dataCenters = kDataCenterMgr::getDcIds();

		if(in_array($object->getDc(), $dataCenters))
		{
			$externalStorageProfiles = StorageProfilePeer::retrieveAutomaticByPartnerId($object->getPartnerId());
			if(!$externalStorageProfiles)
			{
				$exportToPeriodicStorage = true;
			}
		}
		else
		{
			$storageProfile = StorageProfilePeer::retrieveByPK($object->getDc());
			if($storageProfile && !$storageProfile->getExportPeriodically() && ($object->getLinkedId() != 'NULL')
				&& kFlowHelper::checkNonPeriodicExportsFinished($object->getPartnerId(), array($object->getObjectId())) )
			{
				$exportToPeriodicStorage = true;
			}
		}

		KalturaLog::info("FileSync ID [{$object->getId()}] DC [{$object->getDc()}] Should Export To Periodic [$exportToPeriodicStorage]");
		return $exportToPeriodicStorage;
	}

	public static function exportSourceFlavorAsset($entryId)
	{
		$srcFlavors = assetPeer::retrieveLocalReadyByEntryIdAndFlavorParams($entryId, array(flavorParams::SOURCE_FLAVOR_ID));
		foreach($srcFlavors as $srcFlavor)
		{
			if($srcFlavor->getType() == assetType::FLAVOR)
			{
				KalturaLog::info('Export source asset to private and periodic storages');

				//Private storage
				self::handleAssetStorageExports($srcFlavor);

				//Periodic storage
				$periodicStorageProfiles = kStorageExporter::getPeriodicStorageProfilesByFlag();
				if(!$periodicStorageProfiles)
				{
					$periodicStorageProfiles = kStorageExporter::getPeriodicStorageProfiles($srcFlavor->getPartnerId());
				}
				self::exportToPeriodicStorage($srcFlavor, $periodicStorageProfiles);
				break;
			}
		}
	}

	/**
	 * @param flavorAsset $flavor
	 * @param StorageProfile $externalStorage
	 * @param boolean $skipFlavorAssetStatusValidation
	 */
	static public function exportFlavorAsset(asset $flavor, StorageProfile $externalStorage, $skipFlavorAssetStatusValidation = false)
	{
		if (!$externalStorage->shouldExportFlavorAsset($flavor, $skipFlavorAssetStatusValidation))
		{
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
		$partner = $entry->getPartner();

		// all export jobs for replacing entry that has periodic storage will happen on the original replaced entry
		// after we will copy the matching file sync
		if($entry->getReplacedEntryId() && $partner && kStorageExporter::getPeriodicStorageIdsByPartner($partner->getId()))
		{
			return;
		}

		// dont export to periodic local storage if partner configured delete local content
		if($externalStorage->getExportPeriodically() && $partner && $partner->getStorageDeleteFromKaltura())
		{
			$partnerStorageProfiles = StorageProfilePeer::retrieveExternalByPartnerId($partner->getId());
			if($partnerStorageProfiles)
			{
				return;
			}
		}

		/* @var $fileSync FileSync */
		list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($key,true,false);
		if (!$fileSync)
		{
			KalturaLog::info("no ready fileSync was found for key [$key]");
			return;
		}
		
		$externalFileSync = kFileSyncUtils::createPendingExternalSyncFileForKey($key, $externalStorage, $fileSync->getIsDir());
		
		$srcFileSync = kFileSyncUtils::resolve($fileSync);
		if($externalStorage->getExportPeriodically())
		{
			$externalFileSync->setFileSize($srcFileSync->getFileSize());
			$externalFileSync->setSrcPath($srcFileSync->getFullPath());
			$externalFileSync->setSrcEncKey($srcFileSync->getSrcEncKey());
			$externalFileSync->save();
		}
		else
		{
			kJobsManager::addStorageExportJob(null, $entry->getId(), $entry->getPartnerId(),
				$externalStorage, $externalFileSync, $srcFileSync, $force, $srcFileSync->getDc());
		}
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
		{
			return;
		}
		if($entry->getStatus() == entryStatus::NO_CONTENT)
		{
			return;
		}
				
		$storageProfiles = StorageProfilePeer::retrieveAutomaticByPartnerId($entry->getPartnerId());
		foreach ($storageProfiles as $profile) 
		{			
			/* @var $profile StorageProfile */
			KalturaLog::debug('Checking entry ['.$entry->getId().'] re-export to storage ['.$profile->getId().']');
			$scope = $profile->getScope();
			$scope->setEntryId($entry->getId());
			if($profile->triggerFitsReadyAsset($entry->getId()) && $profile->fulfillsRules($scope))
			{
				self::tryExportEntry($entry, $profile);
			}
			else
			{
				self::deleteExportedEntry($entry, $profile);
			}
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
					$ismKey = $entry->getSyncKey(kEntryFileSyncSubType::ISM);
					if(kFileSyncUtils::fileSync_exists($ismKey))
						self::export($entry, $externalStorage, $ismKey);
					
					$ismcKey = $entry->getSyncKey(kEntryFileSyncSubType::ISMC);
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
		$additionalFileSyncKeys = array(kEntryFileSyncSubType::DATA, kEntryFileSyncSubType::ISM, kEntryFileSyncSubType::ISMC);
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
		$additionalFileSyncKeys = array(kEntryFileSyncSubType::DATA, kEntryFileSyncSubType::ISM, kEntryFileSyncSubType::ISMC);
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

	public static function getPeriodicStorageIds()
	{
		return kConf::get('periodic_storage_ids','cloud_storage', array());
	}

	public static function getPeriodicStorageIdsByPartner($partnerId)
	{
		$isPartnerValid = true;
		$partner = PartnerPeer::retrieveActiveByPK($partnerId);

		do
		{
			if($partner && !$partner->getStorageDeleteFromKaltura())
			{
				$partnerIds = kConf::get('export_to_cloud_partner_ids', 'cloud_storage', array());
				if (in_array($partnerId, $partnerIds) || in_array(self::ALL_PARTNERS_WILD_CHAR, $partnerIds))
				{
					break;
				}

				$partnerPackages = kConf::get('export_to_cloud_partner_package', 'cloud_storage', array());
				if (in_array($partner->getPartnerPackage(), $partnerPackages))
				{
					break;
				}
			}

			$isPartnerValid = false;
		}while(0);

		if($isPartnerValid)
		{
			return self::getPeriodicStorageIds();
		}

		return array();
	}

	public static function getPeriodicStorageProfilesByFlag()
	{
		$storageProfiles = array();
		if( kConf::get('copy_all_content_to_cloud', 'cloud_storage', 0) )
		{
			$storageIds = self::getPeriodicStorageIds();
			if($storageIds)
			{
				$storageProfiles = StorageProfilePeer::retrieveByPKs($storageIds);
			}
		}
		return $storageProfiles;
	}

	public static function getPeriodicStorageProfiles($partnerId)
	{
		$externalStorages = array();
		$storageIds = self::getPeriodicStorageIdsByPartner($partnerId);
		if($storageIds)
		{
			$externalStorages = StorageProfilePeer::retrieveByPKs($storageIds);
		}
		return $externalStorages;
	}

	public static function exportMultipleFlavors($assets, $storage)
	{
		foreach ($assets as $asset)
		{
			$exported = kStorageExporter::exportFlavorAsset($asset, $storage);
			KalturaLog::debug("assetId [{$asset->getId()}] exported status is [$exported]");
		}
	}


	protected static function getPeriodicStorageProfilesForExport(FileSync $object)
	{
		if($object->getObjectType() != FileSyncObjectType::ASSET || $object->getObjectSubType() != asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET)
		{
			return null;
		}

		$periodicStorageProfiles = kStorageExporter::getPeriodicStorageProfilesByFlag();
		if($periodicStorageProfiles)
		{
			return $periodicStorageProfiles;
		}

		return kStorageExporter::getPeriodicStorageProfiles($object->getPartnerId());
	}

	public static function exportToPeriodicStorage($asset, $periodicStorageProfiles)
	{
		if($periodicStorageProfiles)
		{
			foreach($periodicStorageProfiles as $periodicStorageProfile)
			{
				$exported = kStorageExporter::exportFlavorAsset($asset, $periodicStorageProfile, true);
				KalturaLog::debug("assetId [{$asset->getId()}] exported is [$exported]");
			}
		}
	}

	public static function handleAssetStorageExports($object)
	{
		$externalStorageProfiles = StorageProfilePeer::retrieveAutomaticByPartnerId($object->getPartnerId());
		foreach($externalStorageProfiles as $externalStorage)
		{
			if ($externalStorage->triggerFitsReadyAsset($object->getEntryId()))
			{
				self::exportFlavorAsset($object, $externalStorage);
			}
		}
	}

	protected static function isSourceFlavorAsset($asset)
	{
		if( ($asset->getType() == assetType::FLAVOR) && ($asset->getFlavorParamsId() == flavorParams::SOURCE_FLAVOR_ID) )
		{
			return true;
		}
		return false;
	}

	protected static function handleFileSyncStorageExports(FileSync $object)
	{
		$asset = assetPeer::retrieveById($object->getObjectId());
		if( ($asset) && (!self::isSourceFlavorAsset($asset)) && (self::shouldExportToPeriodicStorage($object)) )
		{
			$storageProfiles = self::getPeriodicStorageProfilesForExport($object);
			self::exportToPeriodicStorage($asset, $storageProfiles);
		}
	}

	protected static function shouldHandleAssetObjectChanged($object, $modifiedColumns)
	{
		if( ($object instanceof flavorAsset && ($object->getFlavorParamsId() != flavorParams::SOURCE_FLAVOR_ID))
			|| $object instanceof thumbAsset || $object instanceof captionAsset)
		{
			if(in_array(assetPeer::STATUS, $modifiedColumns) && $object->isLocalReadyStatus())
			{
				return true;
			}
		}
		return false;
	}

	protected static function shouldHandleFileSyncObjectChanged($object, $modifiedColumns)
	{
		if(!($object instanceof FileSync))
		{
			return false;
		}

		if (in_array(FileSyncPeer::STATUS, $modifiedColumns)
			&& $object->getColumnsOldValue(FileSyncPeer::STATUS) == FileSync::FILE_SYNC_STATUS_PENDING
			&& $object->getStatus() == FileSync::FILE_SYNC_STATUS_READY)
		{
			return true;
		}

		return false;
	}

}
