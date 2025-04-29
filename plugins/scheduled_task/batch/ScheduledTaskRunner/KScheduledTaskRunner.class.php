<?php
/**
 * @package plugins.scheduledTask
 * @subpackage Scheduler
 */
class KScheduledTaskRunner extends KPeriodicWorker
{
	const runnerTypes = 'runnerTypes';
	
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

	protected function getMrIndexPerPartner($partnerId)
	{
		$index = $partnerId % $this->getParams('mrNumberOfWorkers');
		if ($index < 0)
		{
			$index += 10;
		}
		return $index;
	}

	/* (non-PHPdoc)
	 * @see KBatchBase::run()
	*/
	public function run($jobs = null)
	{
		$maxProfiles = $this->getParams('maxProfiles');
		$mrIndex = $this->getIndex();
		$lastRuntimePerPartner = array();
		$profiles = $this->getSortedScheduledTaskProfiles($maxProfiles);
		/** @var KalturaScheduledTaskProfile $profile */
		$profile = $this->getNextProfile($profiles);
		while( $profile )
		{
			$mrIndexPerPartner = $this->getMrIndexPerPartner($profile->partnerId);
			if ($mrIndexPerPartner == $mrIndex)
			{
				//make sure a profile for the same partner runs in a minimum of 2 seconds diff
				KalturaLog::notice('partnerId [' . $profile->partnerId . '] index [' . $mrIndex . '] mrIndexPerPartner [' . $mrIndexPerPartner . ']');
				if (isset($lastRuntimePerPartner[$profile->partnerId]) && time() - $lastRuntimePerPartner[$profile->partnerId] <= 2)
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
				$lastRuntimePerPartner[$profile->partnerId] = time();
				$profile = $this->getNextProfile($profiles);
			}
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
		
		$runnerTypes = $this->getAdditionalParams(KScheduledTaskRunner::runnerTypes);
		if ($runnerTypes)
		{
			$filter->objectFilterEngineTypeIn = $runnerTypes;
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
