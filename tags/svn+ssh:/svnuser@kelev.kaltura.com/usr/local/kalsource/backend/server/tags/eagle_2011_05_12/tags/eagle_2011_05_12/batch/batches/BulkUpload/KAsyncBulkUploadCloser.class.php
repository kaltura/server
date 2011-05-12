<?php
/**
 * @package Scheduler
 * @subpackage Bulk-Upload
 */
require_once("bootstrap.php");


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
class KAsyncBulkUploadCloser extends KBatchBase
{
	/**
	 * @return number
	 */
	public static function getType()
	{
		return KalturaBatchJobType::BULKUPLOAD;
	}
	
	protected function init()
	{
		$this->saveQueueFilter(self::getType(), true);
	}
	
	public function run()
	{
		KalturaLog::debug("run()");
		KalturaLog::info("Bulk upload closer batch is running");
		
		if($this->taskConfig->isInitOnly())
			return $this->init();
		
		$jobs = $this->kClient->batch->getExclusiveAlmostDoneBulkUploadJobs( 
			$this->getExclusiveLockKey() , 
			$this->taskConfig->maximumExecutionTime , 
			$this->taskConfig->maxJobsEachRun , 
			$this->getFilter());
			
		KalturaLog::info(count($jobs) . " bulk upload jobs to close");
					
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
			
		$openedEntries = $this->kClient->batch->updateBulkUploadResults($job->id);
		if(!$openedEntries)
			return $this->closeJob($job, null, null, 'Finished successfully', KalturaBatchJobStatus::FINISHED);
			
		return $this->closeJob($job, null, null, null, KalturaBatchJobStatus::ALMOST_DONE);
	}
	
	protected function updateExclusiveJob($jobId, KalturaBatchJob $job)
	{
		return $this->kClient->batch->updateExclusiveBulkUploadJob($jobId, $this->getExclusiveLockKey(), $job);
	}
	
	protected function freeExclusiveJob(KalturaBatchJob $job)
	{
		$resetExecutionAttempts = false;
		if($job->status == KalturaBatchJobStatus::ALMOST_DONE)
			$resetExecutionAttempts = true;
			
		$response = $this->kClient->batch->freeExclusiveBulkUploadJob($job->id, $this->getExclusiveLockKey(), $resetExecutionAttempts);
		
		KalturaLog::info("Queue size: $response->queueSize sent to scheduler");
		$this->saveSchedulerQueue(self::getType(), $response->queueSize);
		
		return $response->job;
	}
}
