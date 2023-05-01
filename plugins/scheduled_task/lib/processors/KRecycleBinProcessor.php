<?php

/**
 * @package plugins.scheduledTask
 * @subpackage lib.processors
 */
class KRecycleBinProcessor extends KGenericProcessor
{
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
		$numberOfHandledEntries = 0;
		$pageIndex = 1;
		$daysBeforeDelete = $this->getPartnerDaysBeforeDelete($profile->partnerId);
		if (!$daysBeforeDelete)
		{
			return;
		}
		$entriesList = $this->getOverdueRecycledEntries($pageIndex, $daysBeforeDelete);
		while ($entriesList && $entriesList->totalCount);
		{
			$deleteResults = $this->deleteEntries($entriesList);
			$numberOfHandledEntries += count($deleteResults);
			$pageIndex++;
			$entriesList = $this->getOverdueRecycledEntries($pageIndex, $daysBeforeDelete);
		}
		
		KalturaLog::info("Number of recycled entries deleted for partner [{$profile->partnerId}]: $numberOfHandledEntries");
	}
	
	/**
	 * @param $partnerId
	 * @return MultiRequestSubResult
	 */
	protected function getPartnerDaysBeforeDelete($partnerId)
	{
		KBatchBase::impersonate($partnerId);
		$partner = KBatchBase::$kClient->partner->get($partnerId);
		KBatchBase::unimpersonate();
		if (!$partner)
		{
			KalturaLog::err("Could not retrieve partner [$partnerId]");
			return null;
		}
		if (!$partner->daysBeforeRecycleBinDeletion || 1 > $partner->daysBeforeRecycleBinDeletion)
		{
			KalturaLog::err("Could not retrieve daysBeforeRecycleBinDeletion for partner [$partnerId]");
			return null;
		}
		return $partner->daysBeforeRecycleBinDeletion;
	}
	
	protected function getOverdueRecycledEntries($pageIndex, $daysBeforeDelete)
	{
		$pager = new KalturaPager();
		$pager->pageIndex = $pageIndex;
		$pager->pageSize = 50;
		
		$range = new KalturaESearchRange();
		$range->lessThanOrEqual = time() - kTimeConversion::DAYS * $daysBeforeDelete;
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
	
	protected function deleteEntries($entriesList)
	{
		KBatchBase::$kClient->startMultiRequest();
		foreach ($entriesList->objects as $entry)
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
