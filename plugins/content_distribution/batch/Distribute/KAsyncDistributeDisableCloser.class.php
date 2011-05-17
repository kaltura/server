<?php
require_once("bootstrap.php");
/**
 * Distributes kaltura entries to remote destination  
 *
 * @package plugins.contentDistribution 
 * @subpackage Scheduler.Distribute
 */
class KAsyncDistributeDisableCloser extends KAsyncDistributeCloser
{
	/**
	 * @return number
	 */
	public static function getType()
	{
		return KalturaBatchJobType::DISTRIBUTION_DISABLE;
	}
	
	/* (non-PHPdoc)
	 * @see KBatchBase::init()
	 */
	protected function init()
	{
		$this->saveQueueFilter(self::getType());
	}
	
	/* (non-PHPdoc)
	 * @see KAsyncDistribute::saveEmptyQueue()
	 */
	protected function saveEmptyQueue()
	{
		$this->saveSchedulerQueue(self::getType());
	}
	
	/* (non-PHPdoc)
	 * @see KAsyncDistributeCloser::getExclusiveAlmostDoneDistributeJobs()
	 */
	public function getExclusiveAlmostDoneDistributeJobs()
	{
		return $this->kClient->contentDistributionBatch->getExclusiveAlmostDoneDistributionDisableJobs($this->getExclusiveLockKey(), $this->taskConfig->maximumExecutionTime, $this->taskConfig->maxJobsEachRun, $this->getFilter());
	}
	
	/* (non-PHPdoc)
	 * @see KBatchBase::updateExclusiveJob()
	 */
	protected function updateExclusiveJob($jobId, KalturaBatchJob $job, $entryStatus = null)
	{
		return $this->kClient->contentDistributionBatch->updateExclusiveDistributionDisableJob($jobId, $this->getExclusiveLockKey(), $job, $entryStatus);
	}
	
	/* (non-PHPdoc)
	 * @see KBatchBase::freeExclusiveJob()
	 */
	protected function freeExclusiveJob(KalturaBatchJob $job)
	{
		$resetExecutionAttempts = false;
		if($job->status == KalturaBatchJobStatus::ALMOST_DONE)
			$resetExecutionAttempts = true;
	
		$response = $this->kClient->contentDistributionBatch->freeExclusiveDistributionDisableJob($job->id, $this->getExclusiveLockKey(), $resetExecutionAttempts);
		
		KalturaLog::info("Queue size: $response->queueSize sent to scheduler");
		$this->saveSchedulerQueue(self::getType(), $response->queueSize);
		
		return $response->job;
	}
	
	/* (non-PHPdoc)
	 * @see KAsyncDistribute::getDistributionEngine()
	 */
	protected function getDistributionEngine($providerType, KalturaDistributionJobData $data)
	{
		return DistributionEngine::getEngine('IDistributionEngineCloseUpdate', $providerType, $this->getClient(), $this->taskConfig, $data);
	}
	
	/* (non-PHPdoc)
	 * @see KAsyncDistribute::execute()
	 */
	protected function execute(KalturaDistributionJobData $data)
	{
		return $this->engine->closeUpdate($data);
	}
}
