<?php
/**
 * @package Scheduler
 * @subpackage MoveCategoryEntries
 */

/**
 * Will move category entries from source category to destination category
 *
 * @package Scheduler
 * @subpackage MoveCategoryEntries
 */
class KAsyncMoveCategoryEntries extends KJobHandlerWorker
{
	const CATEGORY_ENTRY_ALREADY_EXISTS = 'CATEGORY_ENTRY_ALREADY_EXISTS';
	const INVALID_ENTRY_ID = 'INVALID_ENTRY_ID';
	
	/**
	 * Indicates that the moving of the entries could be started
	 * Used when the batch crash during the recursion
	 * and the move should start from the last crash point
	 * 
	 * @var bool
	 */
	private $startMove = true;
	
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::MOVE_CATEGORY_ENTRIES;
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
		return $this->move($job, $job->data);
	}
	
	/**
	 * Moves category entries from source category to destination category
	 * 
	 * @param KalturaBatchJob $job
	 * @param KalturaMoveCategoryEntriesJobData $data
	 * 
	 * @return KalturaBatchJob
	 */
	protected function move(KalturaBatchJob $job, KalturaMoveCategoryEntriesJobData $data)
	{
	    KBatchBase::impersonate($job->partnerId);
		KalturaLog::debug("Move category entries job id [$job->id]");
		
		if($data->lastMovedCategoryId)
			$this->startMove = false;
			
		$job = $this->moveCategory($job, $data);
		KBatchBase::unimpersonate();
		$job = $this->closeJob($job, null, null, null, KalturaBatchJobStatus::FINISHED);
		
		return $job;
	}
	
	/**
	 * Go through all categories tree and call moveEntries
	 * 
	 * @param KalturaBatchJob $job
	 * @param KalturaMoveCategoryEntriesJobData $data
	 * @param int $srcCategoryId Current source category id
	 * 
	 * @return KalturaBatchJob
	 */
	private function moveCategory(KalturaBatchJob $job, KalturaMoveCategoryEntriesJobData $data, $srcCategoryId = null)
	{
	    
		if(is_null($srcCategoryId))
			$srcCategoryId = $data->srcCategoryId;
		
		if(!$this->startMove && $data->lastMovedCategoryId == $srcCategoryId)
			$this->startMove = true;
		
		if($this->startMove)
			$movedEntries = $this->moveEntries($job, $data, $srcCategoryId);
		
		if($data->moveFromChildren)
		{
			$categoryFilter = new KalturaCategoryFilter();
			$categoryFilter->parentIdEqual = $srcCategoryId;
			
			$categoryPager = new KalturaFilterPager();
			$categoryPager->pageSize = 100;
			if(KBatchBase::$taskConfig->params->pageSize)
				$categoryPager->pageSize = KBatchBase::$taskConfig->params->pageSize;
				
			if($data->lastMovedCategoryId == $srcCategoryId)
				$categoryPager->pageIndex = $data->lastMovedCategoryPageIndex;
				
			$categoriesList = KBatchBase::$kClient->category->listAction($categoryFilter, $categoryPager);
			while(count($categoriesList->objects))
			{
				foreach($categoriesList->objects as $category)
				{
					/* @var $category KalturaCategory */
					$movedEntries += $this->moveCategory($job, $data, $category->id);
				}
				
				$categoryPager->pageIndex++;
				
				$data->lastMovedCategoryPageIndex = $categoryPager->pageIndex;
				$this->updateJob($job, null, KalturaBatchJobStatus::PROCESSING, $data);
				
				$categoriesList = KBatchBase::$kClient->category->listAction($categoryFilter, $categoryPager);
			}
		}
		
		$data->lastMovedCategoryId = $srcCategoryId;
		
		KBatchBase::unimpersonate();
		$this->updateJob($job, "Moved [$movedEntries] entries", KalturaBatchJobStatus::PROCESSING, $data);
		KBatchBase::impersonate($job->partnerId);
		
		return $job;
	}

	/**
	 * Moves category entries from source category to destination category
	 */
	private function moveEntries(KalturaBatchJob $job, KalturaMoveCategoryEntriesJobData $data, $srcCategoryId)
	{
		$categoryEntryFilter = new KalturaCategoryEntryFilter();
		$categoryEntryFilter->orderBy = KalturaCategoryEntryOrderBy::CREATED_AT_ASC;
		$categoryEntryFilter->categoryIdEqual = $srcCategoryId;
		
		$categoryEntryPager = new KalturaFilterPager();
		$categoryEntryPager->pageSize = 100;
		if(KBatchBase::$taskConfig->params->pageSize)
			$categoryEntryPager->pageSize = KBatchBase::$taskConfig->params->pageSize;
			
		if($data->lastMovedCategoryId == $srcCategoryId)
			$categoryPager->pageIndex = $data->lastMovedCategoryEntryPageIndex;
			
		$movedEntries = 0;
		$categoryEntriesList = KBatchBase::$kClient->categoryEntry->listAction($categoryEntryFilter, $categoryEntryPager);
		while(count($categoryEntriesList->objects))
		{
			KBatchBase::$kClient->startMultiRequest();
			$entryIds = array();
			foreach($categoryEntriesList->objects as $oldCategoryEntry)
			{
				/* @var $categoryEntry KalturaCategoryEntry */
				$newCategoryEntry = new KalturaCategoryEntry();
				$newCategoryEntry->entryId = $oldCategoryEntry->entryId;
				$newCategoryEntry->categoryId = $data->destCategoryId;
				KBatchBase::$kClient->categoryEntry->add($newCategoryEntry);
				$entryIds[] = $oldCategoryEntry->entryId;
			}
			$addedCategoryEntriesResults = KBatchBase::$kClient->doMultiRequest();
	
			KBatchBase::$kClient->startMultiRequest();
			foreach($addedCategoryEntriesResults as $index => $addedCategoryEntryResult)
			{
				if(	is_array($addedCategoryEntryResult) 
					&& isset($addedCategoryEntryResult['code']) 
					&& !in_array($addedCategoryEntryResult['code'], array(self::CATEGORY_ENTRY_ALREADY_EXISTS, self::INVALID_ENTRY_ID))
				)
					continue;
					
				if($data->copyOnly)
					continue;
					
				KBatchBase::$kClient->categoryEntry->delete($entryIds[$index], $srcCategoryId);
			}
			$deletedCategoryEntriesResults = KBatchBase::$kClient->doMultiRequest();
			if(is_null($deletedCategoryEntriesResults))
				$deletedCategoryEntriesResults = array();
			
			foreach($deletedCategoryEntriesResults as $index => $deletedCategoryEntryResult)
			{
				if(is_array($deletedCategoryEntryResult) && isset($deletedCategoryEntryResult['code']))
				{
					KalturaLog::err('error: ' . $deletedCategoryEntryResult['code']);
					unset($deletedCategoryEntriesResults[$index]);
				}
			}
			
			$movedEntries += count($deletedCategoryEntriesResults);
			
			if($data->copyOnly)
			{
				$categoryEntryPager->pageIndex++;
				
				$data->lastMovedCategoryEntryPageIndex = $categoryEntryPager->pageIndex;
				$this->updateJob($job, null, KalturaBatchJobStatus::PROCESSING, $data);
			}
				
			$categoryEntriesList = KBatchBase::$kClient->categoryEntry->listAction($categoryEntryFilter, $categoryEntryPager);
		}
	}
}
