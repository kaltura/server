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
		return $this->moveCategoryEntries($job, $job->data);
	}
	
	/**
	 * Moves category entries from source category to destination category 
	 */
	private function move(KalturaBatchJob $job, KalturaMoveCategoryEntriesJobData $data)
	{
		KalturaLog::debug("Move category entries job id [$job->id]");
		
		$this->moveEntries($data->srcCategoryId, $data->destCategoryId, $data->moveFromChildren);
		
		return $job;
	}
	
	/**
	 * Moves category entries from source category to destination category
	 * 
	 * @param int $srcCategoryId Source category id
	 * @param int $destCategoryId Destination category id
	 * @param bool $moveFromChildren All entries from all child categories will be moved as well
	 * @param bool $copyOnly Entries won't be deleted from the source entry
	 */
	private function moveEntries($srcCategoryId, $destCategoryId, $moveFromChildren, $copyOnly)
	{
		$categoryEntryFilter = new KalturaCategoryEntryFilter();
		$categoryEntryFilter->categoryIdEqual = $srcCategoryId;
		
		$categoryEntriesList = $this->kClient->categoryEntry->listAction($categoryEntryFilter);
		if(!count($categoryEntriesList->objects))
			return 0;
			
		$this->kClient->startMultiRequest();
		foreach($categoryEntriesList->objects as $oldCategoryEntry)
		{
			/* @var $categoryEntry KalturaCategoryEntry */
			$newCategoryEntry = new KalturaCategoryEntry();
			$newCategoryEntry->entryId = $oldCategoryEntry->entryId;
			$newCategoryEntry->categoryId = $destCategoryId;
			$this->kClient->categoryEntry->add($newCategoryEntry);
		}
		$addedCategoryEntriesResults = $this->kClient->doMultiRequest();
		// TODO update job

		$this->kClient->startMultiRequest();
		foreach($addedCategoryEntriesResults as $addedCategoryEntryResult)
		{
			if($addedCategoryEntryResult instanceof Exception)
			{
				// TODO validate that it's not CATEGORY_ENTRY_ALREADY_EXISTS
				throw $addedCategoryEntryResult;
			}
				
			if($copyOnly)
				continue;
				
			if($addedCategoryEntryResult instanceof KalturaCategoryEntry)
				$this->kClient->categoryEntry->delete($addedCategoryEntryResult->entryId, $srcCategoryId);
		}
		$deletedCategoryEntriesResults = $this->kClient->doMultiRequest();
		// TODO update job
		
		foreach($deletedCategoryEntriesResults as $deletedCategoryEntryResult)
		{
			if($deletedCategoryEntryResult instanceof Exception)
				throw $deletedCategoryEntryResult;
		}
		
		$movedEntries = count($deletedCategoryEntriesResults);
		if(!$moveFromChildren)
			return $movedEntries;
			
		$categoryFilter = new KalturaCategoryFilter();
		$categoryFilter->parentIdEqual = $srcCategoryId;
		
		$pager = new KalturaFilterPager();
		$pager->pageSize = 100;
		if($this->taskConfig->params->pageSize)
			$pager->pageSize = $this->taskConfig->params->pageSize;
			
		$categoriesList = $this->kClient->category->listAction($categoryFilter, $pager);
		while(count($categoriesList->objects))
		{
			foreach($categoriesList->objects as $category)
			{
				/* @var $category KalturaCategory */
				
				$movedEntries += $this->moveEntries($category->id, $destCategoryId, $moveFromChildren, $copyOnly);
			}
			
			$pager->pageIndex++;
			$categoriesList = $this->kClient->category->listAction($categoryFilter, $pager);
		}
		
		return $movedEntries;
	}
}
