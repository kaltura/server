<?php
/**
 * batch service lets you handle different batch process from remote machines.
 * As opposed to other objects in the system, locking mechanism is critical in this case.
 * For this reason the GetExclusiveXX, UpdateExclusiveXX and FreeExclusiveXX actions are important for the system's integrity.
 * In general - updating batch object should be done only using the UpdateExclusiveXX which in turn can be called only after
 * acquiring a batch object properly (using  GetExclusiveXX).
 * If an object was acquired and should be returned to the pool in it's initial state - use the FreeExclusiveXX action
 *
 *	Terminology:
 *		LocationId
 *		ServerID
 *		ParternGroups
 *
 * @service batch
 * @package api
 * @subpackage services
 */
class BatchService extends KalturaBatchService
{
	const DEFAULT_MAX_DATA_SIZE = 20000000;

// --------------------------------- BulkUploadJob functions 	--------------------------------- //

	/**
	 * batch addBulkUploadResultAction action adds KalturaBulkUploadResult to the DB
	 *
	 * @action addBulkUploadResult
	 * @param KalturaBulkUploadResult $bulkUploadResult The results to save to the DB
	 * @param KalturaBulkUploadPluginDataArray $pluginDataArray
	 * @return KalturaBulkUploadResult
	 */
	function addBulkUploadResultAction(KalturaBulkUploadResult $bulkUploadResult, KalturaBulkUploadPluginDataArray $pluginDataArray = null)
	{
		if(is_null($bulkUploadResult->action))
			$bulkUploadResult->action = KalturaBulkUploadAction::ADD;

		$bulkUploadResult->pluginsData = $pluginDataArray;

		$dbBulkUploadResult = BulkUploadResultPeer::retrieveByBulkUploadIdAndIndex($bulkUploadResult->bulkUploadJobId, $bulkUploadResult->lineIndex);
		if($dbBulkUploadResult)
			$dbBulkUploadResult = $bulkUploadResult->toUpdatableObject($dbBulkUploadResult);
		else
			$dbBulkUploadResult = $bulkUploadResult->toInsertableObject();

		/* @var $dbBulkUploadResult BulkUploadResult */
		$dbBulkUploadResult->save();

		if($bulkUploadResult->objectId)
		{
			$dbBulkUploadResult->handleRelatedObjects();

			$c = new Criteria();
			$c->add(BatchJobPeer::ENTRY_ID, $bulkUploadResult->objectId);
			$c->add(BatchJobPeer::PARENT_JOB_ID, null, Criteria::ISNULL);
			$c->add(BatchJobPeer::BULK_JOB_ID, null, Criteria::ISNULL);
			$jobs = BatchJobPeer::doSelect($c);

			foreach($jobs as $job)
			{
				$job->setRootJobId($bulkUploadResult->bulkUploadJobId);
				$job->setBulkJobId($bulkUploadResult->bulkUploadJobId);
				$job->save();
			}

			if($dbBulkUploadResult->getObject() && $pluginDataArray && $pluginDataArray->count)
			{
				$pluginValues = $pluginDataArray->toValuesArray();
				if(count($pluginValues))
				{
					$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaBulkUploadHandler');
					foreach($pluginInstances as $pluginInstance)
						$pluginInstance->handleBulkUploadData($dbBulkUploadResult->getObject(), $pluginValues);
				}
			}
		}

		$bulkUploadResult->fromObject($dbBulkUploadResult, $this->getResponseProfile());
		return $bulkUploadResult;
	}


	/**
	 * batch getBulkUploadLastResultAction action returns the last result of the bulk upload
	 *
	 * @action getBulkUploadLastResult
	 * @param int $bulkUploadJobId The id of the bulk upload job
	 * @return KalturaBulkUploadResult
	 */
	function getBulkUploadLastResultAction($bulkUploadJobId)
	{
		$dbBulkUploadResult = BulkUploadResultPeer::retrieveLastByBulkUploadId($bulkUploadJobId);

		if(!$dbBulkUploadResult)
			return null;

		$bulkUploadResult = new KalturaBulkUploadResult();
		$bulkUploadResult->fromObject($dbBulkUploadResult, $this->getResponseProfile());
		return $bulkUploadResult;
	}


