<?php

/**
 *
 * @package Core
 * @subpackage Batch
 *
 */
class kFlowHelper
{
	protected static $thumbUnSupportVideoCodecs = array(
		flavorParams::VIDEO_CODEC_VP8,
	);
	const MAX_INTER_FLOW_ITERATIONS_ALLOWED_ON_SOURCE = 2;
	
	const BULK_DOWNLOAD_EMAIL_PARAMS_SEPARATOR = '|,|';

	const LIVE_REPORT_EXPIRY_TIME = 604800; // 7 * 60 * 60 * 24
	/**
	 * @param int $partnerId
	 * @param string $entryId
	 * @param string $msg
	 * @return flavorAsset
	 */
	public static function createOriginalFlavorAsset($partnerId, $entryId, &$msg = null)
	{
		$flavorAsset = assetPeer::retrieveOriginalByEntryId($entryId);
		if($flavorAsset)
			return $flavorAsset;

		$entry = entryPeer::retrieveByPK($entryId);
		if(!$entry)
		{
			KalturaLog::err("Entry [$entryId] not found");
			return null;
		}

		// creates the flavor asset
		$flavorAsset = flavorAsset::getInstance();
		$flavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_QUEUED);
		$flavorAsset->incrementVersion();
		$flavorAsset->addTags(array(flavorParams::TAG_SOURCE));
		$flavorAsset->setIsOriginal(true);
		$flavorAsset->setFlavorParamsId(flavorParams::SOURCE_FLAVOR_ID);
		$flavorAsset->setPartnerId($partnerId);
		$flavorAsset->setEntryId($entryId);
		$flavorAsset->save();

