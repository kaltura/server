<?php
/**
 * @package plugins.document
 * @subpackage lib
 */
class DocumentCreatedHandler implements kObjectCreatedEventConsumer, kObjectAddedEventConsumer
{
	protected static $fileExtensions = array(
		entry::ENTRY_MEDIA_TYPE_DOCUMENT => array(
			'doc', 'docx', 'docm', 'dotx', 'dotm', 
			'xls', 'xlsx', 'xlsm', 'xltx', 'xltm', 'xlsb', 'xlam', 
			'ppt', 'pptx', 'pptm', 'potx', 'potm', 'ppam', 'ppsm', 
			'odb', 'odc', 'odf', 'odg', 'odi', 'odm', 'odp', 'ods', 'odt', 
			'otc', 'otf', 'otg', 'oth', 'oti', 'otp', 'ots', 'ott', 'oxt',
		),
		entry::ENTRY_MEDIA_TYPE_SWF => array('swf'),
		entry::ENTRY_MEDIA_TYPE_PDF => array('pdf'),
	);
	
	public function setDocumentType(entry $object)
	{
		$fileName = $object->getSourceLink();
		$ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
		foreach(self::$fileExtensions as $documentType => $extensions)
		{
			if(in_array($ext, $extensions))
			{
				$object->setMediaType($documentType);
				$object->setType(entryType::DOCUMENT);
				break;
			}
		}
	}
	
	/**
	 * @param entry $object
	 * @return bool true if should continue to the next consumer
	 */
	public function entryCreated(entry $object)
	{
		$mediaType = null;
		if($object->getType() == entryType::AUTOMATIC)
		{
			$mediaType = $object->getMediaType();
			if(isset(self::$fileExtensions[$mediaType]))
			{
				$object->setType(entryType::DOCUMENT);
			}
			elseif(is_null($mediaType) || $mediaType == entry::ENTRY_MEDIA_TYPE_ANY || $mediaType == entry::ENTRY_MEDIA_TYPE_AUTOMATIC)
			{
				$this->setDocumentType($object);
			}
		}
		
		if($object->getType() != entryType::DOCUMENT)
		{
			KalturaLog::info("entry id [" . $object->getId() . "] type [" . $object->getType() . "]");
			return true;
		}
	
		if(is_null($mediaType) || $mediaType == entry::ENTRY_MEDIA_TYPE_ANY || $mediaType == entry::ENTRY_MEDIA_TYPE_AUTOMATIC)
		{
			$this->setDocumentType($object);
		}
			
		if($object instanceof DocumentEntry)
		{
			KalturaLog::info("entry id [" . $object->getId() . "] already handled");
			return true;
		}
	
		if ($object->getConversionProfileId())
		{
			$object->setStatus(entryStatus::PRECONVERT);
			$object->save();
		}

		return true;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectCreatedEventConsumer::shouldConsumeCreatedEvent()
	 */
	public function shouldConsumeCreatedEvent(BaseObject $object)
	{
		if($object instanceof entry)
			return true;
		
		return false;
	}
		
	/* (non-PHPdoc)
	 * @see kObjectCreatedEventConsumer::objectCreated()
	 */
	public function objectCreated(BaseObject $object)
	{
		return $this->entryCreated($object);
	}
	
	/* (non-PHPdoc)
	 * @see kObjectAddedEventConsumer::shouldConsumeAddedEvent()
	 */
	public function shouldConsumeAddedEvent(BaseObject $object)
	{
		if($object instanceof flavorAsset && $object->getIsOriginal())
			return true;
		
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectAddedEventConsumer::objectAdded()
	 */
	public function objectAdded(BaseObject $object, BatchJob $raisedJob = null)
	{
		$entry = $object->getentry();
		if($entry->getType() == entryType::DOCUMENT)
		{
			if($entry->getConversionQuality() > 0)
			{
				$syncKey = $object->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
				$path = kFileSyncUtils::getLocalFilePathForKey($syncKey);
				$localFileSync = kFileSyncUtils::getLocalFileSyncForKey($syncKey);
				kJobsManager::addConvertProfileJob($raisedJob, $entry, $object->getId(), $path,$localFileSync);
			}
			else
			{
				// only for documents entry, make the source ready since no conversion profile will be executed by default
				$object->setFlavorParamsId(flavorParams::SOURCE_FLAVOR_ID);
				$object->setStatusLocalReady();
				$object->save();
				
				$entry->setStatusReady();
				$entry->save();
			}
		}
		
		return true;
	}
}