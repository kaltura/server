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
	public function index(KalturaFilter $filter)
	{
		return $this->indexCategories($filter);
	}
	
	/**
	 * @param KalturaCategoryFilter $filter
	 * @return int the number of indexed objects
	 */
	protected function indexCategories(KalturaCategoryFilter $filter)
	{
		$filter->orderBy = KalturaCategoryOrderBy::DEPTH_ASC . ',' . KalturaCategoryOrderBy::CREATED_AT_ASC;
		
		$categoriesList = $this->client->category->list($filter, $this->pager);
		if(!count($categoriesList->objects))
			return 0;
			
		$this->client->startMultiRequest();
		foreach($categoriesList->objects as $category)
		{
			$this->client->index->indexCategory($category);
		}
		$results = $this->client->doMultiRequest();
		foreach($results as $result)
			if($result instanceof Exception)
				throw $result;
				
		$lastIndexId = end($results);
		$this->setLastIndexId($lastIndexId);
		
		return count($results);
	}
}
