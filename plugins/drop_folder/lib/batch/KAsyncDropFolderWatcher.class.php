<?php
require_once("bootstrap.php");

//TODO: this class is not finished!

/**
 * Watches drop folder files and executes file handlers as required 
 *
 * @package plugins.dropFolder
 * @subpackage Scheduler.dropFolder
 */
class KAsyncDropFolderWatcher extends KBatchBase
{

	public static function getType()
	{
		return KalturaBatchJobType::DROP_FOLDER_WATCHER;
	}
	
	protected function init()
	{
		//TODO: implement somehow ?
	}
	
	protected function updateExclusiveJob($jobId, KalturaBatchJob $job)
	{ /* non-relevant abstract function */ }
	
	protected function freeExclusiveJob(KalturaBatchJob $job)
	{ /* non-relevant abstract function */ }
	
	
	
	public function run()
	{
		KalturaLog::info("Drop folder watcher batch is running");
		
		if($this->taskConfig->isInitOnly())
			return $this->init();
		
		// get list of drop folders according to configuration
		$filter = new KalturaDropFolderFilter();
		//TODO: use filter to get only folders relevant to current worker's config
		$dropFolders = $this->kClient->dropFolder->listAction($filter);
		
		foreach ($dropFolders as $folder)
		{
			$this->watchFolder($folder);
		}
			//TODO: delete the file from the folder on status = DELETED
			

	}
		
	
	private function watchFolder(KalturaDropFolder $folder)
	{
		
		// get list of DropFolderFile objects from the current $folder
		$dropFolderFiles = null;
		$deletedDropFolderFiles = null;
		try {
			$dropFolderFiles = $this->getDropFolderFileObjects($folder);
			$deletedDropFolderFiles = $this->getDropFolderFileObjects($folder); // deleted objects
		}
		catch (Exception $e) {
			KalturaLog::err('Cannot get drop folder file list from the server for drop folder id ['.$folder->id.'] - '.$e->getMessage());
			return; // skipping to next folder
		}
		
		$dropFolderFileMapByName = array();
		$deletedDropFolderFileMapByName = array();
		foreach ($dropFolderFiles as $dropFolderFile)
		{
			$dropFolderFileMapByName[$dropFolderFile->fileName] = $dropFolderFile;
		}
		foreach ($deletedDropFolderFiles as $dropFolderFile)
		{
			$deletedDropFolderFileMapByName[$dropFolderFile->fileName] = $dropFolderFile;
		}
		
		
		
		// get a list of physical files from the folder's path
		try {
			$physicalFiles = $this->getPhysicalFileList($folder);
		}
		catch (Exception $e) {
			KalturaLog::err('Cannot get physical file list for drop folder id ['.$folder->id.'] - '.$e->getMessage());
			return; // skipping to next folder
		}
		
		
		// sync between physical file list and drop folder file objects
		foreach ($physicalFiles as $physicalFileName)
		{
			//TODO: translate file name to path+name on the shared location
			$sharedPhysicalFilePath = $physicalFileName;
			
			// skip directories
			if (is_dir($sharedPhysicalFilePath)) {
				KalturaLog::log("Path [$physicalFileName] is a directory - skipped");
				continue;
			}

			// purge deleted files if needed
			if (array_key_exists($physicalFileName, $deletedDropFolderFileMapByName))
			{
				$this->purgeFileIfNeeded($deletedDropFolderFileMapByName['$physicalFileName'], $sharedPhysicalFilePath);
				continue;
			}
			
			// check if file is already in the list of drop folder files
			if (!array_key_exists($physicalFileName, $dropFolderFileMapByName))
			{
				// new physical file found in folder - add new drop folder file object with status UPLOADING
				$this->addNewDropFolderFile($folder->id, $physicalFileName, filesize($sharedPhysicalFilePath));	
			}
			else
			{
				// update existing drop folder file object according to current physical file size
				$currentDropFolderFile = $dropFolderFileMapByName['$physicalFileName'];
				if ($currentDropFolderFile->status == KalturaDropFolderFileStatus::UPLOADING)
				{
					$this->updateDropFolderFile($currentDropFolderFile, $sharedPhysicalFilePath);
				}						
			}
		}
		
	}
	
