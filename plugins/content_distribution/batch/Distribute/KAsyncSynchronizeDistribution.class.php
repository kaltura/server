<?php
require_once("bootstrap.php");
/**
 * Synchronize Distribution status and create delayed jobs
 *
 * @package plugins.contentDistribution 
 * @subpackage Scheduler.Distribute
 */
class KAsyncSynchronizeDistribution extends KBatchBase
{
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::DISTRIBUTION_SYNC;
	}
	
	/* (non-PHPdoc)
	 * @see KBatchBase::getJobType()
	 */
	public function getJobType()
	{
		return self::getType();
	}
	
	/* (non-PHPdoc)
	 * @see KBatchBase::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		return null;
	}
	
	// TODO remove run, updateExclusiveJob and freeExclusiveJob
	
	protected function init(){}
	
	/**
	 * @param int $jobId
	 * @param KalturaBatchJob $job
	 */
	protected function updateExclusiveJob($jobId, KalturaBatchJob $job){}
	
	/**
	 * @param KalturaBatchJob $job
	 */
	protected function freeExclusiveJob(KalturaBatchJob $job){}
	
	public function run()
	{
		KalturaLog::info("Synchronize distribution batch is running");
		
		$this->kClient->contentDistributionBatch->updateSunStatus();
		$this->kClient->contentDistributionBatch->createRequiredJobs();
	}
}
