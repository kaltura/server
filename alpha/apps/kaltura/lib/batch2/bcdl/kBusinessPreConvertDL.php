<?php

class kBusinessPreConvertDL
{

	const SAVE_ORIGINAL_SOURCE_FLAVOR_PARAM_SYS_NAME = 'save_original_source_flavor_params';
	
	/**
	 * batch redecideFlavorConvert is the decision layer for a single flavor conversion
	 *
	 * @param string $srcFileSyncLocalPath
	 * @param int $flavorAssetId
	 * @param int $flavorParamsOutputId
	 * @param int $mediaInfoId
	 * @param BatchJob $parentJob
	 * @param int $lastEngineType
	 * @return BatchJob
	 */
	public static function redecideFlavorConvert($flavorAssetId, $flavorParamsOutputId, $mediaInfoId, BatchJob $parentJob, $lastEngineType)
	{
		$originalFlavorAsset = assetPeer::retrieveOriginalByEntryId($parentJob->getEntryId());
		if (is_null($originalFlavorAsset))
		{
			KalturaLog::log('Original flavor asset not found');
			return null;
		}
		
		$flavor = assetParamsOutputPeer::retrieveByPK($flavorParamsOutputId);
		if (is_null($flavor))
		{
			KalturaLog::log("Flavor params output not found [$flavorParamsOutputId]");
			return null;
		}
		
		$flavorAsset = assetPeer::retrieveById($flavorAssetId);
		if (is_null($flavorAsset))
		{
			KalturaLog::log("Flavor asset not found [$flavorAssetId]");
			return null;
		}
		return self::decideFlavorConvert($flavorAsset, $flavor, $originalFlavorAsset, null, $mediaInfoId, $parentJob, $lastEngineType);
	}
	
	/**
	 *
	 * Decide from which asset grab the thumbnail.
	 * @param string $sourceAssetId
	 * @param string $sourceParamsId
	 * @param string $entryId
	 * @return flavorAsset
	 */
	public static function getSourceAssetForGenerateThumbnail($sourceAssetId ,$sourceParamsId, $entryId)
	{
		if($sourceAssetId)
		{
			$srcAsset = assetPeer::retrieveById($sourceAssetId);
			if($srcAsset && $srcAsset->isLocalReadyStatus())
				return $srcAsset;
		}
		
		if($sourceParamsId)
		{
			KalturaLog::debug("Look for flavor params [$sourceParamsId]");
			$srcAsset = assetPeer::retrieveByEntryIdAndParams($entryId, $sourceParamsId);
			if($srcAsset && $srcAsset->isLocalReadyStatus())
				return $srcAsset;
		}
					
		KalturaLog::debug("Look for original flavor of entry [$entryId]");
		$srcAsset = assetPeer::retrieveOriginalByEntryId($entryId);
		if($srcAsset && $srcAsset->isLocalReadyStatus())
			return $srcAsset;
					
			
		KalturaLog::debug("Look for highest bitrate flavor of entry [$entryId]");
		$srcAsset = assetPeer::retrieveHighestBitrateByEntryId($entryId);
		if($srcAsset && $srcAsset->isLocalReadyStatus())
			return $srcAsset;
			
		return null;
	}
	
	
	/**
	 * decideThumbGenerate is the decision layer for a single thumbnail generation
	 *
	 * @param entry $entry
	 * @param thumbParams $destThumbParams
	 * @param BatchJob $parentJob
	 * @return thumbAsset
	 */
	public static function decideThumbGenerate(entry $entry, thumbParams $destThumbParams, BatchJob $parentJob = null, $sourceAssetId = null, $runSync = false , $srcAsset = null)
	{
		if (is_null($srcAsset)){
			$srcAsset = self::getSourceAssetForGenerateThumbnail($sourceAssetId, $destThumbParams->getSourceParamsId(), $entry->getId());
			if (is_null($srcAsset))
				throw new APIException(APIErrors::FLAVOR_ASSET_IS_NOT_READY);
		}
			
		$errDescription = null;
		$mediaInfo = mediaInfoPeer::retrieveByFlavorAssetId($srcAsset->getId());
		$destThumbParamsOutput = self::validateThumbAndMediaInfo($destThumbParams, $mediaInfo, $errDescription);
		
		if($srcAsset->getType() == assetType::FLAVOR && is_null($destThumbParamsOutput->getVideoOffset()))
		{
			$destThumbParamsOutput->setVideoOffset($entry->getThumbOffset());
		}
		$destThumbParamsOutput->setVideoOffset(min($destThumbParamsOutput->getVideoOffset(), $entry->getDuration()));
		
		if (!$destThumbParamsOutput->getDensity())
		{
			$partner = $entry->getPartner();
			if (!is_null($partner))
				$destThumbParamsOutput->setDensity($partner->getDefThumbDensity());
		}
		
		$thumbAsset = assetPeer::retrieveByEntryIdAndParams($entry->getId(), $destThumbParams->getId());
		if($thumbAsset)
		{
			$description = $thumbAsset->getDescription() . "\n" . $errDescription;
			$thumbAsset->setDescription($description);
		}
		else
		{
			$thumbAsset = new thumbAsset();
			$thumbAsset->setPartnerId($entry->getPartnerId());
			$thumbAsset->setEntryId($entry->getId());
			$thumbAsset->setDescription($errDescription);
			$thumbAsset->setFlavorParamsId($destThumbParams->getId());
		}
	
		$thumbAsset->incrementVersion();
		$thumbAsset->setTags($destThumbParamsOutput->getTags());
		$thumbAsset->setFileExt($destThumbParamsOutput->getFileExt());
		
		if($thumbAsset->getStatus() != asset::ASSET_STATUS_READY)
			$thumbAsset->setStatus(asset::ASSET_STATUS_CONVERTING);
		
		//Sets the default thumb if this the only default thumb
		kBusinessPreConvertDL::setIsDefaultThumb($thumbAsset);
		
		if(!$destThumbParamsOutput)
		{
			$thumbAsset->setStatus(thumbAsset::FLAVOR_ASSET_STATUS_ERROR);
			$thumbAsset->save();
			return null;
		}

		$thumbAsset->save();
			
		// save flavor params
		$destThumbParamsOutput->setPartnerId($entry->getPartnerId());
		$destThumbParamsOutput->setEntryId($entry->getId());
		$destThumbParamsOutput->setFlavorAssetId($thumbAsset->getId());
		$destThumbParamsOutput->setFlavorAssetVersion($thumbAsset->getVersion());
		
		$destThumbParamsOutput->save();
		
		$srcSyncKey = $srcAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$srcAssetType = $srcAsset->getType();
		
		if(!$runSync)
		{
			$job = kJobsManager::addCapturaThumbJob($parentJob, $entry->getPartnerId(), $entry->getId(), $thumbAsset->getId(), $srcSyncKey, $srcAsset->getId(), $srcAssetType, $destThumbParamsOutput);
			return $thumbAsset;
		}

		$errDescription = null;
		// Since this method is called when trying to crop an existing thumbnail, need to add this check - thumbAssets have no mediaInfo.
		$capturedPath = self::generateThumbnail($srcAsset, $destThumbParamsOutput, $errDescription, $mediaInfo? $mediaInfo->getVideoRotation() : null);
		
		// failed
		if(!$capturedPath)
		{
			$thumbAsset->incrementVersion();
			$thumbAsset->setStatus(thumbAsset::FLAVOR_ASSET_STATUS_ERROR);
			$thumbAsset->setDescription($thumbAsset->getDescription() . "\n{$errDescription}");
			$thumbAsset->save();
			
			return $thumbAsset;
		}
		
		$thumbAsset->incrementVersion();
		$thumbAsset->setStatus(thumbAsset::ASSET_STATUS_QUEUED);
		
		if(file_exists($capturedPath))
		{
			list($width, $height, $type, $attr) = getimagesize($capturedPath);
			$thumbAsset->setWidth($width);
			$thumbAsset->setHeight($height);
			$thumbAsset->setSize(filesize($capturedPath));
		}
		
		$logPath = $capturedPath . '.log';
		if(file_exists($logPath))
		{
			$thumbAsset->incLogFileVersion();
			$thumbAsset->save();
			
			// creats the file sync
			$logSyncKey = $thumbAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_CONVERT_LOG);
			kFileSyncUtils::moveFromFile($logPath, $logSyncKey);
			KalturaLog::debug("Log archived file to: " . kFileSyncUtils::getLocalFilePathForKey($logSyncKey));
		}
		else
		{
			$thumbAsset->save();
		}
		
