<?php
/**
 * @package plugins.contentDistribution
 * @subpackage lib
 */
class kContentDistributionManager
{
	/**
	 * @param entry $entry
	 * @param DistributionProfile $distributionProfile
	 * @param bool $submit
	 * @return EntryDistribution
	 */
	public static function addEntryDistribution(entry $entry, DistributionProfile $distributionProfile, $submit = false)
	{
		$entryDistribution = self::createEntryDistribution($entry, $distributionProfile);
		if(is_null($entryDistribution))
			return null;
		$entryDistribution->save();
		
		if($distributionProfile->getSubmitEnabled() == DistributionProfileActionStatus::AUTOMATIC)
			$submit = true;
			
		if($submit)
			self::submitAddEntryDistribution($entryDistribution, $distributionProfile);
			
		return $entryDistribution;
	}
	
	
	protected static function addImportJob($dc, $entryUrl, asset $asset)
	{
		$entryUrl = str_replace('//', '/', $entryUrl);
		$entryUrl = preg_replace('/^((https?)|(ftp)|(scp)|(sftp)):\//', '$1://', $entryUrl);
	
		$jobData = new kImportJobData();
		$jobData->setCacheOnly(true);
		$jobData->setSrcFileUrl($entryUrl);
		$jobData->setFlavorAssetId($asset->getId());
	
		$batchJob = new BatchJob();
		$batchJob->setDc($dc);
		$batchJob->setEntryId($asset->getEntryId());
		$batchJob->setPartnerId($asset->getPartnerId());
		$batchJob->setObjectId($asset->getId());
		$batchJob->setObjectType(BatchJobObjectType::ASSET);
		
		return kJobsManager::addJob($batchJob, $jobData, BatchJobType::IMPORT);
	}
	
	/**
	 * @param EntryDistribution $entryDistribution
	 * @param DistributionProfile $distributionProfile
	 * @param int $dc
	 * @return bool true if the job could be created
	 */
	protected static function prepareDistributionJob(EntryDistribution $entryDistribution, DistributionProfile $distributionProfile, &$dc)
	{
		// prepare ids list of all the assets
		$assetIds = explode(',', implode(',', array(
			$entryDistribution->getThumbAssetIds(),
			$entryDistribution->getFlavorAssetIds()
		)));
		
		$assets = assetPeer::retrieveByIds($assetIds);
		$assetObjects = array();
		foreach($assets as $asset)
		{
			/* @var $asset asset */
			$assetObjects[$asset->getId()] = array(
				'asset' => $asset,
				'downloadUrl' => null,
			);
		}
		
		// lists all files from all assets
		$c = new Criteria();
		$c->add(FileSyncPeer::OBJECT_TYPE, FileSyncObjectType::ASSET);
		$c->add(FileSyncPeer::OBJECT_SUB_TYPE, asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
		$c->add(FileSyncPeer::OBJECT_ID, $assetIds, Criteria::IN);
		$c->add(FileSyncPeer::PARTNER_ID, $entryDistribution->getPartnerId());
		$c->add(FileSyncPeer::STATUS, FileSync::FILE_SYNC_STATUS_READY);
		$fileSyncs = FileSyncPeer::doSelect($c);

		if(!$fileSyncs)
  		{
   			KalturaLog::info("No file syncs to distribute");
   			return true;
  		}
  		
		$dcs = array();
		foreach($fileSyncs as $fileSync)
		{
			/* @var $fileSync FileSync */
			$assetId = $fileSync->getObjectId();
			
			if(!isset($assetObjects[$assetId])) // the object is not in the list of assets
				continue;
			
			$asset = $assetObjects[$assetId]['asset'];
			/* @var $asset asset */
			
			if($asset->getVersion() != $fileSync->getVersion()) // the file sync is not of the current asset version
				continue;
			
			$fileSync = kFileSyncUtils::resolve($fileSync);
			
			// use the best URL as the source for download in case it will be needed
			if($fileSync->getFileType() == FileSync::FILE_SYNC_FILE_TYPE_URL)
			{
				if(!is_null($assetObjects[$assetId]['downloadUrl']) && $fileSync->getDc() != $distributionProfile->getRecommendedStorageProfileForDownload())
					continue;
				
				$downloadUrl = $fileSync->getExternalUrl($entryDistribution->getEntryId());
				if(!$downloadUrl)
					continue;
				
				$assetObjects[$assetId]['downloadUrl'] = $downloadUrl;
				continue;
			}
			
			// populates the list of files in each dc
			$fileSyncDc = $fileSync->getDc();
			if(!isset($dcs[$fileSyncDc]))
				$dcs[$fileSyncDc] = array();
			
			$dcs[$fileSyncDc][$assetId] = $fileSync->getId();
		}
		
		if(isset($dcs[$dc]) && count($dcs[$dc]) == count($assets))
		{
			KalturaLog::info("All files exist in the preferred dc [$dc]");
			return true;
		}
		
		// check if all files exist on any of the remote dcs
		$otherDcs = kDataCenterMgr::getAllDcs(true);
		foreach($otherDcs as $remoteDc)
		{
			$remoteDcId = $remoteDc['id'];
			if(!isset($dcs[$remoteDcId]) || count($dcs[$remoteDcId]) != count($assets))
				continue;
			
			$dc = $remoteDcId;
			KalturaLog::info("All files exist in none-preferred dc [$dc]");
			return true;
		}
		
		if(
			$entryDistribution->getStatus() == EntryDistributionStatus::IMPORT_SUBMITTING
			||
			$entryDistribution->getStatus() == EntryDistributionStatus::IMPORT_UPDATING
		)
		{
			KalturaLog::info("Entry distribution already importing");
			return false;
		}
		
		// create all needed import jobs
		$destinationDc = $distributionProfile->getRecommendedDcForDownload();
		//if there is no recommended dc to import choose the distribution job dc
		if(!$destinationDc)
			$destinationDc = $dc;

		$dcExistingFiles = $dcs[$destinationDc];
		foreach($assetObjects as $assetId => $assetObject)
		{
			if(is_null($assetObject['downloadUrl']))
			{
				KalturaLog::info("Download URL not found for asset [$assetId]");
				continue;
			}
			
			$asset = $assetObject['asset'];
			/* @var $asset asset */
			
			if(isset($dcExistingFiles[$assetId]))
				continue;
			
			$jobData = new kImportJobData();
			$jobData->setCacheOnly(true);
			
			self::addImportJob($destinationDc, $assetObject['downloadUrl'], $asset);
		}
		
		return false;
	}
	
	/**
	 * @param EntryDistribution $entryDistribution
	 * @param DistributionProfile $distributionProfile
	 * @return BatchJob
	 */
	protected static function addSubmitAddJob(EntryDistribution $entryDistribution, DistributionProfile $distributionProfile)
	{
		if($entryDistribution->getStatus() == EntryDistributionStatus::SUBMITTING)
		{
			KalturaLog::info("Entry distribution [" . $entryDistribution->getId() . "] already submitting");
			return null;
		}
		
		$entryDistribution->setStatus(EntryDistributionStatus::SUBMITTING);
		
		if(!$entryDistribution->save())
		{
			KalturaLog::err("Unable to save entry distribution [" . $entryDistribution->getId() . "] status");
			$entryDistribution->reload();	//Reload in case object was chnaged
			return null;
		}
		
		$entryDistribution->setDirtyStatus(null);	//Moved down to ensure previous save is done Atomically
		$entryDistribution->save();
	
		$dc = $distributionProfile->getRecommendedDcForExecute();
		if(is_null($dc))
			$dc = kDataCenterMgr::getCurrentDcId();
			
		$jobType = ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_SUBMIT);
		if($distributionProfile->getProvider()->isLocalFileRequired($jobType))
		{
			$readyForSubmit = self::prepareDistributionJob($entryDistribution, $distributionProfile, $dc);
			if(!$readyForSubmit)
			{
				$entryDistribution->setStatus(EntryDistributionStatus::IMPORT_SUBMITTING);
				$entryDistribution->save();
				
				return null;
			}
		}
		
 		$jobData = new kDistributionSubmitJobData();
 		$jobData->setDistributionProfileId($entryDistribution->getDistributionProfileId());
 		$jobData->setEntryDistributionId($entryDistribution->getId());
 		$jobData->setProviderType($distributionProfile->getProviderType());
 		
		$batchJob = new BatchJob();
		$batchJob->setDc($dc);
		$batchJob->setEntryId($entryDistribution->getEntryId());
		$batchJob->setPartnerId($entryDistribution->getPartnerId());
		$batchJob->setObjectId($entryDistribution->getId());
		$batchJob->setObjectType(kPluginableEnumsManager::apiToCore('BatchJobObjectType',ContentDistributionBatchJobObjectType::ENTRY_DISTRIBUTION));
		
		$jobSubType = $distributionProfile->getProviderType();
	
		return kJobsManager::addJob($batchJob, $jobData, $jobType, $jobSubType);
	}
	
