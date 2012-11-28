<?php
class kDropFolderXmlEventsConsumer implements kBatchJobStatusEventConsumer, kObjectChangedEventConsumer
{
	const UPLOADED_BY = 'Drop Folder';
	
	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::objectChanged()
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns) 
	{
		$folder = DropFolderPeer::retrieveByPK($object->getDropFolderId());
		$this->onXmlDropFolderFileStatusChangedToPending($folder, $object);
	}

	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::shouldConsumeChangedEvent()
	 */
	public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns) 
	{
		if(	$object instanceof DropFolderFile && $object->getStatus() == DropFolderFileStatus::PENDING && 
			in_array(DropFolderFilePeer::STATUS, $modifiedColumns))
		{
			$folder = DropFolderPeer::retrieveByPK($object->getDropFolderId());
			if($folder->getFileHandlerType() == DropFolderXmlBulkUploadPlugin::getFileHandlerTypeCoreValue(DropFolderXmlFileHandlerType::XML))
				return true;
		} 		
		
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::shouldConsumeJobStatusEvent()
	 */
	public function shouldConsumeJobStatusEvent(BatchJob $dbBatchJob)
	{
		$coreBulkUploadType = kPluginableEnumsManager::apiToCore('BulkUploadType', DropFolderXmlBulkUploadPlugin::getApiValue(DropFolderXmlBulkUploadType::DROP_FOLDER_XML));
		$isMatch =  $dbBatchJob->getJobType() == BatchJobType::BULKUPLOAD && 
					$dbBatchJob->getJobSubType() == $coreBulkUploadType &&
					($dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FINISHED ||
					$dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FINISHED_PARTIALLY ||
					$dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FAILED ||
					$dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FATAL);
		return $isMatch;		
	}
	
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::updatedJob()
	 */
	public function updatedJob(BatchJob $dbBatchJob)
	{
		$this->onBulkUploadJobStatusUpdated($dbBatchJob);
		return true;
	}
				
	private function onBulkUploadJobStatusUpdated(BatchJob $dbBatchJob)
	{
		$xmlDropFolderFile = DropFolderFilePeer::retrieveByPK($dbBatchJob->getObjectId());
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				$xmlDropFolderFile->setStatus(DropFolderFileStatus::HANDLED);
				$xmlDropFolderFile->save();
				break;
			case BatchJob::BATCHJOB_STATUS_FAILED:
			case BatchJob::BATCHJOB_STATUS_FATAL:
				$relatedFiles = DropFolderFilePeer::retrieveByLeadIdAndStatuses($xmlDropFolderFile->getId(), DropFolderFileStatus::PROCESSING);
				foreach ($relatedFiles as $relatedFile) 
				{
					$this->setFileError($relatedFile, DropFolderFileStatus::ERROR_HANDLING, 
										kPluginableEnumsManager::apiToCore('DropFolderFileErrorCode', DropFolderXmlBulkUploadPlugin::getApiValue(DropFolderXmlBulkUploadErrorCode::ERROR_IN_BULK_UPLOAD)),
										'Error while executing BulkUpload job');
				}			
				break;				
		}		
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
	
	private function onXmlDropFolderFileStatusChangedToPending(DropFolder $folder, DropFolderFile $file)
	{
		KalturaLog::debug('in onXmlDropFolderFileStatusChangedToPending file id ['.$file->getId().'] folder id ['.$folder->getId().']');
		
		if($this->isReadyForProcessing($file))
		{
			KalturaLog::debug('All files finished uploading');
			$xmlFile = null;
			if($file->getId() == $file->getLeadDropFolderFileId())
				$xmlFile = $file;
			else
				$xmlFile = DropFolderFilePeer::retrieveByPK($file->getLeadDropFolderFileId());

			$statuses = array(DropFolderFileStatus::PENDING, DropFolderFileStatus::WAITING);
			$relatedFiles = DropFolderFilePeer::retrieveByLeadIdAndStatuses($file->getLeadDropFolderFileId(), $statuses);
			if($this->setFileProcessing($xmlFile, $relatedFiles))
			{
				try 
				{
					$job = $this->addDropFolderXmlBulkUploadJob($folder, $xmlFile);
					KalturaLog::debug('BulkUpload added with job id ['.$job->getId().']');
					$xmlFile->setBatchJobId($job->getId());
					$xmlFile->save();
				}
				catch (Exception $e)
				{
					KalturaLog::err("Error adding BulkUpload job -".$e->getMessage());
					foreach ($relatedFiles as $relatedFile) 
					{
						$this->setFileError($relatedFile, DropFolderFileStatus::ERROR_HANDLING, 
											kPluginableEnumsManager::apiToCore('DropFolderFileErrorCode', DropFolderXmlBulkUploadPlugin::getApiValue(DropFolderXmlBulkUploadErrorCode::ERROR_ADDING_BULK_UPLOAD)), 
											'Failed to add BulkUpload job for drop folder file XML ['. $xmlFile->getId().']');
					}
				}
			}			
		}
		else
		{
			$file->setStatus(DropFolderFileStatus::WAITING);
			$file->save();				
		}
	}
	
	private function isReadyForProcessing(DropFolderFile $file)
	{
		KalturaLog::debug('Check if file ['.$file->getId().'] ready for processing ');
		
		if(!$file->getLeadDropFolderFileId())
		{
			KalturaLog::debug('The XML file is not uploaded yet - changing status to WAITING');
			return false;
		}
		$statuses = array(DropFolderFileStatus::PARSED, DropFolderFileStatus::UPLOADING);
		$nonReadyFiles = DropFolderFilePeer::retrieveByLeadIdAndStatuses($file->getLeadDropFolderFileId(), $statuses);
		
		if($nonReadyFiles && count($nonReadyFiles) > 0)
		{
			KalturaLog::debug('Not all the files finished uploading - changing status to WAITING');
			return false;
		}
		
		return true;
	}

	private function addDropFolderXmlBulkUploadJob(DropFolder $folder, DropFolderFile $leadDropFolderFile)
	{	
		KalturaLog::debug('Adding BulkUpload job');
		
		$coreBulkUploadType = DropFolderXmlBulkUploadPlugin::getBulkUploadTypeCoreValue(DropFolderXmlBulkUploadType::DROP_FOLDER_XML);
				
		$objectId = $leadDropFolderFile->getId();
		$objectType = kPluginableEnumsManager::apiToCore('BatchJobObjectType',DropFolderXmlBulkUploadPlugin::getApiValue(DropFolderBatchJobObjectType::DROP_FOLDER_FILE));
		$partner = PartnerPeer::retrieveByPK($folder->getPartnerId());
		
		$data = KalturaPluginManager::loadObject('kBulkUploadJobData', $coreBulkUploadType);
		//$data->setUserId($partner->getkuser()->getPuserId());
		$data->setUploadedBy(self::UPLOADED_BY);
		$data->setFileName($leadDropFolderFile->getFileName());
					
		$objectData = new kBulkUploadEntryData();
		$objectData->setConversionProfileId($folder->getConversionProfileId());
		$data->setObjectData($objectData);
				
		return kJobsManager::addBulkUploadJob($partner, $data, $coreBulkUploadType, $objectId, $objectType);		
	}
	
}