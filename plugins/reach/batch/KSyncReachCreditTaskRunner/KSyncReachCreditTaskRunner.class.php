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
		$filter = new KalturaVendorProfileFilter();
		$filter->statusEqual = KalturaVendorProfileStatus::ACTIVE;
		$pager = new KalturaFilterPager();
		$pager->pageIndex = 1;
		$pager->pageSize = 500;

		$result = $reachClient->vendorProfile->listAction($filter, $pager);
		while ($result->totalCount > 0)
		{
			foreach ($result->objects as $vendorProfile)
			{
				try
				{
					$this->syncVendorProfileCredit($vendorProfile);
				} catch (Exception $ex)
				{
					KalturaLog::err($ex);
				}
			}
			$pager->pageIndex++;
			$result = $reachClient->vendorProfile->listAction($filter, $pager);
		}
	}

	/**
	 * @param KalturaVendorProfile $vendorProfile
	 */
	protected function syncVendorProfileCredit(KalturaVendorProfile $vendorProfile)
	{
		$reachClient = $this->SyncReachClient();
		$this->impersonate($vendorProfile->partnerId);
		try
		{
			$result = $reachClient->vendorProfile->syncCredit($vendorProfile->id);
			$this->unimpersonate();
		} catch (Exception $ex)
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
