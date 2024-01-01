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
	const DURATION_ROUND_THRESHOLD_MILISECONDS = 100;
	const LABEL_SEPERATOR = '-';
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

		$sourceEntries = $sourceEntry->getType() == entryType::PLAYLIST ? myPlaylistUtils::retrieveStitchedPlaylistEntries($sourceEntry) : array($sourceEntry);
		$sourceEntryLabels = array();
		foreach ($sourceEntries as $source)
		{
			$sourceEntryLabels[] = "content" . self::LABEL_SEPERATOR . $source->getEntryId();
		}
		// getting the preStart assets (only if the preStartEntry exists)
		$preStartEntry = kSimuliveUtils::getPreStartEntry($currentEvent);
		if ($preStartEntry)
		{
			array_unshift($sourceEntries, $preStartEntry);
			array_unshift($sourceEntryLabels, "preStartContent" . self::LABEL_SEPERATOR . $preStartEntry->getEntryId());
		}

		// getting the postEnd assets (only if the postEndEntry exists)
		$postEndEntry = kSimuliveUtils::getPostEndEntry($currentEvent);
		if ($postEndEntry)
		{
			$sourceEntries[] = $postEndEntry;
			$sourceEntryLabels[] = "postEntryContent-".$postEndEntry->getEntryId();
		}

		list($entriesFlavorAssets, $entriesCaptionAssets, $entriesAudioAssets) = self::getSourceAssets($sourceEntries);
		$durations = self::getSourceDurations($sourceEntries, $currentEvent);

		$endTime = $startTime + array_sum($durations);
		self::addTimestamps($sourceEntryLabels, $startTime, $durations);
		if (self::shouldLiveInterrupt($entry, $currentEvent))
		{
			// endTime null will cause "expirationTime" to be added to the json
			$endTime = null;
		}

		// creating the flavorAssets array (array of arrays s.t each array contain the flavor assets of all the entries exist)
		$flavorAssets = array();
		foreach ($entriesFlavorAssets as $entryAssets)
		{
			$flavorAssets = self::mergeAssetArrays($flavorAssets, $entryAssets);
		}

		$captionAssets = self::createPaddedAssetsArray($entriesCaptionAssets);
		$audioAssets = self::createPaddedAssetsArray($entriesAudioAssets);

		$assets = array_merge($flavorAssets, $captionAssets, $audioAssets);
		$eventLabel = "eventId" . self::LABEL_SEPERATOR .$currentEvent->getId();
		return array($durations, $assets, $startTime, $endTime, $dvrWindowMs, $sourceEntryLabels, $eventLabel);
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
	 * receiving array of asset arrays. padding each asset array with nulls according to the longest array. 
	 * @param array $assets
	 * @return array
	 */
	protected static function createPaddedAssetsArray ($assets)
	{
		$paddedAssets = array();
		// null padding should be according to the largest assets array
		$assetsCount = count(max($assets));
		// we need to handle caption / audio assets only if there is an asset for at least one of the entries
		if ($assetsCount)
		{
			foreach ($assets as &$asset)
			{
				$asset = array_pad($asset, $assetsCount, null);
				$paddedAssets = self::mergeAssetArrays($paddedAssets, $asset);
			}
		}
		return $paddedAssets;
	}

	/**
	 * receiving duration (in ms) , if the received duration (in sec) is slightly above the intval of the duration - it
	 * will return the rounded intval. otherwise - the received duration will be returned.
	 * @param int $durationMs
	 * @return int
	 */
	protected static function roundDuration ($durationMs)
	{
		$durationFrac = $durationMs % self::SECOND_IN_MILLISECONDS;
		if ($durationFrac < self::DURATION_ROUND_THRESHOLD_MILISECONDS)
		{
			$durationMs -= $durationFrac;
		}
		return $durationMs;
	}

	/**
	 * checking whether we currently inside "interruptible" window of the event and if a "real" live stream is streaming to the entry right now.
	 * if so - the event should be interrupted by the "real" live 
	 * @param LiveEntry $entry
	 * @param ILiveStreamScheduleEvent $event
	 * @return bool
	 */
	public static function shouldLiveInterrupt (LiveEntry $entry, ILiveStreamScheduleEvent $event)
	{
		return $event->isInterruptibleNow() && $entry->getEntryServerNodeStatusForPlayback() === EntryServerNodeStatus::PLAYABLE;
	}

	/**
	 * @param ILiveStreamScheduleEvent $event
	 * @param int $time
	 * @return int|null - the time of the future closest transition timestamp that comes after $time, if there isn't such transition time - return null
	 */
	public static function getClosestPlaybackTransitionTime($event, $time)
	{
		$eventTransitionTimes = $event->getEventTransitionTimes();
		// find the first closest future transition time
		foreach ($eventTransitionTimes as $transitionTime)
		{
			if ($time < $transitionTime)
			{
				return $transitionTime;
			}
		}
		// we shouldn't arrive this if $time is inside event
		return null;
	}

	/**
	 * receiving array of entries, returning 3 asset arrays (flavors, captions, audio) 
	 * s.t each array's i'th element is the asset array of the i'th entry assets.
	 * @param array $sourceEntries
	 * @return array
	 */
	public static function getSourceAssets($sourceEntries)
	{
		$entriesFlavorAssets = array();
		$entriesCaptionAssets = array();
		$entriesAudioAssets = array();
		foreach ($sourceEntries as $srcEntry)
		{
			list($mainFlavorAssets, $mainCaptionAssets, $mainAudioAssets) = myEntryUtils::getEntryAssets($srcEntry);
			$entriesFlavorAssets[] = $mainFlavorAssets;
			$entriesCaptionAssets[] = $mainCaptionAssets;
			$entriesAudioAssets[] = $mainAudioAssets;
		}
		return array($entriesFlavorAssets, $entriesCaptionAssets, $entriesAudioAssets);
	}

	/**
	 * receiving array of entries and event. returning array of durations s.t the i'th element is the duration of the 
	 * i'th entry. if accumulated duration is exceeding the event's duration - the appropriate duration will be shorten
	 * in accordance, and all the durations after will be shorten to '1'
	 * @param array $sourceEntries
	 * @param ILiveStreamScheduleEvent $event
	 * @return array
	 */
	public static function getSourceDurations($sourceEntries, $event)
	{
		$durations = array();
		$eventDuration = $event->getCalculatedEndTime() * self::SECOND_IN_MILLISECONDS - $event->getCalculatedStartTime() * self::SECOND_IN_MILLISECONDS;
		$aggregatedDuration = 0;
		foreach ($sourceEntries as $srcEntry)
		{
			$entryRoundedDuration = self::roundDuration($srcEntry->getLengthInMsecs());
			$aggregatedDuration += $entryRoundedDuration;
			if ($aggregatedDuration >= $eventDuration)
			{
				$durations[] = max($entryRoundedDuration - ($aggregatedDuration - $eventDuration), 1); // 1 as 0 is not valid for the packager
			} else
			{
				$durations[] = $entryRoundedDuration;
			}
		}
		return $durations;
	}

	/**
	 * @param $labels
	 * @param $startTime
	 * @param $durations
	 * @return void
	 */
	public static function addTimestamps(&$labels, $startTime, $durations)
	{
		$timestamp = $startTime;
		$labels[0] .= self::LABEL_SEPERATOR .$timestamp;
		for ($i = 1; $i < count($labels); $i++)
		{
			$timestamp = $timestamp + $durations[$i-1];
			$labels[$i] .=  self::LABEL_SEPERATOR . $timestamp;
		}
	}

	/**
	 * validating whether offseted playback is allowed (if got admin ks)
	 * @param string $ksString
	 * @throws Exception
	 * @return bool
	 */
	public static function isOffsetPlaybackAllowed($ksString)
	{
		try
		{
			$ks = kSessionUtils::crackKs($ksString);
			return $ks->isAdmin();
		}
		catch (Exception $e)
		{
			KExternalErrors::dieError(KExternalErrors::INVALID_KS);
		}
	}
}
