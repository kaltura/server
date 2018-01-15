<?php
/**
 * @package Scheduler
 * @subpackage ServerNodeMonitor
 */

/**
 * Will monitor server nodes and mark them NOT_REGISTERED if applicable
 *
 * @package Scheduler
 * @subpackage ServerNodeMonitor
 */
class KAsyncServerNodeMonitor extends KPeriodicWorker
{
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::SERVER_NODE_MONITOR;
	}
	
	/* (non-PHPdoc)
	 * @see KBatchBase::run()
	*/
	public function run($jobs = null)
	{
		$filter = new KalturaServerNodeFilter();
		$filter->typeIn=self::$taskConfig->params->typesToMonitor;
		$filter->heartbeatTimeLessThanOrEqual = (time() - self::$taskConfig->params->serverNodeTTL);
		$pager = new KalturaFilterPager();
		$pager->pageSize=500;
		$pager->pageIndex = 1;
		$serverNodes = self::$kClient->serverNode->listAction($filter, $pager);
		while (count($serverNodes->objects))
		{
			foreach ($serverNodes->objects as $serverNode)
			{
				/**
				 * @var KalturaEdgeServerNode $serverNode
				 */
				KalturaLog::info("ServerNode [" . $serverNode->id . "] is offline, last heartbeat [" . $serverNode->heartbeatTime . "]");
				self::$kClient->serverNode->markOffline($serverNode->id);
			}

			$pager->pageIndex++;
			$serverNodes = self::$kClient->serverNode->listAction($filter, $pager);
		}
	}
}
