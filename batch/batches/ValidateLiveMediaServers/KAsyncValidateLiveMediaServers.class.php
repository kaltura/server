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
	const ENTRY_ID_FIELD = "entry_id";
	const PARTNER_ID_FIELD = "entry_id";
	
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
			$entryInfo = array();
			for($i=0; $i<count($entryServerNodes->objects); $i++)
			{
				$entryInfo[$i] = array();
				$entryInfo[$i][self::ENTRY_ID_FIELD] = $entryServerNode->entryId;
				$entryInfo[$i][self::PARTNER_ID_FIELD] = $entryServerNode->partnerId;
			}
			$uniqueEntryInfo = array_unique($entryInfo, SORT_REGULAR);
			
			foreach($uniqueEntryInfo as $entryInfo)
			{
				try
				{
					/* @var $entryServerNode KalturaEntryServerNode */
					self::impersonate($entryInfo[self::PARTNER_ID_FIELD]);
					self::$kClient->liveStream->validateRegisteredMediaServers($entryInfo[self::ENTRY_ID_FIELD]);
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