	/**
	 * @param EntryDistribution $entryDistribution
	 * @param DistributionProfile $distributionProfile
	 * @return BatchJob
	 */
	protected static function addSubmitDisableJob(EntryDistribution $entryDistribution, DistributionProfile $distributionProfile)
	{
		$entryDistribution->setStatus(EntryDistributionStatus::UPDATING);
		
		if(!$entryDistribution->save())
		{
			KalturaLog::err("Unable to save entry distribution [" . $entryDistribution->getId() . "] status");
			$entryDistribution->reload();
			return null;
		}
		
		$entryDistribution->setDirtyStatus(null);	//Moved down to ensure previous save is done Atomically
		$entryDistribution->save();
		
 		$jobData = new kDistributionDisableJobData();
 		$jobData->setDistributionProfileId($entryDistribution->getDistributionProfileId());
 		$jobData->setEntryDistributionId($entryDistribution->getId());
 		$jobData->setProviderType($distributionProfile->getProviderType());
 		$jobData->setRemoteId($entryDistribution->getRemoteId());
 		$jobData->setMediaFiles($entryDistribution->getMediaFiles());
 		
		$batchJob = new BatchJob();
		$batchJob->setEntryId($entryDistribution->getEntryId());
		$batchJob->setPartnerId($entryDistribution->getPartnerId());
		$batchJob->setObjectId($entryDistribution->getId());
		$batchJob->setObjectType(kPluginableEnumsManager::apiToCore('BatchJobObjectType',ContentDistributionBatchJobObjectType::ENTRY_DISTRIBUTION));
		
		$jobType = ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_DISABLE);
		$jobSubType = $distributionProfile->getProviderType();
	
