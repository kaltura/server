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
	protected function delete(KalturaFilter $filter)
	{
		return $this->deleteCategoryEntries($filter);
	}
	
	/**
	 * @param KalturaCategoryEntryFilter $filter The filter should return the list of category entries that need to be deleted
	 * @return int the number of deleted category entries
	 */
	protected function deleteCategoryEntries(KalturaCategoryEntryFilter $filter)
	{
		$filter->orderBy = KalturaCategoryEntryOrderBy::CREATED_AT_ASC;
		
		$categoryEntriesList = $this->client->categoryEntry->listAction($filter, $this->pager);
		if(!count($categoryEntriesList->objects))
			return 0;
			
		$this->client->startMultiRequest();
		foreach($categoryEntriesList->objects as $categoryEntry)
		{
			/* @var $categoryEntry KalturaCategoryEntry */
			$this->client->categoryEntry->delete($categoryEntry->entryId, $categoryEntry->categoryId);
		}
		$results = $this->client->doMultiRequest();
		foreach($results as $index => $result)
			if(is_array($result) && isset($result['code']))
				unset($results[$index]);

		return count($results);
	}
}
