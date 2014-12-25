<?php
/**
 * @package plugins.metadata
 * @subpackage lib
 */
class kMetadataObjectCreatedHandler implements kObjectCreatedEventConsumer
{
	/* (non-PHPdoc)
	 * @see kObjectCreatedEventConsumer::shouldConsumeCreatedEvent()
	 */
	public function shouldConsumeCreatedEvent(BaseObject $fromObject)
	{
		if($fromObject instanceof entry)
		{
			if ($fromObject->getIsRecordedEntry() == true)
				return true;
		}
		
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectCreatedEventConsumer::objectCreated()
	 */
	public function objectCreated(BaseObject $fromObject)
	{
		if($fromObject instanceof entry)
		{
				$liveEntryId = $fromObject->getRootEntryId();
				$this->copyLiveMetadata($fromObject , $liveEntryId);
		}			
		return true;
	}
	
	
	protected function copyLiveMetadata(baseEntry $object , $liveEntryId)
	{
		$recordedEntryId = $object->getId();
		$partnerId = $object->getPartnerId();
	
		$metadataProfiles = MetadataProfilePeer::retrieveAllActiveByPartnerId($partnerId , MetadataObjectType::ENTRY);
	
		foreach ($metadataProfiles as $metadataProfile)
		{
			$originMetadataObj = MetadataPeer::retrieveByObject($metadataProfile->getId() , MetadataObjectType::ENTRY , $liveEntryId);
			if ($originMetadataObj)
			{
					$metadataProfileId = $metadataProfile->getId();
					$metadataProfileVersion = $metadataProfile->getVersion();
	
					$destMetadataObj = new Metadata();
				
					$destMetadataObj->setPartnerId($partnerId);
					$destMetadataObj->setMetadataProfileId($metadataProfileId);
					$destMetadataObj->setMetadataProfileVersion($metadataProfileVersion);
					$destMetadataObj->setObjectType(MetadataObjectType::ENTRY);
					$destMetadataObj->setObjectId($recordedEntryId);
					$destMetadataObj->setStatus(KalturaMetadataStatus::VALID);
	
					$originMetadataKey = $originMetadataObj->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA);
					$originXml = kFileSyncUtils::file_get_contents($originMetadataKey, true, false);
	
					// validate object exists
					$object = kMetadataManager::getObjectFromPeer($destMetadataObj);
					if($object)
							$destMetadataObj->save();
					else
					{
							KalturaLog::err('invalid object type');
							continue;
					}
	
					$destMetadataKey = $destMetadataObj->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA);
					kFileSyncUtils::file_put_contents($destMetadataKey, $originXml);
			}
		}
	}
}

