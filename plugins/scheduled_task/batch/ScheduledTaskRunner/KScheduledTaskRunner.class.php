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
			try
			{
				$this->processProfile($profile);
			}
			catch(Exception $ex)
			{
				KalturaLog::err($ex);
			}
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
		$this->updateProfileBeforeExecution($profile);

		$pager = new KalturaFilterPager();
		$pager->pageIndex = 1;
		$pager->pageSize = 500;
		while(true)
		{
			$result = ScheduledTaskBatchHelper::query($this->getClient(), $profile, $pager);
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
			try
			{
				$objectTaskEngine->execute($object);
			}
			catch(Exception $ex)
			{
				$id = '';
				if (property_exists($object, 'id'))
					$id = $object->id;
				KalturaLog::err(sprintf('An error occurred while executing %s on object %s (id %s)', get_class($objectTaskEngine), get_class($object), $id));
				KalturaLog::err($ex);
			}
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
	 * Update the profile last execution time so we would have profiles rotation in case one execution dies
	 *
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
