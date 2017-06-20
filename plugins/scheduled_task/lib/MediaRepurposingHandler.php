<?php

/**
 * @package plugins.schedule_task
 * @subpackage Admin
 */
class MediaRepurposingHandler implements kObjectDataChangedEventConsumer
{

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

	private function getMRPWithMetadataSearchByProfile($partnerId, $metadataProfileId)
	{
		$allMediaRepurposingProfilesOnPartner = ScheduledTaskProfilePeer::retrieveBySystemName(MediaRepurposingUtils::MEDIA_REPURPOSING_SYSTEM_NAME, $partnerId);
		$mediaRepurposingProfilesWithSearchOnGivenMetadataId = array();
		foreach($allMediaRepurposingProfilesOnPartner as $mediaRepurposingProfile) {
			/* @var $mr ScheduledTaskProfile*/
			$items = $mediaRepurposingProfile->getObjectFilter()->getAdvancedSearch()->getItems(); // always have items in advance search because that how the MR is build
			foreach($items as $item) {
				if ($item instanceof MetadataSearchFilter && $item->getMetadataProfileId() == $metadataProfileId)
					$mediaRepurposingProfilesWithSearchOnGivenMetadataId[] = $mediaRepurposingProfile->getId();
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