<?php
/**
 * Watches drop folder files and executes file handlers as required 
 *
 * @package plugins.dropFolder
 * @subpackage Scheduler
 */
class KAsyncDropFolderWatcher extends KPeriodicWorker
{
	const NEW_FILE = -1;
	
	/**
	 * @var KalturaDropFolderClientPlugin
	 */
	protected $dropFolderPlugin = null;
	
	/**
	 * @var KPhysicalDropFolderUtils
	 */
	private $physicalDropFolderUtils = null;
	
	/**
	 * @var KDropFolderFileHandler
	 */
	private $dropFolderFileHandler = null;
	
	private $purgedFiles = null;
		
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
			    		$this->setDropFolderError($folder, KalturaDropFolderErrorCode::ERROR_CONNECT, 'Failed to connect to the drop folder', $e);
			    	if($e->getCode() == kFileTransferMgrException::cantAuthenticate)
			    		$this->setDropFolderError($folder, KalturaDropFolderErrorCode::ERROR_AUTENTICATE, 'Failed to autenticate', $e);
			    	else
			    		$this->setDropFolderError($folder, KalturaDropFolderErrorCode::ERROR_GET_PHISICAL_FILE_LIST, 'Failed to retrieve files from the drop folder', $e);
			    	$this->unimpersonate();
			    }
			    catch (KalturaException $e)
			    {
			    	$this->setDropFolderError($folder, KalturaDropFolderErrorCode::ERROR_GET_DB_FILE_LIST, 'Failed to get a list of drop folder files from the Database', $e);
			    	$this->unimpersonate();
			    }
			    catch (Exception $e) 
			    {			        
			        $this->setDropFolderError($folder, KalturaDropFolderErrorCode::DROP_FOLDER_APP_ERROR, 'Applicative error with the drop folder', $e);	
			        $this->unimpersonate();
			    }
			}
		}
	}
	
	private function watchFolder(KalturaDropFolder $folder)
	{
		KalturaLog::debug('Watching folder ['.$folder->id.']');
		
		$this->impersonate($folder->partnerId);	
		
		$originalDropFolderStatus = $folder->status;		
		if($folder->status == KalturaDropFolderStatus::ERROR)
			$folder->status = KalturaDropFolderStatus::ENABLED;
			    										
		$this->physicalDropFolderUtils = new KPhysicalDropFolderUtils($folder);
		$this->dropFolderFileHandler = KDropFolderFileHandler::getHandler($folder->fileHandlerType, $this->kClient, $folder);					
		$physicalFiles = $this->getSortedDropFolderFilesFromPhysicalFolder($folder);
			
		$filter = $this->createDropFolderFileFilter($folder->id);
		$pager = $this->createDropFolderFilterPager();
		$dropFolderFiles = null;
		$ignorePatterns = array_map('trim', explode(',', $folder->ignoreFileNamePatterns));	
		$lastDropFolderFileIndex = 0;
		$eol = false;
		$this->purgedFiles = array();
		
		foreach ($physicalFiles as $physicalFileName) 
		{	
			if($this->validatePhysicalFile($folder, $physicalFileName, $ignorePatterns))
			{	
				KalturaLog::debug('Watch file ['.$physicalFileName.']');
				
				$currentDropFolderFileIndex = $this->findDropFolderFileByPhisicalName($physicalFileName, $dropFolderFiles, 
																					  $filter, $pager, $lastDropFolderFileIndex, $eol);				
				if($currentDropFolderFileIndex == self::NEW_FILE)
				{
					try 
					{
						$fullPath = $folder->path.'/'.$physicalFileName;
						$lastModificationTime = $this->physicalDropFolderUtils->fileTransferMgr->modificationTime($fullPath);
						$fileSize = $this->physicalDropFolderUtils->fileTransferMgr->fileSize($fullPath);
						$this->dropFolderFileHandler->handleFileAdded($physicalFileName, $fileSize, $lastModificationTime);
							
					}
					catch (Exception $e)
					{
						KalturaLog::err("Error handling drop folder file [$physicalFileName] " . $e->getMessage());
					}						
				}
				else //drop folder file entry found
				{
					$this->handleExisitingDropFolderFile($folder, $dropFolderFiles[$currentDropFolderFileIndex]);
				}					
			}					
		}
		$this->markFilesAsPurged($dropFolderFiles, $lastDropFolderFileIndex);
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
	
	
	private function createDropFolderFileFilter($dropFolderId)
	{
		$dropFolderFileFilter = new KalturaDropFolderFileFilter();
		$dropFolderFileFilter->dropFolderIdEqual = $dropFolderId;
		$dropFolderFileFilter->orderBy = KalturaDropFolderFileOrderBy::FILE_NAME_ASC;
		
		return $dropFolderFileFilter;
	}
	
	private function createDropFolderFilterPager()
	{
		$pager = new KalturaFilterPager();
		$pager->pageSize = 500;
		if($this->taskConfig->params->pageSize)
			$pager->pageSize = $this->taskConfig->params->pageSize;		
			
		return $pager;
	}
		
	private function getSortedDropFolderFilesFromPhysicalFolder($folder)
	{
		KalturaLog::debug('Retrieving physical files list');
		
		$physicalFiles = $this->physicalDropFolderUtils->fileTransferMgr->listDir($folder->path);
		if ($physicalFiles) 
			usort($physicalFiles, 'strcasecmp');			
		else
			KalturaLog::info('No physical files found for drop folder id ['.$folder->id.'] with path ['.$folder->path.']');
		
		KalturaLog::log('Found ['.count($physicalFiles).'] in the folder');
		
		return $physicalFiles;
	}
	
	private function findDropFolderFileByPhisicalName($physicalFileName, &$dropFolderFiles, KalturaDropFolderFileFilter $filter, KalturaFilterPager &$pager, &$lastDropFolderFileIndex, &$eol)
	{
		while(!$eol)
		{
			if($dropFolderFiles == null || $lastDropFolderFileIndex > count($dropFolderFiles))
				$getNextPage = true;
			else
				$getNextPage = false;
			
			if($getNextPage)
			{
				$getNextPage = false;
				$pager->pageIndex++;
				$dropFolderFiles = $this->dropFolderPlugin->dropFolderFile->listAction($filter, $pager);
				$dropFolderFiles = $dropFolderFiles->objects;
				$lastDropFolderFileIndex = 0;
			}
			
			for ($i = $lastDropFolderFileIndex; $i < count($dropFolderFiles); $i++) 
			{
				$dropFolderFile = $dropFolderFiles[$i];
				KalturaLog::debug('comparing files: '.$dropFolderFile->fileName. ' '.$physicalFileName);
				$res = strcasecmp($dropFolderFile->fileName, $physicalFileName);
				if($res == 0)
				{
					$res = strcmp($dropFolderFile->fileName, $physicalFileName);
				}
				if($res == 0)
				{
					$lastDropFolderFileIndex = $i+1;
					return $i;
				}
				else if($res > 0)
				{
					$lastDropFolderFileIndex = $i;
					return self::NEW_FILE;
				}
				if($dropFolderFile->status != KalturaDropFolderFileStatus::PARSED)
					$this->purgedFiles[] = $dropFolderFile;
			}
			$lastDropFolderFileIndex = $i;
			if($lastDropFolderFileIndex >= count($dropFolderFiles) && count($dropFolderFiles) < $pager->pageSize)
				$eol = true;
			else 
				$lastDropFolderFileIndex++;
		}
		
		return self::NEW_FILE;
	}
	
	
	private function handleExisitingDropFolderFile(KalturaDropFolder $folder, KalturaDropFolderFile $dropFolderFile)
	{
		KalturaLog::debug('Handling existing drop folder file with id ['.$dropFolderFile->id.']');
		try 
		{
			$knownLastModificationTime = $dropFolderFile->lastModificationTime;
			$fullPath = $folder->path.'/'.$dropFolderFile->fileName;
			$lastModificationTime = $this->physicalDropFolderUtils->fileTransferMgr->modificationTime($fullPath);
			$fileSize = $this->physicalDropFolderUtils->fileTransferMgr->fileSize($fullPath);
		}
		catch (Exception $e)
		{
			$this->dropFolderFileHandler->handleFileError($dropFolderFile->id, KalturaDropFolderFileStatus::ERROR_HANDLING, KalturaDropFolderFileErrorCode::ERROR_READING_FILE, 
															'Cannot read file or file details at path ['.$fullPath.']', $e);	
			return false;		
		}				 
		
		if ($knownLastModificationTime && ($lastModificationTime > $knownLastModificationTime))
		{						 					 			
			$this->dropFolderFileHandler->handleFileReplaced($dropFolderFile->id, $dropFolderFile->fileName, $fileSize, $lastModificationTime);
		}		 	
		else 
		{	
			switch ($dropFolderFile->status)
			 {
			 	case KalturaDropFolderFileStatus::PARSED:
			 		$this->dropFolderFileHandler->handleFileAdded($dropFolderFile->fileName, $fileSize, $lastModificationTime);
			 		break;
			 	case KalturaDropFolderFileStatus::UPLOADING:
			 		$this->handleUploadingDropFolderFile($folder, $dropFolderFile, $fileSize, $lastModificationTime);
			 		break;
			 	case KalturaDropFolderFileStatus::HANDLED:
			 		if($dropFolder->fileDeletePolicy != KalturaDropFolderFileDeletePolicy::AUTO_DELETE)
			 			break;
			 	case KalturaDropFolderFileStatus::DELETED:
			 		$this->purgeFile($folder, $dropFolderFile);
			 		break;
			 }	
		}
	}
	
	private function handleUploadingDropFolderFile(KalturaDropFolder $dropFolder, KalturaDropFolderFile $dropFolderFile, $currentFileSize, $lastModificationTime)
	{		
		if (!$currentFileSize) 
		{
			$this->dropFolderFileHandler->handleFileError($dropFolderFile->id, KalturaDropFolderFileStatus::ERROR_HANDLING, KalturaDropFolderFileErrorCode::ERROR_READING_FILE, 
															'Cannot read file or file details at path ['.$dropFolder->partnerId.'/'.$dropFolderFile->fileName);
		}		
		else if ($currentFileSize != $dropFolderFile->fileSize)
		{
			$this->dropFolderFileHandler->handleFileUploading($dropFolderFile->id, $currentFileSize, $lastModificationTime);
		}
		else // file sizes are equal
		{
			$time = time();
			$fileSizeLastSetAt = $dropFolder->fileSizeCheckInterval + $dropFolderFile->fileSizeLastSetAt;
			
			KalturaLog::debug("time [$time] fileSizeLastSetAt [$fileSizeLastSetAt]");
			
			// check if fileSizeCheckInterval time has passed since the last file size update	
			if ($time > $fileSizeLastSetAt)
			{
				$this->dropFolderFileHandler->handleFileUploaded($dropFolderFile->id, $lastModificationTime);
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
			$this->dropFolderFileHandler->handleFileError($dropFolderFile->id, KalturaDropFolderFileStatus::ERROR_DELETING, KalturaDropFolderFileErrorCode::ERROR_DELETING_FILE, 
														 'Cannot delete physical file at path ['.$fullPath.']');
		else
		 	$this->dropFolderFileHandler->handleFilePurged($dropFolderFile->id);
	}
	
	private function markFilesAsPurged($dropFolderFiles, $index)
	{
		foreach ($this->purgedFiles as $purgedFile) 
		{
			$this->dropFolderFileHandler->handleFilePurged($purgedFile->id);
		}
		for ($i=$index; $i < count ($dropFolderFiles); $i++)
		{
			$this->dropFolderFileHandler->handleFilePurged($dropFolderFiles[$i]->id);
		}
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
		
		try 
		{
			$dropFolders = $this->dropFolderPlugin->dropFolder->listAction($filter, $this->createDropFolderFilterPager());
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
