<?php
/**
 * @package server-infra
 * @subpackage clipconcat
 */

class kClipManager implements kBatchJobStatusEventConsumer
{
	const CLIP_NUMBER = 'clipNumber';
	const CROP_HEIGHT = 'cropHeight';
	const CROP_WIDTH = 'cropWidth';
	const TARGET_HEIGHT = 'targetHeight';
	const TARGET_WIDTH = 'targetWidth';
	const FRAME_RATE = 'frameRate';
	const AUDIO_CHANNELS = 'audioChannels';
	const AUDIO_SAMPLE_RATE = 'audioSamplingRate';
	const IMAGE_TO_VIDEO = 'imageToVideo';
	const CROP = 'crop';
	const CROP_DATA_ARRAY = 'cropDataArray';
	const SUBTITLES_DATA_ARRAY = 'subtitlesDataArray';
	const CONVERSION_PARAMS = 'conversionParams';
	const MEDIA_INFO_OBJECT = 'mediaInfoObject';
	const CLIPS_DURATION = 'clipsDuration';
	const AUDIO_DURATION = 'audioDuration';
	const INVERTED_SOURCE = 'invertedSource';
	const EXTRA_CONVERSION_PARAMS = 'extraConversionParams';
	const TEMP_ENTRY = 'tempEntry';
	const SOURCE_ENTRY = 'sourceEntry';
	const OPERATION_ATTRIBUTES_ARRAY = 'operationAttributesArray';
	const MIN_FRAME_RATE = 10;
	const MAX_FRAME_RATE = 30;
	const DEFAULT_SAMPLE_RATE = 44100;
	const DEFAULT_AUDIO_CHANNELS = 1;
	const AUDIO_VIDEO_DIFF_MS = 200;
	const LOCK_EXPIRY = 10;

	/**
	 * @param kOperationResource $resource
	 * @param entry $destEntry
	 * @param entry $clipEntry
	 * @param string $importUrl
	 * @param int $rootJobId
	 * @param int $resourceOrder
	 * @param string $conversionParams
	 */
	public function createClipConcatJobFromResource($resource, $destEntry, $clipEntry, $importUrl = null,
													$rootJobId = null, $resourceOrder = null, $conversionParams = null)
	{
		$partnerId = $clipEntry->getPartnerId();
		$clipEntryId = $clipEntry->getId();
		$sourceEntryId = $resource->getResource()->getOriginEntryId();
		$operationAttributes = $resource->getOperationAttributes();

		$parentJob = new BatchJob();
		$parentJob->setPartnerId($partnerId);
		$parentJob->setEntryId($clipEntryId);
		if($rootJobId)
		{
			$parentJob->setRootJobId($rootJobId);
			$parentJob->setParentJobId($rootJobId);
		}

		$description = "source entry id: [$sourceEntryId],  template entry id: [$clipEntryId]";
		$this->addClipTrackEntry($destEntry->getId(), $description);

		$jobData = new kClipConcatJobData($importUrl);
		if(!$jobData->getImportNeeded())
		{
			$this->setDummyOriginalFlavorAssetReady($clipEntryId);
		}
		if(!is_null($resourceOrder))
		{
			// clip concat order for multi clip concat
			$jobData->setResourceOrder($resourceOrder);
		}

		$jobData->setTempEntryId($clipEntryId);

		if(!$rootJobId)
		{
			$jobData->setDestEntryId($destEntry->getId());
			//if it is replace(Trim flow) active the copy to destination consumers
			$this->fillDestEntry($destEntry, $sourceEntryId, $operationAttributes);
		}

		$jobData->setSourceEntryId($sourceEntryId);
		$jobData->setPartnerId($partnerId);
		$jobData->setPriority(0);
		$jobData->setOperationAttributes($operationAttributes);
		$jobData->setConversionParams($conversionParams);
		kJobsManager::addJob($parentJob, $jobData, BatchJobType::CLIP_CONCAT);
	}

	/**
	 * @param kOperationResources $resources
	 * @param entry $clipEntry
	 * @param entry $destEntry
	 * @param int $partnerId
	 * @param int $priority
	 * @return BatchJob
	 */
	public function createMultiClipConcatJob(kOperationResources $resources, $clipEntry, $destEntry, $partnerId, $priority = 0)
	{
		$parentJob = new BatchJob();
		$parentJob->setPartnerId($partnerId);
		$parentJob->setEntryId($clipEntry->getEntryId());

		$jobData = new kMultiClipConcatJobData();
		$jobData->setDestEntryId($destEntry->getEntryId());
		$jobData->setMultiTempEntryId($clipEntry->getEntryId());
		$jobData->setPartnerId($partnerId);
		$jobData->setPriority($priority);
		$jobData->setOperationResources($resources->getResources());
		$jobData->setChapterNamePolicy($resources->getChapterNamePolicy());

		$batchJob = kJobsManager::addJob($parentJob, $jobData, BatchJobType::MULTI_CLIP_CONCAT);
		return $batchJob;
	}

	/**
	 * @param BatchJob $batchJob
	 * @return bool true if should continue to the next consumer
	 * @throws KalturaErrors
	 */
	public function updatedJob(BatchJob $batchJob)
	{
		try
		{
			// root job is: clipConcat in case of clipConcat flow, or multiClipConcat in case of multi flow
			$rootJob = $this->getAncestorJob($batchJob);
			if ($batchJob->getJobType() == BatchJobType::CLIP_CONCAT)
			{
				$this->handleClipConcatParentJob($batchJob, $rootJob);
			}
			elseif ($this->isImportFinished($batchJob))
			{
				$this->handleImportFinished($batchJob->getParentJob());
			}
			elseif ($this->isImportFailed($batchJob))
			{
				//When import fails the concat job would have been stuck in queue,
				// in this case we need to fail the concat job
				// and fail the multi clip job in case of multi clip flow
				$this->handleImportFailed($batchJob, $rootJob);
			}
			elseif($this->isClipConcatFinished($batchJob, $rootJob))
			{
				kJobsManager::updateBatchJob($batchJob->getRootJob(), BatchJob::BATCHJOB_STATUS_FINISHED);
			}
			elseif ($this->shouldStartSingleResourceConcat($batchJob, $rootJob))
			{
				$this->startSingleResourceConcat($rootJob);
			}
			elseif($this->isConcatFinished($batchJob, $rootJob))
			{
				$this->concatDone($rootJob);
			}
		}
		catch (Exception $ex)
		{
			KalturaLog::err('Error During Concat Job [' . $ex->getMessage() . ']');
		}
		return true;
	}

	protected function isClipConcatFinished($batchJob, $rootJob)
	{
		if($rootJob && $rootJob->getJobType() != BatchJobType::MULTI_CLIP_CONCAT)
		{
			return false;
		}
		if($batchJob->getParentJob() && $batchJob->getParentJob()->getJobType() == BatchJobType::CONVERT)
		{
			return $this->isClipConcatChildrenFinished($batchJob);
		}
		return false;
	}

	protected function getAncestorJob($batchJob)
	{
		$rootJob = $batchJob->getRootJob();
		if($rootJob && $rootJob->getRootJob())
		{
			return $rootJob->getRootJob();
		}
		return $rootJob;
	}

	protected function isImportFinished(BatchJob $batchJob)
	{
		return 	$batchJob->getJobType() == BatchJobType::IMPORT && $batchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FINISHED;
	}
	
	protected function isImportFailed(BatchJob $batchJob)
	{
		return 	$batchJob->getJobType() == BatchJobType::IMPORT && in_array($batchJob->getStatus(), array(BatchJob::BATCHJOB_STATUS_FAILED, BatchJob::BATCHJOB_STATUS_FATAL, BatchJob::BATCHJOB_STATUS_ABORTED));
	}
	
	protected function shouldStartSingleResourceConcat(BatchJob $batchJob, $rootJob)
	{
		if($rootJob && $rootJob->getJobType() != BatchJobType::CLIP_CONCAT)
		{
			return false;
		}
		if($batchJob->getParentJob() && $batchJob->getParentJob()->getJobType() == BatchJobType::CONVERT)
		{
			return !$this->concatJobExist($rootJob);
		}
		return false;
	}
	
	protected function isConcatFinished(BatchJob $batchJob, $rootJob)
	{
		if(!$batchJob->getParentJob() || $batchJob->getParentJob()->getJobType() != BatchJobType::CONCAT)
		{
			return false;
		}
		if($rootJob->getJobType() == BatchJobType::MULTI_CLIP_CONCAT && !$this->isClipConcatChildrenFinished($rootJob))
		{
			return false;
		}
		return true;
	}
	
	protected function isConcatOfAllChildrenDone(BatchJob $batchJob)
	{
		$childJobs = $batchJob->getChildJobsByTypes(array(BatchJobType::CONVERT, BatchJobType::CONCAT, BatchJobType::POSTCONVERT));
		foreach ($childJobs as $job)
		{
			/** @var BatchJob $job */
			$jobId = $job->getId();
			$jobStatus = $job->getStatus();
			$jobType = $job->getJobType();
			KalturaLog::info("Child job id [$jobId] status [$jobStatus] type [$jobType]");
			if($jobStatus != BatchJob::BATCHJOB_STATUS_FINISHED)
			{
				if (!$this->hasGrandChildFinished($job))
				{
					return false;
				}
			}
		}
		return true;
	}

	protected function isClipConcatChildrenFinished(BatchJob $batchJob)
	{
		foreach ($batchJob->getChildJobs() as $childJob)
		{
			if(!$this->isConcatOfAllChildrenDone($childJob))
			{
				return false;
			}
		}
		return true;
	}
	
	protected function hasGrandChildFinished(BatchJob $job)
	{
		$children = $job->getChildJobs();
		if(is_array($children) && count($children) > 0)
		{
			/** @var BatchJob $jobChild */
			foreach ($children as $jobChild)
			{
				if (($jobChild->getJobType() == $job->getJobType()) && ($jobChild->getStatus() == BatchJob::BATCHJOB_STATUS_FINISHED))
				{
					return true;
				}
			}
		}
		return false;
	}
	
