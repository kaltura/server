<?php



/**
 * @service filesyncImportBatch
 * @package api
 * @subpackage services
 */
class FileSyncImportBatchService extends BatchService 
{


// --------------------------------- FileSyncImportJob functions 	--------------------------------- //
	
	
	/**
	 * batch getExclusiveFileSyncImportJob action allows to get a BatchJob of type FILESYNC_IMPORT 
	 * 
	 * @action getExclusiveFileSyncImportJobs
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param int $maxExecutionTime The maximum time in seconds the job reguarly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return. 
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only rartial list of jobs  
	 * @return KalturaFileSyncImportBatchJobArray 
	 */
	function getExclusiveFileSyncImportJobsAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$jobs = $this->getExclusiveJobsAction($lockKey, $maxExecutionTime, $numberOfJobs, $filter, BatchJob::BATCHJOB_TYPE_FILESYNC_IMPORT );
		
		if($jobs)
		{
			
			foreach ($jobs as $job)
			{
				$data = $job->data;
				// try to get destination path from file sync
				$fileSyncId = $data->filesyncId;
				
				$fileSync = FileSyncPeer::retrieveByPK($fileSyncId);
				if (!$fileSync) {
					throw new KalturaAPIException(MultiCentersErrors::INVALID_FILESYNC_RECORD, $fileSyncId);
				}
				$fileSyncRoot = $fileSync->getFileRoot();
				$fileSyncPath = $fileSync->getFilePath();
				
				if ($fileSyncRoot && $fileSyncPath) {
					// destination path set on filesync
					$dest_path = $fileSyncRoot.$fileSyncPath;					
				}
				else {
					// not set on filesync - get path from path manager
					$fileSyncKey = kFileSyncUtils::getKeyForFileSync($fileSync);
					list($file_root, $real_path) = kPathManager::getFilePathArr($fileSyncKey);
					$dest_path = $file_root . $real_path;
					// update filesync on database
					$fileSync->setFileRoot($file_root);
					$fileSync->setFilePath($real_path);
					$fileSync->save();
				}
				
				// update job data with destination path if needed
				if ($data->destFilePath != $dest_path) {	
					$data->destFilePath = $dest_path;
					$job->data = $data;
				}
				
				if (!is_dir(dirname($dest_path)) && !@mkdir(dirname($dest_path), 0755, true)) {
					KalturaLog::ERR("Cannot create directory [$dest_path] - ".error_get_last());
				}
			}
		}
				
		return $jobs;
	}
	
	
	/**
	 * batch updateExclusiveFileSyncImportJob action updates a BatchJob of type FILESYNC_IMPORT that was claimed using the getExclusiveFileSyncImportJobs
	 * 
	 * @action updateExclusiveFileSyncImportJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param KalturaBatchJob $job
	 * @param KalturaEntryStatus $entryStatus Optional parameter if the entry of the batch should change 
	 * @return KalturaFileSyncImportBatchJob 
	 */
	function updateExclusiveFileSyncImportJobAction($id ,KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job)
	{
		$dbBatchJob = BatchJobPeer::retrieveByPK($id);
		
		// verifies that the job is of the right type
		if($dbBatchJob->getJobType() != KalturaBatchJobType::FILESYNC_IMPORT)
			throw new KalturaAPIException(APIErrors::UPDATE_EXCLUSIVE_JOB_WRONG_TYPE, $id, serialize($lockKey), serialize($job));
	
		$dbBatchJob = kBatchManager::updateExclusiveBatchJob($id, $lockKey->toObject(), $job->toObject($dbBatchJob));
				
		$batchJob = new KalturaFileSyncImportBatchJob(); // start from blank
		return $batchJob->fromObject($dbBatchJob);
	}
	
	/**
	 * batch freeExclusiveFileSyncImportJob action frees a BatchJob of type FILESYNC_IMPORT that was claimed using the getExclusiveFileSyncImportJobs
	 * 
	 * @action freeExclusiveFileSyncImportJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism
	 * @param bool $resetExecutionAttempts Resets the job execution attampts to zero  
	 * @return KalturaFreeJobResponse 
	 */
	function freeExclusiveFileSyncImportJobAction($id ,KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		return $this->freeExclusiveJobAction($id ,$lockKey, KalturaBatchJobType::FILESYNC_IMPORT, $resetExecutionAttempts);
	}	
	
	
	/**
	 * batch getExclusiveAlmostDoneFileSyncImportJobs action allows to get a BatchJob of type BULKUPLOAD that wait for remote closure 
	 * 
	 * @action getExclusiveAlmostDoneFileSyncImportJobs
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param int $maxExecutionTime The maximum time in seconds the job reguarly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return. 
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only rartial list of jobs  
	 * @return KalturaFileSyncImportBatchJobArray 
	 */
	function getExclusiveAlmostDoneFileSyncImportJobsAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		return $this->getExclusiveAlmostDoneAction($lockKey, $maxExecutionTime, $numberOfJobs, $filter, BatchJob::BATCHJOB_TYPE_FILESYNC_IMPORT);
	}
	
	
	/**
	 * batch getExclusiveJobsAction action allows to get a BatchJob 
	 * 
	 * @action getExclusiveJobs
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param int $maxExecutionTime The maximum time in seconds the job reguarly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return. 
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only rartial list of jobs  
	 * @param int $jobType The type of the job - could be a custom extended type
	 * @return KalturaFileSyncImportBatchJobArray 
	 */
	function getExclusiveJobsAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, $jobType)
	{
		$jobs = $this->getExclusiveJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $jobType);
		return KalturaFileSyncImportBatchJobArray::fromBatchJobArray($jobs);
	}

	
	
	/**
	 * batch getExclusiveAlmostDone action allows to get a BatchJob that wait for remote closure 
	 * 
	 * @action getExclusiveAlmostDone
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param int $maxExecutionTime The maximum time in seconds the job reguarly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return. 
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only rartial list of jobs  
	 * @param int $jobType The type of the job - could be a custom extended type
	 * @return KalturaFileSyncImportBatchJobArray 
	 */
	function getExclusiveAlmostDoneAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, $jobType)
	{
		$jobsFilter = new BatchJobFilter();
		if (!$filter)
			$jobsFilter = $filter->toObject($jobsFilter);
		
		$jobs = kBatchManager::getExclusiveAlmostDoneJobs($lockKey->toObject(), $maxExecutionTime, $numberOfJobs, $jobType, $jobsFilter);
		return KalturaFileSyncImportBatchJobArray::fromBatchJobArray($jobs);
	}
	
// --------------------------------- End of FileSyncImportJob functions 	------------------------ //


	
}
