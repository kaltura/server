<?php

/**
 * Will hold helper functions for simulive usage
 */
class kSimuliveUtils
{
	const MINUTE_TO_MS = 60000;
	const SIMULIVE_SCHEDULE_MARGIN = 2;
	/**
	 * @param LiveEntry $entry
	 * @param bool $pathOnly
	 * @return array
	 */
	public static function serveSimuliveAsLiveStream(LiveEntry $entry, $pathOnly)
	{
		$dvrWindow = $entry->getDvrWindow() * self::MINUTE_TO_MS;
		$currentEvent = kSimuliveUtils::getSimuliveEvent($entry, null, $dvrWindow);
		if (!$currentEvent)
		{
			return null;
		}

		/* @var $currentEvent ILiveStreamScheduleEvent */
		$sourceEntry = BaseentryPeer::retrieveByPK($currentEvent->getSourceEntryId());
		$durations[] = $sourceEntry->getLengthInMsecs();
		$startTime = $currentEvent->getStartTime();
		$endTime = min($currentEvent->getEndTime(), $currentEvent->getStartTime() + intval(array_sum($durations) / serveFlavorAction::SECOND_IN_MILLISECONDS));
		$sequences = self::buildSimuliveSequencesArray($sourceEntry, $pathOnly);
		return serveFlavorAction::serveLiveMediaSet($durations, $sequences, $startTime, $startTime,
			null, null, true, true, $dvrWindow, $endTime);
	}

	/**
	 * @param Entry $sourceEntry
	 * @param bool $pathOnly
	 * @return array
	 */
	public static function buildSimuliveSequencesArray(Entry $sourceEntry, $pathOnly)
	{
		$sequences = array();
		// getting the flavors from source entry
		$flavors = assetPeer::retrieveReadyFlavorsByEntryId($sourceEntry->getId());
		foreach ($flavors as $flavor)
		{
			$syncKey = $flavor->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			list ($file_sync, $path, $sourceType) = kFileSyncUtils::getFileSyncServeFlavorFields($syncKey, $flavor, serveFlavorAction::getPreferredStorageProfileId(), serveFlavorAction::getFallbackStorageProfileId(), $pathOnly);
			if(!$path)
			{
				KalturaLog::debug('missing path for flavor ' . $flavor->getId() . ' version ' . $flavor->getVersion());
				continue;
			}
			$sequences[] = array('clips' => serveFlavorAction::getClipData($path, $flavor, $sourceType));
		}
		return $sequences;
	}

	/**
	 * @param Entry $entry
	 * @param int $endTime - epoch time
	 * @param int $duration - in ms
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
			$endTime += self::SIMULIVE_SCHEDULE_MARGIN;
			$startTime = $endTime - $duration - self::SIMULIVE_SCHEDULE_MARGIN;
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