	/**
	 * Returns total created entries count and total error entries count
	 *
	 * @action countBulkUploadEntries
	 * @param int $bulkUploadJobId The id of the bulk upload job
	 * @param KalturaBulkUploadObjectType $bulkUploadObjectType
	 * @return KalturaKeyValueArray the number of created entries and error entries
	 */
	function countBulkUploadEntriesAction($bulkUploadJobId, $bulkUploadObjectType = KalturaBulkUploadObjectType::ENTRY)
	{
		$coreBulkUploadObjectType = kPluginableEnumsManager::apiToCore('BulkUploadObjectType', $bulkUploadObjectType);
		$createdRecordsCount = BulkUploadResultPeer::countWithObjectTypeByBulkUploadId($bulkUploadJobId, $coreBulkUploadObjectType);
		$errorRecordsCount = BulkUploadResultPeer::countErrorWithObjectTypeByBulkUploadId($bulkUploadJobId, $coreBulkUploadObjectType);
		
		$res = array();
		$created = new KalturaKeyValue();
		$created->key = 'created';
		$created->value = $createdRecordsCount;
		$res[] = $created;		
		$error = new KalturaKeyValue();
		$error->key = 'error';
		$error->value = $errorRecordsCount;
		$res[] = $error;
		
		return $res;
	}
	
	/**
	 * batch updateBulkUploadResults action adds KalturaBulkUploadResult to the DB
	 *
	 * @action updateBulkUploadResults
	 * @param int $bulkUploadJobId The id of the bulk upload job
	 * @return int the number of unclosed entries
	 */
	function updateBulkUploadResultsAction($bulkUploadJobId)
	{
		$unclosedEntriesCount = 0;
		$errorObjects = 0;
		$unclosedEntries = array();
		
		
		$criteria = new Criteria();
		$criteria->add(BulkUploadResultPeer::BULK_UPLOAD_JOB_ID, $bulkUploadJobId);
		$criteria->addAscendingOrderByColumn(BulkUploadResultPeer::LINE_INDEX);
		$criteria->setLimit(100);
		
		$bulkUpload = BatchJobPeer::retrieveByPK($bulkUploadJobId);
		if($bulkUpload)
		{
			$handledResults = 0;
			$bulkUploadResults = BulkUploadResultPeer::doSelect($criteria);
			while(count($bulkUploadResults))
			{
				$handledResults += count($bulkUploadResults);
	    		foreach($bulkUploadResults as $bulkUploadResult)
	    		{
	    		    /* @var $bulkUploadResult BulkUploadResult */
	    			$status = $bulkUploadResult->updateStatusFromObject();
	
	    			if ($status == BulkUploadResultStatus::IN_PROGRESS )
	    			{
	        			$unclosedEntriesCount++;
	    			}
	    			if ($status == BulkUploadResultStatus::ERROR )
	    			{
	    			    $errorObjects++;
	    			}
	    		}

	    		if(count($bulkUploadResults) < $criteria->getLimit())
	    			break;
	    			
	    		kMemoryManager::clearMemory();
	    		$criteria->setOffset($handledResults);
				$bulkUploadResults = BulkUploadResultPeer::doSelect($criteria);
			}
			$data = $bulkUpload->getData();
			if($data && $data instanceof kBulkUploadJobData)
			{
				//TODO: find some better alternative, find out why the bulk upload result which reports error is
				// returning objectId "null" for failed entry assets, rather than the entryId to which they pertain.
				//$data->setNumOfEntries(BulkUploadResultPeer::countWithEntryByBulkUploadId($bulkUploadJobId));
				$data->setNumOfObjects(BulkUploadResultPeer::countByBulkUploadId($bulkUploadJobId));
				$data->setNumOfErrorObjects($errorObjects);
				$bulkUpload->setData($data);
				$bulkUpload->save();
			}
		}

		return $unclosedEntriesCount;
	}

// --------------------------------- BulkUploadJob functions 	--------------------------------- //



// --------------------------------- ConvertJob functions 	--------------------------------- //


