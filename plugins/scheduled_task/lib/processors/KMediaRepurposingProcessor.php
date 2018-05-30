<?php

/**
 * @package plugins.scheduledTask
 * @subpackage lib.processors
 */
class KMediaRepurposingProcessor extends KGenericProcessor
{
	/**
	 * @param KalturaScheduledTaskProfile $profile
	 */
	public function processProfile(KalturaScheduledTaskProfile $profile)
	{
		$this->taskRunner->impersonate($profile->partnerId);
		try
		{
			$maxTotalCountAllowed = $this->preProcess($profile);
			$this->addDateToFilter($profile);
			$objectsData = $this->handleProcess($profile, $maxTotalCountAllowed);
			$this->postProcess($profile, $objectsData);
		}
		catch (Exception $ex)
		{
			$this->taskRunner->unimpersonate();
			throw $ex;
		}
		$this->taskRunner->unimpersonate();
	}

	protected function postProcess($profile, $objectsData)
	{
		if ((self::getMediaRepurposingProfileTaskType($profile) == ObjectTaskType::MAIL_NOTIFICATION) && count($objectsData))
			KObjectTaskMailNotificationEngine::sendMailNotification($profile->objectTasks[0], $objectsData, $profile->id, $profile->partnerId);
	}

	protected function handlePager($pager)
	{
		//Nothing to do - not modifying pager
	}

	protected function additionalActions($profile, $object, $tasksCompleted, $error)
	{
		if ($this->shouldUpdateMetadataStatusForMR($tasksCompleted))
			$this->updateMetadataStatusForMediaRepurposing($profile, $object, $error);
	}

	private function addDateToFilter($profile)
	{
		if (self::startsWith($profile->name, 'MR_'))
		{ //as sub task of MR profile
			//first item on advancedSearch is for the MRP
			//in the MRP filter: first item is for entry status, second is for MR status
			$value = self::getMrAdvancedSearchFilter($profile)->items[1]->value;
			$updatedDay = self::getUpdateDay($profile->description);
			$profile->objectFilter->advancedSearch->items[0]->items[1]->value = $value . "," . $updatedDay;
		}
	}

	private static function getMrAdvancedSearchFilter(KalturaScheduledTaskProfile $profile)
	{
		return $profile->objectFilter->advancedSearch->items[0];
	}

	private function shouldUpdateMetadataStatusForMR($tasksCompleted)
	{
		foreach ($tasksCompleted as $task)
		{
			if (in_array($task, self::$dontUpdateMetaDataTaskTypes))
				return false;
		}

		return true;
	}

	private function updateMetadataStatusForMediaRepurposing(KalturaScheduledTaskProfile $profile, $object, $error)
	{
		$metadataProfileId = self::getMrAdvancedSearchFilter($profile)->metadataProfileId;
		$metadataPlugin = KalturaMetadataClientPlugin::get(KBatchBase::$kClient);
		$metadata = $this->getMetadataOnObject($object->id, $metadataProfileId);

		$xml = ($metadata && $metadata->xml) ? $metadata->xml : null;
		if ($profile->systemName == "MRP") //as the first schedule task running in this MRP
			$xml = $this->addMetadataXmlField($profile->id, $xml, $error);
		elseif (self::startsWith($profile->name, 'MR_'))
		{ //sub task of MRP
			$arr = explode(",", self::getMrAdvancedSearchFilter($profile)->items[1]->value);
			$xml = $this->updateMetadataXmlField($arr[0], $arr[1] + 1, $xml, $error);
		}

		try
		{
			$xml = $xml ? $xml->asXML() : null;
			if ($metadata && $metadata->id)
				$result = $metadataPlugin->metadata->update($metadata->id, $xml);
			else
				$result = $metadataPlugin->metadata->add($metadataProfileId, KalturaMetadataObjectType::ENTRY, $object->id, $xml);

		}
		catch (Exception $e)
		{
			if (self::getMediaRepurposingProfileTaskType($profile) == ObjectTaskType::DELETE_ENTRY)
				return null; //delete entry should get exception when update metadata for deleted entry

			throw new KalturaException("Error in metadata for entry [$object->id] with " . $e->getMessage(),
				KalturaBatchJobAppErrors::MEDIA_REPURPOSING_FAILED, null);
		}

		return $result->id;
	}

	private function getMetadataOnObject($objectId, $metadataProfileId)
	{
		$filter = new KalturaMetadataFilter();
		$filter->metadataProfileIdEqual = $metadataProfileId;
		$filter->objectIdEqual = $objectId;
		$metadataPlugin = KalturaMetadataClientPlugin::get(KBatchBase::$kClient);
		$result = $metadataPlugin->metadata->listAction($filter, null);
		if ($result->totalCount > 0)
			return $result->objects[0];
		return null;
	}

	/**
	 * Moves the profile to suspended status
	 *
	 * @param KalturaScheduledTaskProfile $profile
	 */
	public function suspendProfile(KalturaScheduledTaskProfile $profile)
	{
		parent::suspendProfile($profile);
		KalturaLog::alert("Media Repurposing profile [$profile->id] has been suspended");
		$address = $this->getPartnerMail($profile->partnerId);
		KObjectTaskMailNotificationEngine::sendMail(array($address), "Media Repurposing Suspended", "MR profile with id [$profile->name] has been suspended");
	}

	private function addMetadataXmlField($mrId, $xml_string, $error)
	{
		$xml = simplexml_load_string($xml_string);
		if (!$xml || !$xml->MRPData)
			return $this->createFirstMr($mrId, $xml, $error);

		$newVal = "$mrId,1," . self::getUpdateDay();
		if ($error)
			$newVal = "$mrId,Error,1," . self::getUpdateDay();

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
		if (!isset($xml->Status))
			$xml->addChild('Status', 'Enabled');
		$xml->addChild('MRPsOnEntry', "MR_$mrId");
		if ($error)
			$xml->addChild('MRPData', "$mrId,Error,1," . self::getUpdateDay());
		else
			$xml->addChild('MRPData', "$mrId,1," . self::getUpdateDay());
		return $xml;
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
				if (self::startsWith($mprsData[$i], $mrId . ","))
					$mprsData[$i][0] = $newVal;
		}

		return $xml;
	}

	protected static function getMediaRepurposingProfileTaskType(KalturaScheduledTaskProfile $profile)
	{
		return $profile->objectTasks[0]->type;
	}

}