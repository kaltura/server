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
	const MIN_DVR_WINDOW_MS = 30000;
	const MINIMUM_TIME_TO_PLAYABLE_SEC = 18; // 3 * default segment duration
	/**
	 * @param LiveEntry $entry
	 * @return array
	 */
	public static function getSimuliveEventDetails(LiveEntry $entry)
	{
		$dvrWindowMs = max($entry->getDvrWindow() * self::MINUTE_TO_MS, self::MIN_DVR_WINDOW_MS);
		$dvrWindowSec = $dvrWindowMs / self::SECOND_IN_MILLISECONDS;
		$currentEvent = self::getPlayableSimuliveEvent($entry, time() - $dvrWindowSec, $dvrWindowSec);
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
		$startTime = $currentEvent->getCalculatedStartTime() * self::SECOND_IN_MILLISECONDS;
		$durations[] = min($sourceEntry->getLengthInMsecs(), ($currentEvent->getCalculatedEndTime() * self::SECOND_IN_MILLISECONDS) - $startTime);
		$endTime = $startTime + array_sum($durations);
		// getting the flavors from source entry
		$flavors = assetPeer::retrieveReadyWebByEntryId($sourceEntry->getId());
		return array($durations, $flavors, $startTime, $endTime, $dvrWindowMs);
	}

	/**
	 * @param Entry $entry
	 * @param int $startTime - epoch time
	 * @param int $duration - in sec
	 * @return array<ILiveStreamScheduleEvent>
	 */
	public static function getSimuliveEvents(Entry $entry, $startTime = 0, $duration = 0)
	{
		$events = array();
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
					$events[] = $event;
				}
			}
		}
		return $events;
	}

	/**
	 * @param Entry $entry
	 * @param int $startTime - epoch time
	 * @param int $duration - in sec
	 * @return ILiveStreamScheduleEvent | null
	 */
	public static function getSimuliveEvent(Entry $entry, $startTime = 0, $duration = 0)
	{
		$events = self::getSimuliveEvents($entry, $startTime, $duration);
		return $events ? $events[0] : null;
	}

	/**
	 * Get an event that startTime + duration (now epoch by default) is at least MINIMUM_TIME_TO_PLAYABLE_SEC inside
	 * the event.
	 * @param Entry $entry
	 * @param int $startTime - epoch time
	 * @param int $duration - in sec
	 * @return ILiveStreamScheduleEvent | null
	 */
	public static function getPlayableSimuliveEvent(Entry $entry, $startTime = 0, $duration = 0)
	{
		$startTime = $startTime ? $startTime : time();
		$event = self::getSimuliveEvent($entry, $startTime, $duration);
		// consider the event as playable only after 3 segments
		if ($event && ($startTime + $duration) >= ($event->getCalculatedStartTime() + self::MINIMUM_TIME_TO_PLAYABLE_SEC))
		{
			return $event;
		}
		return null;
	}

	/**
	 * @param ILiveStreamScheduleEvent $event
	 * @return Entry
	 */
	public static function getSourceEntry($event)
	{
		return entryPeer::retrieveByPKNoFilter($event->getSourceEntryId(), null, false);
	}

	public static function getIsLiveCacheTime (LiveEntry $entry)
	{
		if (!$entry->hasCapability(LiveEntry::LIVE_SCHEDULE_CAPABILITY))
		{
			return 0;
		}
		$nowEpoch = time();
		$simuliveEvent = kSimuliveUtils::getPlayableSimuliveEvent($entry, $nowEpoch, self::LIVE_SCHEDULE_AHEAD_TIME);
		if (!$simuliveEvent)
		{
			return self::LIVE_SCHEDULE_AHEAD_TIME;
		}
		// playableStartTime only after 3 segments
		$playableStartTime = $simuliveEvent->getCalculatedStartTime() + self::MINIMUM_TIME_TO_PLAYABLE_SEC;
		if ($nowEpoch >= $playableStartTime && $nowEpoch < $simuliveEvent->getCalculatedEndTime())
		{
			return $simuliveEvent->getCalculatedEndTime() - $nowEpoch;
		}
		// conditional cache should expire when event start
		return max($playableStartTime - $nowEpoch, self::SIMULIVE_SCHEDULE_MARGIN);
	}
}
