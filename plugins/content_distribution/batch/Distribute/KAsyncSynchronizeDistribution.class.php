<?php
require_once("bootstrap.php");
/**
 * Synchronize Distribution status and create delayed jobs
 *
 * @package plugins.contentDistribution 
 * @subpackage Scheduler.Distribute
 */
class KAsyncSynchronizeDistribution extends KPeriodicWorker
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
	
	public function run()
	{
		KalturaLog::info("Synchronize distribution batch is running");
		
		$this->kClient->contentDistributionBatch->updateSunStatus();
		$this->kClient->contentDistributionBatch->createRequiredJobs();
	}
}
