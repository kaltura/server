<?php
/**
 * This engine handles the basiC use-cases of drop folders- local, and remote FTP, SFTP.
 */
class KDropFolderFileTransferEngine extends KDropFolderEngine
{
	const IGNORE_PATTERNS_DEFAULT_VALUE  = '*.cache,*.aspx';
	
	/**
	 * @var kFileTransferMgr
	 */	
	protected $fileTransferMgr;

	
	public function watchFolder (KalturaDropFolder $folder)
	{
		$this->dropFolder = $folder;
		$this->fileTransferMgr = self::getFileTransferManager($this->dropFolder);
		KalturaLog::info('Watching folder [' . $this->dropFolder->id . ']');

		$physicalFiles = $this->getDropFolderFilesFromPhysicalFolder();
		$physicalFiles = array_filter($physicalFiles, array($this, 'validatePhysicalFile'));
		if (count($physicalFiles) <= 0)
			return;

		$maxModificationTime = 0;
		$pager = new KalturaFilterPager();
		$pager->pageSize = 500;
		if (KBatchBase::$taskConfig->params->pageSize)
			$pager->pageSize = KBatchBase::$taskConfig->params->pageSize;

		do
		{
			$pager->pageIndex++;
			$dropFolderFiles = $this->loadDropFolderFilesByPage($pager);

			foreach ($dropFolderFiles as $dropFolderFile)
			{
				if (array_key_exists($dropFolderFile->fileName, $physicalFiles))
				{
					$physicalFile = $physicalFiles[$dropFolderFile->fileName];
					unset($physicalFiles[$dropFolderFile->fileName]);

					$maxModificationTime = ($physicalFile->modificationTime > $maxModificationTime) ? $physicalFile->modificationTime : $maxModificationTime;
					KalturaLog::info('Watch file [' . $physicalFile->filename . ']');
					$this->handleExistingDropFolderFile($dropFolderFile);
				} else
				{
					$this->handleFilePurged($dropFolderFile->id);
				}
			}
		} while (count($dropFolderFiles) >= $pager->pageSize);

		foreach ($physicalFiles as &$physicalFile)
		{
			try
			{
				$this->handleFileAdded($physicalFile->filename, $physicalFile->fileSize, $physicalFile->modificationTime);
			} catch (Exception $e)
			{
				KalturaLog::err("Error handling drop folder file [$physicalFile->filename] " . $e->getMessage());
			}
		}

		if ($this->dropFolder->incremental && $maxModificationTime > $this->dropFolder->lastFileTimestamp)
		{
			$updateDropFolder = new KalturaDropFolder();
			$updateDropFolder->lastFileTimestamp = $maxModificationTime;
			$this->dropFolderPlugin->dropFolder->update($this->dropFolder->id, $updateDropFolder);
		}
	}
	
	protected function fileExists ()
	{
		return $this->fileTransferMgr->fileExists($this->dropFolder->path);
	}
	
