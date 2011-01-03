<?php
/**
 * @service contentDistributionBatch
 * @package api
 * @subpackage services
 */
class ContentDistributionBatchService extends BatchService 
{

	
// --------------------------------- DistributionSubmitJob functions 	--------------------------------- //
	
	/**
	 * batch getExclusiveDistributionSubmitJob action allows to get a BatchJob of type DISTRIBUTION_SUBMIT 
	 * 
	 * @action getExclusiveDistributionSubmitJobs
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param int $maxExecutionTime The maximum time in seconds the job reguarly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return. 
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only rartial list of jobs  
	 * @return KalturaBatchJobArray 
	 */
	function getExclusiveDistributionSubmitJobsAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$jobType = ContentDistributionPlugin::getApiValue(ContentDistributionBatchJobType::DISTRIBUTION_SUBMIT);
		return $this->getExclusiveJobsAction($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $jobType);
	}

	
	/**
	 * batch updateExclusiveDistributionSubmitJob action updates a BatchJob of type DISTRIBUTION_SUBMIT that was claimed using the getExclusiveDistributionSubmitJobs
	 * 
	 * @action updateExclusiveDistributionSubmitJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param KalturaBatchJob $job
	 * @return KalturaBatchJob 
	 */
	function updateExclusiveDistributionSubmitJobAction($id ,KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job)
	{
		$dbBatchJob = BatchJobPeer::retrieveByPK($id);
		
		// verifies that the job is of the right type
		$jobType = ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_SUBMIT);
		if($dbBatchJob->getJobType() != $jobType)
			throw new KalturaAPIException(APIErrors::UPDATE_EXCLUSIVE_JOB_WRONG_TYPE, $id, serialize($lockKey), serialize($job));
	
		$dbBatchJob = kBatchManager::updateExclusiveBatchJob($id, $lockKey->toObject(), $job->toObject($dbBatchJob));
				
		$batchJob = new KalturaBatchJob(); // start from blank
		return $batchJob->fromObject($dbBatchJob);
	}

	
	/**
	 * batch freeExclusiveDistributionSubmitJob action frees a BatchJob of type DistributionSubmit that was claimed using the getExclusiveDistributionSubmitJobs
	 * 
	 * @action freeExclusiveDistributionSubmitJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism
	 * @param bool $resetExecutionAttempts Resets the job execution attampts to zero  
	 * @return KalturaFreeJobResponse 
	 */
	function freeExclusiveDistributionSubmitJobAction($id ,KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		$jobType = ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_SUBMIT);
		return $this->freeExclusiveJobAction($id ,$lockKey, $jobType, $resetExecutionAttempts);
	}
	
	
	/**
	 * batch getExclusiveAlmostDoneDistributionSubmitJobs action allows to get a BatchJob of type DISTRIBUTION_SUBMIT that wait for remote closure 
	 * 
	 * @action getExclusiveAlmostDoneDistributionSubmitJobs
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param int $maxExecutionTime The maximum time in seconds the job reguarly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return. 
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only rartial list of jobs  
	 * @return KalturaBatchJobArray 
	 */
	function getExclusiveAlmostDoneDistributionSubmitJobsAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$jobType = ContentDistributionPlugin::getApiValue(ContentDistributionBatchJobType::DISTRIBUTION_SUBMIT);
		return $this->getExclusiveAlmostDoneAction($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $jobType);
	}
	
