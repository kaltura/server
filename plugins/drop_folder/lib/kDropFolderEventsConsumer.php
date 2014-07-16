<?php
class kDropFolderEventsConsumer implements kBatchJobStatusEventConsumer, kObjectChangedEventConsumer
{
	const REFERENCE_ID_WILDCARD = 'referenceId';
	const FLAVOR_NAME_WILDCARD  = 'flavorName';
	const USER_ID_WILDCARD = 'userId';
	const DEFAULT_SLUG_REGEX = '/(?P<referenceId>.+)[.]\w{3,}/';
	
	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::objectChanged()
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns) 
	{
		try
		{
			if ( $object instanceof DropFolderFile )
			{
				$folder = DropFolderPeer::retrieveByPK($object->getDropFolderId());
				if($object->getStatus() == DropFolderFileStatus::PENDING)
				{
					$this->onContentDropFolderFileStatusChangedToPending($folder, $object);
				}
			}
			elseif ( $object instanceof entry )
			{
				$this->onEntryStatusChanged($object);
			}
		}
		catch(Exception $e)
		{
			KalturaLog::err('Failed to process objectChangedEvent for drop folder file ['.$object->getDropFolderId().'] - '.$e->getMessage());
		}
		
		return true;
	}

	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::shouldConsumeChangedEvent()
	 */
	public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns) 
	{
		try 
		{
			if(	$object instanceof DropFolderFile && in_array(DropFolderFilePeer::STATUS, $modifiedColumns)) 
			{
				if($object->getStatus() == DropFolderFileStatus::PENDING)
				{
					$folder = DropFolderPeer::retrieveByPK($object->getDropFolderId());
					if($folder->getFileHandlerType() == DropFolderFileHandlerType::CONTENT)
						return true;
				}
			}			
			elseif(	$object instanceof entry
					&& in_array(entryPeer::STATUS, $modifiedColumns)
					&& ($object->getStatus() == entryStatus::READY || $object->getStatus() == entryStatus::ERROR_CONVERTING) )
			{
				return true;
			}
		}
		catch(Exception $e)
		{
			KalturaLog::err('Failed to process shouldConsumeChangedEvent - '.$e->getMessage());
		}
		
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::shouldConsumeJobStatusEvent()
	 */
	public function shouldConsumeJobStatusEvent(BatchJob $dbBatchJob)
	{
		try 
		{
			if($this->isImportMatch($dbBatchJob))
		    	return true;
		    else if($this->isContentProcessorMatch($dbBatchJob))
		    	return true;			
		}
		catch(Exception $e)
		{
			KalturaLog::err('Failed to process shouldConsumeJobStatusEvent - '.$e->getMessage());
		}
		return false;		
	}
	
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::updatedJob()
	 */
	public function updatedJob(BatchJob $dbBatchJob)
	{
		try 
		{
			$contentProcessorBatchJobType = DropFolderPlugin::getCoreValue('BatchJobType', DropFolderBatchType::DROP_FOLDER_CONTENT_PROCESSOR);
			
			if ($dbBatchJob->getJobType() == BatchJobType::IMPORT)
			{
				$this->onImportJobStatusUpdated($dbBatchJob, $dbBatchJob->getData());
			}
			else if($dbBatchJob->getJobType() == $contentProcessorBatchJobType)
			{
				$this->onContentProcessorJobStatusUpdated($dbBatchJob, $dbBatchJob->getData());
			}
		}
		catch(Exception $e)
		{
			KalturaLog::err('Failed to process updatedJob - '.$e->getMessage());
		}
		return true;					
	}
		
	private function isImportMatch(BatchJob $dbBatchJob)
	{	
		$jobStatuses = array(BatchJob::BATCHJOB_STATUS_FINISHED, BatchJob::BATCHJOB_STATUS_FAILED, BatchJob::BATCHJOB_STATUS_FATAL);
		$isMatch =  $dbBatchJob->getJobType() == BatchJobType::IMPORT && 
					$dbBatchJob->getData() instanceof kDropFolderImportJobData &&
					in_array($dbBatchJob->getStatus(), $jobStatuses);
		return $isMatch;
	}
	
	private function isContentProcessorMatch(BatchJob $dbBatchJob)
	{	
		$batchJobType = DropFolderPlugin::getCoreValue('BatchJobType', DropFolderBatchType::DROP_FOLDER_CONTENT_PROCESSOR);
		$jobStatuses = array(BatchJob::BATCHJOB_STATUS_FAILED, BatchJob::BATCHJOB_STATUS_RETRY, BatchJob::BATCHJOB_STATUS_FATAL);
		$isMatch =  $dbBatchJob->getJobType() == $batchJobType && in_array($dbBatchJob->getStatus(), $jobStatuses);
		return $isMatch;
	}
	
	private function onImportJobStatusUpdated(BatchJob $dbBatchJob, kDropFolderImportJobData $data)
	{
		$dropFolderFile = DropFolderFilePeer::retrieveByPK($data->getDropFolderFileId());
		if(!$dropFolderFile)
			return;
			
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				$newStatus = DropFolderFileStatus::HANDLED;

				$dropFolder = DropFolderPeer::retrieveByPK($dropFolderFile->getDropFolderId());
				if ( $dropFolder
						&& $dropFolder->getFileDeletePolicy() == DropFolderFileDeletePolicy::AUTO_DELETE_WHEN_ENTRY_IS_READY )
				{
					// Shift the state to PROCESSING until the associated entry will reach the READY (or ERROR_CONVERTING) state
					KalturaLog::info("Shifting drop folder file id [{$dropFolderFile->getId()}] from status [{$dropFolderFile->getStatus()}] to PROCESSING due to AUTO_DELETE_WHEN_ENTRY_IS_READY policy"); 
					$newStatus = DropFolderFileStatus::PROCESSING;
				}

				$dropFolderFile->setStatus($newStatus);
				$dropFolderFile->save();
				break;
			case BatchJob::BATCHJOB_STATUS_FAILED:
			case BatchJob::BATCHJOB_STATUS_FATAL:
				$this->setFileError($dropFolderFile, DropFolderFileStatus::ERROR_DOWNLOADING, DropFolderFileErrorCode::ERROR_DOWNLOADING_FILE, DropFolderPlugin::ERROR_DOWNLOADING_FILE_MESSAGE);
				break;				
		}
	}

	/**
	 * Update all the drop folder files processed by the batch job according to batch job status:
	 * 1. Retry - set files status to NO_MATCH
	 * 2. Failure - set to ERROR_HANDLING
	 * @param BatchJob $dbBatchJob
	 * @param kDropFolderContentProcessorJobData $data
	 */
	private function onContentProcessorJobStatusUpdated(BatchJob $dbBatchJob, kDropFolderContentProcessorJobData $data)
	{
		$idsArray = explode(',', $data->getDropFolderFileIds());
		$dropFolderFiles = DropFolderFilePeer::retrieveByPKs($idsArray);
		if(!$dropFolderFiles)
			return;
			
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_RETRY: //TODO: check  error code
				KalturaLog::debug('Batch job status RETRY => set files ['.$data->getDropFolderFileIds().'] to NO_MATCH');
				foreach ($dropFolderFiles as $dropFolderFile) 
				{
					$dropFolderFile->setStatus(DropFolderFileStatus::NO_MATCH);
					$dropFolderFile->save();
				}			
				break;
			case BatchJob::BATCHJOB_STATUS_FAILED:
			case BatchJob::BATCHJOB_STATUS_FATAL:	
				KalturaLog::debug('Batch job FAILED => set files ['.$data->getDropFolderFileIds().'] to ERROR_HANDLING');			
				foreach ($dropFolderFiles as $dropFolderFile) 
				{
					$this->setFileError($dropFolderFile, DropFolderFileStatus::ERROR_HANDLING, DropFolderFileErrorCode::ERROR_IN_CONTENT_PROCESSOR, DropFolderPlugin::ERROR_IN_CONTENT_PROCESSOR_MESSAGE);
				}			
				break;				
		}				
	}

	/**
	 * Handles drop folder file status change to PENDING
	 * 1. set parsed slug and flavor
	 * 2. in case there is no parsed flavor (no related files) - add ContentProcessor job
	 * 3. otherwise
	 * 	a. verify all required flavors are ready 
	 *	b. if yes - add content processor 
	 *	c. else - change files status to WAITING
	 * @param DropFolder $folder
	 * @param DropFolderFile $file
	 */
	private function onContentDropFolderFileStatusChangedToPending(DropFolder $folder, DropFolderFile $file)
	{
		KalturaLog::debug('start onContentDropFolderFileStatusChangedToPending ['.$file->getId().']');
		$updatedFile = $this->setParsedSlugFlavor($folder, $file);
		if($updatedFile)
		{
			$file = $updatedFile;
			if(is_null($file->getParsedFlavor()))
			{
				KalturaLog::debug('Parsed flavor is null, triggering ContentProcessing job for source');
				$this->triggerContentDropFolderFileProcessing($folder, $file);
			}
			else
			{
				$assetParamsList = flavorParamsConversionProfilePeer::retrieveByConversionProfile($folder->getConversionProfileId());
				$flavorNameValid = $this->validateFlavorName($file, $assetParamsList);
				
				if($flavorNameValid)
				{
					KalturaLog::debug('Parsed flavor is set, verifying if all files ready');
					$statuses = array(DropFolderFileStatus::PENDING, DropFolderFileStatus::WAITING, DropFolderFileStatus::NO_MATCH);					
					$relatedFiles = DropFolderFilePeer::retrieveByDropFolderIdStatusesAndSlug($folder->getId(), $statuses, $file->getParsedSlug());				
					$isReady = $this->isAllContentDropFolderIngestedFilesReady($folder, $relatedFiles, $assetParamsList);
					if ($isReady) 
					{				
						$this->triggerContentDropFolderFileProcessing($folder, $file, $relatedFiles);
					}
					else
					{
						KalturaLog::debug('Some required flavors do not exist in the drop folder - changing status to WAITING');
						$file->setStatus(DropFolderFileStatus::WAITING);
						$file->save();
					}	
				}			
			}			
		}
		else 
		{
			$this->setFileError($file, DropFolderFileStatus::ERROR_HANDLING, DropFolderFileErrorCode::SLUG_REGEX_NO_MATCH, DropFolderPlugin::SLUG_REGEX_NO_MATCH_MESSAGE); 
 		}
 		KalturaLog::debug('end onContentDropFolderFileStatusChangedToPending');
	}
	
	private function validateFlavorName(DropFolderFile $file, $assetParamsList)
	{
		foreach ($assetParamsList as $assetParams)
		{
			if($assetParams->getSystemName() == $file->getParsedFlavor())
				return true;
		}
		KalturaLog::debug('Flavor name not found ['.$file->getParsedFlavor().']');
		$this->setFileError($file, DropFolderFileStatus::ERROR_HANDLING, DropFolderFileErrorCode::FLAVOR_NOT_FOUND, DropFolderPlugin::FLAVOR_NOT_FOUND_MESSAGE);
		
		return false;
	}

	/**
	 * Convert all drop folder files' status from PROCESSING to HANDLED/DELETED in case of DropFolderFileDeletePolicy::AUTO_DELETE_WHEN_ENTRY_IS_READY
	 * 
	 * Note that if the entry reached entryStatus::ERROR_CONVERTING, then the drop folder files' 
	 * conversions already failed, so there's no need to change their status (thus they won't be handled here).  
	 *  
	 * @param entry $entry
	 */
	private function onEntryStatusChanged( $entry )
	{
		$dropFolderFiles = DropFolderFilePeer::retrieveByEntryAndPartnerIds($entry->getId(), $entry->getPartnerId());
		$dropFolderIdToDropFolderCache = array();

		$entryStatus = $entry->getStatus();

		foreach ( $dropFolderFiles as $dropFolderFile )
		{
			// Handle only files that are still in the PROCESSING state, which were left
			// in this state due to AUTO_DELETE_WHEN_ENTRY_IS_READY delete policy.
			if ( $dropFolderFile->getStatus() == DropFolderFileStatus::PROCESSING )
			{
				$newDropFolderFileStatus = null;
				
				if ( $entryStatus == entryStatus::ERROR_CONVERTING )
				{
					$newDropFolderFileStatus = DropFolderFileStatus::ERROR_HANDLING;
				}
				elseif ( $entryStatus == entryStatus::READY )
				{
					// Get the associated drop folder
					$dropFolderId = $dropFolderFile->getDropFolderId();
					if ( key_exists( $dropFolderId, $dropFolderIdToDropFolderCache ) )
					{
						$dropFolder = $dropFolderIdToDropFolderCache[ $dropFolderId ];
					}
					else
					{
						$dropFolder = DropFolderPeer::retrieveByPK($dropFolderId);
						$dropFolderIdToDropFolderCache[ $dropFolderId ] = $dropFolder;
					}
					
					if ( $dropFolder->getFileDeletePolicy() == DropFolderFileDeletePolicy::AUTO_DELETE_WHEN_ENTRY_IS_READY )
					{
						if ( $dropFolder->getAutoFileDeleteDays() == 0 )
						{
							$newDropFolderFileStatus = DropFolderFileStatus::DELETED; // Mark for immediate deletion
						}
						else
						{
							$newDropFolderFileStatus = DropFolderFileStatus::HANDLED;
						}
					}
				}

				KalturaLog::info("Entry id [{$entry->getId()}] status [{$entryStatus}], drop folder file id [{$dropFolderFile->getId()}] status [{$dropFolderFile->getStatus()}] => ["
									. ($newDropFolderFileStatus ? $newDropFolderFileStatus : "{$dropFolderFile->getStatus()} (unchanged)") . "]");
				
				if ( $newDropFolderFileStatus )
				{
					$dropFolderFile->setStatus( $newDropFolderFileStatus );
					$dropFolderFile->save();
				}				
			}
		}
	}

	/**
	 * Check if all required files for the given ingestion profile are in the drop folder.
	 */
	private function isAllContentDropFolderIngestedFilesReady(DropFolder $folder, $relatedFiles, $assetParamsList)
	{
		KalturaLog::debug('Ingest files according to conversion profile ['.$folder->getConversionProfileId().']');
				
		$existingFlavors = array();		
		foreach ($relatedFiles as $relatedFile)
		{
			KalturaLog::debug('flavor ['.$relatedFile->getParsedFlavor().'] file id ['.$relatedFile->getId().']');
			$existingFlavors[$relatedFile->getParsedFlavor()] = $relatedFile->getId();
		}
		
		foreach ($assetParamsList as $assetParams)
		{
			if($assetParams->getReadyBehavior() == flavorParamsConversionProfile::READY_BEHAVIOR_REQUIRED && $assetParams->getOrigin() == assetParamsOrigin::INGEST)
			{
				if(!array_key_exists($assetParams->getSystemName(), $existingFlavors))
				{
					KalturaLog::debug('Flavor ['.$assetParams->getSystemName().'] is required and must be ingested');
					return false;
				}			
			}			
		}		
		return true;
	}

	/**
	 * Change drop folder file status to PROCESSING using atomic update
	 * Id status was updated in the database by the current call add ContentProcessor job
	 * @param DropFolder $folder
	 * @param DropFolderFile $file
	 * @param array $relatedFiles
	 */
	private function triggerContentDropFolderFileProcessing(DropFolder $folder, DropFolderFile $file, $relatedFiles = null)
	{
		KalturaLog::debug('in triggerContentDropFolderFileProcessing');
		if($relatedFiles)
		{
			//looking for drop folder file with minimal id to set status to PROCESSING
			$fileWithMinId = null;
			foreach ($relatedFiles as $relatedFile) 
			{
				if(!$fileWithMinId || ($relatedFile->getId() < $fileWithMinId->getId()))
					$fileWithMinId = $relatedFile;
			}
			$dropFolderFileIds = '';
			foreach ($relatedFiles as $relatedFile) 
			{
				$dropFolderFileIds = $dropFolderFileIds.$relatedFile->getId().',';
			}		 
		}
		else //source only
		{
			$fileWithMinId = $file;
			$relatedFiles = array($file);
			$dropFolderFileIds = $file->getId();
		}
		if($this->setFileProcessing($fileWithMinId, $relatedFiles))
		{
			//If atomic status update succeeded add DropFolderContentProcessor job,
			//otherwise the job was added by another event
			try 
			{
				$job = $this->addDropFolderContentProcessorJob($folder, $file, $dropFolderFileIds);
				KalturaLog::debug('DropFolderContent processor job id ['.$job->getId().']');
				foreach ($relatedFiles as $relatedFile) 
				{
					$relatedFile->setBatchJobId($job->getId());
					$relatedFile->save();
				}
			}
			catch (Exception $e)
			{
				KalturaLog::err('Error when adding DropFolderContentProcessor job - '.$e->getMessage());
				foreach ($relatedFiles as $relatedFile) {
					$this->setFileError($relatedFile, DropFolderFileStatus::ERROR_HANDLING, DropFolderFileErrorCode::ERROR_ADDING_CONTENT_PROCESSOR, 
								'Failed to add DropFolderContentProcessor job');
				}
			}										
		}	
	}			
	
	private function addDropFolderContentProcessorJob(DropFolder $folder, DropFolderFile $dropFolderFileForObject, $dropFolderFileIds)
	{	
		KalturaLog::debug('adding  DropFolderContentProcessor job');		
 		$batchJobType = DropFolderPlugin::getCoreValue('BatchJobType', DropFolderBatchType::DROP_FOLDER_CONTENT_PROCESSOR);
 		
		$batchJob = new BatchJob();
		$batchJob->setPartnerId($folder->getPartnerId());			
		$batchJob->setObjectId($dropFolderFileForObject->getId());
		$batchJob->setObjectType(DropFolderPlugin::getCoreValue('BatchJobObjectType',DropFolderBatchJobObjectType::DROP_FOLDER_FILE));
		
		$jobData = kDropFolderContentProcessorJobData::getInstance($folder->getType());
		//Required for plugins which require data to be set on the created entry from the drop folder files.
		$jobData->setData($folder, $dropFolderFileForObject, $dropFolderFileIds) ;		
		
		KalturaLog::debug('created job data: ' . print_r($jobData, true));
		
		return kJobsManager::addJob($batchJob, $jobData, $batchJobType, $folder->getType());		
	}
	
	private function setFileProcessing(DropFolderFile $file, array $relatedFiles)
	{
		$file->setStatus(DropFolderFileStatus::PROCESSING);
		$affectedRows = $file->save();
		KalturaLog::debug('Changing file status to Processing, file id ['.$file->getId().'] affected rows ['.$affectedRows.']');
		if($affectedRows > 0)
		{
			foreach ($relatedFiles as $relatedFile) 
			{
				if($relatedFile->getId() != $file->getId())
				{
					KalturaLog::debug('Changing file status to Processing, file id ['.$relatedFile->getId().']');
					$relatedFile->setStatus(DropFolderFileStatus::PROCESSING);
					$relatedFile->save();
				}
			}
		}
		return $affectedRows;
	}
	
	private function setFileError(DropFolderFile $file, $status, $errorCode, $errorDescription)
	{
		KalturaLog::err('Error with file ['.$file->getId().'] -'.$errorDescription);
		
		$file->setStatus($status);
		$file->setErrorCode($errorCode);
		$file->setErrorDescription($errorDescription);
		$file->save();				
		
	}	

	private function setParsedSlugFlavor(DropFolder $folder, DropFolderFile $file)
	{
		$parsedSlug = null;
		$parsedFlavor = null;
		$parsedUserId = null;
		$isMatch = $this->parseRegex($folder->getFileHandlerConfig(), $file->getNameForParsing(), $parsedSlug, $parsedFlavor, $parsedUserId);

 		if($isMatch)
 		{
 			$file->setParsedSlug($parsedSlug);
 			$file->setParsedFlavor($parsedFlavor);	
			$file->setParsedUserId($parsedUserId);
 			$file->save();	
 			return $file;		
  		}    		
		else 		
		{
			return null;
		} 			
	}
	
	/**
	 * Parse file name according to defined slugRegex and set the extracted parsedSlug and parsedFlavor.
	 * The following expressions are currently recognized and used:
	 * 	- (?P<referenceId>\w+) - will be used as the drop folder file's parsed slug.
	 *  - (?P<flavorName>\w+)  - will be used as the drop folder file's parsed flavor. 
	 *  - (?P<userId>\[\w\@\.]+) - will be used as the drop folder file entry's parsed user id.
	 * @return bool true if file name matches the slugRegex or false otherwise
	 */
	private function parseRegex(DropFolderContentFileHandlerConfig $fileHandlerConfig, $fileName, &$parsedSlug, &$parsedFlavor, &$parsedUserId)
	{
		$matches = null;
		$slugRegex = $fileHandlerConfig->getSlugRegex();
		if(is_null($slugRegex) || empty($slugRegex))
		{
			$slugRegex = self::DEFAULT_SLUG_REGEX;
		}
		$matchFound = preg_match($slugRegex, $fileName, $matches);
		KalturaLog::debug('slug regex: ' . $slugRegex . ' file name:' . $fileName);
		if ($matchFound) 
		{
			$parsedSlug   = isset($matches[self::REFERENCE_ID_WILDCARD]) ? $matches[self::REFERENCE_ID_WILDCARD] : null;
			$parsedFlavor = isset($matches[self::FLAVOR_NAME_WILDCARD])  ? $matches[self::FLAVOR_NAME_WILDCARD]  : null;
			$parsedUserId = isset($matches[self::USER_ID_WILDCARD])  ? $matches[self::USER_ID_WILDCARD]  : null;
			KalturaLog::debug('Parsed slug ['.$parsedSlug.'], Parsed flavor ['.$parsedFlavor.'], parsed user id ['. $parsedUserId .']');
		}
		if(!$parsedSlug)
			$matchFound = false;
		return $matchFound;
	}
}