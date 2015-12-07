<?php
/**
 * @package Scheduler
 * @subpackage Cleanup
 */

/**
 * Will clean from the DB all locked jobs and will mark them as fatal if exeeded max retries
 *
 * @package Scheduler
 * @subpackage Cleanup
 */
class KAsyncDbCleanup extends KPeriodicWorker
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
		self::$kClient->batch->cleanExclusiveJobs();
	}
}
