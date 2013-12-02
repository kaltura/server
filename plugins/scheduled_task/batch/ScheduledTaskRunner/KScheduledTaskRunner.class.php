<?php
/**
 * @package plugins.scheduledTask
 * @subpackage Scheduler
 */
class KScheduledTaskRunner extends KPeriodicWorker
{
	/**
	 * @var array
	 */
	protected $_objectEngineTasksCache;

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
		KalturaLog::info("Scheduled Task Runner");
		$maxProfiles = $this->getParams('maxProfile');

		$profiles = $this->getScheduledTaskProfiles($maxProfiles);
		foreach($profiles as $profile)
		{
			$this->processProfile($profile);
		}
	}

	/**
	 * @param int $maxProfile
	 * @return array
	 */
	protected function getScheduledTaskProfiles($maxProfile = 500)
	{
		$scheduledTaskClient = $this->getScheduledTaskClient();

		$filter = new KalturaScheduledTaskProfileFilter();
		$filter->orderBy = KalturaScheduledTaskProfileOrderBy::LAST_EXECUTION_STARTED_AT_ASC;
		$filter->statusEqual = KalturaScheduledTaskProfileStatus::ACTIVE;

		$pager = new KalturaFilterPager();
		$pager->pageSize = $maxProfile;

		$result = $scheduledTaskClient->scheduledTaskProfile->listAction($filter, $pager);

		return $result->objects;
	}

	/**
	 * @param KalturaScheduledTaskProfile $profile
	 */
	protected function processProfile(KalturaScheduledTaskProfile $profile)
	{
		$scheduledTaskClient = $this->getScheduledTaskClient();
		//$this->updateProfileBeforeExecution($profile);

		$objectFilterEngineType = $profile->objectFilterEngineType;
		$objectFilterEngine = KObjectFilterEngineFactory::getInstanceByType($objectFilterEngineType, $this->getClient());
		if (is_null($objectFilterEngine))
			throw new Exception('Object filter engine was not found for type '.$objectFilterEngineType);

		$pager = new KalturaFilterPager();
		$pager->pageIndex = 1;
		$pager->pageSize = 500;
		while(true)
		{
			$this->impersonate($profile->partnerId);
			$result = $scheduledTaskClient->scheduledTaskProfile->query($profile->id, $pager);
			$this->unimpersonate();
			if (!count($result->objects))
				break;

			foreach($result->objects as $object)
			{
				$this->processObject($profile, $object);
			}

			$pager->pageIndex++;
		}
	}

	/**
	 * @param KalturaScheduledTaskProfile $profile
	 * @param $object
	 */
	protected function processObject(KalturaScheduledTaskProfile $profile, $object)
	{
		foreach($profile->objectTasks as $objectTask)
		{
			/** @var KalturaObjectTask $objectTask */
			$objectTaskEngine = $this->getObjectTaskEngineByType($objectTask->type);
			$objectTaskEngine->setObjectTask($objectTask);
			$objectTaskEngine->execute($object);
		}
	}

	/**
	 * @param $type
	 * @return KObjectTaskEngineBase
	 */
	protected function getObjectTaskEngineByType($type)
	{
		if (!isset($this->_objectEngineTasksCache[$type]))
		{
			$objectTaskEngine = KObjectTaskEngineFactory::getInstanceByType($type);
			$objectTaskEngine->setClient($this->getClient());
			$this->_objectEngineTasksCache[$type] = $objectTaskEngine;
		}

		return $this->_objectEngineTasksCache[$type];
	}

	/**
	 * @return KalturaScheduledTaskClientPlugin
	 */
	protected function getScheduledTaskClient()
	{
		$client = $this->getClient();
		return KalturaScheduledTaskClientPlugin::get($client);
	}

	/**
	 * @param KalturaScheduledTaskProfile $profile
	 */
	protected function updateProfileBeforeExecution(KalturaScheduledTaskProfile $profile)
	{
		$scheduledTaskClient = $this->getScheduledTaskClient();
		$profileForUpdate = new KalturaScheduledTaskProfile();
		$profileForUpdate->lastExecutionStartedAt = time();
		$this->impersonate($profile->partnerId);
		$scheduledTaskClient->scheduledTaskProfile->update($profile->id, $profileForUpdate);
		$this->unimpersonate();
	}
}
