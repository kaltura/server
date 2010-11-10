<?php
/**
 * @service virusScanBatch
 * @package api
 * @subpackage services
 */
class VirusScanBatchService extends BatchService 
{
	
	
// --------------------------------- VirusScanJob functions 	--------------------------------- //
	
	/**
	 * batch getExclusiveVirusScanJob action allows to get a BatchJob of type VIRUS_SCAN 
	 * 
	 * @action getExclusiveVirusScanJobs
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param int $maxExecutionTime The maximum time in seconds the job reguarly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return. 
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only rartial list of jobs  
	 * @return KalturaVirusScanBatchJobArray 
	 */
	function getExclusiveVirusScanJobsAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		return $this->getExclusiveJobsAction($lockKey, $maxExecutionTime, $numberOfJobs, $filter, BatchJob::BATCHJOB_TYPE_VIRUS_SCAN );
	}

	
	/**
	 * batch updateExclusiveVirusScanJob action updates a BatchJob of type VIRUS_SCAN that was claimed using the getExclusiveVirusScanJobs
	 * 
	 * @action updateExclusiveVirusScanJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param KalturaVirusScanBatchJob $job
	 * @return KalturaVirusScanBatchJob 
	 */
	function updateExclusiveVirusScanJobAction($id ,KalturaExclusiveLockKey $lockKey, KalturaVirusScanBatchJob $job)
	{
		$dbBatchJob = BatchJobPeer::retrieveByPK($id);
		
		// verifies that the job is of the right type
		if($dbBatchJob->getJobType() != KalturaBatchJobType::VIRUS_SCAN)
			throw new KalturaAPIException(APIErrors::UPDATE_EXCLUSIVE_JOB_WRONG_TYPE, $id, serialize($lockKey), serialize($job));
	
		$dbBatchJob = kBatchManager::updateExclusiveBatchJob($id, $lockKey->toObject(), $job->toObject($dbBatchJob));
				
		$batchJob = new KalturaVirusScanBatchJob(); // start from blank
		return $batchJob->fromObject($dbBatchJob);
	}

	
	/**
	 * batch freeExclusiveVirusScanJob action frees a BatchJob of type VirusScan that was claimed using the getExclusiveVirusScanJobs
	 * 
	 * @action freeExclusiveVirusScanJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism
	 * @param bool $resetExecutionAttempts Resets the job execution attampts to zero  
	 * @return KalturaFreeJobResponse 
	 */
	function freeExclusiveVirusScanJobAction($id ,KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		return $this->freeExclusiveJobAction($id ,$lockKey, KalturaBatchJobType::VIRUS_SCAN, $resetExecutionAttempts);
	}
	
// --------------------------------- VirusScanJob functions 	--------------------------------- //

	
	

	/**
	 * batch getExclusiveJobsAction action allows to get a BatchJob 
	 * 
	 * @action getExclusiveJobs
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param int $maxExecutionTime The maximum time in seconds the job reguarly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return. 
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only rartial list of jobs  
	 * @param int $jobType The type of the job - could be a custom extended type
	 * @return KalturaVirusScanBatchJobArray 
	 */
	function getExclusiveJobsAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, $jobType = null)
	{
		$jobs = $this->getExclusiveJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $jobType);
		return KalturaVirusScanBatchJobArray::fromBatchJobArray($jobs);
	}	
}
