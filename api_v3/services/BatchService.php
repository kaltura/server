<?php
/**
 * batch service lets you handle different batch process from remote machines.
 * As oppesed to other ojects in the system, locking mechanism is critical in this case.
 * For this reason the GetExclusiveXX, UpdateExclusiveXX and FreeExclusiveXX actions are important for the system's intergity.
 * In general - updating batch object should be done only using the UpdateExclusiveXX which in turn can be called only after 
 * acuiring a batch objet properly (using  GetExclusiveXX).
 * If an object was aquired and should be returned to the pool in it's initial state - use the FreeExclusiveXX action 
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
class BatchService extends KalturaBaseService 
{
	/* (non-PHPdoc)
	 * @see KalturaBaseService::initService()
	 */
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		
		if($this->getPartnerId() != Partner::BATCH_PARTNER_ID)
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN, $this->serviceName.'->'.$this->actionName);
		
		myPartnerUtils::resetAllFilters();
	}
	
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
		$dbBulkUploadResult = $bulkUploadResult->toInsertableObject();
		$dbBulkUploadResult->save();
	
		if($bulkUploadResult->entryId)
		{
			$entry = entryPeer::retrieveByPKNoFilter($bulkUploadResult->entryId); //Gets also deleted entries
			if(!$entry)
				throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $bulkUploadResult->entryId);
				
			$entry->setBulkUploadId($bulkUploadResult->bulkUploadJobId);
			$entry->save();
			
			$jobs = BatchJobPeer::retrieveByEntryId($bulkUploadResult->entryId);
			foreach($jobs as $job)
			{
				if(!$job->getParentJobId())
				{
					$job->setRootJobId($bulkUploadResult->bulkUploadJobId);
					$job->setBulkJobId($bulkUploadResult->bulkUploadJobId);
					$job->save();
				}
			}
			
			if($entry && $pluginDataArray && $pluginDataArray->count)
			{
				$pluginValues = $pluginDataArray->toValuesArray();
				if(count($pluginValues))
				{
					$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaBulkUploadHandler');
					foreach($pluginInstances as $pluginInstance)
						$pluginInstance->handleBulkUploadData($bulkUploadResult->entryId, $pluginValues);
				}
			}
		}
		
		$bulkUploadResult->fromObject($dbBulkUploadResult);
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
		$bulkUploadResult->fromObject($dbBulkUploadResult);
		return $bulkUploadResult;
	}	

	
	protected function updateEntryThumbnail(BulkUploadResult $bulkUploadResult)
	{
		if(		$bulkUploadResult->getEntryStatus() != entryStatus::READY 
			||	!strlen($bulkUploadResult->getThumbnailUrl()) 
			||	$bulkUploadResult->getThumbnailSaved()
		)
			return;
			
		myEntryUtils::updateThumbnailFromFile($bulkUploadResult->getEntry(), $bulkUploadResult->getThumbnailUrl());
		$bulkUploadResult->setThumbnailSaved(true);
		$bulkUploadResult->save();
	}
	
	/**
	 * Returns total created entries count
	 * 
	 * @action countBulkUploadEntries
	 * @param int $bulkUploadJobId The id of the bulk upload job
	 * @return int the number of created entries 
	 */
	function countBulkUploadEntriesAction($bulkUploadJobId)
	{
		return BulkUploadResultPeer::countWithEntryByBulkUploadId($bulkUploadJobId);
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
		$closedStatuses = array(
			KalturaEntryStatus::ERROR_IMPORTING,
			KalturaEntryStatus::ERROR_CONVERTING,
			KalturaEntryStatus::READY,
			KalturaEntryStatus::DELETED,
			KalturaEntryStatus::PENDING,
			KalturaEntryStatus::NO_CONTENT,
		);
		
		$unclosedEntries = array();
		$bulkUploadResults = BulkUploadResultPeer::retrieveByBulkUploadId($bulkUploadJobId);
		
		$bulkUpload = BatchJobPeer::retrieveByPK($bulkUploadJobId);
		if($bulkUpload)
		{
			$data = $bulkUpload->getData();
			if($data && $data instanceof kBulkUploadJobData)
			{
				$data->setNumOfEntries(count($bulkUploadResults));
				$bulkUpload->setData($data);
				$bulkUpload->save();
			}
		}
		
		foreach($bulkUploadResults as $bulkUploadResult)
		{
			$status = $bulkUploadResult->updateStatusFromEntry();
			
			if(in_array($bulkUploadResult->getEntryStatus(), $closedStatuses))
			{
				$this->updateEntryThumbnail($bulkUploadResult);
				continue;
			}
			
			if(in_array($status, $closedStatuses))
				continue;
				
			$unclosedEntries[$bulkUploadResult->getEntryId()] = $status;
		}
		
		return count($unclosedEntries);
	}	
	
// --------------------------------- BulkUploadJob functions 	--------------------------------- //

	
	
