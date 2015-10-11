<?php
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
	 * @see KBatchBase::run()
	*/
	public function run($jobs = null)
	{
		self::$kClient->contentDistributionBatch->updateSunStatus();
		self::$kClient->contentDistributionBatch->createRequiredJobs();
	}
}
