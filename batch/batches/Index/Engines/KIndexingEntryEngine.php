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
	public function index(KalturaFilter $filter)
	{
		return $this->indexEntries($filter);
	}
	
	/**
	 * @param KalturaBaseEntryFilter $filter
	 * @return int the number of indexed objects
	 */
	protected function indexEntries(KalturaBaseEntryFilter $filter)
	{
		$filter->orderBy = KalturaBaseEntryOrderBy::CREATED_AT_ASC;
		
		$entriesList = $this->client->baseEntry->list($filter, $this->pager);
		if(!count($entriesList->objects))
			return 0;
			
		$this->client->startMultiRequest();
		foreach($entriesList->objects as $entry)
		{
			$this->client->index->indexEntry($entry);
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
