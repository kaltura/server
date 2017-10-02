<?php
/**
 * @package plugins.metadata
 * @subpackage lib
 */
class kMetadataObjectCopiedHandler implements kObjectCopiedEventConsumer, kObjectChangedEventConsumer, kObjectCreatedEventConsumer
{
	private static $partnerLevelPermissionTypes = array(
		PermissionType::PLUGIN,
		PermissionType::SPECIAL_FEATURE,
	);
	
	/* (non-PHPdoc)
	 * @see kObjectCopiedEventConsumer::shouldConsumeCopiedEvent()
	 */
	public function shouldConsumeCopiedEvent(BaseObject $fromObject, BaseObject $toObject)
	{
		if($fromObject instanceof Partner)
			return true;
		
		if($fromObject instanceof entry)
			return true;
		
		if($fromObject instanceof MetadataProfile)
			return true;
		
		if($fromObject instanceof IMetadataObject)
			return true;

		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectCopiedEventConsumer::objectCopied()
	 */
	public function objectCopied(BaseObject $fromObject, BaseObject $toObject)
	{
		if($fromObject instanceof Partner)
		{
			$this->copyMetadataProfiles($fromObject, $toObject);
			$this->copyMetadata(MetadataObjectType::PARTNER, $fromObject, $toObject);
		}

		elseif($fromObject instanceof entry && (!isset($toObject->copyMetaData) || $toObject->copyMetaData))
			$this->copyMetadata(MetadataObjectType::ENTRY, $fromObject, $toObject);
		
		elseif($fromObject instanceof category)
			$this->copyMetadata(MetadataObjectType::CATEGORY, $fromObject, $toObject);
		
		elseif($fromObject instanceof kuser)
			$this->copyMetadata(MetadataObjectType::USER, $fromObject, $toObject);
		
		elseif($fromObject instanceof MetadataProfile)
			kObjectCopyHandler::mapIds('MetadataProfile', $fromObject->getId(), $toObject->getId());
		
		elseif($fromObject instanceof IMetadataObject)	
			$this->copyMetadata($fromObject->getMetadataObjectType(), $fromObject, $toObject);
		return true;
	}
	
	/**
	 * @param KalturaMetadataObjectType $objectType
	 * @param BaseObject $fromObject
	 * @param BaseObject $toObject
	 */
	protected function copyMetadata($objectType, BaseObject $fromObject, BaseObject $toObject)
	{
 		$c = new Criteria();
 		$c->add(MetadataPeer::OBJECT_TYPE, $objectType);
 		$c->add(MetadataPeer::OBJECT_ID, $fromObject->getId());
 		
 		$metadatas = MetadataPeer::doSelect($c);
 		foreach($metadatas as $metadata)
 		{
 			$newMetadata = $metadata->copy();
 			$newMetadata->setObjectId($toObject->getId());
 			
 			if($toObject instanceof Partner)
 				$newMetadata->setPartnerId($toObject->getId());
 			else
 				$newMetadata->setPartnerId($toObject->getPartnerId());
 			
			$metadataProfileId = kObjectCopyHandler::getMappedId('MetadataProfile', $metadata->getMetadataProfileId());
			if($metadataProfileId)
			{
				$metadataProfile = MetadataProfilePeer::retrieveByPK($metadataProfileId);
				
				if($metadataProfile)
				{
					$newMetadata->setMetadataProfileId($metadataProfileId);
					$newMetadata->setMetadataProfileVersion($metadataProfile->getVersion());
				}
			}
			
 			$newMetadata->save();
 			
 			kFileSyncUtils::createSyncFileLinkForKey(
 				$newMetadata->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA),
 				$metadata->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA)
 			);
 		}
	}
	
	/**
	 * @param Partner $fromPartner
	 * @param Partner $toPartner
	 */
	protected function copyMetadataProfiles(Partner $fromPartner, Partner $toPartner, $permissionRequiredOnly = false)
	{
		$fromPartnerId = $fromPartner->getId();
		$toPartnerId = $toPartner->getId();
		
 		$c = new Criteria();
 		$c->add(MetadataProfilePeer::PARTNER_ID, $fromPartnerId);
 		
		$systemNameCriteria = new Criteria();
		$systemNameCriteria->add(MetadataProfilePeer::PARTNER_ID, $toPartnerId);
		$systemNameCriteria->add(MetadataProfilePeer::STATUS, MetadataProfile::STATUS_ACTIVE);
		
 		$metadataProfiles = MetadataProfilePeer::doSelect($c);
 		foreach($metadataProfiles as $metadataProfile)
 		{
 			/* @var $metadataProfile MetadataProfile */
 			
 			if ($permissionRequiredOnly && !count($metadataProfile->getRequiredCopyTemplatePermissions()))
 				continue;
 			
 			if (!myPartnerUtils::isPartnerPermittedForCopy ($toPartner, $metadataProfile->getRequiredCopyTemplatePermissions()))
 				continue;
 				
 			if($metadataProfile->getSystemName())
 			{
				$c = clone $systemNameCriteria;
				$c->add(MetadataProfilePeer::SYSTEM_NAME, $metadataProfile->getSystemName());
				if(MetadataProfilePeer::doCount($c))
					continue;
 			}
				
 			$newMetadataProfile = $metadataProfile->copy();
 			$newMetadataProfile->setPartnerId($toPartnerId);
 			$newMetadataProfile->save();
 			
 			kFileSyncUtils::createSyncFileLinkForKey(
 				$newMetadataProfile->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_DEFINITION),
 				$metadataProfile->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_DEFINITION)
 			);
 			
 			kFileSyncUtils::createSyncFileLinkForKey(
 				$newMetadataProfile->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_VIEWS),
 				$metadataProfile->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_VIEWS)
 			);
 			
 			kFileSyncUtils::createSyncFileLinkForKey(
 				$newMetadataProfile->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_XSLT),
 				$metadataProfile->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_XSLT)
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
	
	protected function partnerPermissionEnabled(Partner $partner)
	{
		$templatePartner = PartnerPeer::retrieveByPK($partner->getI18nTemplatePartnerId() ? $partner->getI18nTemplatePartnerId() : kConf::get('template_partner_id'));
		if($templatePartner)
			$this->copyMetadataProfiles($templatePartner, $partner, true);
	}
	
	/* (non-PHPdoc)
	 * @see kObjectCreatedEventConsumer::objectCreated()
	 */
	public function objectCreated(BaseObject $object)
	{
		if($object instanceof Permission)
		{
			/* @var $object Permission */
			$partner = PartnerPeer::retrieveByPK($object->getPartnerId());
			$this->partnerPermissionEnabled($partner);
		}
			
		if($object instanceof entry)
		{
			$tempEntryId = $object->getId();
			$partnerId = $object->getPartnerId();
		
			$metadataObjects = MetadataPeer::retrieveAllByObject(MetadataObjectType::ENTRY , $object->getReplacedEntryId());
		
			foreach($metadataObjects as $sourceMetadataObject)
			{
				$metadataProfileId = $sourceMetadataObject->getMetadataProfileId();
		
				$targetMetadata = new Metadata();
				$targetMetadata->setPartnerId($partnerId);
				$targetMetadata->setMetadataProfileId($metadataProfileId);
				$targetMetadata->setObjectType(MetadataObjectType::ENTRY);
				$targetMetadata->setObjectId($tempEntryId);
				$targetMetadata->setStatus(KalturaMetadataStatus::VALID);
		
				$targetMetadata->save();
		
				$sourceMetadataKey = $sourceMetadataObject->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA);
				$sourceXmlData = kFileSyncUtils::file_get_contents($sourceMetadataKey, true, false);
		
				$targetMetadataKey = $targetMetadata->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA);
				kFileSyncUtils::file_put_contents($targetMetadataKey, $sourceXmlData);
			}
		}
	}

	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::objectChanged()
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns)
	{
		if($object instanceof Permission)
		{
			/* @var $object Permission */
			$partner = PartnerPeer::retrieveByPK($object->getPartnerId());
			$this->partnerPermissionEnabled($partner);
		}
	}

	/* (non-PHPdoc)
	 * @see kObjectCreatedEventConsumer::shouldConsumeCreatedEvent()
	 */
	public function shouldConsumeCreatedEvent(BaseObject $object)
	{
		if($object instanceof Permission && $object->getPartnerId() && in_array($object->getType(), self::$partnerLevelPermissionTypes) && $object->getStatus() == PermissionStatus::ACTIVE)
		{
			return true;
		}
		
		if($object instanceof entry && $object->getReplacedEntryId())
		{
			$replacedEntry = entryPeer::retrieveByPK($object->getReplacedEntryId());
			$replacementOptions = $replacedEntry->getReplacementOptions();
			foreach($replacementOptions->getPluginOptionItems() as $replacementItem)
			{
				if($replacementItem instanceof kMetadataReplacementOptionsItem && $replacementItem->getShouldCopyMetadata())
					return true;
			}
		}	
		
		return false;
	}

	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::shouldConsumeChangedEvent()
	 */
	public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns)
	{
		if($object instanceof Permission && $object->getPartnerId() && in_array($object->getType(), self::$partnerLevelPermissionTypes) && in_array(PermissionPeer::STATUS, $modifiedColumns) && $object->getStatus() == PermissionStatus::ACTIVE)
		{
			return true;
		}
	
		return false;
	}

}
