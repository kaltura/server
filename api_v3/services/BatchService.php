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
	// use initService to add a peer to the partner filter
	/**
	 * @ignore
	 */
	public function initService ($partnerId, $puserId, $ksStr, $serviceName, $action )
	{
		parent::initService ($partnerId, $puserId, $ksStr, $serviceName, $action );
	}
	
	
// --------------------------------- ImportJob functions 	--------------------------------- //
	
	/**
	 * batch getExclusiveImportJob action allows to get a BatchJob of type IMPORT 
	 * 
	 * @action getExclusiveImportJobs
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param int $maxExecutionTime The maximum time in seconds the job reguarly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return. 
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only rartial list of jobs  
	 * @return KalturaBatchJobArray 
	 */
	function getExclusiveImportJobsAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		return $this->getExclusiveJobsAction($lockKey, $maxExecutionTime, $numberOfJobs, $filter, BatchJobType::IMPORT );
	}

	
	/**
	 * batch updateExclusiveImportJob action updates a BatchJob of type IMPORT that was claimed using the getExclusiveImportJobs
	 * 
	 * @action updateExclusiveImportJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param KalturaBatchJob $job
	 * @return KalturaBatchJob 
	 */
	function updateExclusiveImportJobAction($id ,KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job)
	{
		$dbBatchJob = BatchJobPeer::retrieveByPK($id);
		
		// verifies that the job is of the right type
		if($dbBatchJob->getJobType() != KalturaBatchJobType::IMPORT)
			throw new KalturaAPIException(APIErrors::UPDATE_EXCLUSIVE_JOB_WRONG_TYPE, $id, serialize($lockKey), serialize($job));
	
		$dbBatchJob = kBatchManager::updateExclusiveBatchJob($id, $lockKey->toObject(), $job->toObject($dbBatchJob));
				
		$batchJob = new KalturaBatchJob(); // start from blank
		return $batchJob->fromObject($dbBatchJob);
	}

	
	/**
	 * batch freeExclusiveImportJob action frees a BatchJob of type IMPORT that was claimed using the getExclusiveImportJobs
	 * 
	 * @action freeExclusiveImportJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism
	 * @param bool $resetExecutionAttempts Resets the job execution attampts to zero  
	 * @return KalturaFreeJobResponse 
	 */
	function freeExclusiveImportJobAction($id ,KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		return $this->freeExclusiveJobAction($id ,$lockKey, KalturaBatchJobType::IMPORT, $resetExecutionAttempts);
	}	
// --------------------------------- ImportJob functions 	--------------------------------- //

	
	
