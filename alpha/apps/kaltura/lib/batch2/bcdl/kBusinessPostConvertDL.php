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
		/*
		 * For intermediate source generation, both the source and the asset have the same asset id.
		 * In this case sourceMediaInfo should be retrieved as the first version of source asset mediaInfo 
		 */
		if(isset($sourceMediaInfo) && $sourceMediaInfo->getFlavorAssetId()== $flavorAssetId) {
			$productMediaInfo = $sourceMediaInfo;
			
			$entry = $dbBatchJob->getEntry();
			if (!$entry)
			{
				KalturaLog::err("Entry not found [" . $dbBatchJob->getEntryId() . "]");
				throw new APIException(APIErrors::ENTRY_ID_NOT_FOUND, $dbBatchJob->getEntryId());
			}
			$operationAttributes = $entry->getOperationAttributes();

			// if in clipping operation - take the latest created mediainfo object
			$ascending = empty($operationAttributes)? 1 : 0 ;

			$sourceMediaInfo = mediaInfoPeer::retrieveByFlavorAssetId($flavorAssetId, $ascending);

			KalturaLog::log("Intermediate source generation - assetId(".$flavorAssetId."),src MdInf id(".$sourceMediaInfo->getId()."),product MdInf id(".$productMediaInfo->getId()).")";
		}
		else {
			$productMediaInfo = mediaInfoPeer::retrieveByFlavorAssetId($currentFlavorAsset->getId());
		}
		$targetFlavor = assetParamsOutputPeer::retrieveByAssetId($currentFlavorAsset->getId());
		
		//Retrieve convert job executing engien
		$convertEngineType = null;
		if($dbBatchJob->getParentJob()){
			$dbParentBatchJob = $dbBatchJob->getParentJob();
			if($dbParentBatchJob->getJobType() == BatchJobType::CONVERT)
				$convertEngineType =  $dbParentBatchJob->getJobSubType();
		}
		
		$postConvertData = $dbBatchJob->getData();
		$postConvertAssetType = BatchJob::POSTCONVERT_ASSET_TYPE_FLAVOR;
		if($postConvertData instanceof kPostConvertJobData)
			$postConvertAssetType = $postConvertData->getPostConvertAssetType();
		
		// don't validate in case of bypass, in case target flavor or media info are null
		// or ISM/ISMC manifest assets
		if($postConvertAssetType != BatchJob::POSTCONVERT_ASSET_TYPE_BYPASS && $targetFlavor && $productMediaInfo
		&& !$targetFlavor->hasTag(assetParams::TAG_ISM_MANIFEST))
		{
			try{
				$productFlavor = KDLWrap::CDLValidateProduct($sourceMediaInfo, $targetFlavor, $productMediaInfo, $convertEngineType);
			}
			catch(Exception $e){
				KalturaLog::err('KDL Error: ' . print_r($e, true));
			}
			
			$err = kBusinessConvertDL::parseFlavorDescription($productFlavor);

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
			if($dbBatchJob->getParentJob())
				$parentJob = $dbBatchJob->getParentJob();
			else 
				$parentJob = $dbBatchJob;
			kBusinessPreConvertDL::decideFlavorConvert($waitingFlavorAsset, $flavor, $originalFlavorAsset, null, null, $parentJob);
		}
		
		kFlowHelper::generateThumbnailsFromFlavor($dbBatchJob->getEntryId(), $dbBatchJob, $currentFlavorAsset->getFlavorParamsId());
		
		if($currentFlavorAsset->getIsOriginal())
		{
			$entry = $currentFlavorAsset->getentry();
			if($entry)
			{
				kBusinessConvertDL::checkForPendingLiveClips($entry);
			}
		}
		
		return $currentFlavorAsset;
	}
	
	/**
	 * @param BatchJob $dbBatchJob
	 * @param flavorAsset $currentFlavorAsset
	 * @return BatchJob
	 */
	public static function handleConvertFinished(BatchJob $dbBatchJob = null, flavorAsset $currentFlavorAsset)
	{
		$profile = null;
		try{
			$profile = myPartnerUtils::getConversionProfile2ForEntry($currentFlavorAsset->getEntryId());
		}
		catch(Exception $e)
		{
			KalturaLog::err($e->getMessage());
		}
		
		$currentReadyBehavior = self::getReadyBehavior($currentFlavorAsset, $profile);
		KalturaLog::info("Current ready behavior [$currentReadyBehavior]");
		if($currentReadyBehavior == flavorParamsConversionProfile::READY_BEHAVIOR_IGNORE)
			return $dbBatchJob;
		
		$rootBatchJob = null;
		if($dbBatchJob)
			$rootBatchJob = $dbBatchJob->getRootJob();
		if($rootBatchJob)
			KalturaLog::info("root batch job id [" . $rootBatchJob->getId() . "] type [" . $rootBatchJob->getJobType() . "]");
		
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
					KalturaLog::info("job id [" . $siblingJob->getId() . "] status [" . $siblingJob->getStatus() . "]");
					return $dbBatchJob;
				}
			}
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
		KalturaLog::info("required flavor params ids [" . print_r($requiredFlavorParamsIds, true) . "]");
		
		// go over all the flavor assets of the entry
		$entry = $currentFlavorAsset->getentry();
		$inCompleteFlavorIds = array();
		$origianlAssetFlavorId = null;
		$siblingFlavorAssets = assetPeer::retrieveFlavorsByEntryId($currentFlavorAsset->getEntryId());
		foreach($siblingFlavorAssets as $siblingFlavorAsset)
		{
			KalturaLog::info("sibling flavor asset id [" . $siblingFlavorAsset->getId() . "] flavor params id [" . $siblingFlavorAsset->getFlavorParamsId() . "]");
							
			// don't mark any incomplete flag
			if($siblingFlavorAsset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_READY)
			{
				KalturaLog::info("sibling flavor asset id [" . $siblingFlavorAsset->getId() . "] is ready");
				if(isset($requiredFlavorParamsIds[$siblingFlavorAsset->getFlavorParamsId()]))
					unset($requiredFlavorParamsIds[$siblingFlavorAsset->getFlavorParamsId()]);
					
				continue;
			}
			
			if($entry && $entry->getReplacedEntryId() && $siblingFlavorAsset->getStatus() == flavorAsset::ASSET_STATUS_QUEUED)
			{
				KalturaLog::info("sibling flavor asset id [" . $siblingFlavorAsset->getId() . "] is incomplete and in replacement");
				$inCompleteFlavorIds[] = $siblingFlavorAsset->getFlavorParamsId();
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
			        KalturaLog::info("sibling flavor asset id [" . $siblingFlavorAsset->getId() . "] is incomplete");
				    $inCompleteFlavorIds[] = $siblingFlavorAsset->getFlavorParamsId();
			    }
			}		

			if($readyBehavior == flavorParamsConversionProfile::READY_BEHAVIOR_IGNORE)
			{
				KalturaLog::info("sibling flavor asset id [" . $siblingFlavorAsset->getId() . "] is ignored");
				continue;
			}
			
			if(
					$siblingFlavorAsset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_QUEUED 
				||	$siblingFlavorAsset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_CONVERTING 
				||	$siblingFlavorAsset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_IMPORTING 
				||	$siblingFlavorAsset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_VALIDATING
				||  $siblingFlavorAsset->getStatus() == flavorAsset::ASSET_STATUS_WAIT_FOR_CONVERT)
			{
				KalturaLog::info("sibling flavor asset id [" . $siblingFlavorAsset->getId() . "] is incomplete");
				$inCompleteFlavorIds[] = $siblingFlavorAsset->getFlavorParamsId();
			}
						
			if($readyBehavior == flavorParamsConversionProfile::READY_BEHAVIOR_REQUIRED)
			{
				KalturaLog::info("sibling flavor asset id [" . $siblingFlavorAsset->getId() . "] is required");
				$requiredFlavorParamsIds[$siblingFlavorAsset->getFlavorParamsId()] = true;
			}
		}
				
		KalturaLog::info("left required flavor params ids [" . print_r($requiredFlavorParamsIds, true) . "]");				
		KalturaLog::info("left incomplete flavor ids [" . print_r($inCompleteFlavorIds, true) . "]");
		
		if(count($requiredFlavorParamsIds))
		{
			$inCompleteRequiredFlavorParamsIds = array_keys($requiredFlavorParamsIds);
			foreach($inCompleteRequiredFlavorParamsIds as $inCompleteFlavorId)
				$inCompleteFlavorIds[] = $inCompleteFlavorId;
				
			KalturaLog::info('Convert Finished - has In-Complete Required flavors [[' . print_r($inCompleteRequiredFlavorParamsIds, true) . ']');
		} 
		elseif($currentFlavorAsset->getStatus() == asset::ASSET_STATUS_READY && ($currentReadyBehavior == flavorParamsConversionProfile::READY_BEHAVIOR_OPTIONAL || $currentReadyBehavior == flavorParamsConversionProfile::READY_BEHAVIOR_REQUIRED))
		{
			// mark the entry as ready if all required conversions completed or any of the optionals
			if($currentFlavorAsset->getentry()->getReplacedEntryId())
			{
				KalturaLog::info('Entry is temporary replacement and requires all flavors to complete');
			}
			else
			{
				kBatchManager::updateEntry($currentFlavorAsset->getEntryId(), entryStatus::READY);
			}
		}
		
		if ($origianlAssetFlavorId) {
		    $inCompleteFlavorIds = array_diff($inCompleteFlavorIds, array($origianlAssetFlavorId));
		}
		
		if(!count($inCompleteFlavorIds))
		{
			KalturaLog::info('Convert Finished');
			
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
		
		KalturaLog::info('Convert Finished - has In-Complete flavors [' . print_r($inCompleteFlavorIds, true) . ']');
	
		if(!$rootBatchJob || $rootBatchJob->getJobType() != BatchJobType::CONVERT_PROFILE)
			return $dbBatchJob;
			
		$childJobs = $rootBatchJob->getChildJobs();
		KalturaLog::info('Child jobs found [' . count($childJobs) . ']');
		$waitingFlavorAssets = assetPeer::retrieveByEntryIdAndStatus($currentFlavorAsset->getEntryId(), flavorAsset::FLAVOR_ASSET_STATUS_WAIT_FOR_CONVERT);
		KalturaLog::info('Waiting assets found [' . count($waitingFlavorAssets) . ']');
		
		if(count($childJobs) > 1 && count($waitingFlavorAssets) < 1)
		{
			$allDone = true;
			foreach($childJobs as $childJob)
			{
				if($childJob->getId() != $rootBatchJob->getId() && $childJob->getStatus() != BatchJob::BATCHJOB_STATUS_FINISHED)
				{
					KalturaLog::info('Child job id [' . $childJob->getId() . '] status [' . $childJob->getStatus() . ']');
					$allDone = false;
				}
			}
					
			if($allDone)
			{
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
		
			/*
			 * On Webex error, roll back the inter-src asset version in order to allow the retry to get ARF as a source, 
			 * rather than the invlaid WMV file (product of bad nbrplayer session)
			 */
		if($dbBatchJob->getErrNumber()==BatchJobAppErrors::BLACK_OR_SILENT_CONTENT) {
			$prevVer = $flavorAsset->getPreviousVersion();
			$currVer = $flavorAsset->getVersion();
			KalturaLog::log("Webex conversion - Garbled Audio or Black frame or Silence. Rolling back asset/file-sync version - curr($currVer), prev($prevVer)");
			if(isset($prevVer)) {
				$syncKey = $flavorAsset->getSyncKey(asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET, $currVer);
				if(isset($syncKey)){
					kFileSyncUtils::deleteSyncFileForKey($syncKey, false, true);
					$flavorAsset->setVersion($prevVer);
					$flavorAsset->setPreviousVersion(null);
					KalturaLog::log("Webex conversion - Rolled back");
				}
			}
		}

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
			if ($siblingFlavorAsset &&
				$siblingFlavorAsset->getStatus() != flavorAsset::FLAVOR_ASSET_STATUS_ERROR &&
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
