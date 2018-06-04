<?php

/**
 * @package plugins.scheduledTask
 * @subpackage lib.processors
 */
class KReachProcessor extends KGenericProcessor
{
	/**
	 * @param KalturaScheduledTaskProfile $profile
	 */
	public function processProfile(KalturaScheduledTaskProfile $profile)
	{
		// To make sure that we run this task once a day.
		if ( self::wasHandledToday($profile) )
		{
			KalturaLog::info("Reach Scheduled Task Profile [$profile->id] was already handled today. No need to handle again");
			return;
		}

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
		else if ($object instanceof KalturaEntryVendorTask && $object->status == EntryVendorTaskStatus::PENDING_MODERATION)
				$objectsData[] = $object;

		return array($error, $objectsData, $tasksCompleted);
	}

	protected function postProcess($profile, $objectsData)
	{
		if ((self::getReachProfileTaskType($profile) == ObjectTaskType::MAIL_NOTIFICATION) && count($objectsData))
		{
			$client = $this->taskRunner->getClient();
			KReachMailNotificationEngine::sendMailNotification($profile->objectTasks[0], $objectsData, $profile->id, $profile->partnerId, $client);
		}
	}

	protected static function wasHandledToday(KalturaScheduledTaskProfile  $profile) {
		return (intval(time() / 86400) == (intval($profile->lastExecutionStartedAt / 86400)));
	}

	protected static function getReachProfileTaskType(KalturaScheduledTaskProfile $profile)
	{
		return $profile->objectTasks[0]->type;
	}

}