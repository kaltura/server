<?php
require_once("bootstrap.php");
/**
 * Distributes kaltura entries to remote destination  
 *
 * @package Scheduler
 * @subpackage Distribute
 */
class KAsyncDistributeDelete extends KAsyncDistribute
{
	/**
	 * @return number
	 */
	public static function getType()
	{
		return KalturaBatchJobType::DISTRIBUTION_DELETE;
	}
	
	/* (non-PHPdoc)
	 * @see KBatchBase::init()
	 */
	protected function init()
	{
		$this->saveQueueFilter(self::getType());
	}
	
	/* (non-PHPdoc)
	 * @see KAsyncDistribute::getExclusiveDistributeJobs()
	 */
	public function getExclusiveDistributeJobs()
	{
		return $this->kClient->contentDistributionBatch->getExclusiveDistributionDeleteJobs($this->getExclusiveLockKey(), $this->taskConfig->maximumExecutionTime, $this->taskConfig->maxJobsEachRun, $this->getFilter());
	}
	
	/* (non-PHPdoc)
	 * @see KBatchBase::updateExclusiveJob()
	 */
	protected function updateExclusiveJob($jobId, KalturaBatchJob $job, $entryStatus = null)
	{
		return $this->kClient->contentDistributionBatch->updateExclusiveDistributionDeleteJob($jobId, $this->getExclusiveLockKey(), $job, $entryStatus);
	}
	
	/* (non-PHPdoc)
	 * @see KBatchBase::freeExclusiveJob()
	 */
	protected function freeExclusiveJob(KalturaBatchJob $job)
	{
		$response = $this->kClient->contentDistributionBatch->freeExclusiveDistributionDeleteJob($job->id, $this->getExclusiveLockKey(), false);
		
		KalturaLog::info("Queue size: $response->queueSize sent to scheduler");
		$this->saveSchedulerQueue(self::getType(), $response->queueSize);
		
		return $response->job;
	}
	
	/* (non-PHPdoc)
	 * @see KAsyncDistribute::getDistributionEngine()
	 */
	protected function getDistributionEngine($providerType)
	{
		return DistributionEngine::getEngine('IDistributionEngineDelete', $providerType);
	}
	
	/* (non-PHPdoc)
	 * @see KAsyncDistribute::execute()
	 */
	protected function execute(KalturaDistributionJobData $data)
	{
		return $this->engine->delete($data);
	}
}
