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
			} catch ( KalturaException $e ) {
				switch ($e->getCode ()) {
					case KalturaBatchJobAppErrors::ABORTED : // if the job was aborted
						KalturaLog::NOTICE ( "Job was aborted stoping batch" );
						break;
					default :
						KalturaLog::ERR ( "An exception was raised in startBulkUpload: " . $e );
						break;
				}
				ini_set ( 'auto_detect_line_endings', false );
				return false;
			}
		}
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
	
		//TODO: Merge this changes of TanTan to the CSV plugin
	//		$bulkUploadResultChunk[] = $bulkUploadResult;
	//	
	//		$bulkResource = new KalturaBulkResource();
	//		$bulkResource->url = $bulkUploadResult->url;
	//		$bulkResource->bulkUploadId = $job->id;
	//		$this->kClient->media->add($mediaEntry, $bulkResource);
	//		$multiRequestCounter ++;
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
