<?php
/**
 * @package Scheduler
 * @subpackage Bulk-Download
 */
require_once("bootstrap.php");


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
	
	public function run()
	{
		KalturaLog::debug("run()");
		KalturaLog::info("Bulk download closer batch is running");
		
		if($this->taskConfig->isInitOnly())
			return $this->init();
		
		$jobs = $this->kClient->batch->getExclusiveAlmostDoneBulkDownloadJobs( 
			$this->getExclusiveLockKey(), 
			$this->taskConfig->maximumExecutionTime, 
			1, 
			$this->getFilter());
			
		KalturaLog::info(count($jobs) . " bulk download jobs to close");
					
		if(!count($jobs))
		{
			KalturaLog::info("Queue size: 0 sent to scheduler");
			$this->saveSchedulerQueue(self::getType());
			return;
		}
		
		foreach($jobs as $job)
			$this->fetchStatus($job);
	}
	
	private function fetchStatus(KalturaBatchJob $job)
	{
		KalturaLog::debug("fetchStatus($job->id)");
		
		if(($job->queueTime + $this->taskConfig->params->maxTimeBeforeFail) < time())
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::CLOSER_TIMEOUT, 'Timed out', KalturaBatchJobStatus::FAILED);
		
		return $this->closeJob($job, null, null, null, KalturaBatchJobStatus::ALMOST_DONE);
	}
}
