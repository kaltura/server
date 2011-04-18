<?php
require_once("bootstrap.php");

//TODO: update errorDescription on KalturaDropFolderFile for each error

/**
 * Watches drop folder files and executes file handlers as required 
 *
 * @package plugins.dropFolder
 * @subpackage Scheduler
 */
class KAsyncDropFolderWatcher extends KBatchBase
{

	public static function getType()
	{
		return KalturaBatchJobType::DROP_FOLDER_WATCHER;
	}
	
	protected function init()
	{ /* non-relevant abstract function */ }
	
	protected function updateExclusiveJob($jobId, KalturaBatchJob $job)
	{ /* non-relevant abstract function */ }
	
	protected function freeExclusiveJob(KalturaBatchJob $job)
	{ /* non-relevant abstract function */ }
	
	
	
	public function run()
	{
		KalturaLog::info("Drop folder watcher batch is running");
		
		if($this->taskConfig->isInitOnly())
			return $this->init();
		
		// get drop folder tags to work on from configuration
		$folderTags = $this->taskConfig->params->tags;
		$currentDc  = $this->taskConfig->params->dc;
		
		if (empty($folderTags)) {
			KalturaLog::err('Tags configuration is empty - cannot continue');
			return;
		}
		
		// get list of drop folders according to configuration
		$filter = new KalturaDropFolderFilter();
		
		if ($folderTags != '*') {
			$filter->tagsMultiLikeOr = $folderTags;
		}
			
		//TODO: how to know the current dc ?
		//$filter->dcEqual = kDataCenterMgr::getCurrentDcId();
		$filter->statusEqual = KalturaDropFolderStatus::ENABLED;
		
		try {
			$dropFolders = $this->kClient->dropFolder->listAction($filter);
		}
		catch (Exception $e) {
			//TODO: add error
			return;
		}
		
		$dropFolders = $dropFolders->objects; // TODO: Add error
		
		foreach ($dropFolders as $folder)
		{
			$this->watchFolder($folder);
		}
	}
		
