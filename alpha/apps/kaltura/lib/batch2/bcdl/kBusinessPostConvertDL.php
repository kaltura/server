<?php

class kBusinessPostConvertDL
{
	public static function getReadyBehavior(flavorAsset $flavorAsset, conversionProfile2 $profile = null)
	{
		$targetFlavor = assetParamsOutputPeer::retrieveByAssetId($flavorAsset->getId());
		if($targetFlavor)
			return $targetFlavor->getReadyBehavior();
			
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
			
		return flavorParamsConversionProfile::READY_BEHAVIOR_NO_IMPACT;
	}
	
	public static function handleFlavorReady(BatchJob $dbBatchJob, $flavorAssetId)
	{
		// verifies that flavor asset created
		if(!$flavorAssetId)
			throw new APIException(APIErrors::INVALID_FLAVOR_ASSET_ID, $flavorAssetId);
	
		$currentFlavorAsset = assetPeer::retrieveById($flavorAssetId);
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
		$targetFlavor = assetParamsOutputPeer::retrieveByAssetId($currentFlavorAsset->getId());
		
		$postConvertData = $dbBatchJob->getData();
		$postConvertAssetType = BatchJob::POSTCONVERT_ASSET_TYPE_FLAVOR;
		if($postConvertData instanceof kPostConvertJobData)
			$postConvertAssetType = $postConvertData->getPostConvertAssetType();
		
		// don't validate in case of bypass, in case target flavor or media info are null 
		if($postConvertAssetType != BatchJob::POSTCONVERT_ASSET_TYPE_BYPASS && $targetFlavor && $productMediaInfo)
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
		$currentFlavorAsset->setStatusLocalReady();
		$currentFlavorAsset->save();
		
		$waitingFlavorAssets = assetPeer::retrieveByEntryIdAndStatus($currentFlavorAsset->getEntryId(), flavorAsset::FLAVOR_ASSET_STATUS_WAIT_FOR_CONVERT);
		$originalFlavorAsset = assetPeer::retrieveOriginalByEntryId($currentFlavorAsset->getEntryId());
		foreach ($waitingFlavorAssets as $waitingFlavorAsset) 
		{
			$flavor = assetParamsOutputPeer::retrieveByAsset($waitingFlavorAsset);
			KalturaLog::debug('Check waiting flavor asset ['.$waitingFlavorAsset->getId().']');
			if($dbBatchJob->getParentJob())
				$parentJob = $dbBatchJob->getParentJob();
			else 
				$parentJob = $dbBatchJob;
			kBusinessPreConvertDL::decideFlavorConvert($waitingFlavorAsset, $flavor, $originalFlavorAsset, null, null, $parentJob);
		}
		
		kFlowHelper::generateThumbnailsFromFlavor($dbBatchJob->getEntryId(), $dbBatchJob, $currentFlavorAsset->getFlavorParamsId());
		
		return $currentFlavorAsset;
	}
	
