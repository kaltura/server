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
	const CATEGORY_NOT_FOUND = 'CATEGORY_NOT_FOUND';
	
	/**
	 * Indicates that the moving of the entries could be started
	 * Used when the batch crash during the recursion
	 * and the move should start from the last crash point
	 * 
	 * @var bool
	 */
	private $startMove = true;
	
	/**
	 * list of known ancestors for deleted categories
	 */
	private $ancestors = array();
	
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
	 * Gets the first existing category ancestor according to the fallback list
	 * @param string $category
	 * @param string $fallback
	 */
	public function getDeepestLiveAncestor($categoryId, $fallback)
	{
		$this->ancestors[$categoryId] = 0;
		
		if($fallback == $categoryId) {
			return $this->ancestors[$categoryId];
		}
			
		$fallbacks = explode(">", $fallback);
		for($i = count($fallbacks) - 2; $i >= 0 ; $i -= 1) {
			$filter = new KalturaCategoryFilter();
			$filter->idEqual = $fallbacks[$i];
			$result = KBatchBase::$kClient->category->listAction($filter,null);
			if($result->totalCount) {
				$parent = $result->objects[0];
				$this->ancestors[$categoryId] = $parent->id;
				break;
			}
		}
			
		return $this->ancestors[$categoryId];
	}
	
	private function addCategoryEntries($categoryEntriesList, $destCategoryId, &$entryIds) 
	{
		KBatchBase::$kClient->startMultiRequest();
		foreach($categoryEntriesList->objects as $oldCategoryEntry)
		{
			/* @var $categoryEntry KalturaCategoryEntry */
			$newCategoryEntry = new KalturaCategoryEntry();
			$newCategoryEntry->entryId = $oldCategoryEntry->entryId;
			$newCategoryEntry->categoryId = $destCategoryId;
			KBatchBase::$kClient->categoryEntry->add($newCategoryEntry);
			$entryIds[] = $oldCategoryEntry->entryId;
		}
		return KBatchBase::$kClient->doMultiRequest();
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
			$entryIds = array();
			$ancestor = $data->destCategoryId;
			if(array_key_exists($ancestor, $this->ancestors))
				$ancestor = $this->ancestors[$ancestor];
			
			$addedCategoryEntriesResults = $this->addCategoryEntries($categoryEntriesList, $ancestor, $entryIds);
			
			$categoryDeleted = false;
			if(	is_array($addedCategoryEntriesResults[0]) && isset($addedCategoryEntriesResults[0]['code']) && ($addedCategoryEntriesResults[0]['code'] == self::CATEGORY_NOT_FOUND))
				$categoryDeleted = true;
			
			
			if($categoryDeleted) {
				$ancestor = $this->getDeepestLiveAncestor($data->destCategoryId, $data->destCategoryFullIds, true);
				// In case the category isn't found since it was just deleted, recall with the matching ancestor
				if($ancestor) {
					$data->destCategoryId = $ancestor;
					continue;
				}
			}
			
			KBatchBase::$kClient->startMultiRequest();
			foreach($addedCategoryEntriesResults as $index => $addedCategoryEntryResult)
			{
				$code = null;
				if(	is_array($addedCategoryEntryResult) && isset($addedCategoryEntryResult['code']))
					$code = $addedCategoryEntryResult['code'];
						
				if(!is_null($code) && !in_array($code, array(self::CATEGORY_ENTRY_ALREADY_EXISTS, self::INVALID_ENTRY_ID, self::CATEGORY_NOT_FOUND)))
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
