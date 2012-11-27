<?php
/**
 * Watches drop folder files and executes file handlers as required 
 *
 * @package plugins.dropFolder
 * @subpackage Scheduler
 */
class KAsyncDropFolderWatcher extends KPeriodicWorker
{
	/**
	 * @var KalturaDropFolderClientPlugin
	 */
	protected $dropFolderPlugin = null;
	
	/**
	 * @var kFileTransferMgr
	 */
	private $fileTransferMgr = null;
	
	protected static $dropFolderFileErrorStatuses = array(KalturaDropFolderFileStatus::DELETED, KalturaDropFolderFileStatus::ERROR_DELETING, KalturaDropFolderFileStatus::ERROR_DOWNLOADING, KalturaDropFolderFileStatus::ERROR_HANDLING);
	
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::DROP_FOLDER_WATCHER;
	}
	
	/* (non-PHPdoc)
	 * @see KBatchBase::getJobType()
	 */
	public function getJobType()
	{
		return self::getType();
	}
	
	/* (non-PHPdoc)
	 * @see KBatchBase::run()
	*/
	public function run($jobs = null)
	{
		KalturaLog::info("Drop folder watcher batch is running");
		
		$this->dropFolderPlugin = KalturaDropFolderClientPlugin::get($this->kClient);
		
		if($this->taskConfig->isInitOnly())
			return $this->init();
		
		//TODO: use getFilter instead of taskConfig->params
		// get drop folder tags to work on from configuration
		$folderTags = $this->taskConfig->params->tags;
		
		if (strlen($folderTags) == 0) {
			KalturaLog::err('Tags configuration is empty - cannot continue');
			return;
		}
		
		// get list of drop folders according to configuration
		$filter = new KalturaDropFolderFilter();
		
		if ($folderTags != '*') {
			$filter->tagsMultiLikeOr = $folderTags;
		}
			
		$filter->currentDc = KalturaNullableBoolean::TRUE_VALUE;
		$filter->statusEqual = KalturaDropFolderStatus::ENABLED;
		
		try {
			$dropFolders = $this->dropFolderPlugin->dropFolder->listAction($filter);
		}
		catch (Exception $e) {
			$this->unimpersonate();
			KalturaLog::err('Cannot get drop folder list - '.$e->getMessage());
			return;
		}
		
		$dropFolders = $dropFolders->objects;
		KalturaLog::log('['.count($dropFolders).'] folders to watch');
		
		foreach ($dropFolders as $folder)
		{
		    try {
			    $this->watchFolder($folder);
		    }
		    catch (Exception $e) {
			$this->unimpersonate();
		        KalturaLog::err('Unknown error with folder id ['.$folder->id.'] - '.$e->getMessage());			
		    }
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
		KalturaLog::debug('Watching folder ['.$folder->id.']');
		
		// if remote folder -> login to server and set fileTransferManager
		try {
		    $this->fileTransferMgr = DropFolderBatchUtils::getFileTransferManager($folder);
		}
	    catch (Exception $e) {
			$this->unimpersonate();
			KalturaLog::err('Cannot initialize file transfer manager for folder ['.$folder->id.'] - '.$e->getMessage());
			return; // skipping to next folder
		}
		
		// get list of DropFolderFile objects from the current $folder
		$dropFolderFiles = null;
		$deletedDropFolderFiles = null;
		try {
			$dropFolderFiles = $this->getDropFolderFileObjects($folder->id);
		}
		catch (Exception $e) {
			$this->unimpersonate();
			KalturaLog::err('Cannot get drop folder file list from the server for drop folder id ['.$folder->id.'] - '.$e->getMessage());
			return; // skipping to next folder
		}
		
		
		// get a list of physical files from the folder's path
		$physicalFiles = null;
		try {
			$physicalFiles = $this->getPhysicalFileList($folder);
		}
		catch (Exception $e) {
			$this->unimpersonate();
			$physicalFiles = null;
		}
		if (!$physicalFiles) {
			KalturaLog::err('Cannot get physical file list for drop folder id ['.$folder->id.'] with path ['.$folder->path.']');
			return; // skipping to next folder
		}	

		// with local drop folder, file may have been moved (hence deleted) immidiately upon ingestion
		$autoDeleteOriginalFile = $folder->fileDeletePolicy == KalturaDropFolderFileDeletePolicy::AUTO_DELETE && $folder->autoFileDeleteDays == 0;
				
		$dropFolderFileMapByName = array();
		
		foreach ($dropFolderFiles as $dropFolderFile)
		{
			if ($dropFolderFile->status !== KalturaDropFolderFileStatus::PURGED)
			{
				if (!in_array($dropFolderFile->fileName, $physicalFiles))
				{
					if ($autoDeleteOriginalFile)
        				$this->setFileAsPurged($dropFolderFile);
					else if (!in_array($dropFolderFile->status, self::$dropFolderFileErrorStatuses))
						$this->errorWithFile($dropFolderFile, KalturaDropFolderFileErrorCode::ERROR_READING_FILE, 'Cannot find file with name ['.$dropFolderFile->fileName.']');
						
					continue;
				}				
				$dropFolderFileMapByName[$dropFolderFile->fileName] = $dropFolderFile;
			}			
		}		
		
		// get defined file name patterns
		$ignorePatterns = $folder->ignoreFileNamePatterns;
		$ignorePatterns = array_map('trim', explode(',', $ignorePatterns));
		
		// sync between physical file list and drop folder file objects
		foreach ($physicalFiles as $physicalFileName)
		{
			try {
				if (empty($physicalFileName) || $physicalFileName === '.' || $physicalFileName === '..') {
					continue;
				}
				KalturaLog::debug("Watch file [$physicalFileName]");
				
				$shouldIgnore = false;
				foreach ($ignorePatterns as $ignorePattern)
				{
					if (!is_null($ignorePattern) && ($ignorePattern != '')) {
						if (fnmatch($ignorePattern, $physicalFileName)) {
							$shouldIgnore = true;
							KalturaLog::err("Ignoring file [$physicalFileName] matching ignore pattern [$ignorePattern]");
							break;
						}
					}
				}
				if ($shouldIgnore) {
					continue;
				}
				
				// translate file name to path+name on the shared location
				$fullPath = $folder->path.'/'.$physicalFileName;
				
				// skip non-accessible files
				if (!$fullPath || !$this->fileTransferMgr->fileExists($fullPath))
				{
					KalturaLog::err("Cannot access physical file in path [$fullPath]");
					continue;
				}
				
				// skip directories
				/*
				if (is_dir($fullPath)) {
					KalturaLog::log("Path [$fullPath] is a directory - skipped");
					continue;
				}
				*/
				
				// check if file is already in the list of drop folder files
				if (!array_key_exists($physicalFileName, $dropFolderFileMapByName))
				{
					// new physical file found in folder - add new drop folder file object with status UPLOADING
					$this->addNewDropFolderFile($folder->id, $physicalFileName, $fullPath);	
				}
				else
				{
				    $currentDropFolderFile = $dropFolderFileMapByName[$physicalFileName];
				    
				    try {
				        $lastModificationTime = $this->getModificationTime($fullPath);
				    }
				    catch (Exception $e) {
					$this->unimpersonate();
				        KalturaLog::err('Cannot get modification time for file in path ['.$fullPath.'] - '.$e->getMessage());
				        continue; // skipping to next file
				    }
				    
				    $knownLastModificationTime = $currentDropFolderFile->lastModificationTime;
				    if ($knownLastModificationTime && ($lastModificationTime > $knownLastModificationTime) && 
				        ($currentDropFolderFile->status != KalturaDropFolderFileStatus::UPLOADING))
				    {
				        // file has been replaced by a new file with the same name
				        $this->setFileAsPurged($currentDropFolderFile);
				        $this->addNewDropFolderFile($folder->id, $physicalFileName, $fullPath, $lastModificationTime);
				        continue; // continue to next file
				    }			    
				    
					// update existing drop folder file object according to current physical file
					if ($currentDropFolderFile->status == KalturaDropFolderFileStatus::UPLOADING)
					{
						$this->updateDropFolderFile($folder, $currentDropFolderFile, $fullPath, $lastModificationTime);
					}
					else if	($currentDropFolderFile->status == KalturaDropFolderFileStatus::HANDLED)
					{
						$this->purgeHandledFileIfNeeded($folder, $currentDropFolderFile, $fullPath);
					}
				    else if	($currentDropFolderFile->status == KalturaDropFolderFileStatus::DELETED)
					{
					    // purge file marked as deleted
						$this->purgeFile($dropFolderFileMapByName[$physicalFileName], $fullPath);
	    				continue;
					}
				}
			}
			catch (Exception $e)
			{
				$this->unimpersonate();
				KalturaLog::err("Error handling drop folder file [$physicalFileName] " . $e->getMessage());
			}
		}
	}
	
	private static function getRealPath($dropFolderPath, $fileName)
	{
		$realPath = realpath($dropFolderPath.'/'.$fileName);
		return $realPath;
	}
	
	
	/**
	 * @param int $dropFolderId
	 * @param KalturaDropFolderFileStatus 
	 * @return array of KalturaDropFolderFile objects that belong to the given folder id
	 */
	private function getDropFolderFileObjects($dropFolderId)
	{
		$dropFolderFileFilter = new KalturaDropFolderFileFilter();
		$dropFolderFileFilter->dropFolderIdEqual = $dropFolderId;
		
		$pager = new KalturaFilterPager();
		$pager->pageSize = 1000;
		if($this->taskConfig->params->pageSize)
			$pager->pageSize = $this->taskConfig->params->pageSize;
		
		$dropFolderFiles = $this->dropFolderPlugin->dropFolderFile->listAction($dropFolderFileFilter, $pager);
		$dropFolderFiles = $dropFolderFiles->objects;
		return $dropFolderFiles;
	}
	
	/**
	 * @param KalturaDropFolder $folder
	 * @return array of file names in the given $folder's path
	 */
	private function getPhysicalFileList(KalturaDropFolder $folder)
	{	    
	   return $this->fileTransferMgr->listDir($folder->path);
	}
	
	/**
	 * @param string $filePath
	 * @return int file size of the given $filePath
	 */
	private function getFileSize($filePath)
	{
	    return $this->fileTransferMgr->fileSize($filePath);
	}
	
	/**
	 * @param string $filePath
	 * @return int last modification time for the given $filePath
	 */
	private function getModificationTime($filePath)
	{
	    return $this->fileTransferMgr->modificationTime($filePath);
	}
	
	/**
	 * Add a new drop folder file in status KalturaDropFolderFileStatus::UPLOADING
	 * @param int $folderId
	 * @param string $fileName
	 * @param string $fullPath
	 * @param int $lastModificationTime
	 */
	private function addNewDropFolderFile($folderId, $fileName, $fullPath, $lastModificationTime = null)
	{
	    try
	    {
    	    $newDropFolderFile = new KalturaDropFolderFile();
    		$newDropFolderFile->dropFolderId = $folderId;
    		$newDropFolderFile->fileName = $fileName;
    		$newDropFolderFile->fileSize = $this->getFileSize($fullPath);
    		$newDropFolderFile->lastModificationTime = $lastModificationTime ? $lastModificationTime : $this->getModificationTime($fullPath);
			$this->dropFolderPlugin->dropFolderFile->add($newDropFolderFile);
		}
		catch (Exception $e) {
			$this->unimpersonate();
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
	 * @param int $lastModificationTime
	 */
	private function updateDropFolderFile(KalturaDropFolder $dropFolder, KalturaDropFolderFile $dropFolderFile, $sharedPhysicalFilePath, $lastModificationTime)
	{
		$physicalFileSize = $this->getFileSize($sharedPhysicalFilePath);

		if (!$physicalFileSize) {
			$this->errorWithFile($dropFolderFile, KalturaDropFolderFileErrorCode::ERROR_READING_FILE, "Cannot get file size for path [$sharedPhysicalFilePath]");
			KalturaLog::err("Cannot get file size for path [$sharedPhysicalFilePath]");
			return; // error - can't get file size
		}
		
		if ($physicalFileSize != $dropFolderFile->fileSize)
		{
			try {
				$updateDropFolderFile = new KalturaDropFolderFile();
				$updateDropFolderFile->fileSize = $physicalFileSize;
				$updateDropFolderFile->lastModificationTime = $lastModificationTime;
				$this->impersonate($dropFolderFile->partnerId);
				$this->dropFolderPlugin->dropFolderFile->update($dropFolderFile->id, $updateDropFolderFile);
				$this->unimpersonate();	
			}
			catch (Exception $e) {
				KalturaLog::err('Cannot update file size for drop folder file id ['.$dropFolderFile->id.'] - '.$e->getMessage());
				$this->unimpersonate();	
				return; // error - can't update drop folder file object's file size
			}
				
		}
		else // file sizes are equal
		{
			$time = time();
			$fileSizeLastSetAt = $dropFolder->fileSizeCheckInterval + $dropFolderFile->fileSizeLastSetAt;
			KalturaLog::debug("time [$time] fileSizeLastSetAt [$fileSizeLastSetAt]");
			// check if fileSizeCheckInterval time has passed since the last file size update	
			if ($time > $fileSizeLastSetAt)
			{
				// update the file to status PENDING (will raise an event)
				try {
					$updateDropFolderFile = new KalturaDropFolderFile();
					$updateDropFolderFile->lastModificationTime = $lastModificationTime;
					$this->impersonate($dropFolderFile->partnerId);
					$this->dropFolderPlugin->dropFolderFile->update($dropFolderFile->id, $updateDropFolderFile);
					$this->dropFolderPlugin->dropFolderFile->updateStatus($dropFolderFile->id, KalturaDropFolderFileStatus::PENDING);
					$this->unimpersonate();
					return true;
				}
				catch (Exception $e) {
					KalturaLog::err('Cannot update status to PENDING for drop folder file id ['.$dropFolderFile->id.'] - '.$e->getMessage());
					$this->unimpersonate();
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
	private function purgeFile(KalturaDropFolderFile $dropFolderFile, $physicalFilePath)
	{
		// physicaly delete the file
		$delResult = null;
		try {
		    $delResult = $this->fileTransferMgr->delFile($physicalFilePath);
		}
		catch (Exception $e) {
		    $this->unimpersonate();
		    KalturaLog::err('Cannot delete physical file ['.$physicalFilePath.'] for drop folder file id ['.$dropFolderFile->id.'] - '.$e->getMessage());
		    return false;
		}
		if (!$delResult) {
			KalturaLog::err("Cannot delete physical file at path [$physicalFilePath]");
			try {
				$this->impersonate($dropFolderFile->partnerId);
				$this->dropFolderPlugin->dropFolderFile->updateStatus($dropFolderFile->id, KalturaDropFolderFileStatus::ERROR_DELETING);
				$this->unimpersonate();
			}
			catch (Exception $e) {
				$this->unimpersonate();
				KalturaLog::err('Cannot update status for drop folder file id ['.$dropFolderFile->id.'] - '.$e->getMessage());
			}
			return false;
		}
		
		// change status to PURGED
		return $this->setFileAsPurged($dropFolderFile);
	}
	
	
	/**
	 * 
	 * Set file to status KalturaDropFolderStatus::PURGED
	 * @param KalturaDropFolderFile $dropFolderFile
	 */
	private function setFileAsPurged(KalturaDropFolderFile $dropFolderFile)
	{
		// change status to PURGED
		try {
			$this->impersonate($dropFolderFile->partnerId);
			$this->dropFolderPlugin->dropFolderFile->updateStatus($dropFolderFile->id, KalturaDropFolderFileStatus::PURGED);
			$this->unimpersonate();
		}
		catch (Exception $e) {
			KalturaLog::err('Cannot update status for drop folder file id ['.$dropFolderFile->id.'] - '.$e->getMessage());
			$this->unimpersonate();
			return false;
		}
		
		return true;
	}
	
	
	private function errorWithFile(KalturaDropFolderFile $dropFolderFile, $errorCode, $errorMessage)
	{
		try {
			$updateDropFolderFile = new KalturaDropFolderFile();
			$updateDropFolderFile->errorCode = $errorCode;
			$updateDropFolderFile->errorDescription = $errorMessage;
			$this->impersonate($dropFolderFile->partnerId);
			$this->dropFolderPlugin->dropFolderFile->update($dropFolderFile->id, $updateDropFolderFile);
			$this->dropFolderPlugin->dropFolderFile->updateStatus($dropFolderFile->id, KalturaDropFolderFileStatus::ERROR_HANDLING);
			$this->unimpersonate();
		}
		catch (Exception $e) {
			KalturaLog::err('Cannot update status for drop folder file id ['.$dropFolderFile->id.'] - '.$e->getMessage());
			$this->unimpersonate();
			return false;
		}
		
		return true;
	}
	
	function log($message)
	{
		KalturaLog::debug($message);
	}	
}
