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
	const ENTRY_SERVER_NODE_MIN_CREATION_TIMEE = 120;
	
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
		$entryServerNodeMinCreationTime = $this->getAdditionalParams("minCreationTime");
		if(!$entryServerNodeMinCreationTime)
			$entryServerNodeMinCreationTime = self::ENTRY_SERVER_NODE_MIN_CREATION_TIMEE;
		
		$entryServerNodeFilter = new KalturaEntryServerNodeFilter();
		$entryServerNodeFilter->orderBy = KalturaEntryServerNodeOrderBy::CREATED_AT_ASC;
		$entryServerNodeFilter->createdAtLessThanOrEqual = time() - $entryServerNodeMinCreationTime;
		
		$entryServerNodeFilter->statusIn = KalturaEntryServerNodeStatus::PLAYABLE . ',' . 
				KalturaEntryServerNodeStatus::BROADCASTING . ',' .
				KalturaEntryServerNodeStatus::AUTHENTICATED;
		
		$entryServerNodePager = new KalturaFilterPager();
		$entryServerNodePager->pageSize = 500;
		$entryServerNodePager->pageIndex = 1;
		
		$entryServerNodes = self::$kClient->entryServerNode->listAction($entryServerNodeFilter, $entryServerNodePager);
		while(count($entryServerNodes->objects))
		{
			$entryIds = '';
			foreach ($entryServerNodes->objects as $entryServerNode)
				$entryIds .= $entryServerNode->entryId . ',';
			
			$entryFilter = new KalturaLiveStreamEntryFilter();
			$entryFilter->idIn = $entryIds;
			
			$entries = self::$kClient->liveStream->listAction($entryFilter);
			foreach($entries->objects as $entry)
			{
				try
				{
					/* @var $entry KalturaLiveEntry */
					self::impersonate($entry->partnerId);
					self::$kClient->liveStream->validateRegisteredMediaServers($entry->id);
					self::unimpersonate();
				}
				catch (KalturaException $e)
				{
					self::unimpersonate();
					KalturaLog::err("Caught exception with message [" . $e->getMessage()."]");
				}
			}
			
			$entryServerNodePager->pageIndex++;
			$entryServerNodes = self::$kClient->entryServerNode->listAction($entryServerNodeFilter, $entryServerNodePager);
		}
	}
}
