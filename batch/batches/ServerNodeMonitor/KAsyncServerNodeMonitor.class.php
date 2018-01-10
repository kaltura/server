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
		$filter->statusEqual=KalturaServerNodeStatus::ACTIVE;
		$filter->typeIn=self::$taskConfig->params->typesToMonitor;
		$pager = new KalturaFilterPager();
		$pager->pageSize=1000;
		$pager->pageIndex = 1;
		$serverNodes = self::$kClient->serverNode->listAction($filter, $pager);
		while (count($serverNodes->objects))
		{
			foreach ($serverNodes->objects as $serverNode)
			{
				/**
				 * @var KalturaEdgeServerNode $serverNode
				 */
				if ($serverNode->heartbeatTime < (time() - self::$taskConfig->params->serverNodeTTL))
				{
					KalturaLog::info("ServerNode [" . $serverNode->id . "] is offline");
					self::$kClient->serverNode->unregister($serverNode->id);
				}
			}

			$pager->pageIndex++;
			$serverNodes = self::$kClient->serverNode->listAction($filter, $pager);
		}
	}
}
