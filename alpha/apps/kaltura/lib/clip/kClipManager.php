<?php
/**
 * @package server-infra
 * @subpackage clip
 */

class kClipManager implements kBatchJobStatusEventConsumer
{

	//Todo: create parent job for the concat and pass it to children
	private $batchArray;
	private $tempFlavorParams;

	public function createParentBatchJob($entryId,$partnerId, array $operationAttributes, $priority = 0)
	{
		$parentDummyJob = new BatchJob();
		$jobData = new kClipConcatJobData();
		$jobData->setEntryId($entryId);
		$jobData->setPartnerId($partnerId);
		$jobData->setPriority($priority);
		$jobData->setOperationAttributes($operationAttributes);

		kJobsManager::addJob($parentDummyJob,$jobData,BatchJobType::CLIP_CONCAT);
		return $parentDummyJob;
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
		//save the object
		$flavorParamsObj->save();
		//return the object ID
		$this->tempFlavorParams[] = $flavorParamsObj->getId();
		return $flavorParamsObj->getId();
	}


	/***
	 * @param array $dynamicAttributes
	 * @return bool is clip attribute exist in dynamic attribute
	 */
	public function isClipServiceRequired(array $dynamicAttributes)
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
	 * @param int $partnerId
	 * @param string $entryId
	 * @return flavorAsset
	 */
	private function createDummyOriginalFlavorAsset($partnerId, $entryId)
	{

		$flavorAsset = assetPeer::retrieveOriginalByEntryId($entryId);
		if($flavorAsset)
		{
			//set Dummy Ready we will update it later
			$flavorAsset->setStatus(flavorAsset::ASSET_STATUS_READY);
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
		$flavorAsset = flavorAsset::getInstance();
		$flavorAsset->setStatus(flavorAsset::ASSET_STATUS_READY);
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
	 * @param int $partnerId
	 * @param string $entryId
	 * @param $flavorParamId
	 * @return flavorAsset
	 */
	private function createTempClipFlavorAsset($partnerId, $entryId, $flavorParamId)
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

		return $flavorAsset;
	}

	/**
	 * @param BatchJob $batchJob
	 * @return bool true if should continue to the next consumer
	 */
	public function updatedJob(BatchJob $batchJob)
	{
		$this->startConcat($batchJob);
		return true;
	}

	/**
	 * @param BatchJob $batchJob
	 * @return bool true if the consumer should handle the event
	 * @throws APIException
	 * @throws PropelException
	 */
	public function shouldConsumeJobStatusEvent(BatchJob $batchJob)
	{

		if ($batchJob->getJobType() != BatchJobType::CLIP_CONCAT)
		{
			return false;
		}
		if (!$batchJob->getData())
		{
			KalturaLog::err("No Data Element Provided");
			return false;
		}
		if (!$batchJob->getJobSubType())
		{
			$this->handleClipConcatParentJob($batchJob);
		}
		else
		{
			return $this->handleClipChildJob($batchJob);
		}
		return false;
	}


	/**
	 * @param BatchJob $batchJob
	 */
	private function startConcat($batchJob)
	{
		$parentJob = $batchJob->getParentJob();
		foreach ($parentJob->getChildJobs() as $job)
		{
			/** @var BatchJob $job */
			KalturaLog::err('Child job id [' . $job->getId() . '] status [' . $job->getStatus() . ']');
			KalturaLog::err($job->getStatus());
			KalturaLog::err($job->getEntry()->getFlavorParamsIds());
		}

		KalturaLog::err("############################# Ready For Concat #########################################");
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
				$this->decideAddClipEntryFlavor($batchJob, $jobData->getEntryId(), $errDesc,
					$jobData->getPartnerId(),
					$jobData->getOperationAttributes(), $jobData->getPriority());
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
	private function decideAddClipEntryFlavor($parentJob ,$entryId, &$errDescription,$partnerId,
	                                          array $operationAttributes, $priority = 0)
	{
		$this->batchArray = array();
		$this->createDummyOriginalFlavorAsset($partnerId,$entryId);
		foreach($operationAttributes as $singleAttribute)
		{
			KalturaLog::info("Going To create Flavor for clip: " . print_r($singleAttribute));
			//$dbAsset = kFlowHelper::createOriginalFlavorAsset($partnerId, $entryId);
			$clonedID =	$this->cloneFlavorParam($singleAttribute->getAssetParamsId());
			$this->batchArray[] =
				kBusinessPreConvertDL::decideAddEntryFlavor($parentJob, $entryId,
					$clonedID, $errDescription, $this->createTempClipFlavorAsset($partnerId,$entryId,$clonedID)->getId()
					, array($singleAttribute) , $priority);
			KalturaLog::info("clip was created batch Element is:" .  print_r(end($batch)));

		}
		return $this->batchArray;
	}


	/***
	 * @param BatchJob $batchJob
	 * @return bool are all clip batch done
	 */
	private function handleClipChildJob($batchJob)
	{
		if (!$batchJob)
		{
			return false;
		}

		if ($batchJob->getJobSubType() != BatchJob::BATCHJOB_SUB_TYPE_CLIP)
		{
			return false;
		}
		$parentJob = $batchJob->getParentJob();
		if (!$parentJob)
		{
			return false;
		}
		$childJobs = $parentJob->getChildJobs();
		/**
		 * check if all child jobs are finished
		 * @var BatchJob $job */
		foreach ($childJobs as $job)
		{
			if ($job->getStatus() != BatchJob::BATCHJOB_STATUS_FINISHED)
			{
				KalturaLog::info('Child job id [' . $job->getId() . '] status [' . $job->getStatus() . ']');
				return false;
			}
		}
		return true;
	}



}