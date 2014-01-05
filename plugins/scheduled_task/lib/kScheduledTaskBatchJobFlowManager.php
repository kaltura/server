<?php
/**
 * @package plugins.scheduledTask
 * @subpackage lib
 */
class kScheduledTaskBatchJobFlowManager implements kBatchJobStatusEventConsumer
{
	/**
	 * @param BatchJob $dbBatchJob
	 * @return bool true if the consumer should handle the event
	 */
	public function shouldConsumeJobStatusEvent(BatchJob $dbBatchJob)
	{
		$jobType = ScheduledTaskPlugin::getBatchJobTypeCoreValue(ScheduledTaskBatchType::SCHEDULED_TASK);
		if ($dbBatchJob->getJobType() == $jobType)
			return true;

		return false;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @return bool true if should continue to the next consumer
	 */
	public function updatedJob(BatchJob $dbBatchJob)
	{
		$data = $dbBatchJob->getData();
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return self::handleJobFinished($dbBatchJob, $data);
			default:
				return $dbBatchJob;
		}
	}

	public static function handleJobFinished(BatchJob $job, kScheduledTaskJobData $data)
	{
		$resultFilePath = $data->getResultsFilePath();
		if (!file_exists($resultFilePath))
			return self::finishJobWithError($job, 'Results file was not found');

		// we are using the bulk upload sync key, as this should actually be a generic sync key for batch job object
		$syncKey = $job->getSyncKey(BatchJob::FILE_SYNC_BATCHJOB_SUB_TYPE_BULKUPLOAD);
		try
		{
			kFileSyncUtils::moveFromFile($resultFilePath, $syncKey, true);
		}
		catch(Exception $ex)
		{
			KalturaLog::err($ex);
			return self::finishJobWithError($job, 'Failed to move file: '.$ex->getMessage());
		}
		return $job;
	}

	/**
	 * @param BatchJob $job
	 * @param $errorDescription
	 * @return BatchJob
	 */
	protected function finishJobWithError(BatchJob $job, $errorDescription)
	{
		$job->setStatus(BatchJob::BATCHJOB_STATUS_FAILED);
		$job->setDescription($job->getDescription().'\n'.$errorDescription);
		$job->save();
		return $job;
	}
}
