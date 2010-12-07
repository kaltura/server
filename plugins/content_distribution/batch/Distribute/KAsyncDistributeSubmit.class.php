<?php
require_once("bootstrap.php");
/**
 * Distributes kaltura entries to remote destination  
 *
 * @package Scheduler
 * @subpackage Distribute
 */
class KAsyncDistributeSubmit extends KAsyncDistribute
{
	/**
	 * @return number
	 */
	public static function getType()
	{
		// TODO
		return KalturaBatchJobType::VIRUS_SCAN;
	}
	
	protected function init()
	{
		$this->saveQueueFilter(self::getType());
	}
	
	public function getExclusiveDistributeJobs()
	{
		return $this->kClient->contentDistributionBatch->getExclusiveDistributionSubmitJobs($this->getExclusiveLockKey(), $this->taskConfig->maximumExecutionTime, $this->taskConfig->maxJobsEachRun, $this->getFilter());
	}
	
	protected function updateExclusiveJob($jobId, KalturaBatchJob $job, $entryStatus = null)
	{
		return $this->kClient->contentDistributionBatch->updateExclusiveDistributionSubmitJob($jobId, $this->getExclusiveLockKey(), $job, $entryStatus);
	}
	
	protected function freeExclusiveJob(KalturaBatchJob $job)
	{
		$response = $this->kClient->contentDistributionBatch->freeExclusiveDistributionSubmitJob($job->id, $this->getExclusiveLockKey(), false);
		
		KalturaLog::info("Queue size: $response->queueSize sent to scheduler");
		$this->saveSchedulerQueue(self::getType(), $response->queueSize);
		
		return $response->job;
	}
}
