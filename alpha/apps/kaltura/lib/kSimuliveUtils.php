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
	const MOVING_WINDOW_MARGIN_MS = 30000;
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

		$playlistStartTime = $currentEvent->getCalculatedStartTime() * self::SECOND_IN_MILLISECONDS;
		$sourceEntries = $sourceEntry->getType() == entryType::PLAYLIST ? myPlaylistUtils::retrieveStitchedPlaylistEntries($sourceEntry) : array($sourceEntry);

		$preStartEntry = kSimuliveUtils::getPreStartEntry($currentEvent);
		if ($preStartEntry)
		{
			array_unshift($sourceEntries, $preStartEntry);
		}

		$postEndEntry = kSimuliveUtils::getPostEndEntry($currentEvent);
		if ($postEndEntry)
		{
			$sourceEntries[] = $postEndEntry;
		}
		list ($sourceEntries, $windowStartTime, $durations, $initialClipIndex, $initialSegmentIndex) = 
			self::getRelevantEntriesDetails($sourceEntries, $currentEvent, $time * self::SECOND_IN_MILLISECONDS, $dvrWindowMs, $entry->getSegmentDuration());

		list($entriesFlavorAssets, $entriesCaptionAssets, $entriesAudioAssets) = self::getSourceAssets($sourceEntries);

		$endTime = $windowStartTime + array_sum($durations);

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
		return array($durations, $assets, $windowStartTime, $endTime, $dvrWindowMs, $initialClipIndex, $initialSegmentIndex, $playlistStartTime);
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
	 * returning the rounded duration of the entry in miliseconds
	 * @param Entry $entry
	 * @return int
	 */
	protected static function getEntryRoundedDuration ($entry)
	{
		return !$entry ? 0 : self::roundDuration($entry->getLengthInMsecs());
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

	/**
	 * receiving array of all the source entries (including preStart and postEnd), returning the details of the
	 * the relevant entries (the past entries that inside the DVR window or the future entries that should start soon)
	 * @param array $entries
	 * @param ILiveStreamScheduleEvent $event
	 * @param $requestedTime
	 * @param $dvrWindowMs
	 * @param $segmentDurationMs
	 * @return array
	 */
	protected static function getRelevantEntriesDetails(array $entries, ILiveStreamScheduleEvent $event, $requestedTime, $dvrWindowMs, $segmentDurationMs)
	{
		$entriesList = array();
		// the absolute time of the end of postEnd
		$postEndAbsTime = $event->getCalculatedEndTime() * self::SECOND_IN_MILLISECONDS;
		$windowStartTime = $event->getCalculatedStartTime() * self::SECOND_IN_MILLISECONDS;
		$initialClipIndex = 1;
		$initialSegmentIndex = ceil($windowStartTime / $segmentDurationMs);
		// the absolute time of dvr start (including margin)
		$marginedDvrStartTime = $requestedTime - $dvrWindowMs - self::MOVING_WINDOW_MARGIN_MS;

		// removing the past entries (which are not inside the margined DVR window)
		while (count($entries) && (($windowStartTime + self::getEntryRoundedDuration($entries[0])) < $marginedDvrStartTime))
		{
			$currentEntryDuration = self::getEntryRoundedDuration($entries[0]);
			$windowStartTime += $currentEntryDuration;
			array_shift($entries);
			$initialClipIndex++;
			$initialSegmentIndex += ceil($currentEntryDuration / $segmentDurationMs);
		}

		// the end time of the last entry inside the moving window
		$lastEntryEndTime = $windowStartTime;
		$endWindow = min($requestedTime + self::MOVING_WINDOW_MARGIN_MS, $postEndAbsTime);

		// adding past entries that inside the margined dvr window or future entries that should start soon
		while (count($entries) && $lastEntryEndTime <= $endWindow)
		{
			$lastEntryEndTime += self::getEntryRoundedDuration($entries[0]);
			$entriesList[] = array_shift($entries);
		}

		$durations = array_map(function (entry $entry) {
			return self::getEntryRoundedDuration($entry);
		}, $entriesList);

		// if the content end is close
		if (($requestedTime + self::MOVING_WINDOW_MARGIN_MS >= $postEndAbsTime) && count($durations))
		{
			// chop the last entry to fit the event duration
			$durations[count($durations) - 1] -= self::roundDuration($lastEntryEndTime - $postEndAbsTime);
		}
		return array($entriesList, $windowStartTime, $durations, $initialClipIndex, $initialSegmentIndex);
	}
}
