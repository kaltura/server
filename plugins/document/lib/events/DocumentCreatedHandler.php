<?php
class DocumentCreatedHandler implements kObjectCreatedEventConsumer
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
				$object->setType(entry::ENTRY_TYPE_DOCUMENT);
				break;
			}
		}
	}
	
	public function entryCreated(entry $object)
	{
		$mediaType = null;
		if($object->getType() == entry::ENTRY_TYPE_AUTOMATIC)
		{
			KalturaLog::debug("entry id [" . $object->getId() . "] type [" . $object->getType() . "] source link [" . $object->getSourceLink() . "]");
			
			$mediaType = $object->getMediaType();
			if(isset(self::$fileExtensions[$mediaType]))
			{
				$object->setType(entry::ENTRY_TYPE_DOCUMENT);
			}
			elseif(is_null($mediaType) || $mediaType == entry::ENTRY_MEDIA_TYPE_ANY || $mediaType == entry::ENTRY_MEDIA_TYPE_AUTOMATIC)
			{
				$this->setDocumentType($object);
			}
		}
		
		if($object->getType() != entry::ENTRY_TYPE_DOCUMENT)
		{
			KalturaLog::debug("entry id [" . $object->getId() . "] type [" . $object->getType() . "]");
			return;
		}
	
		if(is_null($mediaType) || $mediaType == entry::ENTRY_MEDIA_TYPE_ANY || $mediaType == entry::ENTRY_MEDIA_TYPE_AUTOMATIC)
		{
			$this->setDocumentType($object);
		}
			
		if($object instanceof DocumentEntry)
		{
			KalturaLog::debug("entry id [" . $object->getId() . "] already handled");
			return;
		}
	
		KalturaLog::debug("Handling object [" . get_class($object) . "] type [" . $object->getType() . "] id [" . $object->getId() . "] status [" . $object->getStatus() . "]");

		if ($object->getConversionProfileId())
		{
			$object->setStatus(entry::ENTRY_STATUS_PRECONVERT);
			$object->save();
		}

	}
		
	/**
	 * @param BaseObject $object
	 */
	public function objectCreated(BaseObject $object)
	{
		if($object instanceof entry)
			return $this->entryCreated($object);
	}
}