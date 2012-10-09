<?php
/**
 * @package Core
 * @subpackage model.data
 */
abstract class kJobData
{
	const MAX_ESTIMATED_EFFORT = 99999999;
	
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
}