	protected function handleExistingDropFolderFile (KalturaDropFolderFile $dropFolderFile)
	{
		try 
		{
			$fullPath = $this->dropFolder->path.'/'.$dropFolderFile->fileName;
			$lastModificationTime = $this->fileTransferMgr->modificationTime($fullPath);
			$fileSize = $this->fileTransferMgr->fileSize($fullPath);
		}
		catch (Exception $e)
		{
			$closedStatuses = array(
				KalturaDropFolderFileStatus::HANDLED, 
				KalturaDropFolderFileStatus::PURGED, 
				KalturaDropFolderFileStatus::DELETED
			);
			
			//In cases drop folder is not configured with auto delete we want to verify that the status file is not in one of the closed statuses so 
			//we won't update it to error status
			if(!in_array($dropFolderFile->status, $closedStatuses))
			{
				//Currently "modificationTime" does not throw Exception since from php documentation not all servers support the ftp_mdtm feature
				KalturaLog::err('Failed to get modification time or file size for file ['.$fullPath.']');
				$this->handleFileError($dropFolderFile->id, KalturaDropFolderFileStatus::ERROR_HANDLING, KalturaDropFolderFileErrorCode::ERROR_READING_FILE, 
															DropFolderPlugin::ERROR_READING_FILE_MESSAGE. '['.$fullPath.']', $e);
			}
			return false;		
		}				 
				
		if($dropFolderFile->status == KalturaDropFolderFileStatus::UPLOADING)
		{
			$this->handleUploadingDropFolderFile($dropFolderFile, $fileSize, $lastModificationTime);
		}
		else
		{
			KalturaLog::info('Last modification time ['.$lastModificationTime.'] known last modification time ['.$dropFolderFile->lastModificationTime.']');
			$isLastModificationTimeUpdated = $dropFolderFile->lastModificationTime && $dropFolderFile->lastModificationTime != '' && ($lastModificationTime > $dropFolderFile->lastModificationTime);
			
			if($isLastModificationTimeUpdated) //file is replaced, add new entry
		 	{
		 		$this->handleFileAdded($dropFolderFile->fileName, $fileSize, $lastModificationTime);
		 	}
		 	else
		 	{
		 		$deleteTime = $dropFolderFile->updatedAt + $this->dropFolder->autoFileDeleteDays*86400;
		 		if(($dropFolderFile->status == KalturaDropFolderFileStatus::HANDLED && $this->dropFolder->fileDeletePolicy != KalturaDropFolderFileDeletePolicy::MANUAL_DELETE && time() > $deleteTime) ||
		 			$dropFolderFile->status == KalturaDropFolderFileStatus::DELETED)
		 		{
		 			$this->purgeFile($dropFolderFile);
		 		}
		 	}
		}
	}
	
	protected function handleUploadingDropFolderFile (KalturaDropFolderFile $dropFolderFile, $currentFileSize, $lastModificationTime)
	{
		if (!$currentFileSize) 
		{
			$this->handleFileError($dropFolderFile->id, KalturaDropFolderFileStatus::ERROR_HANDLING, KalturaDropFolderFileErrorCode::ERROR_READING_FILE, 
															DropFolderPlugin::ERROR_READING_FILE_MESSAGE.'['.$this->dropFolder->path.'/'.$dropFolderFile->fileName);
		}		
		else if ($currentFileSize != $dropFolderFile->fileSize)
		{
			$this->handleFileUploading($dropFolderFile->id, $currentFileSize, $lastModificationTime);
		}
		else // file sizes are equal
		{
			$time = time();
			$fileSizeLastSetAt = $this->dropFolder->fileSizeCheckInterval + $dropFolderFile->fileSizeLastSetAt;
			
			KalturaLog::info("time [$time] fileSizeLastSetAt [$fileSizeLastSetAt]");
			
			// check if fileSizeCheckInterval time has passed since the last file size update	
			if ($time > $fileSizeLastSetAt)
			{
				$this->handleFileUploaded($dropFolderFile->id, $lastModificationTime);
			}
		}
	}
	
