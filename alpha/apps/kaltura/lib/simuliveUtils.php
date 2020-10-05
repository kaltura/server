<?php

/**
 * Will hold helper functions for simulive usage
 */
class simuliveUtils
{
	const MINUTE_TO_MS = 60000;
	/**
	 * @param LiveEntry $entry
	 * @param bool $pathOnly
	 * @return array
	 */
	public static function serveSimuliveAsLiveStream(LiveEntry $entry, $pathOnly)
	{
		$currentEvent = simuliveUtils::getCurrentSimuliveEvent($entry);
		if (!$currentEvent)
		{
			return null;
		}

		/* @var $currentEvent ILiveStreamScheduleEvent */
		$sourceEntry = BaseentryPeer::retrieveByPK($currentEvent->getSourceEntryId());
		$dvrWindow = $entry->getDvrWindow() * self::MINUTE_TO_MS;
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
	 * @return ILiveStreamScheduleEvent | null
	 */
	public static function getCurrentSimuliveEvent(Entry $entry)
	{
		if ($entry->hasCapability(LiveEntry::LIVE_SCHEDULE_CAPABILITY) && $entry->getType() == entryType::LIVE_STREAM)
		{
			/* @var $entry LiveEntry */
			foreach ($entry->getScheduleEvents() as $event)
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
