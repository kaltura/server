<?php
/**
 * @package plugins.metadata
 * @subpackage Scheduler.Index
 */
class KIndexingMetadataEngine extends KIndexingEngine
{
	/**
	 * @param KalturaFilter $filter
	 * @param bool $shouldUpdate
	 * @return int
	 */
	protected function index(KalturaFilter $filter, $shouldUpdate)
	{
		return $this->indexMetadataObjects($filter, $shouldUpdate);
	}

	/**
	 * @param KalturaMetadataFilter $filter
	 * @param $shouldUpdate
	 * @return int
	 */
	protected function indexMetadataObjects(KalturaMetadataFilter $filter, $shouldUpdate)
	{
		$filter->orderBy = KalturaMetadataOrderBy::CREATED_AT_ASC;
		$metadataPlugin = KalturaMetadataClientPlugin::get(KBatchBase::$kClient);
		$metadataList = $metadataPlugin->metadata->listAction($filter, $this->pager);
		if(!count($metadataList->objects))
			return 0;
			
		KBatchBase::$kClient->startMultiRequest();
		foreach($metadataList->objects as $metadata)
		{
			$metadataPlugin->metadata->index($metadata->id, $shouldUpdate);
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
