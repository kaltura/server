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
		return $this->moveCategoryEntries($job, $job->data);
	}
	
	/**
	 * Moves category entries from source category to destination category
	 * 
	 * @param KalturaBatchJob $job
	 * @param KalturaMoveCategoryEntriesJobData $data
	 * 
	 * @return KalturaBatchJob
	 */
	private function move(KalturaBatchJob $job, KalturaMoveCategoryEntriesJobData $data)
	{
		KalturaLog::debug("Move category entries job id [$job->id]");
		
		if($data->lastMovedCategoryId)
			$this->startMove = false;
			
		$job = $this->moveCategory($job, $data);
		return $this->closeJob($job, null, null, null, KalturaBatchJobStatus::FINISHED);
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
			if($this->taskConfig->params->pageSize)
				$categoryPager->pageSize = $this->taskConfig->params->pageSize;
				
			if($data->lastMovedCategoryId == $srcCategoryId)
				$categoryPager->pageIndex = $data->lastMovedCategoryPageIndex;
				
			$categoriesList = $this->kClient->category->listAction($categoryFilter, $categoryPager);
			while(count($categoriesList->objects))
			{
				foreach($categoriesList->objects as $category)
				{
					/* @var $category KalturaCategory */
					
					$movedEntries += $this->moveEntries($job, $data, $category->id);
				}
				
				$categoryPager->pageIndex++;
				
				$data->lastMovedCategoryPageIndex = $categoryPager->pageIndex;
				$this->updateJob($job, null, KalturaBatchJobStatus::PROCESSING, null, $data);
				
				$categoriesList = $this->kClient->category->listAction($categoryFilter, $categoryPager);
			}
		}
		
		$data->lastMovedCategoryId = $srcCategoryId;
		$this->updateJob($job, "Moved [$movedEntries] entries", KalturaBatchJobStatus::PROCESSING, null, $data);
		
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
		if($this->taskConfig->params->pageSize)
			$categoryEntryPager->pageSize = $this->taskConfig->params->pageSize;
			
		if($data->lastMovedCategoryId == $srcCategoryId)
			$categoryPager->pageIndex = $data->lastMovedCategoryEntryPageIndex;
			
		$movedEntries = 0;
		$categoryEntriesList = $this->kClient->categoryEntry->listAction($categoryEntryFilter, $categoryEntryPager);
		while(count($categoryEntriesList->objects))
		{
			$this->kClient->startMultiRequest();
			foreach($categoryEntriesList->objects as $oldCategoryEntry)
			{
				/* @var $categoryEntry KalturaCategoryEntry */
				$newCategoryEntry = new KalturaCategoryEntry();
				$newCategoryEntry->entryId = $oldCategoryEntry->entryId;
				$newCategoryEntry->categoryId = $data->destCategoryId;
				$this->kClient->categoryEntry->add($newCategoryEntry);
			}
			$addedCategoryEntriesResults = $this->kClient->doMultiRequest();
	
			$this->kClient->startMultiRequest();
			foreach($addedCategoryEntriesResults as $addedCategoryEntryResult)
			{
				if($addedCategoryEntryResult instanceof Exception)
				{
					if(!($addedCategoryEntryResult instanceof KalturaException) || $addedCategoryEntryResult->getCode() != self::CATEGORY_ENTRY_ALREADY_EXISTS)
						throw $addedCategoryEntryResult;
				}
					
				if($data->copyOnly)
					continue;
					
				if($addedCategoryEntryResult instanceof KalturaCategoryEntry)
					$this->kClient->categoryEntry->delete($addedCategoryEntryResult->entryId, $srcCategoryId);
			}
			$deletedCategoryEntriesResults = $this->kClient->doMultiRequest();
			
			foreach($deletedCategoryEntriesResults as $deletedCategoryEntryResult)
			{
				if($deletedCategoryEntryResult instanceof Exception)
					throw $deletedCategoryEntryResult;
			}
			
			$movedEntries += count($deletedCategoryEntriesResults);
			
			if($data->copyOnly)
			{
				$categoryEntryPager->pageIndex++;
				
				$data->lastMovedCategoryEntryPageIndex = $categoryEntryPager->pageIndex;
				$this->updateJob($job, null, KalturaBatchJobStatus::PROCESSING, null, $data);
			}
				
			$categoryEntriesList = $this->kClient->categoryEntry->listAction($categoryEntryFilter, $categoryEntryPager);
		}
	}
}
