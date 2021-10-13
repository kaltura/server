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
	const SCHEDULE_TIME_OFFSET_URL_PARAM = 'timeOffset';
	const SCHEDULE_TIME_URL_PARAM = 'time';
	const DURATION_ROUND_THRESHOLD_SEC = 0.1;
	/**
	 * @param LiveEntry $entry
	 * @param int $time
	 * @return array
	 */
	public static function getSimuliveEventDetails(LiveEntry $entry, $time)
	{
		$dvrWindowMs = max($entry->getDvrWindow() * self::MINUTE_TO_MS, self::MIN_DVR_WINDOW_MS);
		$dvrWindowSec = $dvrWindowMs / self::SECOND_IN_MILLISECONDS;
		$currentEvent = self::getPlayableSimuliveEvent($entry, $time - $dvrWindowSec, $dvrWindowSec);
		if (!$currentEvent)
		{
			return null;
		}

		/* @var $currentEvent ILiveStreamScheduleEvent */
		$sourceEntry = kSimuliveUtils::getSourceEntry($currentEvent);
		if(!$sourceEntry)
		{
			return null;
		}
		// all times should be in ms
		$startTime = $currentEvent->getCalculatedStartTime() * self::SECOND_IN_MILLISECONDS;
		$durations[] = self::roundDuration(min($sourceEntry->getLengthInMsecs(), ($currentEvent->getCalculatedEndTime() * self::SECOND_IN_MILLISECONDS) - $startTime));

		list($mainFlavorAssets, $mainCaptionAssets, $mainAudioAssets) = myEntryUtils::getEntryAssets($sourceEntry);

		// getting the preStart assets (only if the preStartEntry exists)
		$preStartEntry = kSimuliveUtils::getPreStartEntry($currentEvent);
		list($preStartFlavorAssets, $preStartCaptionAssets, $preStartAudioAssets) = myEntryUtils::getEntryAssets($preStartEntry);
		if ($preStartEntry)
		{
			$preStartRoundedDuration = self::roundDuration($preStartEntry->getLengthInMsecs());
			array_unshift($durations, $preStartRoundedDuration);
		}

		// getting the postEnd assets (only if the postEndEntry exists)
		$postEndEntry = kSimuliveUtils::getPostEndEntry($currentEvent);
		list($postEndFlavorAssets, $postEndCaptionAssets, $postEndAudioAssets) = myEntryUtils::getEntryAssets($postEndEntry);
		if ($postEndEntry)
		{
			$postEndRoundedDuration = self::roundDuration($postEndEntry->getLengthInMsecs());
			$durations[] = $postEndRoundedDuration;
		}
		$endTime = $startTime + array_sum($durations);

		// creating the flavorAssets array (array of arrays s.t each array contain the flavor assets of all the entries exist)
		$flavorAssets = array();
		$flavorAssets = self::mergeAssetArrays($flavorAssets, $preStartFlavorAssets);
		$flavorAssets = self::mergeAssetArrays($flavorAssets, $mainFlavorAssets);
		$flavorAssets = self::mergeAssetArrays($flavorAssets, $postEndFlavorAssets);

		$captionAssets = self::createPaddedAssetsArray($mainCaptionAssets, $preStartCaptionAssets, $postEndCaptionAssets, count($preStartFlavorAssets) != 0, count($postEndFlavorAssets) != 0);
		$audioAssets = self::createPaddedAssetsArray($mainAudioAssets, $preStartAudioAssets, $postEndAudioAssets, count($preStartFlavorAssets) != 0, count($postEndFlavorAssets) != 0);

		$assets = array_merge($flavorAssets, $captionAssets, $audioAssets);
		return array($durations, $assets, $startTime, $endTime, $dvrWindowMs);
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
		if ($entry->hasCapability(LiveEntry::SIMULIVE_CAPABILITY) && $entry->getType() == entryType::LIVE_STREAM)
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
		return entryPeer::retrieveByPK($event->getSourceEntryId());
	}

	/**
	 * @param ILiveStreamScheduleEvent $event
	 * @return Entry
	 */
	public static function getPreStartEntry($event)
	{
		if ($event->getPreStartEntryId())
		{
			return entryPeer::retrieveByPK($event->getPreStartEntryId());
		}
		return null;
	}

	/**
	 * @param ILiveStreamScheduleEvent $event
	 * @return Entry
	 */
	public static function getPostEndEntry($event)
	{
		if ($event->getPostEndEntryId())
		{
			return entryPeer::retrieveByPK($event->getPostEndEntryId());
		}
		return null;
	}

	public static function getIsLiveCacheTime (LiveEntry $entry)
	{
		if (!$entry->hasCapability(LiveEntry::SIMULIVE_CAPABILITY))
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

	/**
	 * Get array of arrays ("arrayOfArrays") and array ("arr"), merge the i'th element of "arr" to the i'th array of "arrayOfArrays"
	 * @param array $arrayOfArrays
	 * @param array $arr
	 * @return array
	 */
	protected static function mergeAssetArrays ($arrayOfArrays, $arr)
	{
		if (!$arr || !count($arr))
		{
			return $arrayOfArrays;
		}
		if (!count($arrayOfArrays))
		{
			foreach ($arr as $elem)
			{
				$arrayOfArrays[] = array($elem);
			}
			return $arrayOfArrays;
		}
		foreach ($arrayOfArrays as &$a)
		{
			$a[] = array_shift($arr);
		}
		return $arrayOfArrays;
	}

	/**
	 * receiving 3 arrays of assets (caption/flavor), if one of the arrays isn't empty - it will fill the empty arrays 
	 * with nulls (according to the non empty arrays size). The function returns array of arrays s.t each array 
	 * has the merged assets (caption OR flavor) of pre+main+post entries padded with nulls for missing assets. 
	 * @param array $mainAssets
	 * @param array $preStartAssets
	 * @param array $postEndAssets
	 * @param boolean $hasPreStart
	 * @param boolean $hasPostEnd
	 * @return array
	 */
	protected static function createPaddedAssetsArray ($mainAssets, $preStartAssets, $postEndAssets, $hasPreStart, $hasPostEnd)
	{
		$assets = array();
		// we need to handle caption / audio assets only if there is caption asset for at least one of the entries
		if ($mainAssets || $preStartAssets || $postEndAssets)
		{
			$assetsCount = max(max(count($preStartAssets), count($mainAssets)), count($postEndAssets));
			// fill the empty caption / audio asset arrays with "nulls"
			$preStartAssets = array_pad($preStartAssets, $assetsCount, null);
			$mainAssets = array_pad($mainAssets, $assetsCount, null);
			$postEndAssets = array_pad($postEndAssets, $assetsCount, null);

			// creating the assets array (as array of arrays s.t each array contain the caption / audio assets of all the entries exist, padded with nulls if needed)
			$assets = $hasPreStart ? self::mergeAssetArrays($assets, $preStartAssets) : $assets;
			$assets = self::mergeAssetArrays($assets, $mainAssets);
			$assets = $hasPostEnd ? self::mergeAssetArrays($assets, $postEndAssets) : $assets;
		}
		return $assets;
	}

	/**
	 * receiving duration (in ms) , if the received duration (in sec) is slightly above the intval of the duration - it
	 * will return the rounded intval. otherwise - the received duration will be returned.
	 * @param int $durationMs
	 * @return int
	 */
	protected static function roundDuration ($durationMs)
	{
		$durationSec = $durationMs / self::SECOND_IN_MILLISECONDS;
		if ($durationSec > intval($durationSec) && $durationSec < intval($durationSec) + self::DURATION_ROUND_THRESHOLD_SEC)
		{
			return intval($durationSec) * self::SECOND_IN_MILLISECONDS;
		}
		return $durationMs;
	}
}
