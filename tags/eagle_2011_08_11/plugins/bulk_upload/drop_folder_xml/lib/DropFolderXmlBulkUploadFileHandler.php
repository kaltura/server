<?php

/**
 * @package dropFolder 
 * @subpackage Scheduler.fileHandlers
 */
class DropFolderXmlBulkUploadFileHandler extends DropFolderFileHandler
{	
	const DROP_FOLDER_RESOURCE_NODE_NAME = 'dropFolderFileContentResource';
	const DROP_FOLDER_RESOURCE_FILE_ID_ATTRIBUTE = 'dropFolderFileId';
	const DROP_FOLDER_RESOURCE_PATH_ATTRIBUTE = 'filePath';
	const DROP_FOLDER_RESOURCE_FILE_SIZE_PARAM = 'fileSize';
	const DROP_FOLDER_RESOURCE_FILE_CHECKSUM_PARAM = 'fileChecksum';
	
	/**
	* @var string
	*/
	private $tempDirectory = null;
	
	/**
	* @var string
	*/
	private $uploadedBy = 'Drop Folder';
	
	/**
	 * @var kFileTransferMgr
	 */
	private $fileTransferMgr = null;
	
	public function getType() 
	{
		return KalturaDropFolderFileHandlerType::XML;
	}
	
	public function setConfig(KalturaClient $client, KalturaDropFolderFile $dropFolderFile, KalturaDropFolder $dropFolder, KSchedularTaskConfig $taskConfig)
	{
		parent::setConfig($client, $dropFolderFile, $dropFolder, $taskConfig);
		if($taskConfig->params->uploadedBy)
			$this->uploadedBy = $taskConfig->params->uploadedBy;
	}
	
