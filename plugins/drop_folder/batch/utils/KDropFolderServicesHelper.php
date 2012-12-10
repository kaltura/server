<?php

/**
 * @package plugins.dropFolder
 * @subpackage Scheduler
 */
class KDropFolderServicesHelper
{	
	/**
	 * @var KalturaClient
	 */
	protected $kClient;
	
	/**
	* @var KalturaDropFolderFileService
	*/
	protected $dropFolderFileService = null;
	
	
	public function __construct(KalturaClient $client)
	{
		$this->kClient = $client;
		$dropFolderPlugin = KalturaDropFolderClientPlugin::get($this->kClient);
		$this->dropFolderFileService = $dropFolderPlugin->dropFolderFile;
	}
	
	/**
	 * Update drop folder entity with error
	 * Enter description here ...
	 * @param int $dropFolderFileId
	 * @param int $errorStatus
	 * @param int $errorCode
	 * @param string $errorMessage
	 * @param Exception $e
	 */
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
	
	/**
	 * Update uploading details
	 * @param int $dropFolderFileId
	 * @param int $fileSize
	 * @param int $lastModificationTime
	 * @param int $uploadStartDetectedAt
	 */
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
									DropFolderPlugin::ERROR_UPDATE_FILE_MESSAGE, $e);
			return null;
		}						
	}
	
	/**
	 * Mark file status as PURGED
	 * @param int $dropFolderFileId
	 */
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
									DropFolderPlugin::ERROR_UPDATE_FILE_MESSAGE, $e);
			
			return null;
		}		
	}
	
	/**
	 * Update upload details and set file status to PENDING
	 * @param int $dropFolderFileId
	 * @param int $lastModificationTime
	 */
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
									DropFolderPlugin::ERROR_UPDATE_FILE_MESSAGE, $e);
			return null;
		}
	}
	
	/**
	 * Add new drop folder file
	 * @param string $fileName
	 * @param int $dropFolderId
	 * @param int $fileSize
	 * @param int $lastModificationTime
	 */
	public function handleFileAdded($fileName, $dropFolderId, $fileSize, $lastModificationTime)
	{
		KalturaLog::debug('Add drop folder file ['.$fileName.'] last modification time ['.$lastModificationTime.'] file size '.$fileSize.']');
		try 
		{
			$newDropFolderFile = new KalturaDropFolderFile();
	    	$newDropFolderFile->dropFolderId = $dropFolderId;
	    	$newDropFolderFile->fileName = $fileName;
	    	$newDropFolderFile->fileSize = $fileSize;
	    	$newDropFolderFile->lastModificationTime = $lastModificationTime; 
	    	$newDropFolderFile->uploadStartDetectedAt = time();
			$dropFolderFile = $this->dropFolderFileService->add($newDropFolderFile);
			return $dropFolderFile;
		}
		catch(Exception $e)
		{
			KalturaLog::err('Cannot add new drop folder file with name ['.$fileName.'] - '.$e->getMessage());
			return null;
		}
	}
}