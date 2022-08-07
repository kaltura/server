<?php

/**
 * @package plugins.schedule_task
 * @subpackage Admin
 */
class MediaRepurposingHandler implements kObjectDataChangedEventConsumer, kBatchJobStatusEventConsumer
{
    	const MRP_IDS_TO_EXCLUDE_RESET_ON_METADATA_UPDATE = "MRP_ids_to_exclude_reset_on_metadata_update";

	public static function enableMrPermission($partnerId)
	{
		if (!MetadataProfilePeer::retrieveBySystemName(MediaRepurposingUtils::MEDIA_REPURPOSING_SYSTEM_NAME, $partnerId)) {
			KalturaLog::info("NO MDP on partner [$partnerId] - cloning from admin-console partner");
			$templateMDPForMR = MetadataProfilePeer::retrieveBySystemName(MediaRepurposingUtils::MEDIA_REPURPOSING_SYSTEM_NAME, MediaRepurposingUtils::ADMIN_CONSOLE_PARTNER);
			if ($templateMDPForMR) {
				$newMDP = $templateMDPForMR->copyToPartner($partnerId);
				$newMDP->save();
			}
		}
	}

	public function objectDataChanged(BaseObject $object, $previousVersion = null, BatchJob $raisedJob = null)
	{
		/* @var $object Metadata*/
		$partnerId = $object->getPartnerId();
		$entryId = $object->getEntryId();

		$mediaRepurposingMetadataProfileId = $this->getMediaRepuposingMetadataProfileId($partnerId);
		$mediaRepuposingMetadata = MetadataPeer::retrieveByObject($mediaRepurposingMetadataProfileId, MetadataObjectType::ENTRY, $entryId);
		if (!$mediaRepuposingMetadata)
			return true; //if no metadata for media repurposing on entry then nothing to do
		
		$key = $mediaRepuposingMetadata->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA);
		$xml = kFileSyncUtils::file_get_contents($key, true, false);
		$xml = simplexml_load_string($xml);

		$mediaRepurposingProfileIdsToRemove = $this->getMRPWithMetadataSearchByProfile($partnerId, $object->getMetadataProfileId());

		KalturaLog::debug("Have Media-Repurposing-Data on entryId [$entryId] as [" . print_r($xml, true) . "]");
		KalturaLog::debug("The MR profile Ids to reset are: " . print_r($mediaRepurposingProfileIdsToRemove, true));

		foreach($mediaRepurposingProfileIdsToRemove as $mediaRepurposingId)
			$xml = $this->removeMediaRepurposingProfileFromMetadata($xml, $mediaRepurposingId);

		KalturaLog::debug("The new XML is: " . print_r($xml, true));

		if (!kFileSyncUtils::compareContent($key, $xml->asXML()))
			MetadataPlugin::updateMetadataFileSync($mediaRepuposingMetadata, $xml->asXML());