// --------------------------------- BulkUploadJob functions 	--------------------------------- //
	
	/**
	 * batch getExclusiveBulkUploadJob action allows to get a BatchJob of type BULKUPLOAD 
	 * 
	 * @action getExclusiveBulkUploadJobs
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param int $maxExecutionTime The maximum time in seconds the job reguarly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return. 
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only rartial list of jobs  
	 * @return KalturaBatchJobArray 
	 */
	function getExclusiveBulkUploadJobsAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		return $this->getExclusiveJobsAction($lockKey, $maxExecutionTime, $numberOfJobs, $filter, BatchJobType::BULKUPLOAD );
	}
	
	/**
	 * batch getExclusiveAlmostDoneBulkUploadJobs action allows to get a BatchJob of type BULKUPLOAD that wait for remote closure 
	 * 
	 * @action getExclusiveAlmostDoneBulkUploadJobs
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param int $maxExecutionTime The maximum time in seconds the job reguarly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return. 
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only rartial list of jobs  
	 * @return KalturaBatchJobArray 
	 */
	function getExclusiveAlmostDoneBulkUploadJobsAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		return $this->getExclusiveAlmostDoneAction($lockKey, $maxExecutionTime, $numberOfJobs, $filter, BatchJobType::BULKUPLOAD);
	}

	
	/**
	 * batch updateExclusiveBulkUploadJob action updates a BatchJob of type BULKUPLOAD that was claimed using the getExclusiveBulkUploadJobs
	 * 
	 * @action updateExclusiveBulkUploadJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param KalturaBatchJob $convertJob
	 * @return KalturaBatchJob 
	 */
	function updateExclusiveBulkUploadJobAction($id ,KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job)
	{
		$dbBatchJob = BatchJobPeer::retrieveByPK($id);
		
		// verifies that the job is of the right type
		if($dbBatchJob->getJobType() != KalturaBatchJobType::BULKUPLOAD)
			throw new KalturaAPIException(APIErrors::UPDATE_EXCLUSIVE_JOB_WRONG_TYPE, $id, serialize($lockKey), serialize($job));
	
		$dbBatchJob = kBatchManager::updateExclusiveBatchJob($id, $lockKey->toObject(), $job->toObject($dbBatchJob));
				
		$batchJob = new KalturaBatchJob(); // start from blank
		return $batchJob->fromObject($dbBatchJob);
	}

	
	/**
	 * batch freeExclusiveBulkUploadJob action frees a BatchJob of type BULKUPLOAD that was claimed using the getExclusiveBulkUploadJobs
	 * 
	 * @action freeExclusiveBulkUploadJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param bool $resetExecutionAttempts Resets the job execution attampts to zero  
	 * @return KalturaFreeJobResponse 
	 */
	function freeExclusiveBulkUploadJobAction($id ,KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		return $this->freeExclusiveJobAction($id ,$lockKey, KalturaBatchJobType::BULKUPLOAD, $resetExecutionAttempts);
	}	

	
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
		$bulkUploadResult->pluginsData = $pluginDataArray;
		$dbBulkUploadResult = $bulkUploadResult->toInsertableObject();
		$dbBulkUploadResult->save();
	
		if($bulkUploadResult->entryId && $pluginDataArray && $pluginDataArray->count)
		{
			$pluginValues = $pluginDataArray->toValuesArray();
			if(count($pluginValues))
			{
				$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaBulkUploadHandler');
				foreach($pluginInstances as $pluginInstance)
					$pluginInstance->handleBulkUploadData($bulkUploadResult->entryId, $pluginValues);
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
			KalturaEntryStatus::DELETED
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
	 * batch getExclusiveAlmostDoneConvertCollectionJobs action allows to get a BatchJob of type CONVERT_COLLECTION that wait for remote closure 
	 * 
	 * @action getExclusiveAlmostDoneConvertCollectionJobs
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param int $maxExecutionTime The maximum time in seconds the job reguarly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return. 
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only rartial list of jobs  
	 * @return KalturaBatchJobArray 
	 */
	function getExclusiveAlmostDoneConvertCollectionJobsAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		return $this->getExclusiveAlmostDoneAction($lockKey, $maxExecutionTime, $numberOfJobs, $filter, BatchJobType::CONVERT_COLLECTION );
	}	
	
	/**
	 * batch getExclusiveAlmostDoneConvertProfileJobs action allows to get a BatchJob of type CONVERT_PROFILE that wait for remote closure 
	 * 
	 * @action getExclusiveAlmostDoneConvertProfileJobs
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param int $maxExecutionTime The maximum time in seconds the job reguarly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return. 
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only rartial list of jobs  
	 * @return KalturaBatchJobArray 
	 */
	function getExclusiveAlmostDoneConvertProfileJobsAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		return $this->getExclusiveAlmostDoneAction($lockKey, $maxExecutionTime, $numberOfJobs, $filter, BatchJobType::CONVERT_PROFILE );
	}
	
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
	 * batch updateExclusiveConvertProfileJobAction action updates a BatchJob of type CONVERT_PROFILE that was claimed using the getExclusiveConvertJobs
	 * 
	 * @action updateExclusiveConvertProfileJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param KalturaBatchJob $job
	 * @return KalturaBatchJob 
	 */
	function updateExclusiveConvertProfileJobAction($id ,KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job)
	{
		$dbBatchJob = BatchJobPeer::retrieveByPK($id);
		
		// verifies that the job is of the right type
		if($dbBatchJob->getJobType() != KalturaBatchJobType::CONVERT_PROFILE)
			throw new KalturaAPIException(APIErrors::UPDATE_EXCLUSIVE_JOB_WRONG_TYPE, $id, serialize($lockKey), serialize($job));
	
		if($dbBatchJob->getStatus() != BatchJob::BATCHJOB_STATUS_FINISHED)
			$dbBatchJob = kBatchManager::updateExclusiveBatchJob($id, $lockKey->toObject(), $job->toObject($dbBatchJob));
				
		$batchJob = new KalturaBatchJob(); // start from blank
		return $batchJob->fromObject($dbBatchJob);
	}
	
	/**
	 * batch freeExclusiveConvertCollectionJobAction action frees a BatchJob of type CONVERT_COLLECTION that was claimed using the getExclusiveConvertJobs
	 * 
	 * @action freeExclusiveConvertCollectionJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param bool $resetExecutionAttempts Resets the job execution attampts to zero  
	 * @return KalturaFreeJobResponse 
	 */
	function freeExclusiveConvertCollectionJobAction($id ,KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		return $this->freeExclusiveJobAction($id ,$lockKey, KalturaBatchJobType::CONVERT_COLLECTION, $resetExecutionAttempts);
	}	
	
	/**
	 * batch freeExclusiveConvertProfileJobAction action frees a BatchJob of type CONVERT_PROFILE that was claimed using the getExclusiveConvertJobs
	 * 
	 * @action freeExclusiveConvertProfileJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param bool $resetExecutionAttempts Resets the job execution attampts to zero  
	 * @return KalturaFreeJobResponse 
	 */
	function freeExclusiveConvertProfileJobAction($id ,KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		return $this->freeExclusiveJobAction($id ,$lockKey, KalturaBatchJobType::CONVERT_PROFILE, $resetExecutionAttempts);
	}	
	
	
	/**
	 * batch getExclusiveConvertCollectionJob action allows to get a BatchJob of type CONVERT_COLLECTION 
	 * 
	 * @action getExclusiveConvertCollectionJobs
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param int $maxExecutionTime The maximum time in seconds the job reguarly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return. 
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only rartial list of jobs  
	 * @return KalturaBatchJobArray 
	 */
	function getExclusiveConvertCollectionJobsAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$jobs = $this->getExclusiveJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, BatchJobType::CONVERT_COLLECTION);
		
		return KalturaBatchJobArray::fromBatchJobArray($jobs);
	}
	
	
	/**
	 * batch getExclusiveConvertJob action allows to get a BatchJob of type CONVERT 
	 * 
	 * @action getExclusiveConvertJobs
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param int $maxExecutionTime The maximum time in seconds the job reguarly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return. 
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only rartial list of jobs  
	 * @return KalturaBatchJobArray 
	 */
	function getExclusiveConvertJobsAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$jobs = $this->getExclusiveJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, BatchJobType::CONVERT);
		
		if($jobs)
		{
			foreach ($jobs as &$job)
			{
				$data = $job->getData();
				$flavorParamsOutput = flavorParamsOutputPeer::retrieveByPK($data->getFlavorParamsOutputId());
				$data->setFlavorParamsOutput($flavorParamsOutput);
				$job->setData($data);
			}
		}
		
		return KalturaBatchJobArray::fromBatchJobArray($jobs);
	}
	
	/**
	 * batch getExclusiveAlmostDoneConvertJobsAction action allows to get a BatchJob of type CONVERT that wait for remote closure 
	 * 
	 * @action getExclusiveAlmostDoneConvertJobs
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param int $maxExecutionTime The maximum time in seconds the job reguarly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return. 
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only rartial list of jobs  
	 * @return KalturaBatchJobArray 
	 */
	function getExclusiveAlmostDoneConvertJobsAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		return $this->getExclusiveAlmostDoneAction($lockKey, $maxExecutionTime, $numberOfJobs, $filter, BatchJobType::CONVERT );
	}
	
	
	/**
	 * batch updateExclusiveConvertJob action updates a BatchJob of type CONVERT that was claimed using the getExclusiveConvertJobs
	 * 
	 * @action updateExclusiveConvertJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param KalturaBatchJob $job
	 * @return KalturaBatchJob 
	 */
	function updateExclusiveConvertJobAction($id ,KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job)
	{
		$dbBatchJob = BatchJobPeer::retrieveByPK($id);
		
		// verifies that the job is of the right type
		if($dbBatchJob->getJobType() != KalturaBatchJobType::CONVERT)
			throw new KalturaAPIException(APIErrors::UPDATE_EXCLUSIVE_JOB_WRONG_TYPE, $id, serialize($lockKey), serialize($job));
	
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

	
	/**
	 * batch freeExclusiveConvertJob action frees a BatchJob of type IMPORT that was claimed using the getExclusiveConvertJobs
	 * 
	 * @action freeExclusiveConvertJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param bool $resetExecutionAttempts Resets the job execution attampts to zero  
	 * @return KalturaFreeJobResponse 
	 */
	function freeExclusiveConvertJobAction($id ,KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		return $this->freeExclusiveJobAction($id ,$lockKey, KalturaBatchJobType::CONVERT, $resetExecutionAttempts);
	}	
// --------------------------------- ConvertJob functions 	--------------------------------- //

	
	
// --------------------------------- PostConvertJob functions 	--------------------------------- //

	
	
	/**
	 * batch getExclusivePostConvertJob action allows to get a BatchJob of type POSTCONVERT 
	 * 
	 * @action getExclusivePostConvertJobs
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param int $maxExecutionTime The maximum time in seconds the job reguarly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return. 
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only rartial list of jobs  
	 * @return KalturaBatchJobArray 
	 */
	function getExclusivePostConvertJobsAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		return $this->getExclusiveJobsAction($lockKey, $maxExecutionTime, $numberOfJobs, $filter, BatchJobType::POSTCONVERT );
	}

	
	/**
	 * batch updateExclusivePostConvertJob action updates a BatchJob of type POSTCONVERT that was claimed using the getExclusivePostConvertJobs
	 * 
	 * @action updateExclusivePostConvertJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param KalturaBatchJob $job
	 * @return KalturaBatchJob 
	 */
	function updateExclusivePostConvertJobAction($id ,KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job)
	{
		$dbBatchJob = BatchJobPeer::retrieveByPK($id);
		
		// verifies that the job is of the right type
		if($dbBatchJob->getJobType() != KalturaBatchJobType::POSTCONVERT)
			throw new KalturaAPIException(APIErrors::UPDATE_EXCLUSIVE_JOB_WRONG_TYPE, $id, serialize($lockKey), serialize($job));
	
		$dbBatchJob = kBatchManager::updateExclusiveBatchJob($id, $lockKey->toObject(), $job->toObject($dbBatchJob));
				
		$batchJob = new KalturaBatchJob(); // start from blank
		return $batchJob->fromObject($dbBatchJob);
	}

	
	/**
	 * batch freeExclusivePostConvertJob action frees a BatchJob of type IMPORT that was claimed using the getExclusivePostConvertJobs
	 * 
	 * @action freeExclusivePostConvertJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param bool $resetExecutionAttempts Resets the job execution attampts to zero  
	 * @return KalturaFreeJobResponse 
	 */
	function freeExclusivePostConvertJobAction($id ,KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		return $this->freeExclusiveJobAction($id ,$lockKey, KalturaBatchJobType::POSTCONVERT, $resetExecutionAttempts);
	}	

