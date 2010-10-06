<?php

class kBusinessPreConvertDL
{

	/**
	 * batch redecideFlavorConvert is the decision layer for a single flavor conversion 
	 * 
	 * @param string $srcFileSyncLocalPath
	 * @param int $flavorAssetId
	 * @param int $flavorParamsOutputId
	 * @param int $mediaInfoId
	 * @param BatchJob $parentJob
	 * @param BatchJob $remoteConvertJob
	 * @param int $lastEngineType
	 * @return BatchJob 
	 */
	public static function redecideFlavorConvert($flavorAssetId, $flavorParamsOutputId, $mediaInfoId, BatchJob $parentJob, $lastEngineType)
	{
		$originalFlavorAsset = flavorAssetPeer::retrieveOriginalByEntryId($parentJob->getEntryId());
		if (is_null($originalFlavorAsset))
		{
			KalturaLog::log('Original flavor asset not found');
			return null;
		}
		$srcSyncKey = $originalFlavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		
		$flavor = flavorParamsOutputPeer::retrieveByPK($flavorParamsOutputId);
		if (is_null($flavor))
		{
			KalturaLog::log("Flavor params output not found [$flavorParamsOutputId]");
			return null;
		}
		
		return kJobsManager::addFlavorConvertJob($srcSyncKey, $flavor, $flavorAssetId, $mediaInfoId, $parentJob, $lastEngineType);
	}
	
	/**
	 * batch decideFlavorConvert is the decision layer for a single flavor conversion 
	 * 
	 * @param FileSyncKey $srcSyncKey
	 * @param int $flavorParamsId
	 * @param string $errDescription
	 * @param int $mediaInfoId
	 * @param BatchJob $parentJob
	 * @param BatchJob $remoteConvertJob
	 * @param int $lastEngineType
	 * @return BatchJob 
	 */
	public static function decideFlavorConvert(FileSyncKey $srcSyncKey, $flavorParamsId, &$errDescription, $mediaInfoId = null, BatchJob $parentJob = null, $lastEngineType = null)
	{
		$flavorParams = flavorParamsPeer::retrieveByPK($flavorParamsId);
		$mediaInfo = mediaInfoPeer::retrieveByPK($mediaInfoId);
		
		$flavor = self::validateFlavorAndMediaInfo($flavorParams, $mediaInfo, $errDescription);
		if(is_null($flavor))
			return null;
		
		$flavorAsset = kBatchManager::createFlavorAsset($flavor, $parentJob->getPartnerId(), $parentJob->getEntryId());
		if(is_null($flavorAsset))
			return null;
		
		return kJobsManager::addFlavorConvertJob($srcSyncKey, $flavor, $flavorAsset->getId(), $mediaInfo->getId(), $parentJob, $lastEngineType);
	}
	
