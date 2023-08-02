<?php
/**
 * @package plugins.scheduledTask
 * @subpackage Scheduler
 */
class KScheduledTaskRunner extends KPeriodicWorker
{
	const runnerType = 'runnerType';
	
	/**
	 * @var array
	 */
	public $_objectEngineTasksCache;

	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::SCHEDULED_TASK;
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
		$maxProfiles = $this->getParams('maxProfiles');
		if (!$maxProfiles)
		{
			$maxProfiles = 10;
		}
		$lastRuntimePerPartner = array();
		$runnerType = $this->getAdditionalParams(KScheduledTaskRunner::runnerType);
		if (!isset($runnerType))
		{
			$runnerType = 0;
		}
		$scheduledTaskClient = $this->getScheduledTaskClient();
		$profile = $scheduledTaskClient->scheduledTaskProfile->getExclusiveTask($runnerType);
		if (!$profile)
		{
			KalturaLog::info('No scheduled task profiles available for this runner');
			return;
		}
		
		/** @var KalturaScheduledTaskProfile $profile */
		$handledProfiles = 1;
		while ($profile && $handledProfiles <= $maxProfiles)
		{
			KalturaLog::info("Processing scheduled task profile [$profile->id]");
			// Make sure a profile for the same partner runs in a minimum of 2 seconds diff
			if (isset($lastRuntimePerPartner[$profile->partnerId]) && time() - $lastRuntimePerPartner[$profile->partnerId] <= 2 )
			{
				sleep(2);
			}
			try
			{
				$processor = $this->getProcessor($profile);
				$processor->processProfile($profile);
			}
			catch (Exception $ex)
			{
				KalturaLog::err($ex);
			}
			$scheduledTaskClient->scheduledTaskProfile->freeExclusiveTask($profile->id);
			$lastRuntimePerPartner[$profile->partnerId] = time();
			$profile = $scheduledTaskClient->scheduledTaskProfile->getExclusiveTask($runnerType);
			$handledProfiles++;
		}
		if ($handledProfiles > $maxProfiles)
		{
			$scheduledTaskClient->scheduledTaskProfile->freeExclusiveTask($profile->id);
		}
	}

	/**
	 * @param $profiles
	 * @param null $previousKey
	 * @return mixed|null
	 */
	protected function getNextProfile(&$profiles)
	{
		if (empty($profiles))
		{
			return null;
		}

		if (count($profiles) == 1)
		{
			sleep(2);
		}

		$currentKey = key($profiles);
		if ($currentKey === null)
		{
			return null;
		}

		$profile = array_shift($profiles[$currentKey]);
		if (empty($profiles[$currentKey]))
		{
			unset($profiles[$currentKey]);
		}

		$res = next($profiles);
		if ($res === false)
		{
			reset($profiles);
		}
		return $profile;
	}

	/**
	 * @param $profiles
	 * @return array
	 */
	protected function sortProfiles($profiles)
	{
		$sorted = array();
		foreach ($profiles as $profile)
		{
			/** @var KalturaScheduledTaskProfile $profile */
			$sorted[$profile->partnerId][] = $profile;
		}
		return $sorted;
	}

	protected function getProcessor($profile)
	{
		if ($this->isReachProfile($profile))
		{
			return new KReachProcessor($this);
		}
		if ($this->isMediaRepurposingProfile($profile))
		{
			return new KMediaRepurposingProcessor($this);
		}
		if ($this->isRecycleBinProfile($profile))
		{
			return new KRecycleBinProcessor($this);
		}
		
		return new KGenericProcessor($this);
	}
	
	protected function isMediaRepurposingProfile(KalturaScheduledTaskProfile $profile)
	{
		return ($profile->systemName == "MRP") || (kString::beginsWith($profile->name, 'MR_'));
	}
	
	protected function isReachProfile(KalturaScheduledTaskProfile $profile)
	{
		return $profile->objectFilterEngineType == ObjectFilterEngineType::ENTRY_VENDOR_TASK;
	}
	
	protected function isRecycleBinProfile(KalturaScheduledTaskProfile $profile)
	{
		return $profile->objectFilterEngineType == ObjectFilterEngineType::RECYCLE_BIN_CLEANUP;
	}

	/**
	 * @param int $maxProfiles
	 * @return array
	 */
	protected function getSortedScheduledTaskProfiles($maxProfiles = 500)
	{
		$scheduledTaskClient = $this->getScheduledTaskClient();

		$filter = new KalturaScheduledTaskProfileFilter();
		$filter->orderBy = KalturaScheduledTaskProfileOrderBy::LAST_EXECUTION_STARTED_AT_ASC;
		$filter->statusEqual = KalturaScheduledTaskProfileStatus::ACTIVE;
		$filter->lastExecutionStartedAtLessThanOrEqualOrNull = kTimeZoneUtils::midnightTimezoneDateTime(time(),'UTC')->getTimestamp();
		$pager = new KalturaFilterPager();
		$pager->pageSize = $maxProfiles;
		
		$runnerType = $this->getAdditionalParams(KScheduledTaskRunner::runnerType);
		if ($runnerType)
		{
			$filter->objectFilterEngineTypeIn = $runnerType;
		}

		$result = $scheduledTaskClient->scheduledTaskProfile->listAction($filter, $pager);
		if (empty($result))
		{
			return array();
		}
		return $this->sortProfiles($result->objects);
	}

	/**
	 * @return KalturaScheduledTaskClientPlugin
	 */
	public function getScheduledTaskClient()
	{
		$client = $this->getClient();
		return KalturaScheduledTaskClientPlugin::get($client);
	}

	/**
	 * @return KalturaClient
	 */
	public function getClient()
	{
		return self::$kClient;
	}

	public function getParams($name)
	{
		return parent::getParams($name);
	}

}
