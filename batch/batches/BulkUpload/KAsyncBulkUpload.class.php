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
class KAsyncBulkUpload extends KBatchBase {
	
	protected $currentPartnerId = null;
	
	/**
	 * @return number
	 */
	public static function getType() {
		return KalturaBatchJobType::BULKUPLOAD;
	}
	
	protected function init() {
		$this->saveQueueFilter ( self::getType () );
	}
	
	public function run() {
		KalturaLog::info ( "Bulk upload batch is running" );
		
		if ($this->taskConfig->isInitOnly ())
			return $this->init ();
		
		$jobs = $this->kClient->batch->getExclusiveBulkUploadJobs ( $this->getExclusiveLockKey (), $this->taskConfig->maximumExecutionTime, 1, $this->getFilter () );
		
		KalturaLog::info ( count ( $jobs ) . " bulk upload jobs to perform" );
		
		if (! count ( $jobs )) {
			KalturaLog::info ( "Queue size: 0 sent to scheduler" );
			$this->saveSchedulerQueue ( self::getType () );
			return;
		}
		
		foreach ( $jobs as $job ) {
			try {
				$this->startBulkUpload ( $job, $job->data );
				
				ini_set ( 'auto_detect_line_endings', false );
				// the closer will report finished after checking the imports and converts, reports almost done
				$this->closeJob($job, null, null, 'Waiting for imports and conversion', KalturaBatchJobStatus::ALMOST_DONE);
				
			} 
			catch ( KalturaException $e ) 
			{
				ini_set ( 'auto_detect_line_endings', false );
				$this->handleExceptions($e, $job);
				return false;
			}
		}
	}
	
	/**
	 * 
	 * Handles all exceptions raised in the bulk engines
	 * @param Exception $e
	 * @param KalturaBatchJob $job
	 */
	private function handleExceptions($e,KalturaBatchJob $job)
	{
		//TODO : fix exception handling
		KalturaLog::ERR ( "An exception was raised in bulk upload: " . $e );
		$errType = KalturaBatchJobErrorTypes::APP;
		$msg = $e->getMessage();
		$errNumber = $e->getCode();
		
		switch ($e->getCode ()) 
		{
			case KalturaBatchJobAppErrors::ABORTED : // if the job was aborted
					$status = KalturaBatchJobStatus::ABORTED;
				break;
			case KalturaBatchJobAppErrors::CSV_FILE_NOT_FOUND:
					$status = KalturaBatchJobStatus::FAILED;
				break;
			case KalturaBatchJobStatus::FAILED :
					$status = KalturaBatchJobStatus::FAILED;
				break;
			default :
				    $status = null;
				break;
		}
		$this->closeJob($job, $errType, $errNumber, $msg, $status);
	}
	
	/**
	 * 
	 * Starts the bulk upload
	 * @param KalturaBatchJob $job
	 * @param KalturaBulkUploadJobData $bulkUploadJobData
	 */
	private function startBulkUpload(KalturaBatchJob $job, KalturaBulkUploadJobData $bulkUploadJobData) {
		KalturaLog::debug ( "startBulkUpload($job->id)" );
		
		//TODO: Roni - Get from the job the job subtype (the BulkUpload Type)
		//Gets the right Engine instance 
		//$engine = KBulkUploadEngine::getEngine($bulkUploadJobData->getBulkType(), $this->taskConfig);
		$engine = KBulkUploadEngine::getEngine ( KalturaBulkUploadType::CSV, $this->taskConfig, $this->kClient, $this->kClientConfig );
		if (is_null ( $engine )) {
			//TODO: handle exceptions better
			KalturaLog::err("Bulk upload engine is null" );
			throw new Exception ( "Unable to find bulk upload engine, action aborted" );
		}
		
		$engine->handleBulkUpload ( $job, $bulkUploadJobData );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see KBatchBase::updateExclusiveJob()
	 */
	protected function updateExclusiveJob($jobId, KalturaBatchJob $job) {
		return $this->kClient->batch->updateExclusiveBulkUploadJob ( $jobId, $this->getExclusiveLockKey (), $job );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see KBatchBase::freeExclusiveJob()
	 */
	protected function freeExclusiveJob(KalturaBatchJob $job) {
		$resetExecutionAttempts = false;
		if ($job->status == KalturaBatchJobStatus::ALMOST_DONE)
			$resetExecutionAttempts = true;
		
		$response = $this->kClient->batch->freeExclusiveBulkUploadJob ( $job->id, $this->getExclusiveLockKey (), $resetExecutionAttempts );
		
		KalturaLog::info ( "Queue size: $response->queueSize sent to scheduler" );
		$this->saveSchedulerQueue ( self::getType (), $response->queueSize );
		
		return $response->job;
	}
}
