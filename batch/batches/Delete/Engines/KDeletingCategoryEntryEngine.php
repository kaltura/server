<?php
/**
 * @package Scheduler
 * @subpackage Delete
 */
class KDeletingCategoryEntryEngine extends KDeletingEngine
{
	/* (non-PHPdoc)
	 * @see KDeletingEngine::delete()
	 */
	protected function delete(KalturaFilter $filter, $shouldUpdate)
	{
		return $this->deleteCategoryEntries($filter, $shouldUpdate);
	}
	
	/**
	 * @param KalturaCategoryEntryFilter $filter The filter should return the list of category entries that need to be deleted
	 * @return int the number of deleted category entries
	 */
	protected function deleteCategoryEntries(KalturaCategoryEntryFilter $filter)
	{
		$filter->orderBy = KalturaCategoryEntryOrderBy::CREATED_AT_ASC;
		
		$categoryEntriesList = $this->client->categoryEntry->list($filter, $this->pager);
		if(!count($categoryEntriesList->objects))
			return 0;
			
		$this->client->startMultiRequest();
		foreach($categoryEntriesList->objects as $categoryEntry)
		{
			/* @var $categoryEntry KalturaCategoryEntry */
			$this->client->categoryEntry->delete($categoryEntry->entryId, $categoryEntry->categoryId);
		}
		$results = $this->client->doMultiRequest();
		foreach($results as $result)
			if($result instanceof Exception)
				throw $result;
				
		return count($results);
	}
}
