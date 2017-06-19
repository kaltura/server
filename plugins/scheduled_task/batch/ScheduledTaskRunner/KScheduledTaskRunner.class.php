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
		$errorObjectsIds = array();
		$isMediaRepurposingProfile = $this->isMediaRepurposingProfile($profile);
		if ($isMediaRepurposingProfile)
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

				if ($error) {
					$errorObjectsIds[] = $object->id;
				} else {
					if (array_key_exists($object->userId, $objectsIds))
						$objectsIds[$object->userId][] = $object->id;
					else
						$objectsIds[$object->userId] = array($object->id);
				} 

				if ($isMediaRepurposingProfile)
					$this->updateMetadataStatusForMediaRepurposing($profile, $object, $error);
			}
			if (!$isMediaRepurposingProfile)
				$pager->pageIndex++;
		}
		
		if ($isMediaRepurposingProfile && (self::getMediaRepurposingProfileTaskType($profile) == ObjectTaskType::MAIL_NOTIFICATION) && count($objectsIds))
		{
			$mediaRepurposingName = $this->getMediaRepurposingProfileName($profile);
			$this->sendMailNotification($profile->objectTasks[0], $objectsIds, $mediaRepurposingName);
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
		KalturaLog::alert("Media Repurposing profile [$profile->id] has been suspended");
		if (self::isMediaRepurposingProfile($profile))
		{
			$address = $this->getPartnerMail($profile->partnerId);
			$this->sendMail(array($address), "Media Repurposing Suspended", "MR profile with id [$profile->name] has been suspended");
		}
	}

	private function addDateToFilter($profile)
	{
		if (self::startsWith($profile->name, 'MR_')) { //as sub task of MR profile
			//first item on advancedSearch is for the MRP
			//in the MRP filter: first item is for entry status, second is for MR status
			$value = self::getMrAdvancedSearchFilter($profile)->items[1]->value;
			$updatedDay = self::getUpdateDay($profile->description);
			$profile->objectFilter->advancedSearch->items[0]->items[1]->value = $value. "," . $updatedDay;
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
		if ($result->totalCount > 0)
			return $result->objects[0];
		return null;
	}

	private function updateMetadataXmlField($mrId, $newStatus, $xml_string, $error)
	{
		$day = self::getUpdateDay();
		if ($error)
			$newVal = "$mrId,Error,$newStatus,$day";
		else
			$newVal = "$mrId,$newStatus,$day";

		$xml = simplexml_load_string($xml_string);
		if ($xml)
		{
			$mprsData = $xml->xpath('/metadata/MRPData');
			for ($i = 0; $i < count($mprsData); $i++)
				if (self::startsWith($mprsData[$i], $mrId.","))
					$mprsData[$i][0] = $newVal;
		}

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
		$xml->addChild('Status', 'Enabled');
		$xml->addChild('MRPsOnEntry', "MR_$mrId");
		if ($error)
			$xml->addChild('MRPData', "$mrId,Error,1," .self::getUpdateDay());
		else
			$xml->addChild('MRPData', "$mrId,1," .self::getUpdateDay());
		return $xml;
	}

	private function isMediaRepurposingProfile(KalturaScheduledTaskProfile $profile)
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
			$arr = explode(",", self::getMrAdvancedSearchFilter($profile)->items[1]->value);
			return $arr[0];
		}
		return null;
	}

	private function getMediaRepurposingProfileName(KalturaScheduledTaskProfile $profile)
	{
		if ($profile->systemName == "MRP")
			return $profile->name;
		if (self::startsWith($profile->name, 'MR_')) {
			$arr = explode("_", $profile->name);
			return $arr[1];
		}
		return null;
	}

	private static function getMediaRepurposingProfileTaskType(KalturaScheduledTaskProfile $profile)
	{
		return $profile->objectTasks[0]->type;
	}

	private static function getMrAdvancedSearchFilter(KalturaScheduledTaskProfile $profile)
	{
		return $profile->objectFilter->advancedSearch->items[0];
	}

	private function getAdminObjectsBody($objectsIds, $sendToUsers)
	{
		$body = "\nExecute for entries: (users aggregate)\n";
		$cnt = 0;
		foreach($objectsIds as $userId => $entriesIds) {
			$body .= "[$userId]" . PHP_EOL;
			foreach($entriesIds as $id) {
				$body .= "\t$id" . PHP_EOL;
				$cnt++;
			}
		}
		$body .= "Total count of affected object: $cnt";
		
		if ($sendToUsers) {
			$body .= PHP_EOL . "Send Notification for the following users: ";
			foreach($objectsIds as $userId => $entriesIds)
				$body .= "$userId" . PHP_EOL;
		}
		return $body;
	}

	private function getUserObjectsBody($objectsIds)
	{
		$body = PHP_EOL ."Execute for entries:" . PHP_EOL;
		foreach($objectsIds as $id)
			$body .= "$id" . PHP_EOL;

		$body .= "Total count of affected object: " . count($objectsIds);
		return $body;
	}

	private function sendMailNotification($mailTask, $objectsIds, $mediaRepurposingName)
	{
		$subject = "Media Repurposing Notification";
		$bodyPrefix = "Notification from Media Repurposing [$mediaRepurposingName]: \n$mailTask->message " . PHP_EOL;
		$body = $bodyPrefix . $this->getAdminObjectsBody($objectsIds, $mailTask->sendToUsers);

		$toArr = explode(",", $mailTask->mailAddress);
		$success = $this->sendMail($toArr, $subject, $body);
		if (!$success)
			KalturaLog::info("Mail for MRP [$mediaRepurposingName] did not send successfully");

		if ($mailTask->sendToUsers)
			foreach ($objectsIds as $user => $objects) {
				$body = $bodyPrefix . $this->getUserObjectsBody($objects);
				$success = $this->sendMail(array($user), $subject, $body);
				if (!$success)
					KalturaLog::info("Mail for MRP [$mediaRepurposingName] did not send successfully");
			}
	}

	private function sendMail($toArray, $subject, $body)
	{
		$mailer = new PHPMailer();
		$mailer->CharSet = 'utf-8';
		if (!$toArray || count($toArray) < 1 || strlen($toArray[0]) == 0)
			return true;
		foreach ($toArray as $to)
			$mailer->AddAddress($to);
		$mailer->Subject = $subject;
		$mailer->Body = $body;
		KalturaLog::info("sending mail to " . implode(",",$toArray) . " with body: $body");
		return $mailer->Send();
	}

	private function getPartnerMail($partnerId)
	{
		$client = $this->getClient();
		self::impersonate($partnerId);
		$res = $client->partner->get($partnerId);
		self::unimpersonate();
		return $res->adminEmail;
	}

	private function updateMetadataStatusForMediaRepurposing(KalturaScheduledTaskProfile $profile, $object, $error) {
		$metadataProfileId = self::getMrAdvancedSearchFilter($profile)->metadataProfileId;

		self::impersonate($object->partnerId);
		$metadataPlugin = KalturaMetadataClientPlugin::get(self::$kClient);
		$metadata = $this->getMetadataOnObject($object->id, $metadataProfileId);

		$xml = ($metadata && $metadata->xml) ? $metadata->xml : null;
		if ($profile->systemName == "MRP") //as the first schedule task running in this MRP
			$xml = $this->addMetadataXmlField($profile->id, $xml, $error);
		elseif (self::startsWith($profile->name, 'MR_')) { //sub task of MRP
			$arr = explode(",", self::getMrAdvancedSearchFilter($profile)->items[1]->value);
			$xml = $this->updateMetadataXmlField($arr[0], $arr[1] + 1, $xml, $error);
		}

		try {
			$xml = $xml ? $xml->asXML(): null;
			if ($metadata && $metadata->id)
				$result = $metadataPlugin->metadata->update($metadata->id, $xml);
			else
				$result = $metadataPlugin->metadata->add($metadataProfileId, KalturaMetadataObjectType::ENTRY,$object->id, $xml);

		} catch (Exception $e) {
			if (self::getMediaRepurposingProfileTaskType($profile) == ObjectTaskType::DELETE_ENTRY)
				return null; //delete entry should get exception when update metadata for deleted entry
			throw new KalturaException("Error in metadata for entry [$object->id] with ". $e->getMessage());
		}
		
		self::unimpersonate();
		return $result->id;
	}

}
