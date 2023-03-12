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
		$entriesToDeletePerRequest = KBatchBase::$taskConfig->params->entriesToDeletePerRequest;
		$waitBetweenRequestsInSeconds = KBatchBase::$taskConfig->params->waitBetweenRequestsInSeconds;
		if (!$entriesToDeletePerRequest || !$waitBetweenRequestsInSeconds)
		{
			throw new KalturaAPIException(KalturaErrors::ENTRY_BULK_DELETE_MISSING_WORKER_PARAMS);
		}
		
		$this->pager->pageIndex = 0;
		$this->pager->pageSize = $entriesToDeletePerRequest;
		
		$numberOfHandledEntries = 0;
		do
		{
			$this->pager->pageIndex++;
			
			$entriesList = KBatchBase::$kClient->baseEntry->listAction($filter, $this->pager);
			if (!$entriesList->objects || !count($entriesList->objects))
			{
				break;
			}
			
			if ($numberOfHandledEntries)
			{
				sleep($waitBetweenRequestsInSeconds);
			}
			
			KBatchBase::$kClient->startMultiRequest();
			foreach ($entriesList->objects as $entry)
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
			$numberOfHandledEntries += count($results);
			
		} while (count($entriesList->objects) >= $this->pager->pageSize);
		
		if (!$numberOfHandledEntries)
		{
			return 0;
		}

		return $numberOfHandledEntries;
	}
}
