<?php
class kDropFolderXmlFileHandler
{
	static function getHandlerInstance ($dropFolderType)
	{
		switch ($dropFolderType)
		{
			case DropFolderType::FTP:
			case DropFolderType::LOCAL:
			case DropFolderType::S3:
			case DropFolderType::SCP:
			case DropFolderType::SFTP:
				return new kDropFolderXmlFileHandler();
				break;
			default:
				return KalturaPluginManager::loadObject('kDropFolderXmlFileHandler', $dropFolderType);
				break;
		}
	}
	
	public function getBulkUploadType ()
	{
		return DropFolderXmlBulkUploadPlugin::getBulkUploadTypeCoreValue(DropFolderXmlBulkUploadType::DROP_FOLDER_XML);
	}
 
	
	public function handlePendingDropFolderFile (DropFolder $folder, DropFolderFile $file)
	{
			$this->setContentResources($folder, $file);
			$xmlFile=$this->getXmlFileIfReadyForProcessing($file);
			
			if($xmlFile)
			{
				$statuses = array(DropFolderFileStatus::PENDING, DropFolderFileStatus::WAITING);
				$relatedFiles = DropFolderFilePeer::retrieveByLeadIdAndStatuses($file->getLeadDropFolderFileId(), $statuses);
					
				if($this->setFileProcessing($xmlFile, $relatedFiles))
				{
					$job = $this->addXMLBulkUploadJob($folder, $xmlFile);
					$xmlFile->setBatchJobId($job->getId());
					$xmlFile->save();
				}			
			}
			else
			{
				$file->setStatus(DropFolderFileStatus::WAITING);
				$file->save();				
			}
	}
	
	public function handlePurgedDropFolderFile (DropFolder $folder, DropFolderFile $file)
	{
		if($this->isXmlFile($file->getFileName(), $folder))
		{
			$statuses = array(DropFolderFileStatus::PARSED);
			$parsedDropFolderFiles = DropFolderFilePeer::retrieveByLeadIdAndStatuses($file->getLeadDropFolderFileId(), $statuses);
			if($parsedDropFolderFiles)
			{
				foreach ($parsedDropFolderFiles as $parsedFile) 
				{
					$parsedFile->setStatus(DropFolderFileStatus::PURGED);
					$parsedFile->save();
				}
			}
		}
	}
	
	/**
	 * Parse XML file:
	 * 1. add resources that are still not in the drop folder in status PARSED
	 * 2. update lead drop folder file id for the exisiting files
	 * @param DropFolder $folder
	 * @param DropFolderFile $file
	 * @throws Exception
	 */
	private function setContentResources(DropFolder $folder, DropFolderFile $file)
	{
		if($this->isXmlFile($file->getFileName(), $folder))
		{	
			try 
			{
				$contentResorces = $this->getContentResources($folder, $file);			
				foreach ($contentResorces as $contentResource) 
				{
					$this->addParsedContentResourceFile($contentResource, $file, $folder);
				}
				$file->setLeadDropFolderFileId($file->getId());
				$file->save();
			}
			catch(Exception $e)
			{
				if(	$e->getCode() != DropFolderXmlBulkUploadPlugin::getErrorCodeCoreValue(DropFolderXmlBulkUploadErrorCode::XML_FILE_SIZE_EXCEED_LIMIT) &&
					$e->getCode() != DropFolderXmlBulkUploadPlugin::getErrorCodeCoreValue(DropFolderXmlBulkUploadErrorCode::MALFORMED_XML_FILE))
					{
						KalturaLog::err("Error in setContentResources - ".$e->getMessage());
						$e = new Exception(DropFolderPlugin::ERROR_READING_FILE_MESSAGE.'['.$folder->getPath().'/'.$file->getFileName().']', DropFolderFileErrorCode::ERROR_READING_FILE, $e);
					}
				throw $e;
			}
		}
	}
	
/**
	 * Check if all the files are finished upload
	 * If yes return XML drop folder file instance
	 * otherwise return false
	 * @param DropFolderFile $file
	 */
	private function getXmlFileIfReadyForProcessing(DropFolderFile $file)
	{
		if(!$file->getLeadDropFolderFileId())
		{
			KalturaLog::info('The XML file is not uploaded yet - changing status to WAITING');
			return false;
		}
		$statuses = array(DropFolderFileStatus::PARSED, DropFolderFileStatus::UPLOADING, DropFolderFileStatus::DETECTED);
		$nonReadyFiles = DropFolderFilePeer::retrieveByLeadIdAndStatuses($file->getLeadDropFolderFileId(), $statuses);
		
		if($nonReadyFiles && count($nonReadyFiles) > 0)
		{
			KalturaLog::info('Not all the files finished uploading - changing status to WAITING');
			return false;
		}
		
		$xmlFile = null;
		if($file->getId() == $file->getLeadDropFolderFileId())
			$xmlFile = $file;
		else
			$xmlFile = DropFolderFilePeer::retrieveByPK($file->getLeadDropFolderFileId());
		
		return $xmlFile;
	}
	