	private function getDropFolderFileObjects(KalturaDropFolder $folder)
	{
		$dropFolderFileFilter = new KalturaDropFolderFileFilter();
		$dropFolderFileFilter->dropFolderIdEqual = $folder->id;
		$dropFolderFiles = $this->kClient->dropFolderFile->listAction($dropFolderFileFilter);
		return $dropFolderFiles;
	}
	
	private function getDeletedDropFolderFileObjects(KalturaDropFolder $folder)
	{
		$dropFolderFileFilter = new KalturaDropFolderFileFilter();
		$dropFolderFileFilter->dropFolderIdEqual = $folder->id;
		$dropFolderFileFilter->statusIn = KalturaDropFolderFileStatus::DELETED.','.KalturaDropFolderFileStatus::PURGED;
		$dropFolderFiles = $this->kClient->dropFolderFile->listAction($dropFolderFileFilter);
		return $dropFolderFiles;
	}
	
	private function getPhysicalFileList(KalturaDropFolder $folder)
	{
		//TODO: how to get the physical file list ?  how to access the shared content drop folder location ?
	}
	
	
	private function addNewDropFolderFile($folderId, $fileName, $fileSize)
	{
		$newDropFolderFile = new KalturaDropFolderFile();
		$newDropFolderFile->dropFolderId = $folderId;
		$newDropFolderFile->fileName = $fileName;
		$newDropFolderFile->fileSize = $fileSize;
		$newDropFolderFile->status = KalturaDropFolderFileStatus::UPLOADING;
		
		try {	
			$this->kClient->dropFolderFile->add($newDropFolderFile);
		}
		catch (Exception $e) {
			KalturaLog::err("Cannot add new drop folder file [$fileName] - ".$e->getMessage());
		}	
	}
	
	private function updateDropFolderFile(KalturaDropFolderFile $dropFolderFile, $sharedPhysicalFilePath)
	{
		$physicalFileSize = filesize($sharedPhysicalFilePath);
		
		if ($physicalFileSize < $dropFolderFile->fileSize)
		{
			//TODO: error!
		}
		else if ($physicalFileSize > $dropFolderFile->fileSize)
		{
			try {
				$updateDropFolderFile = new KalturaDropFolderFile();
				$updateDropFolderFile->fileSize = $physicalFileSize;
				$this->kClient->dropFolderFile->update($dropFolderFile->id, $updateDropFolderFile);				
			}
			catch (Exception $e) {
				KalturaLog::err('Cannot update file size for drop folder file id ['.$dropFolderFile->id.'] - '.$e->getMessage());
			}
		}
		else // ($physicalFileSize == $dropFolderFile->fileSize)
		{
			//TODO: finish	
			
			// check if fileSizeCheckInterval time has passed
				// YES -> update the file to status PENDING (will raise an event)
				// NO -> continue to next file
		}
	}
	
	
	//TODO: finish function
	private function purgeFileIfNeeded(KalturaDropFolderFile $dropFolderFile, $sharedPhysicalFilePath)
	{
		
		//TODO: if more thatn dropFolder->autoFileDeleteDays days had passed since the last file update
			//TODO: physicaly delete the file
			//TODO: change status to PURGED
			
		//TODO: else just quit
		

		/*
		// change status to PURGED
		try {
			$updateDropFolderFile = new KalturaDropFolderFile();
			$updateDropFolderFile->status = KalturaDropFolderFileStatus::PURGED;
			$this->kClient->dropFolderFile->update($dropFolderFileId, $updateDropFolderFile);
		}
		catch (Exception $e) {
			KalturaLog::err("Cannot update status for drop folder file id [$dropFolderFileId] - ".$e->getMessage());
		}
		*/
	}
	
	
	
}
