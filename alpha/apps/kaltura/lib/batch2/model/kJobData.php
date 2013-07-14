<?php
/**
 * @package Core
 * @subpackage model.data
 */
abstract class kJobData
{
	const MAX_ESTIMATED_EFFORT = 1099511627776; // 1 TB
	const BATCH_JOB_DEFAULT_PRIORITY = 3;
	
	/**
	 * This function returns the estimated effort according to the job data
	 * @param BatchJob $batchJob
	 * @return integer estimated effort
	 */
	public function calculateEstimatedEffort(BatchJob $batchJob) {
		return null;
	}
	
	/**
	 * This function calculates the urgency of the job according to its data
	 * @param BatchJob $batchJob
	 * @return integer the calculated urgency
	 */
	public function calculateUrgency(BatchJob $batchJob) {
		return ($batchJob->getBulkJobId() === NULL) ? BatchJobUrgencyType::REQUIRED_REGULAR_UPLOAD : BatchJobUrgencyType::REQUIRED_BULK_UPLOAD;
	}
	
	/**
	 * This function calculates the priority of the job.
	 * @param BatchJob $batchJob
	 * @return integer the calculated priority
	 */
	public function calculatePriority(BatchJob $batchJob) {
		$parentJob = $batchJob->getParentJob();
		if(!is_null($parentJob) && !is_null($parentJob->getLockInfo())) {
			return $parentJob->getLockInfo()->getPriority();
		}
		return self::BATCH_JOB_DEFAULT_PRIORITY;
	}
}