		return true;
	}

	public function shouldConsumeDataChangedEvent(BaseObject $object, $previousVersion = null)
	{
		if ($object instanceof Metadata)
		{
			$changedMetadataProfileId =  $object->getMetadataProfileId();
			$partnerId = $object->getPartnerId();

			$mediaRepurposingMetadataProfileId = $this->getMediaRepuposingMetadataProfileId($partnerId);
			if (!$mediaRepurposingMetadataProfileId || $mediaRepurposingMetadataProfileId == $changedMetadataProfileId)
				return false; // should not consume change in the MRP metadata itself

			$mediaRepurposingProfiles = $this->getMRPWithMetadataSearchByProfile($partnerId, $changedMetadataProfileId);
			if (count($mediaRepurposingProfiles))
				return true;// should consume only if at least one of the partner MRP affected by the metadata profile change
		}
		return false;
	}

	public function updatedJob(BatchJob $dbBatchJob)
	{
		try
		{
			$partnerId = $dbBatchJob->getPartnerId();
			$entryId = $dbBatchJob->getEntryId();

			$mediaRepurposingMetadataProfileId = $this->getMediaRepuposingMetadataProfileId($partnerId);
			if(!$mediaRepurposingMetadataProfileId)
			{
				return true;
			}

			$mediaRepurposingMetadata = MetadataPeer::retrieveByObject($mediaRepurposingMetadataProfileId, MetadataObjectType::ENTRY, $entryId);
			if(!$mediaRepurposingMetadata)
			{
				return true;
			}

			kLock::runLocked("metadata_update_xsl_{$mediaRepurposingMetadata->getId()}", array($this, 'updatedJobImpl'), array($dbBatchJob, $mediaRepurposingMetadata));
		}
		catch(Exception $ex)
		{
			KalturaLog::err( "Error:" . $ex->getMessage() );
		}
		return true;
	}

	public function updatedJobImpl(BatchJob $dbBatchJob, Metadata $mediaRepurposingMetadata)
	{
		$key = $mediaRepurposingMetadata->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA);
		$xml = kFileSyncUtils::file_get_contents($key, true, false);

		if(!$xml)
		{
			return;
		}

		$xml = simplexml_load_string($xml);
		$properties = $xml->children();
		foreach($properties as $property)
		{
			/* @var $property SimpleXMLElement */
			$propertyAsDom = dom_import_simplexml($property);
			if ($property->getName() == 'MRPData')
			{
				$propertyValArr = explode(",", $propertyAsDom->nodeValue);
				// structure of MRPData in process status:
				// where profileId is related to task: e.g. storage export task => storage profile Id
				// mrId,Process:taskType:profileId,taskIndex,unixTimeInDays
				if(count($propertyValArr) < 4 || strpos($propertyAsDom->nodeValue, 'Process') === false)
				{
					continue;
				}
				$processMetadataArr= explode(":", $propertyValArr[1]);
				$taskType = $processMetadataArr[1];
				$jobProfileId = $processMetadataArr[2];

				if ($this->shouldUpdateMRMetadata($taskType, $jobProfileId, $dbBatchJob))
				{
					$propertyAsDom->nodeValue = $this->getUpdatedPostProcessMRMetadata($propertyAsDom->nodeValue);
					MetadataPlugin::updateMetadataFileSync($mediaRepurposingMetadata, $xml->asXML());
					return;
				}
			}
		}
	}

	public function shouldConsumeJobStatusEvent(BatchJob $dbBatchJob)
	{
		$distributionBatchJobType = $this->getDistributionBatchJobType();
		$supportedBatchJobTypes = array(BatchJobType::STORAGE_EXPORT, $distributionBatchJobType);

		if(in_array($dbBatchJob->getJobType(), $supportedBatchJobTypes) && $dbBatchJob->getStatus() == KalturaBatchJobStatus::FINISHED)
		{
			return true;
		}
		return false;
	}

	protected function shouldUpdateMRMetadata($taskType, $jobProfileId, $dbBatchJob)
	{
		$distributionTaskType = ScheduledTaskContentDistributionPlugin::getApiValue(DistributeObjectTaskType::DISTRIBUTE);
		$distributionBatchJobType = $this->getDistributionBatchJobType();

		if($taskType == $distributionTaskType && $dbBatchJob->getJobType() == $distributionBatchJobType)
		{
			if($dbBatchJob->getData() instanceof kDistributionSubmitJobData
				&& $dbBatchJob->getData()->getDistributionProfileId() == $jobProfileId)
			{
				return true;
			}
		}

		elseif($taskType == ObjectTaskType::STORAGE_EXPORT && $dbBatchJob->getJobType() == BatchJobType::STORAGE_EXPORT)
		{
			if($dbBatchJob->getData() instanceof kStorageExportJobData && $dbBatchJob->getData()->getStorageId() == $jobProfileId)
			{
				return true;
			}
		}

		return false;
	}

	protected function getUpdatedPostProcessMRMetadata($mrMetadata)
	{
		$mrMetadataArr = explode(",", $mrMetadata);
		$day = $mrMetadataArr[3] + 1;
		return "$mrMetadataArr[0],$mrMetadataArr[2],$day";
	}

	protected function getDistributionBatchJobType()
	{
		$distributionBatchJobType = null;
		if (class_exists('ContentDistributionPlugin') && KalturaPluginManager::getPluginInstance(ContentDistributionPlugin::getPluginName()))
		{
			$distributionBatchJobType = ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_SUBMIT);
		}
		return $distributionBatchJobType;
	}

	private function getMRPWithMetadataSearchByProfile($partnerId, $metadataProfileId)
	{
        	$allMediaRepurposingProfilesOnPartner = ScheduledTaskProfilePeer::retrieveBySystemName(MediaRepurposingUtils::MEDIA_REPURPOSING_SYSTEM_NAME, $partnerId);
        	$mediaRepurposingProfilesWithSearchOnGivenMetadataId = array();
        	$MRProfilesToExclude = kConf::get(self::MRP_IDS_TO_EXCLUDE_RESET_ON_METADATA_UPDATE, kConfMapNames::RUNTIME_CONFIG, array());

        	foreach($allMediaRepurposingProfilesOnPartner as $mediaRepurposingProfile)
        	{
        	    /* @var $mediaRepurposingProfile ScheduledTaskProfile*/
        	    if(in_array($mediaRepurposingProfile->getId(), $MRProfilesToExclude) || !$mediaRepurposingProfile->getObjectFilter() || !$mediaRepurposingProfile->getObjectFilter()->getAdvancedSearch())
        	        continue;
        	    $items = $mediaRepurposingProfile->getObjectFilter()->getAdvancedSearch()->getItems(); // always have items in advance search because that how the MR is build
        	    foreach($items as $item)
        	    {
        	        if($item instanceof MetadataSearchFilter && $item->getMetadataProfileId() == $metadataProfileId)
        	        {
        	            $mediaRepurposingProfilesWithSearchOnGivenMetadataId[] = $mediaRepurposingProfile->getId();
        	        }
        	    }
        	}
        	return $mediaRepurposingProfilesWithSearchOnGivenMetadataId;
	}

	private function getMediaRepuposingMetadataProfileId($partnerId)
	{
		$mrp = MetadataProfilePeer::retrieveBySystemName(MediaRepurposingUtils::MEDIA_REPURPOSING_SYSTEM_NAME, $partnerId);
		if ($mrp)
			return $mrp->getId();
		return null;
	}

	private function removeMediaRepurposingProfileFromMetadata($xml, $mediaRepurposingId)
	{
		$xml = $this->removeProfileFromField($xml, $mediaRepurposingId,'MRPsOnEntry', '_', 1);
		$xml = $this->removeProfileFromField($xml, $mediaRepurposingId,'MRPData', ',', 0);
		return $xml;
	}
	
	private function removeProfileFromField($xml, $mediaRepurposingId, $fieldName, $separator, $index)
	{
		$result = $xml->xpath('//'. $fieldName);
		foreach ($result as $node){
			$val = explode($separator, (string) $node[0]);
			if ($mediaRepurposingId == $val[$index]) {
				$dom=dom_import_simplexml($node);
				$dom->parentNode->removeChild($dom);
			}
		}
		return $xml;
	}
}
