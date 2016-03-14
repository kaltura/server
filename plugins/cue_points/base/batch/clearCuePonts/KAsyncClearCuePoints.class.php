<?php
/**
 * @package Scheduler
 * @subpackage ClearCuePoints
 */

/**
 * Clear cue points from live entries that were not marked as handled (cases were recording is off)
 *
 * @package Scheduler
 * @subpackage ClearCuePoints
 */
class KAsyncClearCuePoints extends KPeriodicWorker
{	
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::CLEANUP;
	}
	
	/* (non-PHPdoc)
	 * @see KBatchBase::run()
	*/
	public function run($jobs = null)
	{
		$entryFilter = new KalturaLiveStreamEntryFilter();
		$entryFilter->isLive = KalturaNullableBoolean::TRUE_VALUE;
		$entryFilter->orderBy = KalturaLiveStreamEntryOrderBy::CREATED_AT_ASC;
		
		$entryFilter->moderationStatusIn = 
			KalturaEntryModerationStatus::PENDING_MODERATION . ',' .
			KalturaEntryModerationStatus::APPROVED . ',' .
			KalturaEntryModerationStatus::REJECTED . ',' .
			KalturaEntryModerationStatus::FLAGGED_FOR_REVIEW . ',' .
			KalturaEntryModerationStatus::AUTO_APPROVED;
		
		$pager = new KalturaFilterPager();
		$pager->pageSize = 100;
		$pager->pageIndex = 1;
		
		$entries = self::$kClient->liveStream->listAction($entryFilter, $pager);
		
		while(count($entries->objects))
		{
			foreach($entries->objects as $entry)
			{
				//When entry has recording on the cue poitns are copied from the live entry to the vod entry
				//The copy process allready markes the live entry cue points as handled
				/* @var $entry KalturaLiveEntry */
				if($entry->recordStatus !== KalturaRecordStatus::DISABLED)
					continue;
					
				$this->clearEntryCuePoints($entry);
			}
			
			$pager->pageIndex++;
			$entries = self::$kClient->liveStream->listAction($entryFilter, $pager);
		}
	}
	
	private function clearEntryCuePoints($entry)
	{
		$cuePointPlugin = KalturaCuePointClientPlugin::get(self::$kClient);
		
		$cuePointFilter = $this->getFilter("KalturaCuePointFilter");
		$cuePointFilter->entryIdEqual = $entry->id;
		
		$pager = new KalturaFilterPager();
		$pager->pageSize = 100;
		
		$cuePoints = $cuePointPlugin->cuePoint->listAction($cuePointFilter, $pager);

		if(!$cuePoints->objects)
		{
			KalturaLog::debug("No cue points found for entry [{$entry->id}] continue to next live entry");
			return;
		}

		//Clear Max 100 cue points each run on each live entry to avoid massive old cue points updates
		self::impersonate($entry->partnerId);
		self::$kClient->startMultiRequest();
		foreach ($cuePoints->objects as $cuePoint)
		{
			$cuePointPlugin->cuePoint->updateStatus($cuePoint->id, KalturaCuePointStatus::HANDLED);
		}
		self::$kClient->doMultiRequest();
		self::unimpersonate();
	}
}
