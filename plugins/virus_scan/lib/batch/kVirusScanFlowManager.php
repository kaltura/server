<?php
class kVirusScanFlowManager implements kBatchJobStatusEventConsumer
{
	/**
	 * @param BatchJob $dbBatchJob
	 * @param unknown_type $entryStatus
	 * @param BatchJob $twinJob
	 * @return bool true if should continue to the next consumer
	 */
	public function updatedJob(BatchJob $dbBatchJob, $entryStatus, BatchJob $twinJob = null)
	{
		if($dbBatchJob->getJobType() == VirusScanBatchJobType::get()->coreValue(VirusScanBatchJobType::VIRUS_SCAN))
			$dbBatchJob = $this->updatedVirusScan($dbBatchJob, $dbBatchJob->getData(), $entryStatus, $twinJob);
		
		return true;
	}
		
	protected function updatedVirusScan(BatchJob $dbBatchJob, kVirusScanJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_PENDING:
				return $this->updatedVirusScanPending($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_QUEUED:
				return $this->updatedVirusScanQueued($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_PROCESSING:
				return $this->updatedVirusScanProcessing($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_PROCESSED:
				return $this->updatedVirusScanProcessed($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_MOVEFILE:
				return $this->updatedVirusScanMoveFile($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return $this->updatedVirusScanFinished($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FAILED:
				return $this->updatedVirusScanFailed($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_ABORTED:
				return $this->updatedVirusScanAborted($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_ALMOST_DONE:
				return $this->updatedVirusScanAlmostDone($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_RETRY:
				return $this->updatedVirusScanRetry($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return $this->updatedVirusScanFatal($dbBatchJob, $data, $entryStatus, $twinJob);
			default:
				return $dbBatchJob;
		}
	}
	
	protected function updatedVirusScanPending(BatchJob $dbBatchJob, kVirusScanJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedVirusScanQueued(BatchJob $dbBatchJob, kVirusScanJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedVirusScanProcessing(BatchJob $dbBatchJob, kVirusScanJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedVirusScanProcessed(BatchJob $dbBatchJob, kVirusScanJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedVirusScanMoveFile(BatchJob $dbBatchJob, kVirusScanJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedVirusScanFinished(BatchJob $dbBatchJob, kVirusScanJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		// TODO		
		return $dbBatchJob;
	}
	
	protected function updatedVirusScanFailed(BatchJob $dbBatchJob, kVirusScanJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		// TODO
		return $dbBatchJob;
	}
	
	protected function updatedVirusScanAborted(BatchJob $dbBatchJob, kVirusScanJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedVirusScanAlmostDone(BatchJob $dbBatchJob, kVirusScanJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedVirusScanRetry(BatchJob $dbBatchJob, kVirusScanJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedVirusScanFatal(BatchJob $dbBatchJob, kVirusScanJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		return $this->updatedVirusScanFailed($dbBatchJob, $data, $entryStatus, $twinJob);
	}
}