		return kJobsManager::addJob($batchJob, $jobData, $jobType, $jobSubType);
	}
	
	/**
	 * @param EntryDistribution $entryDistribution
	 * @param DistributionProfile $distributionProfile
	 * @return BatchJob
	 */
	protected static function addSubmitEnableJob(EntryDistribution $entryDistribution, DistributionProfile $distributionProfile)
	{
		$entryDistribution->setStatus(EntryDistributionStatus::UPDATING);
		
		if(!$entryDistribution->save())
		{
			KalturaLog::err("Unable to save entry distribution [" . $entryDistribution->getId() . "] status");
			$entryDistribution->reload();
			return null;
		}
		
		$entryDistribution->setDirtyStatus(null);	//Moved down to ensure previous save is done Atomically
		$entryDistribution->save();
		
 		$jobData = new kDistributionEnableJobData();
 		$jobData->setDistributionProfileId($entryDistribution->getDistributionProfileId());
 		$jobData->setEntryDistributionId($entryDistribution->getId());
 		$jobData->setProviderType($distributionProfile->getProviderType());
 		$jobData->setRemoteId($entryDistribution->getRemoteId());
 		$jobData->setMediaFiles($entryDistribution->getMediaFiles());
 		
		$batchJob = new BatchJob();
		$batchJob->setEntryId($entryDistribution->getEntryId());
		$batchJob->setPartnerId($entryDistribution->getPartnerId());
		$batchJob->setObjectId($entryDistribution->getId());
		$batchJob->setObjectType(kPluginableEnumsManager::apiToCore('BatchJobObjectType',ContentDistributionBatchJobObjectType::ENTRY_DISTRIBUTION));
		
		$jobType = ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_ENABLE);
		$jobSubType = $distributionProfile->getProviderType();
	
		return kJobsManager::addJob($batchJob, $jobData, $jobType, $jobSubType);
	}
	
	/**
	 * @param EntryDistribution $entryDistribution
	 * @param DistributionProfile $distributionProfile
	 * @return BatchJob
	 */
	protected static function addSubmitUpdateJob(EntryDistribution $entryDistribution, DistributionProfile $distributionProfile)
	{
		if($entryDistribution->getStatus() == EntryDistributionStatus::UPDATING)
			return null;
	
		$entryDistribution->setStatus(EntryDistributionStatus::UPDATING);
		
		if(!$entryDistribution->save())
		{
			KalturaLog::err("Unable to save entry distribution [" . $entryDistribution->getId() . "] status");
			$entryDistribution->reload();
			return null;
		}
		
		$entryDistribution->setDirtyStatus(null);	//Moved dowen to down previous save is done Atomically
		$entryDistribution->save();
		
		$dc = $distributionProfile->getRecommendedDcForExecute();
		if(is_null($dc))
			$dc = kDataCenterMgr::getCurrentDcId();
		
		$jobType = ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_UPDATE);
		if($distributionProfile->getProvider()->isLocalFileRequired($jobType))
		{
			$readyForSubmit = self::prepareDistributionJob($entryDistribution, $distributionProfile, $dc);
			if(!$readyForSubmit)
			{
				$entryDistribution->setStatus(EntryDistributionStatus::IMPORT_UPDATING);
				$entryDistribution->save();
				
				return null;
			}
		}
		
 		$jobData = new kDistributionUpdateJobData();
 		$jobData->setDistributionProfileId($entryDistribution->getDistributionProfileId());
 		$jobData->setEntryDistributionId($entryDistribution->getId());
 		$jobData->setProviderType($distributionProfile->getProviderType());
 		$jobData->setRemoteId($entryDistribution->getRemoteId());
 		$jobData->setMediaFiles($entryDistribution->getMediaFiles());
 		
		$batchJob = new BatchJob();
		$batchJob->setDc($dc);
		$batchJob->setEntryId($entryDistribution->getEntryId());
		$batchJob->setPartnerId($entryDistribution->getPartnerId());
		$batchJob->setObjectId($entryDistribution->getId());
		$batchJob->setObjectType(kPluginableEnumsManager::apiToCore('BatchJobObjectType',ContentDistributionBatchJobObjectType::ENTRY_DISTRIBUTION));
		
		$jobSubType = $distributionProfile->getProviderType();
	
		return kJobsManager::addJob($batchJob, $jobData, $jobType, $jobSubType);
	}
	
	/**
	 * @param EntryDistribution $entryDistribution
	 * @param DistributionProfile $distributionProfile
	 * @return BatchJob
	 */
	protected static function addFetchReportJob(EntryDistribution $entryDistribution, DistributionProfile $distributionProfile)
	{
 		$jobData = new kDistributionFetchReportJobData();
 		$jobData->setDistributionProfileId($entryDistribution->getDistributionProfileId());
 		$jobData->setEntryDistributionId($entryDistribution->getId());
 		$jobData->setProviderType($distributionProfile->getProviderType());
 		$jobData->setRemoteId($entryDistribution->getRemoteId());
 		
		$batchJob = new BatchJob();
		$batchJob->setEntryId($entryDistribution->getEntryId());
		$batchJob->setPartnerId($entryDistribution->getPartnerId());
		$batchJob->setObjectId($entryDistribution->getId());
		$batchJob->setObjectType(kPluginableEnumsManager::apiToCore('BatchJobObjectType',ContentDistributionBatchJobObjectType::ENTRY_DISTRIBUTION));
		
		$jobType = ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_FETCH_REPORT);
		$jobSubType = $distributionProfile->getProviderType();
	
		return kJobsManager::addJob($batchJob, $jobData, $jobType, $jobSubType);
	}
	
	/**
	 * @param EntryDistribution $entryDistribution
	 * @param DistributionProfile $distributionProfile
	 * @return BatchJob
	 */
	protected static function addSubmitDeleteJob(EntryDistribution $entryDistribution, DistributionProfile $distributionProfile, $shouldKeepDistributionItem = false)
	{
		$entryDistribution->setStatus(EntryDistributionStatus::DELETING);
		
		if(!$entryDistribution->save())
		{
			KalturaLog::err("Unable to save entry distribution [" . $entryDistribution->getId() . "] status");
			$entryDistribution->reload();
			return null;
		}
		
		$entryDistribution->setDirtyStatus(null);	//Moved down to ensure previous save is done Atomically
		$entryDistribution->save();
	
 		$jobData = new kDistributionDeleteJobData();
 		$jobData->setDistributionProfileId($entryDistribution->getDistributionProfileId());
 		$jobData->setEntryDistributionId($entryDistribution->getId());
 		$jobData->setProviderType($distributionProfile->getProviderType());
 		$jobData->setRemoteId($entryDistribution->getRemoteId());
 		$jobData->setMediaFiles($entryDistribution->getMediaFiles());
 		$jobData->setKeepDistributionItem($shouldKeepDistributionItem);
 		
		$batchJob = new BatchJob();
		$batchJob->setEntryId($entryDistribution->getEntryId());
		$batchJob->setPartnerId($entryDistribution->getPartnerId());
		$batchJob->setObjectId($entryDistribution->getId());
		$batchJob->setObjectType(kPluginableEnumsManager::apiToCore('BatchJobObjectType',ContentDistributionBatchJobObjectType::ENTRY_DISTRIBUTION));
		
		$jobType = ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_DELETE);
		$jobSubType = $distributionProfile->getProviderType();
	
		return kJobsManager::addJob($batchJob, $jobData, $jobType, $jobSubType);
	}
	
	/**
	 * @param EntryDistribution $entryDistribution
	 * @param DistributionProfile $distributionProfile
	 * @return BatchJob
	 */
	public static function submitDeleteEntryDistribution(EntryDistribution $entryDistribution, DistributionProfile $distributionProfile, $shouldKeepDistributionItem = false)
	{
		if($distributionProfile->getStatus() != DistributionProfileStatus::ENABLED || $distributionProfile->getDeleteEnabled() == DistributionProfileActionStatus::DISABLED)
			return null;
			
		$validStatus = array(
			EntryDistributionStatus::ERROR_DELETING,
			EntryDistributionStatus::ERROR_UPDATING,
			EntryDistributionStatus::READY,
		);
		
		if(!in_array($entryDistribution->getStatus(), $validStatus))
		{
			KalturaLog::notice("Wrong entry distribution status [" . $entryDistribution->getStatus() . "]");
			return null;
		}
		
		$distributionProvider = $distributionProfile->getProvider();
		if($distributionProvider->isDeleteEnabled())
			return self::addSubmitDeleteJob($entryDistribution, $distributionProfile, $shouldKeepDistributionItem);
			
		if($distributionProvider->isAvailabilityUpdateEnabled())
			return self::addSubmitDisableJob($entryDistribution, $distributionProfile);
		
		if(!$distributionProvider->isScheduleUpdateEnabled() || !$distributionProvider->isUpdateEnabled())
		{
			KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] provider [" . $distributionProfile->getProviderType() . "] doesn't support delete or update");
			return null;
		}
			
		$entryDistribution->setSunset(time());
		$entryDistribution->save();
		
		return self::addSubmitUpdateJob($entryDistribution, $distributionProfile);
	}
	
	/**
	 * @param EntryDistribution $entryDistribution
	 * @param DistributionProfile $distributionProfile
	 * @return BatchJob
	 */
	public static function submitUpdateEntryDistribution(EntryDistribution $entryDistribution, DistributionProfile $distributionProfile)
	{
		if($distributionProfile->getStatus() != DistributionProfileStatus::ENABLED || $distributionProfile->getUpdateEnabled() == DistributionProfileActionStatus::DISABLED)
			return null;
			
		$validStatus = array(
			EntryDistributionStatus::ERROR_DELETING,
			EntryDistributionStatus::ERROR_UPDATING,
			EntryDistributionStatus::IMPORT_UPDATING,
			EntryDistributionStatus::READY,
		);
		
		if(!in_array($entryDistribution->getStatus(), $validStatus))
		{
			KalturaLog::notice("wrong entry distribution status [" . $entryDistribution->getStatus() . "]");
			return null;
		}
		
		
		$validationErrors = $entryDistribution->getValidationErrors();
		if(count($validationErrors))
		{
			KalturaLog::log("Validation errors found");
			return null;
		}
		self::addAssetIdsToEntryDistribution($entryDistribution, $distributionProfile);
		$distributionProvider = $distributionProfile->getProvider();
		if($distributionProvider->isUpdateEnabled())
			return self::addSubmitUpdateJob($entryDistribution, $distributionProfile);

		if($distributionProvider->useDeleteInsteadOfUpdate())
		{
			$job = self::addSubmitDeleteJob($entryDistribution, $distributionProfile);
			return self::addSubmitAddJob($entryDistribution, $distributionProfile);
		}
	
		$entryDistribution->setStatus(EntryDistributionStatus::ERROR_UPDATING);
		$entryDistribution->save();
		
		return null;
	}
	
	/**
	 * @param EntryDistribution $entryDistribution
	 * @param DistributionProfile $distributionProfile
	 * @return BatchJob
	 */
	public static function submitEnableEntryDistribution(EntryDistribution $entryDistribution, DistributionProfile $distributionProfile)
	{
		if($distributionProfile->getStatus() != DistributionProfileStatus::ENABLED || $distributionProfile->getUpdateEnabled() == DistributionProfileActionStatus::DISABLED)
			return null;
			
		$validStatus = array(
			EntryDistributionStatus::ERROR_DELETING,
			EntryDistributionStatus::ERROR_UPDATING,
			EntryDistributionStatus::READY,
		);
		
		if(!in_array($entryDistribution->getStatus(), $validStatus))
		{
			KalturaLog::notice("wrong entry distribution status [" . $entryDistribution->getStatus() . "]");
			return null;
		}
		
		
		$validationErrors = $entryDistribution->getValidationErrors();
		if(count($validationErrors))
		{
			KalturaLog::log("Validation errors found");
			return null;
		}
		
		$distributionProvider = $distributionProfile->getProvider();
		if($distributionProvider->isUpdateEnabled() && $distributionProvider->isAvailabilityUpdateEnabled())
			return self::addSubmitEnableJob($entryDistribution, $distributionProfile);
	
		$entryDistribution->setStatus(EntryDistributionStatus::ERROR_UPDATING);
		$entryDistribution->save();
		
		return null;
	}
	
	/**
	 * @param EntryDistribution $entryDistribution
	 * @param DistributionProfile $distributionProfile
	 * @return BatchJob
	 */
	public static function submitDisableEntryDistribution(EntryDistribution $entryDistribution, DistributionProfile $distributionProfile)
	{
		if($distributionProfile->getStatus() != DistributionProfileStatus::ENABLED || $distributionProfile->getUpdateEnabled() == DistributionProfileActionStatus::DISABLED)
			return null;
			
		$validStatus = array(
			EntryDistributionStatus::ERROR_DELETING,
			EntryDistributionStatus::ERROR_UPDATING,
			EntryDistributionStatus::READY,
		);
		
		if(!in_array($entryDistribution->getStatus(), $validStatus))
		{
			KalturaLog::notice("wrong entry distribution status [" . $entryDistribution->getStatus() . "]");
			return null;
		}
		
		
		$validationErrors = $entryDistribution->getValidationErrors();
		if(count($validationErrors))
		{
			KalturaLog::log("Validation errors found");
			return null;
		}
		
		$distributionProvider = $distributionProfile->getProvider();
		if($distributionProvider->isUpdateEnabled() && $distributionProvider->isAvailabilityUpdateEnabled())
			return self::addSubmitDisableJob($entryDistribution, $distributionProfile);
	
		$entryDistribution->setStatus(EntryDistributionStatus::ERROR_UPDATING);
		$entryDistribution->save();
		
		return null;
	}
	
	/**
	 * @param EntryDistribution $entryDistribution
	 * @param DistributionProfile $distributionProfile
	 * @return BatchJob
	 */
	public static function submitFetchEntryDistributionReport(EntryDistribution $entryDistribution, DistributionProfile $distributionProfile)
	{
		if($distributionProfile->getStatus() != DistributionProfileStatus::ENABLED || $distributionProfile->getReportEnabled() == DistributionProfileActionStatus::DISABLED)
			return null;
			
		$validStatus = array(
			EntryDistributionStatus::READY,
		);
		
		if(!in_array($entryDistribution->getStatus(), $validStatus))
		{
			KalturaLog::notice("wrong entry distribution status [" . $entryDistribution->getStatus() . "]");
			return null;
		}
		
		$distributionProvider = $distributionProfile->getProvider();
		if($distributionProvider->isReportsEnabled())
			return self::addFetchReportJob($entryDistribution, $distributionProfile);

		return null;
	}

	/**
	 * @param EntryDistribution $entryDistribution
	 * @param DistributionProfile $distributionProfile
	 * @param bool $submitWhenReady
	 * @return BatchJob
	 */
	public static function submitAddEntryDistribution(EntryDistribution $entryDistribution, DistributionProfile $distributionProfile, $submitWhenReady = true)
	{
		if($distributionProfile->getStatus() != DistributionProfileStatus::ENABLED || $distributionProfile->getSubmitEnabled() == DistributionProfileActionStatus::DISABLED)
		{
			return null;
		}
			
		$validStatus = array(
			EntryDistributionStatus::ERROR_DELETING,
			EntryDistributionStatus::ERROR_SUBMITTING,
			EntryDistributionStatus::ERROR_UPDATING,
			EntryDistributionStatus::IMPORT_SUBMITTING,
			EntryDistributionStatus::PENDING,
			EntryDistributionStatus::QUEUED,
			EntryDistributionStatus::READY,
			EntryDistributionStatus::REMOVED,
		);
		
		if(!in_array($entryDistribution->getStatus(), $validStatus))
		{
			KalturaLog::notice("Wrong entry distribution status [" . $entryDistribution->getStatus() . "]");
			return null;
		}

		$submitNow = self::shouldSubmitNow($submitWhenReady, $entryDistribution, $distributionProfile);
		$validationErrors = $entryDistribution->getValidationErrors();
		if (!$submitNow)
		{
			self::updateStatusToQueued($entryDistribution);
			return false;
		} elseif (!count($validationErrors))
			return self::addSubmitAddJob($entryDistribution, $distributionProfile);

		self::updateStatusToQueued($entryDistribution);


		KalturaLog::log("Validation errors found");
		$entry = entryPeer::retrieveByPK($entryDistribution->getEntryId());
		if(!$entry)
		{
			KalturaLog::err("Entry [" . $entryDistribution->getEntryId() . "] not found");
			return null;
		}

		self::addAssetIdsToEntryDistribution($entryDistribution, $distributionProfile);
		
		$autoCreateFlavors = $distributionProfile->getAutoCreateFlavorsArray();
		$autoCreateThumbs = $distributionProfile->getAutoCreateThumbArray();
		foreach($validationErrors as $validationError)
		{
			if($validationError->getErrorType() == DistributionErrorType::MISSING_FLAVOR && in_array($validationError->getData(), $autoCreateFlavors))
			{
				$errDescription = null;
				KalturaLog::log("Adding flavor [" . $validationError->getData() . "] to entry [" . $entryDistribution->getEntryId() . "]");
				kBusinessPreConvertDL::decideAddEntryFlavor(null, $entryDistribution->getEntryId(), $validationError->getData(), $errDescription);
				if($errDescription)
					KalturaLog::log($errDescription);
			}
		
		    if($validationError->getErrorType() == DistributionErrorType::MISSING_THUMBNAIL && count($autoCreateThumbs))
			{
			    list($requiredWidth, $requiredHeight) = explode('x', $validationError->getData());
			    $foundThumbParams = false;
			    $thumbParamsObjects = assetParamsPeer::retrieveByPKs($autoCreateThumbs);
			    foreach ($thumbParamsObjects as $thumbParams)
			    {
			    	/* @var $thumbParams thumbParams */
			        if ($thumbParams->getWidth() == intval($requiredWidth) && $thumbParams->getHeight() == intval($requiredHeight))
			        {
			            $foundThumbParams = true;
			            KalturaLog::log("Adding thumbnail [" . $thumbParams->getId() . "] to entry [" . $entryDistribution->getEntryId() . "]");
					    kBusinessPreConvertDL::decideThumbGenerate($entry, $thumbParams);
					    break;
			        }
			    }
			    
			    if (!$foundThumbParams)
			    {
			        KalturaLog::err("Required thumbnail params not found [" . $validationError->getData() . "]");
			    }
			}
		}
		
		return null;
	}
	
	/**
	 * @param EntryDistribution $entryDistribution
	 * @param entry $entry
	 * @param DistributionProfile $distributionProfile
	 * @return boolean true if the list of flavors modified
	 */
	public static function assignFlavorAssets(EntryDistribution $entryDistribution, entry $entry, DistributionProfile $distributionProfile)
	{
		$submittingStatuses = array(
			EntryDistributionStatus::PENDING,
			EntryDistributionStatus::QUEUED,
			EntryDistributionStatus::SUBMITTING,
			EntryDistributionStatus::IMPORT_SUBMITTING,
			EntryDistributionStatus::ERROR_SUBMITTING,
		);
		
		// if not in first submmiting status then it's an update and need to check if update is supported.
		if(!in_array($entryDistribution->getStatus(), $submittingStatuses))
		{
			$distributionProvider = $distributionProfile->getProvider();
			if(!$distributionProvider)
			{
				KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] provider not found");
				return false;
			}
						
			if(!$distributionProvider->isUpdateEnabled() || !$distributionProvider->isMediaUpdateEnabled())
			{
				KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] provider [" . $distributionProvider->getName() . "] does not support update");
				return false;
			}
		}
		
		$requiredFlavorParamsIds = $distributionProfile->getRequiredFlavorParamsIdsArray();
		$optionalFlavorParamsIds = $distributionProfile->getOptionalFlavorParamsIdsArray();
		$flavorParamsIds = array_merge($requiredFlavorParamsIds, $optionalFlavorParamsIds);
		$flavorAssetIds = array();
		if(!is_array($flavorParamsIds))
			return false;
			
		$originalList = $entryDistribution->getFlavorAssetIds();
		// remove deleted flavor assets
		if($originalList)
		{
			$assignedFlavorAssetIds = explode(',', $originalList);
			$assignedFlavorAssets = assetPeer::retrieveByIds($assignedFlavorAssetIds);
			foreach($assignedFlavorAssets as $assignedFlavorAsset)
				if(in_array($assignedFlavorAsset->getFlavorParamsId(), $flavorParamsIds))
					$flavorAssetIds[] = $assignedFlavorAsset->getId();
		}
		
		// adds added flavor assets
		$newFlavorAssetIds = assetPeer::retrieveReadyFlavorsIdsByEntryId($entry->getId(), $flavorParamsIds);
		foreach($newFlavorAssetIds as $newFlavorAssetId)
			$flavorAssetIds[] = $newFlavorAssetId;
		$flavorAssetIds = self::getFilteredFlavorAssets($flavorAssetIds, $entry->getEntryId());

		$entryDistribution->setFlavorAssetIds($flavorAssetIds);
		return ($originalList != $entryDistribution->getFlavorAssetIds());
	}
	
	/**
	 * @param EntryDistribution $entryDistribution
	 * @param entry $entry
	 * @param DistributionProfile $distributionProfile
	 * @return boolean true if the list of thumbnails modified
	 */
	public static function assignThumbAssets(EntryDistribution $entryDistribution, entry $entry, DistributionProfile $distributionProfile)
	{
		$submittingStatuses = array(
			EntryDistributionStatus::PENDING,
			EntryDistributionStatus::QUEUED,
			EntryDistributionStatus::SUBMITTING,
			EntryDistributionStatus::IMPORT_SUBMITTING,
			EntryDistributionStatus::ERROR_SUBMITTING,
		);
		
		// if not in first submmiting status then it's an update and need to check if update is supported.
		if(!in_array($entryDistribution->getStatus(), $submittingStatuses))
		{
			$distributionProvider = $distributionProfile->getProvider();
			if(!$distributionProvider)
			{
				KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] provider not found");
				return false;
			}
						
			if(!$distributionProvider->isUpdateEnabled() || !$distributionProvider->isMediaUpdateEnabled())
			{
				KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] provider [" . $distributionProvider->getName() . "] does not support update");
				return false;
			}
		}
		
		$thumbAssetsIds = array();
		$thumbDimensions = $distributionProfile->getThumbDimensionsObjects();
		$thumbDimensionsWithKeys = array();
		foreach($thumbDimensions as $thumbDimension)
			$thumbDimensionsWithKeys[$thumbDimension->getKey()] = $thumbDimension;
		
		$originalList = $entryDistribution->getThumbAssetIds();
		
		// remove deleted thumb assets
		$assignedThumbAssetIds = $originalList;
		if($assignedThumbAssetIds)
		{
			$assignedThumbAssets = assetPeer::retrieveByIds(explode(',', $assignedThumbAssetIds));
			foreach($assignedThumbAssets as $assignedThumbAsset)
			{
				$key = $assignedThumbAsset->getWidth() . 'x' . $assignedThumbAsset->getHeight();
				if(isset($thumbDimensionsWithKeys[$key]))
				{
					unset($thumbDimensionsWithKeys[$key]);
					$thumbAssetsIds[] = $assignedThumbAsset->getId();
				}
			}
		}
		
		// add new thumb assets
		$requiredThumbParamsIds = $distributionProfile->getAutoCreateThumbArray();
		$thumbAssets = assetPeer::retrieveReadyThumbnailsByEntryId($entry->getId());
		foreach($thumbAssets as $thumbAsset)
		{
			if(in_array($thumbAsset->getFlavorParamsId(), $requiredThumbParamsIds))
			{
				$thumbAssetsIds[] = $thumbAsset->getId();
				KalturaLog::log("Assign thumb asset [" . $thumbAsset->getId() . "] from required thumbnail params ids for entry Distribution [".$entryDistribution->getId()."]");
				continue;
			}
			
			$key = $thumbAsset->getWidth() . 'x' . $thumbAsset->getHeight();
			if(isset($thumbDimensionsWithKeys[$key]))
			{
				unset($thumbDimensionsWithKeys[$key]);
				KalturaLog::log("Assign thumb asset [" . $thumbAsset->getId() . "] from dimension [$key] for entry Distribution [".$entryDistribution->getId()."]");
				$thumbAssetsIds[] = $thumbAsset->getId();
			}
		}
		$entryDistribution->setThumbAssetIds($thumbAssetsIds);
		
		return ($originalList != $entryDistribution->getThumbAssetIds());
	}
	
	/**
	 * @param EntryDistribution $entryDistribution
	 * @param entry $entry
	 * @param DistributionProfile $distributionProfile
	 * @return boolean
	 */
	public static function assignAssets(EntryDistribution $entryDistribution, entry $entry, DistributionProfile $distributionProfile)
	{
		$submittingStatuses = array(
				EntryDistributionStatus::PENDING,
				EntryDistributionStatus::QUEUED,
				EntryDistributionStatus::SUBMITTING,
				EntryDistributionStatus::IMPORT_SUBMITTING,
				EntryDistributionStatus::ERROR_SUBMITTING,
		);
	
		// if not in first submmiting status then it's an update and need to check if update is supported.
		if(!in_array($entryDistribution->getStatus(), $submittingStatuses))
		{
			$distributionProvider = $distributionProfile->getProvider();
			if(!$distributionProvider)
			{
				KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] provider not found");
				return false;
			}
	
			if(!$distributionProvider->isUpdateEnabled() || !$distributionProvider->isMediaUpdateEnabled())
			{
				KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] provider [" . $distributionProvider->getName() . "] does not support update");
				return false;
			}
		}
	
		$requiredAssetsConditions = $distributionProfile->getRequiredAssetDistributionRules();
		$optionalAssetsConditions = $distributionProfile->getOptionalAssetDistributionRules();
		$assetDistributionRules = array_merge($requiredAssetsConditions, $optionalAssetsConditions);

		if(!is_array($assetDistributionRules))
		{
			return false;
		}
		
		$assetIds = array();
		$originalList = $entryDistribution->getAssetIds();
		
		$entryAssets = assetPeer::retrieveReadyByEntryId($entryDistribution->getEntryId());

		foreach ($assetDistributionRules as $assetDistributionRule)
		{
			/* @var $assetDistributionRule kAssetDistributionRule */
			foreach ($entryAssets as $asset)
			{
				/* @var $asset asset */
				if ($assetDistributionRule->fulfilled($asset))
				{
					$assetIds[] = $asset->getId();
				}
			}
		}
			
		$entryDistribution->setAssetIds($assetIds);
		return ($originalList != $entryDistribution->getAssetIds());
	}
	
	/**
	 * @param entry $entry
	 * @param DistributionProfile $distributionProfile
	 * @return EntryDistribution or null if failed to create.
	 */
	public static function createEntryDistribution(entry $entry, DistributionProfile $distributionProfile)
	{
		$illegalEntryDistributionStatus = array(
				EntryDistributionStatus::SUBMITTING,
				EntryDistributionStatus::UPDATING,
				EntryDistributionStatus::DELETING,
				EntryDistributionStatus::IMPORT_SUBMITTING,
				EntryDistributionStatus::IMPORT_UPDATING
		);
		
		$entryDistribution = EntryDistributionPeer::retrieveByEntryAndProfileId($entry->getId(), $distributionProfile->getId());
		
		if((!$entryDistribution) || ($entryDistribution->getStatus() == EntryDistributionStatus::DELETED)) 
		{
			$entryDistribution = new EntryDistribution();
		} else if(in_array($entryDistribution->getStatus(), $illegalEntryDistributionStatus)) {
			KalturaLog::err("Entry distribution already exist. entry [" . $entry->getId() . "] distribution profile [" 
					. $distributionProfile->getId() . "] status [" . $entryDistribution->getStatus() . "]");
			return null;
		} 
		
		$entryDistribution->setEntryId($entry->getId());
		$entryDistribution->setPartnerId($entry->getPartnerId());
		$entryDistribution->setDistributionProfileId($distributionProfile->getId());
		$entryDistribution->setStatus(EntryDistributionStatus::PENDING);
		
		self::assignFlavorAssets($entryDistribution, $entry, $distributionProfile);
		self::assignThumbAssets($entryDistribution, $entry, $distributionProfile);
		self::assignAssets($entryDistribution, $entry, $distributionProfile);
		
		$entryDistribution->save(); // need to save before checking validations
		$validationErrors = $distributionProfile->validateForSubmission($entryDistribution, DistributionAction::SUBMIT);
		$entryDistribution->setValidationErrorsArray($validationErrors);

		return $entryDistribution;
	}
	
	public static function getSearchStringNoDistributionProfiles()
	{
		return "contentDistNoProfiles";
	}
	
	public static function getSearchStringDistributionProfile($distributionProfileId = null)
	{
		if($distributionProfileId)
			return "contentDistProfile{$distributionProfileId}";
			
		return "contentDistProfile{$distributionProfileId}";
	}
	
	public static function getSearchStringDistributionSunStatus($distributionSunStatus, $distributionProfileId = null, $isIndex = true)
	{
		if($distributionProfileId)
			if($isIndex)
				return "entryDistSun{$distributionSunStatus}P{$distributionProfileId} entryDistSun{$distributionSunStatus}";
			else
				return "entryDistSun{$distributionSunStatus}P{$distributionProfileId}";
			
		
		return "entryDistSun{$distributionSunStatus}";
	}
	
	public static function getSearchStringDistributionFlag($entryDistributionFlag, $distributionProfileId = null, $isIndex = true)
	{
		if(is_null($entryDistributionFlag))
			$entryDistributionFlag = EntryDistributionDirtyStatus::NONE;
			
		if($distributionProfileId)
			if($isIndex)
				return "entryDistFlag{$entryDistributionFlag}P{$distributionProfileId} entryDistFlag{$entryDistributionFlag}";
			else
				return "entryDistFlag{$entryDistributionFlag}P{$distributionProfileId}";
			
		return "entryDistFlag{$entryDistributionFlag}";
	}
	
	public static function getSearchStringDistributionStatus($entryDistributionStatus, $distributionProfileId = null, $isIndex = true)
	{
		if($distributionProfileId)
			if($isIndex)
				return "entryDistStatus{$entryDistributionStatus}P{$distributionProfileId} entryDistStatus{$entryDistributionStatus}";
			else
				return "entryDistStatus{$entryDistributionStatus}P{$distributionProfileId}";
			
		return "entryDistStatus{$entryDistributionStatus}";
	}
	
	public static function getSearchStringDistributionValidationError($validationErrorType = null, $distributionProfileId = null, $isIndex = true)
	{
		if($distributionProfileId)
			if($isIndex)
				return "entryDistErr{$validationErrorType}P{$distributionProfileId} entryDistErr{$validationErrorType}";
			else
				return "entryDistErr{$validationErrorType}P{$distributionProfileId}";
			
		return "entryDistErr{$validationErrorType}";
	}
	
	public static function getSearchStringDistributionHasValidationError($distributionProfileId = null, $isIndex = true)
	{
		if($distributionProfileId)
			if($isIndex)
				return "entryDistHasErr{$distributionProfileId} entryDistHasErr";
			else
				return "entryDistHasErr{$distributionProfileId}";
			
		return "entryDistHasErr";
	}
	
	public static function getSearchStringDistributionHasNoValidationError($distributionProfileId = null)
	{
		if($distributionProfileId)
			return "contentDistProfile{$distributionProfileId} -\"entryDistHasErr{$distributionProfileId}\"";
			
		return "contentDistProfile -\"entryDistHasErr\"";
	}
	
	public static function getEntrySearchValues(entry $entry)
	{
		if(!ContentDistributionPlugin::isAllowedPartner($entry->getPartnerId()))
			return null;
			
		$entryDistributions = EntryDistributionPeer::retrieveByEntryId($entry->getId());
		if(!count($entryDistributions))
			return self::getSearchStringNoDistributionProfiles();
			
		$searchValues = array();
		foreach($entryDistributions as $entryDistribution)
		{
			$distributionProfileId = $entryDistribution->getDistributionProfileId();
			$searchValues[] = self::getSearchStringDistributionProfile($distributionProfileId);
			$searchValues[] = self::getSearchStringDistributionStatus($entryDistribution->getStatus(), $distributionProfileId, true);
			$searchValues[] = self::getSearchStringDistributionFlag($entryDistribution->getDirtyStatus(), $distributionProfileId, true);
			$searchValues[] = self::getSearchStringDistributionSunStatus($entryDistribution->getSunStatus(), $distributionProfileId, true);
			
			$validationErrors = $entryDistribution->getValidationErrors();
			if(count($validationErrors))
				$searchValues[] = self::getSearchStringDistributionHasValidationError($distributionProfileId, true);
				
			foreach($validationErrors as $validationError)
				$searchValues[] = self::getSearchStringDistributionValidationError($validationError->getErrorType(), $distributionProfileId, true);
		}
		return implode(' ', $searchValues);
	}
	
	public static function addAssetIdsToEntryDistribution($entryDistribution, $distributionProfile)
	{
		$dbEntry = entryPeer::retrieveByPK($entryDistribution->getEntryId());
		if (!$dbEntry)
		{
			KalturaLog::log("EntryId not found [".$entryDistribution->getEntryId()."]");
			return false;
		}
		$ret_val = self::assignAssets($entryDistribution,  $dbEntry, $distributionProfile);
		$entryDistribution->save();
		return $ret_val;
	}

	private static function shouldWaitForSunrise($entryDistribution, $distributionProfile)
	{
		$sunrise = $entryDistribution->getSunrise(null);
		if($sunrise)
		{
			$distributionProvider = $distributionProfile->getProvider();
			if($distributionProvider && !$distributionProvider->isScheduleUpdateEnabled()
				&& !$distributionProvider->isAvailabilityUpdateEnabled())
			{
				$sunrise -= $distributionProvider->getJobIntervalBeforeSunrise();
				if($sunrise > time())
					return true;
			}
		}
		return false;
	}

	private static function updateStatusToQueued($entryDistribution)
	{
		if (!in_array($entryDistribution->getStatus(), array(EntryDistributionStatus::IMPORT_SUBMITTING, EntryDistributionStatus::IMPORT_UPDATING)) )
		{
			$entryDistribution->setStatus(EntryDistributionStatus::QUEUED);
			$entryDistribution->save();
		}
		KalturaLog::info("EntryDistribution id [".$entryDistribution->getIntId()."] on Entry [".$entryDistribution->getEntryId() 
			."] has status [".$entryDistribution->getStatus() ."] and will be submitted when ready");
	}

	private static function shouldSubmitNow($submitWhenReady, $entryDistribution, $distributionProfile)
	{
		if ($submitWhenReady && !myEntryUtils::isEntryReady($entryDistribution->getEntryId()))
			return false;
		if (self::shouldWaitForSunrise($entryDistribution, $distributionProfile))
			return false;
		return true;
	}

	/**
	 * @param $flavorIds - array of flavor ids
	 * @return string
	 * @throws Exception
	 */
	public static function getFilteredFlavorAssets($flavorAssetsIds, $entryId)
	{
		$tempFlavorsParams = flavorParamsConversionProfilePeer::getTempFlavorsParams($entryId);

		$tempFlavorsParamsIds = array();
		foreach ($tempFlavorsParams as $flavorParams)
		{
			/**
			 * @var flavorParamsConversionProfile $flavorParams
			 */
			$tempFlavorsParamsIds[] = $flavorParams->getFlavorParamsId();
		}

		assetPeer::setUseCriteriaFilter(false);
		$flavorAssets = assetPeer::retrieveByIds($flavorAssetsIds);
		assetPeer::setUseCriteriaFilter(true);

		$outFlavors = array();
		foreach ($flavorAssets as $asset)
		{
			/**
			 * @var flavorAsset $asset
			 */
			if ($asset->getIsOriginal() && $asset->getStatus() == flavorAsset::ASSET_STATUS_TEMP)
				continue;
			if (in_array($asset->getFlavorParamsId(), $tempFlavorsParamsIds))
				continue;

			$outFlavors[] = $asset->getId();
		}
		return $outFlavors;
	}
}
