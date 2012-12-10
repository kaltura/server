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
	 * @var KPhysicalDropFolderUtils
	 */
	private $physicalDropFolderUtils = null;
	
	/**
	 * @var KDropFolderServicesHelper
	 */
	private $dropFolderServicesHelper = null;
			
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
		$this->dropFolderServicesHelper = new KDropFolderServicesHelper($this->kClient);	
		
		if($this->taskConfig->isInitOnly())
			return $this->init();
		
		$dropFolders = $this->getDropFoldersList();
		if(isset($dropFolders))
		{
			$dropFolders = $dropFolders->objects;
			KalturaLog::log('['.count($dropFolders).'] folders to watch');
			
			foreach ($dropFolders as $folder)
			{
			    try 
			    {	
				    $this->watchFolder($folder);				    
			    }
			    catch (kFileTransferMgrException $e)
			    {
			    	if($e->getCode() == kFileTransferMgrException::cantConnect)
			    		$this->setDropFolderError($folder, KalturaDropFolderErrorCode::ERROR_CONNECT, DropFolderPlugin::ERROR_CONNECT_MESSAGE, $e);
			    	if($e->getCode() == kFileTransferMgrException::cantAuthenticate)
			    		$this->setDropFolderError($folder, KalturaDropFolderErrorCode::ERROR_AUTENTICATE, DropFolderPlugin::ERROR_AUTENTICATE_MESSAGE, $e);
			    	else
			    		$this->setDropFolderError($folder, KalturaDropFolderErrorCode::ERROR_GET_PHISICAL_FILE_LIST, DropFolderPlugin::ERROR_GET_PHISICAL_FILE_LIST_MESSAGE, $e);
			    	$this->unimpersonate();
			    }
			    catch (KalturaException $e)
			    {
			    	$this->setDropFolderError($folder, KalturaDropFolderErrorCode::ERROR_GET_DB_FILE_LIST, DropFolderPlugin::ERROR_GET_DB_FILE_LIST_MESSAGE, $e);
			    	$this->unimpersonate();
			    }
			    catch (Exception $e) 
			    {			        
			        $this->setDropFolderError($folder, KalturaDropFolderErrorCode::DROP_FOLDER_APP_ERROR, DropFolderPlugin::DROP_FOLDER_APP_ERROR_MESSAGE.$e->getMessage(), $e);	
			        $this->unimpersonate();
			    }
			}
		}
	}
	
	/**
	 * Handle specific folder:
	 * 1. detect new files and invoke add API
	 * 2. monitor files uploading and change status to PENDING based on the file size change interval
	 * 3. purge files marked as autodelete or in status deleted
	 * 4. mark files that do not exist in a drop folder as purged
	 * @param KalturaDropFolder $folder
	 */
	private function watchFolder(KalturaDropFolder $folder)
	{
		KalturaLog::debug('Watching folder ['.$folder->id.']');
		
		$this->impersonate($folder->partnerId);	
		
		$originalDropFolderStatus = $folder->status;		
		if($folder->status == KalturaDropFolderStatus::ERROR)
			$folder->status = KalturaDropFolderStatus::ENABLED;
			    										
		$this->physicalDropFolderUtils = new KPhysicalDropFolderUtils($folder);		
		$physicalFiles = $this->getDropFolderFilesFromPhysicalFolder($folder);
		if(count($physicalFiles) > 0)
			$dropFolderFilesMap = $this->loadDropFolderFiles($folder);
		else 
			$dropFolderFilesMap = array();
			
		$ignorePatterns = array_map('trim', explode(',', $folder->ignoreFileNamePatterns));	
		
		foreach ($physicalFiles as $physicalFileName) 
		{	
			if($this->validatePhysicalFile($folder, $physicalFileName, $ignorePatterns))
			{	
				KalturaLog::debug('Watch file ['.$physicalFileName.']');
				if(!array_key_exists($physicalFileName, $dropFolderFilesMap))
				{
					try 
					{
						$fullPath = $folder->path.'/'.$physicalFileName;
						$lastModificationTime = $this->physicalDropFolderUtils->fileTransferMgr->modificationTime($fullPath);
						$fileSize = $this->physicalDropFolderUtils->fileTransferMgr->fileSize($fullPath);
						$this->dropFolderServicesHelper->handleFileAdded($physicalFileName, $folder->id, $fileSize, $lastModificationTime);
							
					}
					catch (Exception $e)
					{
						KalturaLog::err("Error handling drop folder file [$physicalFileName] " . $e->getMessage());
					}						
					
				}
				else //drop folder file entry found
				{
					$dropFolderFile = $dropFolderFilesMap[$physicalFileName];
					//if file exist in the folder remove it from the map
					//all the files that are left in a map will be marked as PURGED					
					unset($dropFolderFilesMap[$physicalFileName]);
					$this->handleExisitingDropFolderFile($folder, $dropFolderFile);
				}					
			}					
		}
		foreach ($dropFolderFilesMap as $dropFolderFile) 
		{
			$this->dropFolderServicesHelper->handleFilePurged($dropFolderFile->id);
		}
				
		if($originalDropFolderStatus == KalturaDropFolderStatus::ERROR)
		   	$this->setDropFolderOK($folder);
		
		$this->unimpersonate();		
	}

		
	private function validatePhysicalFile(KalturaDropFolder $folder, $physicalFileName, $ignorePatterns)
	{
		KalturaLog::log('Validating physical file ['.$physicalFileName.']');
		
		$isValid = true;
		try 
		{
			$fullPath = $folder->path.'/'.$physicalFileName;
			if (empty($physicalFileName) || $physicalFileName === '.' || $physicalFileName === '..')
			{
				KalturaLog::err("File name is not set");
				$isValid = false;
			}
			else if(!$fullPath || !$this->physicalDropFolderUtils->fileTransferMgr->fileExists($fullPath))
			{
				KalturaLog::err("Cannot access physical file in path [$fullPath]");
				$isValid = false;				
			}
			else
			{
				foreach ($ignorePatterns as $ignorePattern)
				{
					if (!is_null($ignorePattern) && ($ignorePattern != '') && fnmatch($ignorePattern, $physicalFileName)) 
					{
						KalturaLog::err("Ignoring file [$physicalFileName] matching ignore pattern [$ignorePattern]");
						$isValid = false;
					}
				}
			}
		}
		catch(Exception $e)
		{
			KalturaLog::err("Failure validating physical file [$physicalFileName] - ". $e->getMessage());
			$isValid = false;
		}
		return $isValid;
	}
			
	private function getDropFolderFilesFromPhysicalFolder($folder)
	{
		KalturaLog::debug('Retrieving physical files list');
		
		if($this->physicalDropFolderUtils->fileTransferMgr->fileExists($folder->path))
		{
			$physicalFiles = $this->physicalDropFolderUtils->fileTransferMgr->listDir($folder->path);
			if ($physicalFiles) 
			{
				KalturaLog::log('Found ['.count($physicalFiles).'] in the folder');			
			}		
			else
			{
				KalturaLog::info('No physical files found for drop folder id ['.$folder->id.'] with path ['.$folder->path.']');
				$physicalFiles = array();
			}
		}
		else 
		{
			throw new kFileTransferMgrException('Drop folder path not valid ['.$folder->path.']', kFileTransferMgrException::remotePathNotValid);
		}
				
		return $physicalFiles;
	}
	
	/**
	 * Load all the files from the database that their status is not PURGED, PARSED or DETECTED
	 * @param KalturaDropFolder $folder
	 */
	private function loadDropFolderFiles(KalturaDropFolder $folder)
	{
		$dropFolderFilesMap = array();
		$count = 0;
		$dropFolderFiles =null;
		$eol = false;
		
		$dropFolderFileFilter = new KalturaDropFolderFileFilter();
		$dropFolderFileFilter->dropFolderIdEqual = $folder->id;
		
		$pager = new KalturaFilterPager();
		$pager->pageSize = 500;
		if($this->taskConfig->params->pageSize)
			$pager->pageSize = $this->taskConfig->params->pageSize;	

		while(!$eol)
		{
			$pager->pageIndex++;
			KalturaLog::debug('getting page ['.$pager->pageIndex. '] from Drop Folder File ');
			$dropFolderFiles = $this->dropFolderPlugin->dropFolderFile->listAction($dropFolderFileFilter, $pager);
			$totalCount = $dropFolderFiles->totalCount;
			$dropFolderFiles = $dropFolderFiles->objects;
			KalturaLog::debug('Total files count ['.$totalCount.']');
			foreach ($dropFolderFiles as $dropFolderFile) 
			{
				if($dropFolderFile->status != KalturaDropFolderFileStatus::PARSED && $dropFolderFile->status != KalturaDropFolderFileStatus::DETECTED)
				{
					$dropFolderFilesMap[$dropFolderFile->fileName] = $dropFolderFile;
				}
				$count++;				
				
			}
			KalturaLog::debug('Current count ['.$count.']');
			if($count >= $totalCount)
				$eol = true;
		}	
		return $dropFolderFilesMap;
	}
	
	/**
	 * 1. If file in status UPLOADING check if upload finished
	 * 2. otherwise check if file was replaced based on the last modification time, if yes add new file to the drop folder file table
	 * 3. purge files their autodelete time had arrived and files with status DELETED
	 * @param KalturaDropFolder $folder
	 * @param KalturaDropFolderFile $dropFolderFile
	 */
	private function handleExisitingDropFolderFile(KalturaDropFolder $folder, KalturaDropFolderFile $dropFolderFile)
	{
		KalturaLog::debug('Handling existing drop folder file with id ['.$dropFolderFile->id.']');
		try 
		{
			$fullPath = $folder->path.'/'.$dropFolderFile->fileName;
			$lastModificationTime = $this->physicalDropFolderUtils->fileTransferMgr->modificationTime($fullPath);
			$fileSize = $this->physicalDropFolderUtils->fileTransferMgr->fileSize($fullPath);
		}
		catch (Exception $e)
		{
			KalturaLog::err('Failed to get modification time and file size for file ['.$fullPath.']');
			$this->dropFolderServicesHelper->handleFileError($dropFolderFile->id, KalturaDropFolderFileStatus::ERROR_HANDLING, KalturaDropFolderFileErrorCode::ERROR_READING_FILE, 
															DropFolderPlugin::ERROR_READING_FILE_MESSAGE. '['.$fullPath.']', $e);	
			return false;		
		}				 
				
		if($dropFolderFile->status == KalturaDropFolderFileStatus::UPLOADING)
		{
			$this->handleUploadingDropFolderFile($folder, $dropFolderFile, $fileSize, $lastModificationTime);
		}
		else
		{
			KalturaLog::debug('Last modification time ['.$lastModificationTime.'] known last modification time ['.$dropFolderFile->lastModificationTime.']');
			$isLastModificationTimeUpdated = $dropFolderFile->lastModificationTime && $dropFolderFile->lastModificationTime != '' && ($lastModificationTime > $dropFolderFile->lastModificationTime);
			
			if($isLastModificationTimeUpdated) //file is replaced, add new entry
		 	{
		 		$this->dropFolderServicesHelper->handleFileAdded($dropFolderFile->fileName, $folder->id, $fileSize, $lastModificationTime);
		 	}
		 	else
		 	{
		 		$deleteTime = $dropFolderFile->updatedAt + $folder->autoFileDeleteDays*86400;
		 		if(($dropFolderFile->status == KalturaDropFolderFileStatus::HANDLED && $folder->fileDeletePolicy == KalturaDropFolderFileDeletePolicy::AUTO_DELETE && time() > $deleteTime) ||
		 			$dropFolderFile->status == KalturaDropFolderFileStatus::DELETED)
		 		{
		 			$this->purgeFile($folder, $dropFolderFile);
		 		}
		 	}
		}
	}
	
	private function handleUploadingDropFolderFile(KalturaDropFolder $dropFolder, KalturaDropFolderFile $dropFolderFile, $currentFileSize, $lastModificationTime)
	{		
		if (!$currentFileSize) 
		{
			$this->dropFolderServicesHelper->handleFileError($dropFolderFile->id, KalturaDropFolderFileStatus::ERROR_HANDLING, KalturaDropFolderFileErrorCode::ERROR_READING_FILE, 
															DropFolderPlugin::ERROR_READING_FILE_MESSAGE.'['.$dropFolder->partnerId.'/'.$dropFolderFile->fileName);
		}		
		else if ($currentFileSize != $dropFolderFile->fileSize)
		{
			$this->dropFolderServicesHelper->handleFileUploading($dropFolderFile->id, $currentFileSize, $lastModificationTime);
		}
		else // file sizes are equal
		{
			$time = time();
			$fileSizeLastSetAt = $dropFolder->fileSizeCheckInterval + $dropFolderFile->fileSizeLastSetAt;
			
			KalturaLog::debug("time [$time] fileSizeLastSetAt [$fileSizeLastSetAt]");
			
			// check if fileSizeCheckInterval time has passed since the last file size update	
			if ($time > $fileSizeLastSetAt)
			{
				$this->dropFolderServicesHelper->handleFileUploaded($dropFolderFile->id, $lastModificationTime);
			}
		}
	}
		
	private function purgeFile(KalturaDropFolder $dropFolder, KalturaDropFolderFile $dropFolderFile)
	{
		$fullPath = $dropFolder->path.'/'.$dropFolderFile->fileName;
		// physicaly delete the file
		$delResult = null;
		try 
		{
		    $delResult = $this->physicalDropFolderUtils->fileTransferMgr->delFile($fullPath);
		}
		catch (Exception $e) 
		{
			KalturaLog::err("Error when deleting drop folder file - ".$e->getMessage());
		    $delResult = null;
		}
		if (!$delResult) 
			$this->dropFolderServicesHelper->handleFileError($dropFolderFile->id, KalturaDropFolderFileStatus::ERROR_DELETING, KalturaDropFolderFileErrorCode::ERROR_DELETING_FILE, 
														 DropFolderPlugin::ERROR_DELETING_FILE_MESSAGE. '['.$fullPath.']');
		else
		 	$this->dropFolderServicesHelper->handleFilePurged($dropFolderFile->id);
	}
		
	private function getDropFoldersList() 
	{
		$folderTags = $this->taskConfig->params->tags;
		
		if (strlen($folderTags) == 0) {		
			KalturaLog::err('Tags configuration is empty - cannot continue');			
			return null;
		}
		
		// get list of drop folders according to configuration
		$filter = new KalturaDropFolderFilter();
		
		if ($folderTags != '*') {
			$filter->tagsMultiLikeOr = $folderTags;
		}
			
		$filter->currentDc = KalturaNullableBoolean::TRUE_VALUE;
		$filter->statusIn = KalturaDropFolderStatus::ENABLED. ','. KalturaDropFolderStatus::ERROR;
		
		$pager = new KalturaFilterPager();
		$pager->pageSize = 500;
		if($this->taskConfig->params->pageSize)
			$pager->pageSize = $this->taskConfig->params->pageSize;	
		
		
		try 
		{
			$dropFolders = $this->dropFolderPlugin->dropFolder->listAction($filter, $pager);
			return $dropFolders;
		}
		catch (Exception $e) 
		{
			KalturaLog::err('Cannot get drop folder list - '.$e->getMessage());
			return null;
		}
	}
	
	private function setDropFolderError(KalturaDropFolder $folder, $errorCode, $errorDescirption, Exception $e)
	{
		KalturaLog::err('Error with folder id ['.$folder->id.'] - '.$e->getMessage());
		try 
		{
			if($folder->status != KalturaDropFolderStatus::ERROR)
			{				
				$folder->status = KalturaDropFolderStatus::ERROR;
				$updateDropFolder = new KalturaDropFolder();
				$updateDropFolder->status = KalturaDropFolderStatus::ERROR;
				$updateDropFolder->errorCode = $errorCode;
				$updateDropFolder->errorDescription = $errorDescirption;
				
	    		$this->dropFolderPlugin->dropFolder->update($folder->id, $updateDropFolder);
			}	
		}
		catch(Exception $e)
		{
			KalturaLog::err('Error updating drop folder ['.$folder->id.'] - '.$e->getMessage());
		}	
	}	
	
	private function setDropFolderOK(KalturaDropFolder $folder)
	{
		try 
		{
			$updateDropFolder = new KalturaDropFolder();
			$updateDropFolder->status = KalturaDropFolderStatus::ENABLED;
			$updateDropFolder->errorCode__null = '';
			$updateDropFolder->errorDescription__null = '';
				
	    	$this->dropFolderPlugin->dropFolder->update($folder->id, $updateDropFolder);
		}
		catch(Exception $e)
		{
			KalturaLog::err('Error updating drop folder ['.$folder->id.'] - '.$e->getMessage());
		}	
	}	
		
	function log($message)
	{
		KalturaLog::debug($message);
	}	
}
