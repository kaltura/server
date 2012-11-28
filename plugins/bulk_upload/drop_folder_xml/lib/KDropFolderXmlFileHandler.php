<?php
/**
 * @package plugins.dropFolder
 * @subpackage Scheduler
 */

class KDropFolderXmlFileHandler extends KDropFolderFileHandler
{
	const DROP_FOLDER_RESOURCE_NODE_NAME = 'dropFolderFileContentResource';
	const DROP_FOLDER_RESOURCE_PATH_ATTRIBUTE = 'filePath';
	const MYSQL_CODE_DUPLICATE_KEY = 23000;
	/**
	 * @var KPhysicalDropFolderUtils
	 */
	private $physicalDropFolderUtils = null;
	
	
	protected function initHandler(KalturaClient $client, KalturaDropFolder $dropFolder)
	{
		parent::initHandler($client, $dropFolder);
		$this->physicalDropFolderUtils = new KPhysicalDropFolderUtils($dropFolder);
		
	}
	
	public function handleFileAdded($fileName, $fileSize, $lastModificationTime)
	{
		KalturaLog::debug('Handling adding drop folder file with name ['.$fileName.']');
    	try
	    {
    	    $newDropFolderFile = new KalturaDropFolderFile();
    		$newDropFolderFile->dropFolderId = $this->folder->id;
    		$newDropFolderFile->fileName = $fileName;
    		$newDropFolderFile->fileSize = $fileSize;
    		$newDropFolderFile->lastModificationTime = $lastModificationTime;
			$this->dropFolderFileService->add($newDropFolderFile);
		}
		catch (Exception $e) 
		{
			if($e->getCode() == self::MYSQL_CODE_DUPLICATE_KEY)
			{
				KalturaLog::debug('Handling unique constraint error case');
				$existingFile = $this->getUnprocessedFileByName($fileName); 
				if($existingFile)
				{
					if($existingFile->status == KalturaDropFolderFileStatus::PARSED)
					{
						$this->handleFileUploading($existingFile->id, $fileSize, $lastModificationTime);
						try 
						{
							$this->dropFolderFileService->updateStatus($existingFile->id, KalturaDropFolderFileStatus::UPLOADING);
						}
						catch(Exception $e)
						{
							KalturaLog::err('Failed to update drop folder file status ['.$existingFile->id.'] - '.$e->getMessage());
						}
					}
				}
			}
			else
			{
				KalturaLog::err('Cannot add new drop folder file with name ['.$fileName.'] - '.$e->getMessage());
			}		
		}	
	}
			
	public function handleFileUploaded($dropFolderFileId, $lastModificationTime)
	{
		KalturaLog::debug('Hanlde drop folder file finished uploading with id ['.$dropFolderFileId.']');
		$dropFolderFile = $this->dropFolderFileService->get($dropFolderFileId);
		
		if($this->isXmlFile($dropFolderFile->fileName))
		{
			$contentResorces = $this->getContentResources($dropFolderFile);
			foreach ($contentResorces as $contentResource) 
			{
				$dropFolderFileContentResource = $this->addParsedContentResourceFile($contentResource, $dropFolderFile->id);
			}
			$this->setLeadDropFolderFileId($dropFolderFileId, $dropFolderFileId);
		}
		
		$dropFolderFile = parent::handleFileUploaded($dropFolderFileId, $lastModificationTime);
		
		return $dropFolderFile;
	}
		
	public function handleFilePurged($dropFolderFileId)
	{
		$dropFolderFile = parent::handleFilePurged($dropFolderFileId);
		if($this->isXmlFile($dropFolderFile->fileName))
		{
			$parsedDropFolderFiles = $this->getParsedFilesByLeadId($dropFolderFile->leadDropFolderFileId);
			foreach ($parsedDropFolderFiles as $parsedFile) 
			{
				parent::handleFilePurged($parsedFile->id);
			}
		}
	}
	
	public function handleFileReplaced($dropFolderFileId, $fileName, $fileSize, $lastModificationTime)
	{
		KalturaLog::debug('Handling drop folder file replaced id ['.$dropFolderFileId.'] file name ['.$fileName.'] fileSize ['.$fileSize.'] last modification time ['.$lastModificationTime.']');
		$purgedDropFolderFile = $this->handleFilePurged($dropFolderFileId);
		$dropFolderFile = $this->handleFileAdded($fileName, $fileSize, $lastModificationTime);	
				
		if(!$this->isXmlFile($fileName))
		{
			$this->setLeadDropFolderFileId($dropFolderFile->id, $purgedDropFolderFile->leadDropFolderFileId);
		}
		return $dropFolderFile;
	}
	
