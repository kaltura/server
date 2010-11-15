<?php
require_once("bootstrap.php");
/**
 * Will scan for viruses on specified file  
 *
 * @package Scheduler
 * @subpackage VirusScan
 */
class KAsyncVirusScan extends KBatchBase
{
	/**
	 * @return number
	 */
	public static function getType()
	{
		return KalturaBatchJobType::VIRUS_SCAN;
	}
	
	protected function init()
	{
		$this->saveQueueFilter(self::getType());
	}
	
	public function run($jobs = null)
	{
		KalturaLog::info("Virus scan batch is running");
		
		if($this->taskConfig->isInitOnly())
			return $this->init();
		
		if(is_null($jobs))
			$jobs = $this->kClient->virusScanBatch->getExclusiveVirusScanJobs($this->getExclusiveLockKey(), $this->taskConfig->maximumExecutionTime, 1, $this->getFilter());
		
		KalturaLog::info(count($jobs) . " virus scan jobs to perform");
		
		if(! count($jobs) > 0)
		{
			KalturaLog::info("Queue size: 0 sent to scheduler");
			$this->saveSchedulerQueue(self::getType());
			return null;
		}
		
		foreach($jobs as &$job)
			$job = $this->scan($job, $job->data);
			
		return $jobs;
	}
	
	protected function scan(KalturaVirusScanBatchJob $job, KalturaVirusScanJobData $data)
	{
		KalturaLog::debug("scan($job->id)");
		
		try
		{
			// TODO
		}
		catch(Exception $ex)
		{
			$this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $ex->getCode(), "Error: " . $ex->getMessage(), KalturaBatchJobStatus::FAILED);
		}
		return $job;
	}
	
	/**
	 * @return KalturaBatchJob
	 */
	protected function newEmptyJob()
	{
		return new KalturaVirusScanBatchJob();
	}
	
	protected function updateExclusiveJob($jobId, KalturaBatchJob $job, $entryStatus = null)
	{
		return $this->kClient->virusScanBatch->updateExclusiveVirusScanJob($jobId, $this->getExclusiveLockKey(), $job, $entryStatus);
	}
	
	protected function freeExclusiveJob(KalturaBatchJob $job)
	{
		$response = $this->kClient->virusScanBatch->freeExclusiveVirusScanJob($job->id, $this->getExclusiveLockKey(), false);
		
		KalturaLog::info("Queue size: $response->queueSize sent to scheduler");
		$this->saveSchedulerQueue(self::getType(), $response->queueSize);
		
		return $response->job;
	}
}
?>