	protected function getAllConcatJobsFlavors(BatchJob $rootJob)
	{
		$flavors = array();
		$concatJobs = $rootJob->getChildJobsByTypes(array(BatchJobType::CONCAT));
		foreach ($concatJobs as $concatJob)
		{
			$flavors[] = assetPeer::retrieveById($concatJob->getData()->getFlavorAssetId());
		}
		return $flavors;
	}

	protected function handleImportFinished($batchJob)
	{
		/**@var kClipConcatJobData $jobData */
		$jobData = $batchJob->getData();
		$jobData->setImportNeeded(false);
		$batchJob->setData($jobData);
		kEventsManager::raiseEventDeferred(new kBatchJobStatusEvent($batchJob));
	}
	
	protected function handleImportFailed(BatchJob $batchJob, $rootJob)
	{
		$parentJob = $batchJob->getParentJob();
		$parentJob->setStatus(BatchJob::BATCHJOB_STATUS_FAILED);
		$parentJob->setMessage("Failed to import source file for clipping ");
		$parentJob->save();

		if($rootJob->getJobType() == BatchJobType::MULTI_CLIP_CONCAT)
		{
			$parentJobId = $parentJob->getId();
			$entryId = $batchJob->getEntryId();
			$rootJob->setStatus(BatchJob::BATCHJOB_STATUS_FAILED);
			$rootJob->setMessage("Failed to import source file for clipping for entryId [$entryId], batch job id [$parentJobId]");
			$rootJob->save();

			foreach ($rootJob->getChildJobs() as $childJob)
			{
				$childJob->setStatus(BatchJob::BATCHJOB_STATUS_FAILED);
				$childJob->setMessage("Failed to import source file for clipping for entryId [$entryId], batch job id [$parentJobId]");
				$childJob->save();
			}
		}
	}
	
	/**
	 * @param BatchJob $batchJob
	 * @return bool true if the consumer should handle the event
	 */
	public function shouldConsumeJobStatusEvent(BatchJob $batchJob)
	{
		if ($batchJob->getJobType() == BatchJobType::CLIP_CONCAT)
		{
			return true;
		}

		if ($batchJob->getJobType() == BatchJobType::MULTI_CLIP_CONCAT)
		{
			return false;
		}
		
		if(!$batchJob->getRootJob())
		{
			return false;
		}

		if(!in_array($batchJob->getRootJob()->getJobType(), array(BatchJobType::CLIP_CONCAT, BatchJobType::MULTI_CLIP_CONCAT)))
		{
			return false;
		}

		//If we are there then there is root job and its type is concat
		if ($batchJob->getJobType() == BatchJobType::IMPORT)
		{
			return true;
		}

		if (in_array($batchJob->getJobType(), array(BatchJobType::CONVERT,BatchJobType::CONCAT,BatchJobType::POSTCONVERT)))
		{
			return $this->areAllClipJobsDone($batchJob);
		}

		return false;
	}

	/**
	 * @param kOperationResource $resource
	 * @param entry $destEntry
	 * @param entry $clipEntry
	 * @param string $importUrl
	 * @param int $rootJobId
	 * @param int $order
	 * @param string $conversionParams
	 */
	public function startClipConcatBatchJob($resource, $destEntry, $clipEntry, $importUrl, $rootJobId, $order, $conversionParams)
	{
		$this->createClipConcatJobFromResource($resource, $destEntry , $clipEntry, $importUrl, $rootJobId, $order, $conversionParams);
	}

	/**
	 * @param kOperationResources $resources
	 * @param entry $dbEntry
	 * @param entry $clipEntry
	 * @return BatchJob
	 */
	public function startMultiClipConcatBatchJob($resources, $dbEntry, $clipEntry)
	{
		return $this->createMultiClipConcatJob($resources, $clipEntry, $dbEntry, $dbEntry->getPartnerId());
	}

	/**
	 * @param array $sourceEntryIds
	 * @param string $multiTempEntryId
	 * @param array $tempEntriesIds
	 * @param string $destEntryId
	 */
	public function addMultiClipTrackEntries($sourceEntryIds, $tempEntryIds, $multiTempEntryId, $destEntryId)
	{
		$description = "template entry ids: [" . implode(",",$tempEntryIds) . "]";
		$this->addClipTrackEntry($multiTempEntryId, $description);
		$description = "source entry ids: [" . implode(",", $sourceEntryIds) . "],  multi template entry id: [" . $multiTempEntryId . "]";
		$this->addClipTrackEntry($destEntryId, $description);
	}

	/**
	 * @param string $entryId
	 * @param string $description
	 */
	protected function addClipTrackEntry($entryId, $description)
	{
		$trackEntry = new TrackEntry();
		$trackEntry->setEntryId($entryId);
		$trackEntry->setTrackEventTypeId(TrackEntry::TRACK_ENTRY_EVENT_TYPE_CLIP);
		$trackEntry->setDescription($description);
		TrackEntry::addTrackEntry($trackEntry);
	}

	/***
	 * @param kClipAttributes $singleAttribute
	 * @param string $originalConversionEnginesExtraParams
	 * @param string $encryptionKey
	 * @param bool $isAudio
	 * @param array $conversionData
	 * @return int
	 * @throws kCoreException
	 * @throws KalturaAPIException
	 */
	protected function cloneFlavorParam($singleAttribute, $singleAttributeIndex, $originalConversionEnginesExtraParams, $encryptionKey = null, $isAudio = false, $conversionData = null)
	{
		$flavorParamsObj = assetParamsPeer::getTempAssetParamByPk(kClipAttributes::SYSTEM_DEFAULT_FLAVOR_PARAMS_ID);
		$flavorParamsObj->setFormat(flavorParams::CONTAINER_FORMAT_MPEGTS);
		$this->fixConversionParam($flavorParamsObj, $singleAttribute, $singleAttributeIndex, $originalConversionEnginesExtraParams, $isAudio, $conversionData);
		if ($encryptionKey)
		{
			$flavorParamsObj->setIsEncrypted(true);
		}
		assetParamsPeer::addInstanceToPool($flavorParamsObj);
		return $flavorParamsObj->getId();
	}

	/**
	 * @param string $entryId
	 * @return flavorAsset
	 */
	protected function setDummyOriginalFlavorAssetReady($entryId)
	{
		$flavorAsset = assetPeer::retrieveOriginalByEntryId($entryId);
		//set Dummy Ready we will update it later
		$flavorAsset->setStatus(flavorAsset::ASSET_STATUS_READY);
		$flavorAsset->save();
		return $flavorAsset;
	}

	/**
	 * @param int $partnerId
	 * @param string $entryId
	 * @param $flavorParamId
	 * @param int $order
	 * @return flavorAsset
	 */
	protected function createTempClipFlavorAsset($partnerId, $entryId, $flavorParamId, $order)
	{
		$entry = entryPeer::retrieveByPK($entryId);
		if(!$entry)
		{
			KalturaLog::err("Entry [$entryId] not found");
			return null;
		}

		// creates the flavor asset
		$flavorAsset = flavorAsset::getInstance();
		$flavorAsset->setStatus(flavorAsset::ASSET_STATUS_QUEUED);
		$flavorAsset->incrementVersion();
		$flavorAsset->addTags(array(flavorParams::TAG_TEMP_CLIP));
		$flavorAsset->setIsOriginal(false);
		$flavorAsset->setFlavorParamsId($flavorParamId);
		$flavorAsset->setPartnerId($partnerId);
		$flavorAsset->setEntryId($entryId);
		$flavorAsset->putInCustomData(self::CLIP_NUMBER,$order);
		$flavorAsset->save();
		return $flavorAsset;
	}

