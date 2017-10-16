<?php

/**
 * 
 * Manages the batch mechanism
 *  
 * @package Core
 * @subpackage Batch
 */
class kBatchManager
{
	/**
	 * @var BatchJob
	 */
	protected static $currentUpdatingJob;
	
	/**
	 * @return BatchJob
	 */
	public static function getCurrentUpdatingJob()
	{
		return self::$currentUpdatingJob;
	}
	
	/**
	 * batch createFlavorAsset orgenize a convert job data 
	 * 
	 * @param flavorParamsOutputWrap $flavor
	 * @param int $partnerId
	 * @param int $entryId
	 * @param string $flavorAssetId
	 * @return flavorAsset
	 */
	public static function createFlavorAsset(flavorParamsOutputWrap $flavor, $partnerId, $entryId, $flavorAssetId = null)
	{
		$description = kBusinessConvertDL::parseFlavorDescription($flavor);
		
		$flavorAsset = null;
		if($flavorAssetId)
			$flavorAsset = assetPeer::retrieveById($flavorAssetId);
		
		if(!$flavorAsset)
			$flavorAsset = assetPeer::retrieveByEntryIdAndParams($entryId, $flavor->getFlavorParamsId());
		
		if($flavorAsset)
		{
			$description = $flavorAsset->getDescription() . "\n" . $description;
			$flavorAsset->setDescription($description);
//			$flavorAsset->incrementVersion();
		}	
		else
		{
			// creates the flavor asset 
			$flavorAsset = flavorAsset::getInstance($flavor->getType());
			$flavorAsset->setPartnerId($partnerId);
			$flavorAsset->setEntryId($entryId);
			$flavorAsset->setDescription($description);
		}
		
		$flavorAsset->setTags($flavor->getTags());
		$flavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_QUEUED);
		$flavorAsset->setFlavorParamsId($flavor->getFlavorParamsId());
		$flavorAsset->setFileExt($flavor->getFileExt());
		
		// decided by the business logic layer
		if($flavor->_create_anyway)
		{
			KalturaLog::log("Flavor [" . $flavor->getFlavorParamsId() . "] selected to be created anyway");
		}
		else
		{
			if(!$flavor->IsValid())
			{
				KalturaLog::err("Flavor [" . $flavor->getFlavorParamsId() . "] is invalid");
				$flavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_ERROR);
				$flavorAsset->save();	
				return null;
			}
			
			if($flavor->_force)
			{
				KalturaLog::log("Flavor [" . $flavor->getFlavorParamsId() . "] is forced");
			}
			else
			{
				if($flavor->_isNonComply)
				{
					KalturaLog::err("Flavor [" . $flavor->getFlavorParamsId() . "] is none-comply");
					$flavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_NOT_APPLICABLE);
					$flavorAsset->save();	
					return null;
				}

