<?php
class kDropFolderEventsConsumer implements kBatchJobStatusEventConsumer
{
		
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::shouldConsumeJobStatusEvent()
	 */
	public function shouldConsumeJobStatusEvent(BatchJob $dbBatchJob)
	{
	    // consume import jobs with data of type kDropFolderImportJobData
		if($dbBatchJob->getJobType() == BatchJobType::IMPORT && get_class($dbBatchJob->getData()) == 'kDropFolderImportJobData')
			return true;
				
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::updatedJob()
	 */
	public function updatedJob(BatchJob $dbBatchJob, BatchJob $twinJob = null)
	{
		$dbBatchJob = $this->updatedImport($dbBatchJob, $dbBatchJob->getData(), $twinJob);

		return true;
	}
		
	protected function updatedImport(BatchJob $dbBatchJob, kDropFolderImportJobData $data, BatchJob $twinJob = null)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return $this->updatedImportFinished($dbBatchJob, $data, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FAILED:
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return $this->updatedImportFailed($dbBatchJob, $data, $twinJob);
			default:
				return $dbBatchJob;
		}
	}
	
	protected function updatedImportFinished(BatchJob $dbBatchJob, kDropFolderImportJobData $data, BatchJob $twinJob = null)
	{
		$dropFolderFile = DropFolderFilePeer::retrieveByPK($data->getDropFolderFileId());
		$dropFolderFile->setStatus(DropFolderFileStatus::HANDLED);
		$dropFolderFile->save();
		
		return $dbBatchJob;
	}
	
	protected function updatedImportFailed(BatchJob $dbBatchJob, kDropFolderImportJobData $data, BatchJob $twinJob = null)
	{
	    // set drop folder file status to ERROR_DOWNLOADING
		$dropFolderFile = DropFolderFilePeer::retrieveByPK($data->getDropFolderFileId());
		$dropFolderFile->setStatus(DropFolderFileStatus::ERROR_DOWNLOADING);
		$dropFolderFile->setErrorCode(DropFolderFileErrorCode::ERROR_DOWNLOADING_FILE);
		$dropFolderFile->setErrorDescription('Error while downloading file');
		$dropFolderFile->save();		
		
		return $dbBatchJob;
	}
}