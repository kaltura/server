<?php
/**
 * 
 */
abstract class KDropFolderEngine
{
	protected $dropFolder;
	
	protected $dropFolderPlugin;
	
	protected $dropFolderFileService;
	
	public function __construct ()
	{
		$this->dropFolderPlugin = KalturaDropFolderClientPlugin::get(KBatchBase::$kClient);
		$this->dropFolderFileService = $this->dropFolderPlugin->dropFolderFile;
	}
	
	public static function getInstance ($dropFolderType)
	{
		switch ($dropFolderType) {
			case KalturaDropFolderType::FTP:
			case KalturaDropFolderType::SFTP:
			case KalturaDropFolderType::LOCAL:
				return new KDropFolderFileTransferEngine ();
				break;
			
			default:
				return KalturaPluginManager::loadObject('KDropFolderEngine', $dropFolderType);
				break;
		}
	}
	
	abstract public function watchFolder (KalturaDropFolder $dropFolder);
	
	abstract public function processFolder (KalturaBatchJob $job, KalturaDropFolderContentProcessorJobData $data);
	
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
		$dropFolderFileFilter->statusNotIn = KalturaDropFolderFileStatus::PARSED.','.KalturaDropFolderFileStatus::DETECTED;
		
		$pager = new KalturaFilterPager();
		$pager->pageSize = 500;
		if(KBatchBase::$taskConfig->params->pageSize)
			$pager->pageSize = KBatchBase::$taskConfig->params->pageSize;	

		do
		{
			$pager->pageIndex++;
			KalturaLog::debug('getting page ['.$pager->pageIndex. '] from Drop Folder File ');
			$dropFolderFiles = $this->dropFolderFileService->listAction($dropFolderFileFilter, $pager);
			KalturaLog::debug('response: '. print_r($dropFolderFiles, true));
			$dropFolderFiles = $dropFolderFiles->objects;
			foreach ($dropFolderFiles as $dropFolderFile) 
			{
				$dropFolderFilesMap[$dropFolderFile->fileName] = $dropFolderFile;
			}
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
