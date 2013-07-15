<?php
/**
 * @package Scheduler
 * @subpackage Index
 */
class KIndexingCategoryUserEngine extends KIndexingEngine
{
	/* (non-PHPdoc)
	 * @see KIndexingEngine::index()
	 */
	protected function index(KalturaFilter $filter, $shouldUpdate)
	{
		return $this->indexCategories($filter, $shouldUpdate);
	}
	
	/**
	 * @param KalturaCategoryUserFilter $filter The filter should return the list of categories that need to be reindexed
	 * @param bool $shouldUpdate Indicates that the category user object columns and attributes values should be recalculated before reindexed
	 * @return int the number of indexed categories
	 */
	protected function indexCategories(KalturaCategoryUserFilter $filter, $shouldUpdate)
	{
		$filter->orderBy = KalturaCategoryUserOrderBy::CREATED_AT_ASC;
		
		$categoryUsersList = KBatchBase::$kClient->categoryUser->listAction($filter, $this->pager);
		if(!count($categoryUsersList->objects))
			return 0;
			
		KBatchBase::$kClient->startMultiRequest();
		foreach($categoryUsersList->objects as $categoryUser)
		{
			KBatchBase::$kClient->categoryUser->index($categoryUser->userId, $categoryUser->categoryId, $shouldUpdate);
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
