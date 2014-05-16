<?php
class kIsmIndexEventsConsumer implements kObjectChangedEventConsumer
{	
	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::shouldConsumeChangedEvent()
	 */
	public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns)
	{
		if(
			$object instanceof flavorAsset
			&&	in_array(assetPeer::STATUS, $modifiedColumns)
			&&  $object->isLocalReadyStatus()
			&&  $object->hasTag(assetParams::TAG_ISM_MANIFEST)
			&&  $object->getentry()->getStatus() != entryStatus::DELETED
			&& 	!($object->getentry()->getReplacingEntryId())
		)
			return true;
			
		return false;
	}

	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::objectChanged()
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns)
	{	
		// replacing the ismc file name in the ism file
		$ismPrevVersionFileSyncKey = $object->getSyncKey(flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ISM);
		$ismContents = kFileSyncUtils::file_get_contents($ismPrevVersionFileSyncKey);
		
		$ismcPrevVersionFileSyncKey = $object->getSyncKey(flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ISMC);
		$ismcContents = kFileSyncUtils::file_get_contents($ismcPrevVersionFileSyncKey);
		$ismcPrevVersionFilePath = kFileSyncUtils::getLocalFilePathForKey($ismcPrevVersionFileSyncKey);
		
		$object->incrementVersion();
		$object->save();
		
		$ismcFileSyncKey = $object->getSyncKey(flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ISMC);
		kFileSyncUtils::moveFromFile($ismcPrevVersionFilePath, $ismcFileSyncKey);			
		$ismcNewName = basename(kFileSyncUtils::getLocalFilePathForKey($ismcFileSyncKey));
		
		KalturaLog::debug("Editing ISM set content to [$ismcNewName]");
			
		$ismXml = new SimpleXMLElement($ismContents);
		$ismXml->head->meta['content'] = $ismcNewName;
		
		$tmpPath = kFileSyncUtils::getLocalFilePathForKey($ismPrevVersionFileSyncKey).'.tmp';
		file_put_contents($tmpPath, $ismXml->asXML());
		
		kFileSyncUtils::moveFromFile($tmpPath, $object->getSyncKey(flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ISM));
					
		return true;
	}

}