	/**
	 * batch updateExclusiveConvertCollectionJobAction action updates a BatchJob of type CONVERT_PROFILE that was claimed using the getExclusiveConvertJobs
	 *
	 * @action updateExclusiveConvertCollectionJob
	 * @param bigint $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism
	 * @param KalturaBatchJob $job
	 * @param KalturaConvertCollectionFlavorDataArray $flavorsData
	 * @return KalturaBatchJob
	 */
	function updateExclusiveConvertCollectionJobAction($id ,KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaConvertCollectionFlavorDataArray $flavorsData = null)
	{
		$dbBatchJob = BatchJobPeer::retrieveByPK($id);

		// verifies that the job is of the right type
		if($dbBatchJob->getJobType() != KalturaBatchJobType::CONVERT_COLLECTION)
			throw new KalturaAPIException(APIErrors::UPDATE_EXCLUSIVE_JOB_WRONG_TYPE, $id, serialize($lockKey), serialize($job));

		if($flavorsData)
			$job->data->flavors = $flavorsData;

		$dbBatchJob = kBatchManager::updateExclusiveBatchJob($id, $lockKey->toObject(), $job->toObject($dbBatchJob));

		$batchJob = new KalturaBatchJob(); // start from blank
		return $batchJob->fromBatchJob($dbBatchJob);
	}

// --------------------------------- ConvertJob functions 	--------------------------------- //



// --------------------------------- ExtractMediaJob functions 	--------------------------------- //

	/**
	 * batch addMediaInfoAction action saves a media info object
	 *
	 * @action addMediaInfo
	 * @param KalturaMediaInfo $mediaInfo
	 * @return KalturaMediaInfo
	 * @throws KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND
	 */
	function addMediaInfoAction(KalturaMediaInfo $mediaInfo)
	{
		$mediaInfoDb = null;
		$flavorAsset = null;

		if($mediaInfo->flavorAssetId)
		{
			$flavorAsset = assetPeer::retrieveByIdNoFilter($mediaInfo->flavorAssetId);
			if(!$flavorAsset)
				throw new KalturaAPIException(KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND, $mediaInfo->flavorAssetId);

			$mediaInfoDb = mediaInfoPeer::retrieveByFlavorAssetId($mediaInfo->flavorAssetId);

			if($mediaInfoDb && $mediaInfoDb->getFlavorAssetVersion() == $flavorAsset->getVersion())
			{
				$mediaInfoDb = $mediaInfo->toUpdatableObject($mediaInfoDb);
			}
			else
			{
				$mediaInfoDb = null;
			}
		}

		if(!$mediaInfoDb)
			$mediaInfoDb = $mediaInfo->toInsertableObject();

		if($flavorAsset)
			$mediaInfoDb->setFlavorAssetVersion($flavorAsset->getVersion());

		$mediaInfoDb = kBatchManager::addMediaInfo($mediaInfoDb);

		$mediaInfo->fromObject($mediaInfoDb, $this->getResponseProfile());
		return $mediaInfo;
	}

// --------------------------------- ExtractMediaJob functions 	--------------------------------- //


// --------------------------------- Notification functions 	--------------------------------- //


	/**
	 * batch getExclusiveNotificationJob action allows to get a BatchJob of type NOTIFICATION
	 *
	 * @action getExclusiveNotificationJobs
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism
	 * @param int $maxExecutionTime The maximum time in seconds the job regularly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return.
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only partial list of jobs
	 * @return KalturaBatchGetExclusiveNotificationJobsResponse
	 */
	function getExclusiveNotificationJobsAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$jobs = $this->getExclusiveJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, BatchJobType::NOTIFICATION);

		// Because the notifications need the partner's url and policy of mulriNotifications - we'll send the partner list
		$dbPartners = array();
		foreach ($jobs as $job )
		{
			if ( isset ($dbPartners[$job->getPartnerId()]))
				continue;

			$dbPartners[$job->getPartnerId()] = PartnerPeer::retrieveByPK($job->getPartnerId());
		}

		$response = new KalturaBatchGetExclusiveNotificationJobsResponse();
		$response->notifications = KalturaBatchJobArray::fromBatchJobArray($jobs);
		$response->partners = KalturaPartnerArray::fromPartnerArray($dbPartners) ;

		return $response;
	}

// --------------------------------- Notification functions 	--------------------------------- //


