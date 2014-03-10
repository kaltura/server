<?php
/**
 * @package Scheduler
 * @subpackage ValidateLiveMediaServers
 */

/**
 * Validates periodically that all live entries are still broadcasting to the connected media servers
 *
 * @package Scheduler
 * @subpackage ValidateLiveMediaServers
 */
class KAsyncValidateLiveMediaServers extends KPeriodicWorker
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
		KalturaLog::info("Validating live media servers");
		
		$filter = new KalturaLiveStreamEntryFilter();
		$filter->isLive = KalturaNullableBoolean::TRUE_VALUE;
		$filter->orderBy = KalturaLiveStreamEntryOrderBy::CREATED_AT_ASC;
		
		$filter->moderationStatusIn = 
			KalturaEntryModerationStatus::PENDING_MODERATION . ',' .
			KalturaEntryModerationStatus::APPROVED . ',' .
			KalturaEntryModerationStatus::REJECTED . ',' .
			KalturaEntryModerationStatus::FLAGGED_FOR_REVIEW . ',' .
			KalturaEntryModerationStatus::AUTO_APPROVED;
		
		$pager = new KalturaFilterPager();
		$pager->pageSize = 500;
		$pager->pageIndex = 1;
		
		$entries = self::$kClient->liveStream->listAction($filter, $pager);
		while(count($entries->objects))
		{
			foreach($entries->objects as $entry)
			{
				/* @var $entry KalturaLiveEntry */
				self::impersonate($entry->partnerId);
				self::$kClient->liveStream->validateRegisteredMediaServers($entry->id);
				self::unimpersonate();
				
				$filter->createdAtGreaterThanOrEqual = $entry->createdAt;
			}
			
			$pager->pageIndex++;
			$entries = self::$kClient->liveStream->listAction($filter, $pager);
		}
	}
}
