<?php
/**
 * @package Scheduler
 * @subpackage Cleanup
 */

/**
 * Will balance the jobs queue
 *
 * @package Scheduler
 * @subpackage Cleanup
 */
class KAsyncJobSuspender extends KPeriodicWorker
{
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::CLEANUP;
	}
	
	/* (non-PHPdoc)
	 * @see KBatchBase::run()
	*/
	public function run($jobs = null)
	{
		KalturaLog::info("Suspender batch is running");
		self::$kClient->batch->suspendJobs();
	}
}
