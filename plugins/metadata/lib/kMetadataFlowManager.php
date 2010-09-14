<?php
class kMetadataFlowManager implements kBatchJobStatusEventConsumer
{
	/**
	 * @param BatchJob $dbBatchJob
	 * @param unknown_type $entryStatus
	 * @param BatchJob $twinJob
	 * @return BatchJob
	 */
	public function updatedJob(BatchJob $dbBatchJob, $entryStatus, BatchJob $twinJob = null)
	{
		switch($dbBatchJob->getJobType())
		{
			case BatchJob::BATCHJOB_TYPE_METADATA_IMPORT:
				$dbBatchJob = $this->updatedImportMetadata($dbBatchJob, $dbBatchJob->getData(), $entryStatus, $twinJob);
				break;
		
			case BatchJob::BATCHJOB_TYPE_METADATA_TRANSFORM:
				$dbBatchJob = $this->updatedTransformMetadata($dbBatchJob, $dbBatchJob->getData(), $entryStatus, $twinJob);
				break;
	
			default:
				break;
		}
		
		return $dbBatchJob;
	}
	
		
	protected function updatedImportMetadata(BatchJob $dbBatchJob, kImportMetadataJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_PENDING:
				return $this->updatedImportMetadataPending($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_QUEUED:
				return $this->updatedImportMetadataQueued($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_PROCESSING:
				return $this->updatedImportMetadataProcessing($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_PROCESSED:
				return $this->updatedImportMetadataProcessed($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_MOVEFILE:
				return $this->updatedImportMetadataMoveFile($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return $this->updatedImportMetadataFinished($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FAILED:
				return $this->updatedImportMetadataFailed($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_ABORTED:
				return $this->updatedImportMetadataAborted($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_ALMOST_DONE:
				return $this->updatedImportMetadataAlmostDone($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_RETRY:
				return $this->updatedImportMetadataRetry($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return $this->updatedImportMetadataFatal($dbBatchJob, $data, $entryStatus, $twinJob);
			default:
				return $dbBatchJob;
		}
	}
	
	protected function updatedImportMetadataPending(BatchJob $dbBatchJob, kImportMetadataJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedImportMetadataQueued(BatchJob $dbBatchJob, kImportMetadataJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedImportMetadataProcessing(BatchJob $dbBatchJob, kImportMetadataJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedImportMetadataProcessed(BatchJob $dbBatchJob, kImportMetadataJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedImportMetadataMoveFile(BatchJob $dbBatchJob, kImportMetadataJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedImportMetadataFinished(BatchJob $dbBatchJob, kImportMetadataJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		// TODO - update the metadata file sync
		return $dbBatchJob;
	}
	
	protected function updatedImportMetadataFailed(BatchJob $dbBatchJob, kImportMetadataJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		// TODO - set the metadata status to invalid
		return $dbBatchJob;
	}
	
	protected function updatedImportMetadataAborted(BatchJob $dbBatchJob, kImportMetadataJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedImportMetadataAlmostDone(BatchJob $dbBatchJob, kImportMetadataJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedImportMetadataRetry(BatchJob $dbBatchJob, kImportMetadataJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedImportMetadataFatal(BatchJob $dbBatchJob, kImportMetadataJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		return $this->updatedImportMetadataFailed($dbBatchJob, $data, $entryStatus, $twinJob);
	}
	
		
	protected function updatedTransformMetadata(BatchJob $dbBatchJob, kTransformMetadataJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_PENDING:
				return $this->updatedTransformMetadataPending($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_QUEUED:
				return $this->updatedTransformMetadataQueued($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_PROCESSING:
				return $this->updatedTransformMetadataProcessing($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_PROCESSED:
				return $this->updatedTransformMetadataProcessed($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_MOVEFILE:
				return $this->updatedTransformMetadataMoveFile($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return $this->updatedTransformMetadataFinished($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FAILED:
				return $this->updatedTransformMetadataFailed($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_ABORTED:
				return $this->updatedTransformMetadataAborted($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_ALMOST_DONE:
				return $this->updatedTransformMetadataAlmostDone($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_RETRY:
				return $this->updatedTransformMetadataRetry($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return $this->updatedTransformMetadataFatal($dbBatchJob, $data, $entryStatus, $twinJob);
			default:
				return $dbBatchJob;
		}
	}
	
	protected function updatedTransformMetadataPending(BatchJob $dbBatchJob, kTransformMetadataJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		$metadataProfile = MetadataProfilePeer::retrieveById($data->getMetadataProfileId());
		$metadataProfile->setStatus(MetadataProfile::STATUS_TRANSFORMING);
		$metadataProfile->save();
		
		return $dbBatchJob;
	}
	
	protected function updatedTransformMetadataQueued(BatchJob $dbBatchJob, kTransformMetadataJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedTransformMetadataProcessing(BatchJob $dbBatchJob, kTransformMetadataJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedTransformMetadataProcessed(BatchJob $dbBatchJob, kTransformMetadataJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedTransformMetadataMoveFile(BatchJob $dbBatchJob, kTransformMetadataJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedTransformMetadataFinished(BatchJob $dbBatchJob, kTransformMetadataJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		$metadataProfile = MetadataProfilePeer::retrieveById($data->getMetadataProfileId());
		$metadataProfile->setStatus(MetadataProfile::STATUS_ACTIVE);
		$metadataProfile->save();
		
		return $dbBatchJob;
	}
	
	protected function updatedTransformMetadataFailed(BatchJob $dbBatchJob, kTransformMetadataJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		if(!$data->getMetadataProfileId())
			return $dbBatchJob;
			
		$metadataProfile = MetadataProfilePeer::retrieveById($data->getMetadataProfileId());
		if(!$metadataProfile)
			return $dbBatchJob;
	
		$metadataProfile->setStatus(MetadataProfile::STATUS_DEPRECATED);
		$metadataProfile->save();
		
		return $dbBatchJob;
	}
	
	protected function updatedTransformMetadataAborted(BatchJob $dbBatchJob, kTransformMetadataJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedTransformMetadataAlmostDone(BatchJob $dbBatchJob, kTransformMetadataJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedTransformMetadataRetry(BatchJob $dbBatchJob, kTransformMetadataJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedTransformMetadataFatal(BatchJob $dbBatchJob, kTransformMetadataJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		return $this->updatedTransformMetadataFailed($dbBatchJob, $data, $entryStatus, $twinJob);
	}
	
}