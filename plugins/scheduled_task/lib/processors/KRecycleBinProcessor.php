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
		$entriesList = $this->runSearch($pageIndex, $daysBeforeDelete);
		while ($entriesList && $entriesList->totalCount);
		{
			$deleteResults = $this->deleteEntries($entriesList);
			$numberOfHandledEntries += count($deleteResults);
			$pageIndex++;
			$entriesList = $this->runSearch($pageIndex);
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
		return $partner->daysBeforeRecycleBinDeletion;
	}
	
	protected function runSearch($pageIndex, $daysBeforeDelete)
	{
		$pager = new KalturaPager();
		$pager->pageIndex = $pageIndex;
		$pager->pageSize = 50;
		$range = new KalturaESearchRange();
		$range->lessThanOrEqual = time() - kTimeConversion::DAYS * $daysBeforeDelete;
		$entryItem = new KalturaESearchEntryItem();
		$entryItem->itemType = KalturaESearchItemType::RANGE;
		$entryItem->range = $range;
		$entryItem->fieldName = KalturaESearchEntryFieldName::RECYCLED_AT;
		$operator = new KalturaESearchEntryOperator();
		$operator->operator = KalturaESearchOperatorType::AND_OP;
		$operator->searchItems[] = $entryItem;
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