	/**
	 * 1. add resource in status PARSED
	 * 2. if already exist, but is not processed yet update lead drop folder file id
	 * 3. if already processed, mark processed file as purged and create new row in status PARSED
	 * @param string $fileName
	 * @param DropFolderFile $leadFile
	 * @param DropFolder $folder
	 * @throws Exception
	 */
	private function addParsedContentResourceFile($fileName, DropFolderFile $leadFile, DropFolder $folder)
	{
		try
	    {
    	    $newFile = new DropFolderFile();
    		$newFile->setDropFolderId($folder->getId());
    		$newFile->setFileName($fileName);
    		$newFile->setFileSize(0);
    		$newFile->setStatus(DropFolderFileStatus::PARSED);
    		$newFile->setLeadDropFolderFileId($leadFile->getId());
    		$newFile->setPartnerId($folder->getPartnerId());
 			$newFile->save();
		}
		catch(PropelException $e)
		{
			if($e->getCause() && $e->getCause()->getCode() == kDropFolderXmlEventsConsumer::MYSQL_CODE_DUPLICATE_KEY) //unique constraint
			{
				$existingFile = DropFolderFilePeer::retrieveByDropFolderIdAndFileName($folder->getId(), $fileName);
				if($existingFile)
				{
					$unprocessedStatuses = array(DropFolderFileStatus::WAITING, DropFolderFileStatus::DETECTED, DropFolderFileStatus::UPLOADING, DropFolderFileStatus::PENDING);
					if(in_array($existingFile->getStatus(), $unprocessedStatuses))
					{
						$existingFile->setLeadDropFolderFileId($leadFile->getId());
						$existingFile->save();						
					}
					else 
					{
						$existingFile->setStatus(DropFolderFileStatus::PURGED);
						$existingFile->save();
						
						$newFileCopy = $newFile->copy();
						$newFileCopy->save();					
					}
				}
			}
			else
			{
				KalturaLog::err('Failed to add content resource for Xml file ['.$leadFile->getId().'] - '.$e->getMessage());
				throw new Exception(DropFolderXmlBulkUploadPlugin::ERROR_ADD_CONTENT_RESOURCE_MESSAGE, DropFolderXmlBulkUploadPlugin::getErrorCodeCoreValue(DropFolderXmlBulkUploadErrorCode::ERROR_ADD_CONTENT_RESOURCE));
			}		
		}	
	}
	
