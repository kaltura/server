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
		if (!$filter->orderBy)
		{
			$filter->orderBy = KalturaBaseEntryOrderBy::UPDATED_AT_ASC;
		}
		
		$entriesList = KBatchBase::$kClient->baseEntry->listAction($filter, $this->pager);
		if (!$entriesList->objects || !count($entriesList->objects))
		{
			return 0;
		}
		
		$entriesToDeletePerRequest = KBatchBase::$taskConfig->params->entriesToDeletePerRequest;
		$waitBetweenRequestsInSeconds = KBatchBase::$taskConfig->params->waitBetweenRequestsInSeconds;
		
		$entriesCount = 0;
		$countResults = 0;
		while ($entriesCount < count($entriesList->objects))
		{
			KBatchBase::$kClient->startMultiRequest();
			for ($i = $entriesCount; $i < min($entriesCount + $entriesToDeletePerRequest, count($entriesList->objects)); $i++)
			{
				$entry = $entriesList->objects[$i];
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
			$countResults += count($results);
			
			$entriesCount += $entriesToDeletePerRequest;
			sleep($waitBetweenRequestsInSeconds);
		}

		return $countResults;
	}
}