// --------------------------------- ConvertJob functions 	--------------------------------- //

	
	/**
	 * batch updateExclusiveConvertCollectionJobAction action updates a BatchJob of type CONVERT_PROFILE that was claimed using the getExclusiveConvertJobs
	 * 
	 * @action updateExclusiveConvertCollectionJob
	 * @param int $id The id of the job to free
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
		return $batchJob->fromObject($dbBatchJob);
	}
	
	/**
	 * batch updateExclusiveConvertJobSubType action updates the sub type for a BatchJob of type CONVERT that was claimed using the getExclusiveConvertJobs
	 * 
	 * @action updateExclusiveConvertJobSubType
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param int $subType 
	 * @return KalturaBatchJob 
	 */
	function updateExclusiveConvertJobSubTypeAction($id ,KalturaExclusiveLockKey $lockKey, $subType)
	{
		$dbBatchJob = BatchJobPeer::retrieveByPK($id);
		
		// verifies that the job is of the right type
		if($dbBatchJob->getJobType() != KalturaBatchJobType::CONVERT)
			throw new KalturaAPIException(APIErrors::UPDATE_EXCLUSIVE_JOB_WRONG_TYPE, $id, serialize($lockKey), null);
			
		$msg = "Engine Divreted from " . $dbBatchJob->getJobSubType() . " to $subType";	
		$description = $dbBatchJob->getDescription() . "\n$msg";
		
		$dbBatchJob->setMessage($msg);
		$dbBatchJob->setDescription($description);
		$dbBatchJob->setJobSubType($subType);
		$dbBatchJob->save();
				
		$batchJob = new KalturaBatchJob(); // start from blank
		return $batchJob->fromObject($dbBatchJob);
	}
	
// --------------------------------- ConvertJob functions 	--------------------------------- //

	
	
