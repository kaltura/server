<?php
/**
 * @package Scheduler
 * @subpackage Bulk-Upload
 */

setlocale ( LC_ALL, 'en_US.UTF-8' );

/**
 * Will initiate a single bulk upload.
 * The state machine of the job is as follows:
 * get the csv, parse it and validate it
 * creates the entries
 *
 * @package Scheduler
 * @subpackage Bulk-Upload
 */
class KAsyncBulkUpload extends KJobHandlerWorker 
{
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::BULKUPLOAD;
	}

	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		ini_set('auto_detect_line_endings', true);
		try
		{
			$job = $this->startBulkUpload($job);
		}
		catch (KalturaBulkUploadAbortedException $abortedException)
		{
			self::unimpersonate();
			$job = $this->closeJob($job, null, null, null, KalturaBatchJobStatus::ABORTED);
		}
		catch(KalturaBatchException $kbex)
		{
			self::unimpersonate();
			$job = $this->closeJob($job, KalturaBatchJobErrorTypes::APP, $kbex->getCode(), "Error: " . $kbex->getMessage(), KalturaBatchJobStatus::FAILED);
		}
		catch(KalturaException $kex)
		{
			self::unimpersonate();
			$job = $this->closeJob($job, KalturaBatchJobErrorTypes::KALTURA_API, $kex->getCode(), "Error: " . $kex->getMessage(), KalturaBatchJobStatus::FAILED);
		}
		catch(KalturaClientException $kcex)
		{
			self::unimpersonate();
			$job = $this->closeJob($job, KalturaBatchJobErrorTypes::KALTURA_CLIENT, $kcex->getCode(), "Error: " . $kcex->getMessage(), KalturaBatchJobStatus::RETRY);
		}
		catch(Exception $ex)
		{
			self::unimpersonate();
			$job = $this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $ex->getCode(), "Error: " . $ex->getMessage(), KalturaBatchJobStatus::FAILED);
		}
		ini_set('auto_detect_line_endings', false);

		return $job;
	}


	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::getMaxJobsEachRun()
	 */
	protected function getMaxJobsEachRun()
	{
		return 1;
	}
	
	/**
	 * 
	 * Starts the bulk upload
	 * @param KalturaBatchJob $job
	 */
	private function startBulkUpload(KalturaBatchJob $job)
	{
		KalturaLog::info( "Start bulk upload ($job->id)" );
		
		//Gets the right Engine instance 
		$engine = KBulkUploadEngine::getEngine($job->jobSubType, $job);
		if (is_null ( $engine )) {
			throw new KalturaException ( "Unable to find bulk upload engine", KalturaBatchJobAppErrors::ENGINE_NOT_FOUND );
		}
		$job = $this->updateJob($job, 'Parsing file [' . $engine->getName() . ']', KalturaBatchJobStatus::QUEUED, $engine->getData());
		
		$engine->setJob($job);
		$engine->setData($job->data);
		$engine->handleBulkUpload();
		
		$job = $engine->getJob();
		$data = $engine->getData();

		$countObjects = $this->countCreatedObjects($job->id, $job->data->bulkUploadObjectType);
		$countHandledObjects = $countObjects[0];
		$countErrorObjects = $countObjects[1];

		if(!$countHandledObjects && !$engine->shouldRetry() && $countErrorObjects)
			throw new KalturaBatchException("None of the uploaded items were processed succsessfuly", KalturaBatchJobAppErrors::BULK_NO_ENTRIES_HANDLED, $engine->getData());
		
		if($engine->shouldRetry())
		{
			self::$kClient->batch->resetJobExecutionAttempts($job->id, $this->getExclusiveLockKey(), $job->jobType);
			return $this->closeJob($job, null, null, "Retrying: ".$countHandledObjects." ".$engine->getObjectTypeTitle()." objects were handled untill now", KalturaBatchJobStatus::RETRY);
		}

		//check if all items were done already
		if(!self::$kClient->batch->updateBulkUploadResults($job->id))
		{
			return $this->closeJob($job, null, null, 'Finished successfully', KalturaBatchJobStatus::FINISHED);
		}
			
		return $this->closeJob($job, null, null, 'Waiting for objects closure', KalturaBatchJobStatus::ALMOST_DONE, $data);
	}
	
	/**
	 * Return the count of created entries
	 * @param int $jobId
	 * @return int
	 */
	protected function countCreatedObjects($jobId, $bulkuploadObjectType) 
	{
		$createdCount = 0;
		$errorCount = 0;
		
		$counters = self::$kClient->batch->countBulkUploadEntries($jobId, $bulkuploadObjectType);
		foreach($counters as $counter)
		{
			/** @var KalturaKeyValue $counter */
			if ($counter->key == 'created')
				$createdCount = $counter->value;
			if ($counter->key == 'error')
				$errorCount = $counter->value;
		}
		
		return array($createdCount, $errorCount);
	}
	
}
