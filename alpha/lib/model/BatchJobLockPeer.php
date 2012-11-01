<?php


/**
 * Subclass for performing query and update operations on the 'lock_batch_job' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class BatchJobLockPeer extends BaseBatchJobLockPeer {
	
	private static $LOCK_AFFECTED_BY_COLUMNS_NAMES = array(
			BatchJobPeer::STATUS,
			BatchJobPeer::LOCK_INFO,
			BatchJobPeer::EXECUTION_STATUS,
			BatchJobPeer::OBJECT_ID,
			BatchJobPeer::OBJECT_TYPE,
			BatchJobPeer::PRIORITY
	);
	
	const COUNT = 'COUNT(batch_job_lock.ID)';
	
	
	/**
	 * This function returns a list of all job statuses that still require scheduling
	 * and therefore a lock object should appear for them in the lock table.
	 */
	public static function getSchedulingRequiredStatusList()
	{
		return array(
			BatchJob::BATCHJOB_STATUS_PENDING,
			BatchJob::BATCHJOB_STATUS_QUEUED,
			BatchJob::BATCHJOB_STATUS_ALMOST_DONE,
			BatchJob::BATCHJOB_STATUS_RETRY,
			BatchJob::BATCHJOB_STATUS_PROCESSING,
			BatchJob::BATCHJOB_STATUS_PROCESSED,
			BatchJob::BATCHJOB_STATUS_MOVEFILE
		);
	}
	
	public static function getRetryInterval($job_type = null)
	{
		$jobCheckAgainTimeouts = kConf::get('job_retry_intervals');
		if(isset($jobCheckAgainTimeouts[$job_type]))
			return $jobCheckAgainTimeouts[$job_type];
			
		return kConf::get('default_job_retry_interval');
	}
	
	public static function getMaxExecutionAttempts($job_type = null)
	{
		$jobMaxExecutionAttempts = kConf::get('job_execution_attempt');
		if(isset($jobMaxExecutionAttempts[$job_type]))
			return $jobMaxExecutionAttempts[$job_type];
			
		return kConf::get('default_job_execution_attempt');
	}
	
	/**
	 * This function returns the 'prioritizers ratio' from the configuration files.
	 * This value is a number in range [0-100] that represents the percentage of the  
	 * times we will choose the Through-put prioritizer when we come to choose a prioritizer.
	 * f.i. if the value is 60 then in 60% of the cases we will choose the through-put prioritizer
	 * and in the rest 40% we will use the fairness prioritizer.
	 */
	public static function getPrioritizersRatio($job_type = null)
	{
		$jobRateBetweenSchedulers = kConf::get('prioritizers_ratio');
		if(isset($jobRateBetweenSchedulers[$job_type]))
			return $jobRateBetweenSchedulers[$job_type];
			
		return kConf::get('default_prioritizers_ratio');
	}
	
	/**
	 * This function returns the the maximal number of jobs of a given type a partner
	 * can execute by using the fairness scheduler.
	 * f.i if the value is 1 for type conversion, no partner can execute more than one conversion job.
	 */
	public static function getMaxJobsForPartner($job_type = null)
	{
		$maxJobsForPartner = kConf::get('max_jobs_for_partner');
		if(isset($maxJobsForPartner[$job_type]))
			return $maxJobsForPartner[$job_type];
			
		return kConf::get('default_max_job_for_partner');
	}
	
	
	public static function shouldCreateLockObject(BatchJob $batchJob, $isNew, PropelPDO $con = null) 
	{
		if($isNew)
			return true;
		
		// if the object is not a new object, a batch_job_lock object should exist.
		// an exception is retry request of an entry that was in closed status and we now restarted it. 
		if($batchJob->getStatus() != BatchJob::BATCHJOB_STATUS_RETRY)
			return false;
		
		$lockEntry = BatchJobLockPeer::retrieveByPK($batchJob->getId());
		if($lockEntry === null) {
			return true;
		}
		
		return false;
	}
	
	public static function shouldUpdateLockObject(BatchJob $batchJob, PropelPDO $con = null) 
	{
		
		if(!in_array($batchJob->getStatus(), BatchJobLockPeer::getSchedulingRequiredStatusList())) 
			return false;
		if ($batchJob->getBatchJobLock() === null) 
			return false;
		$result = array_intersect(self::$LOCK_AFFECTED_BY_COLUMNS_NAMES, $batchJob->getModifiedColumns());
		if (count($result) > 0) 
			return true;
		
		return false;
	}
	
	public static function createLockObject(BatchJob $batchJob, PropelPDO $con = null)
	{
		$batchJobLock = new BatchJobLock();
		$batchJobLock->setId($batchJob->getId());
		$batchJobLock->setBatchJob($batchJob);
		$batchJobLock->setEntryId($batchJob->getEntryId());
		$batchJobLock->setPartnerId($batchJob->getPartnerId());
		$batchJobLock->setDc($batchJob->getDc());
		$batchJobLock->setCreatedAt($batchJob->getCreatedAt());
		$batchJobLock->setJobType($batchJob->getJobType());
		$batchJobLock->setJobSubType($batchJob->getJobSubType());
		$batchJobLock->setPriority($batchJob->getPriority());
		$batchJobLock->setExecutionAttempts(0);
		
		self::commonLockObjectUpdate($batchJob, $batchJobLock);
		
		// Don't add save batch job lock, it's done automatically by the save of the batch job!
		return $batchJobLock;
	}
	
	public static function updateLockObject(BatchJob $batchJob, PropelPDO $con = null)
	{
		$batchJobLock = $batchJob->getBatchJobLock();
		if($batchJobLock === null) {
			KalturaLog::info("Lock object wasn't found for Batch Job " . $batchJob->getId());
			return;
		}
		
		self::commonLockObjectUpdate($batchJob, $batchJobLock);
		// Don't add save batch job lock, it's done automatically by the save of the batch job!
		
		$result = array_intersect(self::$LOCK_AFFECTED_BY_COLUMNS_NAMES, $batchJob->getModifiedColumns());
		if (count($result) > 0) {
			$version = $batchJobLock->getVersion() + 1;
			// update
			$batchJobLock->setVersion($version);
			// update lock info
			$lockInfo = $batchJob->getLockInfo();
			$lockInfo->setLockVersion($version);
			$batchJob->setLockInfo($lockInfo);
		}
	}
	
	private static function commonLockObjectUpdate(BatchJob $batchJob, BatchJobLock $batchJobLock) {
		
		$jobType = $batchJob->getJobType();
		
		$batchJobLock->setStatus($batchJob->getStatus());
		$batchJobLock->setObjectId($batchJob->getObjectId());
		$batchJobLock->setObjectType($batchJob->getObjectType());
		$batchJobLock->setPriority($batchJob->getPriority());
		
		if(($batchJob->getStatus() == BatchJob::BATCHJOB_STATUS_RETRY) || ($batchJob->getStatus() == BatchJob::BATCHJOB_STATUS_ALMOST_DONE)) {
			$batchJobLock->setStartAt(time() + BatchJobLockPeer::getRetryInterval($jobType));
		}
		
		if($batchJob->getLockInfo() != null) {
			$batchJobLock->setUrgency($batchJob->getLockInfo()->getUrgency());
			$batchJobLock->setEstimatedEffort($batchJob->getLockInfo()->getEstimatedEffort());
			$batchJobLock->setVersion($batchJob->getLockInfo()->getLockVersion());
		}
	}
	
	
} // BatchJobLockPeer