	/**
	 * batch decideAddEntryFlavor is the decision layer for adding a single flavor conversion to an entry 
	 *
	 * @param BatchJob $parentJob
	 * @param int $entryId 
	 * @param int $flavorParamsId
	 * @param string $errDescription
	 * @param string $flavorAssetId
	 * @return BatchJob 
	 */
	public static function decideAddEntryFlavor(BatchJob $parentJob = null, $entryId, $flavorParamsId, &$errDescription)
	{
		//KalturaLog::debug(__METHOD__." - (parentJob === null) [".($parentJob === null)."] entryId [$entryId], flavorParamsId [$flavorParamsId]");
		KalturaLog::log(__METHOD__." - (parentJob === null) [".($parentJob === null)."] entryId [$entryId], flavorParamsId [$flavorParamsId]");
		
		$originalFlavorAsset = flavorAssetPeer::retrieveOriginalByEntryId($entryId);
		if (is_null($originalFlavorAsset))
		{
			$errDescription = 'Original flavor asset not found';
			KalturaLog::log(__METHOD__." - ".$errDescription);
			return null;
		}
		
		$mediaInfoId = null;
		$mediaInfo = mediaInfoPeer::retrieveByFlavorAssetId($originalFlavorAsset->getId());
		if($mediaInfo)
			$mediaInfoId = $mediaInfo->getId();
		
		$flavorParams = flavorParamsPeer::retrieveByPK($flavorParamsId);
		$flavor = self::validateFlavorAndMediaInfo($flavorParams, $mediaInfo, $errDescription);
		
		if (is_null($flavor))
		{
			KalturaLog::log(__METHOD__." - Failed to validate media info [$errDescription]");
			return null;
		}
			
		if ($parentJob) // prefer the partner id from the parent job, although it should be the same
			$partnerId = $parentJob->getPartnerId();
		else
			$partnerId = $originalFlavorAsset->getPartnerId();
			
		
		$flavorAssetId = null;
		$flavorAsset = flavorAssetPeer::retrieveByEntryIdAndFlavorParams($entryId, $flavorParamsId);
		if($flavorAsset)
			$flavorAssetId = $flavorAsset->getId();
		
		$srcSyncKey = $originalFlavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$flavor->_force = true; // force to convert the flavor, even if none complied
		$flavor->setReadyBehavior(flavorParamsConversionProfile::READY_BEHAVIOR_IGNORE); // should not be taken in completion rules check
		
		$flavorAsset = kBatchManager::createFlavorAsset($flavor, $partnerId, $entryId, $flavorAssetId);
		if (!$flavorAsset)
		{
			KalturaLog::err(__METHOD__." - Failed to create flavor asset");
			return null;
		}
		$flavorAssetId = $flavorAsset->getId();
	
		$collectionTag = $flavor->getCollectionTag();
		if($collectionTag)
		{
			$entry = entryPeer::retrieveByPK($entryId);
			if(!$entry)
				throw new APIException(APIErrors::INVALID_ENTRY, $parentJob, $entryId);
		
			$dbConvertCollectionJob = null;
			if ($parentJob)
			{
				$dbConvertCollectionJob = $parentJob->createChild(false);
				$dbConvertCollectionJob->setEntryId($entryId);
				$dbConvertCollectionJob->save();
			}
			
			$flavorAssets = flavorAssetPeer::retrieveByEntryId($entryId);
			$flavorAssets = flavorAssetPeer::filterByTag($flavorAssets, $collectionTag);
			$flavors = array();
			foreach($flavorAssets as $tagedFlavorAsset)
			{
				if($tagedFlavorAsset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_NOT_APPLICABLE || $tagedFlavorAsset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_DELETED)
					continue;

				$flavorParamsOutput = flavorParamsOutputPeer::retrieveByFlavorAssetId($tagedFlavorAsset->getId());
				if(is_null($flavorParamsOutput))
				{
					KalturaLog::log("Creating flavor params output for asset [" . $tagedFlavorAsset->getId() . "]");
				
					$flavorParams = flavorParamsPeer::retrieveByPK($tagedFlavorAsset->getId());
					$flavorParamsOutput = self::validateFlavorAndMediaInfo($flavorParams, $mediaInfo, $errDescription);
					
					if (is_null($flavorParamsOutput))
					{
						KalturaLog::log(__METHOD__." - Failed to validate media info [$errDescription]");
						continue;
					}
				}
				
				if($flavorParamsOutput)
				{
					KalturaLog::log("Adding Collection flavor [" . $flavorParamsOutput->getId() . "] for asset [" . $tagedFlavorAsset->getId() . "]");
					$flavors[$tagedFlavorAsset->getId()] = flavorParamsOutputPeer::retrieveByFlavorAssetId($tagedFlavorAsset->getId());
				}
			}
			if($flavorAssetId)
			{
				KalturaLog::log("Updating Collection flavor [" . $flavor->getId() . "] for asset [" . $tagedFlavorAsset->getId() . "]");
				$flavors[$flavorAssetId] = $flavor;
			}
		
			switch($collectionTag)
			{
				case flavorParams::TAG_ISM:
					KalturaLog::log("Calling addConvertIsmCollectionJob with [" . count($flavors) . "] flavor params");
					return kJobsManager::addConvertIsmCollectionJob($collectionTag, $srcSyncKey, $entry, $parentJob, $flavors, $dbConvertCollectionJob);
					
				default:
					KalturaLog::log("Error: Invalid collection tag [$collectionTag]");
					return null;
			}
		}
		
		$dbConvertFlavorJob = null;
		if ($parentJob)
		{
			$dbConvertFlavorJob = $parentJob->createChild(false);
			$dbConvertFlavorJob->setEntryId($entryId);
			$dbConvertFlavorJob->save();
		}
		
		return kJobsManager::addFlavorConvertJob($srcSyncKey, $flavor, $flavorAsset->getId(), $mediaInfoId, $parentJob, null, $dbConvertFlavorJob);
	}
	
