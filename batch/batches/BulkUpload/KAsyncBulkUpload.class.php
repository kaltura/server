<?php
/**
 * @package Scheduler
 * @subpackage Bulk-Upload
 */
require_once ("bootstrap.php");

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
	 * @see KBatchBase::getJobType()
	 */
	public function getJobType()
	{
		return self::getType();
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
			$this->unimpersonate();
			$job = $this->closeJob($job, null, null, null, KalturaBatchJobStatus::ABORTED);
		}
		catch(KalturaBatchException $kbex)
		{
			$this->unimpersonate();
			$job = $this->closeJob($job, KalturaBatchJobErrorTypes::APP, $kbex->getCode(), "Error: " . $kbex->getMessage(), KalturaBatchJobStatus::FAILED);
		}
		catch(KalturaException $kex)
		{
			$this->unimpersonate();
			$job = $this->closeJob($job, KalturaBatchJobErrorTypes::KALTURA_API, $kex->getCode(), "Error: " . $kex->getMessage(), KalturaBatchJobStatus::FAILED);
		}
		catch(KalturaClientException $kcex)
		{
			$this->unimpersonate();
			$job = $this->closeJob($job, KalturaBatchJobErrorTypes::KALTURA_CLIENT, $kcex->getCode(), "Error: " . $kcex->getMessage(), KalturaBatchJobStatus::RETRY);
		}
		catch(Exception $ex)
		{
			$this->unimpersonate();
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
		KalturaLog::debug( "startBulkUpload($job->id)" );
		
		//Gets the right Engine instance 
		$engine = KBulkUploadEngine::getEngine($job->jobSubType, $this->taskConfig, $this->kClient, $job);
		if (is_null ( $engine )) {
			throw new KalturaException ( "Unable to find bulk upload engine", KalturaBatchJobAppErrors::ENGINE_NOT_FOUND );
		}
		$this->updateJob($job, 'Parsing file [' . $engine->getName() . ']', KalturaBatchJobStatus::QUEUED);

		$openedEntries = $this->kClient->batch->updateBulkUploadResults($job->id);
		if($openedEntries)
		{
			KalturaLog::debug("There are open objects on the job so we wait for them");
			$this->kClient->batch->resetJobExecutionAttempts($job->id, $this->getExclusiveLockKey(), $job->jobType);
			return $this->closeJob($job, null, null, null, KalturaBatchJobStatus::RETRY);
		}
			
		$engine->handleBulkUpload();
		$job = $engine->getJob();
		$data = $engine->getData();

		$countHandledObjects = $this->countCreatedObjects($job->id, $job->data->bulkUploadObjectType);
				
		if(!$countHandledObjects && !$engine->shouldRetry())
			throw new KalturaBatchException("None of the uploaded items were processed succsessfuly", KalturaBatchJobAppErrors::BULK_NO_ENTRIES_HANDLED);
		
		if($engine->shouldRetry())
		{
			KalturaLog::debug("Set the job to retry");
			$this->kClient->batch->resetJobExecutionAttempts($job->id, $this->getExclusiveLockKey(), $job->jobType);
			return $this->closeJob($job, null, null, "Retrying: ".$countHandledObjects." ".$engine->getObjectTypeTitle()." objects were handled untill now", KalturaBatchJobStatus::RETRY);
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
		return $this->kClient->batch->countBulkUploadEntries($jobId, $bulkuploadObjectType);
	}
	
}
