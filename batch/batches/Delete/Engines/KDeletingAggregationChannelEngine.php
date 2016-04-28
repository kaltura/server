<?php
/**
 * @package Scheduler
 * @subpackage Delete
 */
class KDeletingAggregationChannelEngine extends  KDeletingEngine
{
	protected $lastCreatedAt;
	/* (non-PHPdoc)
	 * @see KDeletingEngine::delete()
	 */
	protected function delete(KalturaFilter $filter) {
		return $this->deleteAggregationCategoryEntries ($filter);
		
	}
	
	protected function deleteAggregationCategoryEntries (KalturaBaseEntryFilter $filter)
	{
		$filter->orderBy = KalturaBaseEntryOrderBy::CREATED_AT_ASC;
		if ($this->lastCreatedAt)
		{
			$filter->createdAtGreaterThanOrEqual = $this->lastCreatedAt;
		}
		
		$entriesList = KBatchBase::$kClient->baseEntry->listAction($filter, $this->pager);
		if(!count($entriesList->objects))
			return 0;
			
		$this->lastCreatedAt = $entriesList->objects[count ($entriesList->objects) -1];
		KBatchBase::$kClient->startMultiRequest();
		foreach($entriesList->objects as $entry)
		{
			$catsMatchAnd = explode(',', $filter->categoriesMatchAnd);
			/* @var $entry KalturaBaseEntry */
			KBatchBase::$kClient->categoryEntry->delete($entry->id, $catsMatchAnd[1]);
		}
		$results = KBatchBase::$kClient->doMultiRequest();
		foreach($results as $index => $result)
			if(is_array($result) && isset($result['code']))
				unset($results[$index]);

		return count($results);
		
	}

	
}