// --------------------------------- PostConvertJob functions 	--------------------------------- //

	
// --------------------------------- CaptureThumbJob functions 	--------------------------------- //

	
	
	/**
	 * batch getExclusiveCaptureThumbJob action allows to get a BatchJob of type CAPTURE_THUMB 
	 * 
	 * @action getExclusiveCaptureThumbJobs
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param int $maxExecutionTime The maximum time in seconds the job reguarly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return. 
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only rartial list of jobs  
	 * @return KalturaBatchJobArray 
	 */
	function getExclusiveCaptureThumbJobsAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$jobs = $this->getExclusiveJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, BatchJobType::CAPTURE_THUMB);
		
		if($jobs)
		{
			foreach ($jobs as &$job)
			{
				$data = $job->getData();
				$thumbParamsOutput = thumbParamsOutputPeer::retrieveByPK($data->getThumbParamsOutputId());
				$data->setThumbParamsOutput($thumbParamsOutput);
				$job->setData($data);
			}
		}
		
		return KalturaBatchJobArray::fromBatchJobArray($jobs);
	}

	
	/**
	 * batch updateExclusiveCaptureThumbJob action updates a BatchJob of type CAPTURE_THUMB that was claimed using the getExclusiveCaptureThumbJobs
	 * 
	 * @action updateExclusiveCaptureThumbJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param KalturaBatchJob $job
	 * @return KalturaBatchJob 
	 */
	function updateExclusiveCaptureThumbJobAction($id ,KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job)
	{
		$dbBatchJob = BatchJobPeer::retrieveByPK($id);
		
		// verifies that the job is of the right type
		if($dbBatchJob->getJobType() != KalturaBatchJobType::CAPTURE_THUMB)
			throw new KalturaAPIException(APIErrors::UPDATE_EXCLUSIVE_JOB_WRONG_TYPE, $id, serialize($lockKey), serialize($job));
	
		$dbBatchJob = kBatchManager::updateExclusiveBatchJob($id, $lockKey->toObject(), $job->toObject($dbBatchJob));
				
		$batchJob = new KalturaBatchJob(); // start from blank
		return $batchJob->fromObject($dbBatchJob);
	}

	
	/**
	 * batch freeExclusiveCaptureThumbJob action frees a BatchJob of type IMPORT that was claimed using the getExclusiveCaptureThumbJobs
	 * 
	 * @action freeExclusiveCaptureThumbJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param bool $resetExecutionAttempts Resets the job execution attampts to zero  
	 * @return KalturaFreeJobResponse 
	 */
	function freeExclusiveCaptureThumbJobAction($id ,KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		return $this->freeExclusiveJobAction($id ,$lockKey, KalturaBatchJobType::CAPTURE_THUMB, $resetExecutionAttempts);
	}	

