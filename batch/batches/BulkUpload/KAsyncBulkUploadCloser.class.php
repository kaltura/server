<?php
/**
 * @package Scheduler
 * @subpackage Bulk-Upload
 */

/**
 * Will close almost done bulk uploads.
 * The state machine of the job is as follows:
 * 	 	get almost done bulk uploads 
 * 		check the imports and converts statuses
 * 		update the bulk status
 *
 * @package Scheduler
 * @subpackage Bulk-Upload
 */
class KAsyncBulkUploadCloser extends KJobCloserWorker
{
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::BULKUPLOAD;
	}
	
	/* (non-PHPdoc)
	 * @see KBatchBase::getJobType()
	 */
	public function getJobType()
	{
		return self::getType();
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		return $this->fetchStatus($job);
	}
	
	private function fetchStatus(KalturaBatchJob $job)
	{
		KalturaLog::debug("fetchStatus($job->id)");
		
		if(($job->queueTime + $this->taskConfig->params->maxTimeBeforeFail) < time())
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::CLOSER_TIMEOUT, 'Timed out', KalturaBatchJobStatus::FAILED);
			
		$openedEntries = $this->kClient->batch->updateBulkUploadResults($job->id);
		if(!$openedEntries)
			return $this->closeJob($job, null, null, 'Finished successfully', KalturaBatchJobStatus::FINISHED);
			
		return $this->closeJob($job, null, null, null, KalturaBatchJobStatus::ALMOST_DONE);
	}
}