	public function handle()
	{
		$this->tempDirectory = sys_get_temp_dir();
		if (!is_dir($this->tempDirectory)) {
			KalturaLog::err('Missing temporary directory');
			return false;
		}
		
		// check prerequisites
		$checkConfig = $this->checkConfig();
		if (!$checkConfig) {
			KalturaLog::err('Missing required configurations');
			return false;
		}
		
		$this->fileTransferMgr =  DropFolderBatchUtils::getFileTransferManager($this->dropFolder);
		if (!$this->fileTransferMgr) {
		    $this->dropFolderFile->status = KalturaDropFolderFileStatus::ERROR_HANDLING;
			$this->dropFolderFile->errorCode = KalturaDropFolderFileErrorCode::INTERNAL_ERROR;
			$this->dropFolderFile->errorDescription = 'Internal server error - cannot initiate file transfer manager';
			KalturaLog::err($this->dropFolderFile->errorDescription);
			$this->updateDropFolderFile();
			return false;
		}
		$xmlPath = $this->getLocalXmlFilePath();
		
		if (!$xmlPath) {
			$this->dropFolderFile->status = KalturaDropFolderFileStatus::ERROR_HANDLING;
			$this->dropFolderFile->errorCode = KalturaDropFolderFileErrorCode::ERROR_READING_FILE;
			$this->dropFolderFile->errorDescription = 'Cannot read file at path ['.$this->dropFolder->path.'/'.$this->dropFolderFile->fileName.']';
			KalturaLog::err($this->dropFolderFile->errorDescription);
			$this->updateDropFolderFile();
			return false;
		}
		
		$xmlDoc = new DOMDocument();
		$xmlDoc->load($xmlPath);

		if (!$xmlDoc) {
			$this->dropFolderFile->status = KalturaDropFolderFileStatus::ERROR_HANDLING;
			$this->dropFolderFile->errorCode = KalturaDropFolderFileErrorCode::ERROR_READING_FILE;
			$this->dropFolderFile->errorDescription = "Cannot parse XML file at [$xmlPath]";
			KalturaLog::err($this->dropFolderFile->errorDescription);
			$this->updateDropFolderFile();
			return false;
		}
		
		$localResources = $xmlDoc->getElementsByTagName(self::DROP_FOLDER_RESOURCE_NODE_NAME);
		
		if (!$localResources) {
			$this->dropFolderFile->status = KalturaDropFolderFileStatus::ERROR_HANDLING;
			$this->dropFolderFile->errorCode = KalturaDropFolderFileErrorCode::ERROR_READING_FILE;
			$this->dropFolderFile->errorDescription = "Cannot parse XML file at [$xmlPath]";
			KalturaLog::err($this->dropFolderFile->errorDescription);
			$this->updateDropFolderFile();
			return false;
		}
		
		$addtionalDropFolderFileIds = array();
		$replaceResources = array();
		
		$localResourcesLength = $localResources->length;
		foreach ($localResources as $local)
		{
			// already have drop folder file id
			if(!is_null($this->getDropFolderFileId($local)))
				continue;
				
			// replacement/modification of $local must not happen inside this foreach loop
			$dropFolderFileId = $this->checkFileExists($local);
			if (is_null($dropFolderFileId)) {
				KalturaLog::debug('Some required files do not exist in the drop folder - changing status to WAITING');
				$this->dropFolderFile->status = KalturaDropFolderFileStatus::WAITING;
				KalturaLog::debug('Changing status to WAITING');
				$this->updateDropFolderFile();
				return false;
			}
			$localVerified = $this->verifyLocalResource($local);
			if (!$localVerified) {
				$this->dropFolderFile->status = KalturaDropFolderFileStatus::ERROR_HANDLING; // error code and description already set
				KalturaLog::err($this->dropFolderFile->errorDescription);
				$this->updateDropFolderFile();
				return false;
			}
			$replaceResources[] = array($local, $dropFolderFileId);
			$addtionalDropFolderFileIds[] = $dropFolderFileId;
		}
		
		foreach ($replaceResources as $replace)
		{
			$this->replaceResource($replace[0], $replace[1], $xmlDoc);
		}
		
		// create a temporary XML file from the modified $xmlDoc
		$tempFile = $this->tempDirectory.DIRECTORY_SEPARATOR.uniqid().'_'.$this->dropFolderFile->fileName;
		$xmlDoc->save($tempFile);
		$tempFileRealPath = realpath($tempFile);
		if (!$tempFileRealPath || !is_file($tempFileRealPath)) {
			$this->dropFolderFile->status = KalturaDropFolderFileStatus::ERROR_HANDLING;
			$this->dropFolderFile->errorCode = KalturaDropFolderFileErrorCode::ERROR_WRITING_TEMP_FILE;
			$this->dropFolderFile->errorDescription = "Error writing temporary file [$tempFileRealPath]";
			KalturaLog::err($this->dropFolderFile->errorDescription);
			$this->updateDropFolderFile();
			return false;
		}
		
		$conversionProfile = $this->getConversionProfile();
		
		// add bulk upload of type KalturaBulkUploadType::DROP_FOLDER_XML
		try
		{
			$this->impersonate($this->dropFolderFile->partnerId);
			$this->kClient->bulkUpload->add($conversionProfile->id, $tempFileRealPath, KalturaBulkUploadType::DROP_FOLDER_XML, $this->uploadedBy);
			$this->unimpersonate();
		}
		catch (Exception $e)
		{
			$this->unimpersonate();
			$this->dropFolderFile->status = KalturaDropFolderFileStatus::ERROR_HANDLING;
			$this->dropFolderFile->errorCode = KalturaDropFolderFileErrorCode::ERROR_ADDING_BULK_UPLOAD;
			$this->dropFolderFile->errorDescription = 'Error adding bulk upload - '.$e->getMessage();
			KalturaLog::err($this->dropFolderFile->errorDescription);
			$this->updateDropFolderFile();
			KalturaLog::err($this->dropFolderFile->errorDescription);
			return false;
		}
		
		//delete the temporary file
		@unlink($tempFileRealPath);
	
		// update all relevant drop folder files
		$addtionalDropFolderFileIds[] = $this->dropFolderFile->id;
		$this->setAsHandled($addtionalDropFolderFileIds);
		
		KalturaLog::debug('Drop folder file ['.$this->dropFolderFile->id.'] handled successfully');
		
		return true; // file handled
	}
	
	
	private function getDropFolderFileId(DOMElement $localResource)
	{
		if(!$localResource->hasAttribute(self::DROP_FOLDER_RESOURCE_FILE_ID_ATTRIBUTE))
			return null;
		
		return $localResource->getAttribute(self::DROP_FOLDER_RESOURCE_FILE_ID_ATTRIBUTE);
	}
	
	
	private function checkFileExists(DOMElement $localResource)
	{
		$filePath = $localResource->getAttribute(self::DROP_FOLDER_RESOURCE_PATH_ATTRIBUTE);
		
		$dropFolderFileFilter = new KalturaDropFolderFileFilter();
		$dropFolderFileFilter->dropFolderIdEqual = $this->dropFolderFile->dropFolderId;
		$dropFolderFileFilter->fileNameEqual = $filePath;
		$dropFolderFileFilter->statusIn = KalturaDropFolderFileStatus::PENDING.','.KalturaDropFolderFileStatus::WAITING.','.KalturaDropFolderFileStatus::NO_MATCH;
		
		$pager = new KalturaFilterPager();
		$pager->pageIndex = 1;
		$pager->pageSize = 1;
		
		$listResult = $this->kClient->dropFolderFile->listAction($dropFolderFileFilter, $pager);
		if (isset($listResult->objects[0])) {
			$fileId = $listResult->objects[0]->id;
			return $fileId;
		}
		return null;
	}
	