// --------------------------------- CaptureThumbJob functions 	--------------------------------- //

	
// --------------------------------- ExtractMediaJob functions 	--------------------------------- //
	
	
	
	/**
	 * batch getExclusiveExtractMediaJob action allows to get a BatchJob of type EXTRACT_MEDIA 
	 * 
	 * @action getExclusiveExtractMediaJobs
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param int $maxExecutionTime The maximum time in seconds the job reguarly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return. 
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only rartial list of jobs  
	 * @return KalturaBatchJobArray 
	 */
	function getExclusiveExtractMediaJobsAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		return $this->getExclusiveJobsAction($lockKey, $maxExecutionTime, $numberOfJobs, $filter, BatchJobType::EXTRACT_MEDIA );
	}

	
	/**
	 * batch updateExclusiveExtractMediaJob action updates a BatchJob of type EXTRACT_MEDIA that was claimed using the getExclusiveExtractMediaJobs
	 * 
	 * @action updateExclusiveExtractMediaJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param KalturaBatchJob $job
	 * @return KalturaBatchJob 
	 */
	function updateExclusiveExtractMediaJobAction($id ,KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job)
	{
		$dbBatchJob = BatchJobPeer::retrieveByPK($id);
		
		// verifies that the job is of the right type
		if($dbBatchJob->getJobType() != KalturaBatchJobType::EXTRACT_MEDIA)
			throw new KalturaAPIException(APIErrors::UPDATE_EXCLUSIVE_JOB_WRONG_TYPE, $id, serialize($lockKey), serialize($job));
	
		$dbBatchJob = kBatchManager::updateExclusiveBatchJob($id, $lockKey->toObject(), $job->toObject($dbBatchJob));
				
		$batchJob = new KalturaBatchJob(); // start from blank
		return $batchJob->fromObject($dbBatchJob);
	}

	
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
			$flavorAsset = flavorAssetPeer::retrieveById($mediaInfo->flavorAssetId);
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
	
	/**
	 * batch freeExclusiveExtractMediaJob action frees a BatchJob of type IMPORT that was claimed using the getExclusiveExtractMediaJobs
	 * 
	 * @action freeExclusiveExtractMediaJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param bool $resetExecutionAttempts Resets the job execution attampts to zero  
	 * @return KalturaFreeJobResponse 
	 */
	function freeExclusiveExtractMediaJobAction($id ,KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		return $this->freeExclusiveJobAction($id ,$lockKey, KalturaBatchJobType::EXTRACT_MEDIA, $resetExecutionAttempts);
	}	
