<?php

/**
 * @package dropFolder 
 * @subpackage Scheduler.fileHandlers
 */
class DropFolderXmlBulkUploadFileHandler extends DropFolderFileHandler
{	
	const LOCAL_RESOURCE_NODE_NAME = 'localFileContentResource';
	const LOCAL_RESOURCE_PATH_ATTRIBUTE = 'filePath';
	const LOCAL_RESOURCE_FILE_SIZE_PARAM = 'fileSize';
	const LOCAL_RESOURCE_FILE_CHECKSUM_PARAM = 'fileChecksum';
	
	const DROP_FOLDER_RESOURCE_NODE_NAME = 'dropFolderFileContentResource';
	const DROP_FOLDER_RESOURCE_FILE_ID_ATTRIBUTE = 'dropFolderFileId';
	
	private $tempDirectory = null;
	
	public function getType() 
	{
		return KalturaDropFolderFileHandlerType::XML;
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
		
		$xmlPath = realpath($this->dropFolder->path.'/'.$this->dropFolderFile->fileName);
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
		
		$localResources = $xmlDoc->getElementsByTagName(self::LOCAL_RESOURCE_NODE_NAME);
		
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
			// replacement/modification of $local must not happen inside this foreach loop
			
			$dropFolderFileId = $this->checkFileExists($local);
			if (is_null($dropFolderFileId)) {
				KalturaLog::debug('Some required files do not exist in the drop folder - changing status to WAITING');
				$this->dropFolderFile->status = KalturaDropFolderFileStatus::WAITING;
				KalturaLog::debug('Changing status to WAITING');
				$this->updateDropFolderFile();
				return true;
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
		
		$ingestionProfile = $this->getIngestionProfile();
		
		// add bulk upload of type KalturaBulkUploadType::DROP_FOLDER_XML
		try
		{
			$this->impersonate($this->dropFolderFile->partnerId);
			$this->kClient->bulkUpload->add($ingestionProfile->id, $tempFileRealPath, KalturaBulkUploadType::DROP_FOLDER_XML);
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
	
	
	private function checkFileExists(DOMElement $localResource)
	{
		$filePath = $localResource->getAttribute(self::LOCAL_RESOURCE_PATH_ATTRIBUTE);
		
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
		clearstatcache();
		
		$filePath = $localResource->getAttribute(self::LOCAL_RESOURCE_PATH_ATTRIBUTE);
		$localPath = realpath($this->dropFolder->path.'/'.$filePath);
		if (!$localPath) {
			$this->dropFolderFile->errorCode = KalturaDropFolderFileErrorCode::ERROR_READING_FILE;
			$this->dropFolderFile->errorDescription = "Cannot find file at [$localPath]";
			return false;	
		}
		
		$fileSize = $localResource->getElementsByTagName(self::LOCAL_RESOURCE_FILE_SIZE_PARAM);
		$fileSize = ($fileSize->length > 0) ? $fileSize->item(0)->nodeValue : null;
		if (!is_null($fileSize))
		{
			$localSize = filesize($localPath);
			if ($fileSize != $localSize) {
				$this->dropFolderFile->errorCode = KalturaDropFolderFileErrorCode::LOCAL_FILE_WRONG_SIZE;
				$this->dropFolderFile->errorDescription = "Wrong filesize [$localSize] for file [$localPath]";
				return false;
			}
			KalturaLog::debug("Filesize [$fileSize] verified for local resource [$filePath]");
		}
		
		$fileChecksumTags = $localResource->getElementsByTagName(self::LOCAL_RESOURCE_FILE_CHECKSUM_PARAM);
		$fileChecksum = ($fileChecksumTags->length > 0) ? (string)$fileChecksumTags->item(0)->nodeValue : null;
		
		if (!is_null($fileChecksum))
		{
			$checksumType = $fileChecksumTags->item(0)->getAttribute('type');
			
			if ($checksumType == 'sha1') {
				$localChecksum = sha1_file($localPath);
			}
			else {
				$localChecksum = md5_file($localPath);
			}
			if ($fileChecksum != $localChecksum) {
				$this->dropFolderFile->errorCode = KalturaDropFolderFileErrorCode::LOCAL_FILE_WRONG_CHECKSUM;
				$this->dropFolderFile->errorDescription = "Wrong checksum [$localChecksum] for file [$localPath]";
				return false;
			}
			KalturaLog::debug("Checksum [$fileChecksum] verified for local resource [$filePath]");
		}

		return true;
	}
	
	private function replaceResource(DOMElement $localResource, $dropFolderFileId, DOMDocument $xmlDoc)
	{
		// create a new XML drop folder resource with the given id
		$newDropFolderResource = $xmlDoc->createElement(self::DROP_FOLDER_RESOURCE_NODE_NAME);
		$newDropFolderResource->setAttribute(self::DROP_FOLDER_RESOURCE_FILE_ID_ATTRIBUTE, $dropFolderFileId);
		
		// replace the local resource with the new drop folder file resource
		$parent = $localResource->parentNode;
		$parent-> replaceChild($newDropFolderResource, $localResource);
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
	
}