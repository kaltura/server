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
		
	protected function updatedImport(BatchJob $dbBatchJob, kImportJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_PENDING:
				return $this->updatedImportPending($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_QUEUED:
				return $this->updatedImportQueued($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_PROCESSING:
				return $this->updatedImportProcessing($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_PROCESSED:
				return $this->updatedImportProcessed($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_MOVEFILE:
				return $this->updatedImportMoveFile($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return $this->updatedImportFinished($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FAILED:
				return $this->updatedImportFailed($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_ABORTED:
				return $this->updatedImportAborted($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_ALMOST_DONE:
				return $this->updatedImportAlmostDone($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_RETRY:
				return $this->updatedImportRetry($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return $this->updatedImportFatal($dbBatchJob, $data, $entryStatus, $twinJob);
			default:
				return $dbBatchJob;
		}
	}
	
	protected function updatedImportPending(BatchJob $dbBatchJob, kImportJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedImportQueued(BatchJob $dbBatchJob, kImportJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedImportProcessing(BatchJob $dbBatchJob, kImportJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedImportProcessed(BatchJob $dbBatchJob, kImportJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedImportMoveFile(BatchJob $dbBatchJob, kImportJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedImportFinished(BatchJob $dbBatchJob, kImportJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		return kFlowHelper::handleImportFinished($dbBatchJob, $data, $entryStatus, $twinJob);
	}
	
	protected function updatedImportFailed(BatchJob $dbBatchJob, kImportJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		kBatchManager::updateEntry($dbBatchJob, entryStatus::ERROR_IMPORTING);
		return $dbBatchJob;
	}
	
	protected function updatedImportAborted(BatchJob $dbBatchJob, kImportJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedImportAlmostDone(BatchJob $dbBatchJob, kImportJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedImportRetry(BatchJob $dbBatchJob, kImportJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedImportFatal(BatchJob $dbBatchJob, kImportJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		return $this->updatedImportFailed($dbBatchJob, $data, $entryStatus, $twinJob);
	}
	
	protected function updatedExtractMedia(BatchJob $dbBatchJob, kExtractMediaJobData $data, $entryStatus, BatchJob $twinJob=null)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_PENDING:
				return $this->updatedExtractMediaPending($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_QUEUED:
				return $this->updatedExtractMediaQueued($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_PROCESSING:
				return $this->updatedExtractMediaProcessing($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_PROCESSED:
				return $this->updatedExtractMediaProcessed($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_MOVEFILE:
				return $this->updatedExtractMediaMoveFile($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return $this->updatedExtractMediaFinished($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FAILED:
				return $this->updatedExtractMediaFailed($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_ABORTED:
				return $this->updatedExtractMediaAborted($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_ALMOST_DONE:
				return $this->updatedExtractMediaAlmostDone($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_RETRY:
				return $this->updatedExtractMediaRetry($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return $this->updatedExtractMediaFatal($dbBatchJob, $data, $entryStatus, $twinJob);
			default:
				return $dbBatchJob;
		}
	}
	
	protected function updatedExtractMediaPending(BatchJob $dbBatchJob, kExtractMediaJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedExtractMediaQueued(BatchJob $dbBatchJob, kExtractMediaJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedExtractMediaProcessing(BatchJob $dbBatchJob, kExtractMediaJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedExtractMediaProcessed(BatchJob $dbBatchJob, kExtractMediaJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedExtractMediaMoveFile(BatchJob $dbBatchJob, kExtractMediaJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedExtractMediaFinished(BatchJob $dbBatchJob, kExtractMediaJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		return kFlowHelper::handleExtractMediaClosed($dbBatchJob, $data, $entryStatus, $twinJob);
	}
	
	protected function updatedExtractMediaFailed(BatchJob $dbBatchJob, kExtractMediaJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		return kFlowHelper::handleExtractMediaClosed($dbBatchJob, $data, $entryStatus, $twinJob);
	}
	
	protected function updatedExtractMediaAborted(BatchJob $dbBatchJob, kExtractMediaJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedExtractMediaAlmostDone(BatchJob $dbBatchJob, kExtractMediaJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedExtractMediaRetry(BatchJob $dbBatchJob, kExtractMediaJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedExtractMediaFatal(BatchJob $dbBatchJob, kExtractMediaJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		return $this->updatedExtractMediaFailed($dbBatchJob, $data, $entryStatus, $twinJob);
	}
	
	protected function updatedStorageExport(BatchJob $dbBatchJob, kStorageExportJobData $data, $entryStatus, BatchJob $twinJob=null)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_PENDING:
				return $this->updatedStorageExportPending($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_QUEUED:
				return $this->updatedStorageExportQueued($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_PROCESSING:
				return $this->updatedStorageExportProcessing($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_PROCESSED:
				return $this->updatedStorageExportProcessed($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_MOVEFILE:
				return $this->updatedStorageExportMoveFile($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return $this->updatedStorageExportFinished($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FAILED:
				return $this->updatedStorageExportFailed($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_ABORTED:
				return $this->updatedStorageExportAborted($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_ALMOST_DONE:
				return $this->updatedStorageExportAlmostDone($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_RETRY:
				return $this->updatedStorageExportRetry($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return $this->updatedStorageExportFatal($dbBatchJob, $data, $entryStatus, $twinJob);
			default:
				return $dbBatchJob;
		}
	}
	
	protected function updatedStorageExportPending(BatchJob $dbBatchJob, kStorageExportJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedStorageExportQueued(BatchJob $dbBatchJob, kStorageExportJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedStorageExportProcessing(BatchJob $dbBatchJob, kStorageExportJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedStorageExportProcessed(BatchJob $dbBatchJob, kStorageExportJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedStorageExportMoveFile(BatchJob $dbBatchJob, kStorageExportJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedStorageExportFinished(BatchJob $dbBatchJob, kStorageExportJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		return kFlowHelper::handleStorageExportFinished($dbBatchJob, $data);
	}
	
	protected function updatedStorageExportFailed(BatchJob $dbBatchJob, kStorageExportJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		return kFlowHelper::handleStorageExportFailed($dbBatchJob, $data);
	}
	
	protected function updatedStorageExportAborted(BatchJob $dbBatchJob, kStorageExportJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedStorageExportAlmostDone(BatchJob $dbBatchJob, kStorageExportJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedStorageExportRetry(BatchJob $dbBatchJob, kStorageExportJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedStorageExportFatal(BatchJob $dbBatchJob, kStorageExportJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedStorageDelete(BatchJob $dbBatchJob, kStorageDeleteJobData $data, $entryStatus, BatchJob $twinJob=null)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_PENDING:
				return $this->updatedStorageDeletePending($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_QUEUED:
				return $this->updatedStorageDeleteQueued($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_PROCESSING:
				return $this->updatedStorageDeleteProcessing($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_PROCESSED:
				return $this->updatedStorageDeleteProcessed($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_MOVEFILE:
				return $this->updatedStorageDeleteMoveFile($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return $this->updatedStorageDeleteFinished($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FAILED:
				return $this->updatedStorageDeleteFailed($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_ABORTED:
				return $this->updatedStorageDeleteAborted($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_ALMOST_DONE:
				return $this->updatedStorageDeleteAlmostDone($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_RETRY:
				return $this->updatedStorageDeleteRetry($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return $this->updatedStorageDeleteFatal($dbBatchJob, $data, $entryStatus, $twinJob);
			default:
				return $dbBatchJob;
		}
	}
	
	protected function updatedStorageDeletePending(BatchJob $dbBatchJob, kStorageDeleteJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedStorageDeleteQueued(BatchJob $dbBatchJob, kStorageDeleteJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedStorageDeleteProcessing(BatchJob $dbBatchJob, kStorageDeleteJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedStorageDeleteProcessed(BatchJob $dbBatchJob, kStorageDeleteJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedStorageDeleteMoveFile(BatchJob $dbBatchJob, kStorageDeleteJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedStorageDeleteFinished(BatchJob $dbBatchJob, kStorageDeleteJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		return kFlowHelper::handleStorageDeleteFinished($dbBatchJob, $data);
	}
	
	protected function updatedStorageDeleteFailed(BatchJob $dbBatchJob, kStorageDeleteJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedStorageDeleteAborted(BatchJob $dbBatchJob, kStorageDeleteJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedStorageDeleteAlmostDone(BatchJob $dbBatchJob, kStorageDeleteJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedStorageDeleteRetry(BatchJob $dbBatchJob, kStorageDeleteJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedStorageDeleteFatal(BatchJob $dbBatchJob, kStorageDeleteJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedConvert(BatchJob $dbBatchJob, kConvertJobData $data, $entryStatus, BatchJob $twinJob=null)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_PENDING:
				return $this->updatedConvertPending($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_QUEUED:
				return $this->updatedConvertQueued($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_PROCESSING:
				return $this->updatedConvertProcessing($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_PROCESSED:
				return $this->updatedConvertProcessed($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_MOVEFILE:
				return $this->updatedConvertMoveFile($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return $this->updatedConvertFinished($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FAILED:
				return $this->updatedConvertFailed($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_ABORTED:
				return $this->updatedConvertAborted($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_ALMOST_DONE:
				return $this->updatedConvertAlmostDone($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_RETRY:
				return $this->updatedConvertRetry($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return $this->updatedConvertFatal($dbBatchJob, $data, $entryStatus, $twinJob);
			default:
				return $dbBatchJob;
		}
	}
	
	protected function updatedConvertPending(BatchJob $dbBatchJob, kConvertJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		return kFlowHelper::handleConvertPending($dbBatchJob, $data, $entryStatus, $twinJob);
	}
	
	protected function updatedConvertQueued(BatchJob $dbBatchJob, kConvertJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		return kFlowHelper::handleConvertQueued($dbBatchJob, $data, $entryStatus, $twinJob);
	}
	
	protected function updatedConvertProcessing(BatchJob $dbBatchJob, kConvertJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedConvertProcessed(BatchJob $dbBatchJob, kConvertJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedConvertMoveFile(BatchJob $dbBatchJob, kConvertJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedConvertFinished(BatchJob $dbBatchJob, kConvertJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		return kFlowHelper::handleConvertFinished($dbBatchJob, $data, $entryStatus, $twinJob);
	}
	
	protected function updatedConvertFailed(BatchJob $dbBatchJob, kConvertJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		return kFlowHelper::handleConvertFailed($dbBatchJob, $data, $entryStatus, $twinJob);
	}
	
	protected function updatedConvertAborted(BatchJob $dbBatchJob, kConvertJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedConvertAlmostDone(BatchJob $dbBatchJob, kConvertJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedConvertRetry(BatchJob $dbBatchJob, kConvertJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedConvertFatal(BatchJob $dbBatchJob, kConvertJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		return $this->updatedConvertFailed($dbBatchJob, $data, $entryStatus, $twinJob);
	}
	
	protected function updatedPostConvert(BatchJob $dbBatchJob, kPostConvertJobData $data, $entryStatus, BatchJob $twinJob=null)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_PENDING:
				return $this->updatedPostConvertPending($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_QUEUED:
				return $this->updatedPostConvertQueued($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_PROCESSING:
				return $this->updatedPostConvertProcessing($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_PROCESSED:
				return $this->updatedPostConvertProcessed($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_MOVEFILE:
				return $this->updatedPostConvertMoveFile($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return $this->updatedPostConvertFinished($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FAILED:
				return $this->updatedPostConvertFailed($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_ABORTED:
				return $this->updatedPostConvertAborted($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_ALMOST_DONE:
				return $this->updatedPostConvertAlmostDone($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_RETRY:
				return $this->updatedPostConvertRetry($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return $this->updatedPostConvertFatal($dbBatchJob, $data, $entryStatus, $twinJob);
			default:
				return $dbBatchJob;
		}
	}
	
	protected function updatedPostConvertPending(BatchJob $dbBatchJob, kPostConvertJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedPostConvertQueued(BatchJob $dbBatchJob, kPostConvertJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedPostConvertProcessing(BatchJob $dbBatchJob, kPostConvertJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedPostConvertProcessed(BatchJob $dbBatchJob, kPostConvertJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedPostConvertMoveFile(BatchJob $dbBatchJob, kPostConvertJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedPostConvertFinished(BatchJob $dbBatchJob, kPostConvertJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		$dbBatchJob = kFlowHelper::handlePostConvertFinished($dbBatchJob, $data, $entryStatus, $twinJob);
		
		return $dbBatchJob;
	}
	
	protected function updatedPostConvertFailed(BatchJob $dbBatchJob, kPostConvertJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		return kFlowHelper::handlePostConvertFailed($dbBatchJob, $data, $entryStatus, $twinJob);
	}
	
	protected function updatedPostConvertAborted(BatchJob $dbBatchJob, kPostConvertJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedPostConvertAlmostDone(BatchJob $dbBatchJob, kPostConvertJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedPostConvertRetry(BatchJob $dbBatchJob, kPostConvertJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedPostConvertFatal(BatchJob $dbBatchJob, kPostConvertJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		return $this->updatedPostConvertFailed($dbBatchJob, $data, $entryStatus, $twinJob);
	}
	
	protected function updatedPull(BatchJob $dbBatchJob, kPullJobData $data, $entryStatus, BatchJob $twinJob=null)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_PENDING:
				return $this->updatedPullPending($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_QUEUED:
				return $this->updatedPullQueued($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_PROCESSING:
				return $this->updatedPullProcessing($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_PROCESSED:
				return $this->updatedPullProcessed($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_MOVEFILE:
				return $this->updatedPullMoveFile($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return $this->updatedPullFinished($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FAILED:
				return $this->updatedPullFailed($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_ABORTED:
				return $this->updatedPullAborted($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_ALMOST_DONE:
				return $this->updatedPullAlmostDone($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_RETRY:
				return $this->updatedPullRetry($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return $this->updatedPullFatal($dbBatchJob, $data, $entryStatus, $twinJob);
			default:
				return $dbBatchJob;
		}
	}
	
	protected function updatedPullPending(BatchJob $dbBatchJob, kPullJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedPullQueued(BatchJob $dbBatchJob, kPullJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedPullProcessing(BatchJob $dbBatchJob, kPullJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedPullProcessed(BatchJob $dbBatchJob, kPullJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedPullMoveFile(BatchJob $dbBatchJob, kPullJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedPullFinished(BatchJob $dbBatchJob, kPullJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		return kFlowHelper::handlePullFinished($dbBatchJob, $data, $entryStatus, $twinJob);
	}
	
	protected function updatedPullFailed(BatchJob $dbBatchJob, kPullJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		return kFlowHelper::handlePullFailed($dbBatchJob, $data, $entryStatus, $twinJob);
	}
	
	protected function updatedPullAborted(BatchJob $dbBatchJob, kPullJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedPullAlmostDone(BatchJob $dbBatchJob, kPullJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedPullRetry(BatchJob $dbBatchJob, kPullJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedPullFatal(BatchJob $dbBatchJob, kPullJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		return $this->updatedPullFailed($dbBatchJob, $data, $entryStatus, $twinJob);
	}
	
	protected function updatedDelete(BatchJob $dbBatchJob, $data, $entryStatus, BatchJob $twinJob=null)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_PENDING:
				return $this->updatedDeletePending($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_QUEUED:
				return $this->updatedDeleteQueued($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_PROCESSING:
				return $this->updatedDeleteProcessing($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_PROCESSED:
				return $this->updatedDeleteProcessed($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_MOVEFILE:
				return $this->updatedDeleteMoveFile($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return $this->updatedDeleteFinished($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FAILED:
				return $this->updatedDeleteFailed($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_ABORTED:
				return $this->updatedDeleteAborted($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_ALMOST_DONE:
				return $this->updatedDeleteAlmostDone($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_RETRY:
				return $this->updatedDeleteRetry($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return $this->updatedDeleteFatal($dbBatchJob, $data, $entryStatus, $twinJob);
			default:
				return $dbBatchJob;
		}
	}
	
	protected function updatedDeletePending(BatchJob $dbBatchJob, kDeleteJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedDeleteQueued(BatchJob $dbBatchJob, kDeleteJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedDeleteProcessing(BatchJob $dbBatchJob, kDeleteJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedDeleteProcessed(BatchJob $dbBatchJob, kDeleteJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedDeleteMoveFile(BatchJob $dbBatchJob, kDeleteJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedDeleteFinished(BatchJob $dbBatchJob, kDeleteJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedDeleteFailed(BatchJob $dbBatchJob, kDeleteJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedDeleteAborted(BatchJob $dbBatchJob, kDeleteJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedDeleteAlmostDone(BatchJob $dbBatchJob, kDeleteJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedDeleteRetry(BatchJob $dbBatchJob, kDeleteJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedDeleteFatal(BatchJob $dbBatchJob, kDeleteJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		return $this->updatedDeleteFailed($dbBatchJob, $data, $entryStatus, $twinJob);
	}
	
	protected function updatedFlatten(BatchJob $dbBatchJob, $data, $entryStatus, BatchJob $twinJob=null)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_PENDING:
				return $this->updatedFlattenPending($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_QUEUED:
				return $this->updatedFlattenQueued($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_PROCESSING:
				return $this->updatedFlattenProcessing($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_PROCESSED:
				return $this->updatedFlattenProcessed($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_MOVEFILE:
				return $this->updatedFlattenMoveFile($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return $this->updatedFlattenFinished($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FAILED:
				return $this->updatedFlattenFailed($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_ABORTED:
				return $this->updatedFlattenAborted($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_ALMOST_DONE:
				return $this->updatedFlattenAlmostDone($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_RETRY:
				return $this->updatedFlattenRetry($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return $this->updatedFlattenFatal($dbBatchJob, $data, $entryStatus, $twinJob);
			default:
				return $dbBatchJob;
		}
	}
	
	protected function updatedFlattenPending(BatchJob $dbBatchJob, kFlattenJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedFlattenQueued(BatchJob $dbBatchJob, kFlattenJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedFlattenProcessing(BatchJob $dbBatchJob, kFlattenJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedFlattenProcessed(BatchJob $dbBatchJob, kFlattenJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedFlattenMoveFile(BatchJob $dbBatchJob, kFlattenJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedFlattenFinished(BatchJob $dbBatchJob, kFlattenJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedFlattenFailed(BatchJob $dbBatchJob, kFlattenJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedFlattenAborted(BatchJob $dbBatchJob, kFlattenJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedFlattenAlmostDone(BatchJob $dbBatchJob, kFlattenJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedFlattenRetry(BatchJob $dbBatchJob, kFlattenJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedFlattenFatal(BatchJob $dbBatchJob, kFlattenJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		return $this->updatedFlattenFailed($dbBatchJob, $data, $entryStatus, $twinJob);
	}
		
	protected function updatedBulkUpload(BatchJob $dbBatchJob, kBulkUploadJobData $data, $entryStatus, BatchJob $twinJob=null)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_PENDING:
				return $this->updatedBulkUploadPending($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_QUEUED:
				return $this->updatedBulkUploadQueued($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_PROCESSING:
				return $this->updatedBulkUploadProcessing($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_PROCESSED:
				return $this->updatedBulkUploadProcessed($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_MOVEFILE:
				return $this->updatedBulkUploadMoveFile($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return $this->updatedBulkUploadFinished($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FAILED:
				return $this->updatedBulkUploadFailed($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_ABORTED:
				return $this->updatedBulkUploadAborted($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_ALMOST_DONE:
				return $this->updatedBulkUploadAlmostDone($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_RETRY:
				return $this->updatedBulkUploadRetry($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return $this->updatedBulkUploadFatal($dbBatchJob, $data, $entryStatus, $twinJob);
			default:
				return $dbBatchJob;
		}
	}
	
	protected function updatedBulkUploadPending(BatchJob $dbBatchJob, kBulkUploadJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		return kFlowHelper::handleBulkUploadPending($dbBatchJob, $data, $entryStatus, $twinJob);
	}
	
	protected function updatedBulkUploadQueued(BatchJob $dbBatchJob, kBulkUploadJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedBulkUploadProcessing(BatchJob $dbBatchJob, kBulkUploadJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedBulkUploadProcessed(BatchJob $dbBatchJob, kBulkUploadJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedBulkUploadMoveFile(BatchJob $dbBatchJob, kBulkUploadJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedBulkUploadFinished(BatchJob $dbBatchJob, kBulkUploadJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedBulkUploadFailed(BatchJob $dbBatchJob, kBulkUploadJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedBulkUploadAborted(BatchJob $dbBatchJob, kBulkUploadJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedBulkUploadAlmostDone(BatchJob $dbBatchJob, kBulkUploadJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedBulkUploadRetry(BatchJob $dbBatchJob, kBulkUploadJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedBulkUploadFatal(BatchJob $dbBatchJob, kBulkUploadJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		return $this->updatedBulkUploadFailed($dbBatchJob, $data, $entryStatus, $twinJob);
	}
			
	protected function updatedDownload(BatchJob $dbBatchJob, $data, $entryStatus, BatchJob $twinJob=null)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_PENDING:
				return $this->updatedDownloadPending($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_QUEUED:
				return $this->updatedDownloadQueued($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_PROCESSING:
				return $this->updatedDownloadProcessing($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_PROCESSED:
				return $this->updatedDownloadProcessed($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_MOVEFILE:
				return $this->updatedDownloadMoveFile($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return $this->updatedDownloadFinished($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FAILED:
				return $this->updatedDownloadFailed($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_ABORTED:
				return $this->updatedDownloadAborted($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_ALMOST_DONE:
				return $this->updatedDownloadAlmostDone($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_RETRY:
				return $this->updatedDownloadRetry($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return $this->updatedDownloadFatal($dbBatchJob, $data, $entryStatus, $twinJob);
			default:
				return $dbBatchJob;
		}
	}
	
	protected function updatedDownloadPending(BatchJob $dbBatchJob, kDownloadJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedDownloadQueued(BatchJob $dbBatchJob, kDownloadJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedDownloadProcessing(BatchJob $dbBatchJob, kDownloadJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedDownloadProcessed(BatchJob $dbBatchJob, kDownloadJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedDownloadMoveFile(BatchJob $dbBatchJob, kDownloadJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedDownloadFinished(BatchJob $dbBatchJob, kDownloadJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedDownloadFailed(BatchJob $dbBatchJob, kDownloadJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedDownloadAborted(BatchJob $dbBatchJob, kDownloadJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedDownloadAlmostDone(BatchJob $dbBatchJob, kDownloadJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedDownloadRetry(BatchJob $dbBatchJob, kDownloadJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedDownloadFatal(BatchJob $dbBatchJob, kDownloadJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		return $this->updatedDownloadFailed($dbBatchJob, $data, $entryStatus, $twinJob);
	}
			
	protected function updatedConvertCollection(BatchJob $dbBatchJob, kConvertCollectionJobData $data, $entryStatus, BatchJob $twinJob=null)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_PENDING:
				return $this->updatedConvertCollectionPending($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_QUEUED:
				return $this->updatedConvertCollectionQueued($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_PROCESSING:
				return $this->updatedConvertCollectionProcessing($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_PROCESSED:
				return $this->updatedConvertCollectionProcessed($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_MOVEFILE:
				return $this->updatedConvertCollectionMoveFile($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return $this->updatedConvertCollectionFinished($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FAILED:
				return $this->updatedConvertCollectionFailed($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_ABORTED:
				return $this->updatedConvertCollectionAborted($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_ALMOST_DONE:
				return $this->updatedConvertCollectionAlmostDone($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_RETRY:
				return $this->updatedConvertCollectionRetry($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return $this->updatedConvertCollectionFatal($dbBatchJob, $data, $entryStatus, $twinJob);
			default:
				return $dbBatchJob;
		}
	}
	
	protected function updatedConvertCollectionPending(BatchJob $dbBatchJob, kConvertCollectionJobData $data, $entryStatus, BatchJob $twinJob = null)
	{ 
		return kFlowHelper::handleConvertCollectionPending($dbBatchJob, $data, $entryStatus, $twinJob); 
	}
	
	protected function updatedConvertCollectionQueued(BatchJob $dbBatchJob, kConvertCollectionJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedConvertCollectionProcessing(BatchJob $dbBatchJob, kConvertCollectionJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedConvertCollectionProcessed(BatchJob $dbBatchJob, kConvertCollectionJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedConvertCollectionMoveFile(BatchJob $dbBatchJob, kConvertCollectionJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedConvertCollectionFinished(BatchJob $dbBatchJob, kConvertCollectionJobData $data, $entryStatus, BatchJob $twinJob = null)
	{ 
		return kFlowHelper::handleConvertCollectionFinished($dbBatchJob, $data, $entryStatus, $twinJob); 
	}
	
	protected function updatedConvertCollectionFailed(BatchJob $dbBatchJob, kConvertCollectionJobData $data, $entryStatus, BatchJob $twinJob = null)
	{ 
		return kFlowHelper::handleConvertCollectionFailed($dbBatchJob, $data, $entryStatus, $twinJob); 
	}
	
	protected function updatedConvertCollectionAborted(BatchJob $dbBatchJob, kConvertCollectionJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedConvertCollectionAlmostDone(BatchJob $dbBatchJob, kConvertCollectionJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedConvertCollectionRetry(BatchJob $dbBatchJob, kConvertCollectionJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedConvertCollectionFatal(BatchJob $dbBatchJob, kConvertCollectionJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
		
			
	protected function updatedConvertProfile(BatchJob $dbBatchJob, kConvertProfileJobData $data, $entryStatus, BatchJob $twinJob=null)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_PENDING:
				return $this->updatedConvertProfilePending($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_QUEUED:
				return $this->updatedConvertProfileQueued($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_PROCESSING:
				return $this->updatedConvertProfileProcessing($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_PROCESSED:
				return $this->updatedConvertProfileProcessed($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_MOVEFILE:
				return $this->updatedConvertProfileMoveFile($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return $this->updatedConvertProfileFinished($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FAILED:
				return $this->updatedConvertProfileFailed($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_ABORTED:
				return $this->updatedConvertProfileAborted($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_ALMOST_DONE:
				return $this->updatedConvertProfileAlmostDone($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_RETRY:
				return $this->updatedConvertProfileRetry($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return $this->updatedConvertProfileFatal($dbBatchJob, $data, $entryStatus, $twinJob);
			default:
				return $dbBatchJob;
		}
	}
	
	protected function updatedConvertProfilePending(BatchJob $dbBatchJob, kConvertProfileJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		$dbBatchJob = kFlowHelper::handleConvertProfilePending($dbBatchJob, $data, $entryStatus, $twinJob);
		
		return $dbBatchJob;
	}
	
	protected function updatedConvertProfileQueued(BatchJob $dbBatchJob, kConvertProfileJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedConvertProfileProcessing(BatchJob $dbBatchJob, kConvertProfileJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedConvertProfileProcessed(BatchJob $dbBatchJob, kConvertProfileJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedConvertProfileMoveFile(BatchJob $dbBatchJob, kConvertProfileJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedConvertProfileFinished(BatchJob $dbBatchJob, kConvertProfileJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		return kFlowHelper::handleConvertProfileFinished($dbBatchJob, $data, $entryStatus, $twinJob);
	}
	
	protected function updatedConvertProfileFailed(BatchJob $dbBatchJob, kConvertProfileJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		return kFlowHelper::handleConvertProfileFailed($dbBatchJob, $data, $entryStatus, $twinJob);
	}
	
	protected function updatedConvertProfileAborted(BatchJob $dbBatchJob, kConvertProfileJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedConvertProfileAlmostDone(BatchJob $dbBatchJob, kConvertProfileJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedConvertProfileRetry(BatchJob $dbBatchJob, kConvertProfileJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedConvertProfileFatal(BatchJob $dbBatchJob, kConvertProfileJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		return $this->updatedConvertProfileFailed($dbBatchJob, $data, $entryStatus, $twinJob);
	}
		
	protected function updatedRemoteConvert(BatchJob $dbBatchJob, kRemoteConvertJobData $data, $entryStatus, BatchJob $twinJob=null)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_PENDING:
				return $this->updatedRemoteConvertPending($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_QUEUED:
				return $this->updatedRemoteConvertQueued($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_PROCESSING:
				return $this->updatedRemoteConvertProcessing($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_PROCESSED:
				return $this->updatedRemoteConvertProcessed($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_MOVEFILE:
				return $this->updatedRemoteConvertMoveFile($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return $this->updatedRemoteConvertFinished($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FAILED:
				return $this->updatedRemoteConvertFailed($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_ABORTED:
				return $this->updatedRemoteConvertAborted($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_ALMOST_DONE:
				return $this->updatedRemoteConvertAlmostDone($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_RETRY:
				return $this->updatedRemoteConvertRetry($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return $this->updatedRemoteConvertFatal($dbBatchJob, $data, $entryStatus, $twinJob);
			default:
				return $dbBatchJob;
		}
	}
	
	protected function updatedRemoteConvertPending(BatchJob $dbBatchJob, kRemoteConvertJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		return kFlowHelper::handleRemoteConvertPending($dbBatchJob, $data, $entryStatus, $twinJob);
	}
	
	protected function updatedRemoteConvertQueued(BatchJob $dbBatchJob, kRemoteConvertJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedRemoteConvertProcessing(BatchJob $dbBatchJob, kRemoteConvertJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedRemoteConvertProcessed(BatchJob $dbBatchJob, kRemoteConvertJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedRemoteConvertMoveFile(BatchJob $dbBatchJob, kRemoteConvertJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedRemoteConvertFinished(BatchJob $dbBatchJob, kRemoteConvertJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedRemoteConvertFailed(BatchJob $dbBatchJob, kRemoteConvertJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedRemoteConvertAborted(BatchJob $dbBatchJob, kRemoteConvertJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedRemoteConvertAlmostDone(BatchJob $dbBatchJob, kRemoteConvertJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedRemoteConvertRetry(BatchJob $dbBatchJob, kRemoteConvertJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedRemoteConvertFatal(BatchJob $dbBatchJob, kRemoteConvertJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		return $this->updatedRemoteConvertFailed($dbBatchJob, $data, $entryStatus, $twinJob);
	}
		
	protected function updatedBulkDownload(BatchJob $dbBatchJob, kBulkDownloadJobData $data, $entryStatus, BatchJob $twinJob=null)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_PENDING:
				return $this->updatedBulkDownloadPending($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_QUEUED:
				return $this->updatedBulkDownloadQueued($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_PROCESSING:
				return $this->updatedBulkDownloadProcessing($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_PROCESSED:
				return $this->updatedBulkDownloadProcessed($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_MOVEFILE:
				return $this->updatedBulkDownloadMoveFile($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return $this->updatedBulkDownloadFinished($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FAILED:
				return $this->updatedBulkDownloadFailed($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_ABORTED:
				return $this->updatedBulkDownloadAborted($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_ALMOST_DONE:
				return $this->updatedBulkDownloadAlmostDone($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_RETRY:
				return $this->updatedBulkDownloadRetry($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return $this->updatedBulkDownloadFatal($dbBatchJob, $data, $entryStatus, $twinJob);
			default:
				return $dbBatchJob;
		}
	}
	
	protected function updatedBulkDownloadPending(BatchJob $dbBatchJob, kBulkDownloadJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		return kFlowHelper::handleBulkDownloadPending($dbBatchJob, $data, $entryStatus, $twinJob);
	}
	
	protected function updatedBulkDownloadQueued(BatchJob $dbBatchJob, kBulkDownloadJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedBulkDownloadProcessing(BatchJob $dbBatchJob, kBulkDownloadJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedBulkDownloadProcessed(BatchJob $dbBatchJob, kBulkDownloadJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedBulkDownloadMoveFile(BatchJob $dbBatchJob, kBulkDownloadJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedBulkDownloadFinished(BatchJob $dbBatchJob, kBulkDownloadJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		return kFlowHelper::handleBulkDownloadFinished($dbBatchJob, $data, $entryStatus, $twinJob);
	}
	
	protected function updatedBulkDownloadFailed(BatchJob $dbBatchJob, kBulkDownloadJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedBulkDownloadAborted(BatchJob $dbBatchJob, kBulkDownloadJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedBulkDownloadAlmostDone(BatchJob $dbBatchJob, kBulkDownloadJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedBulkDownloadRetry(BatchJob $dbBatchJob, kBulkDownloadJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedBulkDownloadFatal(BatchJob $dbBatchJob, kBulkDownloadJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		return $this->updatedBulkDownloadFailed($dbBatchJob, $data, $entryStatus, $twinJob);
	}
	
		
	protected function updatedProvisionDelete(BatchJob $dbBatchJob, kProvisionJobData $data, $entryStatus, BatchJob $twinJob=null)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_PENDING:
				return $this->updatedProvisionDeletePending($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_QUEUED:
				return $this->updatedProvisionDeleteQueued($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_PROCESSING:
				return $this->updatedProvisionDeleteProcessing($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_PROCESSED:
				return $this->updatedProvisionDeleteProcessed($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_MOVEFILE:
				return $this->updatedProvisionDeleteMoveFile($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return $this->updatedProvisionDeleteFinished($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FAILED:
				return $this->updatedProvisionDeleteFailed($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_ABORTED:
				return $this->updatedProvisionDeleteAborted($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_ALMOST_DONE:
				return $this->updatedProvisionDeleteAlmostDone($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_RETRY:
				return $this->updatedProvisionDeleteRetry($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return $this->updatedProvisionDeleteFatal($dbBatchJob, $data, $entryStatus, $twinJob);
			default:
				return $dbBatchJob;
		}
	}
	
	protected function updatedProvisionDeletePending(BatchJob $dbBatchJob, kProvisionJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedProvisionDeleteQueued(BatchJob $dbBatchJob, kProvisionJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedProvisionDeleteProcessing(BatchJob $dbBatchJob, kProvisionJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedProvisionDeleteProcessed(BatchJob $dbBatchJob, kProvisionJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedProvisionDeleteMoveFile(BatchJob $dbBatchJob, kProvisionJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedProvisionDeleteFinished(BatchJob $dbBatchJob, kProvisionJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedProvisionDeleteFailed(BatchJob $dbBatchJob, kProvisionJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedProvisionDeleteAborted(BatchJob $dbBatchJob, kProvisionJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedProvisionDeleteAlmostDone(BatchJob $dbBatchJob, kProvisionJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedProvisionDeleteRetry(BatchJob $dbBatchJob, kProvisionJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedProvisionDeleteFatal(BatchJob $dbBatchJob, kProvisionJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		return $this->updatedProvisionDeleteFailed($dbBatchJob, $data, $entryStatus, $twinJob);
	}
	
		
	protected function updatedProvisionProvide(BatchJob $dbBatchJob, kProvisionJobData $data, $entryStatus, BatchJob $twinJob=null)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_PENDING:
				return $this->updatedProvisionProvidePending($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_QUEUED:
				return $this->updatedProvisionProvideQueued($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_PROCESSING:
				return $this->updatedProvisionProvideProcessing($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_PROCESSED:
				return $this->updatedProvisionProvideProcessed($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_MOVEFILE:
				return $this->updatedProvisionProvideMoveFile($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return $this->updatedProvisionProvideFinished($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FAILED:
				return $this->updatedProvisionProvideFailed($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_ABORTED:
				return $this->updatedProvisionProvideAborted($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_ALMOST_DONE:
				return $this->updatedProvisionProvideAlmostDone($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_RETRY:
				return $this->updatedProvisionProvideRetry($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return $this->updatedProvisionProvideFatal($dbBatchJob, $data, $entryStatus, $twinJob);
			default:
				return $dbBatchJob;
		}
	}
	
	protected function updatedProvisionProvidePending(BatchJob $dbBatchJob, kProvisionJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedProvisionProvideQueued(BatchJob $dbBatchJob, kProvisionJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedProvisionProvideProcessing(BatchJob $dbBatchJob, kProvisionJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedProvisionProvideProcessed(BatchJob $dbBatchJob, kProvisionJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedProvisionProvideMoveFile(BatchJob $dbBatchJob, kProvisionJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedProvisionProvideFinished(BatchJob $dbBatchJob, kProvisionJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		return kFlowHelper::handleProvisionProvideFinished($dbBatchJob, $data, $entryStatus, $twinJob);
	}
	
	protected function updatedProvisionProvideFailed(BatchJob $dbBatchJob, kProvisionJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		return kFlowHelper::handleProvisionProvideFailed($dbBatchJob, $data, $entryStatus, $twinJob);
	}
	
	protected function updatedProvisionProvideAborted(BatchJob $dbBatchJob, kProvisionJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedProvisionProvideAlmostDone(BatchJob $dbBatchJob, kProvisionJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedProvisionProvideRetry(BatchJob $dbBatchJob, kProvisionJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedProvisionProvideFatal(BatchJob $dbBatchJob, kProvisionJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		return $this->updatedProvisionProvideFailed($dbBatchJob, $data, $entryStatus, $twinJob);
	}
	
	/**
	 * @param BatchJob $dbBatchJob
	 * @param int $entryStatus
	 * @param BatchJob $twinJob
	 * @return bool true if should continue to the next consumer
	 */
	public function updatedJob(BatchJob $dbBatchJob, $entryStatus, BatchJob $twinJob = null)
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
				
				kJobsManager::abortChildJobs($dbBatchJob);
			}
			
			if ($entryStatus )
				kBatchManager::updateEntry($dbBatchJob, $entryStatus);
			
			switch($jobType)
			{
				case BatchJobType::IMPORT:
					$dbBatchJob = $this->updatedImport($dbBatchJob, $dbBatchJob->getData(), $entryStatus, $twinJob);
					break;
			
				case BatchJobType::EXTRACT_MEDIA:
					$dbBatchJob = $this->updatedExtractMedia($dbBatchJob, $dbBatchJob->getData(), $entryStatus, $twinJob);
					break;
			
				case BatchJobType::CONVERT:
					$dbBatchJob = $this->updatedConvert($dbBatchJob, $dbBatchJob->getData(), $entryStatus, $twinJob);
					break;
			
				case BatchJobType::POSTCONVERT:
					$dbBatchJob = $this->updatedPostConvert($dbBatchJob, $dbBatchJob->getData(), $entryStatus, $twinJob);
					break;
			
				case BatchJobType::PULL:
					$dbBatchJob = $this->updatedPull($dbBatchJob, $dbBatchJob->getData(), $entryStatus, $twinJob);
					break;
					
				case BatchJobType::DELETE:
//					$dbBatchJob = $this->updatedDelete($dbBatchJob, $dbBatchJob->getData(), $entryStatus, $twinJob);
					break;
					
				case BatchJobType::FLATTEN:
					$dbBatchJob = $this->updatedFlatten($dbBatchJob, $dbBatchJob->getData(), $entryStatus, $twinJob);
					break;
					
				case BatchJobType::BULKUPLOAD:
					$dbBatchJob = $this->updatedBulkUpload($dbBatchJob, $dbBatchJob->getData(), $entryStatus, $twinJob);
					break;
					
				case BatchJobType::DOWNLOAD:
					$dbBatchJob = $this->updatedDownload($dbBatchJob, $dbBatchJob->getData(), $entryStatus, $twinJob);
					break;
					
				case BatchJobType::CONVERT_PROFILE:
					$dbBatchJob = $this->updatedConvertProfile($dbBatchJob, $dbBatchJob->getData(), $entryStatus, $twinJob);
					break;
					
				case BatchJobType::REMOTE_CONVERT:
					$dbBatchJob = $this->updatedRemoteConvert($dbBatchJob, $dbBatchJob->getData(), $entryStatus, $twinJob);
					break;
					
				case BatchJobType::BULKDOWNLOAD:
					$dbBatchJob = $this->updatedBulkDownload($dbBatchJob, $dbBatchJob->getData(), $entryStatus, $twinJob);
					break;
					
				case BatchJobType::PROVISION_PROVIDE:
					$dbBatchJob = $this->updatedProvisionProvide($dbBatchJob, $dbBatchJob->getData(), $entryStatus, $twinJob);
					break;
					
				case BatchJobType::PROVISION_DELETE:
					$dbBatchJob = $this->updatedProvisionDelete($dbBatchJob, $dbBatchJob->getData(), $entryStatus, $twinJob);
					break;
					
				case BatchJobType::CONVERT_COLLECTION:
					$dbBatchJob = $this->updatedConvertCollection($dbBatchJob, $dbBatchJob->getData(), $entryStatus, $twinJob);
					break;
					
				case BatchJobType::STORAGE_EXPORT:
					$dbBatchJob = $this->updatedStorageExport($dbBatchJob, $dbBatchJob->getData(), $entryStatus, $twinJob);
					break;
					
				case BatchJobType::STORAGE_DELETE:
					$dbBatchJob = $this->updatedStorageDelete($dbBatchJob, $dbBatchJob->getData(), $entryStatus, $twinJob);
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