// --------------------------------- ExtractMediaJob functions 	--------------------------------- //
	
	
// --------------------------------- StorageExportJob functions 	--------------------------------- //
	
	
	
	/**
	 * batch getExclusiveStorageExportJob action allows to get a BatchJob of type STORAGE_EXPORT 
	 * 
	 * @action getExclusiveStorageExportJobs
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param int $maxExecutionTime The maximum time in seconds the job reguarly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return. 
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only rartial list of jobs  
	 * @return KalturaBatchJobArray 
	 */
	function getExclusiveStorageExportJobsAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		return $this->getExclusiveJobsAction($lockKey, $maxExecutionTime, $numberOfJobs, $filter, BatchJobType::STORAGE_EXPORT );
	}

	
	/**
	 * batch updateExclusiveStorageExportJob action updates a BatchJob of type STORAGE_EXPORT that was claimed using the getExclusiveStorageExportJobs
	 * 
	 * @action updateExclusiveStorageExportJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param KalturaBatchJob $job
	 * @return KalturaBatchJob 
	 */
	function updateExclusiveStorageExportJobAction($id ,KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job)
	{
		$dbBatchJob = BatchJobPeer::retrieveByPK($id);
		
		// verifies that the job is of the right type
		if($dbBatchJob->getJobType() != KalturaBatchJobType::STORAGE_EXPORT)
			throw new KalturaAPIException(APIErrors::UPDATE_EXCLUSIVE_JOB_WRONG_TYPE, $id, serialize($lockKey), serialize($job));
	
		$dbBatchJob = kBatchManager::updateExclusiveBatchJob($id, $lockKey->toObject(), $job->toObject($dbBatchJob));
				
		$batchJob = new KalturaBatchJob(); // start from blank
		return $batchJob->fromObject($dbBatchJob);
	}

	
	/**
	 * batch freeExclusiveStorageExportJob action frees a BatchJob of type IMPORT that was claimed using the getExclusiveStorageExportJobs
	 * 
	 * @action freeExclusiveStorageExportJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param bool $resetExecutionAttempts Resets the job execution attampts to zero  
	 * @return KalturaFreeJobResponse 
	 */
	function freeExclusiveStorageExportJobAction($id ,KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		return $this->freeExclusiveJobAction($id ,$lockKey, KalturaBatchJobType::STORAGE_EXPORT, $resetExecutionAttempts);
	}	
// --------------------------------- StorageExportJob functions 	--------------------------------- //
		
// --------------------------------- StorageDeleteJob functions 	--------------------------------- //
	
	
	
	/**
	 * batch getExclusiveStorageDeleteJob action allows to get a BatchJob of type STORAGE_DELETE 
	 * 
	 * @action getExclusiveStorageDeleteJobs
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param int $maxExecutionTime The maximum time in seconds the job reguarly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return. 
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only rartial list of jobs  
	 * @return KalturaBatchJobArray 
	 */
	function getExclusiveStorageDeleteJobsAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		return $this->getExclusiveJobsAction($lockKey, $maxExecutionTime, $numberOfJobs, $filter, BatchJobType::STORAGE_DELETE );
	}

	
	/**
	 * batch updateExclusiveStorageDeleteJob action updates a BatchJob of type StorageDelete that was claimed using the getExclusiveStorageDeleteJobs
	 * 
	 * @action updateExclusiveStorageDeleteJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param KalturaBatchJob $job
	 * @return KalturaBatchJob 
	 */
	function updateExclusiveStorageDeleteJobAction($id ,KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job)
	{
		$dbBatchJob = BatchJobPeer::retrieveByPK($id);
		
		// verifies that the job is of the right type
		if($dbBatchJob->getJobType() != KalturaBatchJobType::STORAGE_DELETE)
			throw new KalturaAPIException(APIErrors::UPDATE_EXCLUSIVE_JOB_WRONG_TYPE, $id, serialize($lockKey), serialize($job));
	
		$dbBatchJob = kBatchManager::updateExclusiveBatchJob($id, $lockKey->toObject(), $job->toObject($dbBatchJob));
				
		$batchJob = new KalturaBatchJob(); // start from blank
		return $batchJob->fromObject($dbBatchJob);
	}

	
	/**
	 * batch freeExclusiveStorageDeleteJob action frees a BatchJob of type IMPORT that was claimed using the getExclusiveStorageDeleteJobs
	 * 
	 * @action freeExclusiveStorageDeleteJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param bool $resetExecutionAttempts Resets the job execution attampts to zero  
	 * @return KalturaFreeJobResponse 
	 */
	function freeExclusiveStorageDeleteJobAction($id ,KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		return $this->freeExclusiveJobAction($id ,$lockKey, KalturaBatchJobType::STORAGE_DELETE, $resetExecutionAttempts);
	}	
