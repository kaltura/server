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
		$flavorAsset->setIsOriginal(true);
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
		KalturaLog::debug("Import retried, with file: " . $data->getDestFileLocalPath());

		if($dbBatchJob->getExecutionStatus() == BatchJobExecutionStatus::ABORTED)
			return $dbBatchJob;

		if(!$data->getFlavorAssetId())
			return $dbBatchJob;

		$dbFlavorAsset = assetPeer::retrieveById($data->getFlavorAssetId());
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
		KalturaLog::debug("Import finished, with file: " . $data->getDestFileLocalPath());

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

		$flavorAsset->setFileExt($ext);
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
		
		if(($isNewContent || $dbEntry->getStatus() == entryStatus::IMPORT) && !$convertProfileExist)
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
	 * @param kExtractMediaJobData $data
	 * @return BatchJob
	 */
	public static function handleExtractMediaClosed(BatchJob $dbBatchJob, kExtractMediaJobData $data)
	{
		KalturaLog::debug("Extract media closed");

		if($dbBatchJob->getExecutionStatus() == BatchJobExecutionStatus::ABORTED)
			return $dbBatchJob;

		$rootBatchJob = $dbBatchJob->getRootJob();
		if(!$rootBatchJob)
			return $dbBatchJob;

		if($dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FINISHED)
		{
			$entry = entryPeer::retrieveByPKNoFilter($dbBatchJob->getEntryId());
			if($entry->getStatus() != entryStatus::READY && $entry->getStatus() != entryStatus::DELETED)
				kBatchManager::updateEntry($dbBatchJob->getEntryId(), entryStatus::PRECONVERT);
		}

		if($rootBatchJob->getJobType() == BatchJobType::CONVERT_PROFILE)
		{
			kBusinessPreConvertDL::decideProfileConvert($dbBatchJob, $rootBatchJob, $data->getMediaInfoId());

			// handle the source flavor as if it was converted, makes the entry ready according to ready behavior rules
			$currentFlavorAsset = assetPeer::retrieveById($data->getFlavorAssetId());
			if($currentFlavorAsset && $currentFlavorAsset->getStatus() == asset::FLAVOR_ASSET_STATUS_READY)
				$dbBatchJob = kBusinessPostConvertDL::handleConvertFinished($dbBatchJob, $currentFlavorAsset);
		}

		return $dbBatchJob;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kConvertJobData $data
	 * @return BatchJob
	 */
	public static function handleConvertPending(BatchJob $dbBatchJob, kConvertJobData $data)
	{
		KalturaLog::debug("Convert created with source file: " . $data->getSrcFileSyncLocalPath());

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
		KalturaLog::debug("Convert collection created with source file: " . $data->getSrcFileSyncLocalPath());

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
		KalturaLog::debug("Convert finished with destination file: " . $data->getDestFileSyncLocalPath());

		if($dbBatchJob->getExecutionStatus() == BatchJobExecutionStatus::ABORTED)
			return $dbBatchJob;

		// verifies that flavor asset created
		if(!$data->getFlavorAssetId())
			throw new APIException(APIErrors::INVALID_FLAVOR_ASSET_ID, $data->getFlavorAssetId());

		$flavorAsset = assetPeer::retrieveById($data->getFlavorAssetId());
		// verifies that flavor asset exists
		if(!$flavorAsset)
			throw new APIException(APIErrors::INVALID_FLAVOR_ASSET_ID, $data->getFlavorAssetId());

		$flavorAsset->incrementVersion();
		$flavorAsset->save();

		$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);

		$flavorParamsOutput = $data->getFlavorParamsOutput();
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
			KalturaLog::debug("Convert archived file to: " . $data->getDestFileSyncLocalPath());

			// save the data changes to the db
			$dbBatchJob->setData($data);
			$dbBatchJob->save();
		}

		$entry = $dbBatchJob->getEntry();
		if(!$entry)
			throw new APIException(APIErrors::INVALID_ENTRY, 'entry', $dbBatchJob->getEntryId());


		$offset = $entry->getThumbOffset(); // entry getThumbOffset now takes the partner DefThumbOffset into consideration

		$createThumb = $entry->getCreateThumb();
		$extractMedia = true;

		if($entry->getType() != entryType::MEDIA_CLIP) // e.g. document
			$extractMedia = false;

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

		$operatorSet = new kOperatorSets();
		$operatorSet->setSerialized(stripslashes($flavorParamsOutput->getOperators()));
		//		KalturaLog::debug("Operators: ".$flavorParamsOutput->getOperators()
		//			."\ngetCurrentOperationSet:".$data->getCurrentOperationSet()
		//			."\ngetCurrentOperationIndex:".$data->getCurrentOperationIndex());
		//		KalturaLog::debug("Operators set: " . print_r($operatorSet, true));
		$nextOperator = $operatorSet->getOperator($data->getCurrentOperationSet(), $data->getCurrentOperationIndex() + 1);

		$nextJob = null;
		if($nextOperator)
		{
			//			KalturaLog::debug("Found next operator");
			//Note: consequent operators doesn't support at the moment conversion based on outputs of multiple sources
			$nextJob = kJobsManager::addFlavorConvertJob(array($syncKey), $flavorParamsOutput, $data->getFlavorAssetId(), null,
					$data->getMediaInfoId(), $dbBatchJob, $dbBatchJob->getJobSubType());
		}

		if(!$nextJob)
		{
			if($createThumb || $extractMedia)
			{
				$postConvertAssetType = BatchJob::POSTCONVERT_ASSET_TYPE_FLAVOR;
				if($flavorAsset->getIsOriginal())
					$postConvertAssetType = BatchJob::POSTCONVERT_ASSET_TYPE_SOURCE;

				kJobsManager::addPostConvertJob($dbBatchJob, $postConvertAssetType, $data->getDestFileSyncLocalPath(), $data->getFlavorAssetId(), $flavorParamsOutput->getId(), $createThumb, $offset);
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

		// this logic decide when a thumbnail should be created
		if($rootBatchJob && $rootBatchJob->getJobType() == BatchJobType::BULKDOWNLOAD)
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
		return $dbBatchJob;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kCaptureThumbJobData $data
	 * @return BatchJob
	 */
	public static function handleCaptureThumbFinished(BatchJob $dbBatchJob, kCaptureThumbJobData $data)
	{
		KalturaLog::debug("Capture thumbnail finished with destination file: " . $data->getThumbPath());

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
		KalturaLog::debug("Thumbnail archived file to: " . $data->getThumbPath());

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
			kFlowHelper::generateThumbnailsFromFlavor($dbBatchJob->getEntryId(), $dbBatchJob, $thumbAsset->getFlavorParamsId());

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
		KalturaLog::debug("Convert failed with destination file: " . $data->getDestFileSyncLocalPath());

		if($dbBatchJob->getExecutionStatus() == BatchJobExecutionStatus::ABORTED)
			return $dbBatchJob;

		// verifies that flavor asset created
		if(!$data->getFlavorAssetId())
			throw new APIException(APIErrors::INVALID_FLAVOR_ASSET_ID, $data->getFlavorAssetId());

		$flavorAsset = assetPeer::retrieveById($data->getFlavorAssetId());
		// verifies that flavor asset exists
		if(!$flavorAsset)
			throw new APIException(APIErrors::INVALID_FLAVOR_ASSET_ID, $data->getFlavorAssetId());

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
		KalturaLog::debug("Captura thumbnail failed with destination file: " . $data->getThumbPath());

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

		return $dbBatchJob;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kPostConvertJobData $data
	 * @return BatchJob
	 */
	public static function handlePostConvertFailed(BatchJob $dbBatchJob, kPostConvertJobData $data)
	{
		KalturaLog::debug("Post Convert failed for flavor params output: " . $data->getFlavorParamsOutputId());

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
		KalturaLog::debug("File delete finished for file path: ". $data->getLocalFileSyncPath().", data center: ".$dbBatchJob->getDc());

		//Change status of the filesync to "purged"
		$fileSyncFroDeletedFile = kFileSyncUtils::retrieveObjectForSyncKey($data->getSyncKey());
		$fileSyncFroDeletedFile->setStatus(FileSync::FILE_SYNC_STATUS_PURGED);
		$fileSyncFroDeletedFile->save();

		return $dbBatchJob;
	}

	public static function handleDeleteFileProcessing (kDeleteFileJobData $data)
	{
		KalturaLog::info("Delete started for file path " . $data->getLocalFileSyncPath());
		$fileSyncFroDeletedFile = kFileSyncUtils::retrieveObjectForSyncKey($data->getSyncKey());
		$fileSyncFroDeletedFile->setStatus(FileSync::FILE_SYNC_STATUS_DELETED);
		$fileSyncFroDeletedFile->save();
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kConvertCollectionJobData $data
	 * @return BatchJob
	 */
	public static function handleConvertCollectionFinished(BatchJob $dbBatchJob, kConvertCollectionJobData $data)
	{
		KalturaLog::debug("Convert Collection finished for entry id: " . $dbBatchJob->getEntryId());

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
			KalturaLog::debug("Convert archived file to: " . $flavor->getDestFileSyncLocalPath());
			$newName = basename($flavor->getDestFileSyncLocalPath());
			KalturaLog::debug("Editing ISM [$oldName] to [$newName]");
			$ismContent = str_replace("src=\"$oldName\"", "src=\"$newName\"", $ismContent);

			// creating post convert job (without thumb)
			$postConvertAssetType = BatchJob::POSTCONVERT_ASSET_TYPE_FLAVOR;
			kJobsManager::addPostConvertJob($dbBatchJob, $postConvertAssetType, $flavor->getDestFileSyncLocalPath(), $flavor->getFlavorAssetId(), $flavor->getFlavorParamsOutputId(), file_exists($thumbPath), $offset);

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
		KalturaLog::debug("Editing ISM [$oldName] to [$newName]");
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
		KalturaLog::debug("Convert Collection failed for entry id: " . $dbBatchJob->getEntryId());

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
		KalturaLog::debug("Post Convert finished with thumnail: " . $data->getThumbPath());

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

			KalturaLog::debug("Saving thumbnail from: " . $data->getThumbPath());
			// creats thumbnail the file sync
			$entry = $dbBatchJob->getEntry(false, false);
			if(!$entry)
			{
				KalturaLog::err("Entry not found [" . $dbBatchJob->getEntryId() . "]");
				return;
			}

			KalturaLog::debug("Entry duration: " . $entry->getLengthInMsecs());
			if(!$entry->getLengthInMsecs())
			{
				KalturaLog::debug("Copy duration from flvor asset: " . $data->getFlavorAssetId());
				$mediaInfo = mediaInfoPeer::retrieveByFlavorAssetId($data->getFlavorAssetId());
				if($mediaInfo)
				{
					KalturaLog::debug("Set duration to: " . $mediaInfo->getContainerDuration());
					$entry->setDimensions($mediaInfo->getVideoWidth(), $mediaInfo->getVideoHeight());
					
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
					KalturaLog::debug("Start conversion");
					$path = kFileSyncUtils::getLocalFilePathForKey($syncKey);

					$entry = $dbBatchJob->getEntry();
					if($entry)
						kJobsManager::addConvertProfileJob(null, $entry, $currentFlavorAsset->getId(), $path);
				}
				else
				{
					KalturaLog::debug("File sync not created yet");
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
		kJobsManager::addMailJob(
			null,
			0,
			$dbBatchJob->getPartnerId(),
			$email_id,
			kMailJobData::MAIL_PRIORITY_NORMAL,
			kConf::get( "batch_alert_email" ),
			kConf::get( "batch_alert_name" ),
			$dbBatchJob->getPartner()->getBulkUploadNotificationsEmail(),
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
		KalturaLog::debug("Export to storage finished for sync file[" . $data->getSrcFileSyncId() . "]");

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
		if ($asset && in_array($asset->getStatus(), array(asset::ASSET_STATUS_EXPORTING, asset::ASSET_STATUS_ERROR)))
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
				$syncKey = kFileSyncUtils::getKeyForFileSync($fileSync);
				kFileSyncUtils::deleteSyncFileForKey($syncKey, false, true);
			}
		}

		return $dbBatchJob;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kStorageExportJobData $data
	 * @return BatchJob
	 */
	public static function handleStorageExportFailed(BatchJob $dbBatchJob, kStorageExportJobData $data)
	{
		KalturaLog::debug("Export to storage failed for sync file[" . $data->getSrcFileSyncId() . "]");
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
	    KalturaLog::debug("Remote storage file deletion finished for fileysnc ID:[ ". $data->getSrcFileSyncId() ."]");

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
		KalturaLog::debug("Convert Profile created, with input file: " . $data->getInputFileSyncLocalPath());

		if($data->getExtractMedia()) // check if extract media required
		{
			// creates extract media job
			kJobsManager::addExtractMediaJob($dbBatchJob, $data->getInputFileSyncLocalPath(), $data->getFlavorAssetId());
		}
		else
		{
			$conversionsCreated = kBusinessPreConvertDL::decideProfileConvert($dbBatchJob, $dbBatchJob);

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
		KalturaLog::debug("Convert Profile failed");

		kBatchManager::updateEntry($dbBatchJob->getEntryId(), entryStatus::ERROR_CONVERTING);

		self::deleteTemporaryFlavors($dbBatchJob->getEntryId());

		return $dbBatchJob;
	}

	public static function handleConvertProfileFinished(BatchJob $dbBatchJob, kConvertProfileJobData $data)
	{
		KalturaLog::debug("Convert Profile finished");

		self::deleteTemporaryFlavors($dbBatchJob->getEntryId());

		kFlowHelper::generateThumbnailsFromFlavor($dbBatchJob->getEntryId(), $dbBatchJob);

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
					$jobIsFinished = false;
					$entry->createDownloadAsset($dbBatchJob, $flavorParamsId, $data->getPuserId());
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
				KalturaLog::debug("profile [" . $profile->getId() . "]");
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
		if(!is_subclass_of($uploadToken->getObjectType(), assetPeer::OM_CLASS) && $uploadToken->getObjectType() != entryPeer::OM_CLASS)
			return;

		$fullPath = kUploadTokenMgr::getFullPathByUploadTokenId($uploadToken->getId());

		if(!file_exists($fullPath))
		{
			$remoteDCHost = kUploadTokenMgr::getRemoteHostForUploadToken($uploadToken->getId(), kDataCenterMgr::getCurrentDcId());
			if(!$remoteDCHost)
				return;

			kFileUtils::dumpApiRequest($remoteDCHost);
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

			// increments version
			$dbEntry->setData('100000.jpg');
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
		KalturaLog::debug("Handling temp entry id [" . $tempEntry->getId() . "] for real entry id [" . $tempEntry->getReplacedEntryId() . "]");
		$entry = entryPeer::retrieveByPK($tempEntry->getReplacedEntryId());
		if(!$entry)
		{
			KalturaLog::err("Real entry id [" . $tempEntry->getReplacedEntryId() . "] not found");
			myEntryUtils::deleteEntry($tempEntry,null,true);
			return;
		}

		switch($entry->getReplacementStatus())
		{
			case entryReplacementStatus::APPROVED_BUT_NOT_READY:
				KalturaLog::debug("status changed to ready");
				kEventsManager::raiseEventDeferred(new kObjectReadyForReplacmentEvent($tempEntry));
				break;

			case entryReplacementStatus::READY_BUT_NOT_APPROVED:
				break;

			case entryReplacementStatus::NOT_READY_AND_NOT_APPROVED:
				$entry->setReplacementStatus(entryReplacementStatus::READY_BUT_NOT_APPROVED);
				$entry->save();
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
	
	private static function deleteTemporaryFlavors($entryId)
	{
		KalturaLog::debug('checking for temporary flavors to delete');
		$originalflavorAsset = assetPeer::retrieveOriginalByEntryId($entryId);
		if($originalflavorAsset && $originalflavorAsset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_TEMP)
		{
			$originalflavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_DELETED);
			$originalflavorAsset->setDeletedAt(time());
			$originalflavorAsset->save();
		}
		
		$conversionProfile = myPartnerUtils::getConversionProfile2ForEntry($entryId);
		
		$criteria = new Criteria();
		$criteria->add(flavorParamsConversionProfilePeer::CONVERSION_PROFILE_ID, $conversionProfile->getId());
		$criteria->add(flavorParamsConversionProfilePeer::DELETE_POLICY, AssetParamsDeletePolicy::DELETE);
		$tempFlavorsParams = flavorParamsConversionProfilePeer::doSelect($criteria);
		
		foreach ($tempFlavorsParams as $tempFlavorsParam) 
		{
			$tempFlavorAsset = assetPeer::retrieveByEntryIdAndParams($entryId, $tempFlavorsParam->getFlavorParamsId());
			if($tempFlavorAsset)
			{
				KalturaLog::debug('Deleting flavor ['.$tempFlavorAsset->getId().']');				
				$tempFlavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_DELETED);
				$tempFlavorAsset->setDeletedAt(time());
				$tempFlavorAsset->save();
			}
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
			KalturaLog::debug('Activating convert profile job, local file path ['.$localFilePath.']');
			$data = $batchJob->getData();
			$data->setInputFileSyncLocalPath($localFilePath);
			$batchJob->setData($data);
			kJobsManager::updateBatchJob($batchJob, BatchJob::BATCHJOB_STATUS_PENDING);
			return true;
		}
		else 
			return false;
	}
}