	/**
	 * Main logic function.
	 * Sync between the list of physical files and drop folder file object for the given drop folder ($folder).
	 * Add new files, update sizes and status and delete physical files when required.
	 * @param KalturaDropFolder $folder
	 */
	private function watchFolder(KalturaDropFolder $folder)
	{
		
		// get list of DropFolderFile objects from the current $folder
		$dropFolderFiles = null;
		$deletedDropFolderFiles = null;
		try {
			$dropFolderFiles = $this->getDropFolderFileObjects($folder->id);
			$deletedDropFolderFiles = $this->getDropFolderFileObjects($folder->id, KalturaDropFolderFileStatus::DELETED);
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
		$physicalFiles = null;
		try {
			$physicalFiles = self::getPhysicalFileList($folder);
		}
		catch (Exception $e) {
			$physicalFiles = null;
		}
		if (!$physicalFiles) {
			KalturaLog::err('Cannot get physical file list for drop folder id ['.$folder->id.'] with path ['.$folder->path.']');
			return; // skipping to next folder
		}
		
		
		// sync between physical file list and drop folder file objects
		foreach ($physicalFiles as $physicalFileName)
		{
			// translate file name to path+name on the shared location
			$sharedPhysicalFilePath = self::getRealPath($folder->path, $physicalFileName);
			
			// skip non-accessible files
			if (!$sharedPhysicalFilePath || !file_exists($sharedPhysicalFilePath))
			{
				KalturaLog::err("Cannot access physical file in path [$sharedPhysicalFilePath]");
				continue;
			}
			
			// skip directories
			if (is_dir($sharedPhysicalFilePath)) {
				KalturaLog::log("Path [$physicalFileName] is a directory - skipped");
				continue;
			}

			// purge file marked as deleted
			if (array_key_exists($physicalFileName, $deletedDropFolderFileMapByName))
			{
				$this->purgeFile($deletedDropFolderFileMapByName[$physicalFileName], $sharedPhysicalFilePath);
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
				$currentDropFolderFile = $dropFolderFileMapByName[$physicalFileName];
				if ($currentDropFolderFile->status == KalturaDropFolderFileStatus::UPLOADING)
				{
					$newStatus = $this->updateDropFolderFile($folder, $currentDropFolderFile, $sharedPhysicalFilePath);
					if ($newStatus == KalturaDropFolderFileStatus::PENDING) {
						$this->handlePendingFile($folder, $currentDropFolderFile);
					}
				}
				else if	($currentDropFolderFile->status == KalturaDropFolderFileStatus::HANDLED)
				{
					$this->purgeHandledFileIfNeeded($folder, $currentDropFolderFile, $sharedPhysicalFilePath);
				}
			}
		}
		
	}
	
	private static function getRealPath($dropFolderPath, $fileName)
	{
		//TODO: some other logic might be required here
		$realPath = realpath($dropFolderPath.'/'.$fileName);
		return $realPath;
	}
	
	
	/**
	 * @param int $dropFolderId
	 * @param KalturaDropFolderFileStatus 
	 * @return array of KalturaDropFolderFile objects that belong to the given folder id and status
	 */
	private function getDropFolderFileObjects($dropFolderId, $status = null)
	{
		$dropFolderFileFilter = new KalturaDropFolderFileFilter();
		$dropFolderFileFilter->dropFolderIdEqual = $dropFolderId;
		if (!is_null($status)) {
			$dropFolderFileFilter->statusEqual = $status;
		}
		$dropFolderFiles = $this->kClient->dropFolderFile->listAction($dropFolderFileFilter);
		$dropFolderFiles = $dropFolderFiles->objects; //TODO: add error check
		return $dropFolderFiles;
	}
		
	/**
	 * @param KalturaDropFolder $folder
	 * @return array of file names in the given $folder's path
	 */
	private static function getPhysicalFileList(KalturaDropFolder $folder)
	{
		$fileList = @scandir($folder->path, 0);
		return $fileList;
	}
	
	/**
	 * @param string $filePath
	 * @return int file size of the given $filePath
	 */
	private static function getFileSize($filePath)
	{
		clearstatcache(true, $filePath);
		$fileSize = @filesize($filePath);
		return $fileSize;
	}
	
	/**
	 * Add a new drop folder file in status KalturaDropFolderFileStatus::UPLOADING
	 * @param int $folderId
	 * @param string $fileName
	 * @param size $fileSize
	 */
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
	
	/**
	 * Update an existing drop folder file object according to the related physical file
	 * If physical file size keeps changing -> update the file size for the drop folder file object
	 * If physical file size stopped changing -> change drop folder file object status to KalturaDropFolderFileStatus::PENDING if enough time has passed
	 * @param KalturaDropFolder $dropFolder
	 * @param KalturaDropFolderFile $dropFolderFile
	 * @param string $sharedPhysicalFilePath
	 */
	private function updateDropFolderFile(KalturaDropFolder $dropFolder, KalturaDropFolderFile $dropFolderFile, $sharedPhysicalFilePath)
	{
		$physicalFileSize = self::getFileSize($sharedPhysicalFilePath);
		
		if (!$physicalFileSize) {
			KalturaLog::err("Cannot get file size for path [$sharedPhysicalFilePath]");
			return; // error - can't get file size
		}
		
		if ($physicalFileSize < $dropFolderFile->fileSize)
		{
			KalturaLog::err('Physical file size ['.$physicalFileSize.'] for ['.$sharedPhysicalFilePath.'] is smaller than the file size ['.$dropFolderFile->fileSize.'] of the drop folder file id ['.$dropFolderFile->id.'] - something went wrong!');
			return; // error - file size became smaller
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
				return; // error - can't update drop folder file object's file size
			}
		}
		else // file sizes are equal
		{
			// check if fileSizeCheckInterval time has passed since the last file size update	
			if (time() > $dropFolder->fileSizeCheckInterval + $dropFolderFile->fileSizeLastSetAt)
			{
				// update the file to status PENDING (will raise an event)
				try {
					$updateDropFolderFile = new KalturaDropFolderFile();
					$updateDropFolderFile->status = KalturaDropFolderFileStatus::PENDING;
					$this->kClient->dropFolderFile->update($dropFolderFile->id, $updateDropFolderFile);
					return KalturaDropFolderFileStatus::PENDING;
				}
				catch (Exception $e) {
					KalturaLog::err('Cannot update status to PENDING for drop folder file id ['.$dropFolderFile->id.'] - '.$e->getMessage());
					return; // error - can't update drop folder file object's status
				}
			}
			else {
				// not enough time passed - continue to next file
				return;
			}
		}
	}
	
	
	/**
	 * Check if enough time had passed since the already handled $dropFolderFile was last updated, and purge it if required by $dropFolder configuration
	 * @param KalturaDropFolder $dropFolder
	 * @param KalturaDropFolderFile $dropFolderFile
	 * @param string $sharedPhysicalFilePath
	 */
	private function purgeHandledFileIfNeeded(KalturaDropFolder $dropFolder, KalturaDropFolderFile $dropFolderFile, $sharedPhysicalFilePath)
	{
		if ($dropFolderFile->status != KalturaDropFolderFileStatus::HANDLED) {
			return null;
		}
		
		if ($dropFolder->fileDeletePolicy != KalturaDropFolderFileDeletePolicy::AUTO_DELETE) {
			return null;	
		}
		
		if (time() > $dropFolderFile->updatedAt + $dropFolder->autoFileDeleteDays*86400)
		{
			return $this->purgeFile($dropFolderFile, $sharedPhysicalFilePath);
		}
		
		return true;		
	}
	