// --------------------------------- StorageDeleteJob functions 	--------------------------------- //
	
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

	
	/**
	 * batch updateExclusiveNotificationJob action updates a BatchJob of type NOTIFICATION that was claimed using the getExclusiveNotificationJobs
	 * 
	 * @action updateExclusiveNotificationJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param KalturaBatchJob $job
	 * @return KalturaBatchJob 
	 */
	function updateExclusiveNotificationJobAction($id ,KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job)
	{
		$dbBatchJob = BatchJobPeer::retrieveByPK($id);
		
		// verifies that the job is of the right type
		if($dbBatchJob->getJobType() != KalturaBatchJobType::NOTIFICATION)
			throw new KalturaAPIException(APIErrors::UPDATE_EXCLUSIVE_JOB_WRONG_TYPE, $id, serialize($lockKey), serialize($job));
	
		$dbBatchJob = kBatchManager::updateExclusiveBatchJob($id, $lockKey->toObject(), $job->toObject($dbBatchJob));
				
		$batchJob = new KalturaBatchJob(); // start from blank
		return $batchJob->fromObject($dbBatchJob);
	}

	
	/**
	 * batch freeExclusiveNotificationJob action frees a BatchJob of type IMPORT that was claimed using the getExclusiveNotificationJobs
	 * 
	 * @action freeExclusiveNotificationJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param bool $resetExecutionAttempts Resets the job execution attampts to zero  
	 * @return KalturaFreeJobResponse 
	 */
	function freeExclusiveNotificationJobAction($id ,KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		return $this->freeExclusiveJobAction($id ,$lockKey, KalturaBatchJobType::NOTIFICATION, $resetExecutionAttempts);
	}	
	
	
// --------------------------------- Notification functions 	--------------------------------- //


	
// --------------------------------- MailJob functions 	--------------------------------- //	
	
	/**
	 * batch getExclusiveMailJob action allows to get a BatchJob of type MAIL 
	 * 
	 * @action getExclusiveMailJobs
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param int $maxExecutionTime The maximum time in seconds the job reguarly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return. 
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only rartial list of jobs  
	 * @return KalturaBatchJobArray 
	 */
	function getExclusiveMailJobsAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		return $this->getExclusiveJobsAction($lockKey, $maxExecutionTime, $numberOfJobs, $filter, BatchJobType::MAIL );
	}

	
	/**
	 * batch updateExclusiveMailJob action updates a BatchJob of type MAIL that was claimed using the getExclusiveMailJobs
	 * 
	 * @action updateExclusiveMailJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param KalturaBatchJob $job
	 * @return KalturaBatchJob 
	 */
	function updateExclusiveMailJobAction($id ,KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job)
	{
		$dbBatchJob = BatchJobPeer::retrieveByPK($id);
		
		// verifies that the job is of the right type
		if($dbBatchJob->getJobType() != KalturaBatchJobType::MAIL)
			throw new KalturaAPIException(APIErrors::UPDATE_EXCLUSIVE_JOB_WRONG_TYPE, $id, serialize($lockKey), serialize($job));
	
		$dbBatchJob = kBatchManager::updateExclusiveBatchJob($id, $lockKey->toObject(), $job->toObject($dbBatchJob));
				
		$batchJob = new KalturaBatchJob(); // start from blank
		return $batchJob->fromObject($dbBatchJob);
	}

	
	/**
	 * batch freeExclusiveMailJob action frees a BatchJob of type MAIL that was claimed using the getExclusiveMailJobs
	 * 
	 * @action freeExclusiveMailJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param bool $resetExecutionAttempts Resets the job execution attampts to zero  
	 * @return KalturaFreeJobResponse 
	 */
	function freeExclusiveMailJobAction($id ,KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		return $this->freeExclusiveJobAction($id ,$lockKey, KalturaBatchJobType::MAIL, $resetExecutionAttempts);
	}	
// --------------------------------- MailJob functions 	--------------------------------- //
	