	/**
	 * @param BatchJob $batchJob
	 * @throws Exception
	 */
	protected function startSingleResourceConcat($batchJob)
	{
		KalturaLog::info('Going To Start Concat Job');
		if($batchJob->getJobType() != BatchJobType::CLIP_CONCAT)
		{
			return;
		}

		foreach ($batchJob->getChildJobs() as $job)
		{
			/** @var BatchJob $job */
			KalturaLog::info('Child job id [' . $job->getId() . '] status [' . $job->getStatus() . ']' . '] type ['.$job->getJobType() .']' );
			if($job->getJobType() == BatchJobType::CONVERT)
			{
				KalturaLog::info('Flavor Param Ids:' . $job->getEntry()->getFlavorParamsIds());
			}
		}
		/** @var kClipConcatJobData $jobData */
		$jobData = $batchJob->getData();

		$tempEntry = entryPeer::retrieveByPK($jobData->getTempEntryId());
		$assets = assetPeer::retrieveByEntryId($jobData->getTempEntryId(), array(assetType::FLAVOR));
		usort($assets, array("kClipManager", "cmpByClipOrder"));
		
		$files = $this->getFilesPath($assets);
		foreach ($files as $assetId => $relatedFiles)
		{
			$asset = assetPeer::retrieveById($assetId);
			$flavorAsset = $this->addNewAssetToTargetEntry($tempEntry, $asset->getFlavorParamsId());
			
			//calling addConcatJob only if lock succeeds
			$store = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_LOCK_KEYS);
			$lockKey = 'kclipManager_add_concat_job' . $batchJob->getId() . $assetId;
			if (!$store || $store->add($lockKey, true, self::LOCK_EXPIRY))
			{
				kJobsManager::addConcatJob($batchJob, $flavorAsset, $relatedFiles, false);
			}
		}
	}

	/**
	 * @param $a flavorAsset
	 * @param $b flavorAsset
	 * @return int
	 */
	protected function cmpByClipOrder($a, $b)
	{
		$aClipNumber = $a->getFromCustomData(self::CLIP_NUMBER);
		$bClipNumber = $b->getFromCustomData(self::CLIP_NUMBER);
		return $this->cmpInt($aClipNumber, $bClipNumber);
	}

	/**
	 * @param $a BatchJob
	 * @param $b BatchJob
	 * @return int
	 */
	protected function cmpByResourceOrder($a, $b)
	{
		$aData = $a->getData();
		$bData = $b->getData();

		/** @var $aData kClipConcatJobData */
		$aResourceOrder = $aData->getResourceOrder();

		/** @var $bData kClipConcatJobData */
		$bResourceOrder = $bData->getResourceOrder();

		return $this->cmpInt($aResourceOrder, $bResourceOrder);
	}

	/**
	 * @param $a int
	 * @param $b int
	 * @return int
	 */
	protected function cmpInt($a, $b)
	{
		if (!$a)
		{
			return -1;
		}
		if (!$b)
		{
			return 1;
		}
		return  ($a < $b) ? -1 : 1;
	}


	/***
	 * @param BatchJob $batchJob
	 * @throws kCoreException|PropelException|KalturaAPIException|APIException
	 */
	protected function handleClipConcatParentJob($batchJob, $rootJob)
	{
		switch ($batchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_PENDING:

				/**@var kClipConcatJobData $jobData */
				$jobData = $batchJob->getData();
				if ($jobData->getImportNeeded())
				{
					//set entry flow type to handle import in clip concat
					$tempEntry = entryPeer::retrieveByPK($jobData->getTempEntryId());
					$tempEntry->setFlowType(EntryFlowType::IMPORT_FOR_CLIP_CONCAT);
					$tempEntry->save();
					KalturaLog::info("Adding import job in clip manager for temp entry " . $jobData->getTempEntryId() . " to url: " . $jobData->getImportUrl());
					kJobsManager::addImportJob($batchJob, $jobData->getTempEntryId(), $jobData->getPartnerId(), $jobData->getImportUrl(), null, null, null, true);
				}
				else
				{
					$imageToVideo = $this->getJobDataConversionParams($jobData, self::IMAGE_TO_VIDEO);
					if($imageToVideo)
					{
						// processing of images (convert image to video) is done in concat job
						kJobsManager::updateBatchJob($batchJob, BatchJob::BATCHJOB_STATUS_FINISHED);
					}
					else
					{
						$this->addClipJobsFromBatchJob($batchJob, $jobData);
					}
				}
				break;

			case BatchJob::BATCHJOB_STATUS_FINISHED:

				if($this->allClipConcatJobsFinished($rootJob) && !$this->concatJobExist($rootJob))
				{
					$this->startMultiResourceConcat($rootJob);
				}
				break;

			default:
				break;
		}
	}

	protected function allClipConcatJobsFinished($rootJob)
	{
		if($rootJob->getJobType() != BatchJobType::MULTI_CLIP_CONCAT)
		{
			return false;
		}
		/* @var $jobData kMultiClipConcatJobData**/
		$jobData = $rootJob->getData();
		$clipConcatJobs = $rootJob->getChildJobsByTypes(array(BatchJobType::CLIP_CONCAT));
		if(count($clipConcatJobs) != count($jobData->getOperationResources()))
		{
			return false;
		}
		foreach ($clipConcatJobs as $clipConcatJob)
		{
			if($clipConcatJob->getStatus() != BatchJob::BATCHJOB_STATUS_FINISHED)
			{
				return false;
			}
		}
		return true;
	}

	/**
	 * @throws KalturaAPIException
	 */
	public function calculateAndEditConversionParams(&$resourcesData, $resources, $conversionProfileId)
	{
		// choose min height and min width of input dimensions and conversion profile max dimensions
		// if aspect ratio is not passed, choose the most common of aspect ratio and audio channels
		// choose sampling rate of 44.1 kHz
		// scale chosen height and width by chosen aspect ratio
		// choose max frame rate of all input frame rates, limit 10<=frameRate<=30
		// if cropping, base the above dimensions calculation on the cropped output

		$targetHeight = 0;
		$targetWidth = 0;
		$frameRate = self::MIN_FRAME_RATE;
		$aspectRatios = array();
		$allAudioChannels = array();
		$allAudioSampleRates = array();
		$cropAspectRatio = $resources->getCropAspectRatio();
		$crop = $cropAspectRatio ? $cropAspectRatio->getCrop() : false;
		$aspectRatio = $cropAspectRatio ? $cropAspectRatio->getAspectRatio() : null;

		foreach ($resourcesData as $key => $resourceData)
		{
			$entryId = $resourceData[self::SOURCE_ENTRY] ? $resourceData[self::SOURCE_ENTRY]->getId() : null;

			/** @var $mediaInfoObj mediaInfo*/
			$mediaInfoObj = $resourceData[self::MEDIA_INFO_OBJECT];
			if(!($mediaInfoObj instanceof mediaInfo))
			{
				throw new KalturaAPIException(KalturaErrors::INVALID_MEDIA_INFO, $entryId);
			}

			$currentWidth = $mediaInfoObj->getVideoWidth();
			$currentHeight = $mediaInfoObj->getVideoHeight();
			if($currentWidth * $currentHeight == 0)
			{
				throw new KalturaAPIException(KalturaErrors::INVALID_MEDIA_INFO, $entryId);
			}

			if($crop && $aspectRatio)
			{
				$resourcesData[$key][self::CROP_DATA_ARRAY] = $this->getCropDataArray($aspectRatio, $currentWidth, $currentHeight, $resourceData[self::OPERATION_ATTRIBUTES_ARRAY]);
				if(count($resourcesData[$key][self::CROP_DATA_ARRAY]) > 0)
				{
					// consider the dimensions after cropping to calculate target dimensions
					$currentWidth = $resourcesData[$key][self::CROP_DATA_ARRAY][0]["outWidth"];
					$currentHeight = $resourcesData[$key][self::CROP_DATA_ARRAY][0]["outHeight"];
				}
			}

			if(!$aspectRatio)
			{
				// calculate aspect ratio by frequency
				$duration = $resourceData[self::CLIPS_DURATION];
				$this->updateKeyFrequency($aspectRatios, $currentWidth/$currentHeight, $duration);
			}

			$targetWidth = $targetWidth == 0 ? $currentWidth : min($currentWidth, $targetWidth);
			$targetHeight = $targetHeight == 0 ? $currentHeight : min($currentHeight, $targetHeight);

			$subtitlesArray = $this->getSubtitlesDataArray($resourceData[self::OPERATION_ATTRIBUTES_ARRAY]);
			if(count($subtitlesArray) > 0)
			{
				$resourcesData[$key][self::SUBTITLES_DATA_ARRAY] = $subtitlesArray;
			}

			if($mediaInfoObj->getAudioChannels())
			{
				$allAudioChannels[] = $mediaInfoObj->getAudioChannels();
			}
			if($mediaInfoObj->getAudioSamplingRate())
			{
				$allAudioChannels[] = $mediaInfoObj->getAudioChannels();
			}
			if(!$resourceData[self::IMAGE_TO_VIDEO])
			{
				$frameRate = max($frameRate, $mediaInfoObj->getVideoFrameRate());
			}
		}
		$audioChannels = $this->decideAudioChannels($allAudioChannels);
		$audioSampleRate = $this->decideAudioSamplingRate($allAudioSampleRates);
		$targetAspectRatio = $aspectRatio ? $aspectRatio : $this->decideAspectRatio($aspectRatios);
		$this->decideResolution($conversionProfileId, $targetAspectRatio, $targetWidth, $targetHeight);

		KalturaLog::debug("Multi Clip target dimensions: width [$targetWidth], height [$targetHeight], aspect ratio [$targetAspectRatio]");

		$generalConversionParams = array(
			self::CROP => $crop,
			self::TARGET_WIDTH => $targetWidth,
			self::TARGET_HEIGHT => $targetHeight,
			self::AUDIO_CHANNELS => $audioChannels,
			self::AUDIO_SAMPLE_RATE => $audioSampleRate,
			self::FRAME_RATE => min($frameRate, self::MAX_FRAME_RATE)
		);

		$this->setConversionParamsOnResourcesData($resourcesData, $generalConversionParams);
	}

	protected function getCropData($targetAspectRatio, $inWidth, $inHeight, $cropAlignmentPercent = null)
	{
		// no cropping
		if($inWidth / $inHeight == $targetAspectRatio)
		{
			return array();
		}

		$inHeight -= 1;
		$inWidth -= 1;

		$outWidth = $inWidth;
		$outHeight = $inHeight;
		$widthPosition = 0;
		$heightPosition = 0;

		// $alignment (0 - 1) which is (top - bottom) or (left - right), default is 0.5 (middle)
		$alignment = $cropAlignmentPercent !== null ? $cropAlignmentPercent / 100 : 0.5;

		// crop height
		if($inWidth / $inHeight < $targetAspectRatio)
		{
			$outHeight = min($outWidth / $targetAspectRatio, $inHeight);
			$cropLength = $inHeight - $outHeight;
			$heightPosition = $alignment * $cropLength;
		}

		// crop width
		else if($inWidth / $inHeight > $targetAspectRatio)
		{
			$outWidth = min($targetAspectRatio * $outHeight, $inWidth);
			$cropLength = $inWidth - $outWidth;
			$widthPosition = $alignment * $cropLength;
		}

		return array(
			"outWidth" => floor($outWidth),
			"outHeight" => floor($outHeight),
			"widthPosition" => floor($widthPosition),
			"heightPosition" => floor($heightPosition)
		);
	}

	protected function getCropDataArray($targetAspectRatio, $inWidth, $inHeight, $operationAttributes)
	{
		$cropDataArray = array();
		foreach ($operationAttributes as $operationAttribute)
		{
			$cropDataArray[] = $this->getCropData($targetAspectRatio, $inWidth, $inHeight, $operationAttribute->getCropAlignment());
		}
		return $cropDataArray;
	}

	protected function getSubtitlesDataArray($operationAttributes)
	{
		$subtitlesDataArray = array();
		foreach ($operationAttributes as $operationAttribute)
		{
			$subtitlesDataArray[] = $this->getSubtitlesData($operationAttribute->getCaptionsOptions());
		}
		return $subtitlesDataArray;
	}

	protected function getSubtitlesData($captionsOptions)
	{
		$subtitlesData = array();
		if(!($captionsOptions instanceOf kCaptionsOptions) || !$captionsOptions->getCaptionFileUrl())
		{
			return $subtitlesData;
		}

		$forceStyle = array();
		$forceStyle["FontName"] = $captionsOptions->getFontName();
		$forceStyle["Fontsize"] = $captionsOptions->getFontSize();
		$forceStyle["Bold"] = $captionsOptions->getBold() ? 1 : null;
		$forceStyle["Italic"] = $captionsOptions->getItalic() ? 1 : null;
		$forceStyle["Underline"] = $captionsOptions->getUnderline() ? 1 : null;
		$forceStyle["PrimaryColour"] = $captionsOptions->getPrimaryColour();
		$forceStyle["Alignment"] = $captionsOptions->getAlignment();
		$forceStyle["BorderStyle"] = $captionsOptions->getBorderStyle();
		$forceStyle["BackColour"] = $captionsOptions->getBackColour();
		$forceStyle["OutlineColour"] = $captionsOptions->getOutlineColour();

		foreach ($forceStyle as $key => $value)
		{
			if($value === null)
			{
				unset($forceStyle[$key]);
			}
		}

		// set subtitles data
		$subtitlesData["action"] = $captionsOptions->getAction();
		$subtitlesData["captionFileUrl"] = $captionsOptions->getCaptionFileUrl();
		if(count($forceStyle) > 0)
		{
			$subtitlesData["force_style"] = $forceStyle;
		}

		return $subtitlesData;
	}

	protected function setConversionParamsOnResourcesData(&$resourcesData, $conversionParams)
	{
		$targetWidth = $conversionParams[self::TARGET_WIDTH];
		$targetHeight = $conversionParams[self::TARGET_HEIGHT];
		$audioChannels = $conversionParams[self::AUDIO_CHANNELS];
		$audioSampleRate = $conversionParams[self::AUDIO_SAMPLE_RATE];
		$crop = $conversionParams[self::CROP];

		foreach ($resourcesData as $key => $resourceData)
		{
			$imageToVideo = $resourceData[self::IMAGE_TO_VIDEO];
			$mediaInfoObj = $resourceData[self::MEDIA_INFO_OBJECT];

			$currentConversionParams = array();
			$currentConversionParams[self::TARGET_HEIGHT] = $targetHeight;
			$currentConversionParams[self::AUDIO_CHANNELS] = $audioChannels;
			$currentConversionParams[self::AUDIO_SAMPLE_RATE] = $audioSampleRate;
			$currentConversionParams[self::AUDIO_DURATION] = $mediaInfoObj->getAudioDuration();

			if(isset($resourceData[self::SUBTITLES_DATA_ARRAY]))
			{
				$currentConversionParams[self::SUBTITLES_DATA_ARRAY] = $resourceData[self::SUBTITLES_DATA_ARRAY];
			}

			$croppingMode = $crop && isset($resourceData[self::CROP_DATA_ARRAY]) && count($resourceData[self::CROP_DATA_ARRAY]) > 0;
			$currentConversionParams[self::CROP] = $croppingMode;
			if($croppingMode)
			{
				$currentConversionParams[self::CROP_DATA_ARRAY] = $resourceData[self::CROP_DATA_ARRAY];
				$outHeight = $resourceData[self::CROP_DATA_ARRAY][0]["outHeight"];
				$outWidth = $resourceData[self::CROP_DATA_ARRAY][0]["outWidth"];
				$currentConversionParams[self::CROP_HEIGHT] = $outHeight;
				$currentConversionParams[self::CROP_WIDTH] = $outWidth;

				$shouldScale = $outHeight != $targetHeight || $outWidth != $targetWidth;
				if($shouldScale)
				{
					$currentConversionParams[self::TARGET_WIDTH] = $targetWidth;
				}
			}
			else
			{
				if($this->shouldScaleNoCropping($mediaInfoObj, $targetWidth, $targetHeight))
				{
					$currentConversionParams[self::TARGET_WIDTH] = $targetWidth; // trigger scale
				}
			}

			if($mediaInfoObj->getVideoWidth() < $mediaInfoObj->getVideoHeight())
			{
				$currentConversionParams[self::INVERTED_SOURCE] = true;
				$currentConversionParams[self::TARGET_WIDTH] = $targetWidth;
			}

			$audioVideoDurationDiff = $mediaInfoObj->getAudioDuration() ? abs($mediaInfoObj->getAudioDuration() - $mediaInfoObj->getVideoDuration()) : 0;
			if($audioVideoDurationDiff > self::AUDIO_VIDEO_DIFF_MS)
			{
				$usingFilterComplex = $currentConversionParams[self::TARGET_WIDTH] || $croppingMode || $currentConversionParams[self::SUBTITLES_DATA_ARRAY];
				$currentConversionParams[self::EXTRA_CONVERSION_PARAMS] = $this->getSilentAudioPaddingCommand($usingFilterComplex);
			}
			if($imageToVideo)
			{
				$currentConversionParams[self::IMAGE_TO_VIDEO] = $imageToVideo;
				if($this->shouldScaleNoCropping($mediaInfoObj, $targetWidth, $targetHeight))
				{
					$currentConversionParams[self::TARGET_WIDTH] = $targetWidth; // trigger scale
				}
			}

			$resourcesData[$key][self::CONVERSION_PARAMS] = json_encode($currentConversionParams, true);
		}
	}

	protected function getSilentAudioPaddingCommand($usingFilterComplex)
	{
		if($usingFilterComplex)
		{
			return " -filter_complex 'aresample=async=1:min_hard_comp=0.100000:first_pts=0' ";
		}
		else
		{
			// if we do not scale the clipped asset, then add stream mapping, because applying only audio filter_complex, changes the streams order and concat fails
			return " -filter_complex 'aresample=async=1:min_hard_comp=0.100000:first_pts=0[a]' -map v -map [\"a\"] ";
		}
	}

	protected function shouldScaleNoCropping($inputMediaInfo, $outputWidth, $outputHeight)
	{
		$inputWidth = $inputMediaInfo->getVideoWidth();
		$inputHeight = $inputMediaInfo->getVideoHeight();

		$inputRatio = $inputWidth / $inputHeight;
		$outputRatio = $outputWidth / $outputHeight;

		return $inputRatio != $outputRatio;
	}

	protected function decideResolution($conversionProfileId, $aspectRatio, &$width, &$height)
	{
		$this->limitByMaxProfileResolution($conversionProfileId, $aspectRatio, $width, $height);
		$this->adjustResolutionByAspectRatios($aspectRatio, $width, $height);
		$this->adjustResolutionDivisionByValue($width, $height);
	}

	protected function adjustResolutionDivisionByValue(&$width, &$height, $value = 2)
	{
		// h264 requires height and width to be divided by two
		$width -= $width % $value;
		$height -= $height % $value;
	}

	protected function updateKeyFrequency(&$array, $key, $frequency)
	{
		$array["$key"] = isset($array["$key"]) ? $array["$key"] + $frequency : $frequency;
	}

	protected function decideAudioSamplingRate(array $allAudioSampleRates)
	{
		return self::DEFAULT_SAMPLE_RATE;
	}

	protected function decideAudioChannels(array $allAudioChannels)
	{
		return (count($allAudioChannels) && min($allAudioChannels) > 1) ? 2 : self::DEFAULT_AUDIO_CHANNELS;
	}

	protected function limitByMaxProfileResolution($conversionProfileId, $aspectRatio, &$width, &$height)
	{
		$profileWidth = 0;
		$profileHeight = 0;
		$flavors = flavorParamsConversionProfilePeer::retrieveByConversionProfile($conversionProfileId);
		foreach($flavors as $flavor)
		{
			/* @var $flavor flavorParamsConversionProfile */
			$flavorParams = assetParamsPeer::retrieveByPK($flavor->getFlavorParamsId());
			$profileHeight = max($profileHeight, $flavorParams->getHeight());
			$profileWidth = max($profileWidth, $flavorParams->getWidth());
		}

		if($profileHeight == 0 && $profileWidth == 0)
		{
			return;
		}

		KalturaLog::debug("Conversion profile maximum dimensions: width [$profileWidth], height [$profileHeight]");
		$this->adjustResolutionByAspectRatios($aspectRatio, $profileWidth, $profileHeight);
		$width = min($width, $profileWidth);
		$height = min($height, $profileHeight);
	}

	protected function adjustResolutionByAspectRatios($aspectRatio, &$width, &$height)
	{
		// aspect ration is aw/ah, therefore, the goal is w/h = aw/ah where w=aw or h=ah
		if($height * $aspectRatio >= $width)
		{
			$width = round($height * $aspectRatio);
		}
		else
		{
			$height = round($width * (1/$aspectRatio));
		}
	}

	protected function decideAspectRatio($inputARs)
	{
		if(count($inputARs) == 0)
		{
			throw new KalturaAPIException(KalturaErrors::INCOMPATIBLE_RESOURCES_DIMENSIONS);
		}
		$maxDurationAspectRatio = max($inputARs);
		return array_search($maxDurationAspectRatio, $inputARs);
	}

	/**
	 * @param $batchJob
	 * @param $jobData
	 * @throws APIException|kCoreException|KalturaAPIException
	 * Prepare Extract Media jobs for additional flavors if exist, and run the clipping
	 */
	protected function addClipJobsFromBatchJob($batchJob, $jobData)
	{
		/**@var kClipConcatJobData $jobData */
		$rootJob = $batchJob->getRootJob();
		$flavorAssetsToBeProcessed = array();
		$audioAssets = array();
		if(!$rootJob || $rootJob->getJobType() != BatchJobType::MULTI_CLIP_CONCAT)
		{
			// audio clipping is not supported for multi clip concat flow
			$audioAssets = assetPeer::retrieveAudioFlavorsByEntryID($jobData->getSourceEntryId(), array(asset::ASSET_STATUS_READY));
			$flavorAssetsToBeProcessed = $audioAssets;
		}
		$flavorAssetsToBeProcessed[] = assetPeer::retrieveOriginalByEntryId($jobData->getTempEntryId());
		foreach($flavorAssetsToBeProcessed as $asset)
		{
			/** @var flavorAsset $asset */
			//start child clip jobs
			$isAudio = in_array($asset, $audioAssets);
			$errDesc = '';
			$this->addClipJobs($batchJob, $jobData->getTempEntryId(), $errDesc, $jobData->getPartnerId(),
								 $jobData->getOperationAttributes(), $asset, $isAudio);
		}
		kJobsManager::updateBatchJob($batchJob, BatchJob::BATCHJOB_STATUS_ALMOST_DONE);
	}

	/**
	 * @param BatchJob $parentJob clipConcat job
	 * @param $entryId
	 * @param $errDescription
	 * @param $partnerId
	 * @param array $operationAttributes
	 * @param flavorAsset $asset
	 * @param bool $isAudio
	 * @return BatchJob[]
	 * @throws APIException|kCoreException|KalturaAPIException
	 */
	protected function addClipJobs($parentJob , $entryId, &$errDescription, $partnerId, array $operationAttributes, $asset, $isAudio = false)
	{
		$batchArray = array();
		$order = 0;
		$originalConversionEnginesExtraParams =
			assetParamsPeer::retrieveByPK(kClipAttributes::SYSTEM_DEFAULT_FLAVOR_PARAMS_ID)->getConversionEnginesExtraParams();
		
		$encryptionKey = null;
		if ($asset)
		{
			$encryptionKey = $asset->getEncryptionKey();
		}

		$conversionData = $this->getJobDataConversionParams($parentJob->getData());

		/* @var $singleAttribute kClipAttributes */
		foreach($operationAttributes as $index => $singleAttribute)
		{
			KalturaLog::info("Going To create Flavor for entry Id [$entryId] for clip: " . print_r($singleAttribute, true));
			if($singleAttribute->getDuration() <= 0)
			{
				KalturaLog::info("Ignoring clip attribute with non-positive duration");
				continue;
			}

			$clonedID = $this->cloneFlavorParam($singleAttribute, $index, $originalConversionEnginesExtraParams, $encryptionKey, $isAudio, $conversionData);
			$flavorAsset = $this->createTempClipFlavorAsset($partnerId, $entryId, $clonedID, $order);
			$flavorAsset->setActualSourceAssetParamsIds($asset->getId());

			$batchJob =	kBusinessPreConvertDL::decideAddEntryFlavor($parentJob, $entryId,
					$clonedID, $errDescription, $flavorAsset->getId(),
					array($singleAttribute), kConvertJobData::TRIMMING_FLAVOR_PRIORITY, $asset);

			if(!$batchJob)
			{
				throw new APIException(KalturaErrors::CANNOT_CREATE_CLIP_FLAVOR_JOB, $parentJob->getJobType(), $parentJob->getId());
			}

			$batchArray[] = $batchJob;
			$order++;
		}
		return $batchArray;
	}

	/***
	 * @param BatchJob $batchJob
	 * @return bool are all clip batch done
	 */
	protected function areAllClipJobsDone($batchJob)
	{
		$c = new Criteria();
		$c->add(BatchJobPeer::JOB_TYPE,array(BatchJobType::CONVERT,BatchJobType::CONCAT),Criteria::IN);
		$c->add(BatchJobPeer::STATUS,array(BatchJob::BATCHJOB_STATUS_FINISHED),Criteria::NOT_IN);
		$childJobs = $batchJob->getRootJob()->getChildJobs($c);
		if (count($childJobs) != 0)
		{
			/** @var BatchJob $job */
			foreach ($childJobs as $job)
			{
				KalturaLog::info("number of children:   ". count($childJobs));
				KalturaLog::info('Child job id [' . $job->getId() . '] status [' . $job->getStatus() . ']');
			}
			return false;
		}
		if ($batchJob->getJobType() == BatchJobType::POSTCONVERT  &&
			$batchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FINISHED)
		{
			return true;
		}
		return false;
	}

	/**
	 * @param $assets
	 * @return array
	 * @throws kCoreException
	 * @throws Exception
	 */
	protected function getFilesPath($assets, $allowIsOriginal = false)
	{
		$files = array();
		foreach ($assets as $asset)
		{
			/**
			 * Don't take source
			 * @var flavorAsset $asset */
			if (!$allowIsOriginal && $asset->getIsOriginal())
			{
				continue;
			}
			$syncKey = $asset->getSyncKey(asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
			$fileSync = kFileSyncUtils::getReadyFileSyncForKey($syncKey);
			//fileSync should be local
			if (!$fileSync[1])
			{
				throw new kCoreException("Clip Does not exist locally operations stops, there will be no concat");
			}
			if ($fileSync[0]->getFullPath())
			{
				if (!isset($files[$asset->getActualSourceAssetParamsIds()]))
				{
					$files[$asset->getActualSourceAssetParamsIds()] = array();
				}
				$files[$asset->getActualSourceAssetParamsIds()][] =  $fileSync[0]->getFullPath();
			}
		}
		return $files;
	}

	/**
	 * @param entry $tempEntry
	 * @param int $flavorParamsId
	 * @return flavorAsset
	 * @throws kCoreException
	 */
	protected function addNewAssetToTargetEntry($tempEntry, $flavorParamsId)
	{

		/** @var flavorAsset $flavorAsset */
		$flavorAsset =  assetPeer::getNewAsset(assetType::FLAVOR);
		// create asset
		$flavorAsset->setPartnerId($tempEntry->getPartnerId());
		$flavorAsset->setEntryId($tempEntry->getId());
		$flavorAsset->setStatus(asset::ASSET_STATUS_QUEUED);
		$flavorAsset->setFlavorParamsId($flavorParamsId);
		$flavorAsset->setIsOriginal(false);
		/*
		 * TODO - AWS - Handle shared concat flow
		 * When using shared storage based on partner we need to set file ext and version so that the shared file paths will be created correctly
		 *
		$flavorAsset->setFileExt("mp4");
		$flavorAsset->setVersion(0);
		*/
		$flavorAsset->save();
		return $flavorAsset;
	}

	/**
	 * @param $partnerId
	 * @return entry
	 * @throws Exception
	 */
	public function createTempEntryForClip($partnerId, $namePrefix = 'TEMP_')
	{
		$tempEntry = new entry();
		$tempEntry->setType(entryType::MEDIA_CLIP);
		$tempEntry->setMediaType(entry::ENTRY_MEDIA_TYPE_VIDEO);
		$tempEntry->setName($namePrefix . time());
		$tempEntry->setPartnerId($partnerId);
		$tempEntry->setStatus(entryStatus::NO_CONTENT);
		$tempEntry->setDisplayInSearch(EntryDisplayInSearchType::SYSTEM);
		$tempEntry->setSourceType(EntrySourceType::CLIP);
		$tempEntry->setKuserId(kCurrentContext::getCurrentKsKuserId());
		$tempEntry->setConversionProfileId(myPartnerUtils::getConversionProfile2ForPartner($partnerId)->getId());
		$tempEntry->setIsTemporary(true);
		$tempEntry->setCreateThumb(false);
		$tempEntry->save();
		KalturaLog::info('Temp ClipConcat Entry Created, Entry name:' . $tempEntry->getName() . 'Entry ID:  ' . $tempEntry->getId());
		return $tempEntry;
	}

	/**
	 * @param string $entryId
	 * @param asset $concatAsset
	 * @throws Exception
	 * @throws KalturaAPIException
	 */
	protected function addDestinationEntryAsset($entryId, $concatAsset)
	{
		$concatSyncKey = $concatAsset->getSyncKey(flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
		$dbEntry = entryPeer::retrieveByPK($entryId);
		$isNewAsset = false;
		if(!$dbEntry)
		{
			KalturaLog::err("Flavor asset not created for entry [ $entryId ]");
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
		}
		if($concatAsset->getFlavorParamsId() == 0)
		{
			$isNewAsset = true;
			$dbAsset = kFlowHelper::createOriginalFlavorAsset($dbEntry->getPartnerId(), $entryId, $concatAsset->getFileExt());
		}
		else
		{
			$dbAsset = kFlowHelper::createAdditionalFlavorAsset($dbEntry->getPartnerId(), $entryId, $concatAsset->getFlavorParamsId(), $concatAsset->getFileExt());
		}

		if(!$dbAsset)
		{
			$this->updateAssetFailedToConvert($entryId, $dbEntry);
			throw new KalturaAPIException(KalturaErrors::ORIGINAL_FLAVOR_ASSET_NOT_CREATED);
		}

		$this->updateAssetState($concatSyncKey, $dbAsset, $isNewAsset, $dbEntry);
	}

	/**
	 * @param BatchJob $batchJob
	 * @throws Exception
	 * @throws KalturaAPIException
	 */
	protected function concatDone(BatchJob $rootJob)
	{
		/** @var $clipConcatJobData kMultiClipConcatJobData|kClipConcatJobData */
		$clipConcatJobData = $rootJob->getData();

		if ($this->isConcatOfAllChildrenDone($rootJob))
		{
			$destinationEntry = $clipConcatJobData->getDestEntryId();
			$listOfFlavorAssets = $this->getAllConcatJobsFlavors($rootJob);
			//collect all assets from temp entry and add them to the dest entry
			foreach ($listOfFlavorAssets as $flavorAsset)
			{
				$this->addDestinationEntryAsset($destinationEntry, $flavorAsset);
			}

			kJobsManager::updateBatchJob($rootJob, BatchJob::BATCHJOB_STATUS_FINISHED);

			if($rootJob->getJobType() == BatchJobType::MULTI_CLIP_CONCAT)
			{
				$deleteEntry = $clipConcatJobData->getMultiTempEntryId();
			}
			else
			{
				$deleteEntry = $clipConcatJobData->getTempEntryId();
			}
			$this->deleteEntry($deleteEntry);
		}
	}

	/**
	 * @param BatchJob $multiClipConcatJob
	 * @throws kCoreException
	 * @throws KalturaAPIException
	 */
	protected function startMultiResourceConcat(BatchJob $multiClipConcatJob)
	{
		$clipConcatJobs = $multiClipConcatJob->getChildJobsByTypes(array(BatchJobType::CLIP_CONCAT));
		usort($clipConcatJobs, array("kClipManager", "cmpByResourceOrder"));
		$tempEntry = entryPeer::retrieveByPK($multiClipConcatJob->getData()->getMultiTempEntryId());

		//calling addConcatJob only if lock succeeds
		$store = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_LOCK_KEYS);
		// create one concat job for multi temp entry
		$lockKey = 'kclipManager_add_concat_job' . $multiClipConcatJob->getId() . $tempEntry->getId();
		if (!$store || $store->add($lockKey, true, self::LOCK_EXPIRY))
		{
			$lastAssetId = null;
			$allRelatedFiles = array();
			$convertCommands = array();
			foreach ($clipConcatJobs as $clipConcatJob)
			{
				KalturaLog::debug('Going To Start Concat Job for Multi Clip Concat');

				if($clipConcatJob->getStatus() != BatchJob::BATCHJOB_STATUS_FINISHED)
				{
					kJobsManager::updateBatchJob($clipConcatJob, BatchJob::BATCHJOB_STATUS_FINISHED);
				}

				/** @var kClipConcatJobData $jobData */
				$jobData = $clipConcatJob->getData();

				$flavorAssetsOnTempEntry = assetPeer::retrieveByEntryId($jobData->getTempEntryId(), array(assetType::FLAVOR));
				usort($flavorAssetsOnTempEntry, array("kClipManager", "cmpByClipOrder"));
				$imageToVideo = $this->getJobDataConversionParams($jobData, self::IMAGE_TO_VIDEO);

				// each file of the concatenated files is an output of an operation attribute
				$files = $this->getFilesPath($flavorAssetsOnTempEntry, $imageToVideo);
				$operationAttributesSorted = $jobData->getOperationAttributes();

				foreach ($files as $flavorAssetId => $relatedFiles)
				{
					/** @var array $relatedFiles */
					foreach ($relatedFiles as $key => $relatedFile)
					{
						$allRelatedFiles[] = $relatedFile;
						$convertCommands[] = $this->getConvertCommandForFile($jobData, $operationAttributesSorted, $key);
					}
					KalturaLog::debug("Asset Id: [$flavorAssetId], Related file : " . print_r($relatedFiles, true));
					// assume concatenated assets have the same actualFlavorParamsId and take the last
					$lastAssetId = $flavorAssetId ? $flavorAssetId : $lastAssetId;
				}
				$this->deleteEntry($jobData->getTempEntryId());
			}

			// use no filter because we delete the entry
			$flavorAsset = assetPeer::retrieveByIdNoFilter($lastAssetId);
			$flavorParamsId = $flavorAsset ? $flavorAsset->getFlavorParamsId() : flavorParams::SOURCE_FLAVOR_ID;

			// create one target flavor asset for all clip concat jobs
			$targetFlavorAsset = $this->addNewAssetToTargetEntry($tempEntry, $flavorParamsId);
			kJobsManager::addConcatJob($multiClipConcatJob, $targetFlavorAsset, $allRelatedFiles, false, null, null, $convertCommands);
		}
	}

	protected function getConvertCommandForFile($jobData, $operationAttributesSorted, $clipIndex)
	{
		/** @var kClipConcatJobData $jobData */
		$operationAttribute = $operationAttributesSorted[$clipIndex];
		$sortedFilters = $this->getSortedFiltersComplexForConcat($jobData, $operationAttributesSorted, $clipIndex);
		$imageToVideo = $this->getJobDataConversionParams($jobData, self::IMAGE_TO_VIDEO);
		if($imageToVideo)
		{
			return $this->getConvertImageToVideoCommand($jobData, $operationAttribute, $sortedFilters);
		}

		$audioDuration = $this->getJobDataConversionParams($jobData, self::AUDIO_DURATION);
		if(!$audioDuration)
		{
			return $this->getAddSilentAudioCommand($jobData, $operationAttribute, $sortedFilters);
		}

		return $this->getGeneralCommand($jobData, $sortedFilters);
	}

	protected function getAspectCommand($conversionParams, $sortedFilters)
	{
		$cmdStr = "";
		if(isset($sortedFilters["scale"]))
		{
			$width = $conversionParams[self::TARGET_WIDTH];
			$height = $conversionParams[self::TARGET_HEIGHT];
			$frameSize = "$width:$height";
			$cmdStr .= " -s " . str_replace(':', 'x', $frameSize);
			$cmdStr .= " -aspect $frameSize";
		}
		return $cmdStr;
	}

	protected function getGeneralCommand($jobData, $sortedFilters)
	{
		$filterComplex = $this->getMappedSortedFiltersComplex($sortedFilters);
		$flavorParamsObj = assetParamsPeer::getTempAssetParamByPk(kClipAttributes::SYSTEM_DEFAULT_FLAVOR_PARAMS_ID);

		if(!$flavorParamsObj || $filterComplex == "")
		{
			return "-";
		}

		$conversionParams = $this->getJobDataConversionParams($jobData);

		$cmdStr = " -i __inFileName__";
		$cmdStr .= $this->getAspectCommand($conversionParams, $sortedFilters);
		$cmdStr .= " -filter_complex '$filterComplex'";
		$cmdStr .= " -c:v libx264 -subq 5 -qcomp 0.6 -qmin 10 -qmax 50 -qdiff 4";
		$cmdStr .= " -coder 1 -refs 2 -x264opts stitchable -vprofile main -force_key_frames expr:'gte(t,n_forced*2)'";

		$bitrate = $flavorParamsObj->getVideoBitRate();
		$cmdStr .= " -pix_fmt yuv420p -b:v $bitrate" . "k";
		$cmdStr .= " -c:a libfdk_aac -b:a 192k";

		$cmdStr .= " -ac " . $conversionParams[self::AUDIO_CHANNELS];
		$cmdStr .= " -ar " . $conversionParams[self::AUDIO_SAMPLE_RATE];

		if(isset($conversionParams[self::FRAME_RATE]))
		{
			$cmdStr.= " -r " . $conversionParams[self::FRAME_RATE];
		}
		$cmdStr .= " -f mpegts -vsync 1 -y __outFileName__ ";
		return $cmdStr;
	}

	protected function getAddSilentAudioCommand($jobData, $operationAttribute, $sortedFilters)
	{
		/** @var kClipConcatJobData $jobData */
		$cmdStr = " -i __inFileName__";
		$conversionParams = $this->getJobDataConversionParams($jobData);
		$cmdStr .= " -ac " . $conversionParams[self::AUDIO_CHANNELS];
		$cmdStr .= " -ar " . $conversionParams[self::AUDIO_SAMPLE_RATE];
		$cmdStr .= " -f s16le -i /dev/zero";

		$filterComplex = $this->getMappedSortedFiltersComplex($sortedFilters);
		if($filterComplex != "")
		{
			$cmdStr .= $this->getAspectCommand($conversionParams, $sortedFilters);
			$cmdStr .= " -filter_complex '$filterComplex'";
			$cmdStr .= " -c:v libx264 -pix_fmt yuv420p";
		}
		else
		{
			$cmdStr .= " -c:v copy";
		}
		$cmdStr .= " -t " . $operationAttribute->getDuration()/1000;
		$cmdStr .= "  -c:a libfdk_aac -b:a 192k -f mpegts -y __outFileName__ ";
		return $cmdStr;
	}

	protected function getConvertImageToVideoCommand($jobData, $operationAttribute, $sortedFilters)
	{
		/** @var kClipConcatJobData $jobData */
		$cmdStr = " -loop 1 -i __inFileName__";
		$cmdStr .= " -f s16le -i /dev/zero -c:a libfdk_aac -b:a 192k";

		$conversionParams = $this->getJobDataConversionParams($jobData);
		$cmdStr .= " -ac " . $conversionParams[self::AUDIO_CHANNELS];
		$cmdStr .= " -ar " . $conversionParams[self::AUDIO_SAMPLE_RATE];

		if(isset($conversionParams[self::FRAME_RATE]))
		{
			$cmdStr.= " -r " . $conversionParams[self::FRAME_RATE];
		}
		$cmdStr .= " -c:v libx264 -pix_fmt yuv420p";

		$filterComplex = $this->getMappedSortedFiltersComplex($sortedFilters);
		$cmdStr .= $this->getAspectCommand($conversionParams, $sortedFilters);
		$cmdStr .= " -filter_complex '$filterComplex'";

		// image should have only one clipAttribute
		$duration = $operationAttribute->getDuration()/1000;
		$cmdStr .= " -t $duration -shortest -fflags +shortest";
		$cmdStr .= " -f mpegts -vsync 1";
		$cmdStr .= " -y __outFileName__";
		return $cmdStr;
	}

	/**
	 * @param $entryId
	 * @param null $entryType
	 * @throws Exception
	 * @throws KalturaAPIException
	 */
	protected function deleteEntry($entryId, $entryType = null)
	{
		$entryToDelete = entryPeer::retrieveByPK($entryId);

		if (!$entryToDelete || ($entryType !== null && $entryToDelete->getType() != $entryType))
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		myEntryUtils::deleteEntry($entryToDelete);
	}

	/**
	 * @param asset $dbAsset
	 * @throws PropelException
	 */
	protected function updateMediaFlowOnAsset($dbAsset)
	{
		$mediaInfo = mediaInfoPeer::retrieveByFlavorAssetId($dbAsset->getId());
		if ($mediaInfo) {
			$newMediaInfo = $mediaInfo->copy();
			$newMediaInfo->setFlavorAssetId($dbAsset->getId());
			$newMediaInfo->save();
		}
	}

	/**
	 * @param asset $dbAsset
	 * @param entry $dbEntry
	 */
	protected function syncFlavorParamToAsset($dbAsset, $dbEntry)
	{
		if ($dbAsset->getStatus() == asset::ASSET_STATUS_READY) {
			$dbEntry->syncFlavorParamsIds();
			$dbEntry->save();
		}
	}

	/**
	 * @param string $entryId
	 * @param entry $dbEntry
	 */
	protected function updateAssetFailedToConvert($entryId, $dbEntry)
	{
		KalturaLog::err("Flavor asset not created for entry [" . $entryId . "]");

		if ($dbEntry->getStatus() == entryStatus::NO_CONTENT) {
			$dbEntry->setStatus(entryStatus::ERROR_CONVERTING);
			$dbEntry->save();
		}
	}

	/**
	 * @param $concatSyncKey
	 * @param asset $dbAsset
	 * @param $isNewAsset
	 * @param $dbEntry
	 * @throws PropelException
	 */
	protected function updateAssetState($concatSyncKey, $dbAsset, $isNewAsset, $dbEntry)
	{
		$newSyncKey = $dbAsset->getSyncKey(flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
		kFileSyncUtils::createSyncFileLinkForKey($newSyncKey, $concatSyncKey);

		if ($isNewAsset)
			kEventsManager::raiseEvent(new kObjectAddedEvent($dbAsset));
		kEventsManager::raiseEvent(new kObjectDataChangedEvent($dbAsset));

		$this->updateMediaFlowOnAsset($dbAsset);

		$this->syncFlavorParamToAsset($dbAsset, $dbEntry);
	}

	/**
	 * @param entry $destEntry
	 * @param $sourceEntryId
	 * @param array $operationAttributes
	 */
	protected function fillDestEntry($destEntry, $sourceEntryId, array $operationAttributes)
	{
		if ($destEntry->getIsTemporary())
			$destEntry->setFlowType(EntryFlowType::TRIM_CONCAT);
		else 
			$destEntry->setFlowType(EntryFlowType::CLIP_CONCAT);
		$destEntry->setSourceEntryId($sourceEntryId);
		$destEntry->setOperationAttributes($operationAttributes);
		$destEntry->setStatus(entryStatus::PENDING);
		$destEntry->save();
	}

	/**
	 * @param array $conversionExtraParamsArray
	 * @param array $conversionEngines
	 * @param kClipAttributes $singleAttribute
	 * @param bool $isAudio
	 * @return string
	 */
	protected function editConversionEngineExtraParam($conversionEngines, $singleAttribute, $conversionExtraParamsArray = array(), $isAudio = false, $baseExtraParams = '', $effects = true)
	{
		$newConversionExtraParams = array();
		for ($i = 0; $i < count($conversionEngines) ; $i++)
		{
			$extraParams = $baseExtraParams;
			if ($i < count($conversionExtraParamsArray))
			{
				$extraParams .= $conversionExtraParamsArray[$i];
			}
			if ($effects && !$isAudio && in_array($conversionEngines[$i], array(conversionEngineType::FFMPEG, conversionEngineType::FFMPEG_AUX)))
			{
				$extraParams .= $this->addEffects($singleAttribute);
			}
			$newConversionExtraParams[] = $extraParams;
		}
		return implode(' | ',$newConversionExtraParams);
	}

	/**
	 * @param assetParams $flavorParamsObj
	 * @param kClipAttributes $singleAttribute
	 * @param string $originalConversionEnginesExtraParams
	 * @param bool $isAudio
	 * @param array $conversionData
	 * @throws KalturaAPIException
	 */
	protected function fixConversionParam($flavorParamsObj, $singleAttribute, $singleAttributeIndex, $originalConversionEnginesExtraParams, $isAudio, $conversionData = null)
	{
		$extraParams = '';
		if($conversionData)
		{
			// for multi clip flow, reset the original values of the flavor params (-1) and edit the necessary fields for the current clip
			$this->resetFlavorParamsObject($flavorParamsObj);
			$originalConversionEnginesExtraParams = $flavorParamsObj->getConversionEnginesExtraParams();
			if(isset($conversionData[self::EXTRA_CONVERSION_PARAMS]))
			{
				$extraParams = $conversionData[self::EXTRA_CONVERSION_PARAMS];
			}
		}
		$conversionEngines = explode(',', $flavorParamsObj->getConversionEngines());
		$conversionExtraParams = $originalConversionEnginesExtraParams ? explode('|', $originalConversionEnginesExtraParams) : null;
		$allowEffects = $this->allowEffectsOnConvert($conversionData, $singleAttributeIndex);
		$newExtraConversionParams = $this->editConversionEngineExtraParam($conversionEngines, $singleAttribute, $conversionExtraParams, $isAudio, $extraParams, $allowEffects);
		$flavorParamsObj->setConversionEnginesExtraParams($newExtraConversionParams);
		if($conversionData && $flavorParamsObj instanceof flavorParams)
		{
			$this->editConversionParams($flavorParamsObj, $conversionData, $singleAttributeIndex);
		}
	}

	/**
	 * @throws KalturaAPIException
	 */
	protected function editConversionParams(&$flavorParamsObj, $conversionParams, $index)
	{
		if($conversionParams)
		{
			$flavorParamsObj->setForceFrameToMultiplication16(0);
			$flavorParamsObj->setIsAvoidVideoShrinkFramesizeToSource(1);
			$flavorParamsObj->setHeight($conversionParams[self::TARGET_HEIGHT]);

			$invertedResource = isset($conversionParams[self::INVERTED_SOURCE]) && $conversionParams[self::INVERTED_SOURCE];

			$croppingMode = false;
			if(isset($conversionParams[self::CROP_DATA_ARRAY]) && isset($conversionParams[self::CROP_DATA_ARRAY][$index]))
			{
				$cropData = $conversionParams[self::CROP_DATA_ARRAY][$index];
				$croppingMode = is_array($cropData) && count($cropData) > 0;
			}

			if($croppingMode)
			{
				// crop
				$processingMode = $invertedResource ? 8 : 7;
				$flavorParamsObj->setAspectRatioProcessingMode($processingMode);
				$flavorParamsObj->setCropData(json_encode($cropData));
			}
			else if($invertedResource)
			{
				// for inverted source calculation, the output flavor is inverted
				// _arProcessingMode = 6, inverts back the output flavor
				$flavorParamsObj->setAspectRatioProcessingMode(6);
			}
			else if(isset($conversionParams[self::TARGET_WIDTH]))
			{
				// scale
				$flavorParamsObj->setAspectRatioProcessingMode(2);
			}

			if(isset($conversionParams[self::TARGET_WIDTH]))
			{
				$flavorParamsObj->setWidth($conversionParams[self::TARGET_WIDTH]);
			}

			if(isset($conversionParams[self::FRAME_RATE]))
			{
				$flavorParamsObj->setFrameRate($conversionParams[self::FRAME_RATE]);
			}
			if(isset($conversionParams[self::SUBTITLES_DATA_ARRAY]) && isset($conversionParams[self::SUBTITLES_DATA_ARRAY][$index]))
			{
				$subtitlesData = $conversionParams[self::SUBTITLES_DATA_ARRAY][$index];
				if(is_array($subtitlesData) && count($subtitlesData) > 0)
				{
					$flavorParamsObj->setSubtitlesData(json_encode($subtitlesData));
				}
			}
			if(isset($conversionParams[self::AUDIO_CHANNELS]))
			{
				$flavorParamsObj->setAudioChannels($conversionParams[self::AUDIO_CHANNELS]);
			}
			if(isset($conversionParams[self::AUDIO_SAMPLE_RATE]))
			{
				$flavorParamsObj->setAudioSampleRate($conversionParams[self::AUDIO_SAMPLE_RATE]);
			}
		}
	}

	/**
	 * @throws KalturaAPIException
	 */
	protected function resetFlavorParamsObject(&$flavorParamsObj)
	{
		$flavorParamsObj->setAspectRatioProcessingMode(0);
		$flavorParamsObj->setIsAvoidVideoShrinkFramesizeToSource(0);
		$flavorParamsObj->setSubtitlesData(null);
		$flavorParamsObj->setCropData(null);

		/** @var flavorParams $flavorParamsObj*/
		if($flavorParamsObj->getColumnsOldValue(assetParamsPeer::FRAME_RATE))
		{
			$flavorParamsObj->setFrameRate($flavorParamsObj->getColumnsOldValue(assetParamsPeer::FRAME_RATE));
		}
		if($flavorParamsObj->getColumnsOldValue(assetParamsPeer::WIDTH))
		{
			$flavorParamsObj->setWidth($flavorParamsObj->getColumnsOldValue(assetParamsPeer::WIDTH));
		}
		if($flavorParamsObj->getColumnsOldValue(assetParamsPeer::HEIGHT))
		{
			$flavorParamsObj->setHeight($flavorParamsObj->getColumnsOldValue(assetParamsPeer::HEIGHT));
		}
		if($flavorParamsObj->getColumnsOldValue(assetParamsPeer::AUDIO_CHANNELS))
		{
			$flavorParamsObj->setAudioChannels($flavorParamsObj->getColumnsOldValue(assetParamsPeer::AUDIO_CHANNELS));
		}
		if($flavorParamsObj->getColumnsOldValue(assetParamsPeer::AUDIO_SAMPLE_RATE))
		{
			$flavorParamsObj->setAudioSampleRate($flavorParamsObj->getColumnsOldValue(assetParamsPeer::AUDIO_SAMPLE_RATE));
		}
		if($flavorParamsObj->getColumnsOldValue(assetParamsPeer::CONVERSION_ENGINES_EXTRA_PARAMS))
		{
			$flavorParamsObj->setConversionEnginesExtraParams($flavorParamsObj->getColumnsOldValue(assetParamsPeer::CONVERSION_ENGINES_EXTRA_PARAMS));
		}
	}

	/**
	 * @param kClipAttributes $singleAttribute
	 * @return string
	 */
	protected function addEffects($singleAttribute)
	{
		$effects = new kEffectsManager();
		return $effects->getFFMPEGEffects($singleAttribute);
	}

	/**
	 * @param BatchJob $rootJob
	 * @return bool
	 */
	protected function concatJobExist($rootJob)
	{
		if (!$rootJob)
		{
			return false;
		}
		$concatJobs = $rootJob->getChildJobsByTypes(array(BatchJobType::CONCAT));
		return count($concatJobs) > 0;
	}

	protected function getJobDataConversionParams($jobData, $field = null)
	{
		if($jobData instanceof kClipConcatJobData)
		{
			$conversionParamsJson = $jobData->getConversionParams();
			if($conversionParamsJson)
			{
				$conversionParams = json_decode($conversionParamsJson, true);
				if($field)
				{
					return isset($conversionParams[$field]) ? $conversionParams[$field] : null;
				}
				return $conversionParams;
			}
		}
		return null;
	}

	protected function getSortedFiltersComplexForConcat($jobData, $operationAttributesSorted, $clipIndex)
	{
		/** @var kClipConcatJobData $jobData */

		$filters = array();
		$filters["crop"] = $this->getCropFilterForConcat($jobData, $clipIndex);
		$filters["effects"] = $this->getEffectsFilterForConcat($jobData, $operationAttributesSorted, $clipIndex);
		$filters["scale"] = $this->getPaddedScaleFiltersForConcat($jobData, $clipIndex);

		if(!$filters["effects"] || $filters["effects"] == "")
		{
			$filters["whiteBackground"] = $this->getWhiteBackgroundForConcat($jobData);
		}

		foreach ($filters as $ind => $value)
		{
			if(!$value || $value == "")
			{
				unset($filters[$ind]);
			}
		}

		return $filters;
	}

	protected function getMappedSortedFiltersComplex($sortedFilters)
	{
		$mappedFilters = "";
		if(count($sortedFilters) == 1 && isset($sortedFilters["whiteBackground"]))
		{
			return "[0]" . $sortedFilters[0];
		}
		if(count($sortedFilters) > 0)
		{
			$sortedFilterTypes = array_keys($sortedFilters);
			$filterType = $sortedFilterTypes[0];
			$mappedFilters .= "[0:v]" . $sortedFilters[$filterType];

			for ($i = 0; $i < count($sortedFilterTypes) - 1; $i++)
			{
				$filterType = $sortedFilterTypes[$i+1];
				$mappedFilters .= "[vflt$i];[vflt$i]" . $sortedFilters[$filterType];
			}
		}
		return $mappedFilters;
	}

	protected function getPaddedScaleFiltersForConcat($jobData, $clipIndex)
	{
		$filter = "";
		if($this->shouldScaleOnConcat($jobData, $clipIndex))
		{
			$imageToVideo = $this->getJobDataConversionParams($jobData, self::IMAGE_TO_VIDEO);
			$cropped = $this->getJobDataConversionParams($jobData, self::CROP);

			$oh = $this->getJobDataConversionParams($jobData, self::TARGET_HEIGHT);
			$ow = $this->getJobDataConversionParams($jobData, self::TARGET_WIDTH);
			$ih = $imageToVideo || !$cropped ? "ih" : $this->getJobDataConversionParams($jobData, self::CROP_HEIGHT);
			$iw = $imageToVideo || !$cropped ? "iw" : $this->getJobDataConversionParams($jobData, self::CROP_WIDTH);

			$scaleFilter = $this->getScaleFilter($ow, $oh, $iw, $ih);
			$padFilter = $this->getPadFilter($ow, $oh, $iw, $ih);
			$filter = $scaleFilter. "[vscale];[vscale]" . $padFilter;
		}
		return $filter;
	}

	protected function getCropFilterForConcat($jobData, $clipIndex)
	{
		$filter = "";
		$conversionParams = $this->getJobDataConversionParams($jobData);
		$cropData = $this->getCropDataFromConversionParams($conversionParams, $clipIndex);

		if($this->shouldCropOnConcat($jobData, $clipIndex))
		{
			$widthPosition = $cropData["widthPosition"];
			$heightPosition = $cropData["heightPosition"];
			$outWidth = $cropData["outWidth"];
			$outHeight = $cropData["outHeight"];
			$filter = $this->getCropFilter($outWidth, $outHeight, $widthPosition, $heightPosition);
		}

		return $filter;
	}

	protected function getWhiteBackgroundForConcat($jobData)
	{
		/** @var kClipConcatJobData $jobData */

		$whiteBackgroundFilter = null;
		$imageToVideo = $this->getJobDataConversionParams($jobData, self::IMAGE_TO_VIDEO);
		if($imageToVideo)
		{
			// add white background to the video to handle transparency, transparent pixels shows the background color
			$whiteBackgroundFilter = $this->getWhiteBackgroundFilter();
		}
		return $whiteBackgroundFilter;
	}

	protected function getEffectsFilterForConcat($jobData, $operationAttributes, $clipIndex)
	{
		/** @var kClipConcatJobData $jobData */

		$effectsFilter = null;
		if($this->shouldApplyEffectsOnConcat($jobData, $clipIndex))
		{
			$effectsManager = new kEffectsManager();
			$effectsFilter = $effectsManager->addVideoEffects($operationAttributes[$clipIndex]);
		}
		return $effectsFilter;
	}

	protected function shouldApplyEffectsOnConcat($jobData, $clipIndex)
	{
		/** @var kClipConcatJobData $jobData */

		// check if we already used filter_complex on clipping
		$conversionParams = $this->getJobDataConversionParams($jobData);
		$cropped = $this->isCroppedClip($conversionParams, $clipIndex);
		$scaled = $this->getJobDataConversionParams($jobData, self::TARGET_WIDTH);
		$subtitles = $this->isSubtitledClip($conversionParams, $clipIndex);
		$imageToVideo = $this->getJobDataConversionParams($jobData, self::IMAGE_TO_VIDEO);
		return $scaled || $cropped || $imageToVideo || $subtitles;
	}

	protected function shouldScaleOnConcat($jobData, $clipIndex)
	{
		/** @var kClipConcatJobData $jobData */

		$conversionParams = $this->getJobDataConversionParams($jobData);
		$cropped = $this->isCroppedClip($conversionParams, $clipIndex);
		$scaled = $this->getJobDataConversionParams($jobData, self::TARGET_WIDTH);
		$subtitles = $this->isSubtitledClip($conversionParams, $clipIndex);
		$imageToVideo = $this->getJobDataConversionParams($jobData, self::IMAGE_TO_VIDEO);
		return $scaled && ($cropped || $imageToVideo || $subtitles);
	}

	protected function shouldCropOnConcat($jobData, $clipIndex)
	{
		/** @var kClipConcatJobData $jobData */

		$conversionParams = $this->getJobDataConversionParams($jobData);
		$cropped = $this->isCroppedClip($conversionParams, $clipIndex);
		$imageToVideo = $this->getJobDataConversionParams($jobData, self::IMAGE_TO_VIDEO);
		return $imageToVideo && $cropped;
	}

	protected function isCroppedClip($conversionParams, $clipIndex)
	{
		$cropData = $this->getCropDataFromConversionParams($conversionParams, $clipIndex);
		return count($cropData) > 0;
	}

	protected function isSubtitledClip($conversionParams, $clipIndex)
	{
		$subtitleData = $this->getSubtitlesDataFromConversionParams($conversionParams, $clipIndex);
		return count($subtitleData) > 0;
	}

	protected function allowEffectsOnConvert($conversionData, $singleAttributeIndex)
	{
		// do not apply effects if already using filter complex
		if(!$conversionData)
		{
			return true;
		}
		$cropped = $this->isCroppedClip($conversionData, $singleAttributeIndex);
		$subtitles = $this->isSubtitledClip($conversionData, $singleAttributeIndex);
		$imageToVideo = isset($conversionData[self::IMAGE_TO_VIDEO]);
		$scale = isset($conversionData[self::TARGET_WIDTH]);
		return !$imageToVideo && !$scale && !$cropped && !$subtitles;
	}

	protected function getCropDataFromConversionParams($conversionParams, $clipIndex)
	{
		$cropDataArray = isset($conversionParams[self::CROP_DATA_ARRAY]) ? $conversionParams[self::CROP_DATA_ARRAY] : array();
		return isset($cropDataArray[$clipIndex]) ? $cropDataArray[$clipIndex] : array();
	}

	protected function getSubtitlesDataFromConversionParams($conversionParams, $clipIndex)
	{
		$subtitleDataArray = isset($conversionParams[self::SUBTITLES_DATA_ARRAY]) ? $conversionParams[self::SUBTITLES_DATA_ARRAY] : array();
		return isset($subtitleDataArray[$clipIndex]) ? $subtitleDataArray[$clipIndex] : array();
	}

	protected function getScaleFilter($ow, $oh, $iw = "iw", $ih = "ih")
	{
		return "scale=$iw*min($ow/$iw\,$oh/$ih):$ih*min($ow/$iw\,$oh/$ih)";
	}
	protected function getPadFilter($ow, $oh, $iw = "iw", $ih = "ih")
	{
		return "pad=$ow:$oh:($ow-$iw)/2:($oh-$ih)/2";
	}

	protected function getCropFilter($ow, $oh, $wp, $hp)
	{
		return "crop=$ow:$oh:$wp:$hp";
	}

	protected function getWhiteBackgroundFilter()
	{
		return "split=2[bg][fg];[bg]drawbox=c=white@1:replace=1:t=fill[bg];[bg][fg]overlay=format=auto";
	}
}
