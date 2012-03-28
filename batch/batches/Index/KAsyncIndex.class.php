<?php
/**
 * @package Scheduler
 * @subpackage Index
 */

/**
 * Will index objects in the indexing server
 * according to the suppolied engine type and filter 
 *
 * @package Scheduler
 * @subpackage Index
 */
class KAsyncIndex extends KJobHandlerWorker
{
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::INDEX;
	}
	
	/* (non-PHPdoc)
	 * @see KBatchBase::getJobType()
	 */
	public function getJobType()
	{
		return self::getType();
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		return $this->indexObjects($job, $job->data);
	}
	
	/**
	 * Will take a single filter and call each item to be indexed 
	 */
	private function indexObjects(KalturaBatchJob $job, KalturaIndexJobData $data)
	{
		KalturaLog::debug("Index objects job id [$job->id]");
		
		$engine = KIndexingEngine::getInstance($objectType);
		
		return $job;
	}
}