// --------------------------------- DistributionSubmitJob functions 	--------------------------------- //
	
	
// --------------------------------- DistributionUpdateJob functions 	--------------------------------- //
	
	/**
	 * batch getExclusiveDistributionUpdateJob action allows to get a BatchJob of type DISTRIBUTION_UPDATE 
	 * 
	 * @action getExclusiveDistributionUpdateJobs
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param int $maxExecutionTime The maximum time in seconds the job reguarly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return. 
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only rartial list of jobs  
	 * @return KalturaBatchJobArray 
	 */
	function getExclusiveDistributionUpdateJobsAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$jobType = ContentDistributionPlugin::getApiValue(ContentDistributionBatchJobType::DISTRIBUTION_UPDATE);
		return $this->getExclusiveJobsAction($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $jobType);
	}

	
	/**
	 * batch updateExclusiveDistributionUpdateJob action updates a BatchJob of type DISTRIBUTION_UPDATE that was claimed using the getExclusiveDistributionUpdateJobs
	 * 
	 * @action updateExclusiveDistributionUpdateJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param KalturaBatchJob $job
	 * @return KalturaBatchJob 
	 */
	function updateExclusiveDistributionUpdateJobAction($id ,KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job)
	{
		$dbBatchJob = BatchJobPeer::retrieveByPK($id);
		
		// verifies that the job is of the right type
		$jobType = ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_UPDATE);
		if($dbBatchJob->getJobType() != $jobType)
			throw new KalturaAPIException(APIErrors::UPDATE_EXCLUSIVE_JOB_WRONG_TYPE, $id, serialize($lockKey), serialize($job));
	
		$dbBatchJob = kBatchManager::updateExclusiveBatchJob($id, $lockKey->toObject(), $job->toObject($dbBatchJob));
				
		$batchJob = new KalturaBatchJob(); // start from blank
		return $batchJob->fromObject($dbBatchJob);
	}

	
	/**
	 * batch freeExclusiveDistributionUpdateJob action frees a BatchJob of type DistributionUpdate that was claimed using the getExclusiveDistributionUpdateJobs
	 * 
	 * @action freeExclusiveDistributionUpdateJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism
	 * @param bool $resetExecutionAttempts Resets the job execution attampts to zero  
	 * @return KalturaFreeJobResponse 
	 */
	function freeExclusiveDistributionUpdateJobAction($id ,KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		$jobType = ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_UPDATE);
		return $this->freeExclusiveJobAction($id ,$lockKey, $jobType, $resetExecutionAttempts);
	}
	
	
	/**
	 * batch getExclusiveAlmostDoneDistributionUpdateJobs action allows to get a BatchJob of type DISTRIBUTION_UPDATE that wait for remote closure 
	 * 
	 * @action getExclusiveAlmostDoneDistributionUpdateJobs
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param int $maxExecutionTime The maximum time in seconds the job reguarly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return. 
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only rartial list of jobs  
	 * @return KalturaBatchJobArray 
	 */
	function getExclusiveAlmostDoneDistributionUpdateJobsAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$jobType = ContentDistributionPlugin::getApiValue(ContentDistributionBatchJobType::DISTRIBUTION_UPDATE);
		return $this->getExclusiveAlmostDoneAction($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $jobType);
	}
	
// --------------------------------- DistributionUpdateJob functions 	--------------------------------- //
	
// --------------------------------- DistributionDeleteJob functions 	--------------------------------- //
	
	/**
	 * batch getExclusiveDistributionDeleteJob action allows to get a BatchJob of type DISTRIBUTION_DELETE 
	 * 
	 * @action getExclusiveDistributionDeleteJobs
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param int $maxExecutionTime The maximum time in seconds the job reguarly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return. 
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only rartial list of jobs  
	 * @return KalturaBatchJobArray 
	 */
	function getExclusiveDistributionDeleteJobsAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$jobType = ContentDistributionPlugin::getApiValue(ContentDistributionBatchJobType::DISTRIBUTION_DELETE);
		return $this->getExclusiveJobsAction($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $jobType);
	}

	
	/**
	 * batch updateExclusiveDistributionDeleteJob action updates a BatchJob of type DISTRIBUTION_DELETE that was claimed using the getExclusiveDistributionDeleteJobs
	 * 
	 * @action updateExclusiveDistributionDeleteJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param KalturaBatchJob $job
	 * @return KalturaBatchJob 
	 */
	function updateExclusiveDistributionDeleteJobAction($id ,KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job)
	{
		$dbBatchJob = BatchJobPeer::retrieveByPK($id);
		
		// verifies that the job is of the right type
		$jobType = ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_DELETE);
		if($dbBatchJob->getJobType() != $jobType)
			throw new KalturaAPIException(APIErrors::UPDATE_EXCLUSIVE_JOB_WRONG_TYPE, $id, serialize($lockKey), serialize($job));
	
		$dbBatchJob = kBatchManager::updateExclusiveBatchJob($id, $lockKey->toObject(), $job->toObject($dbBatchJob));
				
		$batchJob = new KalturaBatchJob(); // start from blank
		return $batchJob->fromObject($dbBatchJob);
	}

	
	/**
	 * batch freeExclusiveDistributionDeleteJob action frees a BatchJob of type DistributionDelete that was claimed using the getExclusiveDistributionDeleteJobs
	 * 
	 * @action freeExclusiveDistributionDeleteJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism
	 * @param bool $resetExecutionAttempts Resets the job execution attampts to zero  
	 * @return KalturaFreeJobResponse 
	 */
	function freeExclusiveDistributionDeleteJobAction($id ,KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		$jobType = ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_DELETE);
		return $this->freeExclusiveJobAction($id ,$lockKey, $jobType, $resetExecutionAttempts);
	}
	
	
	/**
	 * batch getExclusiveAlmostDoneDistributionDeleteJobs action allows to get a BatchJob of type DISTRIBUTION_DELETE that wait for remote closure 
	 * 
	 * @action getExclusiveAlmostDoneDistributionDeleteJobs
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param int $maxExecutionTime The maximum time in seconds the job reguarly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return. 
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only rartial list of jobs  
	 * @return KalturaBatchJobArray 
	 */
	function getExclusiveAlmostDoneDistributionDeleteJobsAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$jobType = ContentDistributionPlugin::getApiValue(ContentDistributionBatchJobType::DISTRIBUTION_DELETE);
		return $this->getExclusiveAlmostDoneAction($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $jobType);
	}
	
