<?php

class ScheduledTaskBatchHelper
{
	/**
	 * @param KalturaClient $client
	 * @param KalturaScheduledTaskProfile $scheduledTaskProfile
	 * @param KalturaFilterPager $pager
	 * @return KalturaObjectListResponse
	 */
	public static function query(KalturaClient $client, KalturaScheduledTaskProfile $scheduledTaskProfile, KalturaFilterPager $pager)
	{
		$objectFilterEngineType = $scheduledTaskProfile->objectFilterEngineType;
		$objectFilterEngine = KObjectFilterEngineFactory::getInstanceByType($objectFilterEngineType, $client);
		$objectFilterEngine->setPageSize($pager->pageSize);
		$objectFilterEngine->setPageIndex($pager->pageIndex);
		return $objectFilterEngine->query($scheduledTaskProfile->objectFilter);
	}
}