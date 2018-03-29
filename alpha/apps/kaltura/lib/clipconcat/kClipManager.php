<?php
/**
 * @package server-infra
 * @subpackage clipconcat
 */

class kClipManager implements kBatchJobStatusEventConsumer
{

	//Todo: create parent job for the concat and pass it to children

	const CLIP_NUMBER = 'clipNumber';

	/***
	 * @param array $dynamicAttributes
	 * @return bool is clip attribute exist in dynamic attribute
	 */
	public static function isClipServiceRequired(array $dynamicAttributes)
	{
		if (count($dynamicAttributes) <= 1)
		{
			return false;
		}

		foreach ($dynamicAttributes as $value)
		{
			if ($value instanceof kClipAttributes)
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * @param string $sourceEntryId
	 * @param entry $clipEntry
	 * @param entry $destEntry
	 * @param $partnerId
	 * @param array $operationAttributes
	 * @param int $priority
	 */
	public function createParentBatchJob($sourceEntryId,$clipEntry, $destEntry, $partnerId, array $operationAttributes, $priority = 0)
	{
		$parentJob = new BatchJob();
		$this->setDummyOriginalFlavorAssetReady($clipEntry->getId());
		$jobData = new kClipConcatJobData();
		$jobData->setDestEntryId($destEntry->getEntryId());
		$jobData->setTempEntryId($clipEntry->getEntryId());
		//if it is replace(Trim flow) active the copy to destenation consumers
		if ($destEntry->getIsTemporary())
		{
			$destEntry->putInCustomData('clipConcatFlow','true');
			$destEntry->save();
		}
		$jobData->setSourceEntryId($sourceEntryId);
		$jobData->setPartnerId($partnerId);
		$jobData->setPriority($priority);
		$jobData->setOperationAttributes($operationAttributes);

		kJobsManager::addJob($parentJob,$jobData,BatchJobType::CLIP_CONCAT);
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

			if ($batchJob->getParentJob() && $batchJob->getParentJob()->getJobType() == BatchJobType::CONVERT)
			{
				$this->startConcat($batchJob->getRootJob());
			}

			elseif($batchJob->getParentJob() && $batchJob->getParentJob()->getJobType()  == BatchJobType::CONCAT )
			{
				$this->concatDone($batchJob);
			}

		}
		catch (Exception $ex)
		{
			KalturaLog::err('Error During Concat Job' . $ex);
			return false;
		}
		return true;
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

		elseif ($batchJob->getRootJob() && $batchJob->getRootJob()->getJobType() == BatchJobType::CLIP_CONCAT)
		{
			return $this->handleClipChildJob($batchJob);
		}

		return false;
	}

	/**
	 * @param kOperationResource $resource
	 * @param entry $dbEntry
	 * @param $operationAttributes
	 * @param $clipEntry
	 */
	public function startBatchJob($resource, entry $dbEntry, $operationAttributes, $clipEntry)
	{
		$internalResource = $resource->getResource();
		if ($internalResource instanceof kFileSyncResource && $internalResource->getOriginEntryId()) {
			$this->createParentBatchJob($internalResource->getOriginEntryId(), $clipEntry, $dbEntry,
													$dbEntry->getPartnerId(), $operationAttributes);
		} else {
			$this->createParentBatchJob(null, $clipEntry, $dbEntry, $dbEntry->getPartnerId(), $operationAttributes);
		}
	}

	/***
	 * @param $sourceFlavorParamId
	 * @return int
	 * @throws PropelException
	 */
	private function cloneFlavorParam($sourceFlavorParamId)
	{
		//flavorParamsObj = getByPk($sourceFlavorParamId);
		$flavorParamsObj = assetParamsPeer::retrieveByPK($sourceFlavorParamId);
		// unset flavorParamsObj ID
		$flavorParamsObj->setId(null);
		$flavorParamsObj->setNew(true);
		$flavorParamsObj->setFormat(flavorParams::CONTAINER_FORMAT_MPEGTS);
		//save the object
		$flavorParamsObj->save();
		//return the object ID
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
		//$flavorAsset->setFlavorParamsId(kClipAttributes::SYSTEM_DEFAULT_FLAVOR_PARAMS_ID);
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
		$flavorAsset->save();
		$flavorAsset->putInCustomData(self::CLIP_NUMBER,$order);

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
			KalturaLog::info('Flavor Param Ids:' .$job->getEntry()->getFlavorParamsIds());
		}
		/** @var kClipConcatJobData $jobData */
		$jobData = $batchJob->getData();

		$tempEntry = entryPeer::retrieveByPK($jobData->getTempEntryId());
		$assets = assetPeer::retrieveByEntryId($jobData->getTempEntryId());
		usort($assets, array("kClipManager","cmpByOrder"));

		$files = $this->getFilesPath($assets);

		$flavorAsset = $this->getNewAssetFromEntry($tempEntry);

		kJobsManager::addConcatJob($batchJob, $flavorAsset, $files,false);

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
	 * @throws PropelException
	 */
	private function handleClipConcatParentJob($batchJob)
	{
		switch ($batchJob->getStatus()) {
			case BatchJob::BATCHJOB_STATUS_PENDING:
				$errDesc = '';
				/**@var kClipConcatJobData $jobData */
				$jobData = $batchJob->getData();
				$this->addClipJobs($batchJob, $jobData->getTempEntryId(), $errDesc,
					$jobData->getPartnerId(),
					$jobData->getOperationAttributes(), $jobData->getPriority());
				kJobsManager::updateBatchJob($batchJob, BatchJob::BATCHJOB_STATUS_PROCESSING);
				break;

			default:
				break;
		}
	}

	/**
	 * @param BatchJob $parentJob  clipConcat job
	 * @param $entryId
	 * @param $errDescription
	 * @param $partnerId
	 * @param array $operationAttributes
	 * @param int $priority
	 * @return BatchJob[]
	 * @throws APIException
	 * @throws PropelException
	 */
	private function addClipJobs($parentJob , $entryId, &$errDescription, $partnerId,
	                             array $operationAttributes, $priority = 0)
	{
		$batchArray = array();
		$order = 0;
		foreach($operationAttributes as $singleAttribute)
		{
			KalturaLog::info("Going To create Flavor for clip: " . print_r($singleAttribute));
			$clonedID =	$this->cloneFlavorParam($singleAttribute->getAssetParamsId());
			$flavorAsst = $this->createTempClipFlavorAsset($partnerId,$entryId,$clonedID,$order);
			$batchJob =	kBusinessPreConvertDL::decideAddEntryFlavor($parentJob, $entryId,
					$clonedID, $errDescription,$flavorAsst->getId()
					, array($singleAttribute) , $priority);
			if(!$batchJob) //todo error;
					return null;
			$batchArray[] = $batchJob;
			$order++;
		}
		return $batchArray;
	}


	/***
	 * @param BatchJob $batchJob
	 * @return bool are all clip batch done
	 */
	private function handleClipChildJob($batchJob)
	{

		$childJobs = $batchJob->getRootJob()->getChildJobs();
		/**
		 * check if all child jobs are finished(all clip jobs and then a single concat job)
		 * @var BatchJob $job */
		foreach ($childJobs as $job)
		{
			if ($job->getStatus() != BatchJob::BATCHJOB_STATUS_FINISHED )
			{
				KalturaLog::info("number of children:   ". count($childJobs));
				KalturaLog::info('Child job id [' . $job->getId() . '] status [' . $job->getStatus() . ']');
				return false;
			}
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
	private function getFilesPath($assets): array
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
	private function getNewAssetFromEntry($tempEntry): flavorAsset
	{

		/** @var flavorAsset $flavorAsset */
		$flavorAsset =  assetPeer::getNewAsset(assetType::FLAVOR);
		// create asset
		$flavorAsset->setPartnerId($tempEntry->getPartnerId());
		$flavorAsset->setEntryId($tempEntry->getId());
		$flavorAsset->setStatus(asset::ASSET_STATUS_QUEUED);
		$flavorAsset->setFlavorParamsId(kClipAttributes::SYSTEM_DEFAULT_FLAVOR_PARAMS_ID);
		//$flavorAsset->setFromAssetParams($flavorParams);
		$flavorAsset->setIsOriginal(false);
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
		$tempEntry->setConversionProfileId(myPartnerUtils::getConversionProfile2ForPartner($partnerId)->getId());
		$tempEntry->save();
		return $tempEntry;
	}

	/**
	 * @param string $entryId
	 * @param  FileSyncKey $concatSyncKey
	 * @throws Exception
	 * @throws KalturaAPIException
	 */
	private function addDestinationEntryAsset($entryId, $concatSyncKey)
	{
		$dbAsset = assetPeer::retrieveOriginalByEntryId($entryId);
		$dbEntry = entryPeer::retrieveByPK($entryId);
		$isNewAsset = false;
		if(!$dbAsset)
		{
			$isNewAsset = true;
			$dbAsset = kFlowHelper::createOriginalFlavorAsset($dbEntry->getPartnerId(), $entryId);
		}

		if(!$dbAsset)
		{
			KalturaLog::err("Flavor asset not created for entry [" . $entryId . "]");

			if($dbEntry->getStatus() == entryStatus::NO_CONTENT)
			{
				$dbEntry->setStatus(entryStatus::ERROR_CONVERTING);
				$dbEntry->save();
			}

			throw new KalturaAPIException(KalturaErrors::ORIGINAL_FLAVOR_ASSET_NOT_CREATED);
		}

		$newSyncKey = $dbAsset->getSyncKey(flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
		kFileSyncUtils::createSyncFileLinkForKey($newSyncKey, $concatSyncKey);

		if($isNewAsset)
			kEventsManager::raiseEvent(new kObjectAddedEvent($dbAsset));
		kEventsManager::raiseEvent(new kObjectDataChangedEvent($dbAsset));

		$mediaInfo = mediaInfoPeer::retrieveByFlavorAssetId($dbAsset->getId());
		if($mediaInfo)
		{
			$newMediaInfo = $mediaInfo->copy();
			$newMediaInfo->setFlavorAssetId($dbAsset->getId());
			$newMediaInfo->save();
		}

		if ($dbAsset->getStatus() == asset::ASSET_STATUS_READY)
		{
			$dbEntry->syncFlavorParamsIds();
			$dbEntry->save();
		}
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
		$concatSyncKey = $concatAsset->getSyncKey(flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
		/** @var kClipConcatJobData $clipConcatJobData */
		$clipConcatJobData = $batchJob->getRootJob()->getData();
		$this->addDestinationEntryAsset($clipConcatJobData->getDestEntryId(), $concatSyncKey);
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

		try
		{
			$wrapper = objectWrapperBase::getWrapperClass($entryToDelete);
			$wrapper->removeFromCache("entry", $entryToDelete->getId());
		}
		catch(Exception $e)
		{
			KalturaLog::err($e);
		}
	}


}