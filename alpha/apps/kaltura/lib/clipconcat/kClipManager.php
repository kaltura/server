<?php
/**
 * @package server-infra
 * @subpackage clipconcat
 */

class kClipManager implements kBatchJobStatusEventConsumer
{
	const CLIP_NUMBER = 'clipNumber';
	const LOCK_EXPIRY = 10;

	/**
	 * @param kOperationResource $resource
	 * @param entry $destEntry
	 * @param entry $clipEntry
	 * @param string $importUrl
	 * @param int $rootJobId
	 * @param int $resourceOrder
	 * @param bool $fillDest
	 */
	public function createClipConcatJobFromResource($resource, $destEntry, $clipEntry, $importUrl = null, $rootJobId = null, $resourceOrder = null, $fillDest = true)
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
		if($resourceOrder !== null)
		{
			// clip concat order for multi clip concat
			$jobData->setResourceOrder($resourceOrder);
		}

		$jobData->setDestEntryId($destEntry->getId());
		$jobData->setTempEntryId($clipEntryId);

		if($fillDest)
		{
			//if it is replace(Trim flow) active the copy to destination consumers
			$this->fillDestEntry($destEntry, $sourceEntryId, $operationAttributes);
		}

		$jobData->setSourceEntryId($sourceEntryId);
		$jobData->setPartnerId($partnerId);
		$jobData->setPriority(0);
		$jobData->setOperationAttributes($operationAttributes);
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
			$rootJob = $this->getAncestorJob($batchJob);
			if ($batchJob->getJobType() == BatchJobType::CLIP_CONCAT)
			{
				$this->handleClipConcatParentJob($batchJob);
			}
			elseif ($this->isImportFinished($batchJob))
			{
				$this->handleImportFinished($batchJob->getParentJob());
			}
			elseif ($this->isImportFailed($batchJob))
			{
				//When import fails the concat job would have been stuck in queue, in this case we need to fail the concat job as well
				$this->handleImportFailed($batchJob);
			}
			elseif($this->shouldStartMultiResourceConcat($batchJob))
			{
				$this->startMultiResourceConcat($rootJob);
			}
			elseif ($this->shouldStartSingleResourceConcat($batchJob))
			{
				$this->startSingleResourceConcat($rootJob);
			}
			elseif($this->isConcatFinished($batchJob))
			{
				$this->concatDone($batchJob);
			}
		}
		catch (Exception $ex)
		{
			KalturaLog::err('Error During Concat Job [' . $ex->getMessage() . ']');
		}
		return true;
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
	
	protected function shouldStartSingleResourceConcat(BatchJob $batchJob)
	{
		$root = $this->getAncestorJob($batchJob);
		if($root && $root->getJobType() != BatchJobType::CLIP_CONCAT)
		{
			return false;
		}
		if($batchJob->getParentJob() && $batchJob->getParentJob()->getJobType() == BatchJobType::CONVERT)
		{
			return !$this->concatJobExist($root);
		}
		return false;
	}
	
	protected function isConcatFinished(BatchJob $batchJob)
	{
		return 	$batchJob->getParentJob() &&
				$batchJob->getParentJob()->getJobType() == BatchJobType::CONCAT;
	}
	
	protected function isConcatOfAllChildrenDone(BatchJob $batchJob)
	{
		foreach ($batchJob->getChildJobs() as $job)
		{
			if(in_array($job->getJobType(), array(BatchJobType::CONVERT, BatchJobType::CONCAT, BatchJobType::POSTCONVERT)))
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
	
	protected function getAllConcatJobsFlavors(BatchJob  $batchJob)
	{
		$root = $batchJob->getRootJob();
		$flavors = array();
		foreach ($root->getChildJobs() as $job)
		{
			/** @var BatchJob $job */
			if ($job->getJobType() == BatchJobType::CONCAT)
			{
				$flavors[] = assetPeer::retrieveById($job->getData()->getFlavorAssetId());
			}
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
	
	protected function handleImportFailed(BatchJob $batchJob)
	{
		$parentJob = $batchJob->getParentJob();
		$parentJob->setStatus(BatchJob::BATCHJOB_STATUS_FAILED);
		$parentJob->setMessage("Failed to import source file for clipping ");
		$parentJob->save();

		$rootJob = $batchJob->getRootJob();
		if($rootJob->getId() != $parentJob->getId())
		{
			$rootJob->setStatus(BatchJob::BATCHJOB_STATUS_FAILED);
			$rootJob->setMessage("Failed to import source file for clipping for entry Id [" . $batchJob->getEntryId() ."]");
			$rootJob->save();
		}
	}
	
	/**
	 * @param BatchJob $batchJob
	 * @return bool true if the consumer should handle the event
	 */
	public function shouldConsumeJobStatusEvent(BatchJob $batchJob)
	{
		if($batchJob->getJobType() == BatchJobType::MULTI_CLIP_CONCAT)
		{
			return false;
		}

		if ($batchJob->getJobType() == BatchJobType::CLIP_CONCAT)
		{
			return true;
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
	 * @param string $rootJobId
	 * @param bool $fillDest
	 */
	public function startClipConcatBatchJob($resource, $destEntry, $clipEntry, $importUrl = null, $rootJobId = null, $order = null, $fillDest = false)
	{
		$this->createClipConcatJobFromResource($resource, $destEntry , $clipEntry, $importUrl, $rootJobId, $order, $fillDest);
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
	public function addMultiClipTrackEntries($sourceEntryIds, $multiTempEntryId, $tempEntriesIds, $destEntryId)
	{
		$description = "source entry Ids : [" . implode(",", $sourceEntryIds) . "] template entry ids: [" . implode(",",$tempEntriesIds) . "].";
		$this->addClipTrackEntry($multiTempEntryId, $description);

		$description = "source entry ids: [" . implode(",", $sourceEntryIds) . "],  multi template entry id: [" . $multiTempEntryId . "].";
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
	 * @return int
	 * @throws kCoreException
	 */
	private function cloneFlavorParam($singleAttribute, $originalConversionEnginesExtraParams, $encryptionKey = null, $isAudio = false)
	{
		$flavorParamsObj = assetParamsPeer::getTempAssetParamByPk(kClipAttributes::SYSTEM_DEFAULT_FLAVOR_PARAMS_ID);
		$flavorParamsObj->setFormat(flavorParams::CONTAINER_FORMAT_MPEGTS);
		$this->fixConversionParam($flavorParamsObj, $singleAttribute, $originalConversionEnginesExtraParams, $isAudio);
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
	private function setDummyOriginalFlavorAssetReady($entryId)
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
	private function createTempClipFlavorAsset($partnerId, $entryId, $flavorParamId, $order)
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
	private function startSingleResourceConcat($batchJob)
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
			$flavorAsset = $this->addNewAssetToTargetEntry($tempEntry, $assetId);
			
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
	private function cmpByClipOrder($a, $b)
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
	private function cmpByResourceOrder($a, $b)
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
	private function cmpInt($a, $b)
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
	 * @throws APIException
	 * @throws kCoreException
	 */
	private function handleClipConcatParentJob($batchJob)
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
					$this->addClipJobsFromBatchJob($batchJob, $jobData);
				}
				break;
			default:
				break;
		}
	}
	
	/**
	 * @param $batchJob
	 * @param $jobData
	 * @throws APIException
	 * @throws kCoreException
	 * Prepare Extract Media jobs for additional flavors if exist, and run the clipping
	 */
	protected function addClipJobsFromBatchJob($batchJob, $jobData)
	{
		/**@var kClipConcatJobData $jobData */
		$rootJob = $batchJob->getRootJob();
		$flavorAssetsToBeProcessed = array();
		$audioAssets = array();

		// audio clipping is not supported for multi clip flow
		if(!$rootJob || $rootJob->getJobType() != BatchJobType::MULTI_CLIP_CONCAT)
		{
			$audioAssets = assetPeer::retrieveAudioFlavorsByEntryID($jobData->getSourceEntryId(), array(asset::ASSET_STATUS_READY));
			$flavorAssetsToBeProcessed = $audioAssets;
		}

		$flavorAssetsToBeProcessed[] = assetPeer::retrieveOriginalByEntryId($jobData->getTempEntryId());
		foreach($flavorAssetsToBeProcessed as $asset)
		{
			/** @var flavorAsset $asset */
			//start child clip jobs
			$isAudio = false;
			if (in_array($asset, $audioAssets))
			{
				$isAudio = true;
			}
			$errDesc = '';
			$this->addClipJobs($batchJob, $jobData->getTempEntryId(), $errDesc, $jobData->getPartnerId(),
								 $jobData->getOperationAttributes(), $asset->getId(), kConvertJobData::TRIMMING_FLAVOR_PRIORITY, $isAudio);
		}
		kJobsManager::updateBatchJob($batchJob, BatchJob::BATCHJOB_STATUS_ALMOST_DONE);
	}

	/**
	 * @param BatchJob $parentJob clipConcat job
	 * @param $entryId
	 * @param $errDescription
	 * @param $partnerId
	 * @param array $operationAttributes
	 * @param string $assetId
	 * @param int $priority
	 * @param bool $isAudio
	 * @return BatchJob[]
	 * @throws APIException
	 * @throws kCoreException
	 */
	private function addClipJobs($parentJob , $entryId, &$errDescription, $partnerId,
								 array $operationAttributes, $assetId, $priority = 0, $isAudio = false)
	{
		$batchArray = array();
		$order = 0;
		$originalConversionEnginesExtraParams =
			assetParamsPeer::retrieveByPK(kClipAttributes::SYSTEM_DEFAULT_FLAVOR_PARAMS_ID)->getConversionEnginesExtraParams();
		
		$injectedFlavorAsset = assetPeer::retrieveById($assetId);
		$encryptionKey = null;
		if ($injectedFlavorAsset)
		{
			$encryptionKey = $injectedFlavorAsset->getEncryptionKey();
		}

		/* @var $singleAttribute kClipAttributes */
		foreach($operationAttributes as $singleAttribute)
		{
			KalturaLog::info("Going To create Flavor for clip: " . print_r($singleAttribute, true));
			if($singleAttribute->getDuration()<=0)
			{
				KalturaLog::info("Ignoring clip attribute with non-positive duration");
				continue;
			}

			$clonedID =	$this->cloneFlavorParam($singleAttribute, $originalConversionEnginesExtraParams, $encryptionKey, $isAudio);
			$flavorAsst = $this->createTempClipFlavorAsset($partnerId,$entryId,$clonedID,$order);
			$flavorAsst->setActualSourceAssetParamsIds($assetId);
			$batchJob =	kBusinessPreConvertDL::decideAddEntryFlavor($parentJob, $entryId,
					$clonedID, $errDescription,$flavorAsst->getId()
					, array($singleAttribute) , $priority, $injectedFlavorAsset);
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
	private function areAllClipJobsDone($batchJob)
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
	private function getFilesPath($assets)
	{
		$files = array();
		foreach ($assets as $asset) {
			/**
			 * Don't take source it is empty
			 * @var flavorAsset $asset */
			if ($asset->getIsOriginal()) {
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
	 * @return flavorAsset
	 * @throws kCoreException
	 */
	private function addNewAssetToTargetEntry($tempEntry, $assetId)
	{

		/** @var flavorAsset $flavorAsset */
		$flavorAsset =  assetPeer::getNewAsset(assetType::FLAVOR);
		// create asset
		$flavorAsset->setPartnerId($tempEntry->getPartnerId());
		$flavorAsset->setEntryId($tempEntry->getId());
		$flavorAsset->setStatus(asset::ASSET_STATUS_QUEUED);
		$asset = assetPeer::retrieveById($assetId);
		if ($asset->getFlavorParamsId() != kClipAttributes::SYSTEM_DEFAULT_FLAVOR_PARAMS_ID)
		{
			$flavorAsset->setFlavorParamsId($asset->getFlavorParamsId());
		}
		else
		{
			$flavorAsset->setFlavorParamsId(kClipAttributes::SYSTEM_DEFAULT_FLAVOR_PARAMS_ID);
		}
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
	private function addDestinationEntryAsset($entryId, $concatAsset)
	{
		$concatSyncKey = $concatAsset->getSyncKey(flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
		$dbAsset = assetPeer::retrieveOriginalByEntryId($entryId);
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
	private function concatDone(BatchJob $batchJob)
	{
		$rootJob = $batchJob->getRootJob();

		/** @var $clipConcatJobData kMultiClipConcatJobData|kClipConcatJobData */
		$clipConcatJobData = $rootJob->getData();

		kJobsManager::updateBatchJob($rootJob, BatchJob::BATCHJOB_STATUS_FINISHED);

		if($rootJob->getJobType() == BatchJobType::MULTI_CLIP_CONCAT && !$this->isClipConcatChildrenFinished($rootJob))
		{
			return;
		}

		if ($this->isConcatOfAllChildrenDone($rootJob))
		{
			$destinationEntry = $clipConcatJobData->getDestEntryId();
			$listOfFlavorAssets = $this->getAllConcatJobsFlavors($batchJob);
			//collect all assets from temp entry and add them to the dest entry
			foreach ($listOfFlavorAssets as $flavorAsset)
			{
				$this->addDestinationEntryAsset($destinationEntry, $flavorAsset);
			}
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
	 * @param BatchJob $batchJob
	 * @return bool
	 */
	private function shouldStartMultiResourceConcat($batchJob)
	{
		$rootJob = $this->getAncestorJob($batchJob);
		if($rootJob->getJobType() != BatchJobType::MULTI_CLIP_CONCAT)
		{
			return false;
		}
		if($batchJob->getParentJob() && $batchJob->getParentJob()->getJobType() == BatchJobType::CONVERT)
		{
			return !$this->concatJobExist($rootJob) && $this->isClipConcatChildrenFinished($rootJob);
		}
		return false;
	}

	/**
	 * @param BatchJob $batchJob
	 * @throws kCoreException
	 */
	private function startMultiResourceConcat(BatchJob $batchJob)
	{
		if($batchJob->getJobType() != BatchJobType::MULTI_CLIP_CONCAT)
		{
			return;
		}

//		$assetId = null; //TODO: add one asset for concatenated output
		$tempEntry = entryPeer::retrieveByPK($batchJob->getData()->getMultiTempEntryId());
//		$flavorAsset = $this->addNewAssetToTargetEntry($tempEntry, $assetId);
		$outAssetId = null;

		$batchJobs = array_filter($batchJob->getChildJobs(), function ($job) {return $job->getJobType() == BatchJobType::CLIP_CONCAT;});
		usort($batchJobs, array("kClipManager", "cmpByResourceOrder"));

		$allRelatedFiles = array();
		foreach ($batchJobs as $childJob)
		{
			KalturaLog::debug('Going To Start Multi Concat Job');
			kJobsManager::updateBatchJob($childJob, BatchJob::BATCHJOB_STATUS_FINISHED);

			/** @var kClipConcatJobData $jobData */
			$jobData = $childJob->getData();

			$assets = assetPeer::retrieveByEntryId($jobData->getTempEntryId(), array(assetType::FLAVOR));
			usort($assets, array("kClipManager", "cmpByClipOrder"));

			$files = $this->getFilesPath($assets);
			foreach ($files as $assetId => $relatedFiles)
			{
				/** @var array $relatedFiles */
				KalturaLog::debug("Related file : " . print_r($relatedFiles, true));
				//TODO remove from here
				$outAssetId = $assetId;
				$allRelatedFiles = array_merge($allRelatedFiles, $relatedFiles);
			}
			$this->deleteEntry($jobData->getTempEntryId());
		}
		$flavorAsset = $this->addNewAssetToTargetEntry($tempEntry, $outAssetId);

		// TODO check if needed to set version
//		$flavorAsset->setVersion(0);
		//calling addConcatJob only if lock succeeds
		$store = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_LOCK_KEYS);
		$lockKey = 'kclipManager_add_concat_job' . $batchJob->getId() . $flavorAsset->getId();
		if (!$store || $store->add($lockKey, true, self::LOCK_EXPIRY))
		{
			kJobsManager::addConcatJob($batchJob, $flavorAsset, $allRelatedFiles, false);
		}
	}

	/**
	 * @param $entryId
	 * @param null $entryType
	 * @throws Exception
	 * @throws KalturaAPIException
	 */
	private function deleteEntry($entryId, $entryType = null)
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
	private function updateMediaFlowOnAsset($dbAsset)
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
	private function syncFlavorParamToAsset($dbAsset, $dbEntry)
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
	private function updateAssetFailedToConvert($entryId, $dbEntry)
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
	private function updateAssetState($concatSyncKey, $dbAsset, $isNewAsset, $dbEntry)
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
	private function fillDestEntry($destEntry, $sourceEntryId, array $operationAttributes)
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
	private function editConversionEngineExtraParam($conversionEngines, $singleAttribute, $conversionExtraParamsArray = array(), $isAudio = false)
	{
		$newConversionExtraParams = array();
		for ($i = 0; $i < count($conversionEngines) ; $i++)
		{
			$extraParams = '';
			if($i < count($conversionExtraParamsArray))
				$extraParams = $conversionExtraParamsArray[$i];
			if (!$isAudio && ($conversionEngines[$i] == conversionEngineType::FFMPEG || $conversionEngines[$i] == conversionEngineType::FFMPEG_AUX))
					$extraParams .= $this->addEffects($singleAttribute);
			$newConversionExtraParams[] = $extraParams;
		}
		return implode(' | ',$newConversionExtraParams);
	}

	/**
	 * @param assetParams $flavorParamsObj
	 * @param kClipAttributes $singleAttribute
	 * @param string $originalConversionEnginesExtraParams
	 * @param bool $isAudio
	 */
	private function fixConversionParam($flavorParamsObj, $singleAttribute, $originalConversionEnginesExtraParams, $isAudio)
	{
		$conversionEngines = explode(',', $flavorParamsObj->getConversionEngines());
		if (is_null($originalConversionEnginesExtraParams))
			$newExtraConversionParams = $this->editConversionEngineExtraParam($conversionEngines, $singleAttribute, null, $isAudio);
		else {
			$conversionExtraParams = explode('|', $originalConversionEnginesExtraParams);
			$newExtraConversionParams =
				$this->editConversionEngineExtraParam($conversionEngines, $singleAttribute,$conversionExtraParams,$isAudio);
		}
		$flavorParamsObj->setConversionEnginesExtraParams($newExtraConversionParams);
	}

	/**
	 * @param kClipAttributes $singleAttribute
	 * @return string
	 */
	private function addEffects($singleAttribute)
	{
		$effects = new kEffectsManager();
		return $effects->getFFMPEGEffects($singleAttribute);
	}

	/**
	 * @param BatchJob $rootJob
	 * @return bool
	 */
	private function concatJobExist($rootJob)
	{
		if (!$rootJob)
			return false;

		/** @var BatchJob $job */
		foreach ($rootJob->getChildJobs() as $job)
		{
			if ($job->getJobType() == BatchJobType::CONCAT)
				return true;
		}
		return false;
	}
	
}
