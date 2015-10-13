<?php
/**
 * @package Scheduler
 * @subpackage Copy
 */

/**
 * Will copy objects and add them
 * according to the suppolied engine type and filter 
 *
 * @package Scheduler
 * @subpackage Copy
 */
class KAsyncCopy extends KJobHandlerWorker
{
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::COPY;
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		return $this->copyObjects($job, $job->data);
	}
	
	/**
	 * Will take a single filter and call each item to be Copied 
	 */
	private function copyObjects(KalturaBatchJob $job, KalturaCopyJobData $data)
	{
		$engine = KCopyingEngine::getInstance($job->jobSubType);
		$engine->configure($job->partnerId);
	
		$filter = clone $data->filter;
		$advancedFilter = new KalturaIndexAdvancedFilter();
		
		if($data->lastCopyId)
		{
			
			$advancedFilter->indexIdGreaterThan = $data->lastCopyId;
			$filter->advancedSearch = $advancedFilter;
		}
		
		$continue = true;
		while($continue)
		{
			$copiedObjectsCount = $engine->run($filter, $data->templateObject);
			$continue = (bool) $copiedObjectsCount;
			$lastCopyId = $engine->getLastCopyId();
			
			$data->lastCopyId = $lastCopyId;
			$this->updateJob($job, "Copied $copiedObjectsCount objects", KalturaBatchJobStatus::PROCESSING, $data);
			
			$advancedFilter->indexIdGreaterThan = $lastCopyId;
			$filter->advancedSearch = $advancedFilter;
		}
		
		return $this->closeJob($job, null, null, "Copy objects finished", KalturaBatchJobStatus::FINISHED);
	}
}
