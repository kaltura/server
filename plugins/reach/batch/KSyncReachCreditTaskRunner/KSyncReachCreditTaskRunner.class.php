<?php
/**
 * @package plugins.reach
 * @subpackage Scheduler
 */
class KSyncReachCreditTaskRunner extends KPeriodicWorker
{
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::SYNC_REACH_CREDIT_TASK;
	}

	/* (non-PHPdoc)
	 * @see KBatchBase::getJobType()
	 */
	public function getJobType()
	{
		return self::getType();
	}

	/* (non-PHPdoc)
	 * @see KBatchBase::run()
	*/
	public function run($jobs = null)
	{
		$reachClient = $this->SyncReachClient();
		$filter = new KalturaReachProfileFilter();
		$filter->statusEqual = KalturaReachProfileStatus::ACTIVE;
		$pager = new KalturaFilterPager();
		$pager->pageIndex = 1;
		$pager->pageSize = 500;
		
		
		do {
			$result = $reachClient->reachProfile->listAction($filter, $pager);
			foreach ($result->objects as $reachProfile)
			{
				try
				{
					$this->syncReachProfileCredit($reachProfile);
				}
				catch (Exception $ex)
				{
					KalturaLog::err($ex);
				}
			}
			
			$pager->pageIndex++;
		}  while(count($result->objects) == $pager->pageSize);
	}

	/**
	 * @param KalturaReachProfile $reachProfile
	 */
	protected function syncReachProfileCredit(KalturaReachProfile $reachProfile)
	{
		$reachClient = $this->SyncReachClient();
		$this->impersonate($reachProfile->partnerId);
		try
		{
			$result = $reachClient->reachProfile->syncCredit($reachProfile->id);
			$this->unimpersonate();
		}
		catch (Exception $ex)
		{
			$this->unimpersonate();
			throw $ex;
		}
	}

	/**
	 * @return KalturaReachClientPlugin
	 */
	protected function SyncReachClient()
	{
		$client = $this->getClient();
		return KalturaReachClientPlugin::get($client);
	}
}
