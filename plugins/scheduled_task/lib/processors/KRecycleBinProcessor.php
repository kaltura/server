<?php

/**
 * @package plugins.scheduledTask
 * @subpackage lib.processors
 */
class KRecycleBinProcessor extends KGenericProcessor
{
	const ENTRIES_PAGE_SIZE = 50;
	const ENTRIES_NUMBER_OF_PAGES = 20;
	
	/**
	 * @param KalturaScheduledTaskProfile $profile
	 */
	public function processProfile(KalturaScheduledTaskProfile $profile)
	{
		if ($this->wasHandledToday($profile->lastExecutionStartedAt))
		{
			KalturaLog::info("Recycle Bin Scheduled Task Profile [$profile->id] was already handled today. No need to handle again");
			return;
		}
		
		$this->taskRunner->impersonate($profile->partnerId);
		try
		{
			$maxTotalCountAllowed = $this->preProcess($profile);
			$objectsData = $this->handleProcess($profile, $maxTotalCountAllowed);
			$this->postProcess($profile, $objectsData);
			
		} catch (Exception $ex)
		{
			$this->taskRunner->unimpersonate();
			throw $ex;
		}
		$this->taskRunner->unimpersonate();
	}
	
	/**
	 * @param KalturaScheduledTaskProfile $profile
	 * @param $maxTotalCountAllowed
	 * @return mixed
	 */
	protected function handleProcess(KalturaScheduledTaskProfile $profile, $maxTotalCountAllowed)
	{
		KBatchBase::impersonate($profile->partnerId);
		$recycleBinRetentionPeriod = $this->getPartnerRecycleBinRetentionPeriod($profile->partnerId);
		if (!$recycleBinRetentionPeriod)
		{
			return;
		}
		$entriesListsToDelete = $this->getEntriesListsToDelete($recycleBinRetentionPeriod);
		$numberOfHandledEntries = $this->handleEntriesListsToDelete($entriesListsToDelete);
		KBatchBase::unimpersonate();
		KalturaLog::info("Number of recycled entries deleted for partner [{$profile->partnerId}]: $numberOfHandledEntries");
	}
	
	/**
	 * @param $partnerId
	 * @return MultiRequestSubResult
	 */
	protected function getPartnerRecycleBinRetentionPeriod($partnerId)
	{
		$partner = KBatchBase::$kClient->partner->get($partnerId);
		if (!$partner)
		{
			KalturaLog::err("Could not retrieve partner [$partnerId]");
			return null;
		}
		if (!$partner->recycleBinRetentionPeriod || 1 > $partner->recycleBinRetentionPeriod)
		{
			KalturaLog::err("Could not retrieve recycleBinRetentionPeriod for partner [$partnerId]");
			return null;
		}
		return $partner->recycleBinRetentionPeriod;
	}
	
	protected function getEntriesListsToDelete($recycleBinRetentionPeriod)
	{
		$pageIndex = 1;
		$entriesList = $this->getOverdueRecycledEntries($pageIndex, $recycleBinRetentionPeriod);
		if (!$entriesList || !$entriesList->objects)
		{
			return array();
		}
		$entriesToDelete = array($entriesList->objects);
		while (count($entriesList->objects) >= self::ENTRIES_PAGE_SIZE && self::ENTRIES_NUMBER_OF_PAGES >= $pageIndex)
		{
			$pageIndex++;
			$entriesList = $this->getOverdueRecycledEntries($pageIndex, $recycleBinRetentionPeriod);
			if (!$entriesList || !$entriesList->objects)
			{
				return $entriesToDelete;
			}
			$entriesToDelete[] = $entriesList;
		}
		
		return $entriesToDelete;
	}
	
	protected function getOverdueRecycledEntries($pageIndex, $recycleBinRetentionPeriod)
	{
		$pager = new KalturaPager();
		$pager->pageIndex = $pageIndex;
		$pager->pageSize = self::ENTRIES_PAGE_SIZE;
		
		$range = new KalturaESearchRange();
		$range->lessThanOrEqual = time() - kTimeConversion::DAYS * $recycleBinRetentionPeriod;
		$recycledAtRange = new KalturaESearchEntryItem();
		$recycledAtRange->fieldName = KalturaESearchEntryFieldName::RECYCLED_AT;
		$recycledAtRange->itemType = KalturaESearchItemType::RANGE;
		$recycledAtRange->range = $range;
		$displayInSearchValue = new KalturaESearchEntryItem();
		$displayInSearchValue->fieldName = KalturaESearchEntryFieldName::DISPLAY_IN_SEARCH;
		$displayInSearchValue->itemType = KalturaESearchItemType::EXACT_MATCH;
		$displayInSearchValue->searchTerm = KalturaEntryDisplayInSearchType::RECYCLED;
		$operator = new KalturaESearchEntryOperator();
		$operator->operator = KalturaESearchOperatorType::AND_OP;
		$operator->searchItems[] = $displayInSearchValue;
		$operator->searchItems[] = $recycledAtRange;
		$entryOrderByItem = new KalturaESearchEntryOrderByItem();
		$entryOrderByItem->sortOrder = KalturaESearchSortOrder::ORDER_BY_ASC;
		$entryOrderByItem->sortField = KalturaESearchEntryOrderByFieldName::RECYCLED_AT;
		$orderBy = new KalturaESearchOrderBy();
		$orderBy->orderItems[] = $entryOrderByItem;
		$searchParams = new KalturaESearchEntryParams();
		$searchParams->searchOperator = $operator;
		
		$eSearchClientPlugin = KalturaElasticSearchClientPlugin::get(KBatchBase::$kClient);
		return $eSearchClientPlugin->eSearch->searchEntry($searchParams, $pager);
	}
	
	protected function handleEntriesListsToDelete($entriesListsToDelete)
	{
		$numberOfHandledEntries = 0;
		foreach ($entriesListsToDelete as $entriesList)
		{
			$deleteResults = $this->deleteEntries($entriesList);
			$numberOfHandledEntries += count($deleteResults);
		}
		return $numberOfHandledEntries;
	}
	
	protected function deleteEntries($entriesList)
	{
		KBatchBase::$kClient->startMultiRequest();
		foreach ($entriesList as $entry)
		{
			KBatchBase::$kClient->baseEntry->delete($entry->object->id);
		}
		$results = KBatchBase::$kClient->doMultiRequest();
		if (!$results)
		{
			return array();
		}
		
		foreach ($results as $index => $result)
		{
			if (is_array($result) && isset($result['code']))
			{
				unset($results[$index]);
			}
		}
		return $results;
	}
	
	protected function wasHandledToday($time)
	{
		return (intval(time() / kTimeConversion::DAY) == (intval($time / kTimeConversion::DAY)));
	}
}