// --------------------------------- generic functions 	--------------------------------- //

	/**
	 * batch updatePartnerLoadTable action cleans the partner load table
	 *
	 * @action updatePartnerLoadTable
	 */
	function updatePartnerLoadTableAction() {
		KalturaResponseCacher::disableCache();
		PartnerLoadPeer::updatePartnerLoadTable();
	}

	/**
	 * batch suspendJobs action suspends jobs from running.
	 *
	 * @action suspendJobs
	 */
	function suspendJobsAction() {
		KalturaResponseCacher::disableCache();
		kJobsSuspender::balanceJobsload();
	}

	/**
	 * batch resetJobExecutionAttempts action resets the execution attempts of the job
	 *
	 * @action resetJobExecutionAttempts
	 * @param bigint $id The id of the job
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism
	 * @param KalturaBatchJobType $jobType The type of the job
	 * @throws KalturaErrors::UPDATE_EXCLUSIVE_JOB_FAILED
	 * @throws KalturaErrors::UPDATE_EXCLUSIVE_JOB_WRONG_TYPE
	 */
	function resetJobExecutionAttemptsAction($id ,KalturaExclusiveLockKey $lockKey, $jobType)
	{
		$jobType = kPluginableEnumsManager::apiToCore('BatchJobType', $jobType);

		$c = new Criteria();

		$c->add(BatchJobLockPeer::ID, $id );
		$c->add(BatchJobLockPeer::SCHEDULER_ID, $lockKey->schedulerId );
		$c->add(BatchJobLockPeer::WORKER_ID, $lockKey->workerId );
		$c->add(BatchJobLockPeer::BATCH_INDEX, $lockKey->batchIndex );

		$job = BatchJobLockPeer::doSelectOne ( $c );
		if(!$job)
			throw new KalturaAPIException(KalturaErrors::UPDATE_EXCLUSIVE_JOB_FAILED, $id, $lockKey->schedulerId, $lockKey->workerId, $lockKey->batchIndex);

		// verifies that the job is of the right type
		if($job->getJobType() != $jobType)
			throw new KalturaAPIException(KalturaErrors::UPDATE_EXCLUSIVE_JOB_WRONG_TYPE, $id, $lockKey, null);

		$job->setExecutionAttempts(0);
		$job->save();
	}

	/**
	 * batch freeExclusiveJobAction action allows to get a generic BatchJob
	 *
	 * @action freeExclusiveJob
	 * @param bigint $id The id of the job
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism
	 * @param KalturaBatchJobType $jobType The type of the job
	 * @param bool $resetExecutionAttempts Resets the job execution attempts to zero
	 * @return KalturaFreeJobResponse
	 */
	function freeExclusiveJobAction($id ,KalturaExclusiveLockKey $lockKey, $jobType, $resetExecutionAttempts = false)
	{
		$jobType = kPluginableEnumsManager::apiToCore('BatchJobType', $jobType);

		$job = BatchJobPeer::retrieveByPK($id);

		// verifies that the job is of the right type
		if($job->getJobType() != $jobType)
			throw new KalturaAPIException(APIErrors::UPDATE_EXCLUSIVE_JOB_WRONG_TYPE, $id, $lockKey, null);

		$job = kBatchManager::freeExclusiveBatchJob($id, $lockKey->toObject(), $resetExecutionAttempts);
		$batchJob = new KalturaBatchJob(); // start from blank
		$batchJob->fromBatchJob($job);

		// gets queues length
		$c = new Criteria();
		$c->add(BatchJobLockPeer::STATUS, array(KalturaBatchJobStatus::PENDING, KalturaBatchJobStatus::RETRY, KalturaBatchJobStatus::ALMOST_DONE), Criteria::IN);
		$c->add(BatchJobLockPeer::JOB_TYPE, $jobType);
		$c->add(BatchJobLockPeer::DC, kDataCenterMgr::getCurrentDcId());
		$queueSize = BatchJobLockPeer::doCount($c, false, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2) );

		if(!$queueSize)
		{
			// gets queues length
			$c = new Criteria();
			$c->add(BatchJobLockPeer::BATCH_INDEX, null, Criteria::ISNOTNULL);
			$c->add(BatchJobLockPeer::EXPIRATION, time(), Criteria::GREATER_THAN);
			$c->add(BatchJobLockPeer::EXECUTION_ATTEMPTS, BatchJobLockPeer::getMaxExecutionAttempts($jobType), Criteria::LESS_THAN);
			$c->add(BatchJobLockPeer::DC, kDataCenterMgr::getCurrentDcId());
			$c->add(BatchJobLockPeer::JOB_TYPE, $jobType);
			$queueSize = BatchJobLockPeer::doCount($c, false, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2) );
		}

		$response = new KalturaFreeJobResponse();
		$response->job = $batchJob;
		$response->jobType = $jobType;
		$response->queueSize = $queueSize;

		return $response;
	}

	/**
	 * batch getQueueSize action get the queue size for job type
	 *
	 * @action getQueueSize
	 * @param KalturaWorkerQueueFilter $workerQueueFilter The worker filter
	 * @return int
	 */
	function getQueueSizeAction(KalturaWorkerQueueFilter $workerQueueFilter)
	{
		$jobType = kPluginableEnumsManager::apiToCore('BatchJobType', $workerQueueFilter->jobType);
		$filter = $workerQueueFilter->filter->toFilter($jobType);

		return kBatchManager::getQueueSize($workerQueueFilter->workerId, $jobType, $filter);
	}


	/**
	 * batch getExclusiveJobsAction action allows to get a BatchJob
	 *
	 * @action getExclusiveJobs
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism
	 * @param int $maxExecutionTime The maximum time in seconds the job regularly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return.
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only partial list of jobs
	 * @param KalturaBatchJobType $jobType The type of the job - could be a custom extended type
	 * @param int $maxJobToPullForCache The number of job to pull to cache
	 * @return KalturaBatchJobArray
	 */
	function getExclusiveJobsAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs,
			KalturaBatchJobFilter $filter = null, $jobType = null, $maxJobToPullForCache = 0)
	{
		$jobType = kPluginableEnumsManager::apiToCore('BatchJobType', $jobType);
		$jobs = $this->getExclusiveJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $jobType, $maxJobToPullForCache);
		return KalturaBatchJobArray::fromBatchJobArray($jobs);
	}

	/**
	 * batch getExclusiveAlmostDone action allows to get a BatchJob that wait for remote closure
	 *
	 * @action getExclusiveAlmostDone
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism
	 * @param int $maxExecutionTime The maximum time in seconds the job regularly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return.
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only partial list of jobs
	 * @param KalturaBatchJobType $jobType The type of the job - could be a custom extended type
	 * @return KalturaBatchJobArray
	 */
	function getExclusiveAlmostDoneAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, $jobType = null)
	{
		$jobType = kPluginableEnumsManager::apiToCore('BatchJobType', $jobType);
		$jobsFilter = new BatchJobFilter(false);
		if ($filter)
			$jobsFilter = $filter->toFilter($jobType);

		$jobs = kBatchManager::getExclusiveAlmostDoneJobs($lockKey->toObject(), $maxExecutionTime, $numberOfJobs, $jobType, $jobsFilter);
		return KalturaBatchJobArray::fromBatchJobArray($jobs);
	}

	/**
	 * batch updateExclusiveJobAction action updates a BatchJob of extended type that was claimed using the getExclusiveJobs
	 *
	 * @action updateExclusiveJob
	 * @param bigint $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism
	 * @param KalturaBatchJob $job
	 * @return KalturaBatchJob
	 */
	function updateExclusiveJobAction($id ,KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job)
	{
		$dbBatchJob = BatchJobPeer::retrieveByPK($id);
		$dbBatchJob = kBatchManager::updateExclusiveBatchJob($id, $lockKey->toObject(), $job->toObject($dbBatchJob));

		$batchJob = new KalturaBatchJob(); // start from blank
		return $batchJob->fromBatchJob($dbBatchJob);
	}


	/**
	 * batch cleanExclusiveJobs action mark as fatal error all expired jobs

	 * @action cleanExclusiveJobs
	 * @return int the count of jobs that cleaned
	 */
	function cleanExclusiveJobsAction()
	{
		return kBatchManager::cleanExclusiveJobs();
	}


	/**
	 * Add the data to the flavor asset conversion log, creates it if doesn't exists
	 *
	 * @action logConversion
	 * @param string $flavorAssetId
	 * @param string $data
	 * @throws KalturaErrors::INVALID_FLAVOR_ASSET_ID
	 */
	function logConversionAction($flavorAssetId, $data)
	{
		$flavorAsset = assetPeer::retrieveById($flavorAssetId);
		// verifies that flavor asset exists
		if(!$flavorAsset)
			throw new KalturaAPIException(KalturaErrors::INVALID_FLAVOR_ASSET_ID, $flavorAssetId);

		$flavorAsset->incLogFileVersion();
		$flavorAsset->save();
		$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_CONVERT_LOG);
		$log = kFileSyncUtils::file_get_contents($syncKey, true, false);
		$log .= $data;
		kFileSyncUtils::file_put_contents($syncKey, $log, false);
	}


	/**
	 * batch checkFileExists action check if the file exists
	 *
	 * @action checkFileExists
	 * @param string $localPath
	 * @param float $size
	 * @return KalturaFileExistsResponse
	 */
	function checkFileExistsAction($localPath, $size)
	{
		// need to explicitly disable the cache since this action does not perform any queries
		kApiCache::disableConditionalCache();
		
		$ret = new KalturaFileExistsResponse();
		$ret->exists = kFile::checkFileExists($localPath);
		$ret->sizeOk = false;

		if($ret->exists)
		{
			clearstatcache();
			$ret->sizeOk = (kFile::fileSize($localPath) == $size);
		}

		return $ret;
	}