	/**
	 * @param BatchJob $dbBatchJob
	 * @param flavorAsset $currentFlavorAsset
	 * @return BatchJob
	 */
	public static function handleConvertFinished(BatchJob $dbBatchJob = null, flavorAsset $currentFlavorAsset)
	{
		KalturaLog::debug("entry id [" . $currentFlavorAsset->getEntryId() . "] flavor asset id [" . $currentFlavorAsset->getId() . "]");
		$profile = null;
		try{
			$profile = myPartnerUtils::getConversionProfile2ForEntry($currentFlavorAsset->getEntryId());
			KalturaLog::debug("profile [" . $profile->getId() . "]");
		}
		catch(Exception $e)
		{
			KalturaLog::err($e->getMessage());
		}
		
		$flavorSize = $currentFlavorAsset->getSize();
		if($dbBatchJob) {
			// Multiply by 1024 to get the file size in bytes.
			$dbBatchJob->putInCustomData("flavor_size", $flavorSize * 1024);
			$dbBatchJob->save();
		}
				
		$currentReadyBehavior = self::getReadyBehavior($currentFlavorAsset, $profile);
		KalturaLog::debug("Current ready behavior [$currentReadyBehavior]");
		if($currentReadyBehavior == flavorParamsConversionProfile::READY_BEHAVIOR_IGNORE)
			return $dbBatchJob;
		
		$rootBatchJob = null;
		if($dbBatchJob)
			$rootBatchJob = $dbBatchJob->getRootJob();
		if($rootBatchJob)
			KalturaLog::debug("root batch job id [" . $rootBatchJob->getId() . "] type [" . $rootBatchJob->getJobType() . "]");
		
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
				if(!in_array($siblingJob->getStatus(), BatchJobPeer::getClosedStatusList()))
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
		
		$requiredFlavorParamsIds = array();
		$flavorParamsConversionProfileItems = array();
		if($profile)
			$flavorParamsConversionProfileItems = flavorParamsConversionProfilePeer::retrieveByConversionProfile($profile->getId());
		
		foreach($flavorParamsConversionProfileItems as $flavorParamsConversionProfile)
		{
			if($flavorParamsConversionProfile->getReadyBehavior() == flavorParamsConversionProfile::READY_BEHAVIOR_REQUIRED)
				$requiredFlavorParamsIds[$flavorParamsConversionProfile->getFlavorParamsId()] = true;
		}
		KalturaLog::debug("required flavor params ids [" . print_r($requiredFlavorParamsIds, true) . "]");
		
		// go over all the flavor assets of the entry
		$inCompleteFlavorIds = array();
		$origianlAssetFlavorId = null;
		$siblingFlavorAssets = assetPeer::retrieveFlavorsByEntryId($currentFlavorAsset->getEntryId());
		foreach($siblingFlavorAssets as $siblingFlavorAsset)
		{
			KalturaLog::debug("sibling flavor asset id [" . $siblingFlavorAsset->getId() . "] flavor params id [" . $siblingFlavorAsset->getFlavorParamsId() . "]");
							
			// don't mark any incomplete flag
			if($siblingFlavorAsset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_READY)
			{
				KalturaLog::debug("sibling flavor asset id [" . $siblingFlavorAsset->getId() . "] is ready");
				if(isset($requiredFlavorParamsIds[$siblingFlavorAsset->getFlavorParamsId()]))
					unset($requiredFlavorParamsIds[$siblingFlavorAsset->getFlavorParamsId()]);
					
				continue;
			}
				
			$readyBehavior = self::getReadyBehavior($siblingFlavorAsset, $profile);
			
		    if ($siblingFlavorAsset->getStatus() == flavorAsset::ASSET_STATUS_EXPORTING)
			{
			    if ($siblingFlavorAsset->getIsOriginal())
			    {
			        $origianlAssetFlavorId = $siblingFlavorAsset->getFlavorParamsId();
			    }
			    else if ($readyBehavior != flavorParamsConversionProfile::READY_BEHAVIOR_IGNORE)
			    {
			        KalturaLog::debug("sibling flavor asset id [" . $siblingFlavorAsset->getId() . "] is incomplete");
				    $inCompleteFlavorIds[] = $siblingFlavorAsset->getFlavorParamsId();
			    }
			}			
			
			if($readyBehavior == flavorParamsConversionProfile::READY_BEHAVIOR_IGNORE)
			{
				KalturaLog::debug("sibling flavor asset id [" . $siblingFlavorAsset->getId() . "] is ignored");
				continue;
			}
			
			if(
					$siblingFlavorAsset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_QUEUED 
				||	$siblingFlavorAsset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_CONVERTING 
				||	$siblingFlavorAsset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_IMPORTING 
				||	$siblingFlavorAsset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_VALIDATING
				||  $siblingFlavorAsset->getStatus() == flavorAsset::ASSET_STATUS_WAIT_FOR_CONVERT)
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
				
			KalturaLog::debug('Convert Finished - has In-Complete Required flavors [[' . print_r($inCompleteRequiredFlavorParamsIds, true) . ']');
		} 
		elseif($currentFlavorAsset->getStatus() == asset::ASSET_STATUS_READY && ($currentReadyBehavior == flavorParamsConversionProfile::READY_BEHAVIOR_OPTIONAL || $currentReadyBehavior == flavorParamsConversionProfile::READY_BEHAVIOR_REQUIRED))
		{
			// mark the entry as ready if all required conversions completed or any of the optionals
			if($currentFlavorAsset->getentry()->getReplacedEntryId())
			{
				KalturaLog::debug('Entry is temporary replacement and requires all flavors to complete');
			}
			else
			{
				KalturaLog::debug('Mark the entry as ready');
				kBatchManager::updateEntry($currentFlavorAsset->getEntryId(), entryStatus::READY);
			}
		}
		
		if ($origianlAssetFlavorId) {
		    $inCompleteFlavorIds = array_diff($inCompleteFlavorIds, array($origianlAssetFlavorId));
		}
		
		if(!count($inCompleteFlavorIds))
		{
			KalturaLog::debug('Convert Finished');
			
			if($origianlAssetFlavorId && $rootBatchJob && $rootBatchJob->getJobType() == BatchJobType::CONVERT_PROFILE)
			{
        		kStorageExporter::exportSourceAssetFromJob($rootBatchJob);
			}
			else
			{
			    // mark the context root job as finished only if all conversion jobs are completed
    			kBatchManager::updateEntry($currentFlavorAsset->getEntryId(), entryStatus::READY);
    			
    			if($rootBatchJob && $rootBatchJob->getJobType() == BatchJobType::CONVERT_PROFILE)
    				kJobsManager::updateBatchJob($rootBatchJob, BatchJob::BATCHJOB_STATUS_FINISHED);
			}
			return $dbBatchJob;	
		}
		
		KalturaLog::debug('Convert Finished - has In-Complete flavors [' . print_r($inCompleteFlavorIds, true) . ']');
	
		if(!$rootBatchJob || $rootBatchJob->getJobType() != BatchJobType::CONVERT_PROFILE)
			return $dbBatchJob;
			
		$childJobs = $rootBatchJob->getChildJobs();
		KalturaLog::debug('Child jobs found [' . count($childJobs) . ']');
		$waitingFlavorAssets = assetPeer::retrieveByEntryIdAndStatus($currentFlavorAsset->getEntryId(), flavorAsset::FLAVOR_ASSET_STATUS_WAIT_FOR_CONVERT);
		KalturaLog::debug('Waiting assets found [' . count($waitingFlavorAssets) . ']');
		
		if(count($childJobs) > 1 && count($waitingFlavorAssets) < 1)
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
		$flavorAssets = assetPeer::retrieveFlavorsByEntryId($dbBatchJob->getEntryId());
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
		$flavorAsset = assetPeer::retrieveById($flavorAssetId);
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

		// bulk download root job no need to handle 
		if($rootBatchJob->getJobType() == BatchJobType::BULKDOWNLOAD)
		{
			kJobsManager::failBatchJob($rootBatchJob, "Convert job " . $dbBatchJob->getId() . " failed");
			return false;
		}
			
		if(is_null($flavorParamsOutputId))
		{
			kJobsManager::failBatchJob($rootBatchJob, "Job " . $dbBatchJob->getId() . " failed");
			kBatchManager::updateEntry($dbBatchJob->getEntryId(), entryStatus::ERROR_CONVERTING);
			return false;
		}
		
		$readyBehavior = $dbBatchJob->getData()->getReadyBehavior();
		if($readyBehavior == flavorParamsConversionProfile::READY_BEHAVIOR_REQUIRED)
		{
			kJobsManager::failBatchJob($rootBatchJob, "Job " . $dbBatchJob->getId() . " failed");
			kBatchManager::updateEntry($dbBatchJob->getEntryId(), entryStatus::ERROR_CONVERTING);
			return false;
		}
		
		// failing the root profile job if all child jobs failed 
		if($rootBatchJob->getJobType() != BatchJobType::CONVERT_PROFILE)
			return false;
		
		$siblingJobs = $rootBatchJob->getChildJobs();
		foreach($siblingJobs as $siblingJob)
		{
			/* @var $siblingJob BatchJob */
			
			// not conversion job and should be ignored
			if($siblingJob->getJobType() != BatchJobType::CONVERT && $siblingJob->getJobType() != BatchJobType::POSTCONVERT)
				continue;
		
			$jobData = $siblingJob->getData();
			if(!$jobData || (!($jobData instanceof kConvertJobData) && !($jobData instanceof kPostConvertJobData)))
			{
				KalturaLog::err("Job id [" . $siblingJob->getId() . "] has no valid job data");
				continue;
			}
			
			// found child flavor asset that hasn't failed, no need to fail the root job
			$siblingFlavorAssetId = $jobData->getFlavorAssetId();
			$siblingFlavorAsset = assetPeer::retrieveById($siblingFlavorAssetId);
			if ($siblingFlavorAsset->getStatus() != flavorAsset::FLAVOR_ASSET_STATUS_ERROR &&
				$siblingFlavorAsset->getStatus() != flavorAsset::FLAVOR_ASSET_STATUS_NOT_APPLICABLE &&
				$siblingFlavorAsset->getStatus() != flavorAsset::FLAVOR_ASSET_STATUS_DELETED)
				{
					return false;
				}
		}
				
		// all conversions failed, should fail the root job
		kJobsManager::failBatchJob($rootBatchJob, "All conversions failed");
		kBatchManager::updateEntry($dbBatchJob->getEntryId(), entryStatus::ERROR_CONVERTING);
		return false;
	}
	
}