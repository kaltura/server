<?php

class ScheduledTaskBatchHelper
{
	const MAX_RESULTS_THRESHOLD = 1000;

	/**
	 * @param KalturaClient $client
	 * @param KalturaScheduledTaskProfile $scheduledTaskProfile
	 * @param KalturaFilterPager $pager
	 * @param KalturaFilter $filter
	 * @return KalturaObjectListResponse
	 */
	public static function query(KalturaClient $client, KalturaScheduledTaskProfile $scheduledTaskProfile, KalturaFilterPager $pager, $filter = null)
	{
		$objectFilterEngineType = $scheduledTaskProfile->objectFilterEngineType;
		$objectFilterEngine = KObjectFilterEngineFactory::getInstanceByType($objectFilterEngineType, $client);
		$objectFilterEngine->setPageSize($pager->pageSize);
		$objectFilterEngine->setPageIndex($pager->pageIndex);
		if(!$filter)
			$filter = $scheduledTaskProfile->objectFilter;

		return $objectFilterEngine->query($filter);
	}

	/**
	 * @param KalturaBaseEntryArray $entries
	 * @param $createAtTime
	 * @return array
	 */
	public static function getEntriesIdWithSameCreateAtTime($entries, $createAtTime)
	{
		$result = array();
		foreach ($entries as $entry)
		{
			if($entry->createdAt == $createAtTime)
				$result[] = $entry->id;
		}

		$result = array_column($result, 'id');
		return $result;
	}

	public static function getMediaTypeString($mediaType)
	{
		switch ($mediaType)
		{
			case 1:
				return "video";
				break;
			case 2:
				return "image";
				break;
			case 5:
				return "audio";
				break;
			case 201:
				return "live steam flash";
				break;
			case 202:
				return "live steam windows media";
				break;
			case 203:
				return "live steam real media";
				break;
			case 204:
				return "live steam quicktime";
				break;
			default:
				return "unkown";
		}
	}
}