// --------------------------------- DistributionDeleteJob functions 	--------------------------------- //
	
// --------------------------------- DistributionFetchReportJob functions 	--------------------------------- //
	
	/**
	 * batch getExclusiveDistributionFetchReportJob action allows to get a BatchJob of type DISTRIBUTION_FETCH_REPORT 
	 * 
	 * @action getExclusiveDistributionFetchReportJobs
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param int $maxExecutionTime The maximum time in seconds the job reguarly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return. 
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only rartial list of jobs  
	 * @return KalturaBatchJobArray 
	 */
	function getExclusiveDistributionFetchReportJobsAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$jobType = ContentDistributionPlugin::getApiValue(ContentDistributionBatchJobType::DISTRIBUTION_FETCH_REPORT);
		return $this->getExclusiveJobsAction($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $jobType);
	}

	
	/**
	 * batch updateExclusiveDistributionFetchReportJob action updates a BatchJob of type DISTRIBUTION_FETCH_REPORT that was claimed using the getExclusiveDistributionFetchReportJobs
	 * 
	 * @action updateExclusiveDistributionFetchReportJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param KalturaBatchJob $job
	 * @return KalturaBatchJob 
	 */
	function updateExclusiveDistributionFetchReportJobAction($id ,KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job)
	{
		$dbBatchJob = BatchJobPeer::retrieveByPK($id);
		
		// verifies that the job is of the right type
		$jobType = ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_FETCH_REPORT);
		if($dbBatchJob->getJobType() != $jobType)
			throw new KalturaAPIException(APIErrors::UPDATE_EXCLUSIVE_JOB_WRONG_TYPE, $id, serialize($lockKey), serialize($job));
	
		$dbBatchJob = kBatchManager::updateExclusiveBatchJob($id, $lockKey->toObject(), $job->toObject($dbBatchJob));
				
		$batchJob = new KalturaBatchJob(); // start from blank
		return $batchJob->fromObject($dbBatchJob);
	}

	
	/**
	 * batch freeExclusiveDistributionFetchReportJob action frees a BatchJob of type DistributionFetchReport that was claimed using the getExclusiveDistributionFetchReportJobs
	 * 
	 * @action freeExclusiveDistributionFetchReportJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism
	 * @param bool $resetExecutionAttempts Resets the job execution attampts to zero  
	 * @return KalturaFreeJobResponse 
	 */
	function freeExclusiveDistributionFetchReportJobAction($id ,KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		$jobType = ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_FETCH_REPORT);
		return $this->freeExclusiveJobAction($id ,$lockKey, $jobType, $resetExecutionAttempts);
	}
	
	
	/**
	 * batch getExclusiveAlmostDoneDistributionFetchReportJobs action allows to get a BatchJob of type DISTRIBUTION_FETCH_REPORT that wait for remote closure 
	 * 
	 * @action getExclusiveAlmostDoneDistributionFetchReportJobs
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param int $maxExecutionTime The maximum time in seconds the job reguarly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return. 
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only rartial list of jobs  
	 * @return KalturaBatchJobArray 
	 */
	function getExclusiveAlmostDoneDistributionFetchReportJobsAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$jobType = ContentDistributionPlugin::getApiValue(ContentDistributionBatchJobType::DISTRIBUTION_FETCH_REPORT);
		return $this->getExclusiveAlmostDoneAction($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $jobType);
	}
	
// --------------------------------- DistributionFetchReportJob functions 	--------------------------------- //

// --------------------------------- Distribution Synchronizer functions 	--------------------------------- //


	
	/**
	 * creates all required jobs according to entry distribution dirty flags 
	 * 
	 * @action createRequiredJobs
	 */
	function createRequiredJobsAction()
	{
		// TODO read from sphinx the dirty records and create jobs
		
		$criteria = KalturaCriteria::create(EntryDistributionPeer::OM_CLASS);
		$criteria->add(EntryDistributionPeer::NEXT_REPORT, time(), Criteria::GREATER_EQUAL);
		$entryDistributions = EntryDistributionPeer::doSelect($criteria);
		
		foreach($entryDistributions as $entryDistribution)
		{
			$distributionProfile = DistributionProfilePeer::retrieveByPK($entryDistribution->getDistributionProfileId());
			if($distributionProfile)
				kContentDistributionManager::submitFetchEntryDistributionReport($entryDistribution, $distributionProfile);
			else
				KalturaLog::err("Distribution profile [" . $entryDistribution->getDistributionProfileId() . "] not found for entry distribution [" . $entryDistribution->getId() . "]");
		}
	}
	
// --------------------------------- Distribution Synchronizer functions 	--------------------------------- //
	
}
