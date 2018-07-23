<?php

/**
 * @package plugins.scheduledTask
 * @subpackage lib.processors
 */
class KGenericProcessor
{
	/**
	 * @var KScheduledTaskRunner
	 */
	protected $taskRunner;

	public function __construct(KScheduledTaskRunner $taskRunner)
	{
		$this->taskRunner = $taskRunner;
	}

	/**
	 * @param KalturaScheduledTaskProfile $profile
	 */
	public function processProfile(KalturaScheduledTaskProfile $profile)
	{
		$this->taskRunner->impersonate($profile->partnerId);
		try
		{
			$maxTotalCountAllowed = $this->preProcess($profile);
			$objectsData = $this->handleProcess($profile, $maxTotalCountAllowed);
			$this->postProcess($profile, $objectsData);

		} catch (Exception $ex)
		{
			$this->taskRunner->unimpersonate();
			throw $ex;
		}
		$this->taskRunner->unimpersonate();
	}

	protected function postProcess($profile, $objectsData)
	{
		//Nothing To Do in case of Generic.
	}

	protected function additionalActions($profile, $object, $tasksCompleted, $error)
	{
		//Nothing To Do in case of Generic.
	}

	/**
	 * @param KalturaScheduledTaskProfile $profile
	 * @return int
	 */
	protected function preProcess(KalturaScheduledTaskProfile $profile)
	{
		$this->updateProfileBeforeExecution($profile);
		if ($profile->maxTotalCountAllowed)
			$maxTotalCountAllowed = $profile->maxTotalCountAllowed;
		else
			$maxTotalCountAllowed = $this->taskRunner->getParams('maxTotalCountAllowed');

		return $maxTotalCountAllowed;
	}

	protected static function startsWith($haystack, $needle)
	{
		$length = strlen($needle);
		return (substr($haystack, 0, $length) === $needle);
	}

	protected static function getUpdateDay($waitDays = 0) {
		$now = intval(time() / 86400);  // as num of sec in day to get day number
		return $now - $waitDays;
	}

	/**
	 * Moves the profile to suspended status
	 *
	 * @param KalturaScheduledTaskProfile $profile
	 */
	protected function suspendProfile(KalturaScheduledTaskProfile $profile)
	{
		$scheduledTaskClient = $this->taskRunner->getScheduledTaskClient();
		$profileForUpdate = new KalturaScheduledTaskProfile();
		$profileForUpdate->status = KalturaScheduledTaskProfileStatus::SUSPENDED;
		$scheduledTaskClient->scheduledTaskProfile->update($profile->id, $profileForUpdate);
		KalturaLog::alert("Media Repurposing profile [$profile->id] has been suspended");
	}

	/**
	 * @param string $userId
	 * @return null|string
	 */
	protected function getMailFromUserId($userId)
	{
		$result = null;
		$user = null;
		$client = $this->taskRunner->getClient();
		try
		{
			$user = $client->user->get($userId);
		}
		catch ( Exception $e )
		{
			KalturaLog::err( $e );
			return null;
		}

		if($user && $user->email)
			$result = $user->email;
		else if (filter_var($userId, FILTER_VALIDATE_EMAIL))
			$result = $userId;

		return $result;
	}

	/**
	 * @param KalturaScheduledTaskProfile $profile
	 * @param $object
	 */
	protected function processObject(KalturaScheduledTaskProfile $profile, $object)
	{
		$tasksCompleted = array();
		$error = false;
		foreach($profile->objectTasks as $objectTask)
		{
			if ($objectTask->type == ObjectTaskType::MAIL_NOTIFICATION)
				continue; //no execute on object
			/** @var KalturaObjectTask $objectTask */
			$objectTaskEngine = $this->getObjectTaskEngineByType($objectTask->type);
			$objectTaskEngine->setObjectTask($objectTask);
			try
			{
				$objectTaskEngine->execute($object);
				$tasksCompleted[] = $objectTask->type;
			}
			catch(Exception $ex)
			{
				$id = '';
				if (property_exists($object, 'id'))
					$id = $object->id;

				KalturaLog::err(sprintf('An error occurred while executing %s on object %s (id %s)', get_class($objectTaskEngine), get_class($object), $id));
				KalturaLog::err($ex);
				$error = true;

				if ($objectTask->stopProcessingOnError)
				{
					KalturaLog::log('Object task is configured to stop processing on error');
					break;
				}
			}
		}

		return array($error, $tasksCompleted);
	}

