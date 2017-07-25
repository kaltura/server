<?php
/**
 * @package Scheduler
 * @subpackage Index
 */
class KIndexingEntryEngine extends KIndexingEngine
{
	/* (non-PHPdoc)
	 * @see KIndexingEngine::index()
	 */
	protected function index(KalturaFilter $filter, $shouldUpdate)
	{
		return $this->indexEntries($filter, $shouldUpdate);
	}
	
	/**
	 * @param KalturaBaseEntryFilter $filter The filter should return the list of entries that need to be reindexed
	 * @param bool $shouldUpdate Indicates that the entry columns and attributes values should be recalculated before reindexed
	 * @return int the number of indexed entries
	 */
	protected function indexEntries(KalturaBaseEntryFilter $filter, $shouldUpdate)
	{
		$filter->orderBy = KalturaBaseEntryOrderBy::CREATED_AT_ASC;
		
		$entriesList = KBatchBase::$kClient->baseEntry->listAction($filter, $this->pager);
		if(!count($entriesList->objects))
			return 0;
			
		KBatchBase::$kClient->startMultiRequest();
		foreach($entriesList->objects as $entry)
		{
			KBatchBase::$kClient->baseEntry->index($entry->id, $shouldUpdate);
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
	
	public function initAdvancedFilter(&$advancedFilter , &$data)
	{
		parent::initAdvancedFilter($advancedFilter, $data);
		$advancedFilter->idColumnName = "int_id";
	}
}
