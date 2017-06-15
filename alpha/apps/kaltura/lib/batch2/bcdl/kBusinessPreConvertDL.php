<?php

class kBusinessPreConvertDL
{

	const SAVE_ORIGINAL_SOURCE_FLAVOR_PARAM_SYS_NAME = 'save_original_source_flavor_params';

	const TAG_VARIANT_A = 'watermark_a';
	const TAG_VARIANT_B = 'watermark_b';
	const TAG_VARIANT_PAIR_ID = 'watermark_pair_';
	
	public static $conditionalMapBySourceType = array (
			EntrySourceType::LECTURE_CAPTURE => "LECTURE_CAPTURE_PROFILE",
	);

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
			KalturaLog::info("Look for flavor params [$sourceParamsId]");
			$srcAsset = assetPeer::retrieveByEntryIdAndParams($entryId, $sourceParamsId);
			if($srcAsset && $srcAsset->isLocalReadyStatus())
				return $srcAsset;
		}

		KalturaLog::info("Look for a flavor tagged with thumbsource of entry [$entryId]");
		$srcAsset = assetPeer::retrieveHighestBitrateByEntryId($entryId, flavorParams::TAG_THUMBSOURCE);
		if($srcAsset && $srcAsset->isLocalReadyStatus())
			return $srcAsset;


		KalturaLog::info("Look for original flavor of entry [$entryId]");
		$srcAsset = assetPeer::retrieveOriginalByEntryId($entryId);
		if($srcAsset && $srcAsset->isLocalReadyStatus())
			return $srcAsset;


		KalturaLog::info("Look for highest bitrate flavor with web tag on entry [$entryId]");
		$srcAsset = assetPeer::retrieveHighestBitrateByEntryId($entryId, flavorParams::TAG_WEB);
		if($srcAsset && $srcAsset->isLocalReadyStatus())
			return $srcAsset;


		KalturaLog::info("Look for highest bitrate flavor of entry [$entryId]");
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
		$destThumbParamsOutput = self::validateThumbAndMediaInfo($destThumbParams, $mediaInfo, $errDescription, $srcAsset);

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
			KalturaLog::info("Log archived file to: " . kFileSyncUtils::getLocalFilePathForKey($logSyncKey));
		}
		else
		{
			$thumbAsset->save();
		}

		$syncKey = $thumbAsset->getSyncKey(thumbAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		kFileSyncUtils::moveFromFile($capturedPath, $syncKey);
		KalturaLog::info("Thumbnail archived file to: " . kFileSyncUtils::getLocalFilePathForKey($syncKey));

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
				assetPeer::removeThumbAssetDeafultTags($entry->getId(), $thumbAsset->getId());
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
			   	$thumbAsset->removeTags(array(thumbParams::TAG_DEFAULT_THUMB));
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
		$destPath = $tempDir . DIRECTORY_SEPARATOR . $uniqid . '.jpg';
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
				$destPath = $tempDir . DIRECTORY_SEPARATOR . $uniqid . '.jpg';
			}

			if($srcAsset->getType() == assetType::THUMBNAIL)
			{
				$tempDir = kConf::get('cache_root_path') . DIRECTORY_SEPARATOR . 'thumb';
				if(!file_exists($tempDir))
					mkdir($tempDir, 0700, true);
				$destPath = $tempDir . DIRECTORY_SEPARATOR . $uniqid . "." . $srcAsset->getFileExt();
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

		$mediaInfoId = null;
		$mediaInfo = mediaInfoPeer::retrieveByFlavorAssetId($originalFlavorAsset->getId());
		if($mediaInfo){
			$mediaInfoId = $mediaInfo->getId();
			/*
			 * Auto decrypt
			 */
			if($originalFlavorAsset->getEncryptionKey()){
				KalturaLog::log("Encrypted Source, adding decryption (encryptionKey:".$originalFlavorAsset->getEncryptionKey().")");
				$mediaInfo->decryptionKey = bin2hex(base64_decode($originalFlavorAsset->getEncryptionKey()));
			}
		}
		$flavorParams = assetParamsPeer::retrieveByPK($flavorParamsId);
		if (!$flavorParams)
		{
			KalturaLog::err("Flavor Params Id [$flavorParamsId] not found");
			return null;
		}
		
		//Check if the flavor we are trying to generate has source flavor, source can only be ingested, not generated.
		if(in_array(assetParams::TAG_SOURCE, $flavorParams->getTagsArray()))
		{
			KalturaLog::err("Cannot generate flavor params Id [$flavorParamsId], flavor has source tag");
			return null;
		}

		$flavorParams->setDynamicAttributes($dynamicAttributes);

		$conversionProfile = myPartnerUtils::getConversionProfile2ForEntry($entryId);
		if($conversionProfile)
		{
			$flavorParamsConversionProfile = flavorParamsConversionProfilePeer::retrieveByFlavorParamsAndConversionProfile($flavorParams->getId(), $conversionProfile->getId());
			if($flavorParamsConversionProfile){
				/*
				 * Update flavorParams settings with overloaded params from 'conversionProfileFlavorParams'
				 */
				self::overrideFlavorParamsWithConversionProfileSettings($flavorParams, $flavorParamsConversionProfile);
				self::overrideFlavorParamsWithMultistreamData($flavorParams, $entryId);
			}
		}

		self::adjustAssetParams($entryId, array($flavorParams));
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

		if($flavorParamsConversionProfile)
			$flavor->setReadyBehavior($flavorParamsConversionProfile->getReadyBehavior());

		$flavorAsset = kBatchManager::createFlavorAsset($flavor, $partnerId, $entryId, $flavorAssetId);
		if (!$flavorAsset)
		{
			return null;
		}

			/*
			 * Add at rest encryption
			 */
		if($flavor->_isEncrypted==true){
			self::setEncryptionAtRest($flavor, $flavorAsset);
		}
			/*
			 * Add label notation
			 */
		if(($multiStreamJson=$flavor->getMultiStream())!=null && ($multiStreamObj=json_decode($multiStreamJson))!=null
		&& KDLAudioMultiStreaming::IsStreamFieldSet($multiStreamObj, "label")){
			$flavorAsset->setLabel($multiStreamObj->audio->streams[0]->label);
			$flavorAsset->save();
		}

		if(!$flavorAsset->getIsOriginal())
			$flavor->setReadyBehavior(flavorParamsConversionProfile::READY_BEHAVIOR_IGNORE); // should not be taken in completion rules check

		$flavorAssetId = $flavorAsset->getId();

		$collectionTag = $flavor->getCollectionTag();
			/*
			 * CHANGE: collection porcessing only for ExpressionEncoder jobs
			 * to allow FFmpeg/ISMV processing
			 */
		KalturaLog::log("Check for collection case - asset(".$flavorAssetId."),engines(".$flavor->getConversionEngines().")");
		if($collectionTag && $flavor->getConversionEngines()==conversionEngineType::EXPRESSION_ENCODER3)
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
					self::adjustAssetParams($entryId, array($flavorParams));
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

		self::adjustAssetParams($entryId, $flavors);
		// call the decision layer
		KalturaLog::log("Generate Target " . count($flavors) . " Flavors supplied");
		/*
		 * Update flavorParams settings with overloeded params from 'conversionProfileFlavorParams'
		 */
		foreach($flavors as $flavor) {
			$flavorParamsConversionProfile = $conversionProfileFlavorParams[$flavor->getId()];
			self::overrideFlavorParamsWithConversionProfileSettings($flavor, $flavorParamsConversionProfile);
			self::overrideFlavorParamsWithMultistreamData($flavor, $entryId);
		}

		$cdl = KDLWrap::CDLGenerateTargetFlavors($mediaInfo, $flavors);
		KalturaLog::log("Generate Target " . count($cdl->_targetList) . " Flavors returned");

		// check for errors
		$errDescription = '';
		if(count($cdl->_errors))
		{
			$errDesc = '';
			foreach($cdl->_errors as $section => $errors)
			{
				$errDesc .= "$section errors: " . join(";", $errors) . "\n";
				foreach($errors as $error)
				{
					if (strpos($error, 'Invalid File - No media content' !== false))
					{
						$errDescription .= "\nMedia err: $errDesc";
						KalturaLog::err($error);
						throw new kCoreException($error , KDLErrors::NoValidMediaStream);
					}

					if (strpos($error,'Invalid frame dimensions') !== false)
					{
						$errDescription .= "\nMedia err: $errDesc";
						KalturaLog::err($error);
						throw new kCoreException($error , KDLErrors::SanityInvalidFrameDim);
					}
				}
			}

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
				// If the flavor is set to 'force' (generate the asset regardless of any Kaltura optimization),
				// don't fail it even if it is 'NonComply'
				if($flavor->getReadyBehavior() == flavorParamsConversionProfile::READY_BEHAVIOR_REQUIRED && !$flavor->_force)
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
				 * matches in the best way the source frame size, will be generated.
				 * Optimally this procedure should be executed for EVERY tag. But this
				 * might cause generation of unrequired flavors that might potentially
				 * harm the entry playback.
				 * Furthermore - we have duplicate iOS tagging (iphonenew and ipadnew),
				 * therefore anyhow at least one ipad flavor will be always generated.
				 * The other tags are less relevant for the framesize adjustment cases.
				 */
		if(isset($mediaInfo) && ($profile=myPartnerUtils::getConversionProfile2ForEntry($entryId))!=null) {
			if(($collectionTags=$profile->getCollectionTags())==null ) {
				$collectionTagsArr = array(flavorParams::TAG_MBR, flavorParams::TAG_ISM);
			}
			else {
				$collectionTagsArr = explode(',',$collectionTags);
				if(count($collectionTagsArr)==0)
					$collectionTagsArr = array(flavorParams::TAG_MBR, flavorParams::TAG_ISM);
			}
			foreach($collectionTagsArr as $collectionTag) {
				if(array_key_exists($collectionTag, $tagedFlavors)) self::adjustToFramesize($mediaInfo, $tagedFlavors[$collectionTag]);
			}
		}
		// filter out all not forced, none complied, and invalid flavors
		$finalTagedFlavors = array();
		foreach($tagedFlavors as $tag => $tagedFlavorsArray)
		{
			KalturaLog::log("Filtering flavors by tag [$tag]");
				/*
				 * Digital-watermark tags should not participate in the 'tagged' flavor activation logic
				 */
			if(strstr($tag,"watermark_pair_")==false && strstr($tag,self::TAG_VARIANT_A)==false && strstr($tag,self::TAG_VARIANT_B)==false) {
				$finalTagedFlavors[$tag] = kBusinessConvertDL::filterTagFlavors($tagedFlavorsArray);
			}
			else
				$finalTagedFlavors[$tag] = $tagedFlavorsArray;
		}

		$finalFlavors = array();
		foreach($finalTagedFlavors as $tag => $tagedFlavorsArray)
		{
			foreach($tagedFlavorsArray as $flavorParamsId => $tagedFlavor)
				$finalFlavors[$flavorParamsId] = $tagedFlavor;
		}
			/*
			 * Digital-watermark flavors go through 'find the matching pair' procedure
			 */
		if(array_key_exists(self::TAG_VARIANT_A,$finalTagedFlavors) && array_key_exists(self::TAG_VARIANT_B,$finalTagedFlavors)) {
			$finalFlavors = self::adjustToPairedDigitalWatermarking(self::TAG_VARIANT_A, $finalTagedFlavors, $finalFlavors);
			$finalFlavors = self::adjustToPairedDigitalWatermarking(self::TAG_VARIANT_B, $finalTagedFlavors, $finalFlavors);
		}
		// sort the flavors to decide which one will be performed first
		usort($finalFlavors, array('kBusinessConvertDL', 'compareFlavors'));
		KalturaLog::log(count($finalFlavors) . " flavors sorted for execution");
		return $finalFlavors;
	}

	/**
	 * batch adjustToPairedDigitalWatermarking
	 *
	 * @param watermarkVersionString version tag
	 * @param array finalTagedFlavors is array of flavorParamsOutput
	 * @param array finalFlavors
	 * @return array of flavorParamsOutput
	 */
	protected static function adjustToPairedDigitalWatermarking($watermarkVersionString, $finalTagedFlavors, $finalFlavors)
	{
		foreach($finalTagedFlavors[$watermarkVersionString] as  $flavorParamsId => $final){
				// If not to be produced - not-applicable and not forced (or not 'create_anyway')
				// ==> carry on to next flavor
			if(($final->_isRedundant || $final->_isNonComply) && !($final->_create_anyway || $final->_force)){
				continue;
			}
			KalturaLog::log("id:$flavorParamsId,".($final->getName()).",rdn:".$final->_isRedundant.",non:".$final->_isNonComply.",frc:".$final->_force.",any:".$final->_create_anyway.",tags:".$final->getTags());
			$tags = explode(',', $final->getTags());
			foreach($tags as $tag) {
				// Only for 'digital-watermark' flavors, otherwise - skip
				if(strstr($tag,self::TAG_VARIANT_PAIR_ID)==false)
					continue;

				KalturaLog::log("found tag:$tag,count:".(count($finalTagedFlavors[$tag])));
				// Only a pair of flavors allowed for 'digital-watermark', otherwsie - skip
				if(count($finalTagedFlavors[$tag])==2){
					$f1 = reset($finalTagedFlavors[$tag]);
					$f2 = end($finalTagedFlavors[$tag]);
					KalturaLog::log("found pair:".($f1->getFlavorParamsId()).",".$f2->getFlavorParamsId());
					if($f1->getFlavorParamsId()==$flavorParamsId) {
						$finalFlavors[$f2->getFlavorParamsId()]->_create_anyway = 1;
					}
					else {
						$finalFlavors[$f1->getFlavorParamsId()]->_create_anyway = 1;
					}
				}
				break;
			}
		}
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
		$targetLargestHeight = 0;		// To save the height of the largest target flavor and its key
		$targetLargestHeightKey = null;	// and its key id.

		foreach($targetFlavorArr as $key=>$target){

				/*
				 * Ignore flavors that are smaller than the source -
				 * they are not in the scope of 'adjustToFramesize'.
				 * Track the largest target flavor, required for cases when the source height is larger than ALL flavors
				 */
			if($target->getHeight()<$srcHgt){
KalturaLog::log("Source is larger than the target, skipping - key:$key, srcHgt:$srcHgt, trgHgt:".$target->getHeight());

				if($targetLargestHeight<$target->getHeight()){
					$targetLargestHeight=$target->getHeight();
					$targetLargestHeightKey = $key;
				}
				continue;
			}

				/*
				 * Stop searching if there is a flavor, in that set, that matches the source frame size -
				 * no need to activate another flavor conversion
				 */
			if(!$target->_isNonComply || $target->_force || $target->_create_anyway) {
				$matchSourceHeightIdx = null;
				$targetLargestHeightKey = null;
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
			 * If no match was found, use the largest target flavor
			 */
		if(!isset($matchSourceHeightIdx) && isset($targetLargestHeightKey)
		&& $targetFlavorArr[$targetLargestHeightKey]->getHeight()<$srcHgt){
			$matchSourceHeightIdx = $targetLargestHeightKey;
		}
				/*
				 * If smallest-source-height-matching is found and it is 'non-compliant' (therefore it will not be generated),
				 * set '_create_anyway' flag for the 'matchSourceHeightIdx' flavor.
				 */
		if(isset($matchSourceHeightIdx) && $targetFlavorArr[$matchSourceHeightIdx]->_isNonComply) {
			$targetFlavorArr[$matchSourceHeightIdx]->_create_anyway = true;
KalturaLog::log("Forcing (create anyway) target $matchSourceHeightIdx");

			/*
			 * Check whether the next closest flavor to the matched one is redundant.
			 * If so - set it to NonComply.
			 */
			self::removeClosestRedundantFlavor($targetFlavorArr, $matchSourceHeightIdx);
		}
	}

	/**
	 *
	 * @param unknown_type $targetFlavorArr
	 * @param unknown_type $flavorIdx
	 * @param unknown_type $thresholdRatio
	 */
	protected static function removeClosestRedundantFlavor(array &$targetFlavorArr, $flavorIdx, $thresholdRatio=0.15)
	{
		/*
		 * Check whether the next closest flavor to the matched one is redundant.
		 * If so - set it to NonComply.
		 * Example -
		 * Matched flavor is 2700Kbps/1080p, the closest is 2500Kbps/720p.
		 * The 2500 flavor is very close to the matched flavor, thus redundant.
		 */
		$closest=null;
		$matchSourceHeightFlavor = $targetFlavorArr[$flavorIdx];
		/*
		 * Loop for the closest flavor
		 */
		foreach($targetFlavorArr as $key=>$flavor){
			if($key==$flavorIdx)
				continue;
			/*
			 * To avoid playback/delivery inconsistency, the aledged redundant flavor
			 * should share the same tags like the newly matched
			 */
			if($matchSourceHeightFlavor->getTags()!=$flavor->getTags())
				continue;
			$diff = $matchSourceHeightFlavor->getVideoBitrate()-$flavor->getVideoBitrate();
			/*
			 * The flavor considered to be redundant if it's bitrate
			 * is closer than 15% to the matched flavor,
			 * otherwise - skip it
			 */
			if($diff<0 || $diff/$matchSourceHeightFlavor->getVideoBitrate()>$thresholdRatio){
				continue;
			}
			KalturaLog::log("Look for redundant: diff($diff),percent(".($diff*100/$matchSourceHeightFlavor->getVideoBitrate()).")");
			if(!isset($closest)){
				$closest = $key;
				$closestDiff = $diff;
				continue;
			}
			if($closestDiff>$diff) {
				$closest = $key;
				$closestDiff = $diff;
			}
		}
		if(isset($closest)){
			KalturaLog::log("Found redundant: bitrate ".$targetFlavorArr[$closest]->getVideoBitrate());
			$targetFlavorArr[$closest]->_isNonComply = true;
			return true;
		}
		return false;
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
	protected static function validateThumbAndMediaInfo(thumbParams $thumbParams, mediaInfo $mediaInfo = null, &$errDescription, $srcAsset = null)
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
		$extensionTypes = kConf::hasParam('image_file_ext') ? kConf::get('image_file_ext') : array();

		$ext = null;
		if ($srcAsset)
			$ext = $srcAsset->getFileExt();

		if (!is_null($ext) && in_array($ext ,$extensionTypes))
			$thumbParamsOutput->setFileExt($ext);
		else
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

		if($mediaInfo && $mediaInfo->getVideoDuration())
		{
            $videoDurationSec = floor($mediaInfo->getVideoDuration() / 1000);
            if($thumbParamsOutput->getVideoOffset())
            {
				if($thumbParamsOutput->getVideoOffset() > $videoDurationSec)
					$thumbParamsOutput->setVideoOffset($videoDurationSec);
			}

            elseif(!is_null($thumbParams->getVideoOffsetInPercentage()))
            {
                $percentage = $thumbParams->getVideoOffsetInPercentage() / 100;
                $thumbParamsOutput->setVideoOffset(floor($videoDurationSec * $percentage));
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
			kBusinessPostConvertDL::handleConvertFinished($convertProfileJob, $originalFlavorAsset);
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


		$mediaInfo = null;
		if($mediaInfoId)
			$mediaInfo = mediaInfoPeer::retrieveByPK($mediaInfoId);
		
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
		KalturaLog::info("Flavors in conversion profile [" . implode(',', $flavorsIds) . "]");

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
			{
				$originalFlavorAsset->incrementInterFlowCount();
				$originalFlavorAsset->save();
				return false;
			}
			$originalFlavorAsset->removeInterFlowCount();
			$originalFlavorAsset->save();
		}
		elseif($shouldConvert)
		{
			KalturaLog::log("Source flavor params [" . $sourceFlavor->getId() . "] found");
			$originalFlavorAsset->setFlavorParamsId($sourceFlavor->getId());

			$res = self::decideSourceFlavorConvert($entryId, $sourceFlavor, $originalFlavorAsset, $profile->getId(), $flavors, $mediaInfo, $parentJob, $convertProfileJob);
			if(!$res)
			{
				$originalFlavorAsset->incrementInterFlowCount();
				$originalFlavorAsset->save();
				return false;
			}

			$originalFlavorAsset->removeInterFlowCount();
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
				if ($entry->getStatus() != entryStatus::PRECONVERT)
				{
					kJobsManager::updateBatchJob($convertProfileJob, BatchJob::BATCHJOB_STATUS_FINISHED);
				}
				return false;
			}
			else
			{
				self::bypassConversion($originalFlavorAsset, $entry, $convertProfileJob);
				return true;
			}
		}

		try{
			return self::decideProfileFlavorsConvert($parentJob, $convertProfileJob, $flavors, $conversionProfileFlavorParams,  $profile->getId(), $mediaInfo);
		}
		catch(Exception $e){
			$code = $e->getCode();
			if ($code == KDLErrors::SanityInvalidFrameDim || $code == KDLErrors::NoValidMediaStream)
				throw $e;
		}
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

		try{
			return self::decideProfileFlavorsConvert($parentJob, $convertProfileJob, $flavors, $conversionProfileFlavorParams,  $profile->getId(), $mediaInfo);
		}
		catch(Exception $e){
			KalturaLog::err('decideProfileFlavorsConvert - ' . $e->getMessage());
		}
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
		/*
		 * Auto-decrypt
		 */
		if(isset($mediaInfo) && $originalFlavorAsset->getEncryptionKey()){
			KalturaLog::log("Encrypted Source, adding decryption (encryptionKey:".$originalFlavorAsset->getEncryptionKey().")");
			$mediaInfo->decryptionKey = bin2hex(base64_decode($originalFlavorAsset->getEncryptionKey()));
		}

		$errDescription = null;

		try{
			$finalFlavors = self::validateConversionProfile($convertProfileJob->getPartnerId(), $entryId, $mediaInfo, $flavors, $conversionProfileFlavorParams, $errDescription);
		}
		catch(Exception $e){
			$code = $e->getCode();
			if ($code == KDLErrors::SanityInvalidFrameDim || $code == KDLErrors::NoValidMediaStream)
			{
				$convertProfileJob = kJobsManager::failBatchJob($convertProfileJob, $errDescription);
				KalturaLog::err($e->getMessage());
				throw $e;
			}
		}

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

			//Check if the error thrown is invalid file - no media content
			if(strpos($errDescription, KDLErrors::ToString(KDLErrors::NoValidMediaStream)) !== false)
				throw new Exception(KDLErrors::ToString(KDLErrors::NoValidMediaStream), KDLErrors::NoValidMediaStream);
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
				continue;
			}
				/*
				 * Add at rest encryption
				 */
			if($flavor->_isEncrypted==true){
				self::setEncryptionAtRest($flavor, $flavorAsset);
			}
			
				/*
				 * Add label notation
				 */
			if(($multiStreamJson=$flavor->getMultiStream())!=null && ($multiStreamObj=json_decode($multiStreamJson))!=null
			&& KDLAudioMultiStreaming::IsStreamFieldSet($multiStreamObj, "label")){
				$flavorAsset->setLabel($multiStreamObj->audio->streams[0]->label);
				$flavorAsset->save();
			}

			$collectionTag = $flavor->getCollectionTag();
			/*
			 * CHANGE: collection porcessing only for ExpressionEncoder jobs
			 * to allow FFmpeg/ISMV processing
			 */
			KalturaLog::log("Check for collection case - engines(".$flavor->getConversionEngines().")");
			if($collectionTag && $flavor->getConversionEngines()==conversionEngineType::EXPRESSION_ENCODER3)
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
			KalturaLog::info('Changing asset status from Waiting to Queued, affected rows ['.$affectedRows.']');
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
		if($parentJob && $parentJob->getJobType() == BatchJobType::POSTCONVERT)
		{
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
		$c->add(assetPeer::STATUS, array(flavorAsset::ASSET_STATUS_READY, flavorAsset::ASSET_STATUS_NOT_APPLICABLE, flavorAsset::ASSET_STATUS_EXPORTING), Criteria::IN);
		$c->add(assetPeer::FLAVOR_PARAMS_ID, $srcFlavorParamsIds, Criteria::IN);

		$readyAndNonApplicableAssetsCount = assetPeer::doCount($c);

		KalturaLog::info('Verify source flavors are ready: number of ready and NA assets ['.$readyAndNonApplicableAssetsCount.'], number of source params ids ['.count($srcFlavorParamsIds).']');
		if($readyAndNonApplicableAssetsCount < count($srcFlavorParamsIds))
		{
			KalturaLog::info('Not all source flavors are ready, changing status to WAIT_FOR_CONVERT');
			$flavorAsset->setStatus(flavorAsset::ASSET_STATUS_WAIT_FOR_CONVERT);
			$flavorAsset->setDescription("Source flavor assets are not ready");
			$flavorAsset->save();

			return false;
		}

		$srcFlavors = assetPeer::retrieveLocalReadyByEntryIdAndFlavorParams($flavorAsset->getEntryId(), $srcFlavorParamsIds);
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
		if($sourceFlavor && ($sourceFlavor->getOperators() || $sourceFlavor->getConversionEngines()) && $originalFlavorAsset->getInterFlowCount()== null)
		{
			KalturaLog::log("Source flavor asset requires conversion");

			self::adjustAssetParams($entryId, array($sourceFlavor));
			$srcSyncKey = $originalFlavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);

			$flavorParamsConversionProfile = flavorParamsConversionProfilePeer::retrieveByFlavorParamsAndConversionProfile($sourceFlavor->getId(), $conversionProfileId);
			/*
			 * Update flavorParams settings with overloaded params from 'conversionProfileFlavorParams'
			 */
			self::overrideFlavorParamsWithConversionProfileSettings($sourceFlavor, $flavorParamsConversionProfile);
			self::overrideFlavorParamsWithMultistreamData($sourceFlavor, $entryId);

			$errDescription = null;
			$sourceFlavorOutput = self::validateFlavorAndMediaInfo($sourceFlavor, $mediaInfo, $errDescription);
			/*
			 * Source encryption at-rest
			 */
			if(isset($sourceFlavorOutput->_isEncrypted) && $sourceFlavorOutput->_isEncrypted==true){
				self::setEncryptionAtRest($sourceFlavorOutput, $originalFlavorAsset);
			}

			/*
			 * Source auto-decrypt
			 */
			if(isset($mediaInfo) && $originalFlavorAsset->getEncryptionKey()){
				KalturaLog::log("Encrypted Source, adding decryption (encryptionKey:".$originalFlavorAsset->getEncryptionKey().")");
				$mediaInfo->decryptionKey = bin2hex(base64_decode($originalFlavorAsset->getEncryptionKey()));
			}

			/*
			 * Add label notation
			 */
			if(isset($sourceFlavorOutput) && ($multiStreamJson=$sourceFlavorOutput->getMultiStream())!=null && ($multiStreamObj=json_decode($multiStreamJson))!=null
			&& KDLAudioMultiStreaming::IsStreamFieldSet($multiStreamObj, "label")){
				$originalFlavorAsset->setLabel($multiStreamObj->audio->streams[0]->label);
				$originalFlavorAsset->save();
			}

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
			/*
			 * If the conversion profile contains source flavor and the source is tagged with 'save_source' ==>
			 * save the original source asset in another asset, in order
			 * to prevent its liquidated by the inter-source asset.
			 */
			if(isset($sourceFlavor) && array_search(assetParams::TAG_SAVE_SOURCE,$sourceFlavor->getTagsArray())!==false) {
				self::saveOriginalSource($mediaInfo);
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
			if(isset($sourceFlavor)) {
				self::saveOriginalSource($mediaInfo);
			}
		}

			/*
			 * '_passthrough' controls whether the source is to be 'passthrough' although there
			 * is a source flavor that contains transcoder settings.
			 * Looks for a '_passthrough' flag on the source's flavor params output.
			 */
		if(!isset($sourceFlavorOutput) || $sourceFlavorOutput->_passthrough==true)
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
			$tagsArr = $sourceFlavor->getTagsArray();
				// No need for 'save_source' tag on the inter-src asset, remove it.
			if(($key=array_search(assetParams::TAG_SAVE_SOURCE, $tagsArr))!==false) {
				unset($tagsArr[$key]);
			}
			$originalFlavorAsset->addTags($tagsArr);
			$originalFlavorAsset->setFileExt($sourceFlavorOutput->getFileExt());
			$originalFlavorAsset->save();
		}

		// save flavor params
		$sourceFlavorOutput->setFlavorAssetVersion($originalFlavorAsset->getVersion());
		$sourceFlavorOutput->save();

		$mediaInfoId = null;
		if($mediaInfo)
			$mediaInfoId = $mediaInfo->getId();
		kJobsManager::addFlavorConvertJob(array($srcSyncKey), $sourceFlavorOutput, $originalFlavorAsset->getId(), $conversionProfileId, $mediaInfoId, $parentJob);
		return false;
	}

	public static function convertSource($flavorAsset, $conversionProfileId = null, $mediaInfoId = null, $dbParentBatchJob = null)
	{
		$originalFlavorAsset = assetPeer::retrieveOriginalByEntryId($flavorAsset->getEntryId());
		$flavorParamOutput = assetParamsOutputPeer::retrieveByAsset($originalFlavorAsset);
		$srcSyncKeys = array();
		$srcSyncKeys[] = $originalFlavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		return kJobsManager::addFlavorConvertJob($srcSyncKeys, $flavorParamOutput, $originalFlavorAsset->getId(), $conversionProfileId, $mediaInfoId, $dbParentBatchJob);
	}

		/*
		 * Save the original source asset in another asset, in order
		 * to prevent its liquidation by the inter-source asset.
		 */
	private static function saveOriginalSource($mediaInfo)
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
		$origFileSync = kFileSyncUtils::resolve($origFileSync);
		$asset->setSize(intval($origFileSync->getFileSize()/1000));
		$asset->save();
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
			/* @var $flavor assetParams */

			KalturaLog::info("Check flavor [" . $flavor->getId() . "]");
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
				KalturaLog::info("Flavor [" . $flavor->getId() . "] won't be converted because it has source tag");
				continue;
			}

			if($flavor instanceof liveParams)
			{
				unset($flavors[$index]);
				$ingestedNeeded = true;
				KalturaLog::info("Flavor [" . $flavor->getId() . "] won't be converted because it's ingested recorded live");
				continue;
			}

			if($conversionProfileFlavorParamsItem->getOrigin() == assetParamsOrigin::CONVERT)
				continue;

			if($conversionProfileFlavorParamsItem->getOrigin() == assetParamsOrigin::INGEST)
			{
				unset($flavors[$index]);
				$ingestedNeeded = true;
				KalturaLog::info("Flavor [" . $flavor->getId() . "] won't be converted because it should be ingested");
				continue;
			}

			if(in_array($flavor->getId(), $entryIngestedFlavors) &&
				$conversionProfileFlavorParamsItem->getOrigin() == assetParamsOrigin::CONVERT_WHEN_MISSING)
			{
				KalturaLog::info("Flavor [" . $flavor->getId() . "] won't be converted because it already ingested");
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

	/**
	 *
	 * @param string $entryId
	 * @param array<assetParams> $flavors
	 */
	protected static function adjustAssetParams($entryId, array $flavors)
	{
		$assetParamsAdjusters = KalturaPluginManager::getPluginInstances('IKalturaAssetParamsAdjuster');
		foreach($assetParamsAdjusters as $assetParamsAdjuster)
		{
			/* @var $assetParamsAdjuster IKalturaAssetParamsAdjuster */
			$assetParamsAdjuster->adjustAssetParams($entryId, $flavors);
		}
	}

	/**
	 *
	 * @param flavorParamsOutput $flavor
	 * @param flavorAsset $flavorAsset
	 */
	protected static function setEncryptionAtRest($flavor, $flavorAsset)
	{
		KalturaLog::log("for asset".$flavorAsset->getId());
		$encryptionParams = self::acquireEncryptionParams($flavorAsset->getEntryId(), $flavorAsset->getId(), $flavorAsset->getPartnerId());
		if(($commandLines=$flavor->getCommandLines())!=null) {
				// Update the transcoding engines cmd-lines with encryption key/key_id values
			KalturaLog::log("CommandLines Pre:".serialize($commandLines));
			$commandLines = str_replace (
				array(KDLFlavor::ENCRYPTION_KEY_PLACEHOLDER, KDLFlavor::ENCRYPTION_KEY_ID_PLACEHOLDER),
				array(bin2hex(base64_decode($encryptionParams->key)), bin2hex(base64_decode($encryptionParams->key_id))),
				$commandLines);
				// Save updated cmd-lines
			$flavor->setCommandLines($commandLines);
			$flavor->save();
		}
		else if(($operatorsJsonStr=$flavor->getOperators())!=null){
				// Update the transcoding operators cmd-lines with encryption key/key_id values
			KalturaLog::log("Operators Pre:".($operatorsJsonStr));
			$operatorsJsonStr = str_replace (
				array(KDLFlavor::ENCRYPTION_KEY_PLACEHOLDER, KDLFlavor::ENCRYPTION_KEY_ID_PLACEHOLDER),
				array(bin2hex(base64_decode($encryptionParams->key)), bin2hex(base64_decode($encryptionParams->key_id))),
				$operatorsJsonStr);
				// Save updated cmd-lines
			$flavor->setOperators($operatorsJsonStr);
			$flavor->save();
		}
			// Save encryption key on the flavorAsset obj
		$flavorAsset->setEncryptionKey($encryptionParams->key);
		$flavorAsset->save();
	}

	/**
	 * Get the encryption key and key_id from the udrm service.
	 * @param string $entryId
	 * @param string $assetId
	 * @param string $partnerId
	 */
	protected static function acquireEncryptionParams($entryId, $assetId, $partnerId)
	{
			/*
			 * UDRM 'signing_key' and 'internal_encryption_url' should be stored in the drm.ini
			 * If not exist - exception
			 */
		$licenseServerUrl = kConf::get('internal_encryption_url', 'drm', null);
		if(!(isset($licenseServerUrl))) {
			$errMsg = "Encryption: Missing 'internal_encryption_url' ";
			KalturaLog::err($errMsg);
			throw new kCoreException($errMsg , KDLErrors::Encryption);
		}

		$signingKeys  = kConf::get('partner_signing_key', 'drm', array());
		if(isset($signingKeys[$partnerId]))
			$signingKey = $signingKeys[$partnerId];
		else
			$signingKey = kConf::get('signing_key', 'drm', null);

		if(!(isset($signingKey))) {
			$errMsg = "Encryption: Missing 'signing_key' ";
			KalturaLog::err($errMsg);
			throw new kCoreException($errMsg , KDLErrors::Encryption);
		}
		KalturaLog::log("Successfully retrieved UDRM 'internal_encryption_url' and 'signing_key' vals ($signingKeys)");

			/*
			 * Prepare data for the UDRM service curl call
			 */
		$requestInfo["ca_system"] = "ovp";
		$requestInfo["account_id"] = $partnerId;
		$requestInfo["content_id"] = $entryId;
		$requestInfo["files"] = $assetId;

		$jsonPostData = json_encode($requestInfo);
		$signature = urlencode(base64_encode(sha1($signingKey . $jsonPostData, true)));
		$serviceURL = $licenseServerUrl.'/cenc/encryption?signature=' . $signature;

		$retryCount = 3;
		while ($retryCount--) {
			$ch = curl_init($serviceURL);
			curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-type: application/json')	);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPostData);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			KalturaLog::log("calling UDRM service - serviceURL($serviceURL), data ($jsonPostData)");

			$output = curl_exec($ch);
			if ($output === false){
				$errMsg = "Encryption: Could not get UDRM Data,error message 'Curl had an error '".curl_error($ch)."'";
				KalturaLog::err($errMsg);
				throw new kCoreException($errMsg , KDLErrors::Encryption);
			}
			$retVal = json_decode($output);
			if (is_array($retVal) && isset($retVal[0]->key_id))
				return $retVal[0];
		}

		$errMsg = "Encryption: Did got invalid result from udrm service, output ($output)";
		KalturaLog::err($errMsg);
		throw new kCoreException($errMsg , KDLErrors::Encryption);
	}

	/**
	 *
	 * @param unknown_type $flavorParams
	 * @param unknown_type $flavorParamsConversionProfile
	 */
	private static function overrideFlavorParamsWithConversionProfileSettings($flavorParams, $flavorParamsConversionProfile)
	{
		if($flavorParamsConversionProfile){
			/*
			 * Update flavorParams settings with overloaded params from 'conversionProfileFlavorParams'
			 */
			$overrideParam = $flavorParamsConversionProfile->getIsEncrypted();
			if(isset($overrideParam))
				$flavorParams->setIsEncrypted($overrideParam);
			$overrideParam = $flavorParamsConversionProfile->getContentAwareness();
			if(isset($overrideParam))
				$flavorParams->setContentAwareness($overrideParam);
			$overrideParam = $flavorParamsConversionProfile->getTwoPass();
			if(isset($overrideParam))
				$flavorParams->setTwoPass($overrideParam);
			if($flavorParamsConversionProfile->getTags()!==null) $flavorParams->setTags($flavorParamsConversionProfile->getTags());
		}
	}

	/**
	 *
	 * @param unknown_type $flavorParams
	 * @param int $entryId
	 */
	private static function overrideFlavorParamsWithMultistreamData($flavorParams, $entryId)
	{
		KalturaLog::log("entry($entryId)");
		$entry = entryPeer::retrieveByPK($entryId);
		if(!isset($entry))
			return;
		
		$overrideSourceStreams = $entry->getStreams();	
		if(!isset($overrideSourceStreams)){
			KalturaLog::log("Nothing to override");
			return;
		}
		
		$overrides = array();
		KalturaLog::log("entry($entryId) - streams - ".print_r($overrideSourceStreams,1));
		foreach($overrideSourceStreams as $obj){
			$override = new stdClass();
			if($obj->getType()!='audio')
				continue;
			if($obj->getTrackIndex()!==null) 	$override->id = $obj->getTrackIndex();
			else continue;
			if($obj->getChannelLayout()!==null) 	$override->audioChannelLayout = $obj->getChannelLayout();
			if($obj->getChannelIndex()!==null) 	$override->audioChannelIndex = $obj->getChannelIndex();
			if($obj->getLanguage()!==null) 		$override->audioLanguage = $obj->getLanguage();
			if($obj->getLabel()!==null) 		$override->audioLabel = $obj->getLabel();
			$overrides[] = $override;
		}

		if(count($overrides)==0){
			return;
		}
		
		$returnOverideObj = new stdClass();
		$returnOverideObj->audio = $overrides;
		
		$jsonMultiStream = $flavorParams->getMultiStream();
		KalturaLog::log("entry($entryId) - json - original($jsonMultiStream)");
		if(isset($jsonMultiStream)){
			$multiStreamObj = json_decode($jsonMultiStream);
			if(!isset($multiStreamObj)) {
				$multiStreamObj = new stdClass();
			}
		}
		else {
			$multiStreamObj = new stdClass();
		}
		$multiStreamObj->source = $returnOverideObj;
		$jsonMultiStream = json_encode($multiStreamObj);
		KalturaLog::log("entry($entryId) - json - updated($jsonMultiStream)");
		$flavorParams->setMultiStream($jsonMultiStream);
	}
	
	/*
	 *
	 */
	public static function checkConditionalProfiles($entry, $mediaInfo)
	{
		if($entry->getSourceType() == EntrySourceType::LECTURE_CAPTURE) 
		{
			$profile = conversionProfile2Peer::retrieveByPartnerIdAndSystemName($entry->getPartnerId(), self::$conditionalMapBySourceType[EntrySourceType::LECTURE_CAPTURE], ConversionProfileType::MEDIA);
		}
		else 
		{
			$profile = myPartnerUtils::getConversionProfile2ForEntry($entry->getId());
		}
		
		if(!$profile) 
		{
			KalturaLog::log("No profile for entry(".($entry->getId())."), cannot check profile conditions ");
			return;
		}
		
		$jsonStr = $profile->getConditionalProfiles();
		if(!isset($jsonStr))
		{
			KalturaLog::log("No conditionalProfiles for profile(".($profile->getId())."), entry(".($entry->getId()).")");
			return;
		}
		
		KalturaLog::log("Conditional profiles:$jsonStr");
		KalturaLog::log("mediaInfo::maxGOP(".$mediaInfo->getMaxGOP().")");
		
		$medSet = new KDLMediaDataSet();
		KDLWrap::ConvertMediainfoCdl2Mediadataset($mediaInfo,$medSet);
		KalturaLog::log("medSet::GOP(".$medSet->_video->_gop.")");
		$conditionalProfiles = json_decode($jsonStr);
		
		foreach($conditionalProfiles as $conditionalProfile)
		{
			KalturaLog::log("Checking condition($conditionalProfile->condition)");
			$rv = $medSet->IsCondition($conditionalProfile->condition);
			if($rv==true) 
			{
				if(isset($conditionalProfile->profileId))
					$profId = $conditionalProfile->profileId;
				else
					$profId = $profile->getId();
				
				$entry->setConversionProfileId($profId);
				$entry->setConversionQuality($profId);
				$entry->save();
				KalturaLog::log("Condition is met! Switching to profile($profId)");
				return;
			}
		}
		
		KalturaLog::log("None of the conditions are met.");
		return;
	}
}
