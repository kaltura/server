<?php

/**
 * 
 * Manages the batch flow
 * 
 * @package Core
 * @subpackage Batch
 *
 */
class kFlowManager implements kBatchJobStatusEventConsumer, kObjectAddedEventConsumer, kObjectChangedEventConsumer, kObjectDeletedEventConsumer
{
	public final function __construct()
	{ 
	} 
		
	protected function updatedImport(BatchJob $dbBatchJob, kImportJobData $data, BatchJob $twinJob = null)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return kFlowHelper::handleImportFinished($dbBatchJob, $data, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FAILED:
			case BatchJob::BATCHJOB_STATUS_FATAL:
				kBatchManager::updateEntry($dbBatchJob->getEntryId(), entryStatus::ERROR_IMPORTING);
				return $dbBatchJob;
			default:
				return $dbBatchJob;
		}
	}
	
	protected function updatedExtractMedia(BatchJob $dbBatchJob, kExtractMediaJobData $data, BatchJob $twinJob = null)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_FINISHED:
			case BatchJob::BATCHJOB_STATUS_FAILED:
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return kFlowHelper::handleExtractMediaClosed($dbBatchJob, $data, $twinJob);
			default:
				return $dbBatchJob;
		}
	}
	
	protected function updatedStorageExport(BatchJob $dbBatchJob, kStorageExportJobData $data, BatchJob $twinJob = null)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return kFlowHelper::handleStorageExportFinished($dbBatchJob, $data);
			case BatchJob::BATCHJOB_STATUS_FAILED:
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return kFlowHelper::handleStorageExportFailed($dbBatchJob, $data);
			default:
				return $dbBatchJob;
		}
	}
	
	protected function updatedStorageDelete(BatchJob $dbBatchJob, kStorageDeleteJobData $data, BatchJob $twinJob = null)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return kFlowHelper::handleStorageDeleteFinished($dbBatchJob, $data);
			default:
				return $dbBatchJob;
		}
	}
	
	protected function updatedCaptureThumb(BatchJob $dbBatchJob, kCaptureThumbJobData $data, BatchJob $twinJob = null)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return kFlowHelper::handleCaptureThumbFinished($dbBatchJob, $data, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FAILED:
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return kFlowHelper::handleCaptureThumbFailed($dbBatchJob, $data, $twinJob);
			default:
				return $dbBatchJob;
		}
	}
	
	protected function updatedConvert(BatchJob $dbBatchJob, kConvertJobData $data, BatchJob $twinJob = null)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_PENDING:
				return kFlowHelper::handleConvertPending($dbBatchJob, $data, $twinJob);
			case BatchJob::BATCHJOB_STATUS_QUEUED:
				return kFlowHelper::handleConvertQueued($dbBatchJob, $data, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return kFlowHelper::handleConvertFinished($dbBatchJob, $data, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FAILED:
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return kFlowHelper::handleConvertFailed($dbBatchJob, $data, $twinJob);
			default:
				return $dbBatchJob;
		}
	}
	
	protected function updatedPostConvert(BatchJob $dbBatchJob, kPostConvertJobData $data, BatchJob $twinJob = null)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return kFlowHelper::handlePostConvertFinished($dbBatchJob, $data, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FAILED:
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return kFlowHelper::handlePostConvertFailed($dbBatchJob, $data, $twinJob);
			default:
				return $dbBatchJob;
		}
	}
	
	protected function updatedBulkUpload(BatchJob $dbBatchJob, kBulkUploadJobData $data, BatchJob $twinJob = null)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_PENDING:
				return kFlowHelper::handleBulkUploadPending($dbBatchJob, $data, $twinJob);
			default:
				return $dbBatchJob;
		}
	}
	
	protected function updatedConvertCollection(BatchJob $dbBatchJob, kConvertCollectionJobData $data, BatchJob $twinJob = null)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_PENDING:
				return kFlowHelper::handleConvertCollectionPending($dbBatchJob, $data, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return kFlowHelper::handleConvertCollectionFinished($dbBatchJob, $data, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FAILED:
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return kFlowHelper::handleConvertCollectionFailed($dbBatchJob, $data, $twinJob);
			default:
				return $dbBatchJob;
		}
	}
	
	protected function updatedConvertProfile(BatchJob $dbBatchJob, kConvertProfileJobData $data, BatchJob $twinJob = null)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_PENDING:
				return kFlowHelper::handleConvertProfilePending($dbBatchJob, $data, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return kFlowHelper::handleConvertProfileFinished($dbBatchJob, $data, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FAILED:
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return kFlowHelper::handleConvertProfileFailed($dbBatchJob, $data, $twinJob);
			default:
				return $dbBatchJob;
		}
	}
	
	protected function updatedBulkDownload(BatchJob $dbBatchJob, kBulkDownloadJobData $data, BatchJob $twinJob = null)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_PENDING:
				return kFlowHelper::handleBulkDownloadPending($dbBatchJob, $data, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return kFlowHelper::handleBulkDownloadFinished($dbBatchJob, $data, $twinJob);
			default:
				return $dbBatchJob;
		}
	}
		
	protected function updatedProvisionDelete(BatchJob $dbBatchJob, kProvisionJobData $data, BatchJob $twinJob = null)
	{
		return $dbBatchJob;
	}
	
	protected function updatedProvisionProvide(BatchJob $dbBatchJob, kProvisionJobData $data, BatchJob $twinJob = null)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return kFlowHelper::handleProvisionProvideFinished($dbBatchJob, $data, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FAILED:
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return kFlowHelper::handleProvisionProvideFailed($dbBatchJob, $data, $twinJob);
			default:
				return $dbBatchJob;
		}
	}
	
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::shouldConsumeJobStatusEvent()
	 */
	public function shouldConsumeJobStatusEvent(BatchJob $dbBatchJob)
	{
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::updatedJob()
	 */
	public function updatedJob(BatchJob $dbBatchJob, BatchJob $twinJob = null)
	{
		try
		{
			$jobType = $dbBatchJob->getJobType();
			
			if(is_null($dbBatchJob->getQueueTime()) && $dbBatchJob->getStatus() != BatchJob::BATCHJOB_STATUS_PENDING && $dbBatchJob->getStatus() != BatchJob::BATCHJOB_STATUS_RETRY)
			{
				$dbBatchJob->setQueueTime(time());
				$dbBatchJob->save();
			}
			
			if($dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FINISHED)
			{
				$dbBatchJob->setFinishTime(time());
				$dbBatchJob->save();
			}
			
			if($dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_RETRY)
			{
				$dbBatchJob->setCheckAgainTimeout(time() + BatchJobPeer::getCheckAgainTimeout($jobType));
				$dbBatchJob->setQueueTime(null);
				$dbBatchJob->save();
			}
			
			if($dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_ALMOST_DONE)
			{
				$dbBatchJob->setCheckAgainTimeout(time() + BatchJobPeer::getCheckAgainTimeout($jobType));
				$dbBatchJob->save();
			}
			
			if($dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FAILED || $dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FATAL)
			{
				$dbBatchJob->setFinishTime(time());
				$dbBatchJob->save();
				
				// TODO - don't abort if it's bulk upload
				kJobsManager::abortChildJobs($dbBatchJob);
			}
			
			switch($jobType)
			{
				case BatchJobType::IMPORT:
					$dbBatchJob = $this->updatedImport($dbBatchJob, $dbBatchJob->getData(), $twinJob);
					break;
			
				case BatchJobType::EXTRACT_MEDIA:
					$dbBatchJob = $this->updatedExtractMedia($dbBatchJob, $dbBatchJob->getData(), $twinJob);
					break;
			
				case BatchJobType::CONVERT:
					$dbBatchJob = $this->updatedConvert($dbBatchJob, $dbBatchJob->getData(), $twinJob);
					break;
			
				case BatchJobType::POSTCONVERT:
					$dbBatchJob = $this->updatedPostConvert($dbBatchJob, $dbBatchJob->getData(), $twinJob);
					break;
			
				case BatchJobType::BULKUPLOAD:
					$dbBatchJob = $this->updatedBulkUpload($dbBatchJob, $dbBatchJob->getData(), $twinJob);
					break;
					
				case BatchJobType::CONVERT_PROFILE:
					$dbBatchJob = $this->updatedConvertProfile($dbBatchJob, $dbBatchJob->getData(), $twinJob);
					break;
					
				case BatchJobType::BULKDOWNLOAD:
					$dbBatchJob = $this->updatedBulkDownload($dbBatchJob, $dbBatchJob->getData(), $twinJob);
					break;
					
				case BatchJobType::PROVISION_PROVIDE:
					$dbBatchJob = $this->updatedProvisionProvide($dbBatchJob, $dbBatchJob->getData(), $twinJob);
					break;
					
				case BatchJobType::PROVISION_DELETE:
					$dbBatchJob = $this->updatedProvisionDelete($dbBatchJob, $dbBatchJob->getData(), $twinJob);
					break;
					
				case BatchJobType::CONVERT_COLLECTION:
					$dbBatchJob = $this->updatedConvertCollection($dbBatchJob, $dbBatchJob->getData(), $twinJob);
					break;
					
				case BatchJobType::STORAGE_EXPORT:
					$dbBatchJob = $this->updatedStorageExport($dbBatchJob, $dbBatchJob->getData(), $twinJob);
					break;
					
				case BatchJobType::STORAGE_DELETE:
					$dbBatchJob = $this->updatedStorageDelete($dbBatchJob, $dbBatchJob->getData(), $twinJob);
					break;
					
				case BatchJobType::CAPTURE_THUMB:
					$dbBatchJob = $this->updatedCaptureThumb($dbBatchJob, $dbBatchJob->getData(), $twinJob);
					break;
		
				default:
					break;
			}
			
			if(!kConf::get("batch_ignore_duplication"))
			{
				if($dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FINISHED)
				{
					$twinBatchJobs = $dbBatchJob->getTwinJobs();
					// update status at all twin jobs 
					foreach($twinBatchJobs as $twinBatchJob)
					{
						if($twinBatchJob->getStatus() != BatchJob::BATCHJOB_STATUS_FINISHED)
							kJobsManager::updateBatchJob($twinBatchJob, BatchJob::BATCHJOB_STATUS_FINISHED);
					}
				}
			}
			
			if($dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_RETRY && $dbBatchJob->getExecutionAttempts() >= BatchJobPeer::getMaxExecutionAttempts($jobType))
			{
				$dbBatchJob = kJobsManager::updateBatchJob($dbBatchJob, BatchJob::BATCHJOB_STATUS_FAILED);
			}
		}
		catch ( Exception $ex )
		{
			self::alert($dbBatchJob, $ex);
			KalturaLog::err( "Error:" . $ex->getMessage() );
		}
			
		return true;
	}	
	
	// creates a mail job with the exception data
	protected static function alert(BatchJob $dbBatchJob, Exception $exception)
	{
	  	$jobData = new kMailJobData();
		$jobData->setMailPriority( kMailJobData::MAIL_PRIORITY_HIGH);
		$jobData->setStatus(kMailJobData::MAIL_STATUS_PENDING);
	  	
		KalturaLog::alert("Error in job [{$dbBatchJob->getId()}]\n".$exception);
		
		$jobData->setMailType(90); // is the email template
		$jobData->setBodyParamsArray(array($dbBatchJob->getId(), $exception->getFile(), $exception->getLine(), $exception->getMessage(), $exception->getTraceAsString()));
		
	 	$jobData->setFromEmail(kConf::get("batch_alert_email")); 
	 	$jobData->setFromName(kConf::get("batch_alert_name"));
		$jobData->setRecipientEmail(kConf::get("batch_alert_email")); 
		$jobData->setSubjectParamsArray( array() );
		
		kJobsManager::addJob($dbBatchJob->createChild(), $jobData, BatchJobType::MAIL, $jobData->getMailType());	
	}
	
	/* (non-PHPdoc)
	 * @see kObjectAddedEventConsumer::shouldConsumeAddedEvent()
	 */
	public function shouldConsumeAddedEvent(BaseObject $object)
	{
		if($object instanceof flavorAsset)
			return true;
		
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectAddedEventConsumer::objectAdded()
	 */
	public function objectAdded(BaseObject $object, BatchJob $raisedJob = null)
	{
		$entry = $object->getentry();
		
		if($object->getStatus() == asset::FLAVOR_ASSET_STATUS_QUEUED)
		{
			if($object->getIsOriginal())
			{
				if($entry->getType() == entryType::MEDIA_CLIP)
				{
					$syncKey = $object->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
					$path = kFileSyncUtils::getLocalFilePathForKey($syncKey);
				
					if(kFileSyncUtils::fileSync_exists($syncKey))
						kJobsManager::addConvertProfileJob($raisedJob, $entry, $object->getId(), $path);
				}
			}
			else
			{
				$object->setStatus(asset::FLAVOR_ASSET_STATUS_VALIDATING);
				$object->save();
			}
		}
		
		if($object->getIsOriginal() && $entry->getStatus() == entryStatus::NO_CONTENT)
		{
			$entry->setStatus(entryStatus::PENDING);
			$entry->save();
		}
		
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::shouldConsumeChangedEvent()
	 */
	public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns)
	{
		if(
				$object instanceof entry 
			&&	in_array(entryPeer::STATUS, $modifiedColumns)
			&&	$object->getStatus() == entryStatus::READY
			&&	$object->getReplacedEntryId()
		)
			return true;
		
		if(
				$object instanceof UploadToken 
			&&	in_array(UploadTokenPeer::STATUS, $modifiedColumns)
			&&	$object->getStatus() == UploadToken::UPLOAD_TOKEN_FULL_UPLOAD
		)
			return true;
		
		if(
				$object instanceof flavorAsset 
			&&	in_array(assetPeer::STATUS, $modifiedColumns))
			return true;
			
		return false;		
	}
	
	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::objectChanged()
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns)
	{
		if(
				$object instanceof entry 
			&&	in_array(entryPeer::STATUS, $modifiedColumns)
			&&	$object->getStatus() == entryStatus::READY
			&&	$object->getReplacedEntryId()
		)
		{
			kFlowHelper::handleEntryReplacement($object);
			return true;
		}
		
		if(
				$object instanceof UploadToken 
			&&	in_array(UploadTokenPeer::STATUS, $modifiedColumns)
			&&	$object->getStatus() == UploadToken::UPLOAD_TOKEN_FULL_UPLOAD
		)
		{
			kFlowHelper::handleUploadFinished($object);
			return true;
		}
		
		if(
				!($object instanceof flavorAsset) 
			||	!in_array(assetPeer::STATUS, $modifiedColumns))
			return true;
		
		$entry = $object->getentry();
		
		KalturaLog::debug("Asset id [" . $object->getId() . "] isOriginal [" . $object->getIsOriginal() . "] status [" . $object->getStatus() . "]");
		if($object->getIsOriginal())
		{
			if($object->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_QUEUED && $entry->getType() == entryType::MEDIA_CLIP)
			{
				$syncKey = $object->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
				if(kFileSyncUtils::fileSync_exists($syncKey))
				{
					KalturaLog::debug("Start conversion");
					$path = kFileSyncUtils::getLocalFilePathForKey($syncKey);
					kJobsManager::addConvertProfileJob(null, $entry, $object->getId(), $path);
				}
				else
				{
					KalturaLog::debug("File sync not created yet");
				}
			}
		}
		elseif($object->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_VALIDATING)
		{
			$jobSubType = BatchJob::BATCHJOB_SUB_TYPE_POSTCONVERT_FLAVOR;
			$offset = $entry->getThumbOffset(); // entry getThumbOffset now takes the partner DefThumbOffset into consideration
			$syncKey = $object->getSyncKey(asset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			
			$fileSync = kFileSyncUtils::getLocalFileSyncForKey($syncKey, false);
			if(!$fileSync)
				return true;
				
			$srcFileSyncLocalPath = kFileSyncUtils::getLocalFilePathForKey($syncKey);
			if($srcFileSyncLocalPath)
				kJobsManager::addPostConvertJob(null, $jobSubType, $srcFileSyncLocalPath, $object->getId(), null, $entry->getCreateThumb(), $offset);
		}
		elseif ($object->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_READY) 
		{
			// If we get a ready flavor and the entry is in no content 
			if($entry->getStatus() == entryStatus::NO_CONTENT)
			{
				$entry->setStatus(entryStatus::PENDING); // we change the entry to pending 
				$entry->save();
			}
		}
		
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectDeletedEventConsumer::shouldConsumeDeletedEvent()
	 */
	public function shouldConsumeDeletedEvent(BaseObject $object)
	{
		if($object instanceof UploadToken)
			return true;
			
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectDeletedEventConsumer::objectDeleted()
	 */
	public function objectDeleted(BaseObject $object, BatchJob $raisedJob = null)
	{
		kFlowHelper::handleUploadCanceled($object);
		return true;
	}
	
}