<?php

/**
 * Will hold helper functions for simulive usage
 */
class kSimuliveUtils
{
	const MINUTE_TO_MS = 60000;
	const SIMULIVE_SCHEDULE_MARGIN = 2;
	const SECOND_IN_MILLISECONDS = 1000;
	const LIVE_SCHEDULE_AHEAD_TIME = 60;
	/**
	 * @param LiveEntry $entry
	 * @return array
	 */
	public static function getSimuliveEventDetails(LiveEntry $entry)
	{
		$dvrWindowMs = $entry->getDvrWindow() * self::MINUTE_TO_MS;
		$dvrWindowSec = $dvrWindowMs / self::SECOND_IN_MILLISECONDS;
		$currentEvent = self::getSimuliveEvent($entry, time() - $dvrWindowSec, $dvrWindowSec);
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
		return array($durations, $flavors, $startTime, $endTime, $dvrWindowMs);
	}

	/**
	 * @param Entry $entry
	 * @param int $startTime - epoch time
	 * @param int $duration - in sec
	 * @return ILiveStreamScheduleEvent | null
	 */
	public static function getSimuliveEvent(Entry $entry, $startTime = 0, $duration = 0)
	{

		if ($entry->hasCapability(LiveEntry::LIVE_SCHEDULE_CAPABILITY) && $entry->getType() == entryType::LIVE_STREAM)
		{
			if (!$startTime)
			{
				$startTime = time();
			}
			$endTime = $startTime + $duration + self::SIMULIVE_SCHEDULE_MARGIN;
			$startTime -= self::SIMULIVE_SCHEDULE_MARGIN;
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

	public static function isSimuliveCurrentlyLive (LiveEntry $entry)
	{
		if (!$entry->hasCapability(LiveEntry::LIVE_SCHEDULE_CAPABILITY))
		{
			return false;
		}
		$nowEpoch = time();
		$simuliveEvent = kSimuliveUtils::getSimuliveEvent($entry, $nowEpoch, self::LIVE_SCHEDULE_AHEAD_TIME);
		if (!$simuliveEvent)
		{
			KalturaResponseCacher::setConditionalCacheExpiry(self::LIVE_SCHEDULE_AHEAD_TIME);
			return false;
		}
		if ($nowEpoch >= $simuliveEvent->getCalculatedStartTime() && $nowEpoch <= $simuliveEvent->getCalculatedEndTime())
		{
			KalturaResponseCacher::setConditionalCacheExpiry($simuliveEvent->getCalculatedEndTime() - $nowEpoch);
			return true;
		}
		// conditional cache should expire when event start
		KalturaResponseCacher::setConditionalCacheExpiry($simuliveEvent->getCalculatedStartTime() - $nowEpoch);
		return false;
	}
}
