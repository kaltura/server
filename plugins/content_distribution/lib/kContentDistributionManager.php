<?php
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
		KalturaLog::debug("Adding entry [" . $entry->getId() . "] for distribution profile [" . $distributionProfile->getId() . "]");
		$entryDistribution = self::createEntryDistribution($entry, $distributionProfile);
		$entryDistribution->save();
		
		if($submit)
			self::submitAddEntryDistribution($entryDistribution, $distributionProfile);
			
		return $entryDistribution;
	}
	
	/**
	 * @param EntryDistribution $entryDistribution
	 * @param DistributionProfile $distributionProfile
	 * @return BatchJob
	 */
	public static function addSubmitAddJob(EntryDistribution $entryDistribution, DistributionProfile $distributionProfile)
	{
 		$jobData = new kDistributionJobData();
 		$jobData->setDistributionProfileId($entryDistribution->getDistributionProfileId());
 		$jobData->setEntryDistributionId($entryDistribution->getId());
 		
		$batchJob = new BatchJob();
		$batchJob->setEntryId($entryDistribution->getEntryId());
		$batchJob->setPartnerId($entryDistribution->getPartnerId());
		
		$jobType = ContentDistributionBatchJobType::get()->coreValue(ContentDistributionBatchJobType::DISTRIBUTION_SUBMIT);
		$jobSubType = $distributionProfile->getProviderType();
	
		return kJobsManager::addJob($batchJob, $jobData, $jobType, $jobSubType);
	}
	
	/**
	 * @param EntryDistribution $entryDistribution
	 * @param DistributionProfile $distributionProfile
	 * @return BatchJob
	 */
	public static function addSubmitUpdateJob(EntryDistribution $entryDistribution, DistributionProfile $distributionProfile)
	{
 		$jobData = new kDistributionJobData();
 		$jobData->setDistributionProfileId($entryDistribution->getDistributionProfileId());
 		$jobData->setEntryDistributionId($entryDistribution->getId());
 		$jobData->setRemoteId($entryDistribution->getRemoteId());
 		
		$batchJob = new BatchJob();
		$batchJob->setEntryId($entryDistribution->getEntryId());
		$batchJob->setPartnerId($entryDistribution->getPartnerId());
		
		$jobType = ContentDistributionBatchJobType::get()->coreValue(ContentDistributionBatchJobType::DISTRIBUTION_UPDATE);
		$jobSubType = $distributionProfile->getProviderType();
	
		return kJobsManager::addJob($batchJob, $jobData, $jobType, $jobSubType);
	}
	
	/**
	 * @param EntryDistribution $entryDistribution
	 * @param DistributionProfile $distributionProfile
	 * @return BatchJob
	 */
	public static function addSubmitDeleteJob(EntryDistribution $entryDistribution, DistributionProfile $distributionProfile)
	{
 		$jobData = new kDistributionJobData();
 		$jobData->setDistributionProfileId($entryDistribution->getDistributionProfileId());
 		$jobData->setEntryDistributionId($entryDistribution->getId());
 		$jobData->setRemoteId($entryDistribution->getRemoteId());
 		
		$batchJob = new BatchJob();
		$batchJob->setEntryId($entryDistribution->getEntryId());
		$batchJob->setPartnerId($entryDistribution->getPartnerId());
		
		$jobType = ContentDistributionBatchJobType::get()->coreValue(ContentDistributionBatchJobType::DISTRIBUTION_DELETE);
		$jobSubType = $distributionProfile->getProviderType();
	
		return kJobsManager::addJob($batchJob, $jobData, $jobType, $jobSubType);
	}
	
	/**
	 * @param EntryDistribution $entryDistribution
	 * @param DistributionProfile $distributionProfile
	 * @return BatchJob
	 */
	public static function submitDeleteEntryDistribution(EntryDistribution $entryDistribution, DistributionProfile $distributionProfile)
	{
		$distributionProvider = $distributionProfile->getProvider();
		if($distributionProvider->isDeleteEnabled())
			return self::addSubmitDeleteJob($entryDistribution, $distributionProfile);
			
		if(!$distributionProvider->isScheduleUpdateEnabled() || !$distributionProvider->isUpdateEnabled())
		{
			KalturaLog::debug("Entry distribution [" . $entryDistribution->getId() . "] provider [" . $distributionProfile->getProviderType() . "] doesn't support delete or update");
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
	 * @param bool $submitWhenReady
	 * @return BatchJob
	 */
	public static function submitAddEntryDistribution(EntryDistribution $entryDistribution, DistributionProfile $distributionProfile, $submitWhenReady = true)
	{
		if($submitWhenReady && $entryDistribution->getStatus() != EntryDistributionStatus::QUEUED)
		{
			$entryDistribution->setStatus(EntryDistributionStatus::QUEUED);
			$entryDistribution->save();
		}
		
		$validationErrors = $entryDistribution->getValidationErrors();
		if(!count($validationErrors))
		{
			KalturaLog::debug("Entry ready for submit");
			$sunrise = $entryDistribution->getSunrise(null);
			if($sunrise)
			{
				$distributionProvider = $distributionProfile->getProvider();
				if(!$distributionProvider->isScheduleUpdateEnabled())
				{
					$sunrise -= $distributionProvider->getJobIntervalBeforeSunrise();
					if($sunrise > time())
					{
						KalturaLog::log("Will be sent on exact time [$sunrise] for sunrise time [" . $entryDistribution->getSunrise() . "]");
						$entryDistribution->setDirtyStatus(EntryDistributionDirtyStatus::SUBMIT_REQUIRED);
						$entryDistribution->save();
						return null;
					}
				}
			}
			return self::addSubmitAddJob($entryDistribution, $distributionProfile);
		}
		
		KalturaLog::debug("Validation errors found");
		$entry = entryPeer::retrieveByPK($entryDistribution->getEntryId());
		if(!$entry)
		{
			KalturaLog::err("Entry [" . $entryDistribution->getEntryId() . "] not found");
			return null;
		}
			
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
		
			if($validationError->getErrorType() == DistributionErrorType::MISSING_THUMBNAIL && in_array($validationError->getData(), $autoCreateThumbs))
			{
				$destThumbParams = thumbParamsPeer::retrieveByPK($validationError->getData());
				if($destThumbParams)
				{
					KalturaLog::log("Adding thumbnail [" . $validationError->getData() . "] to entry [" . $entryDistribution->getEntryId() . "]");
					kBusinessPreConvertDL::decideThumbGenerate($entry, $destThumbParams);
				}
				else 
				{
					KalturaLog::err("Required thumbnail params not found [" . $validationError->getData() . "]");
				}	
			}
		}
		
		return null;
	}
	
	/**
	 * @param entry $entry
	 * @param DistributionProfile $distributionProfile
	 * @return EntryDistribution
	 */
	public static function createEntryDistribution(entry $entry, DistributionProfile $distributionProfile)
	{
		$entryDistribution = new EntryDistribution();
		$entryDistribution->setEntryId($entry->getId());
		$entryDistribution->setPartnerId($entry->getPartnerId());
		$entryDistribution->setDistributionProfileId($distributionProfile->getId());
		$entryDistribution->setStatus(EntryDistributionStatus::PENDING);
		$entryDistribution->setSunrise($entry->getStartDate(null));
		$entryDistribution->setSunset($entry->getEndDate(null));
		
		$requiredFlavorParamsIds = $distributionProfile->getRequiredFlavorParamsIds();
		$optionalFlavorParamsIds = $distributionProfile->getRequiredFlavorParamsIds();
		$flavorAssetIds = flavorAssetPeer::getReadyIdsByParamsIds(array_merge($requiredFlavorParamsIds, $optionalFlavorParamsIds));
		
		$entryDistribution->setFlavorAssetIds($flavorAssetIds);
		
		$thumbDimensions = $distributionProfile->getThumbDimensionsObjects();
		$thumbDimensionsWithKeys = array();
		foreach($thumbDimensions as $thumbDimension)
			$thumbDimensionsWithKeys[$thumbDimension->getKey()] = $thumbDimension;
		
		$thumbAssetsIds = array();
		$requiredThumbParamsIds = $distributionProfile->getAutoCreateThumb();
		$thumbAssets = thumbAssetPeer::retreiveReadyByEntryId($entry->getId());
		foreach($thumbAssets as $thumbAsset)
		{
			if(in_array($thumbAsset->getFlavorParamsId(), $requiredThumbParamsIds))
			{
				$thumbAssetsIds[] = $thumbAsset->getId();
				KalturaLog::debug("Assign humb asset [" . $thumbAsset->getId() . "] from required thumbnail params ids");
				continue;
			}
			
			$key = $thumbAssets->getWidth() . 'x' . $thumbAssets->getHeight();
			if(isset($thumbDimensionsWithKeys[$key]))
			{
				unset($thumbDimensionsWithKeys[$key]);
				KalturaLog::debug("Assign humb asset [" . $thumbAsset->getId() . "] from dimension [$key]");
				$thumbAssetsIds[] = $thumbAsset->getId();
			}
		}
		$entryDistribution->setThumbAssetIds($thumbAssetsIds);
		
		$validationErrors = $distributionProfile->validate($entryDistribution, DistributionAction::SUBMIT);
		$entryDistribution->setValidationErrors($validationErrors);
		if(count($validationErrors))
			KalturaLog::debug("Validation errors [" . print_r($validationErrors, true) . "]");

		return $entryDistribution;
	}
	
	public static function getSearchStringDistributionProfile($distributionProfileId)
	{
		return "^contentDistProfile $distributionProfileId$";
	}
	
	public static function getSearchStringDistributionSunStatus($distributionSunStatus, $distributionProfileId = null)
	{
		if($distributionProfileId)
			return "^entryDistSun $distributionSunStatus $distributionProfileId$";
			
		return "^entryDistSun $distributionSunStatus$";
	}
	
	public static function getSearchStringDistributionFlag($entryDistributionFlag, $distributionProfileId = null)
	{
		if($distributionProfileId)
			return $conditions[] = "^entryDistFlag $entryDistributionFlag $distributionProfileId$";;
			
		return $conditions[] = "^entryDistFlag $entryDistributionFlag$";;
	}
	
	public static function getSearchStringDistributionStatus($entryDistributionStatus, $distributionProfileId = null)
	{
		if($distributionProfileId)
			return $conditions[] = "^entryDistStatus $entryDistributionStatus $distributionProfileId$";;
			
		return $conditions[] = "^entryDistStatus $entryDistributionStatus$";;
	}
	
	public static function getEntrySearchValues(entry $entry)
	{
		if(!ContentDistributionPlugin::isAllowedPartner($entry->getPartnerId()))
			return null;
			
		$entryDistributions = EntryDistributionPeer::retrieveByEntryId($entry->getId());
		$searchValues = array();
		foreach($entryDistributions as $entryDistribution)
		{
			$distributionProfileId = $entryDistribution->getDistributionProfileId();
			$searchValues[] = self::getSearchStringDistributionProfile($distributionProfileId);
			$searchValues[] = self::getSearchStringDistributionStatus($entryDistribution->getStatus(), $distributionProfileId);
			$searchValues[] = self::getSearchStringDistributionFlag($entryDistribution->getDirtyStatus(), $distributionProfileId);
			$searchValues[] = self::getSearchStringDistributionSunStatus($entryDistribution->getSunStatus(), $distributionProfileId);
		}
		return implode(',', $searchValues);
	}
}