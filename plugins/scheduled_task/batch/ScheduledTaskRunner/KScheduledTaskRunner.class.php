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
		$maxProfiles = $this->getParams('maxProfiles');

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
	 * @param KalturaScheduledTaskProfile $profile
	 */
	protected function processProfile(KalturaScheduledTaskProfile $profile)
	{
		$this->updateProfileBeforeExecution($profile);
		if ($profile->maxTotalCountAllowed)
			$maxTotalCountAllowed = $profile->maxTotalCountAllowed;
		else
			$maxTotalCountAllowed = $this->getParams('maxTotalCountAllowed');

		$objectsIds = array();
		$isMrProfile = $this->isMrProfile($profile);
		if ($isMrProfile)
			$this->addDateToFilter($profile);

		$pager = new KalturaFilterPager();
		$pager->pageIndex = 1;
		$pager->pageSize = 500;
		while(true)
		{
			$this->impersonate($profile->partnerId);
			try
			{
				$result = ScheduledTaskBatchHelper::query($this->getClient(), $profile, $pager);
				$this->unimpersonate();
			}
			catch(Exception $ex)
			{
				$this->unimpersonate();
				throw $ex;
			}

			if ($result->totalCount > $maxTotalCountAllowed)
			{
				KalturaLog::crit("List query for profile $profile->id returned too many results ($result->totalCount when the allowed total count is $maxTotalCountAllowed), suspending the profile");
				$this->suspendProfile($profile);
				break;
			}
			if (!count($result->objects))
				break;

			foreach($result->objects as $object)
			{
				$error = $this->processObject($profile, $object);

				if (!$error)
					$objectsIds[] = $object->id;
				if ($isMrProfile)
					$this->updateMetadataStatusForMR($profile, $object, $error);
			}
			if (!$isMrProfile)
				$pager->pageIndex++;
		}

		if ($isMrProfile && (self::getMrProfileTaskType($profile) == ObjectTaskType::MAIL_NOTIFICATION) && count($objectsIds))
		{
			$mrId = $this->getMrProfileId($profile);
			$this->sendMailNotification($profile->objectTasks[0], $objectsIds, $mrId);
		}


	}

	/**
	 * @param KalturaScheduledTaskProfile $profile
	 * @param $object
	 */
	protected function processObject(KalturaScheduledTaskProfile $profile, $object)
	{
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

			}
			catch(Exception $ex)
			{
				$this->unimpersonate();
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
		return $error;
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

	/**
	 * Moves the profile to suspended status
	 *
	 * @param KalturaScheduledTaskProfile $profile
	 */
	protected function suspendProfile(KalturaScheduledTaskProfile $profile)
	{
		$scheduledTaskClient = $this->getScheduledTaskClient();
		$profileForUpdate = new KalturaScheduledTaskProfile();
		$profileForUpdate->status = KalturaScheduledTaskProfileStatus::SUSPENDED;
		$this->impersonate($profile->partnerId);
		$scheduledTaskClient->scheduledTaskProfile->update($profile->id, $profileForUpdate);
		$this->unimpersonate();
	}

	private function addDateToFilter($profile)
	{
		if (self::startsWith($profile->name, 'MR_')) { //as sub task of MR profile
			// first item is for entry status, second is for MR status
			$value = $profile->objectFilter->advancedSearch->items[1]->value;
			$updatedDay = self::getUpdateDay($profile->description);
			$profile->objectFilter->advancedSearch->items[1]->value = $value. "," . $updatedDay;
		}
	}

	private static function getUpdateDay($waitDays = 0) {
		$now = intval(time() / 86400);  // as num of sec in day to get day number
		return $now - $waitDays;
	}

	private static function startsWith($haystack, $needle)
	{
		$length = strlen($needle);
		return (substr($haystack, 0, $length) === $needle);
	}

	private function getMetadataOnObject($objectId, $metadataProfileId)
	{
		$filter = new KalturaMetadataFilter();
		$filter->metadataProfileIdEqual = $metadataProfileId;
		$filter->objectIdEqual = $objectId;
		$metadataPlugin = KalturaMetadataClientPlugin::get(KBatchBase::$kClient);
		$result =  $metadataPlugin->metadata->listAction($filter, null);
		return $result->objects[0];
	}

	private function updateMetadataXmlField($mrId, $newStatus, $xml_string, $error)
	{
		$day = self::getUpdateDay();
		if ($error)
			$newVal = "$mrId,Error,$newStatus,$day";
		else
			$newVal = "$mrId,$newStatus,$day";

		$xml = simplexml_load_string($xml_string);
		$mprsData = $xml->xpath('/metadata/MRPData');
		for ($i = 0; $i < count($mprsData); $i++)
			if (self::startsWith($mprsData[$i], $mrId.","))
				$mprsData[$i][0] = $newVal;
		return $xml;
	}

	private function addMetadataXmlField($mrId, $xml_string, $error)
	{
		$xml = simplexml_load_string($xml_string);
		if (!$xml || !$xml->MRPData)
			return $this->createFirstMr($mrId, $xml, $error);

		$newVal = "$mrId,1," .self::getUpdateDay();
		if ($error)
			$newVal = "$mrId,Error,1," .self::getUpdateDay();

		$xml->MRPData[] = $newVal;
		$target_dom = dom_import_simplexml(current($xml->xpath('//MRPsOnEntry[last()]')));
		$insert_dom = $target_dom->ownerDocument->createElement("MRPsOnEntry", "MR_$mrId");
		$target_dom->parentNode->insertBefore($insert_dom, $target_dom->nextSibling);
		return $xml;
	}

	private function createFirstMr($mrId, $xml = null, $error)
	{
		if (!$xml)
			$xml = new SimpleXMLElement("<metadata/>");
		$xml->addChild('Status', '1');
		$xml->addChild('MRPsOnEntry', "MR_$mrId");
		if ($error)
			$xml->addChild('MRPData', "$mrId,Error,1," .self::getUpdateDay());
		else
			$xml->addChild('MRPData', "$mrId,1," .self::getUpdateDay());
		return $xml;
	}

	private function isMrProfile(KalturaScheduledTaskProfile $profile)
	{
		if (($profile->systemName == "MRP") || (self::startsWith($profile->name, 'MR_')))
			return true;
		return false;
	}

	private function getMrProfileId(KalturaScheduledTaskProfile $profile)
	{
		if ($profile->systemName == "MRP")
			return $profile->id;
		if (self::startsWith($profile->name, 'MR_')) {
			$arr = explode(",", $profile->objectFilter->advancedSearch->items[1]->value);
			return $arr[0];
		}
		return null;
	}

	private static function getMrProfileTaskType(KalturaScheduledTaskProfile $profile)
	{
		return $profile->objectTasks[0]->type;
	}

	private function sendMailNotification($mailTask, $objectsIds, $mrId)
	{
		$body = "Notification from MR id [$mrId]: \n$mailTask->message \n\nExecute for entries: \n";
		foreach($objectsIds as $id)
			$body .= "$id\n";
		$body .= "Total count of affected object: " . count($objectsIds);

		KalturaLog::info("sending mail to $mailTask->mailAddress with body: $body");
		$toArr = explode(",", $mailTask->mailAddress);
		$success = $this->sendMail($toArr, "Media Repurposing Notification" , $body);
		if (!$success)
			KalturaLog::alert("Mail for MRP [$mrId] did not send successfully");
	}

	private function sendMail($toArray, $subject, $body)
	{
		$mailer = new PHPMailer();
		$mailer->CharSet = 'utf-8';
		foreach ($toArray as $to)
			$mailer->AddAddress($to);
		$mailer->Subject = $subject;
		$mailer->Body = $body;
		return $mailer->Send();
	}

	private function updateMetadataStatusForMR(KalturaScheduledTaskProfile $profile, $object, $error) {
		$metadataProfileId = $profile->objectFilter->advancedSearch->metadataProfileId;

		self::impersonate($object->partnerId);
		$metadataPlugin = KalturaMetadataClientPlugin::get(self::$kClient);
		$metadata = $this->getMetadataOnObject($object->id, $metadataProfileId);

		$xml = $metadata->xml;
		if ($profile->systemName == "MRP") //as the first schedule task running in this MRP
			$xml = $this->addMetadataXmlField($profile->id, $metadata->xml, $error);
		elseif (self::startsWith($profile->name, 'MR_')) { //sub task of MRP
			$arr = explode(",", $profile->objectFilter->advancedSearch->items[1]->value);
			$xml = $this->updateMetadataXmlField($arr[0], $arr[1] + 1, $metadata->xml, $error);
		}

		try {
			if ($metadata->id)
				$result = $metadataPlugin->metadata->update($metadata->id, $xml->asXML());
			else
				$result = $metadataPlugin->metadata->add($metadataProfileId, KalturaMetadataObjectType::ENTRY,$object->id, $xml->asXML());

		} catch (Exception $e) {
			if (self::getMrProfileTaskType($profile) == ObjectTaskType::DELETE_ENTRY)
				return null; //delete entry should get exception when update metadata for deleted entry
			throw new KalturaException("Error in metadata for entry [$object->id] with ". $e->getMessage());
		}
		
		self::unimpersonate();
		return $result->id;
	}

}