	/**
	 * Load XML file
	 * @param DropFolder $folder
	 * @param DropFolderFile $file
	 * @throws Exception
	 */
	private function getContentResources(DropFolder $folder, DropFolderFile $file)
	{
		$contentResources = array();
		$engineOptions = array(
			'useCmd' => false,
			'asperaTempFolder' => kConf::get('temp_folder') . '/aspera_upload',
		);
		$fileTransferManager = kFileTransferMgr::getInstance($folder->getFileTransferMgrType(), $engineOptions);
		$loginStatus = $folder->loginByCredentialsType($fileTransferManager);

		$fileSizeRemoteFile = $fileTransferManager->fileSize($folder->getPath().'/'.$file->getFileName());
		if($fileSizeRemoteFile > kDropFolderXmlEventsConsumer::MAX_XML_FILE_SIZE)
			throw new Exception(DropFolderXmlBulkUploadPlugin::XML_FILE_SIZE_EXCEED_LIMIT_MESSAGE, DropFolderXmlBulkUploadPlugin::getErrorCodeCoreValue(DropFolderXmlBulkUploadErrorCode::XML_FILE_SIZE_EXCEED_LIMIT));
			
		$xmlPath = $folder->getLocalFilePath($file->getFileName(), $file->getId(), $fileTransferManager, $fileSizeRemoteFile);
		
		$xmlContent = $this->getOriginalOrTransformIfNeeded($folder, $xmlPath);
		
		$xmlDoc = new KDOMDocument();
		$res = $xmlDoc->loadXML($xmlContent);
		
		$localResourceNodes = $xmlDoc->getElementsByTagName(kDropFolderXmlEventsConsumer::DROP_FOLDER_RESOURCE_NODE_NAME);						
		foreach ($localResourceNodes as $localResourceNode) 
		{
			$contentResources[] = $localResourceNode->getAttribute(kDropFolderXmlEventsConsumer::DROP_FOLDER_RESOURCE_PATH_ATTRIBUTE);
		}	
										
		return $contentResources;
	}
	
	private function setFileProcessing(DropFolderFile $file, array $relatedFiles)
	{
		$file->setStatus(DropFolderFileStatus::PROCESSING);
		$affectedRows = $file->save();
		if($affectedRows > 0)
		{
			foreach ($relatedFiles as $relatedFile) 
			{
				if($relatedFile->getId() != $file->getId())
				{
					$relatedFile->setStatus(DropFolderFileStatus::PROCESSING);
					$relatedFile->save();
				}
			}
		}
		return $affectedRows;
	}
	
	protected function getOriginalOrTransformIfNeeded(DropFolder $folder, $xmlPath)
	{
		if(!file_exists($xmlPath) || !filesize($xmlPath))
			throw new Exception('Empty file supplied as input');
		
		if(!$folder->getConversionProfileId())
		{
			KalturaLog::info('No conversion profile found on drop folder [' . $folder->getId() . '] assuming no xsl transformation is needed');
			return file_get_contents($xmlPath);
		}
		
		$conversionProfile = conversionProfile2Peer::retrieveByPK($folder->getConversionProfileId());
		
		if(!$conversionProfile || (strlen($conversionProfile->getXsl()) == 0))
		{
			KalturaLog::info('No conversion profile found Or no xsl transform found');
			return file_get_contents($xmlPath);
		}
		
		$originalXmlDoc = file_get_contents($xmlPath);
		$origianlXml = new KDOMDocument();
		if(!$origianlXml->loadXML($originalXmlDoc))
		{
			$errorMessage = kXml::getLibXmlErrorDescription($originalXmlDoc);
			throw new Exception(DropFolderXmlBulkUploadPlugin::MALFORMED_XML_FILE_MESSAGE, DropFolderXmlBulkUploadPlugin::getErrorCodeCoreValue(DropFolderXmlBulkUploadErrorCode::MALFORMED_XML_FILE));
		}

		libxml_clear_errors();
		$proc = new XSLTProcessor;
		$proc->registerPHPFunctions(kXml::getXslEnabledPhpFunctions());
		$xsl = new KDOMDocument();
		if(!$xsl->loadXML($conversionProfile->getXsl()))
		{
			$errorMessage = kXml::getLibXmlErrorDescription($conversionProfile->getXsl());
			throw new Exception(DropFolderXmlBulkUploadPlugin::MALFORMED_XML_FILE_MESSAGE, DropFolderXmlBulkUploadPlugin::getErrorCodeCoreValue(DropFolderXmlBulkUploadErrorCode::MALFORMED_XML_FILE));
		}
		
		$proc->importStyleSheet($xsl);
		libxml_clear_errors();
		$transformedXml = $proc->transformToXML($origianlXml);
		if(!$transformedXml)
		{
			$errorMessage = kXml::getLibXmlErrorDescription($conversionProfile->getXsl());
			throw new Exception(DropFolderXmlBulkUploadPlugin::MALFORMED_XML_FILE_MESSAGE, DropFolderXmlBulkUploadPlugin::getErrorCodeCoreValue(DropFolderXmlBulkUploadErrorCode::MALFORMED_XML_FILE));
		}
		
		$xmlDoc = new KDOMDocument();
		$res = $xmlDoc->loadXML($transformedXml);
		
		if(!$res)
			throw new Exception(DropFolderXmlBulkUploadPlugin::MALFORMED_XML_FILE_MESSAGE, DropFolderXmlBulkUploadPlugin::getErrorCodeCoreValue(DropFolderXmlBulkUploadErrorCode::MALFORMED_XML_FILE));
		
		
		return $transformedXml;	
	}
	
