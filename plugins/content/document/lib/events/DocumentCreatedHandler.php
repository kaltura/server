<?php
/**
 * @package plugins.document
 * @subpackage lib
 */
class DocumentCreatedHandler implements kObjectCreatedEventConsumer, kObjectAddedEventConsumer
{
	protected static $fileExtensions = array(
		entry::ENTRY_MEDIA_TYPE_DOCUMENT => array(
			assetParams::CONTAINER_FORMAT_DOC,
			assetParams::CONTAINER_FORMAT_DOCX,
			assetParams::CONTAINER_FORMAT_DOCM,
			assetParams::CONTAINER_FORMAT_DOTX,
			assetParams::CONTAINER_FORMAT_DOTM,
			assetParams::CONTAINER_FORMAT_XLS,
			assetParams::CONTAINER_FORMAT_XLSX,
			assetParams::CONTAINER_FORMAT_XLSM,
			assetParams::CONTAINER_FORMAT_XLTX,
			assetParams::CONTAINER_FORMAT_XLTM,
			assetParams::CONTAINER_FORMAT_XLSB,
			assetParams::CONTAINER_FORMAT_XLAM,
			assetParams::CONTAINER_FORMAT_PPT,
			assetParams::CONTAINER_FORMAT_PPTX,
			assetParams::CONTAINER_FORMAT_PPTM,
			assetParams::CONTAINER_FORMAT_POTX,
			assetParams::CONTAINER_FORMAT_POTM,
			assetParams::CONTAINER_FORMAT_PPAM,
			assetParams::CONTAINER_FORMAT_PPSM,
			assetParams::CONTAINER_FORMAT_ODB,
			assetParams::CONTAINER_FORMAT_ODC,
			assetParams::CONTAINER_FORMAT_ODF,
			assetParams::CONTAINER_FORMAT_ODG,
			assetParams::CONTAINER_FORMAT_ODI,
			assetParams::CONTAINER_FORMAT_ODM,
			assetParams::CONTAINER_FORMAT_ODP,
			assetParams::CONTAINER_FORMAT_ODS,
			assetParams::CONTAINER_FORMAT_ODT,
			assetParams::CONTAINER_FORMAT_OTC,
			assetParams::CONTAINER_FORMAT_OTF,
			assetParams::CONTAINER_FORMAT_OTG,
			assetParams::CONTAINER_FORMAT_OTH,
			assetParams::CONTAINER_FORMAT_OTI,
			assetParams::CONTAINER_FORMAT_OTP,
			assetParams::CONTAINER_FORMAT_OTS,
			assetParams::CONTAINER_FORMAT_OTT,
			assetParams::CONTAINER_FORMAT_OXT
		),
		entry::ENTRY_MEDIA_TYPE_SWF => array(assetParams::CONTAINER_FORMAT_SWF),
		entry::ENTRY_MEDIA_TYPE_PDF => array(assetParams::CONTAINER_FORMAT_PDF),
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
				$fileSync = kFileSyncUtils::getLocalFileSyncForKey($syncKey, false);
				kJobsManager::addConvertProfileJob($raisedJob, $entry, $object->getId(), $fileSync);
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