	/**
	 * batch validateConversionProfile validates profile completion rules 
	 * 
	 * @param mediaInfo $mediaInfo
	 * @param array $flavors is array of flavorParams
	 * @param string $errDescription
	 * @return array of flavorParamsOutput
	 */
	protected static function validateConversionProfile(mediaInfo $mediaInfo = null, array $flavors, array $conversionProfileFlavorParams, &$errDescription)
	{
		// if there is no media info, the entire profile returned as is, decision layer ignored
		if(!$mediaInfo)
		{
			KalturaLog::log("Validate Conversion Profile, no media info supplied");
//			$ret = array();
//			foreach($flavors as $flavor)
//			{
//				$outFlavor = new flavorParamsOutputWrap();
//				$ret[] = flavorParamsOutputPeer::doCopy($flavor, $outFlavor);
//			}
//			return $ret; 
		}
		else
		{
			KalturaLog::log("Validate Conversion Profile, media info [" . $mediaInfo->getId() . "]");
		}
		
		// call the decision layer
		KalturaLog::log("Generate Target " . count($flavors) . " Flavors supplied");
		$cdl = KDLWrap::CDLGenerateTargetFlavors($mediaInfo, $flavors);
		KalturaLog::log("Generate Target " . count($cdl->_targetList) . " Flavors returned");
		
		// check for errors
		$errDescription = '';
		if(count($cdl->_errors))
		{
			$errDesc = '';
			foreach($cdl->_errors as $section => $errors)
				$errDesc .= "$section errors: " . join(";", $errors) . "\n";
				
			KalturaLog::log("Decision layer input errors: $errDesc");
			$errDescription .= "\nMedia err: $errDesc";
		}
		
		// check for warnings
		if(count($cdl->_warnings))
		{
			$errDesc = '';
			foreach($cdl->_warnings as $section => $errors)
				$errDesc .= "$section warnings: " . join(";", $errors) . "\n";
				
			KalturaLog::log("Decision layer input warnings: $errDesc");
			$errDescription .= "\nMedia warn: $errDesc";
		}
			
		// rv - returned value from the decision layer
		if(!$cdl->_rv)
		{
			KalturaLog::log("Decision layer returned false");
			return null;
		}
	
		// orgenizing the flavors by the tags
		$tagedFlavors = array();
		foreach($cdl->_targetList as $flavor)
		{
			// overwrite ready behavior from the conversion profile
			$flavorParamsConversionProfile = $conversionProfileFlavorParams[$flavor->getFlavorParamsId()];
			$flavor->_force = $flavorParamsConversionProfile->getForceNoneComplied();
			
			if($flavorParamsConversionProfile->getReadyBehavior() != flavorParamsConversionProfile::READY_BEHAVIOR_INHERIT_FLAVOR_PARAMS)
				$flavor->setReadyBehavior($flavorParamsConversionProfile->getReadyBehavior());	

			if(!$flavor->IsValid())
			{
				KalturaLog::log("Flavor [" . $flavor->getFlavorParamsId() . "] is invalid");
				
				// if required - failing the profile
				if($flavor->getReadyBehavior() == flavorParamsConversionProfile::READY_BEHAVIOR_REQUIRED)
				{
					$errDescription = "Business decision layer, required flavor not valid: " . $flavor->getId();
					$errDescription .= kBusinessConvertDL::parseFlavorDescription($flavor);
					KalturaLog::log($errDescription);
					return null;
				}
			}
			
			// if required - failing the profile
			if($flavor->_isNonComply)
			{
				KalturaLog::log("Flavor [" . $flavor->getFlavorParamsId() . "] is none complied");
				
				if($flavor->getReadyBehavior() == flavorParamsConversionProfile::READY_BEHAVIOR_REQUIRED)
				{
					$errDescription = "Business decision layer, required flavor none complied: id[" . $flavor->getId() . "] flavor params id [" . $flavor->getFlavorParamsId() . "]";
					$errDescription .= kBusinessConvertDL::parseFlavorDescription($flavor);
					KalturaLog::log($errDescription);
					return null;
				}
			}
			
			foreach($flavor->getTagsArray() as $tag)
			{
				KalturaLog::log("Taged [$tag] flavor added [" . $flavor->getFlavorParamsId() . "]");
				$tagedFlavors[$tag][$flavor->getFlavorParamsId()] = $flavor;
			}
		}
		
		// filter out all not forced, none complied, and invalid flavors
		$finalTagedFlavors = array();
		foreach($tagedFlavors as $tag => $tagedFlavorsArray)
		{
			KalturaLog::log("Filtering flavors by tag [$tag]");
			$finalTagedFlavors[$tag] = kBusinessConvertDL::filterTagFlavors($tagedFlavorsArray);
		}
			
		$finalFlavors = array();
		foreach($finalTagedFlavors as $tag => $tagedFlavorsArray)
		{
			foreach($tagedFlavorsArray as $flavorParamsId => $tagedFlavor)
				$finalFlavors[$flavorParamsId] = $tagedFlavor;
		}
		
		// sort the flavors to decide which one will be performed first
		usort($finalFlavors, array('kBusinessConvertDL', 'compareFlavors'));
		KalturaLog::log(count($finalFlavors) . " flavors sorted for execution");
	
		return $finalFlavors;
	}
	
