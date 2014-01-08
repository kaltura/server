<?php
class kDropFolderXmlEventsConsumer implements kBatchJobStatusEventConsumer, kObjectChangedEventConsumer
{
	const UPLOADED_BY = 'Drop Folder';
	const DROP_FOLDER_RESOURCE_NODE_NAME = 'dropFolderFileContentResource';
	const DROP_FOLDER_RESOURCE_PATH_ATTRIBUTE = 'filePath';
	const MYSQL_CODE_DUPLICATE_KEY = 23000;
	const MAX_XML_FILE_SIZE = 10485760;
	
	
	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::objectChanged()
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns) 
	{
		try 
		{
			$folder = DropFolderPeer::retrieveByPK($object->getDropFolderId());
			switch($object->getStatus())
			{
				case DropFolderFileStatus::PENDING:
					$this->onXmlDropFolderFileStatusChangedToPending($folder, $object);
					break;
				case DropFolderFileStatus::PURGED:
					$this->onXmlDropFolderFileStatusChangedToPurged($folder, $object);
					break;
			}
		}
		catch(Exception $e)
		{
			KalturaLog::err('Failed to process objectChangedEvent for drop folder file ['.$object->getDropFolderId().'] - '.$e->getMessage());
		}
		return true;
	}

	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::shouldConsumeChangedEvent()
	 */
	public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns) 
	{
		try 
		{
			if(	$object instanceof DropFolderFile && 
				($object->getStatus() == DropFolderFileStatus::PENDING || $object->getStatus() == DropFolderFileStatus::PURGED) && 
				in_array(DropFolderFilePeer::STATUS, $modifiedColumns))
			{
				$folder = DropFolderPeer::retrieveByPK($object->getDropFolderId());
				if($folder->getFileHandlerType() == DropFolderXmlBulkUploadPlugin::getFileHandlerTypeCoreValue(DropFolderXmlFileHandlerType::XML))
					return true;
			}
		}
		catch(Exception $e)
		{
			KalturaLog::err('Failed to process shouldConsumeChangedEvent - '.$e->getMessage());
		} 		
		
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::shouldConsumeJobStatusEvent()
	 */
	public function shouldConsumeJobStatusEvent(BatchJob $dbBatchJob)
	{
		try 
		{
			$coreBulkUploadType = DropFolderXmlBulkUploadPlugin::getBulkUploadTypeCoreValue(DropFolderXmlBulkUploadType::DROP_FOLDER_XML);
			$jobStatuses = array(BatchJob::BATCHJOB_STATUS_FINISHED, BatchJob::BATCHJOB_STATUS_FINISHED_PARTIALLY, BatchJob::BATCHJOB_STATUS_FAILED, BatchJob::BATCHJOB_STATUS_FATAL, BatchJob::BATCHJOB_STATUS_QUEUED,);
			$isMatch =  $dbBatchJob->getJobType() == BatchJobType::BULKUPLOAD && 
						$dbBatchJob->getJobSubType() == $coreBulkUploadType &&
						in_array($dbBatchJob->getStatus(), $jobStatuses);
			return $isMatch;	
		}
		catch(Exception $e)
		{
			KalturaLog::err('Failed to process shouldConsumeJobStatusEvent - '.$e->getMessage());
		}
		return false;	
	}
	
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::updatedJob()
	 */
	public function updatedJob(BatchJob $dbBatchJob)
	{
		try 
		{
			$this->onBulkUploadJobStatusUpdated($dbBatchJob);
		}
		catch(Exception $e)
		{
			KalturaLog::err('Failed to process updatedJob - '.$e->getMessage());
		}
		return true;
	}
				
	private function onBulkUploadJobStatusUpdated(BatchJob $dbBatchJob)
	{
		$xmlDropFolderFile = DropFolderFilePeer::retrieveByPK($dbBatchJob->getObjectId());
		if(!$xmlDropFolderFile)
			return;		
		KalturaLog::debug('object id '.$dbBatchJob->getObjectId());
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_QUEUED:
				$jobData = $dbBatchJob->getData();
				if(!is_null($jobData->getFilePath()))
				{
					$syncKey = $dbBatchJob->getSyncKey(BatchJob::FILE_SYNC_BATCHJOB_SUB_TYPE_BULKUPLOAD);
					try{
						kFileSyncUtils::moveFromFile($jobData->getFilePath(), $syncKey, true);
					}
					catch(Exception $e)
					{
						KalturaLog::err($e);
						throw new APIException(APIErrors::BULK_UPLOAD_CREATE_CSV_FILE_SYNC_ERROR);
					}
					
					$filePath = kFileSyncUtils::getLocalFilePathForKey($syncKey);
					$jobData->setFilePath($filePath);
					
					//save new info on the batch job
					$dbBatchJob->setData($jobData);
					$dbBatchJob->save();
				}
				break;
			case BatchJob::BATCHJOB_STATUS_FINISHED:
			case BatchJob::BATCHJOB_STATUS_FINISHED_PARTIALLY:
				KalturaLog::debug("Handling Bulk Upload finished");
				$xmlDropFolderFile->setStatus(DropFolderFileStatus::HANDLED);
				$xmlDropFolderFile->save();
				break;
			case BatchJob::BATCHJOB_STATUS_FAILED:
			case BatchJob::BATCHJOB_STATUS_FATAL:
				KalturaLog::debug("Handling Bulk Upload failed");
				$relatedFiles = DropFolderFilePeer::retrieveByLeadIdAndStatuses($xmlDropFolderFile->getId(), array(DropFolderFileStatus::PROCESSING));
				foreach ($relatedFiles as $relatedFile) 
				{
					$this->setFileError($relatedFile, DropFolderFileStatus::ERROR_HANDLING, 
										DropFolderXmlBulkUploadPlugin::getErrorCodeCoreValue(DropFolderXmlBulkUploadErrorCode::ERROR_IN_BULK_UPLOAD),
										DropFolderXmlBulkUploadPlugin::ERROR_IN_BULK_UPLOAD_MESSAGE);
				}			
				break;				
		}		
		
	}
			
	private function setFileProcessing(DropFolderFile $file, array $relatedFiles)
	{
		$file->setStatus(DropFolderFileStatus::PROCESSING);
		$affectedRows = $file->save();
		KalturaLog::debug('Changing file status to Processing, file id ['.$file->getId().'] affected rows ['.$affectedRows.']');
		if($affectedRows > 0)
		{
			foreach ($relatedFiles as $relatedFile) 
			{
				if($relatedFile->getId() != $file->getId())
				{
					KalturaLog::debug('Changing file status to Processing, file id ['.$relatedFile->getId().']');
					$relatedFile->setStatus(DropFolderFileStatus::PROCESSING);
					$relatedFile->save();
				}
			}
		}
		return $affectedRows;
	}
	
	private function setFileError(DropFolderFile $file, $status, $errorCode, $errorDescription)
	{
		KalturaLog::err('Error with file ['.$file->getId().'] -'.$errorDescription);
		
		$file->setStatus($status);
		$file->setErrorCode($errorCode);
		$file->setErrorDescription($errorDescription);
		$file->save();				
		
	}

	/**
	 * Mark any PARSED files as PURGED in case the purged file is an XML
	 * @param DropFolder $folder
	 * @param DropFolderFile $file
	 */
	private function onXmlDropFolderFileStatusChangedToPurged(DropFolder $folder, DropFolderFile $file)
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
	 * Validate if all the files ready:
	 * 1. Yes: add BulkUpload job
	 * 2. No: set status to Waiting
	 * @param DropFolder $folder
	 * @param DropFolderFile $file
	 */
	private function onXmlDropFolderFileStatusChangedToPending(DropFolder $folder, DropFolderFile $file)
	{
		KalturaLog::debug('in onXmlDropFolderFileStatusChangedToPending file id ['.$file->getId().'] folder id ['.$folder->getId().']');
		$relatedFiles = array();
		try 
		{
			$this->setContentResources($folder, $file);
			$xmlFile=$this->getXmlFileIfReadyForProcessing($file);
			
			if($xmlFile)
			{
				KalturaLog::debug('All files finished uploading');
	
				KalturaLog::debug('Retrieving related files from the database');
				$statuses = array(DropFolderFileStatus::PENDING, DropFolderFileStatus::WAITING);
				$relatedFiles = DropFolderFilePeer::retrieveByLeadIdAndStatuses($file->getLeadDropFolderFileId(), $statuses);
					
				if($this->setFileProcessing($xmlFile, $relatedFiles))
				{
					$job = $this->addDropFolderXmlBulkUploadJob($folder, $xmlFile);
					KalturaLog::debug('BulkUpload added with job id ['.$job->getId().']');
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
		catch (Exception $e)
		{
			KalturaLog::err("Error in  onXmlDropFolderFileStatusChangedToPending -".$e->getMessage());
			if($e->getCode() == DropFolderXmlBulkUploadPlugin::getErrorCodeCoreValue(DropFolderXmlBulkUploadErrorCode::ERROR_ADDING_BULK_UPLOAD))
			{
				foreach ($relatedFiles as $relatedFile) 
				{
					$this->setFileError($relatedFile, DropFolderFileStatus::ERROR_HANDLING, $e->getCode(), $e->getMessage());											
				}				
			}
			else
				$this->setFileError($file, DropFolderFileStatus::ERROR_HANDLING, $e->getCode(), $e->getMessage());														
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
	 * Load XML file
	 * @param DropFolder $folder
	 * @param DropFolderFile $file
	 * @throws Exception
	 */
	private function getContentResources(DropFolder $folder, DropFolderFile $file)
	{
		KalturaLog::debug('Parsing content resources from Xml');
		
		$contentResources = array();
		$engineOptions = array(
			'useCmd' => false,
			'asperaTempFolder' => kConf::get('temp_folder') . '/aspera_upload',
		);
		$fileTransferManager = kFileTransferMgr::getInstance($folder->getFileTransferMgrType(), $engineOptions);
		$loginStatus = $folder->loginByCredentialsType($fileTransferManager);
		
		if($fileTransferManager->fileSize($folder->getPath().'/'.$file->getFileName()) > self::MAX_XML_FILE_SIZE)
			throw new Exception(DropFolderXmlBulkUploadPlugin::XML_FILE_SIZE_EXCEED_LIMIT_MESSAGE, DropFolderXmlBulkUploadPlugin::getErrorCodeCoreValue(DropFolderXmlBulkUploadErrorCode::XML_FILE_SIZE_EXCEED_LIMIT));
			
		$xmlPath = $folder->getLocalFilePath($file->getFileName(), $file->getId(), $fileTransferManager);
		KalturaLog::debug('Local XML path ['.$xmlPath.']');
		$xmlDoc = new KDOMDocument();
		$res = $xmlDoc->load($xmlPath);
		
		if(!$res)
			throw new Exception(DropFolderXmlBulkUploadPlugin::MALFORMED_XML_FILE_MESSAGE, DropFolderXmlBulkUploadPlugin::getErrorCodeCoreValue(DropFolderXmlBulkUploadErrorCode::MALFORMED_XML_FILE));
			
		$localResourceNodes = $xmlDoc->getElementsByTagName(self::DROP_FOLDER_RESOURCE_NODE_NAME);						
		foreach ($localResourceNodes as $localResourceNode) 
		{
			$contentResources[] = $localResourceNode->getAttribute(self::DROP_FOLDER_RESOURCE_PATH_ATTRIBUTE);
		}									
		return $contentResources;
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
		KalturaLog::debug('Trying to add content resource in status PARSED ['.$fileName.']');
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
			if($e->getCause()->getCode() == self::MYSQL_CODE_DUPLICATE_KEY) //unique constraint
			{
				$existingFile = DropFolderFilePeer::retrieveByDropFolderIdAndFileName($folder->getId(), $fileName);
				if($existingFile)
				{
					$unprocessedStatuses = array(DropFolderFileStatus::WAITING, DropFolderFileStatus::DETECTED, DropFolderFileStatus::UPLOADING, DropFolderFileStatus::PENDING);
					if(in_array($existingFile->getStatus(), $unprocessedStatuses))
					{
						KalturaLog::debug('Updating drop folder file ['.$existingFile->getId().'] with lead id ['.$leadFile->getId().']');
						$existingFile->setLeadDropFolderFileId($leadFile->getId());
						$existingFile->save();						
					}
					else 
					{
						KalturaLog::debug('Deleting drop folder file ['.$existingFile->getId().']');
						$existingFile->setStatus(DropFolderFileStatus::PURGED);
						$existingFile->save();
						
						KalturaLog::debug('Adding new drop folder file ['.$newFile->getFileName().'] with status PARSED');
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
	 * Check if all the files are finished upload
	 * If yes return XML drop folder file instance
	 * otherwise return false
	 * @param DropFolderFile $file
	 */
	private function getXmlFileIfReadyForProcessing(DropFolderFile $file)
	{
		KalturaLog::debug('Check if file ['.$file->getId().'] ready for processing ');
		
		if(!$file->getLeadDropFolderFileId())
		{
			KalturaLog::debug('The XML file is not uploaded yet - changing status to WAITING');
			return false;
		}
		$statuses = array(DropFolderFileStatus::PARSED, DropFolderFileStatus::UPLOADING, DropFolderFileStatus::DETECTED);
		$nonReadyFiles = DropFolderFilePeer::retrieveByLeadIdAndStatuses($file->getLeadDropFolderFileId(), $statuses);
		
		if($nonReadyFiles && count($nonReadyFiles) > 0)
		{
			KalturaLog::debug('Not all the files finished uploading - changing status to WAITING');
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
	 * Add bulk upload job
	 * @param DropFolder $folder
	 * @param DropFolderFile $leadDropFolderFile
	 * @throws Exception
	 */
	private function addDropFolderXmlBulkUploadJob(DropFolder $folder, DropFolderFile $leadDropFolderFile)
	{	
		KalturaLog::debug('Adding BulkUpload job');
		try 
		{
			$coreBulkUploadType = DropFolderXmlBulkUploadPlugin::getBulkUploadTypeCoreValue(DropFolderXmlBulkUploadType::DROP_FOLDER_XML);
					
			$objectId = $leadDropFolderFile->getId();
			$objectType = DropFolderXmlBulkUploadPlugin::getBatchJobObjectTypeCoreValue(DropFolderBatchJobObjectType::DROP_FOLDER_FILE);
			$partner = PartnerPeer::retrieveByPK($folder->getPartnerId());
			
			$data = KalturaPluginManager::loadObject('kBulkUploadJobData', $coreBulkUploadType);
			$data->setUploadedBy(self::UPLOADED_BY);
			$data->setFileName($leadDropFolderFile->getFileName());
						
			$objectData = new kBulkUploadEntryData();
			KalturaLog::debug('conversion profile id: '.$folder->getConversionProfileId());
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
		KalturaLog::debug('checking if file '.$fileName.' is XML');
		$isXml = false;
		$fileNamePatterns = trim($folder->getFileNamePatterns(), ' *');
		KalturaLog::debug('file name pattern [ '.$fileNamePatterns.']');
		if($fileNamePatterns)
			$isXml = stristr($fileName, $fileNamePatterns);
		else
			$isXml = true;
		return $isXml;
	}
	
}