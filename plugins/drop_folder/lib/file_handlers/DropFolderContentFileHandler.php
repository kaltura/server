<?php

class DropFolderContentFileHandler extends DropFolderFileHandler
{
	
	/**
	 * @var DropFolderContentFileHandlerConfig
	 */
	private $config;
	
	/**
	 * 
	 * @var DropFolderFile
	 */
	private $dropFolderFile;
	
	
	
	
	public function setConfig(DropFolderFileHandlerConfig $config) {
		if ($config instanceof DropFolderContentFileHandlerConfig) {
			$this->config = $config;
		}
	}

	public function getType() {
		return DropFolderFileHandlerType::CONTENT;
	}


	public function handleFile($dropFolderFileId)
	{
		$this->dropFolderFile = DropFolderFilePeer::retrieveByPK($dropFolderFileId);
		if (!$this->dropFolderFile) {
			//TODO: add error!
		}
		
		if ($this->config)
		{
			//TODO: error - config not set
		}
		
		//TODO: parse slug and flavor and save on drop folder file object
		
		switch ($this->config->getContentMatchPolicy())
		{
			case DropFolderContentFileHandlerMatchPolicy::ADD_AS_NEW:
				$entryAdded = $this->addAsNewContent();
				break;
			
			case DropFolderContentFileHandlerMatchPolicy::MATCH_EXISTING_OR_KEEP_IN_FOLDER:
				$entryAdded = $this->addAsExistingContent();
				break;
				
			case DropFolderContentFileHandlerMatchPolicy::MATCH_EXISTING_OR_ADD_AS_NEW:
				$entryAdded = $this->addAsExistingContent();
				if (!$entryAdded) {
					$entryAdded = $this->addAsNewContent();
				}
				break;
		}

		if ($entryAdded)
		{
			//TODO: update status to HANDLED
		}
		else
		{
			//TODO: update status to ERROR and update error description
		}		
	}
	
	private function getDefaultApiEntryObject()
	{
		$templateDbEntry = null;
		$dropFolder = DropFolderPeer::retrieveByPK($this->dropFolderFile->getDropFolderId());
		$conversionProfileId = $dropFolder->getConversionProfileId();
		$conversionProfile = myPartnerUtils::getConversionProfile2ForPartner($this->getPartnerId(), $conversionProfileId);
		
		if($conversionProfile && $conversionProfile->getDefaultEntryId())
		{
			$templateDbEntry = entryPeer::retrieveByPK($conversionProfile->getDefaultEntryId());
		}		
		
		$defaultApiEntry = new KalturaBaseEntry();
		if ($templateDbEntry)
		{
			switch ($templateDbEntry->getType())
			{
				case entryType::MEDIA_CLIP:
					$defaultApiEntry = new KalturaMediaEntry();
					$defaultApiEntry = $defaultApiEntry->fromObject($templateDbEntry); 
					$defaultApiEntry->ingestionProfileId = $conversionProfileId;
					break;
				/*
				 * document service doesn't yet support KalturaResource
				 * 
				case entryType::DOCUMENT:
					$defaultApiEntry = new KalturaDocumentEntry();
					$defaultApiEntry = $defaultApiEntry->fromObject($templateDbEntry);
					$defaultApiEntry->conversionProfileId = $conversionProfileId;
					break;
				*/
				default:
					$defaultApiEntry = new KalturaBaseEntry();
					$defaultApiEntry = $defaultApiEntry->fromObject($templateDbEntry);
					// no support for drop folder's ingestion profile for KalturaBaseEntry
					break;
			}
			
		}
		
		return $defaultApiEntry;	
	}
	
	
	
	/**
	 * Add a new entry with the given drop folder file as the resource.
	 * Entry's ingestion profile id should be the one defined on the file's drop folder object.
	 */
	private function addAsNewContent()
	{ 
		$dropFolderFileResource = new KalturaDropFolderFileResource();
		$dropFolderFileResource->dropFolderFileId = $this->dropFolderFile->getId();
		
		$defaultApiEntry = $this->getDefaultApiEntryObject();
		$defaultApiEntry->name = $this->dropFolderFile->getParsedSlug();
		
		//TODO: what to do with the parsed flavor ?  how to add new entry as a specific flavor ?
		
		kCurrentContext::$partner_id = $this->dropFolderFile->getPartnerId();
		
		
		switch ($defaultApiEntry->type)
		{
			case KalturaEntryType::MEDIA_CLIP:
				$serviceInstance = new MediaService();
				$serviceName = 'media';
				break;
				
			default:
				$serviceInstance = new BaseEntryService();
				$serviceName = 'baseEntry';
				
				break;
		}
		
		$serviceInstance->initService($serviceName, $serviceName, 'add');
		$addedEntry = $serviceInstance->addAction($defaultApiEntry, $dropFolderFileResource);
		
		//TODO: return true/false - if entry was added
	}
	
	
	
	private function addAsExistingContent()
	{
		//TODO: implement
				
		//TODO: search for an entry that matches the slug name - if no entry -> quit
		
		//TODO: what to do ?? - if no flavor is set according to slugRegex -> update entry with current file as the source flavor ????		
		
		//TODO: if flavor is set and found -> update entry with current file as the required flavor (baseEntry->update)
		
		//TODO: return true/false - if entry was added
	}
}