	/**
	 * 
	 * Physically delete the file in $sharedPhysicalFilePath and change the $dropFolderFile status to KalturaDropFolderFileStatus::PURGED
	 * @param KalturaDropFolderFile $dropFolderFile
	 * @param string $sharedPhysicalFilePath
	 */
	private function purgeFile(KalturaDropFolderFile $dropFolderFile, $sharedPhysicalFilePath)
	{
		// physicaly delete the file
		$delResult = unlink($sharedPhysicalFilePath);
		if (!$delResult) {
			KalturaLog::err("Cannot delete physical file at path [$sharedPhysicalFilePath]");
			return false;
		}
		
		// change status to PURGED
		try {
			$updateDropFolderFile = new KalturaDropFolderFile();
			$updateDropFolderFile->status = KalturaDropFolderFileStatus::PURGED;
			$this->kClient->dropFolderFile->update($dropFolderFile->id, $updateDropFolderFile);
		}
		catch (Exception $e) {
			KalturaLog::err('Cannot update status for drop folder file id ['.$dropFolderFile->id.'] - '.$e->getMessage());
			return false;
		}
		
		return true;
	}
	
	
	private function handlePendingFile(KalturaDropFolder $dropFolder, KalturaDropFolderFile $dropFolderFile)
	{
		// handle the PENDING file
		$this->handleFile($dropFolder, $dropFolderFile);
		
		// handle all files in status WAITING
		$waitingFiles = $this->getDropFolderFileObjects($dropFolderFile->dropFolderId, KalturaDropFolderFileStatus::WAITING);
		foreach ($waitingFiles as $waitingFile)
		{
			//TODO: get the file from the API again, because its status might have been changed
			$this->handleFile($dropFolder, $waitingFile);
		}				
	}
	
	
	private function handleFile(KalturaDropFolder $dropFolder, KalturaDropFolderFile $dropFolderFile)
	{
		// get defined file name patterns
		$filePatterns = $dropFolder->fileNamePatterns;
		$filePatterns = array_map('trim', explode(',', $filePatterns));
		
		// get current file name
		$fileName = $dropFolderFile->fileName;
		
		// search for a match
		$matchFound = false;
		foreach ($filePatterns as $pattern)
		{
			if (!is_null($pattern) && ($pattern != '')) {
				if (fnmatch($pattern, $fileName)) {
					$matchFound = true;
				}
			}
		}
		
		// if match found -> handle file by the file handelr configured for its drop folder
		if ($matchFound)
		{
			$fileHandler = DropFolderFileHandler::getHandler($dropFolder->fileHandlerType);
			$fileHandler->setConfig($this->kClient, $dropFolderFile, $dropFolder);
			$fileHandler->handle();
		}		
		
	}
	
	
	
}
