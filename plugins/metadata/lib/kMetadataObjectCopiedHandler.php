<?php
class kMetadataObjectCopiedHandler implements kObjectCopiedEventConsumer
{
	/**
	 * @param BaseObject $fromObject
	 * @param BaseObject $toObject
	 */
	public function objectCopied(BaseObject $fromObject, BaseObject $toObject)
	{
		if($fromObject instanceof Partner)
			$this->copyMetadataProfiles($fromObject->getId(), $toObject->getId());
		
		if($fromObject instanceof entry)
			$this->copyMetadata(Metadata::TYPE_ENTRY, $fromObject, $toObject);
		
		if($fromObject instanceof MetadataProfile)
			kObjectCopyHandler::mapIds('MetadataProfile', $fromObject->getId(), $toObject->getId());
	}
	
	/**
	 * @param KalturaMetadataObjectType $objectType
	 * @param BaseObject $fromObject
	 * @param BaseObject $toObject
	 */
	protected function copyMetadata($objectType, BaseObject $fromObject, BaseObject $toObject)
	{
		KalturaLog::debug("Copy metadata type [$objectType] from " . get_class($fromObject) . '[' . $fromObject->getId() . "] to[" . $toObject->getId() . "]");
			
 		$c = new Criteria();
 		$c->add(MetadataPeer::OBJECT_TYPE, $objectType);
 		$c->add(MetadataPeer::OBJECT_ID, $fromObject->getId());
 		
 		$metadatas = MetadataPeer::doSelect($c);
 		foreach($metadatas as $metadata)
 		{
 			$newMetadata = $metadata->copy();
 			$newMetadata->setObjectId($toObject->getId());
 			$newMetadata->setPartnerId($toObject->getPartnerId());
 			
			$metadataProfileId = kObjectCopyHandler::getMappedId('MetadataProfile', $metadata->getMetadataProfileId());
			if($metadataProfileId)
			{
				$metadataProfile = MetadataProfilePeer::retrieveById($metadataProfileId);
				
				if($metadataProfile)
				{
					$newMetadata->setMetadataProfileId($metadataProfileId);
					$newMetadata->setMetadataProfileVersion($metadataProfile->getVersion());
				}
			}
			
 			$newMetadata->save();
 			
 			kFileSyncUtils::createSyncFileLinkForKey(
 				$newMetadata->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA),
 				$metadata->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA),
 				false
 			);
 		}
	}
	
	/**
	 * @param int $fromPartnerId
	 * @param int $toPartnerId
	 */
	protected function copyMetadataProfiles($fromPartnerId, $toPartnerId)
	{
		KalturaLog::debug("Copy metadata profiles from [$fromPartnerId] to [$toPartnerId]");
		
 		$c = new Criteria();
 		$c->add(MetadataProfilePeer::PARTNER_ID, $fromPartnerId);
 		
 		$metadataProfiles = MetadataProfilePeer::doSelect($c);
 		foreach($metadataProfiles as $metadataProfile)
 		{
 			$newMetadataProfile = $metadataProfile->copy();
 			$newMetadataProfile->setPartnerId($toPartnerId);
 			$newMetadataProfile->save();
 			
 			kFileSyncUtils::createSyncFileLinkForKey(
 				$newMetadataProfile->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_DEFINITION),
 				$metadataProfile->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_DEFINITION),
 				false
 			);
 			
 			kFileSyncUtils::createSyncFileLinkForKey(
 				$newMetadataProfile->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_VIEWS),
 				$metadataProfile->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_VIEWS),
 				false
 			);
 			
 			$metadataProfileFields = MetadataProfileFieldPeer::retrieveByMetadataProfileId($metadataProfile->getId());
 			foreach($metadataProfileFields as $metadataProfileField)
 			{
	 			$newMetadataProfileField = $metadataProfileField->copy();
	 			$newMetadataProfileField->setMetadataProfileId($newMetadataProfile->getId());
	 			$newMetadataProfileField->setPartnerId($toPartnerId);
	 			$newMetadataProfileField->save();
 			}
 		}
	}
}