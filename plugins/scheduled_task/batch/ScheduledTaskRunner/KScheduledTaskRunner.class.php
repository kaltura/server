<?php
/**
 * @package plugins.scheduledTask
 * @subpackage Scheduler
 */
class KScheduledTaskRunner extends KPeriodicWorker
{

	private static $dontUpdateMetaDataTaskTypes = array (KalturaObjectTaskType::DELETE_ENTRY);
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

		$profiles = $this->getScheduledTaskProfiles($maxProfiles);
		foreach($profiles as $profile)
		{
			try
			{

				$processor = $this->getProcessor($profile);
				$processor->processProfile($profile);
			}
			catch(Exception $ex)
			{
				KalturaLog::err($ex);
			}
		}
	}

	protected function getProcessor($profile)
	{
		if ($this->isReachProfile($profile))
			return new KReachProcessor($this);
		if ($this->isMediaRepurposingProfile($profile))
			return new KMediaRepurposingProcessor($this);
		else
			return new KGenericProcessor($this);
	}

	private function isMediaRepurposingProfile(KalturaScheduledTaskProfile $profile)
	{
		return ($profile->systemName == "MRP") || (kstring::beginsWith($profile->name, 'MR_'));
	}

	private function isReachProfile(KalturaScheduledTaskProfile $profile)
	{
		return $profile->objectFilterEngineType == ObjectFilterEngineType::ENTRY_VENDOR_TASK;
	}

	/**
	 * @param int $maxProfiles
	 * @return array
	 */
	protected function getScheduledTaskProfiles($maxProfiles = 500)
	{
		$scheduledTaskClient = $this->getScheduledTaskClient();

		$filter = new KalturaScheduledTaskProfileFilter();
		$filter->orderBy = KalturaScheduledTaskProfileOrderBy::LAST_EXECUTION_STARTED_AT_ASC;
		$filter->statusEqual = KalturaScheduledTaskProfileStatus::ACTIVE;

		$pager = new KalturaFilterPager();
		$pager->pageSize = $maxProfiles;

		$result = $scheduledTaskClient->scheduledTaskProfile->listAction($filter, $pager);

		return $result->objects;
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
