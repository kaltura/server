<?php
/**
 * @package Scheduler
 * @subpackage MoveCategoryEntries
 */

/**
 * Will sync category privacy context on category entries and entries
 *
 * @package Scheduler
 * @subpackage SyncCategoryPrivacyContext
 */
class KAsyncSyncCategoryPrivacyContext extends KJobHandlerWorker
{
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::SYNC_CATEGORY_PRIVACY_CONTEXT;
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		return $this->syncPrivacyContext($job, $job->data);
	}
	
	/**
	 * sync category privacy context on category entries and entries
	 * 
	 * @param KalturaBatchJob $job
	 * @param KalturaSyncCategoryPrivacyContextJobData $data
	 * 
	 * @return KalturaBatchJob
	 */
	protected function syncPrivacyContext(KalturaBatchJob $job, KalturaSyncCategoryPrivacyContextJobData $data)
	{
		KalturaLog::debug("Sync category entries and entries for category [$data->categoryId]");
		
	    KBatchBase::impersonate($job->partnerId);
	    
	    $categoryEntryFilter = new KalturaCategoryEntryFilter();
		$categoryEntryFilter->orderBy = KalturaCategoryEntryOrderBy::CREATED_AT_ASC;
		$categoryEntryFilter->categoryIdEqual = $data->categoryId;
		if($data->lastUpdatedCategoryEntryCreatedAt)
			$categoryEntryFilter->$createdAtGreaterThanOrEqual = $data->lastUpdatedCategoryEntryCreatedAt;
	    
		$categoryEntryPager = new KalturaFilterPager();
		$categoryEntryPager->pageSize = 100;
		if(KBatchBase::$taskConfig->params->pageSize)
			$categoryEntryPager->pageSize = KBatchBase::$taskConfig->params->pageSize;
		
		$categoryEntryList = KBatchBase::$kClient->categoryEntry->listAction($categoryEntryFilter, $categoryEntryPager);
		while(count($categoryEntryList->objects))
		{
			KBatchBase::$kClient->startMultiRequest();
			foreach ($categoryEntryList->objects as $categoryEntry) 
			{
				KalturaLog::debug('entryId '.$categoryEntry->entryId.' categoryId '.$categoryEntry->categoryId);
				KBatchBase::$kClient->categoryEntry->syncPrivacyContext($categoryEntry->entryId, $categoryEntry->categoryId);				
			}

			KBatchBase::$kClient->doMultiRequest();	
			$data->lastUpdatedCategoryEntryCreatedAt = $categoryEntry->createdAt;
			$categoryEntryPager->pageIndex++;
			
			KBatchBase::unimpersonate();
			$this->updateJob($job, null, KalturaBatchJobStatus::PROCESSING, $data);
			KBatchBase::impersonate($job->partnerId);
							
			$categoryEntryList = KBatchBase::$kClient->categoryEntry->listAction($categoryEntryFilter, $categoryEntryPager);
		}
		
		KBatchBase::unimpersonate();
		$job = $this->closeJob($job, null, null, null, KalturaBatchJobStatus::FINISHED);
		
		return $job;
	}
}
