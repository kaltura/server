<?php
/**
 * @package Scheduler
 * @subpackage Delete
 */
class KDeletingEntryEngine extends KDeletingEngine
{
	/* (non-PHPdoc)
	 * @see KDeletingEngine::delete()
	 */
	protected function delete(KalturaFilter $filter)
	{
		return $this->deleteEntries($filter);
	}
	
	/**
	 * @param KalturaBaseEntryFilter $filter The filter should return the list of entries that need to be deleted
	 * @return int the number of deleted entries
	 */
	protected function deleteEntries(KalturaBaseEntryFilter $filter)
	{
		//$filter->orderBy = KalturaBaseEntryFilter::;
		
		$entriesList = KBatchBase::$kClient->baseEntry->listAction($filter, $this->pager);
		if (!$entriesList->objects || !count($entriesList->objects))
		{
			return 0;
		}
		
		KBatchBase::$kClient->startMultiRequest();
		foreach($entriesList->objects as $entry)
		{
			/* @var $entry KalturaBaseEntry */
			KBatchBase::$kClient->baseEntry->delete($entry->id);
		}
		$results = KBatchBase::$kClient->doMultiRequest();
		foreach ($results as $index => $result)
		{
			if (is_array($result) && isset($result['code']))
			{
				unset($results[$index]);
			}
		}

		return count($results);
	}
}
