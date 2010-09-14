<?php
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
class KAsyncBulkDownloadCloser extends KBatchBase
{
	/**
	 * @return number
	 */
	public static function getType()
	{
		return KalturaBatchJobType::BULKDOWNLOAD;
	}
	
	protected function init()
	{
		$this->saveQueueFilter(self::getType(), true); 
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
			$this->saveSchedulerQueue(self::getType(), null, true);
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
	
	protected function updateExclusiveJob($jobId, KalturaBatchJob $job, $entryStatus = null)
	{
		return $this->kClient->batch->updateExclusiveBulkDownloadJob($jobId, $this->getExclusiveLockKey(), $job);
	}
	
	protected function freeExclusiveJob(KalturaBatchJob $job)
	{
		$resetExecutionAttempts = false;
		if($job->status == KalturaBatchJobStatus::ALMOST_DONE)
			$resetExecutionAttempts = true;
			
		$response = $this->kClient->batch->freeExclusiveBulkDownloadJob($job->id, $this->getExclusiveLockKey(), $resetExecutionAttempts);
		
		KalturaLog::info("Queue size: $response->queueSize sent to scheduler");
		$this->saveSchedulerQueue(self::getType(), $response->queueSize);
		
		return $response->job;
	}
}

?>