	/**
	 * Add bulk upload job
	 * @param DropFolder $folder
	 * @param DropFolderFile $leadDropFolderFile
	 * @throws Exception
	 */
	private function addXMLBulkUploadJob(DropFolder $folder, DropFolderFile $leadDropFolderFile)
	{	
		try 
		{
			$coreBulkUploadType = DropFolderXmlBulkUploadPlugin::getBulkUploadTypeCoreValue(DropFolderXmlBulkUploadType::DROP_FOLDER_XML);
					
			$objectId = $leadDropFolderFile->getId();
			$objectType = DropFolderXmlBulkUploadPlugin::getBatchJobObjectTypeCoreValue(DropFolderBatchJobObjectType::DROP_FOLDER_FILE);
			$partner = PartnerPeer::retrieveByPK($folder->getPartnerId());
			
			$data = KalturaPluginManager::loadObject('kBulkUploadJobData', $coreBulkUploadType);
			/* @var $data kBulkUploadJobData */
			$data->setUploadedBy(kDropFolderXmlEventsConsumer::UPLOADED_BY);
			$data->setFileName($leadDropFolderFile->getFileName());
			$data->setBulkUploadObjectType(BulkUploadObjectType::ENTRY);
						
			$objectData = new kBulkUploadEntryData();
			$objectData->setConversionProfileId($folder->getConversionProfileId());
			$data->setObjectData($objectData);
	
			$job = kJobsManager::addBulkUploadJob($partner, $data, $coreBulkUploadType, $objectId, $objectType);
			return $job;
		}
		catch (Exception $e)
		{
			KalturaLog::err("Error adding BulkUpload job -".$e->getMessage());
			throw new Exception(DropFolderXmlBulkUploadPlugin::ERROR_ADDING_BULK_UPLOAD_MESSAGE, DropFolderXmlBulkUploadPlugin::getErrorCodeCoreValue(DropFolderXmlBulkUploadErrorCode::ERROR_ADDING_BULK_UPLOAD));
		}
			
	}
	
	
	/**
	 * Check if file is XML according to the file pattern set on the drop folder
	 * The comparison is case insensitive
	 * @param string $fileName
	 * @param DropFolder $folder
	 */
	private function isXmlFile($fileName, DropFolder $folder)
	{
		$isXml = false;
		$fileNamePatterns = trim($folder->getFileNamePatterns(), ' *');
		if($fileNamePatterns)
			$isXml = stristr($fileName, $fileNamePatterns);
		else
			$isXml = true;
		return $isXml;
	}
}