// --------------------------------- BulkDownloadJob functions 	--------------------------------- //
	
	/**
	 * batch getExclusiveBulkDownloadJobs action allows to get a BatchJob of type BULKDOWNLOAD
	 * 
	 * @action getExclusiveBulkDownloadJobs
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param int $maxExecutionTime The maximum time in seconds the job reguarly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return. 
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only rartial list of jobs  
	 * @return KalturaBatchJobArray 
	 */
	function getExclusiveBulkDownloadJobsAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		return $this->getExclusiveJobsAction($lockKey, $maxExecutionTime, $numberOfJobs, $filter, BatchJobType::BULKDOWNLOAD);
	}
	
	/**
	 * batch getExclusiveAlmostDoneBulkDownloadJobs action allows to get a BatchJob of type BULKDOWNLOAD that wait for remote closure 
	 * 
	 * @action getExclusiveAlmostDoneBulkDownloadJobs
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param int $maxExecutionTime The maximum time in seconds the job reguarly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return. 
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only rartial list of jobs  
	 * @return KalturaBatchJobArray 
	 */
	function getExclusiveAlmostDoneBulkDownloadJobsAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		return $this->getExclusiveAlmostDoneAction($lockKey, $maxExecutionTime, $numberOfJobs, $filter, BatchJobType::BULKDOWNLOAD );
	}

	/**
	 * batch updateExclusiveBulkDownloadJob action updates a BatchJob of type BULKDOWNLOAD that was claimed using the getExclusiveBulkDownloadJobs
	 * 
	 * @action updateExclusiveBulkDownloadJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param KalturaBatchJob $job
	 * @return KalturaBatchJob 
	 */
	function updateExclusiveBulkDownloadJobAction($id ,KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job)
	{
		$dbBatchJob = BatchJobPeer::retrieveByPK($id);
		
		// verifies that the job is of the right type
		if($dbBatchJob->getJobType() != KalturaBatchJobType::BULKDOWNLOAD)
			throw new KalturaAPIException(APIErrors::UPDATE_EXCLUSIVE_JOB_WRONG_TYPE, $id, serialize($lockKey), serialize($job));
	
		$dbBatchJob = kBatchManager::updateExclusiveBatchJob($id, $lockKey->toObject(), $job->toObject($dbBatchJob));
				
		$batchJob = new KalturaBatchJob(); // start from blank
		return $batchJob->fromObject($dbBatchJob);
	}
	
	/**
	 * batch freeExclusiveBulkDownloadJob action frees a BatchJob of type BULKDOWNLOAD that was claimed using the getExclusiveBulkDownloadJobs
	 * 
	 * @action freeExclusiveBulkDownloadJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param bool $resetExecutionAttempts Resets the job execution attampts to zero  
	 * @return KalturaFreeJobResponse 
	 */
	function freeExclusiveBulkDownloadJobAction($id ,KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		return $this->freeExclusiveJobAction($id ,$lockKey, KalturaBatchJobType::BULKDOWNLOAD, $resetExecutionAttempts);
	}	
	
// --------------------------------- BulkDownloadJob functions 	--------------------------------- //

	
// --------------------------------- ProvisionProvideJob functions 	--------------------------------- //
	
	/**
	 * batch getExclusiveProvisionProvideJobs action allows to get a BatchJob of type ProvisionProvide
	 * 
	 * @action getExclusiveProvisionProvideJobs
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param int $maxExecutionTime The maximum time in seconds the job reguarly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return. 
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only rartial list of jobs  
	 * @return KalturaBatchJobArray 
	 */
	function getExclusiveProvisionProvideJobsAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		return $this->getExclusiveJobsAction($lockKey, $maxExecutionTime, $numberOfJobs, $filter, BatchJobType::PROVISION_PROVIDE);
	}
	
	/**
	 * batch getExclusiveAlmostDoneProvisionProvideJobs action allows to get a BatchJob of type ProvisionProvide that wait for remote closure 
	 * 
	 * @action getExclusiveAlmostDoneProvisionProvideJobs
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param int $maxExecutionTime The maximum time in seconds the job reguarly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return. 
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only rartial list of jobs  
	 * @return KalturaBatchJobArray 
	 */
	function getExclusiveAlmostDoneProvisionProvideJobsAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		return $this->getExclusiveAlmostDoneAction($lockKey, $maxExecutionTime, $numberOfJobs, $filter, BatchJobType::PROVISION_PROVIDE );
	}

	/**
	 * batch updateExclusiveProvisionProvideJob action updates a BatchJob of type ProvisionProvide that was claimed using the getExclusiveProvisionProvideJobs
	 * 
	 * @action updateExclusiveProvisionProvideJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param KalturaBatchJob $job
	 * @return KalturaBatchJob 
	 */
	function updateExclusiveProvisionProvideJobAction($id ,KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job)
	{
		$dbBatchJob = BatchJobPeer::retrieveByPK($id);
		
		// verifies that the job is of the right type
		if($dbBatchJob->getJobType() != KalturaBatchJobType::PROVISION_PROVIDE)
			throw new KalturaAPIException(APIErrors::UPDATE_EXCLUSIVE_JOB_WRONG_TYPE, $id, serialize($lockKey), serialize($job));
	
		$dbBatchJob = kBatchManager::updateExclusiveBatchJob($id, $lockKey->toObject(), $job->toObject($dbBatchJob));
				
		$batchJob = new KalturaBatchJob(); // start from blank
		return $batchJob->fromObject($dbBatchJob);
	}
	
	/**
	 * batch freeExclusiveProvisionProvideJob action frees a BatchJob of type ProvisionProvide that was claimed using the getExclusiveProvisionProvideJobs
	 * 
	 * @action freeExclusiveProvisionProvideJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param bool $resetExecutionAttempts Resets the job execution attampts to zero  
	 * @return KalturaFreeJobResponse 
	 */
	function freeExclusiveProvisionProvideJobAction($id ,KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		return $this->freeExclusiveJobAction($id ,$lockKey, KalturaBatchJobType::PROVISION_PROVIDE, $resetExecutionAttempts);
	}	
	