	/**
	 * batch validateFlavorAndMediaInfo validate and manipulate a flavor according to the given media info
	 * 
	 * @param flavorParams $flavor
	 * @param mediaInfo $mediaInfo
	 * @param string $errDescription
	 * @return flavorParamsOutputWrap or null for fail
	 */
	protected static function validateFlavorAndMediaInfo(flavorParams $flavor, mediaInfo $mediaInfo = null, &$errDescription)
	{
		$cdl = KDLWrap::CDLGenerateTargetFlavors($mediaInfo, array($flavor));
		
		$errDescription = '';
		if(count($cdl->_errors))
		{
			$errDesc = '';
			foreach($cdl->_errors as $section => $errors)
				$errDesc .= "$section errors: " . join(";", $errors) . "\n";
				
			KalturaLog::log("Decision layer input error: $errDesc");
			$errDescription .= "\nMedia err: $errDesc";
		}
		
		if(count($cdl->_warnings))
		{
			$errDesc = '';
			foreach($cdl->_warnings as $section => $errors)
				$errDesc .= "$section warnings: " . join(";", $errors) . "\n";
				
			KalturaLog::log("Decision layer input warning: $errDesc");
			$errDescription .= "\nMedia warn: $errDesc";
		}
			
		if(!$cdl->_rv)
			return null;

		return reset($cdl->_targetList);
	}
	
