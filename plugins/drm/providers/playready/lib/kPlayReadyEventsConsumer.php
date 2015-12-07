<?php
class kPlayReadyEventsConsumer implements kObjectReplacedEventConsumer
{
	/* (non-PHPdoc)
	 * @see kObjectReplacedEventConsumer::objectReplaced()
	 */
	public function objectReplaced(BaseObject $object, BaseObject $replacingObject, BatchJob $raisedJob = null) 
	{
		KalturaLog::debug("check for DRM key replacement");
		
		try 
		{
			$replacingDrmKey = $this->getDrmKey($replacingObject);
			if($replacingDrmKey)
			{
				$newKeyId = $replacingDrmKey->getDrmKey();
				
				KalturaLog::debug("replacing drm key with: ".$newKeyId);
				
				$entryDrmKey = $this->getDrmKey($object);
				if(!$entryDrmKey)
				{
					$entryDrmKey = new DrmKey();
					$entryDrmKey->setPartnerId($object->getPartnerId());
					$entryDrmKey->setObjectId($object->getId());
					$entryDrmKey->setObjectType(DrmKeyObjectType::ENTRY);
					$entryDrmKey->setProvider(PlayReadyPlugin::getPlayReadyProviderCoreValue());					
				}
				
				$entryDrmKey->setDrmKey($newKeyId);
				$entryDrmKey->save();
				$object->putInCustomData(PlayReadyPlugin::ENTRY_CUSTOM_DATA_PLAY_READY_KEY_ID, $newKeyId);
				$object->save();				
			}			
		}
		catch (Exception $e)
		{
			KalturaLog::err("Failed to update drm key for entry ".$object->getId());
		}
		
		return true;
		
	}

	/* (non-PHPdoc)
	 * @see kObjectReplacedEventConsumer::shouldConsumeReplacedEvent()
	 */
	public function shouldConsumeReplacedEvent(BaseObject $object) 
	{
		if($object && $object instanceof entry)
			return true;
		else
			return false;	
	}
	
	private function getDrmKey($entry)
	{
		if($entry)
			$drmKey = DrmKeyPeer::retrieveByUniqueKey($entry->getId(), DrmKeyObjectType::ENTRY, PlayReadyPlugin::getPlayReadyProviderCoreValue());
		else 
			$drmKey = null;
		return $drmKey;
	}
}