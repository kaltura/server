<?php
/**
 * @package Scheduler
 * @subpackage Cleanup
 */
require_once("bootstrap.php");

/**
 * Will clean from the DB all locked jobs and will mark them as fatal if exeeded max retries
 *
 * @package Scheduler
 * @subpackage Cleanup
 */
class KAsyncDbCleanup extends KBatchBase
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
		return KalturaBatchJobType::CLEANUP;
	}
	
	/* (non-PHPdoc)
	 * @see KBatchBase::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		return null;
	}
	
	// TODO remove run, updateExclusiveJob and freeExclusiveJob
	
	protected function init()
	{
		
	}
	
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
		KalturaLog::info("DB cleanup batch is running");
		
		$this->kClient->batch->cleanExclusiveJobs();
	}
}
?>