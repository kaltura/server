<?php

class kBusinessPostConvertDL
{
	public static function getReadyBehavior(flavorAsset $flavorAsset, conversionProfile2 $profile = null)
	{
		if($flavorAsset->getIsOriginal())
		{
			if(!$profile)
			{
				try{
					$profile = myPartnerUtils::getConversionProfile2ForEntry($flavorAsset->getEntryId());
				}
				catch(Exception $e)
				{
					KalturaLog::err($e->getMessage());
				}
			}
		
			if($profile)
			{
				$flavorParamsConversionProfile = flavorParamsConversionProfilePeer::retrieveByFlavorParamsAndConversionProfile($flavorAsset->getFlavorParamsId(), $profile->getId());
				if($flavorParamsConversionProfile)
					return $flavorParamsConversionProfile->getReadyBehavior();
			}
		}
		
		$targetFlavor = flavorParamsOutputPeer::retrieveByFlavorAssetId($flavorAsset->getId());
		if($targetFlavor)
			return $targetFlavor->getReadyBehavior();
			
		return flavorParamsConversionProfile::READY_BEHAVIOR_INHERIT_FLAVOR_PARAMS;
	}
	
	public static function handleFlavorReady(BatchJob $dbBatchJob, $flavorAssetId)
	{
		// verifies that flavor asset created
		if(!$flavorAssetId)
			throw new APIException(APIErrors::INVALID_FLAVOR_ASSET_ID, $flavorAssetId);
	
		$currentFlavorAsset = flavorAssetPeer::retrieveById($flavorAssetId);
		// verifies that flavor asset exists
		if(!$currentFlavorAsset)
			throw new APIException(APIErrors::INVALID_FLAVOR_ASSET_ID, $flavorAssetId);

		// if the flavor deleted then it shouldn't be taken into ready calculations
		if($currentFlavorAsset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_DELETED)
			return $currentFlavorAsset;
		
//		Remarked because we want the original flavor ready behavior to work the same as other flavors
//
//		$rootBatchJob = $dbBatchJob->getRootJob();
//		
//		// happens in case of post convert on the original (in case of bypass)
//		if($rootBatchJob && $currentFlavorAsset->getIsOriginal())
//		{
//			kJobsManager::updateBatchJob($rootBatchJob, BatchJob::BATCHJOB_STATUS_FINISHED);
//			return $dbBatchJob;
//		}
					
		$sourceMediaInfo = mediaInfoPeer::retrieveOriginalByEntryId($dbBatchJob->getEntryId());
		$productMediaInfo = mediaInfoPeer::retrieveByFlavorAssetId($currentFlavorAsset->getId());
		$targetFlavor = flavorParamsOutputPeer::retrieveByFlavorAssetId($currentFlavorAsset->getId());
		
		// don't validate in case of bypass, in case target flavor or media info are null 
		if($dbBatchJob->getJobSubType() != BatchJob::BATCHJOB_SUB_TYPE_POSTCONVERT_BYPASS && $targetFlavor && $productMediaInfo)
		{
			try{
				$productFlavor = KDLWrap::CDLValidateProduct($sourceMediaInfo, $targetFlavor, $productMediaInfo);
			}
			catch(Exception $e){
				KalturaLog::err('KDL Error: ' . print_r($e, true));
			}
			
			$err = kBusinessConvertDL::parseFlavorDescription($productFlavor);
			KalturaLog::debug("BCDL: job id [" . $dbBatchJob->getId() . "] flavor params output id [" . $targetFlavor->getId() . "] flavor asset id [" . $currentFlavorAsset->getId() . "] desc: $err");

			if(!$productFlavor->IsValid())
			{
				$description = $currentFlavorAsset->getDescription() . "\n$err";
				
				// mark the asset as ready 
				$currentFlavorAsset->setDescription($description);
				$currentFlavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_ERROR);
				$currentFlavorAsset->save();
				
				if(!kConf::get('ignore_cdl_failure'))
				{
					kJobsManager::failBatchJob($dbBatchJob, $err);
					return null;
				}
			}
		}
		
