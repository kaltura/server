<?php

/**
 * Will hold helper functions for simulive usage
 */
class kSimuliveUtils
{
	const MINUTE_TO_MS = 60000;
	const SIMULIVE_SCHEDULE_MARGIN = 2;
	const SECOND_IN_MILLISECONDS = 1000;
	/**
	 * @param LiveEntry $entry
	 * @return array
	 */
	public static function getSimuliveEventDetails(LiveEntry $entry)
	{
		$dvrWindow = $entry->getDvrWindow();
		$currentEvent = self::getSimuliveEvent($entry, null, $dvrWindow);
		if (!$currentEvent)
		{
			return null;
		}

		/* @var $currentEvent ILiveStreamScheduleEvent */
		$sourceEntry = BaseentryPeer::retrieveByPK($currentEvent->getSourceEntryId());
		if(!$sourceEntry)
		{
			return null;
		}
		// all times should be in ms
		$durations[] = $sourceEntry->getLengthInMsecs();
		$startTime = $currentEvent->getCalculatedStartTime() * self::SECOND_IN_MILLISECONDS;
		$endTime = min($currentEvent->getCalculatedEndTime() * self::SECOND_IN_MILLISECONDS, $startTime + intval(array_sum($durations)));
		// getting the flavors from source entry
		$flavors = assetPeer::retrieveReadyFlavorsByEntryId($sourceEntry->getId());
		$dvrWindow  *= self::MINUTE_TO_MS;
		return array($durations, $flavors, $startTime, $endTime, $dvrWindow);
	}

	/**
	 * @param Entry $entry
	 * @param int $endTime - epoch time
	 * @param int $duration - in sec
	 * @return ILiveStreamScheduleEvent | null
	 */
	public static function getSimuliveEvent(Entry $entry, $endTime = 0, $duration = 0)
	{

		if ($entry->hasCapability(LiveEntry::LIVE_SCHEDULE_CAPABILITY) && $entry->getType() == entryType::LIVE_STREAM)
		{
			if (!$endTime)
			{
				$endTime = time();
			}
			$startTime = $endTime - $duration - self::SIMULIVE_SCHEDULE_MARGIN;
			$endTime += self::SIMULIVE_SCHEDULE_MARGIN;
			/* @var $entry LiveEntry */
			foreach ($entry->getScheduleEvents($startTime, $endTime) as $event)
			{
				if($event->getSourceEntryId())
				{
					return $event;
				}
			}
		}
		return null;
	}
}