	private function verifyLocalResource(DOMElement $localResource)
	{
		$filePath = $localResource->getAttribute(self::DROP_FOLDER_RESOURCE_PATH_ATTRIBUTE);
		$dropFolderPath = $this->dropFolder->path.'/'.$filePath;
		
		if ($this->dropFolder->type == KalturaDropFolderType::LOCAL) {
	        $dropFolderPath = realpath($dropFolderPath);
	    }
	    
		if (!$dropFolderPath) {
			$this->dropFolderFile->errorCode = KalturaDropFolderFileErrorCode::ERROR_READING_FILE;
			$this->dropFolderFile->errorDescription = "Cannot find file at [$dropFolderPath]";
			return false;	
		}
		
		$fileSize = $localResource->getElementsByTagName(self::DROP_FOLDER_RESOURCE_FILE_SIZE_PARAM);
		$fileSize = ($fileSize->length > 0) ? $fileSize->item(0)->nodeValue : null;
		if (!is_null($fileSize))
		{
		    $realSize = $this->fileTransferMgr->fileSize($dropFolderPath);
			if ($fileSize != $realSize) {
				$this->dropFolderFile->errorCode = KalturaDropFolderFileErrorCode::LOCAL_FILE_WRONG_SIZE;
				$this->dropFolderFile->errorDescription = "Wrong filesize [$realSize] for file [$dropFolderPath]";
				return false;
			}
			KalturaLog::debug("Filesize [$fileSize] verified for local resource [$filePath]");
		}
		
		$fileChecksumTags = $localResource->getElementsByTagName(self::DROP_FOLDER_RESOURCE_FILE_CHECKSUM_PARAM);
		$fileChecksum = ($fileChecksumTags->length > 0) ? (string)$fileChecksumTags->item(0)->nodeValue : null;
		
		if ($this->dropFolder->type != KalturaDropFolderType::LOCAL)
		{
		    //TODO: ok not to support for remote drop folders ?
		    KalturaLog::debug('Checksum verification is only supported for local drop folders');
		}
		else
		{
    		if (!is_null($fileChecksum))
    		{
    			$checksumType = $fileChecksumTags->item(0)->getAttribute('type');
    			
    			if ($checksumType == 'sha1') {
    				$localChecksum = sha1_file($dropFolderPath);
    			}
    			else {
    				$localChecksum = md5_file($dropFolderPath);
    			}
    			if ($fileChecksum != $localChecksum) {
    				$this->dropFolderFile->errorCode = KalturaDropFolderFileErrorCode::LOCAL_FILE_WRONG_CHECKSUM;
    				$this->dropFolderFile->errorDescription = "Wrong checksum [$localChecksum] for file [$dropFolderPath]";
    				return false;
    			}
    			KalturaLog::debug("Checksum [$fileChecksum] verified for local resource [$filePath]");
    		}
		}

		return true;
	}
	
	private function replaceResource(DOMElement $localResource, $dropFolderFileId, DOMDocument $xmlDoc)
	{
		$localResource->setAttribute(self::DROP_FOLDER_RESOURCE_FILE_ID_ATTRIBUTE, $dropFolderFileId);
	}
	
	/**
	 * Update the status of all drop folder files with the given ids to be KalturaDropFolderFileStatus::HANDLED
	 * @param array $idsArray array of drop folder file ids
	 */
	private function setAsHandled($idsArray)
	{
		$updateObj = new KalturaDropFolderFile();
		$updateObj->status = KalturaDropFolderFileStatus::HANDLED;
		
		$this->kClient->startMultiRequest();
		foreach ($idsArray as $id)
		{
			KalturaLog::debug('Updating drop folder file ['.$id.'] with status HANDLED');
			$this->kClient->dropFolderFile->update($id, $updateObj);
		}
		$this->kClient->doMultiRequest();		
	}
	
	
	private function getLocalXmlFilePath()
	{
	    $dropFolderFilePath = $this->dropFolder->path.'/'.$this->dropFolderFile->fileName;
	    
	    // local drop folder
	    if ($this->dropFolder->type == KalturaDropFolderType::LOCAL) {
	        $dropFolderFilePath = realpath($dropFolderFilePath);
	        return $dropFolderFilePath;
	    }
	    
	    // remote drop folder	    
		$tempFilePath = tempnam($this->tempDirectory, 'dropFolderFileId_'.$this->dropFolderFile->id.'_');
		
		$this->fileTransferMgr->getFile($dropFolderFilePath, $tempFilePath);

		return $tempFilePath;	    
	}	

}