		// mark the asset as ready 
		$currentFlavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_READY);
		$currentFlavorAsset->save();
		
		kFlowHelper::generateThumbnailsFromFlavor($dbBatchJob->getEntryId(), $dbBatchJob, $currentFlavorAsset->getFlavorParamsId());
		
		return $currentFlavorAsset;
	}
	
	/**
	 * @param BatchJob $dbBatchJob
	 * @param flavorAsset $currentFlavorAsset
	 * @return BatchJob
	 */
	public static function handleConvertFinished(BatchJob $dbBatchJob, flavorAsset $currentFlavorAsset)
	{
		KalturaLog::debug("entry id [" . $dbBatchJob->getEntryId() . "] flavor asset id [" . $currentFlavorAsset->getId() . "]");
		$profile = null;
		try{
			$profile = myPartnerUtils::getConversionProfile2ForEntry($currentFlavorAsset->getEntryId());
		}
		catch(Exception $e)
		{
			KalturaLog::err($e->getMessage());
		}
		KalturaLog::debug("profile [" . $profile->getId() . "]");
		
		$currentReadyBehavior = self::getReadyBehavior($currentFlavorAsset, $profile);
		
		$rootBatchJob = $dbBatchJob->getRootJob();
		if($rootBatchJob)
			KalturaLog::debug("root batch job id [" . $rootBatchJob->getId() . "] type [" . $rootBatchJob->getJobType() . "]");
		
		// update the root job end exit
		if($rootBatchJob && $rootBatchJob->getJobType() == BatchJobType::REMOTE_CONVERT)
		{
			KalturaLog::debug("finish remote convert root job");
			kJobsManager::updateBatchJob($rootBatchJob, BatchJob::BATCHJOB_STATUS_FINISHED);
			return $dbBatchJob;
		}
		
		// update the root job end exit
		if($rootBatchJob && $rootBatchJob->getJobType() == BatchJobType::BULKDOWNLOAD)
		{
			$siblingJobs = $rootBatchJob->getChildJobs();
			foreach($siblingJobs as $siblingJob)
			{
				// checking only conversion child jobs
				if(
					$siblingJob->getJobType() != BatchJobType::CONVERT
					&&
					$siblingJob->getJobType() != BatchJobType::CONVERT_COLLECTION
					&&
					$siblingJob->getJobType() != BatchJobType::POSTCONVERT
					)
					continue;
					
				// if not complete leave function
				if($siblingJob->getStatus() != BatchJob::BATCHJOB_STATUS_FINISHED)
				{
					KalturaLog::debug("job id [" . $siblingJob->getId() . "] status [" . $siblingJob->getStatus() . "]");
					return $dbBatchJob;
				}
			}
			KalturaLog::debug("finish bulk download root job");
			// all child jobs completed
			kJobsManager::updateBatchJob($rootBatchJob, BatchJob::BATCHJOB_STATUS_FINISHED);
			return $dbBatchJob;
		}
		
		$inheritedFlavorParamsIds = array();
		$requiredFlavorParamsIds = array();
		$flavorParamsConversionProfileItems = flavorParamsConversionProfilePeer::retrieveByConversionProfile($profile->getId());
		foreach($flavorParamsConversionProfileItems as $flavorParamsConversionProfile)
		{
			if($flavorParamsConversionProfile->getReadyBehavior() == flavorParamsConversionProfile::READY_BEHAVIOR_REQUIRED)
				$requiredFlavorParamsIds[$flavorParamsConversionProfile->getFlavorParamsId()] = true;
			if($flavorParamsConversionProfile->getReadyBehavior() == flavorParamsConversionProfile::READY_BEHAVIOR_INHERIT_FLAVOR_PARAMS)
				$inheritedFlavorParamsIds[] = $flavorParamsConversionProfile->getFlavorParamsId();
		}
		$flavorParamsItems = flavorParamsPeer::retrieveByPKs($inheritedFlavorParamsIds);
		foreach($flavorParamsItems as $flavorParams)
		{
			if($flavorParams->getReadyBehavior() == flavorParamsConversionProfile::READY_BEHAVIOR_REQUIRED)
				$requiredFlavorParamsIds[$flavorParamsConversionProfile->getFlavorParamsId()] = true;			
		}
		KalturaLog::debug("required flavor params ids [" . print_r($requiredFlavorParamsIds, true) . "]");
		
		
		// go over all the flavor assets of the entry
		$inCompleteFlavorIds = array();
		$siblingFlavorAssets = flavorAssetPeer::retrieveByEntryId($dbBatchJob->getEntryId());
		foreach($siblingFlavorAssets as $siblingFlavorAsset)
		{
			KalturaLog::debug("sibling flavor asset id [" . $siblingFlavorAsset->getId() . "] flavor params id [" . $siblingFlavorAsset->getFlavorParamsId() . "]");
				
			if($siblingFlavorAsset->getId() == $currentFlavorAsset->getId())
			{
				KalturaLog::debug("sibling flavor asset id [" . $siblingFlavorAsset->getId() . "] is current");
				if(isset($requiredFlavorParamsIds[$siblingFlavorAsset->getFlavorParamsId()]))
					unset($requiredFlavorParamsIds[$siblingFlavorAsset->getFlavorParamsId()]);
					
				continue;
			}
			
			// don't mark any incomplete flag
			if($siblingFlavorAsset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_READY)
			{
				KalturaLog::debug("sibling flavor asset id [" . $siblingFlavorAsset->getId() . "] is ready");
				if(isset($requiredFlavorParamsIds[$siblingFlavorAsset->getFlavorParamsId()]))
					unset($requiredFlavorParamsIds[$siblingFlavorAsset->getFlavorParamsId()]);
					
				continue;
			}
				
			$readyBehavior = self::getReadyBehavior($siblingFlavorAsset, $profile);
			
			if($readyBehavior == flavorParamsConversionProfile::READY_BEHAVIOR_IGNORE)
			{
				KalturaLog::debug("sibling flavor asset id [" . $siblingFlavorAsset->getId() . "] is ignored");
				continue;
			}
			
			if(
					$siblingFlavorAsset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_QUEUED 
				||	$siblingFlavorAsset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_CONVERTING 
				||	$siblingFlavorAsset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_IMPORTING 
				||	$siblingFlavorAsset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_VALIDATING)
			{
				KalturaLog::debug("sibling flavor asset id [" . $siblingFlavorAsset->getId() . "] is incomplete");
				$inCompleteFlavorIds[] = $siblingFlavorAsset->getFlavorParamsId();
			}
			
			if($readyBehavior == flavorParamsConversionProfile::READY_BEHAVIOR_REQUIRED)
			{
				KalturaLog::debug("sibling flavor asset id [" . $siblingFlavorAsset->getId() . "] is required");
				$requiredFlavorParamsIds[$siblingFlavorAsset->getFlavorParamsId()] = true;
			}
		}
				
		KalturaLog::debug("left required flavor params ids [" . print_r($requiredFlavorParamsIds, true) . "]");				
		KalturaLog::debug("left incomplete flavor ids [" . print_r($inCompleteFlavorIds, true) . "]");
		
		if(count($requiredFlavorParamsIds))
		{
			$inCompleteRequiredFlavorParamsIds = array_keys($requiredFlavorParamsIds);
			foreach($inCompleteRequiredFlavorParamsIds as $inCompleteFlavorId)
				$inCompleteFlavorIds[] = $inCompleteFlavorId;
				
			KalturaLog::debug('Convert Finished - has In-Compelte Required flavors [[' . print_r($inCompleteRequiredFlavorParamsIds, true) . ']');
		} 
		elseif($currentReadyBehavior == flavorParamsConversionProfile::READY_BEHAVIOR_OPTIONAL || $currentReadyBehavior == flavorParamsConversionProfile::READY_BEHAVIOR_REQUIRED)
		{
			// mark the entry as ready if all required conversions completed or any of the optionals
			kBatchManager::updateEntry($dbBatchJob, entryStatus::READY);
		}
		
		// no need to finished the root job
		if(!$rootBatchJob)
		{
			KalturaLog::debug('Convert Finished - no root job to close');
			return $dbBatchJob;
		}
			
		if(!count($inCompleteFlavorIds))
		{
			// mark the context root job as finished only if all conversion jobs are completed
			kBatchManager::updateEntry($dbBatchJob, entryStatus::READY);
			
			if($rootBatchJob->getJobType() == BatchJobType::CONVERT_PROFILE)
				kJobsManager::updateBatchJob($rootBatchJob, BatchJob::BATCHJOB_STATUS_FINISHED);
		
			return $dbBatchJob;
		}
		
		KalturaLog::debug('Convert Finished - has In-Complete flavors [' . print_r($inCompleteFlavorIds, true) . ']');
	
		if($rootBatchJob->getJobType() != BatchJobType::CONVERT_PROFILE)
			return $dbBatchJob;
			
		$childJobs = $rootBatchJob->getChildJobs();
		KalturaLog::debug('Child jobs found [' . count($childJobs) . ']');
		if(count($childJobs) > 1)
		{
			$allDone = true;
			foreach($childJobs as $childJob)
			{
				if($childJob->getId() != $rootBatchJob->getId() && $childJob->getStatus() != BatchJob::BATCHJOB_STATUS_FINISHED)
				{
					KalturaLog::debug('Child job id [' . $childJob->getId() . '] status [' . $childJob->getStatus() . ']');
					$allDone = false;
				}
			}
					
			if($allDone)
			{
				KalturaLog::debug('All child jobs done, closing profile');
				kJobsManager::updateBatchJob($rootBatchJob, BatchJob::BATCHJOB_STATUS_FINISHED);
			}
		}
		
		return $dbBatchJob;
	}
	
	/**
	 * @param BatchJob $dbBatchJob
	 * @param kConvertCollectionJobData $data
	 * @param int $engineType
	 * @return boolean
	 */
	public static function handleConvertCollectionFailed(BatchJob $dbBatchJob, kConvertCollectionJobData $data, $engineType)
	{
		$collectionFlavors = array();
		foreach($data->getFlavors() as $flavor)
			$collectionFlavors[$flavor->getFlavorAssetId()] = $flavor;
				
		// find the root job
		$rootBatchJob = $dbBatchJob->getRootJob();
			
		$hasIncomplete = false;
		$shouldFailProfile = false;
		$flavorAssets = flavorAssetPeer::retrieveByEntryId($dbBatchJob->getEntryId());
		foreach($flavorAssets as $flavorAsset)
		{
			if(isset($collectionFlavors[$flavorAsset->getId()]))
			{
				$flavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_ERROR);
				$flavorAsset->save();
				
				if(!$rootBatchJob)
					continue;
				
				$flavorData = $collectionFlavors[$flavorAsset->getId()];
				if($flavorData->getReadyBehavior() == flavorParamsConversionProfile::READY_BEHAVIOR_REQUIRED)
					$shouldFailProfile = true;
					
				continue;
			}
			
			if($flavorAsset->getIsOriginal())
				continue;
				
			if(
					$flavorAsset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_QUEUED
				||	$flavorAsset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_CONVERTING
				||	$flavorAsset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_IMPORTING
				||	$flavorAsset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_VALIDATING)
				$hasIncomplete = true;
		}
		
		if(!$rootBatchJob)
			return false;
			
		if($rootBatchJob->getJobType() != BatchJobType::CONVERT_PROFILE)
			return false;
			
		if($shouldFailProfile || !$hasIncomplete)
			kJobsManager::failBatchJob($rootBatchJob, "Job " . $dbBatchJob->getId() . " failed");
		
		return false;
	}
	
	public static function handleConvertFailed(BatchJob $dbBatchJob, $engineType, $flavorAssetId, $flavorParamsOutputId, $mediaInfoId)
	{
		$flavorAsset = flavorAssetPeer::retrieveById($flavorAssetId);
		// verifies that flavor asset exists
		if(!$flavorAsset)
			throw new APIException(APIErrors::INVALID_FLAVOR_ASSET_ID, $flavorAssetId);
		
		$flavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_ERROR);
		$flavorAsset->save();
		
		
		// try to create a convert job with the next engine
		if(!is_null($engineType))
		{
			$data = $dbBatchJob->getData();
			if($data instanceof kConvartableJobData)
			{
				$data->incrementOperationSet();
				$dbBatchJob->setData($data);
				$dbBatchJob->save();
			}
			
			$newDbBatchJob = kBusinessPreConvertDL::redecideFlavorConvert($flavorAssetId, $flavorParamsOutputId, $mediaInfoId, $dbBatchJob, $engineType);
			if($newDbBatchJob)
				return true;
		}
			
		// find the root job
		$rootBatchJob = $dbBatchJob->getRootJob();
		if(!$rootBatchJob)
			return false;
		
		// the root is already failed
		if($rootBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FAILED || $rootBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FATAL)
			return false;

		// failing a remote root job 
		if($rootBatchJob->getJobType() == BatchJobType::REMOTE_CONVERT)
		{
			kJobsManager::failBatchJob($rootBatchJob, "Convert job " . $dbBatchJob->getId() . " failed");
			return false;
		}
			
		// bulk download root job no need to handle 
		if($rootBatchJob->getJobType() == BatchJobType::BULKDOWNLOAD)
		{
			kJobsManager::failBatchJob($rootBatchJob, "Convert job " . $dbBatchJob->getId() . " failed");
			return false;
		}
			
		if(is_null($flavorParamsOutputId))
		{
			kJobsManager::failBatchJob($rootBatchJob, "Job " . $dbBatchJob->getId() . " failed");
			kBatchManager::updateEntry($dbBatchJob, entryStatus::ERROR_CONVERTING);
			return false;
		}
		
		$readyBehavior = $dbBatchJob->getData()->getReadyBehavior();
		if($readyBehavior == flavorParamsConversionProfile::READY_BEHAVIOR_REQUIRED)
		{
			kJobsManager::failBatchJob($rootBatchJob, "Job " . $dbBatchJob->getId() . " failed");
			kBatchManager::updateEntry($dbBatchJob, entryStatus::ERROR_CONVERTING);
			return false;
		}
		
		// failing the root profile job if all child jobs failed 
		if($rootBatchJob->getJobType() != BatchJobType::CONVERT_PROFILE)
			return false;
		
		$siblingJobs = $rootBatchJob->getChildJobs();
		foreach($siblingJobs as $siblingJob)
		{
			// not conversion job and should be ignored
			if($siblingJob->getJobType() != BatchJobType::CONVERT && $siblingJob->getJobType() != BatchJobType::POSTCONVERT)
				continue;
					
			// found child flavor asset that hasn't failed, no need to fail the root job
			$siblingFlavorAssetId = $siblingJob->getData()->getFlavorAssetId();
			$siblingFlavorAsset = flavorAssetPeer::retrieveById($siblingFlavorAssetId);
			if ($siblingFlavorAsset->getStatus() != flavorAsset::FLAVOR_ASSET_STATUS_ERROR &&
				$siblingFlavorAsset->getStatus() != flavorAsset::FLAVOR_ASSET_STATUS_NOT_APPLICABLE &&
				$siblingFlavorAsset->getStatus() != flavorAsset::FLAVOR_ASSET_STATUS_DELETED)
				{
					return false;
				}
		}
				
		// all conversions failed, should fail the root job
		kJobsManager::failBatchJob($rootBatchJob, "All conversions failed");
		kBatchManager::updateEntry($dbBatchJob, entryStatus::ERROR_CONVERTING);
		return false;
	}
	
}