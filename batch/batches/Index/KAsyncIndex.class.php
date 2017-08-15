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
		$engine = KIndexingEngine::getInstance($job->jobSubType);
		$engine->configure($job->partnerId);
		$advancedFilter = $engine->initAdvancedFilter($data);
		
		$filter = clone $data->filter;
		$filter->advancedSearch = $advancedFilter;

		$continue = true;
		while($continue)
		{
			$indexedObjectsCount = $engine->run($filter, $data->shouldUpdate);
			$continue = (bool) $indexedObjectsCount;
			$lastIndexId = $engine->getLastIndexId();
			$lastIndexDepth = $engine->getLastIndexDepth();
			
			$data->lastIndexId = $lastIndexId;
			$data->lastIndexDepth = $lastIndexDepth;
			$this->updateJob($job, "Indexed $indexedObjectsCount objects", KalturaBatchJobStatus::PROCESSING, $data);
			
			$advancedFilter->indexIdGreaterThan = $lastIndexId;
			$advancedFilter->depthGreaterThanEqual = $lastIndexDepth;
			$filter->advancedSearch = $advancedFilter;
		}
		
		return $this->closeJob($job, null, null, "Index objects finished", KalturaBatchJobStatus::FINISHED);
	}
}
