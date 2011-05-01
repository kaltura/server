<?php
/**
 * @package Scheduler
 * @subpackage Drop-Folder
 */
class DropFolderXmlBulkUploadFileHandler extends DropFolderFileHandler
{	
	public function getType() 
	{
		return KalturaDropFolderFileHandlerType::XML;
	}
	
	public function handle()
	{
//		TODO
//		1. Validate that all files arrived and have the correct size
//		2. Replace all localFileContentResource with dropFolderFileContentResource
//		3. add bulk upload of type KalturaBulkUploadType::DROP_FOLDER_XML
	}
}