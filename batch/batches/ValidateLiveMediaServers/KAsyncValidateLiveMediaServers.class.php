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
	const ENTRY_SERVER_NODE_MIN_CREATION_TIME = 120;
	
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::CLEANUP;
	}

    protected function getFilter()
    {
        $entryServerNodeMinCreationTime = $this->getAdditionalParams("minCreationTime");
        if(!$entryServerNodeMinCreationTime)
            $entryServerNodeMinCreationTime = self::ENTRY_SERVER_NODE_MIN_CREATION_TIME;

        $entryServerNodeFilter = new KalturaEntryServerNodeFilter();
        $entryServerNodeFilter->orderBy = KalturaEntryServerNodeOrderBy::CREATED_AT_ASC;
        $entryServerNodeFilter->createdAtLessThanOrEqual = time() - $entryServerNodeMinCreationTime;

        $serverTypesNotIn = $this->getAdditionalParams("serverTypesNotIn");
        if ($serverTypesNotIn)
        {
            $entryServerNodeFilter->serverTypeNotIn = $serverTypesNotIn;
        }
        return $entryServerNodeFilter;
    }
	
	/* (non-PHPdoc)
	 * @see KBatchBase::run()
	*/
	public function run($jobs = null)
	{
		$entryServerNodeFilter = $this->getFilter();
		
		$entryServerNodePager = new KalturaFilterPager();
		$entryServerNodePager->pageSize = 500;
		$entryServerNodePager->pageIndex = 1;
		
		$entryServerNodes = self::$kClient->entryServerNode->listAction($entryServerNodeFilter, $entryServerNodePager);
		
		while($entryServerNodes->objects && count($entryServerNodes->objects))
		{
			foreach($entryServerNodes->objects as $entryServerNode)
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
			
			$entryServerNodePager->pageIndex++;
			$entryServerNodes = self::$kClient->entryServerNode->listAction($entryServerNodeFilter, $entryServerNodePager);
		}
	}
}
