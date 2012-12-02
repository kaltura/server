<?php
class kDropFolderEventsConsumer implements kBatchJobStatusEventConsumer, kObjectChangedEventConsumer
{
	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::objectChanged()
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns) 
	{
		try 
		{
			$folder = DropFolderPeer::retrieveByPK($object->getDropFolderId());
			if($object->getStatus() == DropFolderFileStatus::PENDING)
			{
				$this->onContentDropFolderFileStatusChangedToPending($folder, $object);
			}
		}
		catch(Exception $e)
		{
			KalturaLog::err('Failed to process objectChangedEvent for drop folder file ['.$object->getDropFolderId().'] - '.$e->getMessage());
		}
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
			return true;
		}
		catch(Exception $e)
		{
			KalturaLog::err('Failed to process updatedJob - '.$e->getMessage());
		}
		return false;					
	}
		
	private function isImportMatch(BatchJob $dbBatchJob)
	{	
		$isMatch =  $dbBatchJob->getJobType() == BatchJobType::IMPORT && 
					$dbBatchJob->getData() instanceof kDropFolderImportJobData &&
					($dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FINISHED ||
					$dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FAILED ||
					$dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FATAL);
		return $isMatch;
	}
	
	private function isContentProcessorMatch(BatchJob $dbBatchJob)
	{	
		$batchJobType = DropFolderPlugin::getCoreValue('BatchJobType', DropFolderBatchType::DROP_FOLDER_CONTENT_PROCESSOR);
		$isMatch =  $dbBatchJob->getJobType() == $batchJobType && 
					($dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FAILED ||
					$dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_RETRY ||
					$dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FATAL);
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
				$dropFolderFile->setStatus(DropFolderFileStatus::HANDLED);
				$dropFolderFile->save();
				break;
			case BatchJob::BATCHJOB_STATUS_FAILED:
			case BatchJob::BATCHJOB_STATUS_FATAL:
				$this->setFileError($dropFolderFile, DropFolderFileStatus::ERROR_DOWNLOADING, DropFolderFileErrorCode::ERROR_DOWNLOADING_FILE, 'Error while downloading file');
				break;				
		}
	}
		
	private function onContentProcessorJobStatusUpdated(BatchJob $dbBatchJob, kDropFolderContentProcessorJobData $data)
	{
		$idsArray = explode(',', $data->getDropFolderFileIds());
		$dropFolderFiles = DropFolderFilePeer::retrieveByPKs($idsArray);
		if(!$dropFolderFiles)
			return;
			
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_RETRY:
				foreach ($dropFolderFiles as $dropFolderFile) 
				{
					$dropFolderFile->setStatus(DropFolderFileStatus::NO_MATCH);
					$dropFolderFile->save();
				}			
				break;
			case BatchJob::BATCHJOB_STATUS_FAILED:
			case BatchJob::BATCHJOB_STATUS_FATAL:				
				foreach ($dropFolderFiles as $dropFolderFile) 
				{
					$this->setFileError($dropFolderFile, DropFolderFileStatus::ERROR_HANDLING, DropFolderFileErrorCode::ERROR_IN_CONTENT_PROCESSOR, 'Error while executing DropFolderContentProcessor job');
				}			
				break;				
		}				
	}
			
	private function onContentDropFolderFileStatusChangedToPending(DropFolder $folder, DropFolderFile $file)
	{
		if(is_null($file->getParsedFlavor()))
		{
			$this->triggerContentDropFolderFileProcessing($folder, $file);
		}
		else
		{
			$statuses = array(DropFolderFileStatus::PENDING, DropFolderFileStatus::WAITING, DropFolderFileStatus::NO_MATCH);					
			$relatedFiles = DropFolderFilePeer::retrieveByDropFolderIdStatusesAndSlug($folder->getId(), $statuses, $file->getParsedSlug());				
			$isReady = $this->isAllContentDropFolderIngestedFilesReady($folder, $relatedFiles);
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
	
	/**
	 * Check if all required files for the given ingestion profile are in the drop folder.
	 */
	private function isAllContentDropFolderIngestedFilesReady(DropFolder $folder, $relatedFiles)
	{
		KalturaLog::debug('Ingest files according to conversion profile ['.$folder->getConversionProfileId().']');
				
		$existingFlavors = array();
		foreach ($relatedFiles as $relatedFile)
		{
			KalturaLog::debug('flavor ['.$relatedFile->getParsedFlavor().'] file id ['.$relatedFile->getId().']');
			$existingFlavors[$relatedFile->getParsedFlavor()] = $relatedFile->getId();
		}
		
		$assetParamsList = flavorParamsConversionProfilePeer::retrieveByConversionProfile($folder->getConversionProfileId());
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
	
	private function triggerContentDropFolderFileProcessing(DropFolder $folder, DropFolderFile $file, $relatedFiles = null)
	{
		if($relatedFiles)
		{
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
			try 
			{
				$job = $this->addDropFolderContentProcessorJob($folder, $file, $dropFolderFileIds);
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
 		$batchJobType = DropFolderPlugin::getCoreValue('BatchJobType', DropFolderBatchType::DROP_FOLDER_CONTENT_PROCESSOR);
 		
		$batchJob = new BatchJob();
		$batchJob->setPartnerId($folder->getPartnerId());			
		$batchJob->setObjectId($dropFolderFileForObject->getId());
		$batchJob->setObjectType(DropFolderPlugin::getCoreValue('BatchJobObjectType',DropFolderBatchJobObjectType::DROP_FOLDER_FILE));
		
		$jobData = new kDropFolderContentProcessorJobData();
		$jobData->setConversionProfileId($folder->getConversionProfileId());
		$jobData->setParsedSlug($dropFolderFileForObject->getParsedSlug());
		$jobData->setContentMatchPolicy($folder->getFileHandlerConfig()->getContentMatchPolicy());
		$jobData->setDropFolderFileIds($dropFolderFileIds);
		
		return kJobsManager::addJob($batchJob, $jobData, $batchJobType);		
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
}