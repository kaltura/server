<?php
/**
 * @package plugins.multiCenters
 * @subpackage lib
 */
class kMultiCentersFlowManager implements kBatchJobStatusEventConsumer
{
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::shouldConsumeJobStatusEvent()
	 */
	public function shouldConsumeJobStatusEvent(BatchJob $dbBatchJob)
	{
		if($dbBatchJob->getJobType() == BatchJobType::FILESYNC_IMPORT)
			return true;
				
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::updatedJob()
	 */
	public function updatedJob(BatchJob $dbBatchJob)
	{
		$dbBatchJob = $this->updatedFileSyncImport($dbBatchJob, $dbBatchJob->getData());
		
		return true;
	}
	
		
	/**
	 * @param BatchJob $dbBatchJob
	 * @param kFileSyncImportJobData $data
	 * @return BatchJob
	 */
	protected function updatedFileSyncImport(BatchJob $dbBatchJob, kFileSyncImportJobData $data)
	{
		switch($dbBatchJob->getStatus())
		{
			// success
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return $this->updatedFileSyncImportFinished($dbBatchJob, $data);
				
			// failure
			case BatchJob::BATCHJOB_STATUS_ABORTED:
			case BatchJob::BATCHJOB_STATUS_FATAL:
			case BatchJob::BATCHJOB_STATUS_FAILED:
				return $this->updatedFileSyncImportFailed($dbBatchJob, $data);
			default:
				return $dbBatchJob;
		}
	}
	
	/**
	 * Update relevant filesync as READY
	 * 
	 * @param BatchJob $dbBatchJob
	 * @param kFileSyncImportJobData $data
	 * @throws KalturaAPIException
	 * @return BatchJob
	 */
	protected function updatedFileSyncImportFinished(BatchJob $dbBatchJob, kFileSyncImportJobData $data)
	{
		$fileSyncId = $data->getFilesyncId();
		if (!$fileSyncId) {
			KalturaLog::err('File sync ID not found in job data.');
			throw new KalturaAPIException(MultiCentersErrors::INVALID_FILESYNC_ID);
		}
		
		$fileSync = FileSyncPeer::retrieveByPK($fileSyncId);
		if (!$fileSync) {
			KalturaLog::err("Invalid filesync record with id [$fileSyncId]");
			throw new KalturaAPIException(MultiCentersErrors::INVALID_FILESYNC_RECORD, $fileSyncId);
		}
		
		$fileSync->setStatus(FileSync::FILE_SYNC_STATUS_READY);
		$fileSync->setFileSizeFromPath(kFileSyncUtils::getLocalFilePathForKey(kFileSyncUtils::getKeyForFileSync($fileSync)));
		$fileSync->save();
		return $dbBatchJob;
	}
	
	/**
	 * Update relevant filesync as FAILED.
	 * No need to throw exception if the file sync not found, the job already marked as failed anyway.
	 * 
	 * @param BatchJob $dbBatchJob
	 * @param kFileSyncImportJobData $data
	 * @return BatchJob
	 */
	protected function updatedFileSyncImportFailed(BatchJob $dbBatchJob, kFileSyncImportJobData $data)
	{
		$fileSyncId = $data->getFilesyncId();
		if (!$fileSyncId) {
			KalturaLog::err('File sync ID not found in job data.');
			return $dbBatchJob;
		}
		
		$fileSync = FileSyncPeer::retrieveByPK($fileSyncId);
		if (!$fileSync) {
			KalturaLog::err("Invalid filesync record with id [$fileSyncId]");
			return $dbBatchJob;
		}
		
		$fileSync->setStatus(FileSync::FILE_SYNC_STATUS_ERROR);
		$fileSync->save();
		return $dbBatchJob;
	}
}