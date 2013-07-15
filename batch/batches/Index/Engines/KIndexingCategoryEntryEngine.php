<?php
/**
 * @package Scheduler
 * @subpackage Index
 */
class KIndexingCategoryEntryEngine extends KIndexingEngine
{
	/* (non-PHPdoc)
	 * @see KIndexingEngine::index()
	 */
	protected function index(KalturaFilter $filter, $shouldUpdate)
	{
		return $this->indexCategories($filter, $shouldUpdate);
	}
	
	/**
	 * @param KalturaCategoryEntryFilter $filter The filter should return the list of categories that need to be reindexed
	 * @param bool $shouldUpdate Indicates that the category entry object columns and attributes values should be recalculated before reindexed
	 * @return int the number of indexed categories
	 */
	protected function indexCategories(KalturaCategoryEntryFilter $filter, $shouldUpdate)
	{
		$filter->orderBy = KalturaCategoryEntryOrderBy::CREATED_AT_ASC;
		
		$categoryEntriesList = KBatchBase::$kClient->categoryEntry->listAction($filter, $this->pager);
		if(!count($categoryEntriesList->objects))
			return 0;
			
		KBatchBase::$kClient->startMultiRequest();
		foreach($categoryEntriesList->objects as $categoryEntry)
		{
			KBatchBase::$kClient->categoryEntry->index($categoryEntry->entryId, $categoryEntry->categoryId , $shouldUpdate);
		}
		$results = KBatchBase::$kClient->doMultiRequest();
		foreach($results as $index => $result)
			if(!is_int($result))
				unset($results[$index]);
				
		if(!count($results))
			return 0;
				
		$lastIndexId = end($results);
		$this->setLastIndexId($lastIndexId);
		
		return count($results);
	}
}
