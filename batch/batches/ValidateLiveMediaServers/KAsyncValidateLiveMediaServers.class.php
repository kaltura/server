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
		
		$liveEntryServerNodeFilter = new KalturaLiveEntryServerNodeFilter();
		$liveEntryServerNodeFilter->orderBy = KalturaEntryServerNodeOrderBy::CREATED_AT_ASC;
		$liveEntryServerNodeFilter->createdAtLessThanOrEqual = time() - $entryServerNodeMinCreationTime;
		$liveEntryServerNodeFilter->currentDcOnly = true;
		
		$liveEntryServerNodeFilter->statusIn = KalturaEntryServerNodeStatus::PLAYABLE . ',' . 
				KalturaEntryServerNodeStatus::BROADCASTING . ',' .
				KalturaEntryServerNodeStatus::AUTHENTICATED;
		
		$liveEntryServerNodePager = new KalturaFilterPager();
		$liveEntryServerNodePager->pageSize = 500;
		$liveEntryServerNodePager->pageIndex = 1;
		
		$liveEntryServerNodes = self::$kClient->entryServerNode->listAction($liveEntryServerNodeFilter, $liveEntryServerNodePager);
		while(count($liveEntryServerNodes->objects))
		{
			foreach($liveEntryServerNodes->objects as $entryServerNode)
			{
				try
				{
					/* @var $entryServerNode KalturaEntryServerNode */
					self::impersonate($entryServerNode->partnerId);
					self::$kClient->entryServerNode->validateRegisteredEntryServerNode($entryServerNode->id);
					self::unimpersonate();
				}
				catch (KalturaException $e)
				{
					self::unimpersonate();
					KalturaLog::err("Caught exception with message [" . $e->getMessage()."]");
				}
			}
			
			$liveEntryServerNodePager->pageIndex++;
			$liveEntryServerNodes = self::$kClient->entryServerNode->listAction($liveEntryServerNodeFilter, $liveEntryServerNodePager);
		}
	}
}
