<?php
/**
 * @package server-infra
 * @subpackage clipconcat
 */

class kClipManager implements kBatchJobStatusEventConsumer
{

	//Todo: create parent job for the concat and pass it to children

	const CLIP_NUMBER = 'clipNumber';

	public function createParentBatchJob($entryId, $partnerId, array $operationAttributes, $priority = 0)
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
		$flavorParamsObj->setFormat(flavorParams::CONTAINER_FORMAT_MPEGTS);
		//save the object
		$flavorParamsObj->save();
		//return the object ID
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
			//$flavorAsset->setFlavorParamsId(kClipAttributes::SYSTEM_DEFAULT_FLAVOR_PARAMS_ID);
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
	 * @return bool true if should continue to the next consumer
	 */
	public function updatedJob(BatchJob $batchJob)
	{
		try
		{
			if ($batchJob->getParentJob() && $batchJob->getParentJob()->getJobType() == BatchJobType::CONVERT)
			{
				$this->startConcat($batchJob->getRootJob());
			}
		}
		catch (Exception $ex)
		{
			KalturaLog::err('Error During Concat Job' . $ex);
		}
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

		if ($batchJob->getJobType() == BatchJobType::CLIP_CONCAT)
		{
			$this->handleClipConcatParentJob($batchJob);
			return false;
		}

		elseif ($batchJob->getRootJob() && $batchJob->getRootJob()->getJobType() == BatchJobType::CLIP_CONCAT)
		{
			return $this->handleClipChildJob($batchJob);
		}

		return false;
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

		$tempEntry = entryPeer::retrieveByPK($jobData->getEntryId());
		$assets = assetPeer::retrieveByEntryId($jobData->getEntryId());
		usort($assets, array("kClipManager","cmpByOrder"));

		$files = $this->getFilesPath($assets);

		$flavorAsset = $this->getSourceFlavorAssetFromEntry($tempEntry);

		kJobsManager::addConcatJob($batchJob, $flavorAsset, $files,true);

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
		$batchArray = array();
		$this->createDummyOriginalFlavorAsset($partnerId,$entryId);
		$order = 0;
		foreach($operationAttributes as $singleAttribute)
		{
			KalturaLog::info("Going To create Flavor for clip: " . print_r($singleAttribute));
			//$dbAsset = kFlowHelper::createOriginalFlavorAsset($partnerId, $entryId);
			$clonedID =	$this->cloneFlavorParam($singleAttribute->getAssetParamsId());
			$batchArray[] =
				kBusinessPreConvertDL::decideAddEntryFlavor($parentJob, $entryId,
					$clonedID, $errDescription, $this->createTempClipFlavorAsset($partnerId,$entryId,$clonedID,$order)->getId()
					, array($singleAttribute) , $priority);
			KalturaLog::info("clip was created batch Element is:" .  print_r(end($batch)));
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
		 * check if all child jobs are finished
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
	private function getSourceFlavorAssetFromEntry($tempEntry): flavorAsset
	{

		//$flavorParams = assetParamsPeer::retrieveByPK(kClipAttributes::SYSTEM_DEFAULT_FLAVOR_PARAMS_ID);
		//$flavorParams = assetParamsPeer::retrieveByPKNoFilter($tempEntry->getFlavorParamsId());
		//$flavorAsset = assetPeer::retrieveOriginalByEntryId($tempEntry->getEntryId());
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


}