		return $flavorAsset;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kImportJobData $data
	 * @return BatchJob
	 */
	public static function handleImportFailed(BatchJob $dbBatchJob, kImportJobData $data)
	{
		kBatchManager::updateEntry($dbBatchJob->getEntryId(), entryStatus::ERROR_IMPORTING);

		if($data->getFlavorAssetId())
		{
			$flavorAsset = assetPeer::retrieveById($data->getFlavorAssetId());
			if($flavorAsset && !$flavorAsset->isLocalReadyStatus())
			{
				$flavorAsset->setDescription($dbBatchJob->getMessage());
				$flavorAsset->setStatus(asset::ASSET_STATUS_ERROR);
				$flavorAsset->save();
			}
		}
		return $dbBatchJob;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kImportJobData $data
	 * @return BatchJob
	 */
	public static function handleImportRetried(BatchJob $dbBatchJob, kImportJobData $data)
	{
		if($dbBatchJob->getExecutionStatus() == BatchJobExecutionStatus::ABORTED)
			return $dbBatchJob;

		if(!$data->getFlavorAssetId())
			return $dbBatchJob;

		$dbFlavorAsset = assetPeer::retrieveById($data->getFlavorAssetId());
		
		if(!$dbFlavorAsset)
			return $dbBatchJob;
			
		if($dbFlavorAsset->getStatus() == asset::FLAVOR_ASSET_STATUS_ERROR)
		{
			$dbFlavorAsset->setStatus(asset::FLAVOR_ASSET_STATUS_IMPORTING);
			$dbFlavorAsset->save();
		}

		$dbEntry = $dbFlavorAsset->getentry();
		if($dbEntry->getStatus() == entryStatus::ERROR_IMPORTING)
		{
			$dbEntry->setStatus(entryStatus::IMPORT);
			$dbEntry->save();
		}

		return $dbBatchJob;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kImportJobData $data
	 * @return BatchJob
	 */
	public static function handleImportFinished(BatchJob $dbBatchJob, kImportJobData $data)
	{
		if($dbBatchJob->getExecutionStatus() == BatchJobExecutionStatus::ABORTED)
			return $dbBatchJob;

		if(!file_exists($data->getDestFileLocalPath()))
			throw new APIException(APIErrors::INVALID_FILE_NAME, $data->getDestFileLocalPath());

		// get entry
		$entryId = $dbBatchJob->getEntryId();
		$dbEntry = entryPeer::retrieveByPKNoFilter($entryId);

		// IMAGE media entries
		if ($dbEntry->getType() == entryType::MEDIA_CLIP && $dbEntry->getMediaType() == entry::ENTRY_MEDIA_TYPE_IMAGE)
		{
			$url = $data->getSrcFileUrl();
			$ext = pathinfo($url, PATHINFO_EXTENSION);
			$allowedImageTypes = kConf::get("image_file_ext");
			//setting the entry's data so it can be used for creating file-syncs' file-path version & extension - in kFileSyncUtils::moveFromFile
			//without saving - the updated entry object exists in the instance pool
			$dbEntry->setData(".jpg");
			if (in_array($ext, $allowedImageTypes))
				$dbEntry->setData("." . $ext);
			else				
				$dbEntry->setData(".jpg");
			
			
			$syncKey = $dbEntry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA);

			try
			{
				kFileSyncUtils::moveFromFile($data->getDestFileLocalPath(), $syncKey, true, false, $data->getCacheOnly());
			}
			catch (Exception $e) {
				if($dbEntry->getStatus() == entryStatus::NO_CONTENT)
				{
					$dbEntry->setStatus(entryStatus::ERROR_CONVERTING);
					$dbEntry->save();
				}
				throw $e;
			}
			$dbEntry->setStatus(entryStatus::READY);
			$dbEntry->save();
			return $dbBatchJob;
		}

		$flavorAsset = null;
		if($data->getFlavorAssetId())
			$flavorAsset = assetPeer::retrieveById($data->getFlavorAssetId());

		$isNewFlavor = false;
		if(!$flavorAsset)
		{
			$msg = null;
			$flavorAsset = kFlowHelper::createOriginalFlavorAsset($dbBatchJob->getPartnerId(), $dbBatchJob->getEntryId(), $msg);
			if(!$flavorAsset)
			{
				KalturaLog::err("Flavor asset not created for entry [" . $dbBatchJob->getEntryId() . "]");
				kBatchManager::updateEntry($dbBatchJob->getEntryId(), entryStatus::ERROR_CONVERTING);
				$dbBatchJob->setMessage($msg);
				$dbBatchJob->setDescription($dbBatchJob->getDescription() . "\n" . $msg);
				return $dbBatchJob;
			}
			$isNewFlavor = true;
		}

		$isNewContent = true;
		$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		if(kFileSyncUtils::fileSync_exists($syncKey))
			$isNewContent = false;

		$ext = pathinfo($data->getDestFileLocalPath(), PATHINFO_EXTENSION);
		KalturaLog::info("Imported file extension: $ext");
		if(!$flavorAsset->getVersion())
			$flavorAsset->incrementVersion();

		if($ext)
			$flavorAsset->setFileExt($ext);
			
		if($flavorAsset instanceof thumbAsset)
		{
			list($width, $height, $type, $attr) = getimagesize($data->getDestFileLocalPath());
			
			$flavorAsset->setWidth($width);
			$flavorAsset->setHeight($height);
			$flavorAsset->setSize(filesize($data->getDestFileLocalPath()));
		}
		$flavorAsset->save();
		
		$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		kFileSyncUtils::moveFromFile($data->getDestFileLocalPath(), $syncKey, true, false, $data->getCacheOnly());


		// set the path in the job data
		$localFilePath = kFileSyncUtils::getLocalFilePathForKey($syncKey);
		$data->setDestFileLocalPath($localFilePath);
		$data->setFlavorAssetId($flavorAsset->getId());
		$dbBatchJob->setData($data);
		$dbBatchJob->save();

		$convertProfileExist = self::activateConvertProfileJob($dbBatchJob->getEntryId(), $localFilePath);

		if (($isNewContent || $dbEntry->getStatus() == entryStatus::IMPORT) && !$convertProfileExist)
			// check if status == import for importing file of type url (filesync exists, and we want to raise event for conversion profile to start)
			kEventsManager::raiseEvent(new kObjectAddedEvent($flavorAsset, $dbBatchJob));

		
		if(!$isNewFlavor)
		{
			$entryFlavors = assetPeer::retrieveByEntryIdAndStatus($flavorAsset->getEntryId(), flavorAsset::FLAVOR_ASSET_STATUS_WAIT_FOR_CONVERT);
			$originalFlavorAsset = assetPeer::retrieveOriginalByEntryId($flavorAsset->getEntryId());
			foreach($entryFlavors as $entryFlavor)
			{
				/* @var $entryFlavor flavorAsset */
				if($entryFlavor->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_WAIT_FOR_CONVERT && $entryFlavor->getFlavorParamsId())
				{
					$flavor = assetParamsOutputPeer::retrieveByAsset($entryFlavor);
					kBusinessPreConvertDL::decideFlavorConvert($entryFlavor, $flavor, $originalFlavorAsset, null, null, $dbBatchJob);
				}
			}

			$entryThumbnails = assetPeer::retrieveThumbnailsByEntryId($flavorAsset->getEntryId());
			foreach($entryThumbnails as $entryThumbnail)
			{
				/* @var $entryThumbnail thumbAsset */
				if($entryThumbnail->getStatus() != asset::ASSET_STATUS_WAIT_FOR_CONVERT || !$entryThumbnail->getFlavorParamsId())
					continue;

				$thumbParamsOutput = assetParamsOutputPeer::retrieveByAssetId($entryThumbnail->getId());
				/* @var $thumbParamsOutput thumbParamsOutput */
				if($thumbParamsOutput->getSourceParamsId() != $flavorAsset->getFlavorParamsId())
					continue;

				$srcSyncKey = $flavorAsset->getSyncKey(asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
				$srcAssetType = $flavorAsset->getType();
				kJobsManager::addCapturaThumbJob($entryThumbnail->getPartnerId(), $entryThumbnail->getEntryId(), $entryThumbnail->getId(), $srcSyncKey, $flavorAsset->getId(), $srcAssetType, $thumbParamsOutput);
			}
		}
		
		return $dbBatchJob;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kConvertLiveSegmentJobData $data
	 * @return BatchJob
	 */
	public static function handleConvertLiveSegmentFinished(BatchJob $dbBatchJob, kConvertLiveSegmentJobData $data)
	{
		$liveEntry = entryPeer::retrieveByPKNoFilter($dbBatchJob->getEntryId());
		/* @var $liveEntry LiveEntry */
		if(!$liveEntry)
		{
			KalturaLog::err("Live entry [" . $dbBatchJob->getEntryId() . "] not found");
			return $dbBatchJob;
		}
		
		$recordedEntry = entryPeer::retrieveByPKNoFilter($liveEntry->getRecordedEntryId());
		if(!$recordedEntry)
		{
			KalturaLog::err("Recorded entry [" . $liveEntry->getRecordedEntryId() . "] not found");
			return $dbBatchJob;
		}

		$asset = assetPeer::retrieveByIdNoFilter($data->getAssetId());
		/* @var $asset liveAsset */
		if(!$asset)
		{
			KalturaLog::err("Live asset [" . $data->getAssetId() . "] not found");
			return $dbBatchJob;
		}
		
		$keyType = liveAsset::FILE_SYNC_ASSET_SUB_TYPE_LIVE_PRIMARY;
		if($data->getMediaServerIndex() == EntryServerNodeType::LIVE_BACKUP)
			$keyType = liveAsset::FILE_SYNC_ASSET_SUB_TYPE_LIVE_SECONDARY;
			
		$key = $asset->getSyncKey($keyType);
		$baseName = $asset->getEntryId() . '_' . $asset->getId() . '.ts';
		kFileSyncUtils::moveFromFileToDirectory($key, $data->getDestFilePath(), $baseName);
		
		if($data->getMediaServerIndex() == EntryServerNodeType::LIVE_BACKUP)
			return $dbBatchJob;
			
		$files = kFileSyncUtils::dir_get_files($key, false);

		if (self::hasFileDiscontinuity($files)) {
			KalturaLog::warning('we have a discontinuity with ts files - not running the concat job for entry [ ' . $dbBatchJob->getEntryId() . ']' );
			return $dbBatchJob;
		}

		if(count($files) > 1)
		{
			$lockKey = "create_replacing_entry_" . $recordedEntry->getId();
			$replacingEntry = kLock::runLocked($lockKey, array('kFlowHelper', 'getReplacingEntry'), array($recordedEntry, $asset, count($files)));
			if(!$replacingEntry)
			{
				KalturaLog::err('Failed to allocate replacing entry');
				kJobsManager::updateBatchJob($dbBatchJob, BatchJob::BATCHJOB_STATUS_FAILED);
				return $dbBatchJob;
			}

			$flavorParams = assetParamsPeer::retrieveByPKNoFilter($asset->getFlavorParamsId());
			if(is_null($flavorParams)) { 
				KalturaLog::err('Failed to retrieve asset params');
				return $dbBatchJob;
			}
		
			// create asset
			$replacingAsset = assetPeer::getNewAsset(assetType::FLAVOR);
			$replacingAsset->setPartnerId($replacingEntry->getPartnerId());
			$replacingAsset->setEntryId($replacingEntry->getId());
			$replacingAsset->setStatus(asset::FLAVOR_ASSET_STATUS_QUEUED);
			$replacingAsset->setFlavorParamsId($flavorParams->getId());
			$replacingAsset->setFromAssetParams($flavorParams);
			
			if($flavorParams->hasTag(assetParams::TAG_SOURCE))
			{
				$replacingAsset->setIsOriginal(true);
			}		
			$replacingAsset->save();
			
			$job = kJobsManager::addConcatJob($dbBatchJob, $replacingAsset, $files);
		}

		return $dbBatchJob;
	}

	// get the indexes of all files on disk (form file names)
	// if we have all from 0 to count($files) - return true
	// otherwise return false;
	private static function hasFileDiscontinuity($files)
	{
		$filesArr = array();

		foreach($files as $file){
			$filesArr[intval(self::getFileNumber($file))] = true;
		}

		for ($i = 0 ; $i < count($files); $i++) {
			if (!isset($filesArr[$i])) {
				KalturaLog::info("got ts file discontinuity for " . $i);
				return true;
			}
		}

		return false;
	}

	private static function getFileNumber($file)
	{
		$lastSlash = strrpos($file, '/');
		$firstDotAfterSlash = strpos($file, '.', $lastSlash);
		$fileIndex = substr($file, $lastSlash+1, $firstDotAfterSlash - $lastSlash-1);
		return $fileIndex;
	}

	private static function createReplacigEntry($recordedEntry, $liveSegmentCount)
	{
		$advancedOptions = new kEntryReplacementOptions();
		$advancedOptions->setKeepManualThumbnails(true);
		$advancedOptions->setKeepOldAssets(true);
		$recordedEntry->setReplacementOptions($advancedOptions);

		$replacingEntry = new entry();
		$replacingEntry->setType(entryType::MEDIA_CLIP);
		$replacingEntry->setMediaType(entry::ENTRY_MEDIA_TYPE_VIDEO);
		$replacingEntry->setSourceType($recordedEntry->getSourceType());
		$replacingEntry->setConversionProfileId($recordedEntry->getConversionProfileId());
		$replacingEntry->setName($recordedEntry->getPartnerId().'_'.time());
		$replacingEntry->setKuserId($recordedEntry->getKuserId());
		$replacingEntry->setAccessControlId($recordedEntry->getAccessControlId());
		$replacingEntry->setPartnerId($recordedEntry->getPartnerId());
		$replacingEntry->setSubpId($recordedEntry->getPartnerId() * 100);
		$replacingEntry->setDefaultModerationStatus();
		$replacingEntry->setDisplayInSearch(mySearchUtils::DISPLAY_IN_SEARCH_SYSTEM);
		$replacingEntry->setReplacedEntryId($recordedEntry->getId());
		$replacingEntry->setRecordedEntrySegmentCount($liveSegmentCount);
		$replacingEntry->save();

		$recordedEntry->setReplacingEntryId($replacingEntry->getId());
		$recordedEntry->setReplacementStatus(entryReplacementStatus::APPROVED_BUT_NOT_READY);
		$affectedRows = $recordedEntry->save();
		return $replacingEntry;
	}

	public static function getReplacingEntry($recordedEntry, $asset = null, $liveSegmentCount, $flavorParamsId = null)
	{
		//Reload entry before tryign to get the replacing entry id from it to avoid creating 2 different replacing entries for different flavors
		$recordedEntry->reload();
		$replacingEntryId = $recordedEntry->getReplacingEntryId();
		$replacingEntry = null;
		if(!is_null($replacingEntryId))
		{
				$replacingEntry = entryPeer::retrieveByPKNoFilter($replacingEntryId);
				if ($replacingEntry)
				{
					/* @var $replacingEntry entry */
					$recordedEntrySegmentCount = $replacingEntry->getRecordedEntrySegmentCount();
					if($recordedEntrySegmentCount > $liveSegmentCount)
					{
						KalturaLog::debug("Entry [{$recordedEntry->getId()}] in replacment with higher segment count [$recordedEntrySegmentCount] > [$liveSegmentCount]");
						return null;
					}
					else 
					{
						$flavorParamsId = $asset ? $asset->getFlavorParamsId() : $flavorParamsId;
						$replacingAsset = assetPeer::retrieveByEntryIdAndParams($replacingEntryId, $flavorParamsId);
						if($replacingAsset)
						{
							KalturaLog::debug("Entry in replacement, deleting - [".$replacingEntryId."]");
							myEntryUtils::deleteReplacingEntry($recordedEntry, $replacingEntry);
							$replacingEntry = null;
						}
					}
				}
		}
		
		if(is_null($replacingEntry))
		{
			$replacingEntry = self::createReplacigEntry($recordedEntry, $liveSegmentCount);
		}
		return $replacingEntry;
	}	

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kConvertLiveSegmentJobData $data
	 * @return BatchJob
	 */
	public static function handleConvertLiveSegmentFailed(BatchJob $dbBatchJob, kConvertLiveSegmentJobData $data)
	{
		$entry = entryPeer::retrieveByPKNoFilter($dbBatchJob->getEntryId());
		/* @var $entry LiveEntry */
		
		if(!$entry->isConvertingSegments())
		{
			$attachedPendingMediaEntries = $entry->getAttachedPendingMediaEntries();
			foreach($attachedPendingMediaEntries as $attachedPendingMediaEntry)
			{
				/* @var $attachedPendingMediaEntry kPendingMediaEntry */
				if($attachedPendingMediaEntry->getDc() != kDataCenterMgr::getCurrentDcId())
					continue;
				
				kBatchManager::updateEntry($attachedPendingMediaEntry->getEntryId(), entryStatus::ERROR_CONVERTING);
				$entry->dettachPendingMediaEntry($attachedPendingMediaEntry->getEntryId());
			}
		}
		$entry->save();
		
		return $dbBatchJob;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kConcatJobData $data
	 * @return BatchJob
	 */
	public static function handleConcatFailed(BatchJob $dbBatchJob, kConcatJobData $data)
	{
		if($dbBatchJob->getExecutionStatus() == BatchJobExecutionStatus::ABORTED)
			return $dbBatchJob;
		
		$flavorAsset = assetPeer::retrieveByIdNoFilter($data->getFlavorAssetId());
		if(!$flavorAsset)
			throw new APIException(APIErrors::INVALID_FLAVOR_ASSET_ID, $data->getFlavorAssetId());
			
		if($flavorAsset->getStatus() == asset::ASSET_STATUS_DELETED)
			return $dbBatchJob;
			
		kBatchManager::updateEntry($dbBatchJob->getEntryId(), entryStatus::ERROR_CONVERTING);

		if(!$flavorAsset->isLocalReadyStatus())
		{
			$flavorAsset->setDescription($dbBatchJob->getMessage());
			$flavorAsset->setStatus(asset::ASSET_STATUS_ERROR);
			$flavorAsset->save();
		}
		
		return $dbBatchJob;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kConcatJobData $data
	 * @return BatchJob
	 */
	public static function handleConcatFinished(BatchJob $dbBatchJob, kConcatJobData $data)
	{
		if($dbBatchJob->getExecutionStatus() == BatchJobExecutionStatus::ABORTED)
			return $dbBatchJob;

		if(!file_exists($data->getDestFilePath()))
			throw new APIException(APIErrors::INVALID_FILE_NAME, $data->getDestFilePath());

		$flavorAsset = assetPeer::retrieveByIdNoFilter($data->getFlavorAssetId());
		if(!$flavorAsset)
			throw new APIException(APIErrors::INVALID_FLAVOR_ASSET_ID, $data->getFlavorAssetId());
			
		if($flavorAsset->getStatus() == asset::ASSET_STATUS_DELETED)
			return $dbBatchJob;
			
		$flavorAsset->incrementVersion();
		
		$ext = pathinfo($data->getDestFilePath(), PATHINFO_EXTENSION);
		if($ext)
			$flavorAsset->setFileExt($ext);
			
		$flavorAsset->save();

		$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		kFileSyncUtils::moveFromFile($data->getDestFilePath(), $syncKey);

		kEventsManager::raiseEvent(new kObjectAddedEvent($flavorAsset, $dbBatchJob));

		return $dbBatchJob;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kExtractMediaJobData $data
	 * @return BatchJob
	 */
	public static function handleExtractMediaClosed(BatchJob $dbBatchJob, kExtractMediaJobData $data)
	{
		if($dbBatchJob->getExecutionStatus() == BatchJobExecutionStatus::ABORTED)
			return $dbBatchJob;

		$rootBatchJob = $dbBatchJob->getRootJob();
		if(!$rootBatchJob)
			return $dbBatchJob;

			/*
			 * Fix web-cam sources with bad timestamps
			 */
		$dbBatchJobAux=self::fixWebCamSources($rootBatchJob, $dbBatchJob, $data);
		if($dbBatchJobAux!=null)
			return $dbBatchJobAux;

		if($dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FINISHED)
		{
			$entry = entryPeer::retrieveByPKNoFilter($dbBatchJob->getEntryId());
			if($entry->getStatus() != entryStatus::READY && $entry->getStatus() != entryStatus::DELETED)
				kBatchManager::updateEntry($dbBatchJob->getEntryId(), entryStatus::PRECONVERT);
		}

		if($rootBatchJob->getJobType() == BatchJobType::CONVERT_PROFILE)
		{
			try {
				kBusinessPreConvertDL::decideProfileConvert($dbBatchJob, $rootBatchJob, $data->getMediaInfoId());
			}
			catch (Exception $ex) {
			$code = $ex->getCode();

			if ($code == KDLErrors::SanityInvalidFrameDim || $code == KDLErrors::NoValidMediaStream)
			{
				kBusinessPostConvertDL::handleConvertFailed($dbBatchJob , null , $data->getFlavorAssetId() , null , null);
				return $dbBatchJob;
			}	
	
			//This was added so the all the assets prior to reaching the limit would still be created
			if ($code != kCoreException::MAX_ASSETS_PER_ENTRY)
				throw $ex;

				KalturaLog::err("Max assets per entry was reached continuing with normal flow");
			}			

			// handle the source flavor as if it was converted, makes the entry ready according to ready behavior rules
			$currentFlavorAsset = assetPeer::retrieveById($data->getFlavorAssetId());
			if($currentFlavorAsset && $currentFlavorAsset->getStatus() == asset::FLAVOR_ASSET_STATUS_READY)
				$dbBatchJob = kBusinessPostConvertDL::handleConvertFinished($dbBatchJob, $currentFlavorAsset);
		}

		return $dbBatchJob;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kExtractMediaJobData $data
	 * @return BatchJob/null
	 */
	protected static function fixWebCamSources(BatchJob &$rootBatchJob, BatchJob &$dbBatchJob, kExtractMediaJobData $data)
	{
		$mediaInfo = mediaInfoPeer::retrieveById($data->getMediaInfoId());
		
			/*
			 * Check validity of web-cam sources, for:
			 * - h263/sorenson video
			 * - nellymoser audio
			 * - and for following params:
			 * -- Duration>100hrs (KDLSanityLimits::MaxDuration) or 
			 * -- Bitrate<10Kbps (KDLSanityLimits::MinBitrate)
			 * then run the webcam-flv-fix procedure
			 */
		$webCamVideoCodecs = array("h.263","h263","sorenson spark","vp6");
		if(isset($mediaInfo)
		&& (in_array($mediaInfo->getVideoFormat(),$webCamVideoCodecs)
		 || in_array($mediaInfo->getVideoCodecId(),$webCamVideoCodecs))
		&& (in_array($mediaInfo->getAudioFormat(),array("nellymoser"))
		 || in_array($mediaInfo->getAudioCodecId(),array("nellymoser"))) ){
			if($mediaInfo->getVideoDuration()>0)
				$durToTest = $mediaInfo->getVideoDuration();
			else if($mediaInfo->getAudioDuration()>0)
				$durToTest = $mediaInfo->getAudioDuration();
			else if($mediaInfo->getContainerDuration()>0)
				$durToTest = $mediaInfo->getContainerDuration();
			else 
				$durToTest = 0;

			if($durToTest>0)
				$calcBrToTest = $mediaInfo->getFileSize()*8000/$durToTest;
			else
				$calcBrToTest = 0;
				
			if($mediaInfo->getVideoBitRate()>0)
				$brToTest = $mediaInfo->getVideoBitRate();
			else if($mediaInfo->getContainerBitRate()>0)
				$brToTest = $mediaInfo->getContainerBitRate();
			else
				$brToTest = $calcBrToTest;
			
			KalturaLog::log("durToTest($durToTest),brToTest($brToTest),calcBrToTest($calcBrToTest)");
			if(($durToTest>KDLSanityLimits::MaxDuration	// 360000000
			 ||($calcBrToTest>0 && $calcBrToTest<KDLSanityLimits::MinBitrate) )
			 ||($brToTest>0 && $brToTest<KDLSanityLimits::MinBitrate)) {
				KalturaLog::err("invalid source, should be fixed");
				$flavorAsset = assetPeer::retrieveById($data->getFlavorAssetId());
				if($flavorAsset && $flavorAsset->getVersion()<40){
					$flavorAsset->incrementVersion();
					$flavorAsset->save();
					$fixedFileName = $data->getSrcFileSyncLocalPath().".fixed";
					myFlvHandler::fixFlvTimestamps($data->getSrcFileSyncLocalPath(),$fixedFileName);
					$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
					kFileSyncUtils::moveFromFile($fixedFileName, $syncKey);
					$syncPath=kFileSyncUtils::getLocalFilePathForKey($syncKey);
						/*
						 * Finish the current extract medi job and start a new one
						 */
					kJobsManager::updateBatchJob($dbBatchJob, BatchJob::BATCHJOB_STATUS_FINISHED);
					kJobsManager::addExtractMediaJob($rootBatchJob, $syncPath, $data->getFlavorAssetId());
					return $dbBatchJob;
				}
			}
		}
		return null;
	}
	
	/**
	 * @param BatchJob $dbBatchJob
	 * @param kConvertJobData $data
	 * @return BatchJob
	 */
	public static function handleConvertPending(BatchJob $dbBatchJob, kConvertJobData $data)
	{
		// save the data to the db
		$dbBatchJob->setData($data);
		$dbBatchJob->save();


		$flavorAsset = assetPeer::retrieveById($data->getFlavorAssetId());
		// verifies that flavor asset exists
		if(!$flavorAsset)
		{
			KalturaLog::err("Error: Flavor asset not found [" . $data->getFlavorAssetId() . "]");
			throw new APIException(APIErrors::INVALID_FLAVOR_ASSET_ID, $data->getFlavorAssetId());
		}

		$flavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_CONVERTING);
		$flavorAsset->save();

		return $dbBatchJob;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kConvertCollectionJobData $data
	 * @return BatchJob
	 */
	public static function handleConvertCollectionPending(BatchJob $dbBatchJob, kConvertCollectionJobData $data)
	{
		$flavors = $data->getFlavors();
		foreach($flavors as $flavor)
		{
			$flavorAsset = assetPeer::retrieveById($flavor->getFlavorAssetId());
			// verifies that flavor asset exists
			if(!$flavorAsset)
			{
				KalturaLog::err("Error: Flavor asset not found [" . $flavor->getFlavorAssetId() . "]");
				throw new APIException(APIErrors::INVALID_FLAVOR_ASSET_ID, $flavor->getFlavorAssetId());
			}

			$flavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_CONVERTING);
			$flavorAsset->save();
		}

		return $dbBatchJob;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kConvertJobData $data
	 * @return BatchJob
	 */
	public static function handleConvertFinished(BatchJob $dbBatchJob, kConvertJobData $data)
	{
		if($dbBatchJob->getExecutionStatus() == BatchJobExecutionStatus::ABORTED)
			return $dbBatchJob;

		// verifies that flavor asset created
		if(!$data->getFlavorAssetId())
			throw new APIException(APIErrors::INVALID_FLAVOR_ASSET_ID, $data->getFlavorAssetId());

		$flavorAsset = assetPeer::retrieveById($data->getFlavorAssetId());
		// verifies that flavor asset exists
		if(!$flavorAsset)
			throw new APIException(APIErrors::INVALID_FLAVOR_ASSET_ID, $data->getFlavorAssetId());

		$shouldSave = false;
		if(!is_null($data->getEngineMessage())) {
			$flavorAsset->setDescription($flavorAsset->getDescription() . "\n" . $data->getEngineMessage());
			$shouldSave = true;
		}

		$flavorParamsOutput = $data->getFlavorParamsOutput();
		
		if($data->getDestFileSyncLocalPath()) {
			$flavorAsset->incrementVersion();
			$shouldSave = true;
		}		
		
		if($shouldSave)
			$flavorAsset->save();
		
		if(count($data->getExtraDestFileSyncs()))
		{
			//operation engine creating only file assets should be the last one in the operations chain
			self::handleAdditionalFilesConvertFinished($flavorAsset, $dbBatchJob, $data);
		}			
		if($data->getDestFileSyncLocalPath())
		{
			self::handleFlavorAssetConvertFinished($flavorAsset, $flavorParamsOutput, $dbBatchJob, $data);
		}
		
		$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$fileSync = FileSyncPeer::retrieveByFileSyncKey($syncKey, true);
		if($fileSync) {
			$dbBatchJob->putInCustomData("flavor_size", $fileSync->getFileSize());
			$dbBatchJob->save();
		}
		
		$entry = $dbBatchJob->getEntry();
		if(!$entry)
			throw new APIException(APIErrors::INVALID_ENTRY, 'entry', $dbBatchJob->getEntryId());

		$rootBatchJob = $dbBatchJob->getRootJob();
		if(!$data->getDestFileSyncLocalPath() && $fileSync) 
		{
			//no flavors were created in the last conversion, updating the DestFileSyncLocalPath to the path of the last created flavor
			KalturaLog::info('Setting destFileSyncLocalPath with: '.$fileSync->getFullPath());
			$data->setDestFileSyncLocalPath($fileSync->getFullPath());		
		}
		$nextJob = self::createNextJob($flavorParamsOutput, $dbBatchJob, $data, $syncKey); //todo validate sync key
		if(!$nextJob)
		{
			self::handleOperatorsProcessingFinished($flavorAsset, $flavorParamsOutput, $entry, $dbBatchJob, $data, $rootBatchJob, $syncKey);
		}
		// this logic decide when a thumbnail should be created
		if($rootBatchJob && $rootBatchJob->getJobType() == BatchJobType::BULKDOWNLOAD)
		{
			self::createNotificationBulkDownloadSucceeded($dbBatchJob, $entry, $flavorAsset, $syncKey);
		}
		return $dbBatchJob;
	}
	private static function handleFlavorAssetConvertFinished(flavorAsset $flavorAsset, flavorParamsOutput $flavorParamsOutput, BatchJob $dbBatchJob, kConvertJobData $data)
	{
		$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$storageProfileId = $flavorParamsOutput->getSourceRemoteStorageProfileId();
		if($storageProfileId == StorageProfile::STORAGE_KALTURA_DC)
		{
			kFileSyncUtils::moveFromFile($data->getDestFileSyncLocalPath(), $syncKey);
		}
		elseif($flavorParamsOutput->getRemoteStorageProfileIds())
		{
			$remoteStorageProfileIds = explode(',', $flavorParamsOutput->getRemoteStorageProfileIds());
			foreach($remoteStorageProfileIds as $remoteStorageProfileId)
			{
				$storageProfile = StorageProfilePeer::retrieveByPK($remoteStorageProfileId);
				kFileSyncUtils::createReadyExternalSyncFileForKey($syncKey, $data->getDestFileSyncLocalPath(), $storageProfile);
			}
		}
		
		// creats the file sync
		if(file_exists($data->getLogFileSyncLocalPath()))
		{
			$logSyncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_CONVERT_LOG);
			try{
				kFileSyncUtils::moveFromFile($data->getLogFileSyncLocalPath(), $logSyncKey);
			}
			catch(Exception $e){
				$err = 'Saving conversion log: ' . $e->getMessage();
				KalturaLog::err($err);

				$desc = $dbBatchJob->getDescription() . "\n" . $err;
				$dbBatchJob->getDescription($desc);
			}
		}
		
		if($storageProfileId == StorageProfile::STORAGE_KALTURA_DC)
		{
			$data->setDestFileSyncLocalPath(kFileSyncUtils::getLocalFilePathForKey($syncKey));

			// save the data changes to the db
			$dbBatchJob->setData($data);
			$dbBatchJob->save();
		}
	}
	private static function createNextJob(flavorParamsOutput $flavorParamsOutput, BatchJob $dbBatchJob, kConvertJobData $data, FileSyncKey $syncKey)
	{
		$operatorSet = new kOperatorSets();
		$operatorSet->setSerialized(stripslashes($flavorParamsOutput->getOperators()));
		$nextOperator = $operatorSet->getOperator($data->getCurrentOperationSet(), $data->getCurrentOperationIndex() + 1);

		$nextJob = null;
		if($nextOperator)
		{
			//Note: consequent operators doesn't support at the moment conversion based on outputs of multiple sources
			$nextJob = kJobsManager::addFlavorConvertJob(array($syncKey), $flavorParamsOutput, $data->getFlavorAssetId(), null,
					$data->getMediaInfoId(), $dbBatchJob, $dbBatchJob->getJobSubType());
		}

		return $nextJob;
	}
	private static function handleOperatorsProcessingFinished(flavorAsset $flavorAsset, flavorParamsOutput $flavorParamsOutput, entry $entry, BatchJob $dbBatchJob, kConvertJobData $data, $rootBatchJob = null, $syncKey = null)
	{
		$offset = $entry->getThumbOffset(); // entry getThumbOffset now takes the partner DefThumbOffset into consideration

		$createThumb = $entry->getCreateThumb();
		$extractMedia = true;

		if($entry->getType() != entryType::MEDIA_CLIP) // e.g. document
			$extractMedia = false;				
		
		if(!kFileSyncUtils::fileSync_exists($flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET)))
		{
			$extractMedia = false;
			$createThumb = false;
		}
		
		$rootBatchJob = $dbBatchJob->getRootJob();

		if($extractMedia && $rootBatchJob && $rootBatchJob->getJobType() == BatchJobType::CONVERT_PROFILE)
		{
			$rootBatchJobData = $rootBatchJob->getData();
			if($rootBatchJobData instanceof kConvertProfileJobData)
				$extractMedia = $rootBatchJobData->getExtractMedia();
		}

		// For apple http flavors do not attempt to get thumbs and media info,
		// It is up to the operator to provide that kind of data, rather than hardcoded check
		// To-fix
		if($flavorParamsOutput->getFormat() == assetParams::CONTAINER_FORMAT_APPLEHTTP)
		{
			$createThumb = false;
			$extractMedia = false;
		}
		if($flavorParamsOutput->getFormat() == assetParams::CONTAINER_FORMAT_WIDEVINE)
		{
			$createThumb = false;
		}
		if($createThumb && in_array($flavorParamsOutput->getVideoCodec(), self::$thumbUnSupportVideoCodecs))
			$createThumb = false;
			
		if($createThumb || $extractMedia)
		{
			$postConvertAssetType = BatchJob::POSTCONVERT_ASSET_TYPE_FLAVOR;
			if($flavorAsset->getIsOriginal())
				$postConvertAssetType = BatchJob::POSTCONVERT_ASSET_TYPE_SOURCE;

			kJobsManager::addPostConvertJob($dbBatchJob, $postConvertAssetType, $syncKey, $data->getFlavorAssetId(), $flavorParamsOutput->getId(), $createThumb, $offset);
		}
		else // no need to run post convert
		{
			$flavorAsset = kBusinessPostConvertDL::handleFlavorReady($dbBatchJob, $data->getFlavorAssetId());
			if($flavorAsset)
			{
				if($flavorAsset->hasTag(flavorParams::TAG_SOURCE))
					kBusinessPreConvertDL::continueProfileConvert($dbBatchJob);

				if($flavorAsset->getType() == assetType::FLAVOR)
				{
					$flavorAsset->setBitrate($flavorParamsOutput->getVideoBitrate()+$flavorParamsOutput->getAudioBitrate());
					$flavorAsset->setWidth($flavorParamsOutput->getWidth());
					$flavorAsset->setHeight($flavorParamsOutput->getHeight());
					$flavorAsset->setFrameRate($flavorParamsOutput->getFrameRate());
					$flavorAsset->setIsOriginal(0);
					$flavorAsset->save();
				}

				kBusinessPostConvertDL::handleConvertFinished($dbBatchJob, $flavorAsset);
			}
		}
	}
		
	private static function createNotificationBulkDownloadSucceeded(BatchJob $dbBatchJob, entry $entry, flavorAsset $flavorAsset, FileSyncKey $syncKey)
	{
		$localPath = kFileSyncUtils::getLocalFilePathForKey($syncKey);
		$downloadUrl = $flavorAsset->getDownloadUrl();

		$notificationData = array(
			"puserId" => $entry->getPuserId(),
			"entryId" => $entry->getId(),
			"entryIntId" => $entry->getIntId(),
			"entryVersion" => $entry->getVersion(),
			"fileFormat" => $flavorAsset->getFileExt(),
		//				"email" => '',
			"archivedFile" => $localPath,
			"downoladPath" => $localPath,
			"conversionQuality" => $entry->getConversionQuality(),
			"downloadUrl" => $downloadUrl,
		);

		$extraData = array(
			"data" => json_encode($notificationData),
			"partner_id" => $entry->getPartnerId(),
			"puser_id" => $entry->getPuserId(),
			"entry_id" => $entry->getId(),
			"entry_int_id" => $entry->getIntId(),
			"entry_version" => $entry->getVersion(),
			"file_format" => $flavorAsset->getFileExt(),
		//				"email" => '',
			"archived_file" => $localPath,
			"downolad_path" => $localPath,
			"target" => $localPath,
			"conversion_quality" => $entry->getConversionQuality(),
			"download_url" => $downloadUrl,
			"status" => $entry->getStatus(),
			"abort" => ($dbBatchJob->getExecutionStatus() == BatchJobExecutionStatus::ABORTED),
			"message" => $dbBatchJob->getMessage(),
			"description" => $dbBatchJob->getDescription(),
			"job_type" => BatchJobType::DOWNLOAD,
			"status" => BatchJob::BATCHJOB_STATUS_FINISHED,
			"progress" => 100,
			"debug" => __LINE__,
		);

		myNotificationMgr::createNotification(kNotificationJobData::NOTIFICATION_TYPE_BATCH_JOB_SUCCEEDED, $dbBatchJob , $dbBatchJob->getPartnerId() , null , null ,
		$extraData, $dbBatchJob->getEntryId() );
	}
	
	/**
	 * 
	 * Allows to create additional files in the conversion process in addition to flavor asset 
	 */
	private static function handleAdditionalFilesConvertFinished(flavorAsset $flavorAsset, BatchJob $dbBatchJob, kConvertJobData $data)
	{
		if(!$flavorAsset->getVersion() || !kFileSyncUtils::fileSync_exists($flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET)))
		{
			$flavorAsset->incrementVersion();
			$flavorAsset->save();
		}
		
		foreach ($data->getExtraDestFileSyncs() as $destFileSyncDesc) 
		{
			$syncKey = $flavorAsset->getSyncKey($destFileSyncDesc->getFileSyncObjectSubType());

			$flavorParamsOutput = $data->getFlavorParamsOutput();
			$storageProfileId = $flavorParamsOutput->getSourceRemoteStorageProfileId();
			if($storageProfileId == StorageProfile::STORAGE_KALTURA_DC)
			{
				kFileSyncUtils::moveFromFile($destFileSyncDesc->getFileSyncLocalPath(), $syncKey, false);
			}
			elseif($flavorParamsOutput->getRemoteStorageProfileIds())
			{
				$remoteStorageProfileIds = explode(',', $flavorParamsOutput->getRemoteStorageProfileIds());
				foreach($remoteStorageProfileIds as $remoteStorageProfileId)
				{
					$storageProfile = StorageProfilePeer::retrieveByPK($remoteStorageProfileId);
					kFileSyncUtils::createReadyExternalSyncFileForKey($syncKey, $destFileSyncDesc->getFileSyncLocalPath(), $storageProfile);
				}
			}			
		}
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kCaptureThumbJobData $data
	 * @return BatchJob
	 */
	public static function handleCaptureThumbFinished(BatchJob $dbBatchJob, kCaptureThumbJobData $data)
	{
		if($dbBatchJob->getExecutionStatus() == BatchJobExecutionStatus::ABORTED)
			return $dbBatchJob;

		// verifies that thumb asset created
		if(!$data->getThumbAssetId())
			throw new APIException(APIErrors::INVALID_THUMB_ASSET_ID, $data->getThumbAssetId());

		$thumbAsset = assetPeer::retrieveById($data->getThumbAssetId());
		// verifies that thumb asset exists
		if(!$thumbAsset)
			throw new APIException(APIErrors::INVALID_THUMB_ASSET_ID, $data->getThumbAssetId());

		$thumbAsset->incrementVersion();
		$thumbAsset->setStatus(thumbAsset::FLAVOR_ASSET_STATUS_READY);


		if(file_exists($data->getThumbPath()))
		{
			list($width, $height, $type, $attr) = getimagesize($data->getThumbPath());
			$thumbAsset->setWidth($width);
			$thumbAsset->setHeight($height);
			$thumbAsset->setSize(filesize($data->getThumbPath()));
		}

		$logPath = $data->getThumbPath() . '.log';
		if(file_exists($logPath))
		{
			$thumbAsset->incLogFileVersion();
			$thumbAsset->save();

			// creats the file sync
			$logSyncKey = $thumbAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_CONVERT_LOG);
			try{
				kFileSyncUtils::moveFromFile($logPath, $logSyncKey);
			}
			catch(Exception $e){
				$err = 'Saving conversion log: ' . $e->getMessage();
				KalturaLog::err($err);

				$desc = $dbBatchJob->getDescription() . "\n" . $err;
				$dbBatchJob->getDescription($desc);
			}
		}
		else
		{
			$thumbAsset->save();
		}

		$syncKey = $thumbAsset->getSyncKey(thumbAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		kFileSyncUtils::moveFromFile($data->getThumbPath(), $syncKey);

		$data->setThumbPath(kFileSyncUtils::getLocalFilePathForKey($syncKey));
		KalturaLog::info("Thumbnail archived file to: " . $data->getThumbPath());

		// save the data changes to the db
		$dbBatchJob->setData($data);
		$dbBatchJob->save();

		$entry = $thumbAsset->getentry();
		if($entry && $entry->getCreateThumb() && $thumbAsset->hasTag(thumbParams::TAG_DEFAULT_THUMB))
		{
			$entry = $dbBatchJob->getEntry(false, false);
			if(!$entry)
				throw new APIException(APIErrors::INVALID_ENTRY, $dbBatchJob, $dbBatchJob->getEntryId());

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
			kFlowHelper::generateThumbnailsFromFlavor($dbBatchJob->getEntryId(), $dbBatchJob, $thumbAsset->getFlavorParamsId());

		self::handleLocalFileSyncDeletion($dbBatchJob->getEntryId(), $dbBatchJob->getPartner());
		
		return $dbBatchJob;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kConvertJobData $data
	 * @return BatchJob
	 */
	public static function handleConvertQueued(BatchJob $dbBatchJob, kConvertJobData $data)
	{
		$rootBatchJob = $dbBatchJob->getRootJob();
		if($rootBatchJob && $rootBatchJob->getJobType() == BatchJobType::BULKDOWNLOAD)
		{
			$entry = $dbBatchJob->getEntry();
			if(!$entry)
				return $dbBatchJob;

			$notificationData = array(
				"puserId" => $entry->getPuserId(),
				"entryId" => $entry->getId(),
				"entryIntId" => $entry->getIntId(),
				"entryVersion" => $entry->getVersion(),
			//				"email" => '',
				"conversionQuality" => $entry->getConversionQuality(),
			);

			$extraData = array(
				"data" => json_encode($notificationData),
				"partner_id" => $entry->getPartnerId(),
				"puser_id" => $entry->getPuserId(),
				"entry_id" => $entry->getId(),
				"entry_int_id" => $entry->getIntId(),
				"entry_version" => $entry->getVersion(),
			//				"email" => '',
				"conversion_quality" => $entry->getConversionQuality(),
				"status" => $entry->getStatus(),
				"abort" => ($dbBatchJob->getExecutionStatus() == BatchJobExecutionStatus::ABORTED),
				"message" => $dbBatchJob->getMessage(),
				"description" => $dbBatchJob->getDescription(),
				"job_type" => BatchJobType::DOWNLOAD,
				"status" => BatchJob::BATCHJOB_STATUS_QUEUED,
				"progress" => 0,
				"debug" => __LINE__,
			);

			myNotificationMgr::createNotification(kNotificationJobData::NOTIFICATION_TYPE_BATCH_JOB_STARTED, $dbBatchJob , $dbBatchJob->getPartnerId() , null , null ,
				$extraData, $dbBatchJob->getEntryId() );
		}

		return $dbBatchJob;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kConvertJobData $data
	 * @return BatchJob
	 */
	public static function handleConvertFailed(BatchJob $dbBatchJob, kConvertJobData $data)
	{
		if($dbBatchJob->getExecutionStatus() == BatchJobExecutionStatus::ABORTED)
			return $dbBatchJob;

		// verifies that flavor asset created
		if(!$data->getFlavorAssetId())
			throw new APIException(APIErrors::INVALID_FLAVOR_ASSET_ID, $data->getFlavorAssetId());

		$flavorAsset = assetPeer::retrieveById($data->getFlavorAssetId());
		// verifies that flavor asset exists
		if(!$flavorAsset)
			throw new APIException(APIErrors::INVALID_FLAVOR_ASSET_ID, $data->getFlavorAssetId());
		
		if(!is_null($data->getEngineMessage())) {
			$flavorAsset->setDescription($flavorAsset->getDescription() . "\n" . $data->getEngineMessage());
			$flavorAsset->save();
		}

		// creats the file sync
		if(file_exists($data->getLogFileSyncLocalPath()))
		{
			$logSyncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_CONVERT_LOG);
			try{
				kFileSyncUtils::moveFromFile($data->getLogFileSyncLocalPath(), $logSyncKey);
			}
			catch(Exception $e){
				$err = 'Saving conversion log: ' . $e->getMessage();
				KalturaLog::err($err);

				$desc = $dbBatchJob->getDescription() . "\n" . $err;
				$dbBatchJob->getDescription($desc);
			}
		}

		//		$flavorAsset->incrementVersion();
		//		$flavorAsset->save();

		$fallbackCreated = kBusinessPostConvertDL::handleConvertFailed($dbBatchJob, $dbBatchJob->getJobSubType(), $data->getFlavorAssetId(), $data->getFlavorParamsOutputId(), $data->getMediaInfoId());

		if(!$fallbackCreated)
		{
			$rootBatchJob = $dbBatchJob->getRootJob();
			if($rootBatchJob && $rootBatchJob->getJobType() == BatchJobType::BULKDOWNLOAD)
			{
				$entryId = $dbBatchJob->getEntryId();
				$flavorParamsId = $data->getFlavorParamsOutputId();
				$flavorParamsOutput = assetParamsOutputPeer::retrieveByPK($flavorParamsId);
				$fileFormat = $flavorParamsOutput->getFileExt();

				$entry = $dbBatchJob->getEntry();
				if(!$entry)
					return $dbBatchJob;

				$notificationData = array(
					"puserId" => $entry->getPuserId(),
					"entryId" => $entry->getId(),
					"entryIntId" => $entry->getIntId(),
					"entryVersion" => $entry->getVersion(),
					"fileFormat" => $flavorAsset->getFileExt(),
				//				"email" => '',
					"conversionQuality" => $entry->getConversionQuality(),
				);

				$extraData = array(
					"data" => json_encode($notificationData),
					"partner_id" => $entry->getPartnerId(),
					"puser_id" => $entry->getPuserId(),
					"entry_id" => $entry->getId(),
					"entry_int_id" => $entry->getIntId(),
					"entry_version" => $entry->getVersion(),
				//				"email" => '',
					"conversion_quality" => $entry->getConversionQuality(),
					"status" => $entry->getStatus(),
					"abort" => ($dbBatchJob->getExecutionStatus() == BatchJobExecutionStatus::ABORTED),
					"message" => $dbBatchJob->getMessage(),
					"description" => $dbBatchJob->getDescription(),
					"job_type" => BatchJobType::DOWNLOAD,
					"conversion_error" => "Error while converting [$entryId] [$fileFormat]",
					"status" => BatchJob::BATCHJOB_STATUS_FAILED,
					"progress" => 0,
					"debug" => __LINE__,
				);

				myNotificationMgr::createNotification(kNotificationJobData::NOTIFICATION_TYPE_BATCH_JOB_FAILED, $dbBatchJob , $dbBatchJob->getPartnerId() , null , null ,
					$extraData, $entryId );
			}
		}

		return $dbBatchJob;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kCaptureThumbJobData $data
	 * @return BatchJob
	 */
	public static function handleCaptureThumbFailed(BatchJob $dbBatchJob, kCaptureThumbJobData $data)
	{
		if($dbBatchJob->getExecutionStatus() == BatchJobExecutionStatus::ABORTED)
			return $dbBatchJob;

		// verifies that thumb asset created
		if(!$data->getThumbAssetId())
			throw new APIException(APIErrors::INVALID_THUMB_ASSET_ID, $data->getThumbAssetId());

		$thumbAsset = assetPeer::retrieveById($data->getThumbAssetId());
		// verifies that thumb asset exists
		if(!$thumbAsset)
			throw new APIException(APIErrors::INVALID_THUMB_ASSET_ID, $data->getThumbAssetId());

		$thumbAsset->incrementVersion();
		$thumbAsset->setStatus(thumbAsset::FLAVOR_ASSET_STATUS_ERROR);
		$thumbAsset->save();

		self::handleLocalFileSyncDeletion($dbBatchJob->getEntryId(), $dbBatchJob->getPartner());
		
		return $dbBatchJob;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kPostConvertJobData $data
	 * @return BatchJob
	 */
	public static function handlePostConvertFailed(BatchJob $dbBatchJob, kPostConvertJobData $data)
	{
		// get additional info from the parent job
		$engineType = null;
		$mediaInfoId = null;
		$parentJob = $dbBatchJob->getParentJob();
		if($parentJob)
		{
			$engineType = $parentJob->getJobSubType();
			$convertJobData = $parentJob->getData();
			if($convertJobData instanceof kConvertJobData)
				$mediaInfoId = $convertJobData->getMediaInfoId();
		}

		kBusinessPostConvertDL::handleConvertFailed($dbBatchJob, $engineType, $data->getFlavorAssetId(), $data->getFlavorParamsOutputId(), $mediaInfoId);

		return $dbBatchJob;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kConvertCollectionJobData $data
	 * @return BatchJob
	 */
	public static function handleDeleteFileFinished(BatchJob $dbBatchJob, kDeleteFileJobData $data)
	{
		//Change status of the filesync to "purged"
		FileSyncPeer::setUseCriteriaFilter(false);
		$fileSyncFroDeletedFile = FileSyncPeer::retrieveByFileSyncKey($data->getSyncKey(), true);
		FileSyncPeer::setUseCriteriaFilter(true);
		$fileSyncFroDeletedFile->setStatus(FileSync::FILE_SYNC_STATUS_PURGED);
		$fileSyncFroDeletedFile->save();

		return $dbBatchJob;
	}

	public static function handleDeleteFileProcessing (kDeleteFileJobData $data)
	{
		KalturaLog::info("Delete started for file path " . $data->getLocalFileSyncPath());
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kConvertCollectionJobData $data
	 * @return BatchJob
	 */
	public static function handleConvertCollectionFinished(BatchJob $dbBatchJob, kConvertCollectionJobData $data)
	{
		if($dbBatchJob->getExecutionStatus() == BatchJobExecutionStatus::ABORTED)
			return $dbBatchJob;


		$entry = $dbBatchJob->getEntry();
		if(!$entry)
			throw new APIException(APIErrors::INVALID_ENTRY, $dbBatchJob, $dbBatchJob->getEntryId());

		$ismPath = $data->getDestDirLocalPath() . DIRECTORY_SEPARATOR . $data->getDestFileName() . '.ism';
		$ismcPath = $data->getDestDirLocalPath() . DIRECTORY_SEPARATOR . $data->getDestFileName() . '.ismc';
		$logPath = $data->getDestDirLocalPath() . DIRECTORY_SEPARATOR . $data->getDestFileName() . '.log';
		$thumbPath = $data->getDestDirLocalPath() . DIRECTORY_SEPARATOR . $data->getDestFileName() . '_Thumb.jpg';
		$ismContent = file_get_contents($ismPath);

		$offset = $entry->getThumbOffset(); // entry getThumbOffset now takes the partner DefThumbOffset into consideration

		$finalFlavors = array();
		$addedFlavorParamsOutputsIds = array();
		foreach($data->getFlavors() as $flavor)
		{
			// verifies that flavor asset created
			if(!$flavor->getFlavorAssetId())
				throw new APIException(APIErrors::INVALID_FLAVOR_ASSET_ID, $data->getFlavorAssetId());

			$flavorAsset = assetPeer::retrieveById($flavor->getFlavorAssetId());
			// verifies that flavor asset exists
			if(!$flavorAsset)
				throw new APIException(APIErrors::INVALID_FLAVOR_ASSET_ID, $flavor->getFlavorAssetId());

			// increment flavor asset version (for file sync)
			$flavorAsset->incrementVersion();
			$flavorAsset->save();

			// syncing the media file
			$destFileSyncLocalPath = $flavor->getDestFileSyncLocalPath();
			if(!file_exists($destFileSyncLocalPath))
				continue;

			$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			kFileSyncUtils::moveFromFile($destFileSyncLocalPath, $syncKey);

			// replacing the file name in the ism file
			$oldName = basename($flavor->getDestFileSyncLocalPath());
			$flavor->setDestFileSyncLocalPath(kFileSyncUtils::getLocalFilePathForKey($syncKey));
			KalturaLog::info("Convert archived file to: " . $flavor->getDestFileSyncLocalPath());
			$newName = basename($flavor->getDestFileSyncLocalPath());
			KalturaLog::info("Editing ISM [$oldName] to [$newName]");
			$ismContent = str_replace("src=\"$oldName\"", "src=\"$newName\"", $ismContent);

			// creating post convert job (without thumb)
			$postConvertAssetType = BatchJob::POSTCONVERT_ASSET_TYPE_FLAVOR;
			kJobsManager::addPostConvertJob($dbBatchJob, $postConvertAssetType, $syncKey, $flavor->getFlavorAssetId(), $flavor->getFlavorParamsOutputId(), file_exists($thumbPath), $offset);

			$finalFlavors[] = $flavor;
			$addedFlavorParamsOutputsIds[] = $flavor->getFlavorParamsOutputId();
		}


		$ismVersion = $entry->getIsmVersion();

		// syncing the ismc file
		if(file_exists($ismcPath))
		{
			$syncKey = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_ISMC, $ismVersion);
			kFileSyncUtils::moveFromFile($ismcPath,	$syncKey);
		}

		// replacing the ismc file name in the ism file
		$oldName = basename($ismcPath);
		$newName = basename(kFileSyncUtils::getLocalFilePathForKey($syncKey));
		KalturaLog::info("Editing ISM [$oldName] to [$newName]");
		$ismContent = str_replace("content=\"$oldName\"", "content=\"$newName\"", $ismContent);

		$ismPath .= '.tmp';
		$bytesWritten = file_put_contents($ismPath, $ismContent);
		if(!$bytesWritten)
			KalturaLog::err("Failed to update file [$ismPath]");

		// syncing ism and lig files
		if(file_exists($ismPath))
			kFileSyncUtils::moveFromFile($ismPath, $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_ISM, $ismVersion));

		if(file_exists($logPath))
			kFileSyncUtils::moveFromFile($logPath, $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_CONVERSION_LOG, $ismVersion));

		// saving entry changes
		$entry->save();


		// save the data changes to the db
		$data->setFlavors($finalFlavors);
		$dbBatchJob->setData($data);
		$dbBatchJob->save();

		// send notification if needed
		$rootBatchJob = $dbBatchJob->getRootJob();
		if($rootBatchJob && $rootBatchJob->getJobType() == BatchJobType::BULKDOWNLOAD)
		{
			$localPath = kFileSyncUtils::getLocalFilePathForKey($syncKey);
			$downloadUrl = $flavorAsset->getDownloadUrl();

			$notificationData = array(
				"puserId" => $entry->getPuserId(),
				"entryId" => $entry->getId(),
				"entryIntId" => $entry->getIntId(),
				"entryVersion" => $entry->getVersion(),
			//				"fileFormat" => '',
			//				"email" => '',
				"archivedFile" => $localPath,
				"downoladPath" => $localPath,
				"conversionQuality" => $entry->getConversionQuality(),
				"downloadUrl" => $downloadUrl,
			);

			$extraData = array(
				"data" => json_encode($notificationData),
				"partner_id" => $entry->getPartnerId(),
				"puser_id" => $entry->getPuserId(),
				"entry_id" => $entry->getId(),
				"entry_int_id" => $entry->getIntId(),
				"entry_version" => $entry->getVersion(),
				"file_format" => $flavorAsset->getFileExt(),
			//				"email" => '',
				"archived_file" => $localPath,
				"downolad_path" => $localPath,
				"target" => $localPath,
				"conversion_quality" => $entry->getConversionQuality(),
				"download_url" => $downloadUrl,
				"status" => $entry->getStatus(),
				"abort" => ($dbBatchJob->getExecutionStatus() == BatchJobExecutionStatus::ABORTED),
				"message" => $dbBatchJob->getMessage(),
				"description" => $dbBatchJob->getDescription(),
				"job_type" => BatchJobType::DOWNLOAD,
				"status" => BatchJob::BATCHJOB_STATUS_FINISHED,
				"progress" => 100,
				"debug" => __LINE__,
			);

			myNotificationMgr::createNotification(kNotificationJobData::NOTIFICATION_TYPE_BATCH_JOB_SUCCEEDED, $dbBatchJob , $dbBatchJob->getPartnerId() , null , null ,
				$extraData, $dbBatchJob->getEntryId() );
		}
		return $dbBatchJob;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kConvertCollectionJobData $data
	 * @return BatchJob
	 */
	public static function handleConvertCollectionFailed(BatchJob $dbBatchJob, kConvertCollectionJobData $data)
	{
		kBusinessPostConvertDL::handleConvertCollectionFailed($dbBatchJob, $data, $dbBatchJob->getJobSubType());

		return $dbBatchJob;
	}

	/**
	 * @param BatchJob $parentJob
	 * @param int $srcParamsId
	 */
	public static function generateThumbnailsFromFlavor($entryId, BatchJob $parentJob = null, $srcParamsId = null)
	{
		$entry = entryPeer::retrieveByPK($entryId);
		if(!$entry)
		{
			KalturaLog::notice("Entry id [$entryId] not found");
			return;
		}

		if($entry->getType() != entryType::MEDIA_CLIP || $entry->getMediaType() != entry::ENTRY_MEDIA_TYPE_VIDEO)
		{
			KalturaLog::notice("Cupture thumbnail is not supported for entry [$entryId] of type [" . $entry->getType() . "] and media type [" . $entry->getMediaType() . "]");
			return;
		}

		$profile = null;
		try
		{
			$profile = myPartnerUtils::getConversionProfile2ForEntry($entryId);
		}
		catch(Exception $e)
		{
			KalturaLog::err('getConversionProfile2ForEntry Error: ' . $e->getMessage());
		}

		if(!$profile)
		{
			KalturaLog::notice("Profile not found for entry id [$entryId]");
			return;
		}

		$assetParamsIds = flavorParamsConversionProfilePeer::getFlavorIdsByProfileId($profile->getId());
		if(!count($assetParamsIds))
		{
			KalturaLog::notice("No asset params objects found for profile id [" . $profile->getId() . "]");
			return;
		}

		// the alternative is the source or the highest bitrate if source not defined
		$alternateFlavorParamsId = null;
		if(is_null($srcParamsId))
		{
			$flavorParamsObjects = assetParamsPeer::retrieveFlavorsByPKs($assetParamsIds);
			foreach($flavorParamsObjects as $flavorParams)
				if($flavorParams->hasTag(flavorParams::TAG_SOURCE))
					$alternateFlavorParamsId = $flavorParams->getId();

			if(is_null($alternateFlavorParamsId))
			{
				$srcFlavorAsset = assetPeer::retrieveHighestBitrateByEntryId($entryId);
				if($srcFlavorAsset)
					$alternateFlavorParamsId = $srcFlavorAsset->getFlavorParamsId();
			}

			if(is_null($alternateFlavorParamsId))
			{
				KalturaLog::notice("No source flavor params object found for entry id [$entryId]");
				return;
			}
		}

		// create list of created thumbnails
		$thumbAssetsList = array();
		$thumbAssets = assetPeer::retrieveThumbnailsByEntryId($entryId);
		if(count($thumbAssets))
		{
			foreach($thumbAssets as $thumbAsset)
				if(!is_null($thumbAsset->getFlavorParamsId()))
					$thumbAssetsList[$thumbAsset->getFlavorParamsId()] = $thumbAsset;
		}

		$thumbParamsObjects = assetParamsPeer::retrieveThumbnailsByPKs($assetParamsIds);
		foreach($thumbParamsObjects as $thumbParams)
		{
			if(isset($thumbAssetsList[$thumbParams->getId()]))
			{
				KalturaLog::log("Thumbnail asset already created [" . $thumbAssetsList[$thumbParams->getId()]->getId() . "]");
				continue;
			}

			if(is_null($srcParamsId) && is_null($thumbParams->getSourceParamsId()))
			{
				// alternative should be used
				$thumbParams->setSourceParamsId($alternateFlavorParamsId);
			}
			elseif($thumbParams->getSourceParamsId() != $srcParamsId)
			{
				KalturaLog::log("Only thumbnails that uses source params [$srcParamsId] should be generated for now");
				continue;
			}

			kBusinessPreConvertDL::decideThumbGenerate($entry, $thumbParams, $parentJob);
		}
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kPostConvertJobData $data
	 */
	protected static function createThumbnail(BatchJob $dbBatchJob, kPostConvertJobData $data)
	{
		$ignoreThumbnail = false;

		// this logic decide when this thumbnail should be used
		$entry = $dbBatchJob->getEntry();
		if(!$entry)
			return $dbBatchJob;

		/*
		 * Retrieve data describing the new thumb
		 */
		$thisFlavorHeight = $data->getThumbHeight();
		$thisFlavorBitrate = $data->getThumbBitrate();
		$thisFlavorId = $data->getFlavorAssetId();

		/*
		 * If there is already a thumb assigned to that entry, get the asset id that was used to grab the thumb.
		 * For older entries (w/out grabbedFromAssetId), the original logic would be used.
		 * For newer entries - retrieve mediaInfo's for th new and grabbed assest.
		 * Use KDL logic to normalize the 'grabbed' and 'new' video bitrates.
		 * Set ignoreThumbnail if the new br is lower than the normalized.
		 */
		if($entry->getCreateThumb()) {
			$grabbedFromAssetId=$entry->getThumbGrabbedFromAssetId();
			if(isset($grabbedFromAssetId)){
				$thisMediaInfo = mediaInfoPeer::retrieveByFlavorAssetId($thisFlavorId);
				$grabMediaInfo = mediaInfoPeer::retrieveByFlavorAssetId($grabbedFromAssetId);
				if(isset($thisMediaInfo) && isset($grabMediaInfo)){
					$normalizedBr=KDLVideoBitrateNormalize::NormalizeSourceToTarget($grabMediaInfo->getVideoFormat(), $grabMediaInfo->getVideoBitrate(), $thisMediaInfo->getVideoFormat());
					$ignoreThumbnail = ($normalizedBr>=$thisMediaInfo->getVideoBitrate()? true: false);
				}
				else
					$grabbedFromAssetId=null;
			}

			/*
			 * Nulled 'grabbedFromAssetId' notifies - there is no grabbed asset data available,
			 * ==> use the older logic - w/out br normalizing
			 */
			if(!isset($grabbedFromAssetId)){
				if($entry->getThumbBitrate() > $thisFlavorBitrate) {
			$ignoreThumbnail = true;
		}
		elseif($entry->getThumbBitrate() == $thisFlavorBitrate && $entry->getThumbHeight() > $thisFlavorHeight)	{
			$ignoreThumbnail = true;
				}
			}
		}
		else {
			$ignoreThumbnail = true;
		}

		if(!$ignoreThumbnail)
		{
			$entry->setThumbHeight($thisFlavorHeight);
			$entry->setThumbBitrate($thisFlavorBitrate);
			$entry->setThumbGrabbedFromAssetId($thisFlavorId);
			$entry->save();

			// creats thumbnail the file sync
			$entry = $dbBatchJob->getEntry(false, false);
			if(!$entry)
			{
				KalturaLog::err("Entry not found [" . $dbBatchJob->getEntryId() . "]");
				return;
			}

			KalturaLog::info("Entry duration: " . $entry->getLengthInMsecs());
			if(!$entry->getLengthInMsecs())
			{
				KalturaLog::info("Copy duration from flvor asset: " . $data->getFlavorAssetId());
				$mediaInfo = mediaInfoPeer::retrieveByFlavorAssetId($data->getFlavorAssetId());
				if($mediaInfo)
				{
					KalturaLog::info("Set duration to: " . $mediaInfo->getContainerDuration());
					$entry->setDimensionsIfBigger($mediaInfo->getVideoWidth(), $mediaInfo->getVideoHeight());
					
					if($entry->getCalculateDuration())
						$entry->setLengthInMsecs($mediaInfo->getContainerDuration());
				}
			}

			$entry->reload(); // make sure that the thumbnail version is the latest
			$entry->setThumbnail(".jpg");
			$entry->save();
			$syncKey = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB);
			kFileSyncUtils::moveFromFile($data->getThumbPath(), $syncKey);
		}
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kPostConvertJobData $data
	 * @return BatchJob|BatchJob
	 */
	public static function handlePostConvertFinished(BatchJob $dbBatchJob, kPostConvertJobData $data)
	{
		if($dbBatchJob->getExecutionStatus() == BatchJobExecutionStatus::ABORTED)
			return $dbBatchJob;

		if($data->getCreateThumb())
		{
			try
			{
				self::createThumbnail($dbBatchJob, $data);
			}
			catch (Exception $e)
			{
				KalturaLog::err($e->getMessage());

				// sometimes, because of disc IO load, it takes long time for the thumb to be moved.
				// in such cases, the entry thumb version may be increased by other process.
				// retry the job, it solves the issue.
				kJobsManager::retryJob($dbBatchJob->getId(), $dbBatchJob->getJobType(), true);
				$dbBatchJob->reload();
				return $dbBatchJob;
			}
		}

		$currentFlavorAsset = kBusinessPostConvertDL::handleFlavorReady($dbBatchJob, $data->getFlavorAssetId());

		if($data->getPostConvertAssetType() == BatchJob::POSTCONVERT_ASSET_TYPE_SOURCE)
		{
			$convertProfileJob = $dbBatchJob->getRootJob();
			if($convertProfileJob->getJobType() == BatchJobType::CONVERT_PROFILE)
			{
				try
				{
					$currFlavorAsset = assetPeer::retrieveById($data->getFlavorAssetId());
					//In cases we are returning from intermediate flow need to check maybe if another round is needed
					//This comes to support the creation of silent audio tracks on source assets such as .arf that require initial inter flow for the source and only than the addition
					//of the silent audio track
					if( $currFlavorAsset instanceof flavorAsset && $currFlavorAsset->getIsOriginal() && $currFlavorAsset->getInterFlowCount() != null)
					{ 
						//check if the inter flow count is larger than 2.  
						//In this cases probably something went wrong so we will continue with the original flow and will not check if any additioanl inter flow nneds to be done.
						if($currentFlavorAsset && $currFlavorAsset->getInterFlowCount() < self::MAX_INTER_FLOW_ITERATIONS_ALLOWED_ON_SOURCE)
						{
							$mediaInfo = mediaInfoPeer::retrieveByFlavorAssetId($currentFlavorAsset->getId());
							kBusinessPreConvertDL::decideProfileConvert($dbBatchJob, $convertProfileJob, $mediaInfo->getId());
						}
						else 
							kBusinessPreConvertDL::continueProfileConvert($dbBatchJob);
					}
					else 
						kBusinessPreConvertDL::continueProfileConvert($dbBatchJob);
				}
				catch(Exception $e)
				{
					KalturaLog::err($e->getMessage());
					kBatchManager::updateEntry($dbBatchJob->getEntryId(), entryStatus::ERROR_CONVERTING);
					return $dbBatchJob;
				}
			}
			elseif($currentFlavorAsset)
			{
				KalturaLog::log("Root job [" . $convertProfileJob->getId() . "] is not profile conversion");

				$syncKey = $currentFlavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
				if(kFileSyncUtils::fileSync_exists($syncKey))
				{
					$fileSync = kFileSyncUtils::getLocalFileSyncForKey($syncKey, false);
					$entry = $dbBatchJob->getEntry();
					if($entry)
						kJobsManager::addConvertProfileJob(null, $entry, $currentFlavorAsset->getId(), $fileSync);
				}
				$currentFlavorAsset = null;
			}
		}

		if($currentFlavorAsset)
			kBusinessPostConvertDL::handleConvertFinished($dbBatchJob, $currentFlavorAsset);

		return $dbBatchJob;
	}

	public static function createBulkUploadLogUrl(BatchJob $dbBatchJob)
	{
		$ks = new ks();
		$ks->valid_until = time() + 86400 ;
		$ks->type = ks::TYPE_KS;
		$ks->partner_id = $dbBatchJob->getPartnerId();
		$ks->master_partner_id = null;
		$ks->partner_pattern = $dbBatchJob->getPartnerId();
		$ks->error = 0;
		$ks->rand = microtime(true);
		$ks->user = '';
		$ks->privileges = 'setrole:BULK_LOG_VIEWER';
		$ks->additional_data = null;
		$ks_str = $ks->toSecureString();

		$logFileUrl = kConf::get("apphome_url") . "/api_v3/service/bulkUpload/action/serveLog/id/{$dbBatchJob->getId()}/ks/" . $ks_str;
		return $logFileUrl;
	}

    	public static function sendBulkUploadNotificationEmail(BatchJob $dbBatchJob, $email_id, $params)
    	{

        	$emailRecipients = $dbBatchJob->getPartner()->getBulkUploadNotificationsEmail();
        	$batchJobData = $dbBatchJob->getData();

        	if($batchJobData instanceof kBulkUploadJobData){
            		$jobRecipients = $batchJobData->getEmailRecipients();
            		if(isset($jobRecipients)) {
                		$emailRecipients = $jobRecipients;
            		}
        	}

        	kJobsManager::addMailJob(
            		null,
            		0,
            		$dbBatchJob->getPartnerId(),
            		$email_id,
            		kMailJobData::MAIL_PRIORITY_NORMAL,
            		kConf::get( "batch_alert_email" ),
            		kConf::get( "batch_alert_name" ),
            		$emailRecipients,
            		$params
        	);

    	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kBulkUploadJobData $data
	 * @return BatchJob
	 */
	public static function handleBulkUploadFinished(BatchJob $dbBatchJob, kBulkUploadJobData $data)
	{
		if ($dbBatchJob->getPartner()->getEnableBulkUploadNotificationsEmails())
			self::sendBulkUploadNotificationEmail($dbBatchJob, MailType::MAIL_TYPE_BULKUPLOAD_FINISHED, array($dbBatchJob->getPartner()->getAdminName(), $dbBatchJob->getId(), self::createBulkUploadLogUrl($dbBatchJob)));
		return $dbBatchJob;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kBulkUploadJobData $data
	 * @return BatchJob
	 */
	public static function handleBulkUploadFailed(BatchJob $dbBatchJob, kBulkUploadJobData $data)
	{
		if ($dbBatchJob->getPartner()->getEnableBulkUploadNotificationsEmails())
				self::sendBulkUploadNotificationEmail($dbBatchJob, MailType::MAIL_TYPE_BULKUPLOAD_FAILED, array($dbBatchJob->getPartner()->getAdminName(),$dbBatchJob->getId(), $dbBatchJob->getErrType(), $dbBatchJob->getErrNumber(), $dbBatchJob->getMessage(), self::createBulkUploadLogUrl($dbBatchJob)));
		return $dbBatchJob;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kStorageExportJobData $data
	 * @return BatchJob
	 */
	public static function handleStorageExportFinished(BatchJob $dbBatchJob, kStorageExportJobData $data)
	{
		$fileSync = FileSyncPeer::retrieveByPK($data->getSrcFileSyncId());
		if(!$fileSync)
		{
			KalturaLog::err("FileSync [" . $data->getSrcFileSyncId() . "] not found");
			return $dbBatchJob;
		}

		$fileSync->setStatus(FileSync::FILE_SYNC_STATUS_READY);
		$fileSync->save();

		// if an asset was exported - check if should set its status to READY
		$asset = assetPeer::retrieveByFileSync($fileSync);
			
		if ($asset && in_array($asset->getStatus(), array(asset::ASSET_STATUS_EXPORTING, asset::ASSET_STATUS_ERROR))
			&& self::isAssetExportFinished($fileSync, $asset))
		{
			
			$asset->setStatusLocalReady();
			$asset->save();

			if ( ($asset instanceof flavorAsset) && ($asset->getStatus() == asset::ASSET_STATUS_READY) )
			{
				kBusinessPostConvertDL::handleConvertFinished($dbBatchJob, $asset);
			}
		}		
		// check if all exports finished and delete local file sync according to configuration
		if($asset && $asset->getStatus() == asset::ASSET_STATUS_READY && $dbBatchJob->getJobSubType() != StorageProfile::STORAGE_KALTURA_DC)
		{
			$partner = $dbBatchJob->getPartner();
			if($partner && $partner->getStorageDeleteFromKaltura())
			{
				if(self::isAssetExportFinished($fileSync, $asset))
				{
					if(!is_null($asset->getentry()) && !is_null($asset->getentry()->getReplacedEntryId()))
						self::handleEntryReplacementFileSyncDeletion($fileSync, array(asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET, asset::FILE_SYNC_ASSET_SUB_TYPE_ISM, asset::FILE_SYNC_ASSET_SUB_TYPE_ISMC));
					
					self::conditionalAssetLocalFileSyncsDelete($fileSync, $asset);
				}
			}
		}

		return $dbBatchJob;
	}
	
	/**
	 * 
	 * Handle deleteion of original entries file sync when in the process of entry replacement.
	 * @param FileSync $fileSync
	 * @param array $fileSyncSubTypesToHandle
	 */
	private static function handleEntryReplacementFileSyncDeletion (FileSync $fileSync, $fileSyncSubTypesToHandle)
	{	
		$c = new Criteria();
		$c->add(FileSyncPeer::FILE_TYPE, array (FileSync::FILE_SYNC_FILE_TYPE_URL, FileSync::FILE_SYNC_FILE_TYPE_FILE), Criteria::IN);
		$c->add(FileSyncPeer::OBJECT_TYPE, $fileSync->getObjectType());
		$c->add(FileSyncPeer::OBJECT_SUB_TYPE, $fileSync->getObjectSubType());
		$c->add(FileSyncPeer::PARTNER_ID, $fileSync->getPartnerId());
		$c->add(FileSyncPeer::LINKED_ID, $fileSync->getId());
		
		$originalEntryFileSync = FileSyncPeer::doSelectOne($c);
		if(!$originalEntryFileSync)
		{
			KalturaLog::info("Origianl entry file sync not found with the following details: [object_type, object_sub_type, Partner_id, Linked_id] [" . $fileSync->getObjectType() 
							. ", " . $fileSync->getObjectSubType() . ", " . $fileSync->getPartnerId() . ", " . $fileSync->getId() . "]");
			return;
		}
		
		$originalAssetToDeleteFileSyncFor = assetPeer::retrieveById($originalEntryFileSync->getObjectId());
		if(!$originalAssetToDeleteFileSyncFor)
		{
			KalturaLog::info("Could not find asset matching file sync object id " . $originalEntryFileSync->getObjectId());
			return;
		}
		
		foreach ($fileSyncSubTypesToHandle as $fileSyncSubType)
		{		
			$syncKeyToDelete = $originalAssetToDeleteFileSyncFor->getSyncKey($fileSyncSubType);
			kFileSyncUtils::deleteSyncFileForKey($syncKeyToDelete, false, true);
		}
	}
	
	private static function isAssetExportFinished(FileSync $fileSync, asset $asset)
	{
		$c = new Criteria();
		$c->addAnd ( FileSyncPeer::OBJECT_ID , $fileSync->getObjectId() );
		$c->addAnd ( FileSyncPeer::OBJECT_TYPE , $fileSync->getObjectType() );
		$c->addAnd ( FileSyncPeer::VERSION , $fileSync->getVersion() );
		$c->addAnd ( FileSyncPeer::FILE_TYPE, FileSync::FILE_SYNC_FILE_TYPE_URL);
		$c->addAnd ( FileSyncPeer::DC, $fileSync->getDc());
		$c->addAnd ( FileSyncPeer::STATUS, FileSync::FILE_SYNC_STATUS_PENDING);
		$pendingFileSync = FileSyncPeer::doSelectOne($c);
		if($pendingFileSync)
			return false;
		else
			return true;
	}
	
	private static function conditionalAssetLocalFileSyncsDelete(FileSync $fileSync, asset $asset)
	{
		$unClosedStatuses = array (
			asset::ASSET_STATUS_QUEUED,
			asset::ASSET_STATUS_CONVERTING,
			asset::ASSET_STATUS_WAIT_FOR_CONVERT,
			asset::ASSET_STATUS_EXPORTING
		);
		
		$unClosedAssets = assetPeer::retrieveReadyByEntryId($asset->getEntryId(), null, $unClosedStatuses);
		
		if(count($unClosedAssets))
		{
			$asset->setFileSyncVersionsToDelete(array($fileSync->getVersion()));
			$asset->save();
			return;
		}
		
		$assetsToDelete = assetPeer::retrieveReadyByEntryId($asset->getEntryId());
		
		self::deleteAssetLocalFileSyncsByAssetArray($assetsToDelete);
			
		self::deleteAssetLocalFileSyncs($fileSync->getVersion(), $asset);
	}
	
	private static function deleteAssetLocalFileSyncsByAssetArray($assetsToDeleteArray = array())
	{
		foreach ($assetsToDeleteArray as $assetToDelete)
		{
			/* @var $assetToDelete asset */
			$versionsToDelete =  $assetToDelete->getFileSyncVersionsToDelete();
			KalturaLog::info("file sync versions to delete are " . print_r($versionsToDelete, true));
			if($versionsToDelete)
			{
				foreach ($versionsToDelete as $version)
					self::deleteAssetLocalFileSyncs($version, $assetToDelete);
						
				$assetToDelete->resetFileSyncVersionsToDelete();
				$assetToDelete->save();
			}
		}
	}
	
	private static function deleteAssetLocalFileSyncs($fileSyncVersion, asset $asset)
	{
		$syncKey = $asset->getSyncKey(asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET, $fileSyncVersion);
		kFileSyncUtils::deleteSyncFileForKey($syncKey, false, true);
			
		$syncKey = $asset->getSyncKey(asset::FILE_SYNC_ASSET_SUB_TYPE_ISM, $fileSyncVersion);
		kFileSyncUtils::deleteSyncFileForKey($syncKey, false, true);
			
		$syncKey = $asset->getSyncKey(asset::FILE_SYNC_ASSET_SUB_TYPE_ISMC, $fileSyncVersion);
		kFileSyncUtils::deleteSyncFileForKey($syncKey, false, true);		
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kStorageExportJobData $data
	 * @return BatchJob
	 */
	public static function handleStorageExportFailed(BatchJob $dbBatchJob, kStorageExportJobData $data)
	{
		if ($dbBatchJob->getErrType() == BatchJobErrorTypes::APP && $dbBatchJob->getErrNumber() == BatchJobAppErrors::FILE_ALREADY_EXISTS){
			KalturaLog::notice("remote file already exists");
			return $dbBatchJob;
		}
		$fileSync = FileSyncPeer::retrieveByPK($data->getSrcFileSyncId());
		if(!$fileSync)
		{
			KalturaLog::err("FileSync [" . $data->getSrcFileSyncId() . "] not found");
			return $dbBatchJob;
		}

		$fileSync->setStatus(FileSync::FILE_SYNC_STATUS_ERROR);
		$fileSync->save();

		// if an asset was exported - check if should set its status to ERROR
		$asset = assetPeer::retrieveByFileSync($fileSync);
		if ($asset && $asset->getStatus() == asset::ASSET_STATUS_EXPORTING) // meaning that export is required for asset readiness
		{
            $asset->setStatus(asset::ASSET_STATUS_ERROR);
            $asset->save();

		    if ($asset instanceof flavorAsset)
            {
                $flavorParamsOutput = $asset->getFlavorParamsOutput();
                $flavorParamsOutputId = $flavorParamsOutput ? $flavorParamsOutput->getId() : null;
                $mediaInfo = mediaInfoPeer::retrieveByFlavorAssetId($asset->getId());
                $mediaInfoId = $mediaInfo ? $mediaInfo->getId() : null;
                kBusinessPostConvertDL::handleConvertFailed($dbBatchJob, null, $asset->getId(), $flavorParamsOutputId, $mediaInfoId);
            }
		}


		return $dbBatchJob;
	}

    public static function handleStorageDeleteFinished (BatchJob $dbBatchJob, kStorageDeleteJobData $data)
	{
	    $fileSync = FileSyncPeer::retrieveByPK($data->getSrcFileSyncId());
		if(!$fileSync)
		{
			KalturaLog::err("FileSync [" . $data->getSrcFileSyncId() . "] not found");
			return $dbBatchJob;
		}

		$fileSync->setStatus(FileSync::FILE_SYNC_STATUS_DELETED);
		$fileSync->save();

		return $dbBatchJob;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kConvertProfileJobData $data
	 * @return BatchJob
	 */
	public static function handleConvertProfilePending(BatchJob $dbBatchJob, kConvertProfileJobData $data)
	{
		if($data->getExtractMedia()) // check if extract media required
		{
			// creates extract media job
			kJobsManager::addExtractMediaJob($dbBatchJob, $data->getInputFileSyncLocalPath(), $data->getFlavorAssetId());
		}
		else
		{
			try {
				$conversionsCreated = kBusinessPreConvertDL::decideProfileConvert($dbBatchJob, $dbBatchJob);
			}
			catch (kCoreException $ex) {
				//This was added so the all the assets prior to reaching the limit would still be created
				if ($ex->getCode() != kCoreException::MAX_ASSETS_PER_ENTRY)
					throw $ex;
				
				KalturaLog::err("Max assets per entry was reached continuing with normal flow");
			}

			if($conversionsCreated)
			{
				// handle the source flavor as if it was converted, makes the entry ready according to ready behavior rules
				$currentFlavorAsset = assetPeer::retrieveById($data->getFlavorAssetId());
				if($currentFlavorAsset)
					$dbBatchJob = kBusinessPostConvertDL::handleConvertFinished($dbBatchJob, $currentFlavorAsset);
			}
		}

		// mark the job as almost done
		$dbBatchJob = kJobsManager::updateBatchJob($dbBatchJob, BatchJob::BATCHJOB_STATUS_ALMOST_DONE);

		return $dbBatchJob;
	}

	public static function handleConvertProfileFailed(BatchJob $dbBatchJob, kConvertProfileJobData $data)
	{
		kBatchManager::updateEntry($dbBatchJob->getEntryId(), entryStatus::ERROR_CONVERTING);

		self::deleteTemporaryFlavors($dbBatchJob->getEntryId());
		
		self::handleLocalFileSyncDeletion($dbBatchJob->getEntryId(), $dbBatchJob->getPartner());

		return $dbBatchJob;
	}

	public static function handleConvertProfileFinished(BatchJob $dbBatchJob, kConvertProfileJobData $data)
	{
		self::deleteTemporaryFlavors($dbBatchJob->getEntryId());
		
		self::handleLocalFileSyncDeletion($dbBatchJob->getEntryId(), $dbBatchJob->getPartner());

		kFlowHelper::generateThumbnailsFromFlavor($dbBatchJob->getEntryId(), $dbBatchJob);

		$entry = $dbBatchJob->getEntry();
		if($entry)
		{
			kBusinessConvertDL::checkForPendingLiveClips($entry);

			$clonePendingEntriesArray = $entry->getClonePendingEntries();
			foreach ($clonePendingEntriesArray as $pendingEntryId)
			{
				$pendingEntry = entryPeer::retrieveByPK($pendingEntryId);
				if ( $pendingEntry ) {
					myEntryUtils::copyEntryData($entry, $pendingEntry);
					$pendingEntry->setStatus($entry->getStatus());
					$pendingEntry->setLengthInMsecs($entry->getLengthInMsecs());
					$pendingEntry->save();

				}
			}
			$entry->setClonePendingEntries(array());
			$entry->save();
		}
		
		return $dbBatchJob;
	}

	public static function handleBulkDownloadPending(BatchJob $dbBatchJob, kBulkDownloadJobData $data)
	{
		$entryIds = explode(',', $data->getEntryIds());
		$flavorParamsId = $data->getFlavorParamsId();
		$jobIsFinished = true;
		foreach($entryIds as $entryId)
		{
			$entry = entryPeer::retrieveByPK($entryId);
			if ($entry->getType() != entryType::MEDIA_CLIP || ($entry->getMediaType() != entry::ENTRY_MEDIA_TYPE_AUDIO && $entry->getMediaType() != entry::ENTRY_MEDIA_TYPE_VIDEO))
			{
				continue;
			}
			
			if (is_null($entry))
			{
				KalturaLog::err("Entry id [$entryId] not found.");
			}
			else
			{
				if($entry->hasDownloadAsset($flavorParamsId))
				{
					// why we don't send the notification in case of image is ready?


					$flavorAsset = assetPeer::retrieveByEntryIdAndParams($entryId, $flavorParamsId);
					if ($flavorAsset && $flavorAsset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_READY)
					{
						$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
						$downloadUrl = $flavorAsset->getDownloadUrl();

						$localPath = kFileSyncUtils::getLocalFilePathForKey($syncKey);
						$downloadUrl = $flavorAsset->getDownloadUrl();

						$notificationData = array(
							"puserId" => $entry->getPuserId(),
							"entryId" => $entry->getId(),
							"entryIntId" => $entry->getIntId(),
							"entryVersion" => $entry->getVersion(),
							"fileFormat" => $flavorAsset->getFileExt(),
						//				"email" => '',
							"archivedFile" => $localPath,
							"downoladPath" => $localPath,
							"conversionQuality" => $entry->getConversionQuality(),
							"downloadUrl" => $downloadUrl,
						);

						$extraData = array(
							"data" => json_encode($notificationData),
							"partner_id" => $entry->getPartnerId(),
							"puser_id" => $entry->getPuserId(),
							"entry_id" => $entry->getId(),
							"entry_int_id" => $entry->getIntId(),
							"entry_version" => $entry->getVersion(),
							"file_format" => $flavorAsset->getFileExt(),
						//				"email" => '',
							"archived_file" => $localPath,
							"downolad_path" => $localPath,
							"target" => $localPath,
							"conversion_quality" => $entry->getConversionQuality(),
							"download_url" => $downloadUrl,
							"status" => $entry->getStatus(),
							"abort" => ($dbBatchJob->getExecutionStatus() == BatchJobExecutionStatus::ABORTED),
							"message" => $dbBatchJob->getMessage(),
							"description" => $dbBatchJob->getDescription(),
							"job_type" => BatchJobType::DOWNLOAD,
							"status" => BatchJob::BATCHJOB_STATUS_FINISHED,
							"progress" => 100,
							"debug" => __LINE__,
						);

						myNotificationMgr::createNotification(kNotificationJobData::NOTIFICATION_TYPE_BATCH_JOB_SUCCEEDED, $dbBatchJob , $dbBatchJob->getPartnerId() , null , null ,
							$extraData, $entryId );
					}
				}
				else
				{
					$conversionJob = $entry->createDownloadAsset($dbBatchJob, $flavorParamsId, $data->getPuserId());
					if (!is_null($conversionJob))
						$jobIsFinished = false;
				}
			}
		}

		if ($jobIsFinished)
		{
			// mark the job as finished
			$dbBatchJob = kJobsManager::updateBatchJob($dbBatchJob, BatchJob::BATCHJOB_STATUS_FINISHED);
		}
		else
		{
			// mark the job as almost done
			$dbBatchJob = kJobsManager::updateBatchJob($dbBatchJob, BatchJob::BATCHJOB_STATUS_ALMOST_DONE);
		}

		return $dbBatchJob;
	}


	public static function handleProvisionProvideFinished(BatchJob $dbBatchJob, kProvisionJobData $data)
	{
		kBatchManager::updateEntry($dbBatchJob->getEntryId(), entryStatus::READY);
		$entry = $dbBatchJob->getEntry();
		if(!$entry)
			return $dbBatchJob;

		$data->populateEntryFromData($entry);
		$liveAssets = assetPeer::retrieveByEntryId($entry->getId(),array(assetType::LIVE));
		foreach ($liveAssets as $liveAsset){
			/* @var $liveAsset liveAsset */
			$liveAsset->setStatus(asset::ASSET_STATUS_READY);
			$liveAsset->save();
		}
		$entry->save();
		return $dbBatchJob;
	}

	public static function handleProvisionProvideFailed(BatchJob $dbBatchJob, kProvisionJobData $data)
	{
		kBatchManager::updateEntry($dbBatchJob->getEntryId(), entryStatus::ERROR_CONVERTING);
		return $dbBatchJob;
	}

	public static function handleBulkDownloadFinished(BatchJob $dbBatchJob, kBulkDownloadJobData $data)
	{
		if($dbBatchJob->getExecutionStatus() == BatchJobExecutionStatus::ABORTED)
			return $dbBatchJob;

		$partner = PartnerPeer::retrieveByPK($dbBatchJob->getPartnerId());
		if (!$partner)
		{
			KalturaLog::err("Partner id [".$dbBatchJob->getPartnerId()."] not found, not sending mail");
			return $dbBatchJob;
		}

		$entryIds = explode(",", $data->getEntryIds());
		$flavorParamsId = $data->getFlavorParamsId();
		$links = array();
		foreach($entryIds as $entryId)
		{
			$entry = entryPeer::retrieveByPK($entryId);
			if (is_null($entry))
				continue;
			if ($entry->getType() != entryType::MEDIA_CLIP)
			{
				KalturaLog::info("This entry cannot be downloaded $entryId");
				continue;
			}
			
			$link = $entry->getDownloadAssetUrl($flavorParamsId);

			if (is_null($link))
				$link = "Failed to prepare";
			else
				$link = '<a href="'.$link.'">Download</a>';

			$links[] = $entry->getName() . " - " . $link;
		}
		$linksHtml = implode("<br />", $links);

		// add mail job
		$jobData = new kMailJobData();
		$jobData->setIsHtml(true);
		$jobData->setMailPriority(kMailJobData::MAIL_PRIORITY_NORMAL);
		$jobData->setStatus(kMailJobData::MAIL_STATUS_PENDING);
		if (count($links) <= 1)
			$jobData->setMailType(62);
		else
			$jobData->setMailType(63);

		$jobData->setFromEmail(kConf::get("batch_download_video_sender_email"));
		$jobData->setFromName(kConf::get("batch_download_video_sender_name"));

		$adminName = $partner->getAdminName();
		$recipientEmail = $partner->getAdminEmail();

		$kuser = kuserPeer::getKuserByPartnerAndUid($dbBatchJob->getPartnerId(), $data->getPuserId());
		if ($kuser)
		{
			$recipientEmail = $kuser->getEmail();
			$adminName = $kuser->getFullName();
		}

		if(!$adminName)
			$adminName = $recipientEmail;

		$jobData->setSeparator(self::BULK_DOWNLOAD_EMAIL_PARAMS_SEPARATOR);
		$jobData->setBodyParamsArray(array($adminName, $linksHtml));
		$jobData->setRecipientEmail($recipientEmail);
		$jobData->setSubjectParamsArray(array());

		kJobsManager::addJob($dbBatchJob->createChild(BatchJobType::MAIL, $jobData->getMailType()), $jobData, BatchJobType::MAIL, $jobData->getMailType());

		return $dbBatchJob;
	}

	/**
	 * @param UploadToken $uploadToken
	 */
	public static function handleUploadFailed(UploadToken $uploadToken)
	{
		$uploadToken->setStatus(UploadToken::UPLOAD_TOKEN_DELETED);
		$uploadToken->save();

		if($uploadToken->getObjectType() == FileAssetPeer::OM_CLASS)
		{
			$dbFileAsset = FileAssetPeer::retrieveByPK($uploadToken->getObjectId());
			if(!$dbFileAsset)
			{
				KalturaLog::err("File asset id [" . $uploadToken->getObjectId() . "] not found");
				return;
			}

			if($dbFileAsset->getStatus() == FileAssetStatus::UPLOADING)
			{
				$dbFileAsset->setStatus(FileAssetStatus::ERROR);
				$dbFileAsset->save();
			}

			return;
		}
		
		if(is_subclass_of($uploadToken->getObjectType(), assetPeer::OM_CLASS))
		{
			$dbAsset = assetPeer::retrieveById($uploadToken->getObjectId());
			if(!$dbAsset)
			{
				KalturaLog::err("Asset id [" . $uploadToken->getObjectId() . "] not found");
				return;
			}

			if($dbAsset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_IMPORTING)
			{
				$dbAsset->setStatus(asset::ASSET_STATUS_ERROR);
				$dbAsset->save();
			}

			$profile = null;
			try{
				$profile = myPartnerUtils::getConversionProfile2ForEntry($dbAsset->getEntryId());
			}
			catch(Exception $e)
			{
				KalturaLog::err($e->getMessage());
				return;
			}

			$currentReadyBehavior = kBusinessPostConvertDL::getReadyBehavior($dbAsset, $profile);
			if($currentReadyBehavior == flavorParamsConversionProfile::READY_BEHAVIOR_REQUIRED)
				kBatchManager::updateEntry($dbAsset->getEntryId(), entryStatus::ERROR_IMPORTING);

			return;
		}

		if($uploadToken->getObjectType() == entryPeer::OM_CLASS)
		{
			$dbEntry = entryPeer::retrieveByPK($uploadToken->getObjectId());
			if($dbEntry && $dbEntry->getStatus() == entryStatus::IMPORT)
				kBatchManager::updateEntry($dbEntry->getId(), entryStatus::ERROR_IMPORTING);
		}
	}

	/**
	 * @param UploadToken $uploadToken
	 */
	public static function handleUploadCanceled(UploadToken $uploadToken)
	{
		$dbEntry = null;

		if($uploadToken->getObjectType() == entryPeer::OM_CLASS)
			$dbEntry = entryPeer::retrieveByPK($uploadToken->getObjectId());
	
		if($uploadToken->getObjectType() == FileAssetPeer::OM_CLASS)
		{
			$dbFileAsset = FileAssetPeer::retrieveByPK($uploadToken->getObjectId());
			if(!$dbFileAsset)
			{
				KalturaLog::err("File asset id [" . $uploadToken->getObjectId() . "] not found");
				return;
			}

			if($dbFileAsset->getStatus() == FileAssetStatus::UPLOADING)
			{
				$dbFileAsset->setStatus(FileAssetStatus::PENDING);
				$dbFileAsset->save();
			}
			return;
		}
		
		if(is_subclass_of($uploadToken->getObjectType(), assetPeer::OM_CLASS))
		{
			$dbAsset = assetPeer::retrieveById($uploadToken->getObjectId());
			if(!$dbAsset)
			{
				KalturaLog::err("Asset id [" . $uploadToken->getObjectId() . "] not found");
				return;
			}

			if($dbAsset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_IMPORTING)
			{
				$dbAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_QUEUED);
				$dbAsset->save();
			}

			$dbEntry = $dbAsset->getentry();
		}

		if($dbEntry && $dbEntry->getStatus() == entryStatus::IMPORT)
		{
			$status = entryStatus::NO_CONTENT;
			$entryFlavorAssets = assetPeer::retrieveFlavorsByEntryId($dbEntry->getId());
			foreach($entryFlavorAssets as $entryFlavorAsset)
			{
				/* @var $entryFlavorAsset flavorAsset */

				if($entryFlavorAsset->getStatus() == asset::FLAVOR_ASSET_STATUS_READY && $status == entryStatus::NO_CONTENT)
					$status = entryStatus::PENDING;

				if($entryFlavorAsset->getStatus() == asset::FLAVOR_ASSET_STATUS_IMPORTING && $status != entryStatus::PRECONVERT)
					$status = entryStatus::IMPORT;

				if($entryFlavorAsset->getStatus() == asset::FLAVOR_ASSET_STATUS_CONVERTING)
					$status = entryStatus::PRECONVERT;
			}

			$dbEntry->setStatus($status);
			$dbEntry->save();
		}
	}

	/**
	 * @param UploadToken $uploadToken
	 */
	public static function handleUploadFinished(UploadToken $uploadToken)
	{
		if(!is_subclass_of($uploadToken->getObjectType(), assetPeer::OM_CLASS) && $uploadToken->getObjectType() != FileAssetPeer::OM_CLASS && $uploadToken->getObjectType() != entryPeer::OM_CLASS)
		{
			KalturaLog::info("Class [" . $uploadToken->getObjectType() . "] not supported");
			return;
		}

		$fullPath = kUploadTokenMgr::getFullPathByUploadTokenId($uploadToken->getId());

		if(!file_exists($fullPath))
		{
			KalturaLog::info("File path [$fullPath] not found");
			$remoteDCHost = kUploadTokenMgr::getRemoteHostForUploadToken($uploadToken->getId(), kDataCenterMgr::getCurrentDcId());
			if(!$remoteDCHost)
			{
				KalturaLog::err("File path [$fullPath] could not be redirected");
				return;
			}

			kFileUtils::dumpApiRequest($remoteDCHost);
		}
	
		if($uploadToken->getObjectType() == FileAssetPeer::OM_CLASS)
		{
			$dbFileAsset = FileAssetPeer::retrieveByPK($uploadToken->getObjectId());
			if(!$dbFileAsset)
			{
				KalturaLog::err("File asset id [" . $uploadToken->getObjectId() . "] not found");
				return;
			}

			if(!$dbFileAsset->getFileExt())
				$dbFileAsset->setFileExt(pathinfo($fullPath, PATHINFO_EXTENSION));
				
			$dbFileAsset->incrementVersion();
			$dbFileAsset->save();

			$syncKey = $dbFileAsset->getSyncKey(FileAsset::FILE_SYNC_ASSET);

			try {
				kFileSyncUtils::moveFromFile($fullPath, $syncKey, true);
			}
			catch (Exception $e) {
				$dbFileAsset->setStatus(FileAssetStatus::ERROR);
				$dbFileAsset->save();
				throw $e;
			}

			if($dbFileAsset->getStatus() == FileAssetStatus::UPLOADING)
			{
				$finalPath = kFileSyncUtils::getLocalFilePathForKey($syncKey);
				$dbFileAsset->setSize(kFile::fileSize($finalPath));
				$dbFileAsset->setStatus(FileAssetStatus::READY);
				$dbFileAsset->save();
			}

			$uploadToken->setStatus(UploadToken::UPLOAD_TOKEN_CLOSED);
			$uploadToken->save();
			
			KalturaLog::info("File asset [" . $dbFileAsset->getId() . "] handled");
			return;
		}
		
		if(is_subclass_of($uploadToken->getObjectType(), assetPeer::OM_CLASS))
		{
			$dbAsset = assetPeer::retrieveById($uploadToken->getObjectId());
			if(!$dbAsset)
			{
				KalturaLog::err("Asset id [" . $uploadToken->getObjectId() . "] not found");
				return;
			}

			$ext = pathinfo($fullPath, PATHINFO_EXTENSION);
			$dbAsset->setFileExt($ext);
			$dbAsset->incrementVersion();
			$dbAsset->save();

			$syncKey = $dbAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);

			try {
				kFileSyncUtils::moveFromFile($fullPath, $syncKey, true);
			}
			catch (Exception $e) {
				if($dbAsset instanceof flavorAsset)
					kBatchManager::updateEntry($dbAsset->getEntryId(), entryStatus::ERROR_IMPORTING);

				$dbAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_ERROR);
				$dbAsset->save();
				throw $e;
			}

			if($dbAsset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_IMPORTING)
			{
				$finalPath = kFileSyncUtils::getLocalFilePathForKey($syncKey);
				$dbAsset->setSize(kFile::fileSize($finalPath));

				if($dbAsset instanceof flavorAsset)
				{
					if($dbAsset->getIsOriginal())
						$dbAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_QUEUED);
					else
						$dbAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_VALIDATING);
				}
				else
				{
					$dbAsset->setStatus(thumbAsset::FLAVOR_ASSET_STATUS_READY);
				}

				if($dbAsset instanceof thumbAsset)
				{
					list($width, $height, $type, $attr) = getimagesize($finalPath);
					$dbAsset->setWidth($width);
					$dbAsset->setHeight($height);
				}

				$dbAsset->save();
				kEventsManager::raiseEvent(new kObjectAddedEvent($dbAsset));
			}

			$uploadToken->setStatus(UploadToken::UPLOAD_TOKEN_CLOSED);
			$uploadToken->save();
		}

		if($uploadToken->getObjectType() == entryPeer::OM_CLASS)
		{
			$dbEntry = entryPeer::retrieveByPK($uploadToken->getObjectId());
			if(!$dbEntry)
			{
				KalturaLog::err("Entry id [" . $uploadToken->getObjectId() . "] not found");
				return;
			}
			
			//Keep original extention
			$ext = pathinfo($fullPath, PATHINFO_EXTENSION);
			// increments version
			$dbEntry->setData('100000.'.$ext);
			$dbEntry->save();

			$syncKey = $dbEntry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA);
			try
			{
				kFileSyncUtils::moveFromFile($fullPath, $syncKey, true);
			}
			catch (Exception $e) {

				if($dbAsset instanceof flavorAsset)
					kBatchManager::updateEntry($dbEntry->getId(), entryStatus::ERROR_IMPORTING);

				throw $e;
			}
			$dbEntry->setStatus(entryStatus::READY);
			$dbEntry->save();

			$uploadToken->setStatus(UploadToken::UPLOAD_TOKEN_CLOSED);
			$uploadToken->save();
		}
	}

	/**
	 * @param entry $tempEntry
	 */
	public static function handleEntryReplacement(entry $tempEntry)
	{
		$entry = entryPeer::retrieveByPK($tempEntry->getReplacedEntryId());
		if(!$entry)
		{
			KalturaLog::err("Real entry id [" . $tempEntry->getReplacedEntryId() . "] not found");
			myEntryUtils::deleteEntry($tempEntry,null,true);
			return;
		}

		if ( $tempEntry->getStatus() == entryStatus::ERROR_CONVERTING )
		{
			$entry->setReplacementStatus(entryReplacementStatus::FAILED);
			$entry->save();

			// NOTE: KalturaEntryService::cancelReplace() must be used to reset this status and delete the temp entry

			return;
		}

		switch($entry->getReplacementStatus())
		{
			case entryReplacementStatus::APPROVED_BUT_NOT_READY:
				KalturaLog::log("status changed to ready");
				kEventsManager::raiseEventDeferred(new kObjectReadyForReplacmentEvent($tempEntry));
				break;

			case entryReplacementStatus::READY_BUT_NOT_APPROVED:
				break;

			case entryReplacementStatus::NOT_READY_AND_NOT_APPROVED:
				$entry->setReplacementStatus(entryReplacementStatus::READY_BUT_NOT_APPROVED);
				$entry->save();
				break;

			case entryReplacementStatus::FAILED:
				// Do nothing. KalturaEntryService::cancelReplace() will be used to delete the entry.
				break;

			case entryReplacementStatus::NONE:
			default:
				KalturaLog::err("Real entry id [" . $tempEntry->getReplacedEntryId() . "] replacement canceled");
				myEntryUtils::deleteEntry($tempEntry,null,true);
				break;
		}
	}

	public static function handleIndexPending(BatchJob $dbBatchJob, kIndexJobData $data)
	{
		$featureStatusesToRemove = $data->getFeatureStatusesToRemove();

		foreach($featureStatusesToRemove as $featureStatusToRemove)
		{
			if(!($featureStatusToRemove instanceof kFeatureStatus))
				continue;

			$dbBatchJob->getPartner()->resetFeaturesStatusByType($featureStatusToRemove->getType());
		}
		return $dbBatchJob;
	}

	public static function handleIndexFinished(BatchJob $dbBatchJob, kIndexJobData $data)
	{
		$featureStatusesToRemove = $data->getFeatureStatusesToRemove();
		foreach($featureStatusesToRemove as $featureStatusToRemove)
		{
			if(!($featureStatusToRemove instanceof kFeatureStatus))
				continue;

			$dbBatchJob->getPartner()->resetFeaturesStatusByType($featureStatusToRemove->getType());
		}

		return $dbBatchJob;
	}

	public static function handleIndexFailed(BatchJob $dbBatchJob, kIndexJobData $data)
	{
		$featureStatusesToRemove = $data->getFeatureStatusesToRemove();
		foreach($featureStatusesToRemove as $featureStatusToRemove)
		{
			if(!($featureStatusToRemove instanceof kFeatureStatus))
				continue;

			$dbBatchJob->getPartner()->resetFeaturesStatusByType($featureStatusToRemove->getType());
		}

		return $dbBatchJob;
	}
	
	protected static function createLiveReportExportDownloadUrl ($partner_id, $file_name, $expiry, $applicationUrlTemplate)
	{
		// Extract simple download name
		$regex = "/^{$partner_id}_Export_[a-zA-Z0-9]+_(?<fileName>[\w\-]+.csv)$/";
		if(!preg_match($regex, $file_name, $matches)) {
			KalturaLog::err("File name doesn't match expected format");
			return null;
		}
		$downloadName = $matches['fileName'];
		
		// Add dc to enable redirection
		$dc = kDataCenterMgr::getCurrentDc();
		$file_name = $dc['id'] . "_" . $file_name;
		
		$ksStr = "";
		$partner = PartnerPeer::retrieveByPK ( $partner_id );
		$secret = $partner->getSecret ();
		$privilege = ks::PRIVILEGE_DOWNLOAD . ":" . $file_name;

		$ksStr = kSessionBase::generateSession($partner->getKSVersion(), $partner->getAdminSecret(), null, ks::TYPE_KS, $partner_id, $expiry, $privilege);

		if ($applicationUrlTemplate) {
			$url = str_replace("[ks]", $ksStr, $applicationUrlTemplate);
			$url = str_replace("[id]", $file_name, $url);
		}
		else {
			//url is built with DC url in order to be directed to the same DC of the saved file
			$url = kDataCenterMgr::getCurrentDcUrl() . "/api_v3/index.php/service/liveReports/action/serveReport/ks/$ksStr/id/$file_name/$downloadName";
		}
		return $url;
	}
	
	public static function handleLiveReportExportFinished(BatchJob $dbBatchJob, kLiveReportExportJobData $data) {
		
		// Move file from shared temp to it's final location
		$fileName =  basename($data->outputPath);
		$directory =  myContentStorage::getFSContentRootPath() . "/content/reports/live/" . $dbBatchJob->getPartnerId() ;
		$filePath = $directory . DIRECTORY_SEPARATOR . $fileName;
		
		$moveFile = kFile::moveFile($data->outputPath, $filePath);
		if(!$moveFile) {
			KalturaLog::err("Failed to move report file from: " . $data->outputPath . " to: " . $filePath);
			return kFlowHelper::handleLiveReportExportFailed($dbBatchJob, $data);
		} 
		
		$data->outputPath = $filePath;
		$dbBatchJob->setData($data);
		$dbBatchJob->save();

		$expiry = kConf::get("live_report_export_expiry", 'local', self::LIVE_REPORT_EXPIRY_TIME);
		// Create download URL
		$url = self::createLiveReportExportDownloadUrl($dbBatchJob->getPartnerId(), $fileName, $expiry, $data->applicationUrlTemplate);
		if(!$url) {
			KalturaLog::err("Failed to create download URL");
			return kFlowHelper::handleLiveReportExportFailed($dbBatchJob, $data);
		}
		
		// Create email params
		$time = date("m-d-y H:i", $data->timeReference + $data->timeZoneOffset); 
		$email_id = MailType::MAIL_TYPE_LIVE_REPORT_EXPORT_SUCCESS;
		$validUntil = date("m-d-y H:i", $data->timeReference + $expiry + $data->timeZoneOffset);
		$expiryInDays = $expiry / 60 / 60 / 24;
		$params = array($dbBatchJob->getPartner()->getName(), $time, $dbBatchJob->getId(), $url, $expiryInDays, $validUntil);
		$titleParams = array($time);
		
		
		// Email it all
		kJobsManager::addMailJob(
				null,
				0,
				$dbBatchJob->getPartnerId(),
				$email_id,
				kMailJobData::MAIL_PRIORITY_NORMAL,
				kConf::get( "live_report_sender_email" ),
				kConf::get( "live_report_sender_name" ),
				$data->recipientEmail,
				$params,
				$titleParams
		);
		
		return $dbBatchJob;
	}
	
	public static function handleLiveReportExportFailed(BatchJob $dbBatchJob, kLiveReportExportJobData $data) {
	
		$time = date("m-d-y H:i", $data->timeReference + $data->timeZoneOffset);
		$email_id = MailType::MAIL_TYPE_LIVE_REPORT_EXPORT_FAILURE;
		$params = array($dbBatchJob->getPartner()->getName(), $time, $dbBatchJob->getId(),
				$dbBatchJob->getErrType(), $dbBatchJob->getErrNumber());
		$titleParams = array($time);
	
		kJobsManager::addMailJob(
				null,
				0,
				$dbBatchJob->getPartnerId(),
				$email_id,
				kMailJobData::MAIL_PRIORITY_NORMAL,
				kConf::get( "live_report_sender_email" ),
				kConf::get( "live_report_sender_name" ),
				$data->recipientEmail,
				$params,
				$titleParams
		);
		return $dbBatchJob;
	}
	
	public static function handleLiveReportExportAborted(BatchJob $dbBatchJob, kLiveReportExportJobData $data) {
	
		$time = date("m-d-y H:i", $data->timeReference + $data->timeZoneOffset);
		$email_id = MailType::MAIL_TYPE_LIVE_REPORT_EXPORT_ABORT;
		$params = array($dbBatchJob->getPartner()->getName(), $time, $dbBatchJob->getId());
		$titleParams = array($time);
	
		kJobsManager::addMailJob(
				null,
				0,
				$dbBatchJob->getPartnerId(),
				$email_id,
				kMailJobData::MAIL_PRIORITY_NORMAL,
				kConf::get( "live_report_sender_email" ),
				kConf::get( "live_report_sender_name" ),
				$data->recipientEmail,
				$params,
				$titleParams
		);
		return $dbBatchJob;
	}
	
	private static function deleteTemporaryFlavors($entryId)
	{
		$originalflavorAsset = assetPeer::retrieveOriginalByEntryId($entryId);
		if($originalflavorAsset && $originalflavorAsset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_TEMP)
		{
			$originalflavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_DELETED);
			$originalflavorAsset->setDeletedAt(time());
			$originalflavorAsset->save();
		}

		$tempFlavorsParams = flavorParamsConversionProfilePeer::getTempFlavorsParams($entryId);
		if (!$tempFlavorsParams)
			return;

		foreach ($tempFlavorsParams as $tempFlavorsParam) 
		{
			$tempFlavorAsset = assetPeer::retrieveByEntryIdAndParams($entryId, $tempFlavorsParam->getFlavorParamsId());
			if($tempFlavorAsset)
			{
				$tempFlavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_DELETED);
				$tempFlavorAsset->setDeletedAt(time());
				$tempFlavorAsset->save();
			}
		}
	}
	
	private static function handleLocalFileSyncDeletion($entryId, Partner $partner)
	{
		if($partner && $partner->getStorageDeleteFromKaltura())
		{
			$readyAssets = assetPeer::retrieveReadyFlavorsByEntryId($entryId);
			self::deleteAssetLocalFileSyncsByAssetArray($readyAssets);
		}
	}
	
	private static function activateConvertProfileJob($entryId, $localFilePath)
	{
		$c = new Criteria();
		$c->add ( BatchJobPeer::ENTRY_ID , $entryId );
		$c->add ( BatchJobPeer::JOB_TYPE , BatchJobType::CONVERT_PROFILE );
		$c->add ( BatchJobPeer::STATUS, BatchJob::BATCHJOB_STATUS_DONT_PROCESS);
		
		$batchJob = BatchJobPeer::doSelectOne( $c );	
		if($batchJob)
		{
			$data = $batchJob->getData();
			$data->setInputFileSyncLocalPath($localFilePath);
			$batchJob->setData($data);
			kJobsManager::updateBatchJob($batchJob, BatchJob::BATCHJOB_STATUS_PENDING);
			return true;
		}
		else 
			return false;
	}

	public static function handleUsersCsvFinished(BatchJob $dbBatchJob, kUsersCsvJobData $data)
	{
		// Move file from shared temp to it's final location
		$fileName =  basename($data->getOutputPath());
		$directory =  myContentStorage::getFSContentRootPath() . "/content/userscsv/" . $dbBatchJob->getPartnerId() ;
		if(!file_exists($directory))
			mkdir($directory);
		$filePath = $directory . DIRECTORY_SEPARATOR . $fileName;

		$moveFile = kFile::moveFile($data->getOutputPath, $filePath);
		if(!$moveFile)
			KalturaLog::err("Failed to move users csv file from: " . $data->getOutputPath() . " to: " . $filePath);

		$data->setOutputPath($filePath);
		$dbBatchJob->setData($data);
		$dbBatchJob->save();

		KalturaLog::info("file path: [$filePath]");

		$downloadUrl = self::createUsersCsvDownloadUrl($dbBatchJob->getPartnerId(), $fileName);
		$userName = $data->getUserName();
		$bodyParams = array($userName, $downloadUrl);

		//send the created csv by mail
		kJobsManager::addMailJob(
			null,
			0,
			$dbBatchJob->getPartnerId(),
			MailType::MAIL_TYPE_USERS_CSV,
			kMailJobData::MAIL_PRIORITY_NORMAL,
			kConf::get("partner_notification_email"),
			kConf::get("partner_notification_name"),
			$data->getUserMail(),
			$bodyParams
		);

		return $dbBatchJob;
	}


	protected static function createUsersCsvDownloadUrl ($partner_id, $file_name)
	{
		$ksStr = "";
		$partner = PartnerPeer::retrieveByPK ($partner_id);
		$secret = $partner->getSecret ();
		$privilege = ks::PRIVILEGE_DOWNLOAD . ":" . $file_name;
		//ks will expire after 3 hours
		$expiry = 10800;
		$result = kSessionUtils::startKSession($partner_id, $secret, null, $ksStr, $expiry, false, "", $privilege);

		if ($result < 0)
			throw new Exception ("Failed to generate session for partner [" . $partner . "]");

		//url is built with DC url in order to be directed to the same DC of the saved file
		$url = kDataCenterMgr::getCurrentDcUrl() . "/api_v3/index.php/service/user/action/serveCsv/ks/$ksStr/id/$file_name";

		return $url;
	}


}
