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
	    
	    if(!$data->lastUpdatedCategoryCreatedAt)
	    {
	    	//sync root category
	    	$this->syncCategoryPrivacyContext($job, $data, $data->categoryId);
	    }
	    
	    //sync sub categories	    
	    $categoryFilter = new KalturaCategoryFilter();
		$categoryFilter->orderBy = KalturaCategoryOrderBy::CREATED_AT_ASC;
		$categoryFilter->ancestorIdIn = $data->categoryId;
		if($data->lastUpdatedCategoryCreatedAt)
			$categoryFilter->$createdAtGreaterThanOrEqual = $data->lastUpdatedCategoryCreatedAt;	    
		$pager = $this->getFilterPager();		
		$categoryList = KBatchBase::$kClient->category->listAction($categoryFilter, $pager);
	    
		while(count($categoryList->objects))
		{
			foreach ($categoryList->objects as $category) 
			{
				$data->lastUpdatedCategoryCreatedAt = $category->createdAt;
				KBatchBase::unimpersonate();
				$this->updateJob($job, null, KalturaBatchJobStatus::PROCESSING, $data);
				KBatchBase::impersonate($job->partnerId);
				
				KalturaLog::debug('handling sub category '.$category->id);
				$this->syncCategoryPrivacyContext($job, $data, $category->id);				
			}
			$pager->pageIndex++;
			$categoryList = KBatchBase::$kClient->category->listAction($categoryFilter, $pager);
		}
		
		KBatchBase::unimpersonate();
		$job = $this->closeJob($job, null, null, null, KalturaBatchJobStatus::FINISHED);
		
		return $job;
	}
	
	private function syncCategoryPrivacyContext(KalturaBatchJob $job, KalturaSyncCategoryPrivacyContextJobData $data, $categoryId)
	{

		KalturaLog::debug('Last updated category entry created at: '.$data->lastUpdatedCategoryEntryCreatedAt);
		$categoryEntryPager = $this->getFilterPager();
	    $categoryEntryFilter = new KalturaCategoryEntryFilter();
		$categoryEntryFilter->orderBy = KalturaCategoryEntryOrderBy::CREATED_AT_ASC;
		$categoryEntryFilter->categoryIdEqual = $categoryId;
		if($data->lastUpdatedCategoryEntryCreatedAt && $data->lastUpdatedCategoryCreatedAt != '')
		{
			KalturaLog::debug('setting createdAtGreaterThanOrEqual on the filter' );
			$categoryEntryFilter->createdAtGreaterThanOrEqual = $data->lastUpdatedCategoryEntryCreatedAt;
		}		
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
	}
		
	private function getFilterPager()
	{
		$pager = new KalturaFilterPager();
		$pager->pageSize = 100;
		if(KBatchBase::$taskConfig->params->pageSize)
			$pager->pageSize = KBatchBase::$taskConfig->params->pageSize;
		return $pager;
	}
}
