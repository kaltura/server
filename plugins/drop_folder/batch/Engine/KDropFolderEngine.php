<?php
/**
 * 
 */
abstract class KDropFolderEngine
{
	protected $dropFolder;
	
	protected $dropFolderPlugin;
	
	protected $dropFolderFileService;
	
	public function __construct (KalturaDropFolder $dropFolder)
	{
		$this->dropFolder = $dropFolder;
		$this->dropFolderPlugin = KalturaDropFolderClientPlugin::get(KBatchBase::$kClient);
		$this->dropFolderFileService = $this->dropFolderPlugin->dropFolderFile;
	}
	
	public static function getInstance (KalturaDropFolder $dropFolder)
	{
		switch ($dropFolder->type) {
			case KalturaDropFolderType::FTP:
			case KalturaDropFolderType::SFTP:
			case KalturaDropFolderType::LOCAL:
				return new KDropFolderFileTransferEngine ($dropFolder);
				break;
			
			default:
				return KalturaPluginManager::loadObject('KDropFolderFileTransferEngine', $dropFolder->type, $dropFolder);
				break;
		}
	}
	
	abstract public function watchFolder ();
	
	/**
	 * Load all the files from the database that their status is not PURGED, PARSED or DETECTED
	 * @param KalturaDropFolder $folder
	 */
	protected function loadDropFolderFiles()
	{
		$dropFolderFilesMap = array();
		$dropFolderFiles =null;
		
		$dropFolderFileFilter = new KalturaDropFolderFileFilter();
		$dropFolderFileFilter->dropFolderIdEqual = $this->dropFolder->id;
		$dropFolderFileFilter->statusNotIn(KalturaDropFolderFileStatus::PARSED,KalturaDropFolderFileStatus::DETECTED);
		
		$pager = new KalturaFilterPager();
		$pager->pageSize = 500;
		if(KBatchBase::$taskConfig->params->pageSize)
			$pager->pageSize = KBatchBase::$taskConfig->params->pageSize;	

		do
		{
			$pager->pageIndex++;
			KalturaLog::debug('getting page ['.$pager->pageIndex. '] from Drop Folder File ');
			$dropFolderFiles = $this->dropFolderPlugin->dropFolderFile->listAction($dropFolderFileFilter, $pager);
			$dropFolderFiles = $dropFolderFiles->objects;
		}while (count($dropFolderFiles) >= $pager->pageSize);
			
		return $dropFolderFilesMap;
	}

	/**
 	 * Update drop folder entity with error
	 * @param int $dropFolderFileId
	 * @param int $errorStatus
	 * @param int $errorCode
	 * @param string $errorMessage
	 * @param Exception $e
	 */
	protected function handleFileError($dropFolderFileId, $errorStatus, $errorCode, $errorMessage, Exception $e = null)
	{
		try 
		{
			if($e)
				KalturaLog::err('Error for drop folder file with id ['.$dropFolderFileId.'] - '.$e->getMessage());
			else
				KalturaLog::err('Error for drop folder file with id ['.$dropFolderFileId.'] - '.$errorMessage);
			
			$updateDropFolderFile = new KalturaDropFolderFile();
			$updateDropFolderFile->errorCode = $errorCode;
			$updateDropFolderFile->errorDescription = $errorMessage;
			$this->dropFolderFileService->update($dropFolderFileId, $updateDropFolderFile);
			return $this->dropFolderFileService->updateStatus($dropFolderFileId, $errorStatus);				
		}
		catch (KalturaException $e) 
		{
			KalturaLog::err('Cannot set error details for drop folder file id ['.$dropFolderFileId.'] - '.$e->getMessage());
			return null;
		}
	}
	
	/**
	 * Mark file status as PURGED
	 * @param int $dropFolderFileId
	 */
	protected function handleFilePurged($dropFolderFileId)
	{
		KalturaLog::debug('Handling drop folder file purged id ['.$dropFolderFileId.']');
		try 
		{
			return $this->dropFolderFileService->updateStatus($dropFolderFileId, KalturaDropFolderFileStatus::PURGED);
		}
		catch(Exception $e)
		{
			$this->handleFileError($dropFolderFileId, KalturaDropFolderFileStatus::ERROR_HANDLING, KalturaDropFolderFileErrorCode::ERROR_UPDATE_FILE, 
									DropFolderPlugin::ERROR_UPDATE_FILE_MESSAGE, $e);
			
			return null;
		}		
	}

}
