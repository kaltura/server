<?php
/**
 * @service virusScanBatch
 * @package api
 * @subpackage services
 * @package plugins.virusScan
 * @subpackage api.services
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
	 * @return KalturaBatchJobArray 
	 */
	function getExclusiveVirusScanJobsAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$jobType = VirusScanPlugin::getApiValue(VirusScanBatchJobType::VIRUS_SCAN);
		return $this->getExclusiveJobsAction($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $jobType);
	}

	
	/**
	 * batch updateExclusiveVirusScanJob action updates a BatchJob of type VIRUS_SCAN that was claimed using the getExclusiveVirusScanJobs
	 * 
	 * @action updateExclusiveVirusScanJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param KalturaBatchJob $job
	 * @return KalturaBatchJob 
	 */
	function updateExclusiveVirusScanJobAction($id ,KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job)
	{
		$dbBatchJob = BatchJobPeer::retrieveByPK($id);
		
		// verifies that the job is of the right type
		$jobType = VirusScanPlugin::getBatchJobTypeCoreValue(VirusScanBatchJobType::VIRUS_SCAN);
		if($dbBatchJob->getJobType() != $jobType)
			throw new KalturaAPIException(APIErrors::UPDATE_EXCLUSIVE_JOB_WRONG_TYPE, $id, serialize($lockKey), serialize($job));
	
		$dbBatchJob = kBatchManager::updateExclusiveBatchJob($id, $lockKey->toObject(), $job->toObject($dbBatchJob));
				
		$batchJob = new KalturaBatchJob(); // start from blank
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
		$jobType = VirusScanPlugin::getBatchJobTypeCoreValue(VirusScanBatchJobType::VIRUS_SCAN);
		return $this->freeExclusiveJobAction($id ,$lockKey, $jobType, $resetExecutionAttempts);
	}
	
// --------------------------------- VirusScanJob functions 	--------------------------------- //

}