// --------------------------------- generic functions 	--------------------------------- //


	/**
	 * batch checkEntryIsDone Check weather a specified entry is done converting and changes it to ready.
	 *
	 * @action checkEntryIsDone
	 * @param string $batchJobId The entry to check
	 * @return bool
	 * @throws KalturaAPIException
	 */
	function checkEntryIsDoneAction($batchJobId)
	{
		$ret_val = false;
	
		$dbBatchJob = BatchJobPeer::retrieveByPK($batchJobId);
		if (!$dbBatchJob)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_BATCHJOB_ID, $batchJobId);
		}
	
		$entry = $dbBatchJob->getEntry();
		if (!$entry)
		{
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $dbBatchJob->getEntryId());
		}
	
		switch ($entry->getStatus())
		{
			case entryStatus::PRECONVERT:
				$flavorAssets = assetPeer::retrieveReadyFlavorsByEntryId($entry->getId());
				if (!$flavorAssets || count($flavorAssets) == 0)
				{
					return $ret_val;
				}
				kBusinessPostConvertDL::handleConvertFinished($dbBatchJob, $flavorAssets[0]);
				$entry = entryPeer::retrieveByPK($entry->getId());
				if ($entry->getStatus() != entryStatus::PRECONVERT)
				{
					$ret_val = true;
				}
				break;
	
			case entryStatus::READY:
				$flavorAssets = assetPeer::retrieveFlavorsByEntryIdAndStatusNotIn($entry->getId(), array(asset::ASSET_STATUS_READY, asset::ASSET_STATUS_ERROR, asset::ASSET_STATUS_NOT_APPLICABLE));
				if(!count($flavorAssets))
					$ret_val = true;
				break;
		}
	
		return $ret_val;
	}

	/**
	 * batch extendLockExpiration action allows to extend the expiration of a BatchJobLock by job id
	 *
	 * @action extendLockExpiration
	 * @param int $jobId
	 * @param int $maxExecutionTime The maximum time in seconds the job regularly take.
	 */
	public function extendLockExpirationAction($jobId, $maxExecutionTime)
	{
		$dbBatchJobLock =  BatchJobLockPeer::retrieveByPK($jobId);
		if ($dbBatchJobLock)
		{
			$currentExpiration = $dbBatchJobLock->getExpiration();
			$dbBatchJobLock->setExpiration(time() + $maxExecutionTime);
			$dbBatchJobLock->save();
			KalturaLog::debug('Expiration was: '. $currentExpiration ." ,the new expiration is: ". $dbBatchJobLock->getExpiration());
		}
	}

	/**
	 * batch putFileAction action allows put file on server via http
	 *
	 * @action putFile
	 * @param string $destPath
	 * @param file $data
	 * @return int Number of bytes
	 */
	public function putFileAction($destPath, $data)
	{
		$maxDataSize = kConf::get('maxPutFileSize','batchServices', self::DEFAULT_MAX_DATA_SIZE);
		$allowedPathPrefixArr = kConf::get('allowedPutFilePrefix','batchServices', array());
		$found = false;
		foreach ($allowedPathPrefixArr as $allowedPathPrefix)
		{
			if (strpos($destPath, $allowedPathPrefix) === 0)
			{
				$found = true;
				break;
			}
		}
		if (!$found)
		{
			throw new KalturaAPIException(KalturaErrors::PATH_NOT_ALLOWED, $destPath);
		}
		$size = kFile::fileSize($data['tmp_name']);
		if ( $size > $maxDataSize )
		{
			throw new KalturaAPIException(KalturaErrors::FILE_SIZE_EXCEEDED, $size);
		}

		KalturaLog::debug("Going to save file in path - $destPath");
		if(kFile::checkFileExists($destPath))
		{
			throw new KalturaAPIException(KalturaErrors::FILE_ALREADY_EXISTS, $destPath);
		}
		$ret = kFile::moveFile($data['tmp_name'], $destPath);
		return $ret;
	}
}
