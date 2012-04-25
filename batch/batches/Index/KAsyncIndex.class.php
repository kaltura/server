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
		
		$engine = KIndexingEngine::getInstance($job->jobSubType);
		$engine->configure($job->partnerId, $this->kClient, $this->taskConfig);
	
		$filter = clone $data->filter;
		$advancedFilter = new KalturaIndexAdvancedFilter();
		
		if($data->lastIndexId)
		{
			
			$advancedFilter->indexIdGreaterThan = $data->lastIndexId;
			$filter->advancedSearch = $advancedFilter;
		}
		
		$continue = true;
		while($continue)
		{
			$indexedObjectsCount = $engine->run($filter, $data->shouldUpdate);
			$continue = (bool) $indexedObjectsCount;
			$lastIndexId = $engine->getLastIndexId();
			
			$data->lastIndexId = $lastIndexId;
			$this->updateJob($job, "Indexed $indexedObjectsCount objects", KalturaBatchJobStatus::PROCESSING, null, $data);
			
			$advancedFilter->indexIdGreaterThan = $lastIndexId;
			$filter->advancedSearch = $advancedFilter;
		}
		
		return $this->closeJob($job, null, null, "Index objects finished", KalturaBatchJobStatus::FINISHED);
	}
}
