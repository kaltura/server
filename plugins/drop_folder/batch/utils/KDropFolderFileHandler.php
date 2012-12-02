<?php

/**
 * @package plugins.dropFolder
 * @subpackage Scheduler
 */
abstract class KDropFolderFileHandler
{	
	/**
	 * @var KalturaClient
	 */
	protected $kClient;
	
	/**
	* @var KalturaDropFolderFileService
	*/
	protected $dropFolderFileService = null;
	
	/**
	* @var KalturaDropFolder
	*/
	protected $folder = null;
	
	/**
	 * @var KalturaDropFolderFileHandlerConfig
	 */
	protected $config;
	
	
	/**
	 * Return a new instance of a class extending KalturaDropFolderFileHandler, according to a given $type
	 * @param KalturaDropFolderFileHandlerType $type
	 * @return KalturaDropFolderFileHandler
	 */
	public static function getHandler($type, KalturaClient $client, KalturaDropFolder $dropFolder)
	{
		$handler = null;
		switch ($type)
		{
			case KalturaDropFolderFileHandlerType::CONTENT:
				$handler = new KDropFolderContentFileHandler();		
						
			default:
				$handler = KalturaPluginManager::loadObject('KDropFolderFileHandler', $type);
				if(!$handler)
					throw new Exception("Unknown type [$type] of KDropFolderFileHandler");
		}
		$handler->initHandler($client, $dropFolder);
		return $handler;
	}
	
	protected function initHandler(KalturaClient $client, KalturaDropFolder $dropFolder)
	{
		$this->kClient = $client;
		$dropFolderPlugin = KalturaDropFolderClientPlugin::get($this->kClient);
		$this->dropFolderFileService = $dropFolderPlugin->dropFolderFile;
		$this->folder = $dropFolder;
		$this->config = $dropFolder->fileHandlerConfig;
	}
	
	public function handleFileError($dropFolderFileId, $errorStatus, $errorCode, $errorMessage, Exception $e = null)
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
	
	public function handleFileUploading($dropFolderFileId, $fileSize, $lastModificationTime, $uploadStartDetectedAt = null)
	{
		KalturaLog::debug('Handling drop folder file uploading id ['.$dropFolderFileId.'] fileSize ['.$fileSize.'] last modification time ['.$lastModificationTime.']');
		try 
		{
			$updateDropFolderFile = new KalturaDropFolderFile();
			$updateDropFolderFile->fileSize = $fileSize;
			$updateDropFolderFile->lastModificationTime = $lastModificationTime;
			if($uploadStartDetectedAt)
			{
				$updateDropFolderFile->uploadStartDetectedAt = $uploadStartDetectedAt;
			}
			return $this->dropFolderFileService->update($dropFolderFileId, $updateDropFolderFile);
		}
		catch (Exception $e) 
		{
			$this->handleFileError($dropFolderFileId, KalturaDropFolderFileStatus::ERROR_HANDLING, KalturaDropFolderFileErrorCode::ERROR_UPDATE_FILE, 
									'Cannot update drop folder file', $e);
			return null;
		}						
	}
	
	public function handleFilePurged($dropFolderFileId)
	{
		KalturaLog::debug('Handling drop folder file purged id ['.$dropFolderFileId.']');
		try 
		{
			return $this->dropFolderFileService->updateStatus($dropFolderFileId, KalturaDropFolderFileStatus::PURGED);
		}
		catch(Exception $e)
		{
			$this->handleFileError($dropFolderFileId, KalturaDropFolderFileStatus::ERROR_HANDLING, KalturaDropFolderFileErrorCode::ERROR_UPDATE_FILE, 
									'Cannot update drop folder file', $e);
			
			return null;
		}		
	}
	
	public function handleFileUploaded($dropFolderFileId, $lastModificationTime)
	{
		KalturaLog::debug('Handling drop folder file uploaded id ['.$dropFolderFileId.'] last modification time ['.$lastModificationTime.']');
		try 
		{
			$updateDropFolderFile = new KalturaDropFolderFile();
			$updateDropFolderFile->lastModificationTime = $lastModificationTime;
			$updateDropFolderFile->uploadEndDetectedAt = time();
			$this->dropFolderFileService->update($dropFolderFileId, $updateDropFolderFile);
			return $this->dropFolderFileService->updateStatus($dropFolderFileId, KalturaDropFolderFileStatus::PENDING);			
		}
		catch(KalturaException $e)
		{
			$this->handleFileError($dropFolderFileId, KalturaDropFolderFileStatus::ERROR_HANDLING, KalturaDropFolderFileErrorCode::ERROR_UPDATE_FILE, 
									'Cannot update drop folder file', $e);
			return null;
		}
	}
	
	public abstract function handleFileAdded($fileName, $fileSize, $lastModificationTime);

	public function handleFileReplaced($dropFolderFileId, $fileName, $fileSize, $lastModificationTime)
	{
		KalturaLog::debug('Handling drop folder file replaced id ['.$dropFolderFileId.'] name ['.$fileName.']');
		$this->handleFilePurged($dropFolderFileId);
		return $this->handleFileAdded($fileName, $fileSize, $lastModificationTime);		
	}

}