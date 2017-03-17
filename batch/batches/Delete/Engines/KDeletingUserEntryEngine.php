<?php
/**
 * @package Scheduler
 * @subpackage Delete
 */
class KDeletingUserEntryEngine extends KDeletingEngine
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
	protected function deleteCategoryEntries(KalturaUserEntryFilter $filter)
	{
		$filter->orderBy = KalturaUserEntryOrderBy::CREATED_AT_ASC;
		
		$userEntryList = KBatchBase::$kClient->userEntry->listAction($filter, $this->pager);
		if(!count($userEntryList->objects))
			return 0;
			
		KBatchBase::$kClient->startMultiRequest();
		foreach($userEntryList->objects as $userEntry)
		{
			/* @var $categoryEntry KalturaUserEntry */
			KBatchBase::$kClient->userEntry->delete($userEntry->id);
		}
		$results = KBatchBase::$kClient->doMultiRequest();
		foreach($results as $index => $result)
			if(is_array($result) && isset($result['code']))
				unset($results[$index]);

		return count($results);
	}
}
