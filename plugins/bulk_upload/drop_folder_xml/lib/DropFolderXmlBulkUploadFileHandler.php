<?php
/**
 * @package Scheduler
 * @subpackage Drop-Folder
 */
class DropFolderXmlBulkUploadFileHandler extends DropFolderFileHandler
{	
	const LOCAL_RESOURCE_NODE_NAME = 'localFileContentResource';
	const LOCAL_RESOURCE_PATH_ATTRIBUTE = 'filePath';
	const LOCAL_RESOURCE_FILE_SIZE_PARAM = 'fileSize';
	const LOCAL_RESOURCE_FILE_CHECKSUM_PARAM = 'fileChecksum';
	
	
	public function getType() 
	{
		return KalturaDropFolderFileHandlerType::XML;
	}
	
	public function handle()
	{
		// check prerequisites
		$checkConfig = $this->checkConfig();
		if (!$checkConfig) {
			return false;
		}	
		
		$xmlPath = realpath($this->dropFolder->path.'/'.$this->dropFolderFile->fileName);
		if (!$xmlPath) {
			//TODO: error
			return false;
		}
		
		$xmlDoc = new DOMDocument();
		$xmlDoc->loadXML($xmlPath);

		if (!$xmlDoc) {
			//TODO: error
			return false;
		}
		
		$localResources = $xmlDoc->getElementsByTagName(self::LOCAL_RESOURCE_NODE_NAME);
		
		if (!$localResources) {
			//TODO: error happened
			return error;
		}
		
		foreach ($localResources as $local)
		{
			$dropFolderFileId = $this->checkFileExists($local);
			if (is_null($dropFolderFileId)) {
				//TODO: change status to waiting
			}
			$localVerified = $this->verifyLocalResource($local);
			if ($localVerified) {
				//TODO: error
			}
			$this->replaceResource($local, $dropFolderFileId, $xmlDoc);
		}
		

		//TODO: create a temporary XML file from the modified $xmlDoc
		//TODO: add bulk upload of type KalturaBulkUploadType::DROP_FOLDER_XML
		//TODO: delete the temporary file
	
	}
	
	private function checkFileExists(DOMElement $localResource)
	{
		$filePath = $localResource->getAttribute(self::LOCAL_RESOURCE_PATH_ATTRIBUTE);
		//TODO: verify filePath exists in drop folder
		//TODO: return dropFolderFile id
	}
	
	private function verifyLocalResource(DOMElement $localResource)
	{
		$fileSize = $localResource->getElementsByTagName(self::LOCAL_RESOURCE_FILE_SIZE_PARAM);
		$fileSize = ($fileSize->length > 0) ? $fileSize->item(0)->nodeValue : null;
		//TODO: verify file size
		
		$fileChecksum = $localResource->getElementsByTagName(self::LOCAL_RESOURCE_FILE_CHECKSUM_PARAM);
		$fileChecksum = ($fileChecksum->length > 0) ? $fileChecksum->item(0)->nodeValue : null;
		//TODO: verify checksum - need to find out what type of checksum to verify
		//TODO: set error description and code
	}
	
	private function replaceResource(DOMElement $localResource, $dropFolderFileId, DOMDocument $xmlDoc)
	{
		// create a new XML drop folder resource with the given id
		$newDropFolderResource = $xmlDoc->createElement('dropFolderFileContentResource');
		$newDropFolderResource->appendChild($xmlDoc->createElement('dropFolderFileId', $dropFolderFileId));
		
		// replace the local resource with the new drop folder file resource
		$parent = $localResource->parentNode;
		$parent-> replaceChild($newDropFolderResource, $localResource);
	}
	
	private function addBulkUploadJob($xmlData)
	{
		//TODO: implement
	}
	
}