		$syncKey = $thumbAsset->getSyncKey(thumbAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		kFileSyncUtils::moveFromFile($capturedPath, $syncKey);
		KalturaLog::debug("Thumbnail archived file to: " . kFileSyncUtils::getLocalFilePathForKey($syncKey));
		
		$thumbAsset->setStatus(thumbAsset::ASSET_STATUS_READY);
		$thumbAsset->save();

		if($thumbAsset->hasTag(thumbParams::TAG_DEFAULT_THUMB))
		{
			// increment thumbnail version
			$entry->setThumbnail(".jpg");
			$entry->setCreateThumb(false);
			$entry->save();
			$entrySyncKey = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB);
			$syncFile = kFileSyncUtils::createSyncFileLinkForKey($entrySyncKey, $syncKey);
		
			if($syncFile)
			{
				// removes the DEFAULT_THUMB tag from all other thumb assets
				$entryThumbAssets = assetPeer::retrieveThumbnailsByEntryId($thumbAsset->getEntryId());
				foreach($entryThumbAssets as $entryThumbAsset)
				{
					if($entryThumbAsset->getId() == $thumbAsset->getId())
						continue;
						
					if(!$entryThumbAsset->hasTag(thumbParams::TAG_DEFAULT_THUMB))
						continue;
						
					$entryThumbAsset->removeTags(array(thumbParams::TAG_DEFAULT_THUMB));
					$entryThumbAsset->save();
				}
			}
		}
		
		if(!is_null($thumbAsset->getFlavorParamsId()))
			kFlowHelper::generateThumbnailsFromFlavor($thumbAsset->getEntryId(), null, $thumbAsset->getFlavorParamsId());
			