	private function addParsedContentResourceFile($fileName, $leadDropFolderFileId)
	{
		try
	    {
    	    $newDropFolderFile = new KalturaDropFolderFile();
    		$newDropFolderFile->dropFolderId = $this->folder->id;
    		$newDropFolderFile->fileName = $fileName;
    		$newDropFolderFile->fileSize = 0;
    		$newDropFolderFile->status = KalturaDropFolderFileStatus::PARSED;
    		$newDropFolderFile->leadDropFolderFileId = $leadDropFolderFileId;
			$this->dropFolderFileService->add($newDropFolderFile);
		}
		catch (Exception $e) 
		{
			if($e->getCode() == self::MYSQL_CODE_DUPLICATE_KEY)
			{
				$existingFile = $this->getUnprocessedFileByName($fileName);
				if($existingFile)
				{
					$this->setLeadDropFolderFileId($existingFile->id, $leadDropFolderFileId);
				}
			}
			else
			{
				$this->handleFileError($leadDropFolderFileId, KalturaDropFolderFileStatus::ERROR_HANDLING, KalturaDropFolderFileErrorCode::ERROR_ADD_CONTENT_RESOURCE, 
										'Failed to add drop folder content resource files', $e);
			}		
		}	
	}
	
	private function getUnprocessedFileByName($fileName)
	{
		try 
		{
			$filter = new KalturaDropFolderFileFilter();
			$filter->dropFolderIdEqual = $this->folder->id;
			$filter->fileNameEqual = $fileName;
			$filter->statusIn = KalturaDropFolderFileStatus::PARSED.','.KalturaDropFolderFileStatus::UPLOADING.','.KalturaDropFolderFileStatus::PENDING.','.KalturaDropFolderFileStatus::WAITING;
			$dropFolderFile = $this->dropFolderFileService->listAction($filter); 
			if($dropFolderFile->totalCount == 1)
			{
				return $dropFolderFile->objects[0];			
			}
		}
		catch (Exception $e)
		{
			KalturaLog::err('Failed to get drop folder file with name ['.$fileName.'] - '.$e->getMessage());
		}
		return null;
	}
	
	//TODO: need to write a better comparison
	private function isXmlFile($fileName)
	{
		$isXml = false;
		$fileNamePatterns = trim($this->folder->fileNamePatterns, ' *');

	if($fileNamePatterns)
		$isXml = strstr($fileName, $fileNamePatterns);
	else
		$isXml = true;
		
	return $isXml;
	}
	
	
	private function setLeadDropFolderFileId($dropFolderFileId, $leadDropFolderFileId)
	{
		try 
		{
			$updateDropFolderFile = new KalturaDropFolderFile();
			$updateDropFolderFile->leadDropFolderFileId = $leadDropFolderFileId;
			$dropFolderFile = $this->dropFolderFileService->update($dropFolderFileId, $updateDropFolderFile);
			
			return $dropFolderFile;
		}
		catch(Exception $e)
		{
			$this->handleFileError($dropFolderFile->id, KalturaDropFolderFileStatus::ERROR_HANDLING, KalturaDropFolderFileErrorCode::ERROR_UPDATE_FILE, 
									'Cannot update drop folder file', $e);			
			return null;
		}
	}
	
	private function getContentResources(KalturaDropFolderFile $dropFolderFile)
	{
		try 
		{
			$xmlPath = $this->physicalDropFolderUtils->getLocalFilePath($dropFolderFile->fileName, $dropFolderFile->id);
			$xmlDoc = new KDOMDocument();
			$res = $xmlDoc->load($xmlPath);
			if(!$res)
				throw new Exception('Malformed XML');
			$localResourceNodes = $xmlDoc->getElementsByTagName(self::DROP_FOLDER_RESOURCE_NODE_NAME);
			
			$contentResources = array();
			foreach ($localResourceNodes as $localResourceNode) 
			{
				$contentResources[] = $localResourceNode->getAttribute(self::DROP_FOLDER_RESOURCE_PATH_ATTRIBUTE);
			}			
			return $contentResources;
		}
		catch(Exception $e)
		{
			$this->handleFileError($dropFolderFile->id, KalturaDropFolderFileStatus::ERROR_HANDLING, KalturaDropFolderFileErrorCode::ERROR_READING_FILE, 
									'Cannot read file or file details at path ['.$this->folder->path.'/'.$dropFolderFile->fileName.']', $e);
			return null;
			
		}
	}

	private function getParsedFilesByLeadId($leadFileId)
	{
		try 
		{
			$filter = new KalturaDropFolderFileFilter();
			$filter->dropFolderIdEqual = $this->folder->id;
			$filter->leadDropFolderFileIdEqual = $leadFileId;
			$filter->statusEqual = KalturaDropFolderFileStatus::PARSED;
			$dropFolderFile = $this->dropFolderFileService->listAction($filter); 
			if($dropFolderFile)
			{
				return $dropFolderFile->objects;			
			}
		}
		catch (Exception $e)
		{
			KalturaLog::err('Failed to get drop folder files by lead id with name ['.$leadFileId.'] - '.$e->getMessage());
		}
		return null;
		
	}
}