	protected function handleFileAdded ($fileName, $fileSize, $lastModificationTime)
	{
		try 
		{
			$newDropFolderFile = new KalturaDropFolderFile();
	    	$newDropFolderFile->dropFolderId = $this->dropFolder->id;
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
	
	protected function validatePhysicalFile ($physicalFile)
	{
		$isValid = true;
		try
		{
			/* @var $physicalFile FileObject */
			$physicalFileName = $physicalFile->filename;

			KalturaLog::log('Validating physical file [' . $physicalFileName . ']');

			$utfFileName = kString::stripUtf8InvalidChars($physicalFileName);

			if ($physicalFileName != $utfFileName)
			{
				KalturaLog::info("File name [$physicalFileName] is not utf-8 compatible, Skipping file...");
				return false;
			}

			if (!kXml::isXMLValidContent($utfFileName))
			{
				KalturaLog::info("File name [$physicalFileName] contains invalid XML characters, Skipping file...");
				return false;
			}

			if ($this->dropFolder->incremental && $physicalFile->modificationTime < $this->dropFolder->lastFileTimestamp)
			{
				KalturaLog::info("File modification time [" . $physicalFile->modificationTime . "] predates drop folder last timestamp [" . $this->dropFolder->lastFileTimestamp . "]. Skipping.");
				return false;
			}

			$ignorePatterns = $this->dropFolder->ignoreFileNamePatterns;
			if ($ignorePatterns)
				$ignorePatterns = self::IGNORE_PATTERNS_DEFAULT_VALUE . ',' . $ignorePatterns;
			else
				$ignorePatterns = self::IGNORE_PATTERNS_DEFAULT_VALUE;
			$ignorePatterns = array_map('trim', explode(',', $ignorePatterns));


			$fullPath = $this->dropFolder->path . '/' . $physicalFileName;
			if ($physicalFileName === '.' || $physicalFileName === '..')
			{
				KalturaLog::info("Skipping linux current and parent folder indicators");
				$isValid = false;
			} else if (empty($physicalFileName))
			{
				KalturaLog::err("File name is not set");
				$isValid = false;
			} else if (!$fullPath || !$this->fileTransferMgr->fileExists($fullPath))
			{
				KalturaLog::err("Cannot access physical file in path [$fullPath]");
				$isValid = false;
			} else
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
		} catch (Exception $e)
		{
			KalturaLog::err("Failure validating physical file [$physicalFileName] - " . $e->getMessage());
			$isValid = false;
		}
		return $isValid;
	}
	
	/** 
     * Init a kFileTransferManager acccording to folder type and login to the server
     * @throws Exception
     * 
     * @return kFileTransferMgr
     */
	public static function getFileTransferManager(KalturaDropFolder $dropFolder)
	{
		$engineOptions = isset(KBatchBase::$taskConfig->engineOptions) ? KBatchBase::$taskConfig->engineOptions->toArray() : array();
	    $fileTransferMgr = kFileTransferMgr::getInstance(self::getFileTransferMgrType($dropFolder->type), $engineOptions);
	    
	    $host =null; $username=null; $password=null; $port=null;
	    $privateKey = null; $publicKey = null;
	    
	    if($dropFolder instanceof KalturaRemoteDropFolder)
	    {
	   		$host = $dropFolder->host;
	    	$port = $dropFolder->port;
	    	$username = $dropFolder->username;
	    	$password = $dropFolder->password;
	    }  
	    if($dropFolder instanceof KalturaSshDropFolder)
	    {
	    	$privateKey = $dropFolder->privateKey;
	    	$publicKey = $dropFolder->publicKey;
	    	$passPhrase = $dropFolder->passPhrase;  	    	
	    }

        // login to server
        if ($privateKey || $publicKey) 
        {
	       	$privateKeyFile = self::getTempFileWithContent($privateKey, 'privateKey');
        	$publicKeyFile = self::getTempFileWithContent($publicKey, 'publicKey');
        	$fileTransferMgr->loginPubKey($host, $username, $publicKeyFile, $privateKeyFile, $passPhrase, $port);        	
        }
        else 
        {
        	$fileTransferMgr->login($host, $username, $password, $port);        	
        }
		
		return $fileTransferMgr;		
	}

		/**
	 * This mapping is required since the Enum values of the drop folder and file transfer manager are not the same
	 * @param int $dropFolderType
	 */
	public static function getFileTransferMgrType($dropFolderType)
	{
		switch ($dropFolderType)
		{
			case KalturaDropFolderType::LOCAL:
				return kFileTransferMgrType::LOCAL;
			case KalturaDropFolderType::FTP:
				return kFileTransferMgrType::FTP;
			case KalturaDropFolderType::SCP:
				return kFileTransferMgrType::SCP;
			case KalturaDropFolderType::SFTP:
				return kFileTransferMgrType::SFTP;
			case KalturaDropFolderType::S3:
				return kFileTransferMgrType::S3;
			default:
				return $dropFolderType;				
		}
		
	}
	
	/**
	 * Lazy saving of file content to a temporary path, the file will exist in this location until the temp files are purged
	 * @param string $fileContent
	 * @param string $prefix
	 * @return string path to temporary file location
	 */
	private static function getTempFileWithContent($fileContent, $prefix = '') 
	{
		if(!$fileContent)
			return null;
		$tempDirectory = sys_get_temp_dir();
		$fileLocation = tempnam($tempDirectory, $prefix);		
		file_put_contents($fileLocation, $fileContent);
		return $fileLocation;
	}
	
	/**
	 * Update uploading details
	 * @param int $dropFolderFileId
	 * @param int $fileSize
	 * @param int $lastModificationTime
	 * @param int $uploadStartDetectedAt
	 */
	protected function handleFileUploading($dropFolderFileId, $fileSize, $lastModificationTime, $uploadStartDetectedAt = null)
	{
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
	 * Update upload details and set file status to PENDING
	 * @param int $dropFolderFileId
	 * @param int $lastModificationTime
	 */
	protected function handleFileUploaded($dropFolderFileId, $lastModificationTime)
	{
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
	
	protected function purgeFile(KalturaDropFolderFile $dropFolderFile)
	{
		$fullPath = $this->dropFolder->path.'/'.$dropFolderFile->fileName;
		// physicaly delete the file
		$delResult = null;
		try 
		{
		    $delResult = $this->fileTransferMgr->delFile($fullPath);
		}
		catch (Exception $e) 
		{
			KalturaLog::err("Error when deleting drop folder file - ".$e->getMessage());
		    $delResult = null;
		}
		if (!$delResult) 
			$this->handleFileError($dropFolderFile->id, KalturaDropFolderFileStatus::ERROR_DELETING, KalturaDropFolderFileErrorCode::ERROR_DELETING_FILE, 
														 DropFolderPlugin::ERROR_DELETING_FILE_MESSAGE. '['.$fullPath.']');
		else
		 	$this->handleFilePurged($dropFolderFile->id);
	}
	
	protected function getDropFolderFilesFromPhysicalFolder()
	{
		if($this->fileTransferMgr->fileExists($this->dropFolder->path))
		{
			$physicalFiles = $this->fileTransferMgr->listFileObjects($this->dropFolder->path);
			if ($physicalFiles) 
			{
				KalturaLog::log('Found ['.count($physicalFiles).'] in the folder');			
			}		
			else
			{
				KalturaLog::info('No physical files found for drop folder id ['.$this->dropFolder->id.'] with path ['.$this->dropFolder->path.']');
				$physicalFiles = array();
			}
		}
		else 
		{
			throw new kFileTransferMgrException('Drop folder path not valid ['.$this->dropFolder->path.']', kFileTransferMgrException::remotePathNotValid);
		}

		KalturaLog::info("physical files: ");
		foreach ($physicalFiles as &$currlFile)
		{
			KalturaLog::info(print_r($currlFile, true));
		}
		
		return $physicalFiles;
	}
	
	public function processFolder (KalturaBatchJob $job, KalturaDropFolderContentProcessorJobData $data)
	{
		KBatchBase::impersonate($job->partnerId);
		
		/* @var $data KalturaWebexDropFolderContentProcessorJobData */
		$dropFolder = $this->dropFolderPlugin->dropFolder->get ($data->dropFolderId);
		
		switch ($data->contentMatchPolicy)
		{
			case KalturaDropFolderContentFileHandlerMatchPolicy::ADD_AS_NEW:
				$this->addAsNewContent($job, $data, $dropFolder);
				break;
			
			case KalturaDropFolderContentFileHandlerMatchPolicy::MATCH_EXISTING_OR_KEEP_IN_FOLDER:
				$this->addAsExistingContent($job, $data, null, $dropFolder);
				break;
				
			case KalturaDropFolderContentFileHandlerMatchPolicy::MATCH_EXISTING_OR_ADD_AS_NEW:
				$matchedEntry = $this->isEntryMatch($data);
				if($matchedEntry)
					$this->addAsExistingContent($job, $data, $matchedEntry, $dropFolder);
				else
					 $this->addAsNewContent($job, $data, $dropFolder);	
				break;			
			default:
				throw new kApplicativeException(KalturaDropFolderErrorCode::CONTENT_MATCH_POLICY_UNDEFINED, 'No content match policy is defined for drop folder'); 
				break;
		}
		
		KBatchBase::unimpersonate();
	}
	
	private function addAsNewContent(KalturaBatchJob $job, KalturaDropFolderContentProcessorJobData $data, KalturaDropFolder $dropFolder)
	{ 		
		$resource = $this->getIngestionResource($job, $data);
		$newEntry = new KalturaBaseEntry();
		$newEntry->conversionProfileId = $data->conversionProfileId;
		$newEntry->name = $data->parsedSlug;
		$newEntry->referenceId = $data->parsedSlug;
		$newEntry->userId = $data->parsedUserId;
		KBatchBase::$kClient->startMultiRequest();
		$addedEntry = KBatchBase::$kClient->baseEntry->add($newEntry, null);
		KBatchBase::$kClient->baseEntry->addContent($addedEntry->id, $resource);
		$result = KBatchBase::$kClient->doMultiRequest();
		
		if ($result [1] && $result[1] instanceof KalturaBaseEntry)
		{
			$entry = $result [1];
			$this->createCategoryAssociations ($dropFolder, $entry->userId, $entry->id);
		}	
	}

	private function isEntryMatch(KalturaDropFolderContentProcessorJobData $data)
	{
		try 
		{
			$entryFilter = new KalturaBaseEntryFilter();
			$entryFilter->referenceIdEqual = $data->parsedSlug;
			$entryFilter->statusIn = KalturaEntryStatus::IMPORT.','.KalturaEntryStatus::PRECONVERT.','.KalturaEntryStatus::READY.','.KalturaEntryStatus::PENDING.','.KalturaEntryStatus::NO_CONTENT;		
			
			$entryPager = new KalturaFilterPager();
			$entryPager->pageSize = 1;
			$entryPager->pageIndex = 1;
			$entryList = KBatchBase::$kClient->baseEntry->listAction($entryFilter, $entryPager);
			
			if (is_array($entryList->objects) && isset($entryList->objects[0]) ) 
			{
				$result = $entryList->objects[0];
				if ($result->referenceId === $data->parsedSlug) 
					return $result;
			}
			
			return false;			
		}
		catch (Exception $e)
		{
			KalturaLog::err('Failed to get entry by reference id: [$data->parsedSlug] - '. $e->getMessage() );
			return false;
		}
	}
	
	/**
	 * Match the current file to an existing entry and flavor according to the slug regex.
	 * Update the matched entry with the new file and all other relevant files from the drop folder, according to the ingestion profile.
	 *
	 */
	private function addAsExistingContent(KalturaBatchJob $job, KalturaDropFolderContentProcessorJobData $data, $matchedEntry = null, KalturaDropFolder $dropFolder)
	{	    
		// check for matching entry and flavor
		if(!$matchedEntry)
		{
			$matchedEntry = $this->isEntryMatch($data);
			if(!$matchedEntry)
			{
				$e = new kTemporaryException('No matching entry found', KalturaDropFolderFileErrorCode::FILE_NO_MATCH);
				if(($job->queueTime + KBatchBase::$taskConfig->params->maxTimeBeforeFail) >= time())	
				{
					$e->setResetJobExecutionAttempts(true);
				}	
				throw $e;		
			}
		}
		
		$resource = $this->getIngestionResource($job, $data);
		
		//If entry user ID differs from the parsed user ID on the job data - update the entry
		KBatchBase::$kClient->startMultiRequest();
		if ($data->parsedUserId != $matchedEntry->userId)
		{
			$updateEntry = new KalturaMediaEntry();
			$updateEntry->userId = $data->parsedUserId;
			KBatchBase::$kClient->baseEntry->update ($matchedEntry->id, $updateEntry);
		}
		KBatchBase::$kClient->media->cancelReplace($matchedEntry->id);
		$updatedEntry = KBatchBase::$kClient->baseEntry->updateContent($matchedEntry->id, $resource, $data->conversionProfileId);
		$result = KBatchBase::$kClient->doMultiRequest();
		
		if ($updatedEntry && $updatedEntry instanceof KalturaBaseEntry)
		{
			$this->createCategoryAssociations ($dropFolder, $updatedEntry->userId, $updatedEntry->id);
		}
	}

}