// --------------------------------- ProvisionProvideJob functions 	--------------------------------- //
	
// --------------------------------- ProvisionDeleteJob functions 	--------------------------------- //
	
	/**
	 * batch getExclusiveProvisionDeleteJobs action allows to get a BatchJob of type ProvisionDelete
	 * 
	 * @action getExclusiveProvisionDeleteJobs
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param int $maxExecutionTime The maximum time in seconds the job reguarly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return. 
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only rartial list of jobs  
	 * @return KalturaBatchJobArray 
	 */
	function getExclusiveProvisionDeleteJobsAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		return $this->getExclusiveJobsAction($lockKey, $maxExecutionTime, $numberOfJobs, $filter, BatchJobType::PROVISION_DELETE);
	}
	
	/**
	 * batch getExclusiveAlmostDoneProvisionDeleteJobs action allows to get a BatchJob of type ProvisionDelete that wait for remote closure 
	 * 
	 * @action getExclusiveAlmostDoneProvisionDeleteJobs
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param int $maxExecutionTime The maximum time in seconds the job reguarly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return. 
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only rartial list of jobs  
	 * @return KalturaBatchJobArray 
	 */
	function getExclusiveAlmostDoneProvisionDeleteJobsAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		return $this->getExclusiveAlmostDoneAction($lockKey, $maxExecutionTime, $numberOfJobs, $filter, BatchJobType::PROVISION_DELETE );
	}

	/**
	 * batch updateExclusiveProvisionDeleteJob action updates a BatchJob of type ProvisionDelete that was claimed using the getExclusiveProvisionDeleteJobs
	 * 
	 * @action updateExclusiveProvisionDeleteJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param KalturaBatchJob $job
	 * @return KalturaBatchJob 
	 */
	function updateExclusiveProvisionDeleteJobAction($id ,KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job)
	{
		$dbBatchJob = BatchJobPeer::retrieveByPK($id);
		
		// verifies that the job is of the right type
		if($dbBatchJob->getJobType() != KalturaBatchJobType::PROVISION_DELETE)
			throw new KalturaAPIException(APIErrors::UPDATE_EXCLUSIVE_JOB_WRONG_TYPE, $id, serialize($lockKey), serialize($job));
	
		$dbBatchJob = kBatchManager::updateExclusiveBatchJob($id, $lockKey->toObject(), $job->toObject($dbBatchJob));
				
		$batchJob = new KalturaBatchJob(); // start from blank
		return $batchJob->fromObject($dbBatchJob);
	}
	
	/**
	 * batch freeExclusiveProvisionDeleteJob action frees a BatchJob of type ProvisionDelete that was claimed using the getExclusiveProvisionDeleteJobs
	 * 
	 * @action freeExclusiveProvisionDeleteJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param bool $resetExecutionAttempts Resets the job execution attampts to zero  
	 * @return KalturaFreeJobResponse 
	 */
	function freeExclusiveProvisionDeleteJobAction($id ,KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		return $this->freeExclusiveJobAction($id ,$lockKey, KalturaBatchJobType::PROVISION_DELETE, $resetExecutionAttempts);
	}	
	
// --------------------------------- ProvisionDeleteJob functions 	--------------------------------- //
	
	
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
		$filter = $workerQueueFilter->filter->toObject(new BatchJobFilter());
		
		return kBatchManager::getQueueSize($workerQueueFilter->schedulerId, $workerQueueFilter->workerId, $workerQueueFilter->jobType, $filter);
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
		$jobsFilter = new BatchJobFilter();
		if (!$filter)
			$jobsFilter = $filter->toObject($jobsFilter);
		
		$jobs = kBatchManager::getExclusiveAlmostDoneJobs($lockKey->toObject(), $maxExecutionTime, $numberOfJobs, $jobType, $jobsFilter);
		return KalturaBatchJobArray::fromBatchJobArray($jobs);
	}
	
	protected function getExclusiveJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, $jobType)
	{
		if (!is_null($filter))
			$jobsFilter = $filter->toFilter($jobType);
		
		$dbJobType = kPluginableEnumsManager::apiToCore('KalturaBatchJobType', $jobType);
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
		$flavorAsset = flavorAssetPeer::retrieveById($flavorAssetId);
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
