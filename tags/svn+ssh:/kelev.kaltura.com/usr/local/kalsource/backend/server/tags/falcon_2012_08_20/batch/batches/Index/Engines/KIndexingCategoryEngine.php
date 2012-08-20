<?php
/**
 * @package Scheduler
 * @subpackage Index
 */
class KIndexingCategoryEngine extends KIndexingEngine
{
	/* (non-PHPdoc)
	 * @see KIndexingEngine::index()
	 */
	protected function index(KalturaFilter $filter, $shouldUpdate)
	{
		return $this->indexCategories($filter, $shouldUpdate);
	}
	
	/**
	 * @param KalturaCategoryFilter $filter The filter should return the list of categories that need to be reindexed
	 * @param bool $shouldUpdate Indicates that the category columns and attributes values should be recalculated before reindexed
	 * @return int the number of indexed categories
	 */
	protected function indexCategories(KalturaCategoryFilter $filter, $shouldUpdate)
	{
		$filter->orderBy = KalturaCategoryOrderBy::DEPTH_ASC . ',' . KalturaCategoryOrderBy::CREATED_AT_ASC;
		
		$categoriesList = $this->client->category->listAction($filter, $this->pager);
		if(!count($categoriesList->objects))
			return 0;
			
		$this->client->startMultiRequest();
		foreach($categoriesList->objects as $category)
		{
			$this->client->category->index($category->id, $shouldUpdate);
		}
		
		$results = $this->client->doMultiRequest();
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