	/**
	 * @param $type
	 * @return KObjectTaskEngineBase
	 */
	protected function getObjectTaskEngineByType($type)
	{
		if (!isset($this->taskRunner->_objectEngineTasksCache[$type]))
		{
			$objectTaskEngine = KObjectTaskEngineFactory::getInstanceByType($type);
			$objectTaskEngine->setClient($this->taskRunner->getClient());
			$this->taskRunner->_objectEngineTasksCache[$type] = $objectTaskEngine;
		}

		return $this->taskRunner->_objectEngineTasksCache[$type];
	}

	/**
	 * Update the profile last execution time so we would have profiles rotation in case one execution dies
	 *
	 * @param KalturaScheduledTaskProfile $profile
	 */
	protected function updateProfileBeforeExecution(KalturaScheduledTaskProfile $profile)
	{
		$scheduledTaskClient = $this->taskRunner->getScheduledTaskClient();
		$profileForUpdate = new KalturaScheduledTaskProfile();
		$profileForUpdate->lastExecutionStartedAt = time();
		$scheduledTaskClient->scheduledTaskProfile->update($profile->id, $profileForUpdate);
	}

	protected function getPartnerMail($partnerId)
	{
		$client = $this->taskRunner->getClient();
		$res = $client->partner->get($partnerId);
		return $res->adminEmail;
	}

	/**
	 * @param KalturaScheduledTaskProfile $profile
	 * @param $maxTotalCountAllowed
	 * @param $errorObjectsIds
	 * @param $objectsData
	 * @return mixed
	 */
	protected function handleProcess(KalturaScheduledTaskProfile $profile, $maxTotalCountAllowed)
	{
		$objectsData = array();
		$errorObjectsIds = array();

		$pager = new KalturaFilterPager();
		$pager->pageIndex = 1;
		$pager->pageSize = 500;
		while (true)
		{
			$result = ScheduledTaskBatchHelper::query($this->taskRunner->getClient(), $profile, $pager);
			if ($result->totalCount > $maxTotalCountAllowed)
			{
				KalturaLog::crit("List query for profile $profile->id returned too many results ($result->totalCount when the allowed total count is $maxTotalCountAllowed), suspending the profile");
				$this->suspendProfile($profile);
				break;
			}

			if (!count($result->objects))
				break;

			foreach ($result->objects as $object)
			{
				list($error, $objectsData, $tasksCompleted) = $this->handleObject($profile, $object, $errorObjectsIds, $objectsData);

				$this->additionalActions($profile, $object, $tasksCompleted, $error);
			}
			$this->handlePager($pager);
		}
		return $objectsData;
	}

	protected function handlePager($pager)
	{
		$pager->pageIndex++;
	}

	/**
	 * @param KalturaScheduledTaskProfile $profile
	 * @param $object
	 * @param $errorObjectsIds
	 * @param $objectsData
	 * @return array
	 */
	protected function handleObject(KalturaScheduledTaskProfile $profile, $object, $errorObjectsIds, $objectsData)
	{
		list($error, $tasksCompleted) = $this->processObject($profile, $object);
		if ($error)
			$errorObjectsIds[] = $object->id;
		else if ($object instanceof KalturaBaseEntry)
		{
			if (!array_key_exists($object->userId, $objectsData))
			{
				$email = $this->getMailFromUserId($object->userId);
				$objectsData[$object->userId] = array(KObjectTaskMailNotificationEngine::EMAIL => $email);
			}

			$idAndName = array(KObjectTaskMailNotificationEngine::ENTRY_ID => $object->id, KObjectTaskMailNotificationEngine::ENTRY_NAME => $object->name);
			$objectsData[$object->userId][KObjectTaskMailNotificationEngine::ENTRIES_ID_AND_NAME][] = $idAndName;
		}
		return array($error, $objectsData, $tasksCompleted);

		//Stoped here
	}
}