	/**
	 * @param flavorAsset $originalFlavorAsset
	 * @param entry $entry
	 * @param BatchJob $convertProfileJob
	 * @return BatchJob
	 */
	public static function bypassConversion(flavorAsset $originalFlavorAsset, entry $entry, BatchJob $convertProfileJob)
	{
		if(!$originalFlavorAsset->hasTag(flavorParams::TAG_MBR))
		{
			$mediaInfo = mediaInfoPeer::retrieveByFlavorAssetId($originalFlavorAsset->getId());
			if($mediaInfo)
			{
				$tagsArray = $originalFlavorAsset->getTagsArray();
				$finalTagsArray = KDLWrap::CDLMediaInfo2Tags($mediaInfo, $tagsArray);
				$originalFlavorAsset->setTagsArray($finalTagsArray);
			}
		}
		
		$offset = $entry->getThumbOffset(); // entry getThumbOffset now takes the partner DefThumbOffset into consideration
		
		$srcSyncKey = $originalFlavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$srcFileSyncLocalPath = kFileSyncUtils::getLocalFilePathForKey($srcSyncKey);
		
		$jobSubType = BatchJob::BATCHJOB_SUB_TYPE_POSTCONVERT_BYPASS;
		return kJobsManager::addPostConvertJob($convertProfileJob, $jobSubType, $srcFileSyncLocalPath, $originalFlavorAsset->getId(), null, true, $offset);
	}
	
	/**
	 * batch decideProfileConvert is the decision layer for a conversion profile
	 * 
	 * @param BatchJob $parentJob
	 * @param BatchJob $convertProfileJob
	 * @param int $mediaInfoId  
	 * @return bool true if created all required conversions
	 */
	public static function decideProfileConvert(BatchJob $parentJob, BatchJob $convertProfileJob, $mediaInfoId = null)
	{
		KalturaLog::log("Conversion decision layer used for entry [" . $parentJob->getEntryId() . "]");
		$convertProfileData = $convertProfileJob->getData();
		
		$entryId = $convertProfileJob->getEntryId();
		$entry = $convertProfileJob->getEntry();
		if(!$entry)
			throw new APIException(APIErrors::INVALID_ENTRY, $convertProfileJob, $entryId);
			
		$profile = myPartnerUtils::getConversionProfile2ForEntry($entryId);
		if(! $profile)
		{
			$errDescription = "Conversion profile for entryId [$entryId] not found";
			$convertProfileJob = kJobsManager::failBatchJob($convertProfileJob, $errDescription, BatchJob::BATCHJOB_TYPE_CONVERT_PROFILE);
			kBatchManager::updateEntry($convertProfileJob, entry::ENTRY_STATUS_ERROR_CONVERTING);
			KalturaLog::err("No flavors created: $errDescription");
			return false;
		}
	
		$originalFlavorAsset = flavorAssetPeer::retrieveOriginalByEntryId($entryId);
		if (is_null($originalFlavorAsset))
		{
			$errDescription = 'Original flavor asset not found';
			KalturaLog::err($errDescription);
			$convertProfileJob = kJobsManager::failBatchJob($convertProfileJob, $errDescription, BatchJob::BATCHJOB_TYPE_CONVERT_PROFILE);
			kBatchManager::updateEntry($convertProfileJob, entry::ENTRY_STATUS_ERROR_CONVERTING);
			return false;
		}
		
		$shouldConvert = true;
		
		// gets the list of flavor params of the conversion profile
		$list = flavorParamsConversionProfilePeer::retrieveByConversionProfile($profile->getId());
		if(! count($list))
		{
			$errDescription = "No flavors match the profile id [{$profile->getId()}]";
			KalturaLog::err($errDescription);
			$convertProfileJob = kJobsManager::failBatchJob($convertProfileJob, $errDescription, BatchJob::BATCHJOB_TYPE_CONVERT_PROFILE);
			kBatchManager::updateEntry($convertProfileJob, entry::ENTRY_STATUS_ERROR_CONVERTING);
			
			$originalFlavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_DELETED);
			$originalFlavorAsset->save();
			
			return false;
		}
			