				$vidCodec=$flavor->getVideoCodec();
				$audCodec=$flavor->getAudioCodec();
				$sourceAssetParamsIds=$flavor->getSourceAssetParamsIds();
				/*
				 * Added check for 'sourceAssetParamsIds' to conditions for setting 
				 * of 'FLAVOR_ASSET_STATUS_NOT_APPLICABLE' - 
				 * - flavors that are dependent on other assets/sources can not be 
				 * redundant (evaluated by 'KDL' from bitrate's), 
				 * they should be activated upon completion of dependee asset 
				 * The usecase - PlayReady audio-only flavors
				 */
				if(($flavor->_isRedundant) && !isset($vidCodec) && isset($audCodec) 
				&& !(isset($sourceAssetParamsIds) && strlen($sourceAssetParamsIds)>0))
				{
					KalturaLog::err("Flavor [" . $flavor->getFlavorParamsId() . "] is redandant audio-only");
					$flavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_NOT_APPLICABLE);
					$flavorAsset->save();
					return null;
				}
				
				KalturaLog::log("Flavor [" . $flavor->getFlavorParamsId() . "] is valid");
			}
		}
		$flavorAsset->save();
		
		// save flavor params
		$flavor->setPartnerId($partnerId);
		$flavor->setEntryId($entryId);
		$flavor->setFlavorAssetId($flavorAsset->getId());
		$flavor->setFlavorAssetVersion($flavorAsset->getVersion());
		$flavor->save();
			
		return $flavorAsset;
	}
	
	/**
	 * batch createFlavorAsset orgenize a convert job data 
	 * 
	 * @param flavorParamsOutputWrap $flavor
	 * @param int $partnerId
	 * @param int $entryId
	 * @param string $description
	 * @return flavorAsset
	 */
	public static function createErrorFlavorAsset(flavorParamsOutputWrap $flavor, $partnerId, $entryId, $description)
	{
		$flavorAsset = assetPeer::retrieveByEntryIdAndParams($entryId, $flavor->getFlavorParamsId());
		
		if($flavorAsset)
		{
			$description = $flavorAsset->getDescription() . "\n" . $description;
			$flavorAsset->setDescription($description);
//			$flavorAsset->incrementVersion();
		}	
		else
		{
			// creates the flavor asset 
			$flavorAsset = flavorAsset::getInstance($flavor->getType());
			$flavorAsset->setPartnerId($partnerId);
			$flavorAsset->setEntryId($entryId);
			$flavorAsset->setDescription($description);
		}
		
		$flavorAsset->setTags($flavor->getTags());
		$flavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_ERROR);
		$flavorAsset->setFlavorParamsId($flavor->getFlavorParamsId());
		$flavorAsset->setFileExt($flavor->getFileExt());
		$flavorAsset->save();
		
		// save flavor params
		$flavor->setPartnerId($partnerId);
		$flavor->setEntryId($entryId);
		$flavor->setFlavorAssetId($flavorAsset->getId());
		$flavor->setFlavorAssetVersion($flavorAsset->getVersion());
		$flavor->save();
			
		return $flavorAsset;
	}
	
	
	/**
	 * batch addMediaInfo adds a media info and updates the flavor asset 
	 * 
	 * @param mediaInfo $mediaInfoDb  
	 * @return mediaInfo 
	 */
	public static function addMediaInfo(mediaInfo $mediaInfoDb)
	{
		$mediaInfoDb->save();
		KalturaLog::log("Added media info [" . $mediaInfoDb->getId() . "] for flavor asset [" . $mediaInfoDb->getFlavorAssetId() . "]");
		
		if(!$mediaInfoDb->getFlavorAssetId())
			return $mediaInfoDb;
			
		$flavorAsset = assetPeer::retrieveById($mediaInfoDb->getFlavorAssetId());
		if(!$flavorAsset)
			return $mediaInfoDb;

		if($flavorAsset->getIsOriginal())
		{
			kBusinessPreConvertDL::checkConditionalProfiles($flavorAsset->getentry(), $mediaInfoDb);
		
			KalturaLog::log("Media info is for the original flavor asset");
			$tags = null;
			
			$profile = myPartnerUtils::getConversionProfile2ForEntry($flavorAsset->getEntryId());
			if($profile)
				$tags = $profile->getInputTagsMap();
			KalturaLog::log("Flavor asset tags from profile [$tags]");
			
			if(!is_null($tags))
			{
				$tagsArray = explode(',', $tags);
				
				// support for old migrated profiles
				if($profile->getCreationMode() == conversionProfile2::CONVERSION_PROFILE_2_CREATION_MODE_AUTOMATIC_BYPASS_FLV)
				{
					if(!KDLWrap::CDLIsFLV($mediaInfoDb))
					{
						$key = array_search(flavorParams::TAG_MBR, $tagsArray);
						if($key !== false)
							unset($tagsArray[$key]);
					}
				}
				
				$finalTagsArray = KDLWrap::CDLMediaInfo2Tags($mediaInfoDb, $tagsArray);
				$finalTags = join(',', array_unique($finalTagsArray));
				KalturaLog::log("Flavor asset tags from KDL [$finalTags]");
//KalturaLog::log("Flavor asset tags [".print_r($flavorAsset->setTags(),1)."]");
				$flavorAsset->addTags($finalTagsArray);
			}
		}
		else 
		{
			KalturaLog::log("Media info is for the destination flavor asset");
			$tags = null;
			
			$flavorParams = assetParamsPeer::retrieveByPK($flavorAsset->getFlavorParamsId());
			if($flavorParams)
				$tags = $flavorParams->getTags();
			KalturaLog::log("Flavor asset tags from flavor params [$tags]");
			
			if(!is_null($tags))
			{
				$tagsArray = explode(',', $tags);
				$assetTagsArray = $flavorAsset->getTagsArray();
				foreach($assetTagsArray as $tag)
					$tagsArray[] = $tag;
				
				$finalTagsArray = $tagsArray;

				$finalTags = join(',', array_unique($finalTagsArray));
				KalturaLog::log("Flavor asset tags from KDL [$finalTags]");
				$flavorAsset->setTags($finalTags);
			}
		}
				
		KalturaLog::log("KDLWrap::ConvertMediainfoCdl2FlavorAsset(" . $mediaInfoDb->getId() . ", " . $flavorAsset->getId() . ");");
		KDLWrap::ConvertMediainfoCdl2FlavorAsset($mediaInfoDb, $flavorAsset);
		/*
		 * If the flavorParams has explicit language settings, 
		 * use the first flavorParams language to set/overwrite the flavorAsset language
		 */
		if(isset($flavorParams) && ($multiStreamJson=$flavorParams->getMultiStream())!=null && ($multiStreamObj=json_decode($multiStreamJson))!=null) {
			if(isset($multiStreamObj->audio->languages) && count($multiStreamObj->audio->languages)>0){
				$lang = $multiStreamObj->audio->languages[0];
			}
			else if(KDLAudioMultiStreaming::IsStreamFieldSet($multiStreamObj, "lang")){
				$lang = $multiStreamObj->audio->streams[0]->lang;
			}
			if(isset($lang)){
				KalturaLog::log("Flavor asset(".$flavorAsset->getId().") language overloaded with flavor Params(".$flavorParams->getId().") language($lang)");
				$flavorAsset->setLanguage($lang);
			}
		}
		$flavorAsset->save();

//		if(!$flavorAsset->hasTag(flavorParams::TAG_MBR))
//			return $mediaInfoDb;
			
		$entry = entryPeer::retrieveByPK($flavorAsset->getEntryId());
		if(!$entry)
			return $mediaInfoDb;
		
		$contentDuration = $mediaInfoDb->getContainerDuration();
		if (!$contentDuration)
		{
			$contentDuration = $mediaInfoDb->getVideoDuration();
			if (!$contentDuration)
				$contentDuration = $mediaInfoDb->getAudioDuration();
		}
		
		if ($contentDuration && $entry->getCalculateDuration())
		{
			$entry->setLengthInMsecs($contentDuration);
		}
		
		if($mediaInfoDb->getVideoWidth() && $mediaInfoDb->getVideoHeight())
		{
        		$entry->setDimensionsIfBigger($mediaInfoDb->getVideoWidth(), $mediaInfoDb->getVideoHeight());
		}
				
		$entry->save();
		return $mediaInfoDb;
	} 
	
	// common to all the jobs using the BatchJob table 
	public static function freeExclusiveBatchJob($id, kExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		return kBatchExclusiveLock::freeExclusive($id, $lockKey, $resetExecutionAttempts);
	}
	
	public static function getQueueSize($workerId, $jobType, $filter)
	{
		$c = new Criteria();
		$filter->attachToCriteria($c);
		return kBatchExclusiveLock::getQueueSize($c, $workerId, $jobType);
	}
	
	public static function cleanExclusiveJobs()
	{
		$jobs = kBatchExclusiveLock::getExpiredJobs();
		foreach($jobs as $job)
		{
			KalturaLog::log("Cleaning job id[" . $job->getId() . "]");
			$job->setMessage("Job was cleaned up.");
			kJobsManager::updateBatchJob($job, BatchJob::BATCHJOB_STATUS_FATAL);
		}
		
		$jobs = kBatchExclusiveLock::getStatusInconsistentJob();
		foreach($jobs as $job) {
			KalturaLog::log("Fixing batch job Inconsistency [" . $job->getId() . "]");
			$job->delete();
			// The job shouldhave been deleted. The reason it got here is since the update
			// process has failed fataly. Therefore there is no point in retrying to save it.
		}
		
		
		return 0;
	}
	
	/**
	 * Common to all the jobs using the BatchJob table
	 * 
	 * @param unknown_type $id
	 * @param kExclusiveLockKey $lockKey
	 * @param BatchJob $dbBatchJob
	 * @return Ambigous <BatchJob, NULL, unknown, multitype:>
	 */
	public static function updateExclusiveBatchJob($id, kExclusiveLockKey $lockKey, BatchJob $dbBatchJob)
	{
		self::$currentUpdatingJob = $dbBatchJob;
		
		$dbBatchJob = kBatchExclusiveLock::updateExclusive($id, $lockKey, $dbBatchJob);
		
		$event = new kBatchJobStatusEvent($dbBatchJob);
		kEventsManager::raiseEvent($event);
		
		$dbBatchJob->reload();
		return $dbBatchJob;
	}
	
	public static function getExclusiveAlmostDoneJobs(kExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, $jobType, BatchJobFilter $filter)
	{
		$c = new Criteria();
		$filter->attachToCriteria($c);
		
		return kBatchExclusiveLock::getExclusiveAlmostDone($c, $lockKey, $maxExecutionTime, $numberOfJobs, $jobType);
	}

public static function updateEntry($entryId, $status)
	{
		$entry = entryPeer::retrieveByPK($entryId);
		if(!$entry) {
			KalturaLog::err("Entry was not found for id [$entryId]");
			return null;
		}
		
		// entry status didn't change - no need to send notification
		if($entry->getStatus() == $status)
			return $entry;
		
		// backward compatibility 
		// if entry has kshow, and this is the first entry in the mix, 
		// the thumbnail of the entry should be copied into the mix entry  
		if ($status == entryStatus::READY && $entry->getKshowId())
			myEntryUtils::createRoughcutThumbnailFromEntry($entry, false);
			
		// entry status is ready and above, not changing status through batch job
		$unAcceptedStatuses = array(
			entryStatus::READY,
			entryStatus::DELETED,
		);
		
		if(in_array($entry->getStatus(), $unAcceptedStatuses))
		{
			KalturaLog::info("Entry status [" . $entry->getStatus() . "] will not be changed");
			return $entry;
		}
		
		$entry->setStatus($status);
		$entry->save();
		
		myNotificationMgr::createNotification(kNotificationJobData::NOTIFICATION_TYPE_ENTRY_UPDATE, $entry, null, null, null, null, $entry->getId());
		
		return $entry;
	}

}
