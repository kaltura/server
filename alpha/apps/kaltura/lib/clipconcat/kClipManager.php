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
	 * @param string $sourceEntryId
	 * @param entry $clipEntry
	 * @param entry $destEntry
	 * @param $partnerId
	 * @param array $operationAttributes
	 * @param int $priority
	 * @param string $importUrl
	 */
	public function createParentBatchJob($sourceEntryId,$clipEntry, $destEntry, $partnerId, array $operationAttributes, $priority = 0, $importUrl = null)
	{
		$parentJob = new BatchJob();
		$parentJob->setPartnerId($partnerId);
		$parentJob->setEntryId($clipEntry->getEntryId());

		$trackEntry = new TrackEntry();
		$trackEntry->setEntryId($destEntry->getEntryId());
		$trackEntry->setTrackEventTypeId(TrackEntry::TRACK_ENTRY_EVENT_TYPE_CLIP);
		$trackEntry->setDescription("source entry id: [" .$sourceEntryId. "],  template entry id: [" .$clipEntry->getEntryId(). "].");
		TrackEntry::addTrackEntry($trackEntry);

		$jobData = new kClipConcatJobData($importUrl);
		if(!$jobData->getImportNeeded())
		{
			$this->setDummyOriginalFlavorAssetReady($clipEntry->getId());
		}
		$jobData->setDestEntryId($destEntry->getEntryId());
		$jobData->setTempEntryId($clipEntry->getEntryId());

		//if it is replace(Trim flow) active the copy to destination consumers
		$this->fillDestEntry($destEntry, $sourceEntryId, $operationAttributes);

		$jobData->setSourceEntryId($sourceEntryId);
		$jobData->setPartnerId($partnerId);
		$jobData->setPriority($priority);

		$jobData->setOperationAttributes($operationAttributes);
		kJobsManager::addJob($parentJob, $jobData, BatchJobType::CLIP_CONCAT);
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
			if ($batchJob->getJobType() == BatchJobType::CLIP_CONCAT)
			{
				$this->handleClipConcatParentJob($batchJob);
			}
			elseif ($this->isImportFinished($batchJob))
			{
				$this->handleImportFinished($batchJob->getRootJob());
			}
			elseif ($this->isImportFailed($batchJob))
			{
				//When import fails the concat job would have been stuck in queue, in this case we need to fail the concat job as well
				$this->handleImportFailed($batchJob->getRootJob());
			}
			elseif ($this->shouldStartConcat($batchJob))
			{
				$this->startConcat($batchJob->getRootJob());
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

	protected function isImportFinished(BatchJob $batchJob)
	{
		return 	$batchJob->getJobType() == BatchJobType::IMPORT && $batchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FINISHED;
	}
	
	protected function isImportFailed(BatchJob $batchJob)
	{
		return 	$batchJob->getJobType() == BatchJobType::IMPORT && in_array($batchJob->getStatus(), array(BatchJob::BATCHJOB_STATUS_FAILED, BatchJob::BATCHJOB_STATUS_FATAL, BatchJob::BATCHJOB_STATUS_ABORTED));
	}
	
	protected function shouldStartConcat(BatchJob $batchJob)
	{
		return 	$batchJob->getParentJob() &&
				$batchJob->getParentJob()->getJobType() == BatchJobType::CONVERT &&
				!$this->concatJobExist($batchJob->getRootJob());
	}
	
	protected function isConcatFinished(BatchJob $batchJob)
	{
		return 	$batchJob->getParentJob() &&
				$batchJob->getParentJob()->getJobType() == BatchJobType::CONCAT;
	}

	protected function handleImportFinished($rootJob)
	{
		/**@var kClipConcatJobData $jobData */
		$jobData = $rootJob->getData();
		$jobData->setImportNeeded(false);
		$rootJob->setData($jobData);
		kEventsManager::raiseEventDeferred(new kBatchJobStatusEvent($rootJob));
	}
	
	protected function handleImportFailed(BatchJob $rootJob)
	{
		$rootJob->setStatus(BatchJob::BATCHJOB_STATUS_FAILED);
		$rootJob->setMessage("Failed to import source file for clipping");
		$rootJob->save();
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

		if(!$batchJob->getRootJob())
		{
			return false;
		}

		if($batchJob->getRootJob()->getJobType() != BatchJobType::CLIP_CONCAT)
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
	 * @param entry $dbEntry
	 * @param $operationAttributes
	 * @param $clipEntry
	 * @param $importUrl
	 */
	public function startBatchJob($resource, entry $dbEntry, $operationAttributes, $clipEntry, $importUrl = null)
	{
		$entryInternalResource = $resource->getResource()->getOriginEntryId();
		if ($importUrl)
		{
			$this->createParentBatchJob($entryInternalResource, $clipEntry, $dbEntry, $dbEntry->getPartnerId(), $operationAttributes, 0 , $importUrl);
		}
		else
		{
			$this->createParentBatchJob($entryInternalResource, $clipEntry, $dbEntry, $dbEntry->getPartnerId(), $operationAttributes);
		}
	}

	/***
	 * @param kClipAttributes $singleAttribute
	 * @param string $originalConversionEnginesExtraParams
	 * @param string $encryptionKey
	 * @return int
	 * @throws kCoreException
	 */
	private function cloneFlavorParam($singleAttribute, $originalConversionEnginesExtraParams, $encryptionKey = null)
	{
		$flavorParamsObj = assetParamsPeer::getTempAssetParamByPk(kClipAttributes::SYSTEM_DEFAULT_FLAVOR_PARAMS_ID);
		$flavorParamsObj->setFormat(flavorParams::CONTAINER_FORMAT_MPEGTS);
		$this->fixConversionParam($flavorParamsObj, $singleAttribute, $originalConversionEnginesExtraParams);
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
	private function createTempClipFlavorAsset($partnerId, $entryId, $flavorParamId,$order)
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
	private function startConcat($batchJob)
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
		usort($assets, array("kClipManager","cmpByOrder"));

		$files = $this->getFilesPath($assets);

		$flavorAsset = $this->addNewAssetToTargetEntry($tempEntry);

		//calling addConcatJob only if lock succeeds
		$store = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_LOCK_KEYS);
		$lockKey = "kclipManager_add_concat_job" . $batchJob->getId();
		if (!$store || $store->add($lockKey, true, self::LOCK_EXPIRY))
		{
			kJobsManager::addConcatJob($batchJob, $flavorAsset, $files,false);
		}
	}

	/**
	 * @param $a flavorAsset
	 * @param $b flavorAsset
	 * @return int
	 */
	private function cmpByOrder($a,$b)
	{
		$aClipNumber = $a->getFromCustomData(self::CLIP_NUMBER);
		$bClipNumber = $b->getFromCustomData(self::CLIP_NUMBER);
		if (!$aClipNumber )
		{
			return -1;
		}
		if (!$bClipNumber )
		{
			return 1;
		}
		return  ($aClipNumber < $bClipNumber) ? -1 : 1;

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
					//start child clip jobs
					$errDesc = '';
					$this->addClipJobs($batchJob, $jobData->getTempEntryId(), $errDesc,
						$jobData->getPartnerId(),
						$jobData->getOperationAttributes(), kConvertJobData::TRIMMING_FLAVOR_PRIORITY);
					kJobsManager::updateBatchJob($batchJob, BatchJob::BATCHJOB_STATUS_ALMOST_DONE);
				}
				break;
			default:
				break;
		}
	}

	/**
	 * @param BatchJob $parentJob clipConcat job
	 * @param $entryId
	 * @param $errDescription
	 * @param $partnerId
	 * @param array $operationAttributes
	 * @param int $priority
	 * @return BatchJob[]
	 * @throws APIException
	 * @throws kCoreException
	 */
	private function addClipJobs($parentJob , $entryId, &$errDescription, $partnerId,
								 array $operationAttributes, $priority = 0)
	{
		$batchArray = array();
		$order = 0;
		$originalConversionEnginesExtraParams =
			assetParamsPeer::retrieveByPK(kClipAttributes::SYSTEM_DEFAULT_FLAVOR_PARAMS_ID)->getConversionEnginesExtraParams();

		$originalFlavorAsset = assetPeer::retrieveOriginalByEntryId($entryId);
		$encryptionKey = null;
		if ($originalFlavorAsset)
		{
			$encryptionKey = $originalFlavorAsset->getEncryptionKey();
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

			$clonedID =	$this->cloneFlavorParam($singleAttribute, $originalConversionEnginesExtraParams, $encryptionKey);
			$flavorAsst = $this->createTempClipFlavorAsset($partnerId,$entryId,$clonedID,$order);
			$batchJob =	kBusinessPreConvertDL::decideAddEntryFlavor($parentJob, $entryId,
					$clonedID, $errDescription,$flavorAsst->getId()
					, array($singleAttribute) , $priority);
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
			/*** @var array $fileSync */
			if ($fileSync[0]->getFullPath())
			{
				$files[] = $fileSync[0]->getFullPath();
			}
		}
		return $files;
	}

	/**
	 * @param entry $tempEntry
	 * @return flavorAsset
	 * @throws kCoreException
	 */
	private function addNewAssetToTargetEntry($tempEntry)
	{

		/** @var flavorAsset $flavorAsset */
		$flavorAsset =  assetPeer::getNewAsset(assetType::FLAVOR);
		// create asset
		$flavorAsset->setPartnerId($tempEntry->getPartnerId());
		$flavorAsset->setEntryId($tempEntry->getId());
		$flavorAsset->setStatus(asset::ASSET_STATUS_QUEUED);
		$flavorAsset->setFlavorParamsId(kClipAttributes::SYSTEM_DEFAULT_FLAVOR_PARAMS_ID);
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
	public function createTempEntryForClip($partnerId)
	{
		$tempEntry = new entry();
		$tempEntry->setType(entryType::MEDIA_CLIP);
		$tempEntry->setMediaType(entry::ENTRY_MEDIA_TYPE_VIDEO);
		$tempEntry->setName('TEMP_'.time());
		$tempEntry->setPartnerId($partnerId);
		$tempEntry->setStatus(entryStatus::NO_CONTENT);
		$tempEntry->setDisplayInSearch(EntryDisplayInSearchType::SYSTEM);
		$tempEntry->setSourceType(EntrySourceType::CLIP);
		$tempEntry->setKuserId(kCurrentContext::getCurrentKsKuserId());
		$tempEntry->setConversionProfileId(myPartnerUtils::getConversionProfile2ForPartner($partnerId)->getId());
		$tempEntry->setIsTemporary(true);
		$tempEntry->save();
		KalturaLog::info('Temp ClipConcat Entry Created, Entry ID:  ' . $tempEntry->getId());
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
		if(!$dbAsset)
		{
			$isNewAsset = true;
			$dbAsset = kFlowHelper::createOriginalFlavorAsset($dbEntry->getPartnerId(), $entryId, $concatAsset->getFileExt());
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
		/** @var kConcatJobData $concatJobData */
		$concatJobData = $batchJob->getParentJob()->getData();
		$concatAsset = assetPeer::retrieveById($concatJobData->getFlavorAssetId());
		/** @var kClipConcatJobData $clipConcatJobData */
		$clipConcatJobData = $batchJob->getRootJob()->getData();
		$this->addDestinationEntryAsset($clipConcatJobData->getDestEntryId(), $concatAsset);
		$this->deleteEntry($clipConcatJobData->getTempEntryId());
		kJobsManager::updateBatchJob($batchJob->getRootJob(), BatchJob::BATCHJOB_STATUS_FINISHED);
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
	 * @return string
	 */
	private function editConversionEngineExtraParam($conversionEngines, $singleAttribute, $conversionExtraParamsArray = array())
	{
		$newConversionExtraParams = array();
		for ($i = 0; $i < count($conversionEngines) ; $i++)
		{
			$extraParams = '';
			if($i < count($conversionExtraParamsArray))
				$extraParams = $conversionExtraParamsArray[$i];
			if ($conversionEngines[$i] == conversionEngineType::FFMPEG || $conversionEngines[$i] == conversionEngineType::FFMPEG_AUX)
					$extraParams .= $this->addEffects($singleAttribute);
			$newConversionExtraParams[] = $extraParams;
		}
		return implode(' | ',$newConversionExtraParams);
	}

	/**
	 * @param assetParams $flavorParamsObj
	 * @param kClipAttributes $singleAttribute
	 * @param string $originalConversionEnginesExtraParams
	 */
	private function fixConversionParam($flavorParamsObj, $singleAttribute, $originalConversionEnginesExtraParams)
	{
		$conversionEngines = explode(',', $flavorParamsObj->getConversionEngines());
		if (is_null($originalConversionEnginesExtraParams))
			$newExtraConversionParams = $this->editConversionEngineExtraParam($conversionEngines, $singleAttribute);
		else {
			$conversionExtraParams = explode('|', $originalConversionEnginesExtraParams);
			$newExtraConversionParams =
				$this->editConversionEngineExtraParam($conversionEngines, $singleAttribute,$conversionExtraParams);
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