		$mediaInfo = null;
		if($mediaInfoId)
			$mediaInfo = mediaInfoPeer::retrieveByPK($mediaInfoId);
		
		if($profile->getCreationMode() == ConversionProfile2::CONVERSION_PROFILE_2_CREATION_MODE_AUTOMATIC_BYPASS_FLV)
		{
			KalturaLog::log("The profile created from old conversion profile with bypass flv");
			$isFlv = false;
			if($mediaInfo)
				$isFlv = KDLWrap::CDLIsFLV($mediaInfo);
			
			if($isFlv && $originalFlavorAsset->hasTag(flavorParams::TAG_MBR))
			{
				KalturaLog::log("The source is mbr and flv, conversion will be bypassed");
				$shouldConvert = false;
			}
			else
			{
				KalturaLog::log("The source is NOT mbr or flv, conversion will NOT be bypassed");
			}
		}
		
		// gets the ids of the flavor params 
		$flavorsIds = array(); 
		$conversionProfileFlavorParams = array();
		foreach($list as $flavorParamsConversionProfile)
		{
			$flavorsId = $flavorParamsConversionProfile->getFlavorParamsId();
			$flavorsIds[] = $flavorsId;
			$conversionProfileFlavorParams[$flavorsId] = $flavorParamsConversionProfile;
		}
			
		$dynamicFlavorAttributes = $entry->getDynamicFlavorAttributes();
		
		$sourceFlavor = null;
		// gets the flavor params by the id
		$flavors = flavorParamsPeer::retrieveByPKs($flavorsIds);
		foreach($flavors as $index => $flavor)
		{
			if(isset($dynamicFlavorAttributes[$flavor->getId()]))
			{
				foreach($dynamicFlavorAttributes[$flavor->getId()] as $attributeName => $attributeValue)
					$flavor->setDynamicAttribute($attributeName, $attributeValue);
			}
			
			if($flavor->hasTag(flavorParams::TAG_SOURCE))
			{
				$sourceFlavor = $flavor;
				unset($flavors[$index]);
			}
		}
		
		KalturaLog::log(count($flavors) . " destination flavors found for this profile[" . $profile->getId() . "]");
		