// --------------------------------- ExtractMediaJob functions 	--------------------------------- //
	
	/**
	 * batch addMediaInfoAction action saves a media info object
	 * 
	 * @action addMediaInfo
	 * @param KalturaMediaInfo $mediaInfo
	 * @return KalturaMediaInfo 
	 */
	function addMediaInfoAction(KalturaMediaInfo $mediaInfo)
	{
		$mediaInfoDb = null;
		$flavorAsset = null;
		
		if($mediaInfo->flavorAssetId)
		{
			$flavorAsset = assetPeer::retrieveById($mediaInfo->flavorAssetId);
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
			
		$mediaInfo->fromObject($mediaInfoDb);
		return $mediaInfo;
	}
	
// --------------------------------- ExtractMediaJob functions 	--------------------------------- //
	
	
// --------------------------------- Notification functions 	--------------------------------- //	
	
	
	/**
	 * batch getExclusiveNotificationJob action allows to get a BatchJob of type NOTIFICATION 
	 * 
	 * @action getExclusiveNotificationJobs
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param int $maxExecutionTime The maximum time in seconds the job reguarly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return. 
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only rartial list of jobs  
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
	 * batch resetJobExecutionAttempts action resets the execution attempts of the job 
	 * 
	 * @action resetJobExecutionAttempts
	 * @param int $id The id of the job
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism
	 * @param KalturaBatchJobType $jobType The type of the job  
	 */
	function resetJobExecutionAttemptsAction($id ,KalturaExclusiveLockKey $lockKey, $jobType)
	{
		$jobType = kPluginableEnumsManager::apiToCore('BatchJobType', $jobType);
		
		$c = new Criteria();
		
		$c->add(BatchJobPeer::ID, $id );
		$c->add(BatchJobPeer::SCHEDULER_ID, $lockKey->schedulerId );			
		$c->add(BatchJobPeer::WORKER_ID, $lockKey->workerId );			
		$c->add(BatchJobPeer::BATCH_INDEX, $lockKey->batchIndex );
		
		$job = BatchJobPeer::doSelectOne ( $c );
		if(!$job)
			throw new APIException(APIErrors::UPDATE_EXCLUSIVE_JOB_FAILED, $id, $lockKey->schedulerId, $lockKey->workerId, $lockKey->batchIndex);
		
		// verifies that the job is of the right type
		if($job->getJobType() != $jobType)
			throw new KalturaAPIException(APIErrors::UPDATE_EXCLUSIVE_JOB_WRONG_TYPE, $id, $lockKey, null);
			
		$job->setExecutionAttempts(0);
		$job->save();
	}	
	
	/**
	 * batch freeExclusiveJobAction action allows to get a generic BatchJob 
	 * 
	 * @action freeExclusiveJob
	 * @param int $id The id of the job
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism
	 * @param KalturaBatchJobType $jobType The type of the job  
	 * @param bool $resetExecutionAttempts Resets the job execution attampts to zero  
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
		$batchJob->fromObject($job);
		
		// gets queues length
		$c = new Criteria();
		$c->add(BatchJobPeer::STATUS, array(KalturaBatchJobStatus::PENDING, KalturaBatchJobStatus::RETRY, KalturaBatchJobStatus::ALMOST_DONE), Criteria::IN);
		$c->add(BatchJobPeer::JOB_TYPE, $jobType);
		$queueSize = BatchJobPeer::doCount($c, false, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2) );
		
		if(!$queueSize)
		{
			// gets queues length
			$c = new Criteria();
			$c->add(BatchJobPeer::BATCH_INDEX, null, Criteria::ISNOTNULL);
			$c->add(BatchJobPeer::PROCESSOR_EXPIRATION, time(), Criteria::GREATER_THAN);
			$c->add(BatchJobPeer::EXECUTION_ATTEMPTS, BatchJobPeer::getMaxExecutionAttempts($jobType), Criteria::LESS_THAN);
			$c->add(BatchJobPeer::JOB_TYPE, $jobType);
			$queueSize = BatchJobPeer::doCount($c, false, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2) );
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
		$filter = $workerQueueFilter->filter->toObject(new BatchJobFilter());
		
		return kBatchManager::getQueueSize($workerQueueFilter->schedulerId, $workerQueueFilter->workerId, $jobType, $filter);
	}	
	

	/**
	 * batch getExclusiveJobsAction action allows to get a BatchJob 
	 * 
	 * @action getExclusiveJobs
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param int $maxExecutionTime The maximum time in seconds the job reguarly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return. 
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only rartial list of jobs  
	 * @param KalturaBatchJobType $jobType The type of the job - could be a custom extended type
	 * @return KalturaBatchJobArray 
	 */
	function getExclusiveJobsAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, $jobType = null)
	{
		$jobType = kPluginableEnumsManager::apiToCore('BatchJobType', $jobType);
		$jobs = $this->getExclusiveJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $jobType);
		return KalturaBatchJobArray::fromBatchJobArray($jobs);
	}		
	
	/**
	 * batch getExclusiveAlmostDone action allows to get a BatchJob that wait for remote closure 
	 * 
	 * @action getExclusiveAlmostDone
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param int $maxExecutionTime The maximum time in seconds the job reguarly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return. 
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only rartial list of jobs  
	 * @param KalturaBatchJobType $jobType The type of the job - could be a custom extended type
	 * @return KalturaBatchJobArray 
	 */
	function getExclusiveAlmostDoneAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, $jobType = null)
	{
		$jobType = kPluginableEnumsManager::apiToCore('BatchJobType', $jobType);
		$jobsFilter = new BatchJobFilter();
		if ($filter)
			$jobsFilter = $filter->toFilter($jobType);
		
		$jobs = kBatchManager::getExclusiveAlmostDoneJobs($lockKey->toObject(), $maxExecutionTime, $numberOfJobs, $jobType, $jobsFilter);
		return KalturaBatchJobArray::fromBatchJobArray($jobs);
	}
	
	protected function getExclusiveJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, $jobType)
	{
		$dbJobType = kPluginableEnumsManager::apiToCore('BatchJobType', $jobType);
		
		if (!is_null($filter))
			$jobsFilter = $filter->toFilter($dbJobType);
		
		return kBatchExclusiveLock::getExclusiveJobs($lockKey->toObject(), $maxExecutionTime, $numberOfJobs, $dbJobType, $jobsFilter);
	}	
	
	
	/**
	 * batch updateExclusiveJobAction action updates a BatchJob of extended type that was claimed using the getExclusiveJobs
	 * 
	 * @action updateExclusiveJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param KalturaBatchJob $job
	 * @return KalturaBatchJob 
	 */
	function updateExclusiveJobAction($id ,KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job)
	{
		$dbBatchJob = BatchJobPeer::retrieveByPK($id);
		$dbBatchJob = kBatchManager::updateExclusiveBatchJob($id, $lockKey->toObject(), $job->toObject($dbBatchJob));
				
		$batchJob = new KalturaBatchJob(); // start from blank
		return $batchJob->fromObject($dbBatchJob);
	}
	
	
	/**
	 * batch cleanExclusiveJobs action mark as fatal error all expired jobs
	 * 
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
	 */
	function logConversionAction($flavorAssetId, $data)
	{
		$flavorAsset = assetPeer::retrieveById($flavorAssetId);
		// verifies that flavor asset exists
		if(!$flavorAsset)
			throw new APIException(APIErrors::INVALID_FLAVOR_ASSET_ID, $flavorAssetId);
	
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
	 * @param int $size
	 * @return KalturaFileExistsResponse 
	 */
	function checkFileExistsAction($localPath, $size)
	{
		$ret = new KalturaFileExistsResponse();
		$ret->exists = file_exists($localPath);
		$ret->sizeOk = false;
		
		if($ret->exists)
		{
			clearstatcache();
			$ret->sizeOk = (filesize($localPath) == $size);
		}
		
		return $ret;
	}	


// --------------------------------- generic functions 	--------------------------------- //

}
