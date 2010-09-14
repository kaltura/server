<?php
require_once("bootstrap.php");
/**
 * Will clean from the DB all locked jobs and will mark them as fatal if exeeded max retries
 *
 * @package Scheduler
 * @subpackage Cleanup
 */
class KAsyncDbCleanup extends KBatchBase
{
	/**
	 * @return number
	 */
	public static function getType()
	{
		return KalturaBatchJobType::CLEANUP;
	}
	
	protected function init()
	{
		
	}
	
	/**
	 * @param int $jobId
	 * @param KalturaBatchJob $job
	 * @param int $entryStatus
	 */
	protected function updateExclusiveJob($jobId, KalturaBatchJob $job, $entryStatus = null){}
	
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