		if(!$sourceFlavor)
		{
			KalturaLog::log("Source flavor params not found");
			$originalFlavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_DELETED);
			$originalFlavorAsset->save();
		}
		elseif($shouldConvert)
		{
			KalturaLog::log("Source flavor params [" . $sourceFlavor->getId() . "] found");
			$originalFlavorAsset->setFlavorParamsId($sourceFlavor->getId());
			
			if($sourceFlavor->getOperators() || $sourceFlavor->getConversionEngines())
			{
				KalturaLog::log("Source flavor asset requires conversion");
				
				$srcSyncKey = $originalFlavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
				$errDescription = null;
				$sourceFlavorOutput = self::validateFlavorAndMediaInfo($sourceFlavor, $mediaInfo, $errDescription);
				
				if($errDescription)
					$originalFlavorAsset->setDescription($originalFlavorAsset->getDescription() . "\n$errDescription");
					
				$originalFlavorAsset->incrementVersion();
				$originalFlavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_QUEUED);	
				$originalFlavorAsset->save();
				
				$sourceFlavorOutput->setFlavorAssetId($originalFlavorAsset->getId());
				$sourceFlavorOutput->setFlavorAssetVersion($originalFlavorAsset->getVersion());
				$sourceFlavorOutput->save();
				
				kJobsManager::addFlavorConvertJob($srcSyncKey, $sourceFlavorOutput, $originalFlavorAsset->getId(), $mediaInfoId, $parentJob);
				return false;
			}
			
			$originalFlavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_READY);
			$originalFlavorAsset->save();
			
			$entry->addFlavorParamsId($sourceFlavor->getId());
			$entry->save();
		}
		
		if(!count($flavors))
			$shouldConvert = false;
	
		if(!$shouldConvert)
		{		
			self::bypassConversion($originalFlavorAsset, $entry, $convertProfileJob);
			return true;
		}
				
		return self::decideProfileFlavorsConvert($parentJob, $convertProfileJob, $flavors, $conversionProfileFlavorParams, $mediaInfo);
	}
		
	public static function continueProfileConvert(BatchJob $parentJob)
	{
		$convertProfileJob = $parentJob->getRootJob();
		if($convertProfileJob->getJobType() != BatchJob::BATCHJOB_TYPE_CONVERT_PROFILE)
			return false;
		
		KalturaLog::log("Conversion decision layer continued for entry [" . $parentJob->getEntryId() . "]");
		$convertProfileData = $convertProfileJob->getData();
		
		$entryId = $convertProfileJob->getEntryId();
		$entry = $convertProfileJob->getEntry();
		if(!$entry)
			throw new APIException(APIErrors::INVALID_ENTRY, $convertProfileJob, $entryId);
			
		$profile = myPartnerUtils::getConversionProfile2ForEntry($entryId);
		if(! $profile)
		{
			$errDescription = "Conversion profile for entryId [$entryId] not found";
			$convertProfileJob = kJobsManager::failBatchJob($convertProfileJob, $errDescription, BatchJob::BATCHJOB_TYPE_CONVERT_PROFILE);
			kBatchManager::updateEntry($convertProfileJob, entry::ENTRY_STATUS_ERROR_CONVERTING);
			KalturaLog::err("No flavors created: $errDescription");
			return false;
		}
	
		$originalFlavorAsset = flavorAssetPeer::retrieveOriginalByEntryId($entryId);
		if (is_null($originalFlavorAsset))
		{
			$errDescription = 'Original flavor asset not found';
			KalturaLog::err($errDescription);
			$convertProfileJob = kJobsManager::failBatchJob($convertProfileJob, $errDescription, BatchJob::BATCHJOB_TYPE_CONVERT_PROFILE);
			kBatchManager::updateEntry($convertProfileJob, entry::ENTRY_STATUS_ERROR_CONVERTING);
			return false;
		}
		
		// gets the list of flavor params of the conversion profile
		$list = flavorParamsConversionProfilePeer::retrieveByConversionProfile($profile->getId());
		if(! count($list))
		{
			$errDescription = "No flavors match the profile id [{$profile->getId()}]";
			KalturaLog::err($errDescription);
			$convertProfileJob = kJobsManager::failBatchJob($convertProfileJob, $errDescription, BatchJob::BATCHJOB_TYPE_CONVERT_PROFILE);
			kBatchManager::updateEntry($convertProfileJob, entry::ENTRY_STATUS_ERROR_CONVERTING);
			
			$originalFlavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_DELETED);
			$originalFlavorAsset->save();
			
			return false;
		}
			
		// gets the ids of the flavor params 
		$flavorsIds = array(); 
		$conversionProfileFlavorParams = array();
		foreach($list as $flavorParamsConversionProfile)
		{
			$flavorsId = $flavorParamsConversionProfile->getFlavorParamsId();
			$flavorsIds[] = $flavorsId;
			$conversionProfileFlavorParams[$flavorsId] = $flavorParamsConversionProfile;
		}
			
		$dynamicFlavorAttributes = $entry->getDynamicFlavorAttributes();
		
		// gets the flavor params by the id
		$flavors = flavorParamsPeer::retrieveByPKs($flavorsIds);
		foreach($flavors as $index => $flavor)
		{
			if($flavor->hasTag(flavorParams::TAG_SOURCE))
			{
				unset($flavors[$index]);
				continue;
			}
			
			if(isset($dynamicFlavorAttributes[$flavor->getId()]))
			{
				foreach($dynamicFlavorAttributes[$flavor->getId()] as $attributeName => $attributeValue)
					$flavor->setDynamicAttribute($attributeName, $attributeValue);
			}
		}
		
		KalturaLog::log(count($flavors) . " destination flavors found for this profile[" . $profile->getId() . "]");
		
		if(!count($flavors))
			return false;
	
		$mediaInfo = mediaInfoPeer::retrieveByFlavorAssetId($originalFlavorAsset->getId());
		
		return self::decideProfileFlavorsConvert($parentJob, $convertProfileJob, $flavors, $conversionProfileFlavorParams, $mediaInfo);
	}
		
	public static function decideProfileFlavorsConvert(BatchJob $parentJob, BatchJob $convertProfileJob, array $flavors, array $conversionProfileFlavorParams, mediaInfo $mediaInfo = null)
	{
		$entryId = $convertProfileJob->getEntryId();
		
		$originalFlavorAsset = flavorAssetPeer::retrieveOriginalByEntryId($entryId);
		if (is_null($originalFlavorAsset))
		{
			$errDescription = 'Original flavor asset not found';
			KalturaLog::err($errDescription);
			$convertProfileJob = kJobsManager::failBatchJob($convertProfileJob, $errDescription, BatchJob::BATCHJOB_TYPE_CONVERT_PROFILE);
			kBatchManager::updateEntry($convertProfileJob, entry::ENTRY_STATUS_ERROR_CONVERTING);
			return false;
		}
		
		$errDescription = null;
		$finalFlavors = self::validateConversionProfile($mediaInfo, $flavors, $conversionProfileFlavorParams, $errDescription);
			
		KalturaLog::log(count($finalFlavors) . " flavors returned from the decision layer");
		if(is_null($finalFlavors))
		{
			$convertProfileJob = kJobsManager::failBatchJob($convertProfileJob, $errDescription);
			KalturaLog::log("No flavors created");
			return false;
		}
		elseif(strlen($errDescription))
		{
			$err = $convertProfileJob->getDescription() . $errDescription;
			$convertProfileJob->setDescription($err);
			$convertProfileJob->save();
		}
			
		$srcSyncKey = $originalFlavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		
		$conversionsCreated = 0;
		
		$entry = $convertProfileJob->getEntry();
		if(!$entry)
			throw new APIException(APIErrors::INVALID_ENTRY, $convertProfileJob, $entryId);
			
		$flavorsCollections = array();
		// create a convert job per each flavor
		foreach($finalFlavors as $flavor)
		{
			$flavorAsset = kBatchManager::createFlavorAsset($flavor, $entry->getPartnerId(), $entry->getId());
			if(!$flavorAsset)
			{
				KalturaLog::log("Flavor asset could not be created, flavor conversion won't be created");
				continue;
			}
			
			$collectionTag = $flavor->getCollectionTag();
			if($collectionTag)
			{
				$flavorsCollections[$collectionTag][] = $flavor;
				continue;
			}
				
			KalturaLog::log("Adding flavor conversion with flavor params output id [" . $flavor->getId() . "] and flavor params asset id [" . $flavorAsset->getId() . "]");
			$madiaInfoId = $mediaInfo ? $mediaInfo->getId() : null;
			$createdJob = kJobsManager::addFlavorConvertJob($srcSyncKey, $flavor, $flavorAsset->getId(), $madiaInfoId, $parentJob);
			
			if($createdJob)
				$conversionsCreated++;
		}
		
		foreach($flavorsCollections as $tag => $flavors)
		{
			switch($tag)
			{
				case flavorParams::TAG_ISM:
					$createdJob = kJobsManager::addConvertIsmCollectionJob($tag, $srcSyncKey, $entry, $parentJob, $flavors);
					if($createdJob)
						$conversionsCreated++;
					break;
					
				default:
					KalturaLog::log("Error: Invalid collection tag [$tag]");
					break;
			}
		}
			
		if(!$conversionsCreated)
		{
			$convertProfileJob = kJobsManager::failBatchJob($convertProfileJob, $errDescription);
			KalturaLog::log("No flavors created: $errDescription");
			return false;
		}
		
		return true;
	}
}