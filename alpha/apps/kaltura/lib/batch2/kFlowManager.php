<?php

/**
 * 
 * Manages the batch flow
 * 
 * @package Core
 * @subpackage Batch
 *
 */
class kFlowManager implements kBatchJobStatusEventConsumer, kObjectAddedEventConsumer
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
				kBatchManager::updateEntry($dbBatchJob, entryStatus::ERROR_IMPORTING);
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
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return $this->updatedBulkUploadFailed($dbBatchJob, $data, $twinJob);
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
	
	/**
	 * @param BatchJob $dbBatchJob
	 * @param BatchJob $twinJob
	 * @return bool true if should continue to the next consumer
	 */
	public function updatedJob(BatchJob $dbBatchJob, BatchJob $twinJob = null)
	{
		try
		{
			$jobType = $dbBatchJob->getJobType();
			
			if(is_null($dbBatchJob->getQueueTime()) && $dbBatchJob->getStatus() != BatchJob::BATCHJOB_STATUS_PENDING)
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
	
	/**
	 * @param BaseObject $object
	 * @return bool true if should continue to the next consumer
	 */
	public function objectAdded(BaseObject $object)
	{
		if($object instanceof flavorAsset && $object->getIsOriginal())
		{
			$entry = $object->getentry();
			if($entry->getType() == entryType::MEDIA_CLIP)
			{
				$syncKey = $object->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
				$path = kFileSyncUtils::getLocalFilePathForKey($syncKey);
			
				kJobsManager::addConvertProfileJob(null, $entry, $object->getId(), $path);
			}
		}
		
		return true;
	}
}