<?php
class kDropFolderICalEventsConsumer implements kBatchJobStatusEventConsumer, kObjectChangedEventConsumer
{
	const UPLOADED_BY = 'Drop Folder';
	
	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::objectChanged()
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns) 
	{
		$this->onDropFolderFilePending($object);
		
		return true;
	}

	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::shouldConsumeChangedEvent()
	 */
	public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns) 
	{
		if(	$object instanceof DropFolderFile && 
			$object->getStatus() == DropFolderFileStatus::PENDING && 
			in_array(DropFolderFilePeer::STATUS, $modifiedColumns))
		{
			$folder = DropFolderPeer::retrieveByPK($object->getDropFolderId());
			if(!$folder)
			{
				KalturaLog::err('Failed to process ChangedEvent - Failed to retrieve drop-folder [' . $object->getDropFolderId() . ']');
				return false;
			}
			
			if($folder->getFileHandlerType() == DropFolderSchedulePlugin::getFileHandlerTypeCoreValue(DropFolderFileHandlerScheduleType::ICAL))
				return true;
		}
		
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::shouldConsumeJobStatusEvent()
	 */
	public function shouldConsumeJobStatusEvent(BatchJob $dbBatchJob)
	{
		$jobObjectType = DropFolderPlugin::getBatchJobObjectTypeCoreValue(DropFolderBatchJobObjectType::DROP_FOLDER_FILE);
		$bulkUploadObjectType = BulkUploadSchedulePlugin::getBulkUploadObjectTypeCoreValue(BulkUploadObjectScheduleType::SCHEDULE_EVENT);
		
		$jobStatuses = array(
			BatchJob::BATCHJOB_STATUS_FINISHED, 
			BatchJob::BATCHJOB_STATUS_FINISHED_PARTIALLY, 
			BatchJob::BATCHJOB_STATUS_FAILED, 
			BatchJob::BATCHJOB_STATUS_FATAL, 
			BatchJob::BATCHJOB_STATUS_QUEUED,
		);
		
		if($dbBatchJob->getJobType() == BatchJobType::BULKUPLOAD && 
					$dbBatchJob->getObjectType() == $jobObjectType &&
					in_array($dbBatchJob->getStatus(), $jobStatuses))
		{
			$data = $dbBatchJob->getData();
			if($data instanceof kBulkUploadJobData && $data->getBulkUploadObjectType() == $bulkUploadObjectType)
				return true;
		}
		
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::updatedJob()
	 */
	public function updatedJob(BatchJob $dbBatchJob)
	{
		$dropFolderFile = DropFolderFilePeer::retrieveByPK($dbBatchJob->getObjectId());
		if(!$dropFolderFile)
			return true;
		
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_QUEUED:
				$jobData = $dbBatchJob->getData();
				if(!is_null($jobData->getFilePath()))
					break;
				
				$dropFolder = DropFolderPeer::retrieveByPK($dropFolderFile->getDropFolderId());
				if(!$dropFolder || $dropFolder->getType() != DropFolderType::LOCAL)
					break;
					
				$filePath = $dropFolder->getPath() . '/' . $dropFolderFile->getFileName();
				$syncKey = $dbBatchJob->getSyncKey(BatchJob::FILE_SYNC_BATCHJOB_SUB_TYPE_BULKUPLOAD);
				try{
					kFileSyncUtils::moveFromFile($filePath, $syncKey, true, true);
				}
				catch(Exception $e)
				{
					KalturaLog::err($e);
					throw new APIException(APIErrors::BULK_UPLOAD_CREATE_CSV_FILE_SYNC_ERROR);
				}
				
				$filePath = kFileSyncUtils::getLocalFilePathForKey($syncKey);
				
				$jobData->setFilePath($filePath);
				
				//save new info on the batch job
				$dbBatchJob->setData($jobData);
				$dbBatchJob->save();
				break;
				
			case BatchJob::BATCHJOB_STATUS_FINISHED:
			case BatchJob::BATCHJOB_STATUS_FINISHED_PARTIALLY:
				$dropFolderFile->setStatus(DropFolderFileStatus::HANDLED);
				$dropFolderFile->save();
				break;
				
			case BatchJob::BATCHJOB_STATUS_FAILED:
			case BatchJob::BATCHJOB_STATUS_FATAL:
				$dropFolderFile->setStatus(DropFolderFileStatus::ERROR_HANDLING);
				$dropFolderFile->setErrorCode($dbBatchJob->getErrNumber());
				$dropFolderFile->setErrorDescription('Failed  to execute the bulk upload job in Kaltura');
				$dropFolderFile->save();				
				break;				
		}		
		
		return true;
	}
			
	private function setFileError(DropFolderFile $file, $status, $errorCode, $errorDescription)
	{
		KalturaLog::err('Error with file ['.$file->getId().'] -'.$errorDescription);
		
		$file->setStatus($status);
		$file->setErrorCode($errorCode);
		$file->setErrorDescription($errorDescription);
		$file->save();				
	}

	/**
	 * Add BulkUpload job
	 * @param DropFolderFile $file
	 */
	private function onDropFolderFilePending(DropFolderFile $file)
	{
		$folder = DropFolderPeer::retrieveByPK($file->getDropFolderId());
		if(!$folder)
			return;
			
		$file->setStatus(DropFolderFileStatus::PROCESSING);
		$affectedRows = $file->save();
		if(!$affectedRows)
			return;

		$fileHandlerConfig = $folder->getFileHandlerConfig();
		/* @var $fileHandlerConfig DropFolderICalBulkUploadFileHandlerConfig */
		
		$objectType = DropFolderPlugin::getBatchJobObjectTypeCoreValue(DropFolderBatchJobObjectType::DROP_FOLDER_FILE);
		$coreBulkUploadType = DropFolderSchedulePlugin::getBulkUploadTypeCoreValue(DropFolderScheduleType::DROP_FOLDER_ICAL);
		$bulkUploadObjectType = BulkUploadSchedulePlugin::getBulkUploadObjectTypeCoreValue(BulkUploadObjectScheduleType::SCHEDULE_EVENT);
				
		$objectId = $file->getId();
		$partner = PartnerPeer::retrieveByPK($file->getPartnerId());
		
		$data = KalturaPluginManager::loadObject('kBulkUploadJobData', $coreBulkUploadType);
		/* @var $data kBulkUploadICalJobData */
		$data->setUploadedBy(kDropFolderXmlEventsConsumer::UPLOADED_BY);
		$data->setFileName($file->getFileName());
		$data->setBulkUploadObjectType($bulkUploadObjectType);
		$data->setEventsType($fileHandlerConfig->getEventsType());
					
		$job = kJobsManager::addBulkUploadJob($partner, $data, $coreBulkUploadType, $objectId, $objectType);

		$file->setBatchJobId($job->getId());
		$file->save();
	}
	

}