		return $thumbAsset;
	}
		
	/**
	 *
	 * Sets the default thumb for the assets
	 * If others already exists then we don't set the asset as not default
	 * @param thumbAsset $thumbAsset
	 */
	protected static function setIsDefaultThumb(thumbAsset $thumbAsset)
	{
		$entryThumbAssets = assetPeer::retrieveThumbnailsByEntryId($thumbAsset->getEntryId());
		
		foreach($entryThumbAssets as $entryThumbAsset)
		{
			//if we found another asset with a defualt tag. we remove our default tag
			if($entryThumbAsset->getId() !== $thumbAsset->getId() &&
			   $entryThumbAsset->hasTag(thumbParams::TAG_DEFAULT_THUMB))
			   {
			   	KalturaLog::debug("Found default tag on thumbAsset id[" . $entryThumbAsset->getId() . "]");
			   	$thumbAsset->removeTags(array(thumbParams::TAG_DEFAULT_THUMB));
			   	KalturaLog::debug("Removed default tag from thumbAsset id[" .  $thumbAsset->getId() . "]");
			   	return;
			   }
		}
	}
	
	private static function generateThumbnail(asset $srcAsset, thumbParamsOutput $destThumbParamsOutput, &$errDescription, $rotate=null)
	{
		$srcSyncKey = $srcAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($srcSyncKey, true, false);
		
		if(!$fileSync || $fileSync->getFileType() == FileSync::FILE_SYNC_FILE_TYPE_URL)
		{
			$errDescription = 'Source asset could has no valid file sync';
			return false;
		}
		
		$srcPath = $fileSync->getFullPath();
		$uniqid = uniqid('thumb_');
		$tempDir = kConf::get('cache_root_path') . DIRECTORY_SEPARATOR . 'thumb';
		if(!file_exists($tempDir))
			mkdir($tempDir, 0700, true);
		$destPath = $tempDir . DIRECTORY_SEPARATOR . $uniqid.jpg;
		$logPath = $destPath . '.log';
	
		if(!file_exists($srcPath))
		{
			$errDescription = "Source file [$srcPath] does not exist";
			return false;
		}
		
		if(!is_file($srcPath))
		{
			$errDescription = "Source file [$srcPath] is not a file";
			return false;
		}
		
		try
		{
			if($srcAsset->getType() == assetType::FLAVOR)
			{
				/* @var $srcAsset flavorAsset */
				$dar = null;
				$mediaInfo = mediaInfoPeer::retrieveByFlavorAssetId($srcAsset->getId());
				if($mediaInfo)
					$dar = $mediaInfo->getVideoDar();
				
				// generates the thumbnail
				$thumbMaker = new KFFMpegThumbnailMaker($srcPath, $destPath, kConf::get('bin_path_ffmpeg'));
				$created = $thumbMaker->createThumnail($destThumbParamsOutput->getVideoOffset(), $srcAsset->getWidth(), $srcAsset->getHeight(), null, null, $dar);
				if(!$created || !file_exists($destPath))
				{
					$errDescription = "Thumbnail not captured";
					return false;
				}
				$srcPath = $destPath;
				$uniqid = uniqid('thumb_');
				
				$tempDir = kConf::get('cache_root_path') . DIRECTORY_SEPARATOR . 'thumb';
				if(!file_exists($tempDir))
					mkdir($tempDir, 0700, true);
				$destPath = $tempDir . DIRECTORY_SEPARATOR . $uniqid . ".jpg";
			}
			
			$quality = $destThumbParamsOutput->getQuality();
			$cropType = $destThumbParamsOutput->getCropType();
			$cropX = $destThumbParamsOutput->getCropX();
			$cropY = $destThumbParamsOutput->getCropY();
			$cropWidth = $destThumbParamsOutput->getCropWidth();
			$cropHeight = $destThumbParamsOutput->getCropHeight();
			$bgcolor = $destThumbParamsOutput->getBackgroundColor();
			$width = $destThumbParamsOutput->getWidth();
			$height = $destThumbParamsOutput->getHeight();
			$scaleWidth = $destThumbParamsOutput->getScaleWidth();
			$scaleHeight = $destThumbParamsOutput->getScaleHeight();
			$density = $destThumbParamsOutput->getDensity();
			$stripProfiles = $destThumbParamsOutput->getStripProfiles();
			
			$cropper = new KImageMagickCropper($srcPath, $destPath, kConf::get('bin_path_imagemagick'), true);
			$cropped = $cropper->crop($quality, $cropType, $width, $height, $cropX, $cropY, $cropWidth, $cropHeight, $scaleWidth, $scaleHeight, $bgcolor, $density, $rotate, $stripProfiles);
			if(!$cropped || !file_exists($destPath))
			{
				$errDescription = "Crop failed";
				return false;
			}
			return $destPath;
		}
		catch(Exception $ex)
		{
			$errDescription = $ex->getMessage();
			return false;
		}
	}
	
	/**
	 * batch decideAddEntryFlavor is the decision layer for adding a single flavor conversion to an entry
	 *
	 * @param BatchJob $parentJob
	 * @param int $entryId
	 * @param int $flavorParamsId
	 * @param string $errDescription
	 * @param string $flavorAssetId
	 * @param array<kOperationAttributes> $dynamicAttributes
	 * @return BatchJob
	 */
	public static function decideAddEntryFlavor(BatchJob $parentJob = null, $entryId, $flavorParamsId, &$errDescription, $flavorAssetId = null,
			array $dynamicAttributes = array(), $priority = 0)
	{
		KalturaLog::log("entryId [$entryId], flavorParamsId [$flavorParamsId]");
		
		$originalFlavorAsset = assetPeer::retrieveOriginalByEntryId($entryId);
		if (is_null($originalFlavorAsset))
		{
			$errDescription = 'Original flavor asset not found';
			KalturaLog::err($errDescription);
			return null;
		}
	
		if ($originalFlavorAsset->getId() != $flavorAssetId && !$originalFlavorAsset->isLocalReadyStatus())
		{
			$errDescription = 'Original flavor asset not ready';
			KalturaLog::err($errDescription);
			return null;
		}
		
		// TODO - if source flavor is remote storage, create import job and mark the flavor as FLAVOR_ASSET_STATUS_WAIT_FOR_CONVERT
		
		$mediaInfoId = null;
		$mediaInfo = mediaInfoPeer::retrieveByFlavorAssetId($originalFlavorAsset->getId());
		if($mediaInfo)
			$mediaInfoId = $mediaInfo->getId();
		
		$flavorParams = assetParamsPeer::retrieveByPK($flavorParamsId);
		if (!$flavorParams)
		{
			KalturaLog::err("Flavor Params Id [$flavorParamsId] not found");
			return null;
		}
		
		$flavorParams->setDynamicAttributes($dynamicAttributes);
			
		$flavor = self::validateFlavorAndMediaInfo($flavorParams, $mediaInfo, $errDescription);
		
		if (is_null($flavor))
		{
			KalturaLog::err("Failed to validate media info [$errDescription]");
			return null;
		}
			
		if ($parentJob) // prefer the partner id from the parent job, although it should be the same
			$partnerId = $parentJob->getPartnerId();
		else
			$partnerId = $originalFlavorAsset->getPartnerId();
			
		if(is_null($flavorAssetId))
		{
			$flavorAsset = assetPeer::retrieveByEntryIdAndParams($entryId, $flavorParamsId);
			if($flavorAsset)
				$flavorAssetId = $flavorAsset->getId();
		}
		
		$flavor->_force = true; // force to convert the flavor, even if none complied
		
		$conversionProfile = myPartnerUtils::getConversionProfile2ForEntry($entryId);
		if($conversionProfile)
		{
			$flavorParamsConversionProfile = flavorParamsConversionProfilePeer::retrieveByFlavorParamsAndConversionProfile($flavor->getFlavorParamsId(), $conversionProfile->getId());
			if($flavorParamsConversionProfile)
				$flavor->setReadyBehavior($flavorParamsConversionProfile->getReadyBehavior());
		}
		
		$flavorAsset = kBatchManager::createFlavorAsset($flavor, $partnerId, $entryId, $flavorAssetId);
		if (!$flavorAsset)
		{
			KalturaLog::err("Failed to create flavor asset");
			return null;
		}
		
		if(!$flavorAsset->getIsOriginal())
			$flavor->setReadyBehavior(flavorParamsConversionProfile::READY_BEHAVIOR_IGNORE); // should not be taken in completion rules check
		
		$flavorAssetId = $flavorAsset->getId();
	
		$collectionTag = $flavor->getCollectionTag();
		if($collectionTag)
		{
			$entry = entryPeer::retrieveByPK($entryId);
			if(!$entry)
				throw new APIException(APIErrors::INVALID_ENTRY, $parentJob, $entryId);
		
			$flavorAssets = assetPeer::retrieveFlavorsByEntryId($entryId);
			$flavorAssets = assetPeer::filterByTag($flavorAssets, $collectionTag);
			$flavors = array();
			foreach($flavorAssets as $tagedFlavorAsset)
			{
				$errDescription = null;
				
				if($tagedFlavorAsset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_NOT_APPLICABLE || $tagedFlavorAsset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_DELETED)
					continue;

				$flavorParamsOutput = assetParamsOutputPeer::retrieveByAssetId($tagedFlavorAsset->getId());
				if(is_null($flavorParamsOutput))
				{
					KalturaLog::log("Creating flavor params output for asset [" . $tagedFlavorAsset->getId() . "]");
				
					$flavorParams = assetParamsPeer::retrieveByPK($tagedFlavorAsset->getId());
					$flavorParamsOutput = self::validateFlavorAndMediaInfo($flavorParams, $mediaInfo, $errDescription);
					
					if (is_null($flavorParamsOutput))
					{
						KalturaLog::err("Failed to validate media info [$errDescription]");
						continue;
					}
				}
				
				if($flavorParamsOutput)
				{
					KalturaLog::log("Adding Collection flavor [" . $flavorParamsOutput->getId() . "] for asset [" . $tagedFlavorAsset->getId() . "]");
					$flavors[$tagedFlavorAsset->getId()] = assetParamsOutputPeer::retrieveByAssetId($tagedFlavorAsset->getId());
				}
			}
			if($flavorAssetId)
			{
				KalturaLog::log("Updating Collection flavor [" . $flavor->getId() . "] for asset [" . $tagedFlavorAsset->getId() . "]");
				$flavors[$flavorAssetId] = $flavor;
			}			
			return self::decideCollectionConvert($collectionTag, $originalFlavorAsset, $entry, $parentJob, $flavors);
		}
		else	
		{	
			return self::decideFlavorConvert($flavorAsset, $flavor, $originalFlavorAsset, $conversionProfile->getId(), $mediaInfoId, $parentJob, null, false, $priority);
		}
	}
	
	/**
	 * batch validateConversionProfile validates profile completion rules
	 *
	 * @param mediaInfo $mediaInfo
	 * @param array $flavors is array of flavorParams
	 * @param string $errDescription
	 * @return array of flavorParamsOutput
	 */
	protected static function validateConversionProfile($partnerId, $entryId, mediaInfo $mediaInfo = null, array $flavors, array $conversionProfileFlavorParams, &$errDescription)
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
		$hasInvalidRequired = false;
		foreach($cdl->_targetList as $flavor)
		{
				// Get conv.prof data for that flavor
			$flavorParamsConversionProfile = $conversionProfileFlavorParams[$flavor->getFlavorParamsId()];

				// Update force-transcode flag.
				// This flag might be set by the DL, therefore overide only if it is not set.
			if(!$flavor->_force) {
				$flavor->_force = $flavorParamsConversionProfile->getForceNoneComplied();
			}  
			
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
					kBatchManager::createErrorFlavorAsset($flavor, $partnerId, $entryId, $errDescription);
					$hasInvalidRequired = true;
					continue;
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
					kBatchManager::createErrorFlavorAsset($flavor, $partnerId, $entryId, $errDescription);
					$hasInvalidRequired = true;
					continue;
				}
			}
			
			foreach($flavor->getTagsArray() as $tag)
			{
				KalturaLog::log("Taged [$tag] flavor added [" . $flavor->getFlavorParamsId() . "]");
				$tagedFlavors[$tag][$flavor->getFlavorParamsId()] = $flavor;
			}
		}
		if($hasInvalidRequired)
			return null;
		
				/*
				 * For 'playset' collections (MBR & ISM) make sure that the flavor that
				 * matches in the best way the source framee size, will be generated.
				 * Optimally this procedure should be executed for EVERY tag. But this
				 * might cause generation of unrequired flavors that might potentially
				 * harm the entry playback.
				 * Furthermore - we have duplicate iOS tagging (iphonenew and ipadnew),
				 * therefore anyhow at least one ipad flavor will be always generated.
				 * The other tags are less relevant for the framesize adjustment cases.
				 */
		if(isset($mediaInfo)) {
			if(array_key_exists(flavorParams::TAG_MBR, $tagedFlavors)) self::adjustToFramesize($mediaInfo, $tagedFlavors[flavorParams::TAG_MBR]);
			if(array_key_exists(flavorParams::TAG_ISM, $tagedFlavors)) self::adjustToFramesize($mediaInfo, $tagedFlavors[flavorParams::TAG_ISM]);
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
	 * batch adjustToFramesize - verify that in the given set of target flvors,
	 * there is at least one flavor that matches (or as close as possible)
	 * to the source frame size. If there is no such flavor - set '_create_anyway' for the best matching.
	 * The screencast sources are main cases for such adjustments, but there are other cases as well
	 *
	 * @param mediaInfo $source
	 * @param array $targetFlavorArr is array of flavorOutputParams
	 */
	protected static function adjustToFramesize(mediaInfo $source, array $targetFlavorArr)
	{
			/*
			 * Evaluate the 'adjusted' source height, to use as a for best matching flavor.
			 */
		$srcHgt = 0;
		if(isset($source)){
			$srcHgt = $source->getVideoHeight();
			$srcHgt = $srcHgt - ($srcHgt%16);
		}
		
		$matchSourceHeightIdx = null;	// index of the smallest flavor that matches the source height
		$matchSourceOriginalFlavorBR = 0;
		foreach($targetFlavorArr as $key=>$target){

				/*
				 * Ignore flavors that are smaller than the source -
				 * they are not in the scope of 'adjustToFramesize'
				 */
			if($target->getHeight()<$srcHgt){
KalturaLog::log("Source is larger than the target, skipping - key:$key, srcHgt:$srcHgt, trgHgt:".$target->getHeight());
				continue;
			}
			
				/*
				 * Stop searcing if there is a flavor, in that set, that matches the source frame size -
				 * no need to activate another flavor conversion
				 */
			if(!$target->_isNonComply || $target->_force || $target->_create_anyway) {
				$matchSourceHeightIdx = null;
KalturaLog::log("Found COMPLY/forced/create_anyway, leaving - key:$key, srcHgt:$srcHgt, trgHgt:".$target->getHeight());
				break;
			}
			
				/*
				 * If 'matching-target' is unset
				 * - set it to the current target
				 */
			if(!isset($matchSourceHeightIdx)){
				$matchSourceHeightIdx = $key;
				$flPrm = assetParamsPeer::retrieveByPKs(array($key));
				$matchSourceOriginalFlavorBR = $flPrm[0]->getVideoBitrate();
KalturaLog::log("Set matchSourceHeightIdx:$key, matchSourceOriginalFlavorBR:$matchSourceOriginalFlavorBR, srcHgt:$srcHgt");
				continue;
			}

				/*
				 * If current target is smaller than 'matching-target'
				 * - set it to the current target
				 */
			$flPrm = assetParamsPeer::retrieveByPKs(array($key));
			$flPrmBR = $flPrm[0]->getVideoBitrate();
			if($matchSourceOriginalFlavorBR>$flPrmBR){
				$matchSourceOriginalFlavorBR = $flPrmBR;
				$matchSourceHeightIdx = $key;
KalturaLog::log("Switch to matchSourceHeightIdx:$matchSourceHeightIdx, matchSourceOriginalFlavorBR:$matchSourceOriginalFlavorBR srcHgt:$srcHgt");
			}
//			if($target->getHeight()<$targetFlavorArr[$matchSourceHeightIdx]->getHeight()){
//			}

		}
		
				/*
				 * If samllest-source-height-matching is found and it is 'non-compliant' (therefore it willnot be generated),
				 * set '_create_anyway' flag for the 'matchSourceHeightIdx' flavor.
				 */
		if(isset($matchSourceHeightIdx) && $targetFlavorArr[$matchSourceHeightIdx]->_isNonComply) {
			$targetFlavorArr[$matchSourceHeightIdx]->_create_anyway = true;
KalturaLog::log("Forcing (create anyway) target $matchSourceHeightIdx");
			/*
			$first = reset($targetFlavorArr);
			if($first->_isNonComply) {
				$first->_force = true; // _create_anyway
			}
			*/
		}

	}
	
	/**
	 * validateFlavorAndMediaInfo validate and manipulate a flavor according to the given media info
	 *
	 * @param flavorParams $flavor
	 * @param mediaInfo $mediaInfo
	 * @param string $errDescription
	 * @return flavorParamsOutputWrap or null for fail
	 */
	protected static function validateFlavorAndMediaInfo(assetParams $flavor, mediaInfo $mediaInfo = null, &$errDescription)
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
	 * validateThumbAndMediaInfo validate and manipulate a thumbnail params according to the given media info
	 *
	 * @param thumbParams $thumbParams
	 * @param mediaInfo $mediaInfo
	 * @param string $errDescription
	 * @return thumbParamsOutput or null for fail
	 */
	protected static function validateThumbAndMediaInfo(thumbParams $thumbParams, mediaInfo $mediaInfo = null, &$errDescription)
	{
		$thumbParamsOutput = new thumbParamsOutput();
	
		$thumbParamsOutput->setFlavorParamsId($thumbParams->getId());
		$thumbParamsOutput->setFlavorParamsVersion($thumbParams->getVersion());
		$thumbParamsOutput->setName($thumbParams->getName());
		$thumbParamsOutput->setTags($thumbParams->getTags());
		$thumbParamsOutput->setDescription($thumbParams->getDescription());
		$thumbParamsOutput->setReadyBehavior($thumbParams->getReadyBehavior());
		$thumbParamsOutput->setFormat($thumbParams->getFormat());
		$thumbParamsOutput->setWidth($thumbParams->getWidth());
		$thumbParamsOutput->setHeight($thumbParams->getHeight());
		$thumbParamsOutput->setConversionEngines($thumbParams->getConversionEngines());
		$thumbParamsOutput->setConversionEnginesExtraParams($thumbParams->getConversionEnginesExtraParams());
		$thumbParamsOutput->setOperators($thumbParams->getOperators());
		$thumbParamsOutput->setEngineVersion($thumbParams->getEngineVersion());
		$thumbParamsOutput->setFileExt('jpg');
		$thumbParamsOutput->setRotate($mediaInfo? $mediaInfo->getVideoRotation() : null);
		
		$thumbParamsOutput->setCropType($thumbParams->getCropType());
		$thumbParamsOutput->setQuality($thumbParams->getQuality());
		$thumbParamsOutput->setCropX($thumbParams->getCropX());
		$thumbParamsOutput->setCropY($thumbParams->getCropY());
		$thumbParamsOutput->setCropWidth($thumbParams->getCropWidth());
		$thumbParamsOutput->setCropHeight($thumbParams->getCropHeight());
		$thumbParamsOutput->setCropProvider($thumbParams->getCropProvider());
		$thumbParamsOutput->setCropProviderData($thumbParams->getCropProviderData());
		$thumbParamsOutput->setVideoOffset($thumbParams->getVideoOffset());
		$thumbParamsOutput->setWidth($thumbParams->getWidth());
		$thumbParamsOutput->setHeight($thumbParams->getHeight());
		$thumbParamsOutput->setScaleWidth($thumbParams->getScaleWidth());
		$thumbParamsOutput->setScaleHeight($thumbParams->getScaleHeight());
		$thumbParamsOutput->setBackgroundColor($thumbParams->getBackgroundColor());
		
		if($mediaInfo)
		{
			if($thumbParamsOutput->getVideoOffset() && $mediaInfo->getVideoDuration())
			{
				$videoDurationSec = floor($mediaInfo->getVideoDuration() / 1000);
				if($thumbParamsOutput->getVideoOffset() > $videoDurationSec)
					$thumbParamsOutput->setVideoOffset($videoDurationSec);
			}
		}
		
		return $thumbParamsOutput;
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
		
		if(!$entry->getCreateThumb())
		{
			// mark the asset as ready
			$originalFlavorAsset->setStatusLocalReady();
			$originalFlavorAsset->save();
			
			kFlowHelper::generateThumbnailsFromFlavor($entry->getId(), null, $originalFlavorAsset->getFlavorParamsId());
			kBusinessPostConvertDL::handleConvertFinished(null, $originalFlavorAsset);
			return null;
		}
		
		$offset = $entry->getThumbOffset(); // entry getThumbOffset now takes the partner DefThumbOffset into consideration
		
		$srcSyncKey = $originalFlavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$srcFileSyncLocalPath = kFileSyncUtils::getLocalFilePathForKey($srcSyncKey);
		
		$postConvertAssetType = BatchJob::POSTCONVERT_ASSET_TYPE_BYPASS;
		return kJobsManager::addPostConvertJob($convertProfileJob, $postConvertAssetType, $srcFileSyncLocalPath, $originalFlavorAsset->getId(), null, true, $offset);
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
			self::setError($errDescription, $convertProfileJob, BatchJobType::CONVERT_PROFILE, $convertProfileJob->getEntryId());
			return false;
		}
	
		$originalFlavorAsset = assetPeer::retrieveOriginalByEntryId($entryId);
		if (is_null($originalFlavorAsset))
		{
			$errDescription = 'Original flavor asset not found';
			self::setError($errDescription, $convertProfileJob, BatchJobType::CONVERT_PROFILE, $convertProfileJob->getEntryId());
			return false;
		}
		
		// gets the list of flavor params of the conversion profile
		$list = flavorParamsConversionProfilePeer::retrieveByConversionProfile($profile->getId());
		if(! count($list))
		{
			$errDescription = "No flavors match the profile id [{$profile->getId()}]";
			self::setError($errDescription, $convertProfileJob, BatchJobType::CONVERT_PROFILE, $convertProfileJob->getEntryId());
						
			$originalFlavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_DELETED);
			$originalFlavorAsset->setDeletedAt(time());
			$originalFlavorAsset->save();
			
			return false;
		}
			
		$mediaInfo = null;
		if($mediaInfoId)
			$mediaInfo = mediaInfoPeer::retrieveByPK($mediaInfoId);
		
		$shouldConvert = self::shouldConvertProfileFlavors($profile, $mediaInfo, $originalFlavorAsset);
				
		// gets the ids of the flavor params
		$flavorsIds = array();
		$conversionProfileFlavorParams = array();
		foreach($list as $flavorParamsConversionProfile)
		{
			$flavorsId = $flavorParamsConversionProfile->getFlavorParamsId();
			$flavorsIds[] = $flavorsId;
			$conversionProfileFlavorParams[$flavorsId] = $flavorParamsConversionProfile;
		}
		KalturaLog::debug("Flavors in conversion profile [" . implode(',', $flavorsIds) . "]");
		
		$sourceFlavor = null;
		$flavors = assetParamsPeer::retrieveFlavorsByPKs($flavorsIds);
		
		$ingestedNeeded = self::checkConvertProfileParams($flavors, $conversionProfileFlavorParams, $entry, $sourceFlavor);
		
		KalturaLog::log(count($flavors) . " destination flavors found for this profile[" . $profile->getId() . "]");
		
		if(!$sourceFlavor)
		{
			KalturaLog::log("Source flavor params not found");
			$originalFlavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_TEMP);
			$originalFlavorAsset->save();
				/*
				 * Check for 'auto-intermediate-source
				 */
			$res = self::decideSourceFlavorConvert($entryId, null, $originalFlavorAsset, $profile->getId(), $flavors, $mediaInfo, $parentJob, $convertProfileJob);
			if(!$res)
				return false;
		}
		elseif($shouldConvert)
		{
			KalturaLog::log("Source flavor params [" . $sourceFlavor->getId() . "] found");
			$originalFlavorAsset->setFlavorParamsId($sourceFlavor->getId());
			
			$res = self::decideSourceFlavorConvert($entryId, $sourceFlavor, $originalFlavorAsset, $profile->getId(), $flavors, $mediaInfo, $parentJob, $convertProfileJob);
			if(!$res)
				return false;
						
			$originalFlavorAsset->setStatusLocalReady();
			$originalFlavorAsset->save();
			
			$entry->save();
			
			kFlowHelper::generateThumbnailsFromFlavor($parentJob->getEntryId(), $parentJob);
		}
		
		if(!count($flavors))
			$shouldConvert = false;
	
		if(!$shouldConvert)
		{
			if($ingestedNeeded)
			{
				kJobsManager::updateBatchJob($convertProfileJob, BatchJob::BATCHJOB_STATUS_FINISHED);
				return false;
			}
			else
			{
				self::bypassConversion($originalFlavorAsset, $entry, $convertProfileJob);
				return true;
			}
		}

		return self::decideProfileFlavorsConvert($parentJob, $convertProfileJob, $flavors, $conversionProfileFlavorParams,  $profile->getId(), $mediaInfo);
	}
		
	public static function continueProfileConvert(BatchJob $parentJob)
	{
		$convertProfileJob = $parentJob->getRootJob();
		if($convertProfileJob->getJobType() != BatchJobType::CONVERT_PROFILE)
			throw new Exception("Root job [" . $convertProfileJob->getId() . "] is not profile conversion");
		
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
			self::setError($errDescription, $convertProfileJob, BatchJobType::CONVERT_PROFILE, $entryId);
			throw new Exception($errDescription);
		}
	
		$originalFlavorAsset = assetPeer::retrieveOriginalByEntryId($entryId);
		if (is_null($originalFlavorAsset))
		{
			$errDescription = 'Original flavor asset not found';
			self::setError($errDescription, $convertProfileJob, BatchJobType::CONVERT_PROFILE, $entryId);
			throw new Exception($errDescription);
		}
		
		// gets the list of flavor params of the conversion profile
		$list = flavorParamsConversionProfilePeer::retrieveByConversionProfile($profile->getId());
		if(! count($list))
		{
			$errDescription = "No flavors match the profile id [{$profile->getId()}]";
			self::setError($errDescription, $convertProfileJob, BatchJobType::CONVERT_PROFILE, $entryId);
			
			$originalFlavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_DELETED);
			$originalFlavorAsset->setDeletedAt(time());
			$originalFlavorAsset->save();
			
			throw new Exception($errDescription);
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
				
		// gets the flavor params by the id
		$flavors = assetParamsPeer::retrieveFlavorsByPKs($flavorsIds);
		self::checkConvertProfileParams($flavors, $conversionProfileFlavorParams, $entry);
		
		KalturaLog::log(count($flavors) . " destination flavors found for this profile[" . $profile->getId() . "]");
		
		if(!count($flavors))
			return false;
	
		$mediaInfo = mediaInfoPeer::retrieveByFlavorAssetId($originalFlavorAsset->getId());
		
		return self::decideProfileFlavorsConvert($parentJob, $convertProfileJob, $flavors, $conversionProfileFlavorParams,  $profile->getId(), $mediaInfo);
	}
		
	public static function decideProfileFlavorsConvert(BatchJob $parentJob, BatchJob $convertProfileJob, array $flavors, array $conversionProfileFlavorParams, $conversionProfileId, mediaInfo $mediaInfo = null)
	{
		$entryId = $convertProfileJob->getEntryId();
		
		$originalFlavorAsset = assetPeer::retrieveOriginalByEntryId($entryId);
		if (is_null($originalFlavorAsset))
		{
			$errDescription = 'Original flavor asset not found';
			self::setError($errDescription, $convertProfileJob, BatchJobType::CONVERT_PROFILE, $convertProfileJob->getEntryId());
			return false;
		}
		
		$errDescription = null;
		$finalFlavors = self::validateConversionProfile($convertProfileJob->getPartnerId(), $entryId, $mediaInfo, $flavors, $conversionProfileFlavorParams, $errDescription);
			
		KalturaLog::log(count($finalFlavors) . " flavors returned from the decision layer");
		if(is_null($finalFlavors))
		{
			$convertProfileJob = kJobsManager::failBatchJob($convertProfileJob, $errDescription);
			KalturaLog::log("No flavors created");
			//throw new Exception($errDescription); no need to throw alert if the root job failed
		}
		
		if(strlen($errDescription))
		{
			$err = $convertProfileJob->getDescription() . $errDescription;
			$convertProfileJob->setDescription($err);
			$convertProfileJob->save();
		}
				
		$conversionsCreated = 0;
		$waitingAssets = 0;
		
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
			}
			else 
			{	
				KalturaLog::log("Adding flavor conversion with flavor params output id [" . $flavor->getId() . "] and flavor params asset id [" . $flavorAsset->getId() . "]");
				$madiaInfoId = $mediaInfo ? $mediaInfo->getId() : null;
				$createdJob = self::decideFlavorConvert($flavorAsset, $flavor, $originalFlavorAsset, $conversionProfileId, $madiaInfoId, $parentJob);
				
				if($createdJob)
					$conversionsCreated++;
				if($flavorAsset->getStatus() == flavorAsset::ASSET_STATUS_WAIT_FOR_CONVERT)
					$waitingAssets++;
			}
		}
		
		foreach($flavorsCollections as $tag => $flavors)
		{
			$createdJob = self::decideCollectionConvert($tag, $originalFlavorAsset, $entry, $parentJob, $flavors);
			if($createdJob)
				$conversionsCreated++;
		}
			
		if(!$conversionsCreated && !$waitingAssets)
		{
			KalturaLog::log("No flavors created: $errDescription");			
			$convertProfileJob = kJobsManager::failBatchJob($convertProfileJob, $errDescription);
			return false;
		}
		
		return true;
	}
	
	public static function decideFlavorConvert(flavorAsset $flavorAsset, flavorParamsOutput $flavor, flavorAsset $originalFlavorAsset, $conversionProfileId = null, $mediaInfoId = null, BatchJob $parentJob = null, $lastEngineType = null, $sameRoot = true, $priority = 0)
	{
		KalturaLog::debug('Source params ids ['.$flavor->getSourceAssetParamsIds().'], flavor asset id ['.$flavorAsset->getId().']');
		if(strlen(trim($flavor->getSourceAssetParamsIds())))
		{
			$readySrcFlavorAssets = self::getSourceFlavorAssets($flavorAsset, $flavor);
			if(!$readySrcFlavorAssets)
				return false;
		}
		else 
		{
			$readySrcFlavorAssets = array($originalFlavorAsset);
		}

		//all source flavors are ready
		if($flavorAsset->getStatus() == flavorAsset::ASSET_STATUS_WAIT_FOR_CONVERT)
		{
			$flavorAsset->setStatus(flavorAsset::ASSET_STATUS_QUEUED);
			$affectedRows = $flavorAsset->save();
			KalturaLog::debug('Changing asset status from Waiting to Queued, affected rows ['.$affectedRows.']');
			if(!$affectedRows)
				return false;
			
			$parentJob = self::getParentJobForWaitingAssetConversion($flavorAsset->getEntryId(), $parentJob);
		}
		
		$srcSyncKeys = array(); 
		foreach ($readySrcFlavorAssets as $srcAsset) 
		{
			$srcSyncKeys[] = $srcAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		}
		return kJobsManager::addFlavorConvertJob($srcSyncKeys, $flavor, $flavorAsset->getId(), $conversionProfileId, $mediaInfoId, $parentJob, $lastEngineType, $sameRoot, $priority);
	}

	private static function getParentJobForWaitingAssetConversion($entryId, BatchJob $parentJob = null)
	{
		KalturaLog::debug('start');
		if($parentJob && $parentJob->getJobType() == BatchJobType::POSTCONVERT) 
		{
			KalturaLog::debug('parent is postconvert');
			//In case the flavor conversion is triggered by the ingested flavor add the conversion job
			//under the convert profile job if available
			$c = new Criteria();
			$c->add ( BatchJobPeer::ENTRY_ID , $entryId );
			$c->add ( BatchJobPeer::JOB_TYPE , BatchJobType::CONVERT_PROFILE );
			$statuses = BatchJobPeer::getUnClosedStatusList();
			$statuses[] = BatchJob::BATCHJOB_STATUS_ALMOST_DONE;
			$c->add ( BatchJobPeer::STATUS, $statuses,  Criteria::IN);
			
			$batchJob = BatchJobPeer::doSelectOne( $c );
			if($batchJob)
			{
				KalturaLog::debug("parent job id ".$batchJob->getId());
				return $batchJob;
			}
		}
		return $parentJob;
	}
	
	private static function getSourceFlavorAssets(flavorAsset $flavorAsset, flavorParamsOutput $flavor)
	{
		$srcFlavorParamsIds = explode(',', trim($flavor->getSourceAssetParamsIds()));
		
		$c = new Criteria();
		$c->add(assetPeer::ENTRY_ID, $flavorAsset->getEntryId());
		$c->add(assetPeer::STATUS, array(flavorAsset::ASSET_STATUS_READY, flavorAsset::ASSET_STATUS_NOT_APPLICABLE), Criteria::IN);
		$c->add(assetPeer::FLAVOR_PARAMS_ID, $srcFlavorParamsIds, Criteria::IN);
		
		$readyAndNonApplicableAssetsCount = assetPeer::doCount($c);
		
		KalturaLog::debug('Verify source flavors are ready: number of ready and NA assets ['.$readyAndNonApplicableAssetsCount.'], number of source params ids ['.count($srcFlavorParamsIds).']');
		if($readyAndNonApplicableAssetsCount < count($srcFlavorParamsIds))
		{
			KalturaLog::debug('Not all source flavors are ready, changing status to WAIT_FOR_CONVERT');
			$flavorAsset->setStatus(flavorAsset::ASSET_STATUS_WAIT_FOR_CONVERT);
			$flavorAsset->setDescription("Source flavor assets are not ready");
			$flavorAsset->save();
				
			return false;
		}
		
		$srcFlavors = assetPeer::retrieveReadyByEntryIdAndFlavorParams($flavorAsset->getEntryId(), $srcFlavorParamsIds);
		if(!count($srcFlavors))
		{
			//assuming all source flavors are Not Applicable
			KalturaLog::log("Flavor [" . $flavorAsset->getFlavorParamsId() . "] is set to N/A since all it's sources are N/A");
			$flavorAsset->setStatus(flavorAsset::ASSET_STATUS_NOT_APPLICABLE);
			$flavorAsset->save();	
			return false;
		}
		return $srcFlavors;
	}
	
	private static function decideCollectionConvert($collectionTag, flavorAsset $originalFlavorAsset, entry $entry, BatchJob $parentJob = null, array $flavors)
	{	
		//TODO: add support for source other than original	
		$srcSyncKey = $originalFlavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
				
		switch($collectionTag)
		{
			case flavorParams::TAG_ISM:
				KalturaLog::log("Calling addConvertIsmCollectionJob with [" . count($flavors) . "] flavor params");
				return kJobsManager::addConvertIsmCollectionJob($collectionTag, $srcSyncKey, $entry, $parentJob, $flavors, false);
				
			default:
				KalturaLog::log("Error: Invalid collection tag [$collectionTag]");
				return null;
		}
	}
	
	private static function decideSourceFlavorConvert($entryId, assetParams $sourceFlavor = null, flavorAsset $originalFlavorAsset, $conversionProfileId, $flavors, mediaInfo $mediaInfo = null, BatchJob $parentJob, BatchJob $convertProfileJob)
	{
		if($sourceFlavor && ($sourceFlavor->getOperators() || $sourceFlavor->getConversionEngines()))
		{
			KalturaLog::log("Source flavor asset requires conversion");
				
			$srcSyncKey = $originalFlavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			$errDescription = null;
			$sourceFlavorOutput = self::validateFlavorAndMediaInfo($sourceFlavor, $mediaInfo, $errDescription);
		
			if(!$sourceFlavorOutput)
			{
				if(!$errDescription)
					$errDescription = "Failed to create flavor params output from source flavor";
						
				$originalFlavorAsset->setDescription($originalFlavorAsset->getDescription() . "\n$errDescription");
				$originalFlavorAsset->setStatus(flavorAsset::ASSET_STATUS_ERROR);
				$originalFlavorAsset->save();
					
				kBatchManager::updateEntry($entryId, entryStatus::ERROR_CONVERTING);
				
				kJobsManager::updateBatchJob($convertProfileJob, BatchJob::BATCHJOB_STATUS_FAILED);
				return false;
			}
		}
		elseif($mediaInfo) 
		{
			/*
			 * Check whether there is a need for an intermediate source pre-processing
			 */
			$sourceFlavorOutput = KDLWrap::GenerateIntermediateSource($mediaInfo, $flavors);
			if(!$sourceFlavorOutput)
				return true;
			
			$srcSyncKey = $originalFlavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			$errDescription = null;
			
			/*
			 * Save the original source asset in another asset, in order 
			 * to prevent its liquidated by the inter-source asset.
			 * But, do it only if the conversion profile contains source flavor
			 */
			if($sourceFlavor) 
			{
				$sourceAsset = assetPeer::retrieveById($mediaInfo->getFlavorAssetId());
				$copyFlavorParams = assetParamsPeer::retrieveBySystemName(self::SAVE_ORIGINAL_SOURCE_FLAVOR_PARAM_SYS_NAME);
				if (!$copyFlavorParams)
					throw new APIException(APIErrors::OBJECT_NOT_FOUND);
				
				$asset = $sourceAsset->copy();
				$asset->setFlavorParamsId($copyFlavorParams->getId());
				$asset->setFromAssetParams($copyFlavorParams);
				$asset->setStatus(flavorAsset::ASSET_STATUS_READY);
				$asset->setIsOriginal(0);
				$asset->setTags($copyFlavorParams->getTags());
				$asset->incrementVersion();
				$asset->save();
				kFileSyncUtils::createSyncFileLinkForKey($asset->getSyncKey(asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET), $sourceAsset->getSyncKey(asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET));
				$origFileSync = kFileSyncUtils::getLocalFileSyncForKey($sourceAsset->getSyncKey(asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET));
				$asset->setSize(intval($origFileSync->getFileSize()/1000));		
				$asset->save();
			}
		}
		
			/*
			 * '_passthrough' controls whether the source is to be 'passthrough' although there 
			 * is a source flavor that contains transcoder settings.
			 * Looks for a '_passthrough' flag on the source's flavor params output.
			 */
		if(!$sourceFlavorOutput || $sourceFlavorOutput->_passthrough==true)
			return true;
		
		// save flavor params
		$sourceFlavorOutput->setPartnerId($sourceFlavorOutput->getPartnerId());
		$sourceFlavorOutput->setEntryId($entryId);
		$sourceFlavorOutput->setFlavorAssetId($originalFlavorAsset->getId());
		$sourceFlavorOutput->setFlavorAssetVersion($originalFlavorAsset->getVersion());
		$sourceFlavorOutput->save();
			
		if($errDescription)
			$originalFlavorAsset->setDescription($originalFlavorAsset->getDescription() . "\n$errDescription");
				
		$errDescription = kBusinessConvertDL::parseFlavorDescription($sourceFlavorOutput);
		if($errDescription)
			$originalFlavorAsset->setDescription($originalFlavorAsset->getDescription() . "\n$errDescription");
	
		// decided by the business logic layer
		if($sourceFlavorOutput->_create_anyway)
		{
			KalturaLog::log("Flavor [" . $sourceFlavorOutput->getFlavorParamsId() . "] selected to be created anyway");
		}
		else
		{
			if(!$sourceFlavorOutput->IsValid())
			{
				KalturaLog::log("Flavor [" . $sourceFlavorOutput->getFlavorParamsId() . "] is invalid");
				$originalFlavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_ERROR);
				$originalFlavorAsset->save();
				
				$errDescription = "Source flavor could not be converted";
				self::setError($errDescription, $convertProfileJob, BatchJobType::CONVERT_PROFILE, $convertProfileJob->getEntryId());
				
				return false;
			}
				
			if($sourceFlavorOutput->_force)
				KalturaLog::log("Flavor [" . $sourceFlavorOutput->getFlavorParamsId() . "] is forced");
			elseif($sourceFlavorOutput->_isNonComply)
				KalturaLog::log("Flavor [" . $sourceFlavorOutput->getFlavorParamsId() . "] is none-comply");
			else
				KalturaLog::log("Flavor [" . $sourceFlavorOutput->getFlavorParamsId() . "] is valid");
		}
				
		$originalFlavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_CONVERTING);
		if(isset($sourceFlavor)) {
			$originalFlavorAsset->addTags($sourceFlavor->getTagsArray());
			$originalFlavorAsset->setFileExt($sourceFlavorOutput->getFileExt());
			$originalFlavorAsset->save();
		}
			
		// save flavor params
		$sourceFlavorOutput->setFlavorAssetVersion($originalFlavorAsset->getVersion());
		$sourceFlavorOutput->save();
			
		kJobsManager::addFlavorConvertJob(array($srcSyncKey), $sourceFlavorOutput, $originalFlavorAsset->getId(), $conversionProfileId, $mediaInfo->getId(), $parentJob);
		return false;
	}

	private static function setError($errDescription, BatchJob $batchJob, $batchJobType, $entryId)
	{
		$batchJob = kJobsManager::failBatchJob($batchJob, $errDescription, $batchJobType);
		kBatchManager::updateEntry($entryId, entryStatus::ERROR_CONVERTING);
		KalturaLog::err($errDescription);		
	}
	
	private static function checkConvertProfileParams(&$flavors, $conversionProfileFlavorParams, $entry, &$sourceFlavor = null)
	{	
		$ingestedNeeded = false;
		$dynamicFlavorAttributes = $entry->getDynamicFlavorAttributes();
		$entryIngestedFlavors = explode(',', $entry->getFlavorParamsIds());
		
		foreach($flavors as $index => $flavor)
		{
			KalturaLog::debug("Check flavor [" . $flavor->getId() . "]");
			if(!isset($conversionProfileFlavorParams[$flavor->getId()]))
				continue;
				
			$conversionProfileFlavorParamsItem = $conversionProfileFlavorParams[$flavor->getId()];
			
			// if flavor is not source, apply dynamic attributes defined for id -2 (all flavors)
			if(!$flavor->hasTag(flavorParams::TAG_SOURCE))
			{
    			if(isset($dynamicFlavorAttributes[flavorParams::DYNAMIC_ATTRIBUTES_ALL_FLAVORS_INDEX]))
    			{
    				foreach($dynamicFlavorAttributes[flavorParams::DYNAMIC_ATTRIBUTES_ALL_FLAVORS_INDEX] as $attributeName => $attributeValue)
    					$flavor->setDynamicAttribute($attributeName, $attributeValue);
    			}
			}

			// overwrite dynamic attributes if defined for this specific flavor
			if(isset($dynamicFlavorAttributes[$flavor->getId()]))
			{
				foreach($dynamicFlavorAttributes[$flavor->getId()] as $attributeName => $attributeValue)
					$flavor->setDynamicAttribute($attributeName, $attributeValue);
			}
			
			if($flavor->hasTag(flavorParams::TAG_SOURCE))
			{
				$sourceFlavor = $flavor;
				unset($flavors[$index]);
				KalturaLog::debug("Flavor [" . $flavor->getId() . "] won't be converted because it has source tag");
				continue;
			}
			
			if($conversionProfileFlavorParamsItem->getOrigin() == assetParamsOrigin::CONVERT)
				continue;
			
			if($conversionProfileFlavorParamsItem->getOrigin() == assetParamsOrigin::INGEST)
			{
				unset($flavors[$index]);
				$ingestedNeeded = true;
				KalturaLog::debug("Flavor [" . $flavor->getId() . "] won't be converted because it should be ingested");
				continue;
			}

			if(in_array($flavor->getId(), $entryIngestedFlavors) &&
				$conversionProfileFlavorParamsItem->getOrigin() == assetParamsOrigin::CONVERT_WHEN_MISSING)
			{
				KalturaLog::debug("Flavor [" . $flavor->getId() . "] won't be converted because it already ingested");
				unset($flavors[$index]);
			}
		}	
		
		return $ingestedNeeded;	
	}	
	
	private static function shouldConvertProfileFlavors(conversionProfile2 $profile, mediaInfo $mediaInfo = null, flavorAsset $originalFlavorAsset)
	{
		$shouldConvert = true;
					
		if($profile->getCreationMode() == conversionProfile2::CONVERSION_PROFILE_2_CREATION_MODE_AUTOMATIC_BYPASS_FLV)
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
		return $shouldConvert;	
	}
}