<?php
/**
 * @package Scheduler
 * @subpackage Bulk-Download
 */

/**
 * Will close almost done bulk downloads.
 * The state machine of the job is as follows:
 * 	 	get almost done bulk downloads 
 * 		check converts statuses
 * 		update the bulk status
 *
 * @package Scheduler
 * @subpackage Bulk-Download
 */
class KAsyncBulkDownloadCloser extends KJobCloserWorker
{
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::BULKDOWNLOAD;
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
		if(($job->queueTime + KBatchBase::$taskConfig->params->maxTimeBeforeFail) < time())
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::CLOSER_TIMEOUT, 'Timed out', KalturaBatchJobStatus::FAILED);
		
		return $this->closeJob($job, null, null, null, KalturaBatchJobStatus::ALMOST_DONE);
	}
}
