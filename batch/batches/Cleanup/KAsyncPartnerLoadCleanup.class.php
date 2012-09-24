<?php
/**
 * @package Scheduler
 * @subpackage Cleanup
 */

/**
 * Will clean from the the partner load table all the values according to the actual partner load
 *
 * @package Scheduler
 * @subpackage Cleanup
 */
class KAsyncPartnerLoadCleanup extends KPeriodicWorker
{
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::CLEANUP;
	}
	
	/* (non-PHPdoc)
	 * @see KBatchBase::getJobType()
	 */
	public function getJobType()
	{
		return self::getType();
	}
	
	/* (non-PHPdoc)
	 * @see KBatchBase::run()
	*/
	public function run($jobs = null)
	{
		KalturaLog::info("Partner load cleanup batch is running");
		$this->kClient->batch->updatePartnerLoadTable();
	}
}
