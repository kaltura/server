<?php

/**
 * Will hold helper functions for simulive usage
 */
class simuliveUtils
{
	/**
	 * @param LiveEntry $entry
	 * @param bool $pathOnly
	 * @return array
	 */
	public static function serveSimuliveAsLiveStream(LiveEntry $entry, $pathOnly)
	{
		$currentEvent = $entry->getEvent();
		if (!$currentEvent || is_null($currentEvent->getSourceEntryId()))
		{
			return null;
		}

		/* @var $currentEvent LiveStreamScheduleEvent */
		$sourceEntry = BaseentryPeer::retrieveByPK($currentEvent->getSourceEntryId());
		$dvrWindow = $entry->getDvrWindow() * 60 * 1000;
		$durations[] = $sourceEntry->getLengthInMsecs();
		$startTime = $currentEvent->getStartTime();
		$endTime = min($currentEvent->getEndTime(), $currentEvent->getStartTime() + intval(array_sum($durations) / 1000));
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
}
