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
		{
//			$flavorAsset->incrementVersion();
			$flavorAsset->save();
			
			return $flavorAsset;
		}
		
		$entry = entryPeer::retrieveByPK($entryId);
		if(!$entry)
		{
			KalturaLog::err("Entry [$entryId] not found");
			return null;
		}
		
		// creates the flavor asset
		$flavorAsset = new flavorAsset();
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
	 * @param BatchJob $twinJob
	 * @return BatchJob
	 */
	public static function handleImportFinished(BatchJob $dbBatchJob, kImportJobData $data, BatchJob $twinJob = null)
	{
		KalturaLog::debug("Import finished, with file: " . $data->getDestFileLocalPath());
		
		if($dbBatchJob->getAbort())
			return $dbBatchJob;
			
		if(!$twinJob)
		{
			if(!file_exists($data->getDestFileLocalPath()))
				throw new APIException(APIErrors::INVALID_FILE_NAME, $data->getDestFileLocalPath());
		}
		
		// get entry
		$entryId = $dbBatchJob->getEntryId();
		$dbEntry = entryPeer::retrieveByPKNoFilter($entryId);
		
		// IMAGE media entries
		if ($dbEntry->getType() == entryType::MEDIA_CLIP && $dbEntry->getMediaType() == entry::ENTRY_MEDIA_TYPE_IMAGE)
		{
		    $syncKey = $dbEntry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA);
			try
			{
				kFileSyncUtils::moveFromFile($data->getDestFileLocalPath(), $syncKey, true);
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
		
		$syncKey = null;
		
		if($twinJob)
		{
			$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			// copy file sync
			$twinData = $twinJob->getData();
			if($twinData instanceof kImportJobData)
			{
				$twinFlavorAsset = assetPeer::retrieveById($twinData->getFlavorAssetId());
				if($twinFlavorAsset)
				{
					$twinSyncKey = $twinFlavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
					if($twinSyncKey && kFileSyncUtils::file_exists($twinSyncKey))
					{
						kFileSyncUtils::softCopy($twinSyncKey, $syncKey);
					}
				}
			}
		}
		else
		{
			$ext = pathinfo($data->getDestFileLocalPath(), PATHINFO_EXTENSION);	
			KalturaLog::info("Imported file extension: $ext");
			$flavorAsset->setFileExt($ext);
			$flavorAsset->save();
			$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			kFileSyncUtils::moveFromFile($data->getDestFileLocalPath(), $syncKey);
		}
		
		// set the path in the job data
		$localFilePath = kFileSyncUtils::getLocalFilePathForKey($syncKey);
		$data->setDestFileLocalPath($localFilePath);
		$data->setFlavorAssetId($flavorAsset->getId());
		$dbBatchJob->setData($data);
		$dbBatchJob->save();
		
		if($flavorAsset->getVersion() == 1)
			kEventsManager::raiseEvent(new kObjectAddedEvent($flavorAsset, $dbBatchJob));
		
// 		if(!$isNewFlavor)
// 		{
// 			if($flavorAsset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_READY)
// 			{
// 				// is remote storage flavor
// 				if($flavorAsset->getIsOriginal())
// 					kJobsManager::addConvertProfileJob($dbBatchJob, $flavorAsset->getentry(), $flavorAsset->getId(), $localFilePath);
// 			}
// 			else
// 			{ 
//				// if the flavor is the source, its readiness should be decided during the profile conversion
// 				if($flavorAsset->getIsOriginal())
// 					$flavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_QUEUED);
// 				else
// 					$flavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_VALIDATING);
//					
// 				$flavorAsset->save();
// 			}
//			
// 			if($flavorAsset->getIsOriginal())
// 			{
// 				$entryFlavors = assetPeer::retrieveFlavorsByEntryId($flavorAsset->getEntryId());
// 				foreach($entryFlavors as $entryFlavor)
// 				{
// 					if($entryFlavor->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_WAIT_FOR_CONVERT && $entryFlavor->getFlavorParamsId())
// 						kBusinessPreConvertDL::decideAddEntryFlavor($dbBatchJob, $flavorAsset->getEntryId(), $entryFlavor->getFlavorParamsId());
// 				}
// 			}
// 		}
		
		return $dbBatchJob;
	}
	
	/**
	 * @param BatchJob $dbBatchJob
	 * @param kExtractMediaJobData $data
	 * @param BatchJob $twinJob
	 * @return BatchJob
	 */
	public static function handleExtractMediaClosed(BatchJob $dbBatchJob, kExtractMediaJobData $data, BatchJob $twinJob = null)
	{
		KalturaLog::debug("Extract media closed");
		
		if($dbBatchJob->getAbort())
			return $dbBatchJob;
		
		$rootBatchJob = $dbBatchJob->getRootJob();
		if(!$rootBatchJob)
			return $dbBatchJob;
	
		if($twinJob)
		{
			// copy media info
			$twinData = $twinJob->getData();
			if($twinData->getMediaInfoId())
			{
				$twinMediaInfo = mediaInfoPeer::retrieveByPK($twinData->getMediaInfoId());
				if($twinMediaInfo)
				{
					$mediaInfo = $twinMediaInfo->copy();
					$mediaInfo->setFlavorAssetId($data->getFlavorAssetId());
					$mediaInfo = kBatchManager::addMediaInfo($mediaInfo);
					
					$data->setMediaInfoId($mediaInfo->getId());
					$dbBatchJob->setData($data);
					$dbBatchJob->save();
				}
			}
		}
		
		if($dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FINISHED)
		{
			$entry = entryPeer::retrieveByPKNoFilter($dbBatchJob->getEntryId());
			if($entry->getStatus() != entryStatus::READY && $entry->getStatus() != entryStatus::DELETED)
				kBatchManager::updateEntry($dbBatchJob->getEntryId(), entryStatus::PRECONVERT);
		}
				
		switch($dbBatchJob->getJobSubType())
		{
			case mediaInfo::ASSET_TYPE_ENTRY_INPUT:
				
				if($rootBatchJob->getJobType() == BatchJobType::CONVERT_PROFILE)
				{
					kBusinessPreConvertDL::decideProfileConvert($dbBatchJob, $rootBatchJob, $data->getMediaInfoId());
					
					// handle the source flavor as if it was converted, makes the entry ready according to ready behavior rules
					$currentFlavorAsset = assetPeer::retrieveById($data->getFlavorAssetId());
					if($currentFlavorAsset && $currentFlavorAsset->getStatus() == asset::FLAVOR_ASSET_STATUS_READY)
						$dbBatchJob = kBusinessPostConvertDL::handleConvertFinished($dbBatchJob, $currentFlavorAsset);
				}
				break;
		}
		return $dbBatchJob;
	}
	
	/**
	 * @param BatchJob $dbBatchJob
	 * @param kConvertJobData $data
	 * @param BatchJob $twinJob
	 * @return BatchJob
	 */
	public static function handleConvertPending(BatchJob $dbBatchJob, kConvertJobData $data, BatchJob $twinJob = null)
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
	 * @param BatchJob $twinJob
	 * @return BatchJob
	 */
	public static function handleConvertCollectionPending(BatchJob $dbBatchJob, kConvertCollectionJobData $data, BatchJob $twinJob = null)
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
	 * @param BatchJob $twinJob
	 * @return BatchJob
	 */
	public static function handleConvertFinished(BatchJob $dbBatchJob, kConvertJobData $data, BatchJob $twinJob = null)
	{
		KalturaLog::debug("Convert finished with destination file: " . $data->getDestFileSyncLocalPath());
		
		if($dbBatchJob->getAbort())
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
		else 
		{
			$storageProfile = StorageProfilePeer::retrieveByPK($storageProfileId);
			kFileSyncUtils::createReadyExternalSyncFileForKey($syncKey, $data->getDestFileSyncLocalPath(), $storageProfile);
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
			throw new APIException(APIErrors::INVALID_ENTRY, $dbBatchJob, $dbBatchJob->getEntryId());
			
		$entry->addFlavorParamsId($data->getFlavorParamsOutput()->getFlavorParamsId());
		$entry->save();
		
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
			$nextJob = kJobsManager::addFlavorConvertJob($syncKey, $flavorParamsOutput, $data->getFlavorAssetId(), $data->getMediaInfoId(), $dbBatchJob, $dbBatchJob->getJobSubType());
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
						$flavorAsset->setBitrate($flavorParamsOutput->getVideoBitrate());
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
				"abort" => $dbBatchJob->getAbort(),
				"progress" => $dbBatchJob->getProgress(),
				"message" => $dbBatchJob->getMessage(),
				"description" => $dbBatchJob->getDescription(),
				"updates_count" => $dbBatchJob->getUpdatesCount(),
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
	 * @param BatchJob $twinJob
	 * @return BatchJob
	 */
	public static function handleCaptureThumbFinished(BatchJob $dbBatchJob, kCaptureThumbJobData $data, BatchJob $twinJob = null)
	{
		KalturaLog::debug("Captire thumbnail finished with destination file: " . $data->getThumbPath());
		
		if($dbBatchJob->getAbort())
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
		
		if($thumbAsset->hasTag(thumbParams::TAG_DEFAULT_THUMB))
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
	 * @param BatchJob $twinJob
	 * @return BatchJob
	 */
	public static function handleConvertQueued(BatchJob $dbBatchJob, kConvertJobData $data, BatchJob $twinJob = null)
	{
		$rootBatchJob = $dbBatchJob->getRootJob();
		if($rootBatchJob && $rootBatchJob->getJobType() == BatchJobType::BULKDOWNLOAD)
		{
			$entry = $dbBatchJob->getEntry();
			
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
				"abort" => $dbBatchJob->getAbort(),
				"progress" => $dbBatchJob->getProgress(),
				"message" => $dbBatchJob->getMessage(),
				"description" => $dbBatchJob->getDescription(),
				"updates_count" => $dbBatchJob->getUpdatesCount(),
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
	 * @param BatchJob $twinJob
	 * @return BatchJob
	 */
	public static function handleConvertFailed(BatchJob $dbBatchJob, kConvertJobData $data, BatchJob $twinJob = null)
	{		
		KalturaLog::debug("Convert failed with destination file: " . $data->getDestFileSyncLocalPath());
		
		if($dbBatchJob->getAbort())
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
					"abort" => $dbBatchJob->getAbort(),
					"progress" => $dbBatchJob->getProgress(),
					"message" => $dbBatchJob->getMessage(),
					"description" => $dbBatchJob->getDescription(),
					"updates_count" => $dbBatchJob->getUpdatesCount(),
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
	 * @param BatchJob $twinJob
	 * @return BatchJob
	 */
	public static function handleCaptureThumbFailed(BatchJob $dbBatchJob, kCaptureThumbJobData $data, BatchJob $twinJob = null)
	{		
		KalturaLog::debug("Captura thumbnail failed with destination file: " . $data->getThumbPath());
		
		if($dbBatchJob->getAbort())
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
	 * @param BatchJob $twinJob
	 * @return BatchJob
	 */
	public static function handlePostConvertFailed(BatchJob $dbBatchJob, kPostConvertJobData $data, BatchJob $twinJob = null)
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
	 * @param BatchJob $twinJob
	 * @return BatchJob
	 */
	public static function handleConvertCollectionFinished(BatchJob $dbBatchJob, kConvertCollectionJobData $data, BatchJob $twinJob = null)
	{	
		KalturaLog::debug("Convert Collection finished for entry id: " . $dbBatchJob->getEntryId());
		
		if($dbBatchJob->getAbort())
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

		// adding flavor params ids to the entry
		$addedFlavorParamsOutputs = assetParamsOutputPeer::retrieveByPKs($addedFlavorParamsOutputsIds);
		foreach($addedFlavorParamsOutputs as $addedFlavorParamsOutput)
			$entry->addFlavorParamsId($addedFlavorParamsOutput->getFlavorParamsId());
		
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
				"abort" => $dbBatchJob->getAbort(),
				"progress" => $dbBatchJob->getProgress(),
				"message" => $dbBatchJob->getMessage(),
				"description" => $dbBatchJob->getDescription(),
				"updates_count" => $dbBatchJob->getUpdatesCount(),
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
	 * @param BatchJob $twinJob
	 * @return BatchJob
	 */
	public static function handleConvertCollectionFailed(BatchJob $dbBatchJob, kConvertCollectionJobData $data, BatchJob $twinJob = null)
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
			// check if this thumbnail already created
			if(isset($thumbAssetsList[$thumbParams->getId()]))
				continue;
				
			if(is_null($srcParamsId) && is_null($thumbParams->getSourceParamsId()))
			{
				// alternative should be used
				$thumbParams->setSourceParamsId($alternateFlavorParamsId);
			}
			elseif($thumbParams->getSourceParamsId() != $srcParamsId)
			{
				// only thumbnails that uses srcParamsId should be generated for now
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
		if($entry)
		{ 
			$thisFlavorHeight = $data->getThumbHeight();
			$thisFlavorBitrate = $data->getThumbBitrate();
			
			if(!$entry->getCreateThumb() || $entry->getThumbBitrate() > $thisFlavorBitrate)
			{
				$ignoreThumbnail = true;
			}
			elseif($entry->getThumbBitrate() == $thisFlavorBitrate && $entry->getThumbHeight() > $thisFlavorHeight)
			{
				$ignoreThumbnail = true;
			}
			else
			{
				$entry->setThumbHeight($thisFlavorHeight);
				$entry->setThumbBitrate($thisFlavorBitrate);
				$entry->save();
			}
		}
		
		if(!$ignoreThumbnail)
		{
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
	 * @param BatchJob $twinJob
	 * @return BatchJob|BatchJob
	 */
	public static function handlePostConvertFinished(BatchJob $dbBatchJob, kPostConvertJobData $data, BatchJob $twinJob = null)
	{
		if($dbBatchJob->getAbort())
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
					kJobsManager::addConvertProfileJob(null, $dbBatchJob->getEntry(), $currentFlavorAsset->getId(), $path);
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
	
	/**
	 * @param BatchJob $dbBatchJob
	 * @param kPullJobData $data
	 * @param BatchJob $twinJob
	 * @return BatchJob
	 */
	public static function handlePullFailed(BatchJob $dbBatchJob, kPullJobData $data, BatchJob $twinJob = null)
	{
		$rootBatchJob = $dbBatchJob->getRootJob();
		if($rootBatchJob)
			$rootBatchJob = kJobsManager::failBatchJob($rootBatchJob, "Pull job " . $dbBatchJob->getId() . " failed");
			
		return $dbBatchJob; 	
	}
	
	/**
	 * @param BatchJob $dbBatchJob
	 * @param kPullJobData $data
	 * @param BatchJob $twinJob
	 * @return BatchJob|BatchJob
	 */
	public static function handlePullFinished(BatchJob $dbBatchJob, kPullJobData $data, BatchJob $twinJob = null)
	{
		if($dbBatchJob->getAbort())
			return $dbBatchJob;
		
		// creates a child extract meida job
		$extractMediaData = new kExtractMediaJobData();
		$extractMediaData->setSrcFileSyncLocalPath($data->getDestFileLocalPath());
		kJobsManager::addJob($dbBatchJob->createChild(), $extractMediaData, BatchJobType::EXTRACT_MEDIA, mediaInfo::ASSET_TYPE_FLAVOR_INPUT);		
		
		return $dbBatchJob; 	
	}
	
	/**
	 * @param BatchJob $dbBatchJob
	 * @param kBulkUploadJobData $data
	 * @param BatchJob $twinJob
	 * @return BatchJob
	 */
	public static function handleBulkUploadPending(BatchJob $dbBatchJob, kBulkUploadJobData $data, BatchJob $twinJob = null)
	{
		// moved to BulkUploadService
		// create file sunc
//		$syncKey = $dbBatchJob->getSyncKey(BatchJob::FILE_SYNC_BATCHJOB_SUB_TYPE_BULKUPLOAD);
//		kFileSyncUtils::file_put_contents($syncKey, file_get_contents($data->getCsvFilePath()));
//
//		// sets the pointer to the csv file
//		$data->setCsvFilePath(kFileSyncUtils::getLocalFilePathForKey($syncKey));
//		
//		// save the data to the db
//		$dbBatchJob->setData($data);
//		$dbBatchJob->save();
		
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
		$fileSync->setStatus(FileSync::FILE_SYNC_STATUS_READY);
		$fileSync->save();
		
		if($dbBatchJob->getJobSubType() != StorageProfile::STORAGE_KALTURA_DC)
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
		
		$fileSync = FileSyncPeer::retrieveByPK($data->getSrcFileSyncId());
		$fileSync->setStatus(FileSync::FILE_SYNC_STATUS_ERROR);
		$fileSync->save();
		
		return $dbBatchJob;
	}
	
	/**
	 * @param BatchJob $dbBatchJob
	 * @param kConvertProfileJobData $data
	 * @param BatchJob $twinJob
	 * @return BatchJob
	 */
	public static function handleConvertProfilePending(BatchJob $dbBatchJob, kConvertProfileJobData $data, BatchJob $twinJob = null)
	{
		KalturaLog::debug("Convert Profile created, with input file: " . $data->getInputFileSyncLocalPath());
		
		if($data->getExtractMedia()) // check if extract media required
		{
			// creates extract media job
			kJobsManager::addExtractMediaJob($dbBatchJob, $data->getInputFileSyncLocalPath(), $data->getFlavorAssetId(), mediaInfo::ASSET_TYPE_ENTRY_INPUT);
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
	
	public static function handleConvertProfileFailed(BatchJob $dbBatchJob, kConvertProfileJobData $data, BatchJob $twinJob = null)
	{
		KalturaLog::debug("Convert Profile failed");
		
		kBatchManager::updateEntry($dbBatchJob->getEntryId(), entryStatus::ERROR_CONVERTING);
		
		$originalflavorAsset = assetPeer::retrieveOriginalByEntryId($dbBatchJob->getEntryId());
		if($originalflavorAsset && $originalflavorAsset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_TEMP)
		{
			$originalflavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_DELETED);
			$originalflavorAsset->setDeletedAt(time());
			$originalflavorAsset->save();
		}
		
		return $dbBatchJob; 	
	}
	
	public static function handleConvertProfileFinished(BatchJob $dbBatchJob, kConvertProfileJobData $data, BatchJob $twinJob = null)
	{
		KalturaLog::debug("Convert Profile finished");
		
		$originalflavorAsset = assetPeer::retrieveOriginalByEntryId($dbBatchJob->getEntryId());
		if($originalflavorAsset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_TEMP)
		{
			$originalflavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_DELETED);
			$originalflavorAsset->setDeletedAt(time());
			$originalflavorAsset->save();
		}
		
		kFlowHelper::generateThumbnailsFromFlavor($dbBatchJob->getEntryId(), $dbBatchJob);
			
		return $dbBatchJob; 	
	}
	
	public static function handleRemoteConvertPending(BatchJob $dbBatchJob, kRemoteConvertJobData $data, BatchJob $twinJob = null)
	{
		// creates pull job
		$pullData = new kPullJobData();
		$pullData->setSrcFileUrl($data->getSrcFileUrl()); 
		kJobsManager::addJob($dbBatchJob->createChild(false), $pullData, BatchJobType::PULL);
		
		// mark the job as almost done
		$dbBatchJob = kJobsManager::updateBatchJob($dbBatchJob, BatchJob::BATCHJOB_STATUS_ALMOST_DONE);
			
		return $dbBatchJob; 	
	}

	public static function handleBulkDownloadPending(BatchJob $dbBatchJob, kBulkDownloadJobData $data, BatchJob $twinJob = null)
	{
		$entryIds = explode(',', $data->getEntryIds());
		$flavorParamsId = $data->getFlavorParamsId();
		$jobIsFinished = true;
		foreach($entryIds as $entryId)
		{
			$entry = entryPeer::retrieveByPK($entryId);
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
							"abort" => $dbBatchJob->getAbort(),
							"progress" => $dbBatchJob->getProgress(),
							"message" => $dbBatchJob->getMessage(),
							"description" => $dbBatchJob->getDescription(),
							"updates_count" => $dbBatchJob->getUpdatesCount(),
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
	
	public static function handleProvisionProvideFinished(BatchJob $dbBatchJob, kProvisionJobData $data, BatchJob $twinJob = null)
	{
		$entry = $dbBatchJob->getEntry();
		$entry->setStreamUsername($data->getEncoderUsername());
		$entry->setStreamUrl($data->getRtmp());
		$entry->setStreamRemoteId($data->getStreamID());
		$entry->setStreamRemoteBackupId($data->getBackupStreamID());
		$entry->setPrimaryBroadcastingUrl($data->getPrimaryBroadcastingUrl());
		$entry->setSecondaryBroadcastingUrl($data->getSecondaryBroadcastingUrl());	
		$entry->setStreamName($data->getStreamName());
	
		kBatchManager::updateEntry($dbBatchJob->getEntryId(), entryStatus::READY);
		return $dbBatchJob; 	
	}
	
	public static function handleProvisionProvideFailed(BatchJob $dbBatchJob, kProvisionJobData $data, BatchJob $twinJob = null)
	{
		kBatchManager::updateEntry($dbBatchJob->getEntryId(), entryStatus::ERROR_CONVERTING);
		return $dbBatchJob; 	
	}
	
	public static function handleBulkDownloadFinished(BatchJob $dbBatchJob, kBulkDownloadJobData $data, BatchJob $twinJob = null)
	{
		if($dbBatchJob->getAbort())
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
		
		kJobsManager::addJob($dbBatchJob->createChild(), $jobData, BatchJobType::MAIL, $jobData->getMailType());
		
		return $dbBatchJob;
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
				
			kFile::dumpApiRequest($remoteDCHost);
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
				$dbAsset->setSize(filesize($finalPath));
					
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
			myEntryUtils::deleteEntry($tempEntry);
			return;
		}
		
		switch($entry->getReplacementStatus())
		{
			case entryReplacementStatus::APPROVED_BUT_NOT_READY:
				kBusinessConvertDL::replaceEntry($entry, $tempEntry);
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
				myEntryUtils::deleteEntry($tempEntry);
				break;
		}
	}	
}