<?php
require_once("bootstrap.php");
/**
 * Synchronize Distribution status and create delayed jobs
 *
 * @package Scheduler
 * @subpackage Distribute
 */
class KAsyncSynchronizeDistribution extends KBatchBase
{
	/**
	 * @return number
	 */
	public static function getType()
	{
		return KalturaBatchJobType::DISTRIBUTION_SYNC;
	}
	
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
