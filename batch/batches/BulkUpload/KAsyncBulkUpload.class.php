<?php
/**
 * @package Scheduler
 * @subpackage Bulk-Upload
 */
require_once ("bootstrap.php");

setlocale(LC_ALL, 'en_US.UTF-8');

/**
 * Will initiate a single bulk upload.
 * The state machine of the job is as follows:
 * 	 	get the csv, parse it and validate it
 * 		creates the entries
 *
 * @package Scheduler
 * @subpackage Bulk-Upload
 */
class KAsyncBulkUpload extends KBatchBase
{
	const VALUES_COUNT_V1 = 5;
	const VALUES_COUNT_V2 = 12;
	const BULK_UPLOAD_DATE_FORMAT = '%Y-%m-%dT%H:%i:%s';
		
	protected $currentPartnerId = null;
	
	/**
	 * @return number
	 */
	public static function getType()
	{
		return KalturaBatchJobType::BULKUPLOAD;
	}
	
	protected function init()
	{
		$this->saveQueueFilter(self::getType());
	}
	
	public function run()
	{
		KalturaLog::info("Bulk upload batch is running");
		
		if($this->taskConfig->isInitOnly())
			return $this->init();
		
		$jobs = $this->kClient->batch->getExclusiveBulkUploadJobs($this->getExclusiveLockKey(), $this->taskConfig->maximumExecutionTime, 1, $this->getFilter());
		
		KalturaLog::info(count($jobs) . " bulk upload jobs to perform");
		
		if(! count($jobs))
		{
			KalturaLog::info("Queue size: 0 sent to scheduler");
			$this->saveSchedulerQueue(self::getType());
			return;
		}
		
		foreach($jobs as $job)
		{
			try {
				$this->startBulkUpload($job, $job->data);
			}catch (KalturaException $e)
			{
				switch ($e->getCode())
				{
					case KalturaBatchJobStatus::ABORTED: // if the job was aborted
						KalturaLog::NOTICE("Job was aborted stoping batch");
						break;
					default:
						KalturaLog::ERR("An exception was raised in startBulkUpload: " . $e);
						break;
				}
				return false;
			}
		}
	}
	
	/**
	 * @param string $item
	 */
	public static function trimArray(&$item)
	{
		$item = trim($item);
	}
		
	/**
	 * 
	 * Adds a bulk upload result
	 * @param KalturaBulkUploadResult $bulkUploadResult
	 */
	public static function addBulkUploadResult(KalturaBulkUploadResult $bulkUploadResult)
	{
		$pluginsData = $bulkUploadResult->pluginsData;
		$bulkUploadResult->pluginsData = null;
		$this->kClient->batch->addBulkUploadResult($bulkUploadResult, $pluginsData);
	}
	
	/**
	 * 
	 * Gets the job and job data and returns the file to be opened
	 * @param KalturaBatchJob $job
	 * @param KalturaBulkUploadJobData $bulkUploadJobData
	 */
	public static function getFileHandle(KalturaBatchJob $job, KalturaBulkUploadJobData $bulkUploadJobData)
	{
		// reporting start of work
		$this->updateJob($job, 'Fetching file', KalturaBatchJobStatus::QUEUED, 1);
				
		$fileHandle = fopen($bulkUploadJobData->csvFilePath, "r");
		
		if(! $fileHandle) // fails and exit
		{
			ini_set('auto_detect_line_endings', false);
			$this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::CSV_FILE_NOT_FOUND, "File not found: $bulkUploadJobData->csvFilePath", KalturaBatchJobStatus::FAILED);
			throw new KalturaException("Unable to open file: {$bulkUploadJobData->csvFilePath}");
		}
					
		KalturaLog::info("Opened file: $bulkUploadJobData->csvFilePath");
		
		return $fileHandle;
	}

	/**
	 * 
	 * Gets the start line number for the given job id
	 * @param int $jobId
	 * @return int - the start line for the job id
	 */
	public static function getStartLineNumber($jobId)
	{
		//Get the last line number for the specific job id
		$startLineNumber = 0;
		$bulkUploadLastResult = null;
		try{
			$bulkUploadLastResult = $this->kClient->batch->getBulkUploadLastResult($job->id);
		}
		catch(Exception $e){
			KalturaLog::err("getBulkUploadLastResult: " . $e->getMessage());
		}
		
		if($bulkUploadLastResult)
			$startLineNumber = $bulkUploadLastResult->lineIndex;
		
		return $startLineNumber;
	}
	
	/**
	 * 
	 * Gets the number of current multy request counter and decides if to send the chunked data or not
	 * @param int $multiRequestCounter
	 * @return bool - true if the chunked data was sent, false if the data is not sent and ERROR on error
	 * 
	 */
	public static function trySendChunkedData($multiRequestCounter, KalturaBatchJob $job)
	{
		$multiRequestSize = $this->taskConfig->params->multiRequestSize;

		// send chunk of requests
		if($multiRequestCounter >= $multiRequestSize)
		{
			$this->kClient->doMultiRequest();
			
			KalturaLog::info("Sent $multiRequestCounter invalid lines results");
			
			// check if job aborted
			if(KAsyncBulkUpload::isAborted($job))
			{
				ini_set('auto_detect_line_endings', false);
				throw new KalturaException("Job was aborted", KalturaBatchJobStatus::ABORTED); //The job swas aborted
			}
			
			// start a new multi request
			$this->kClient->startMultiRequest();
			
			$multiRequestCounter = 0;
		}
		
		return $multiRequestCounter;
	}

	/**
	 * 
	 * Starts a multi request for the bulk client
	 */
	public static function startMultiRequest()
	{
		$this->kClient->startMultiRequest();
	}
	
	/**
	 * 
	 * Gets the number of current multy request counter and decides if to send the chunked data or not
	 * @param unknown_type $multiRequestCounter
	 * @param KalturaBatchJob $job
	 * @param array $bulkUploadResultChunk
	 * @return the new multiRequestCounter and -1 on error
	 */
	public static function trySendChunkedDataForPartner($multiRequestCounter, KalturaBatchJob $job, array $bulkUploadResultChunk, $partnerId)
	{
		$multiRequestSize = $this->taskConfig->params->multiRequestSize;
		
		// send chunk of requests
		if($multiRequestCounter > $multiRequestSize)
		{
			// commit the multi request entries
			$requestResults = KAsyncBulkUpload::doMultiRequestForPartnerId();
			
			if(count($requestResults) != count($bulkUploadResultChunk))
			{
				ini_set('auto_detect_line_endings', false);
				$err = __FILE__ . ', line: ' . __LINE__ . ' $requestResults and $$bulkUploadResultChunk must have the same size';
				$this->closeJob($job, KalturaBatchJobErrorTypes::APP, null, $err, KalturaBatchJobStatus::FAILED);
				throw new KalturaException("Error On trySendChunkedDataForPartner");
			}
				
			// saving the results with the created enrty ids
			KAsyncBulkUpload::updateEntriesResults($requestResults, $bulkUploadResultChunk);
					
			// check if job aborted
			if(KAsyncBulkUpload::isAborted($job))
			{
				ini_set('auto_detect_line_endings', false);
				throw new KalturaException("Job was aborted", KalturaBatchJobStatus::ABORTED); //The job swas aborted
			}
			
			// start a new multi request
			$this->startMultiRequestForPartnerId();
			
			$bulkUploadResultChunk = array();
			$multiRequestCounter = 0;
		}
		
		return $multiRequestCounter;
	}
	
	/**
	 * 
	 * Starts a multi request for the specific partner in the task config
	 */
	public static function startMultiRequestForPartnerId()
	{
		$this->kClientConfig->partnerId = $this->taskConfig->getPartnerId();;
		$this->kClient->setConfig($this->kClientConfig);
		
		$this->kClient->startMultiRequest();
	}
	
	/**
	 * @return array
	 */
	public static function doMultiRequestForPartnerId()
	{
		$requestResults = $this->kClient->doMultiRequest();
		
		$this->kClientConfig->partnerId = $this->taskConfig->getPartnerId();
		$this->kClient->setConfig($this->kClientConfig);
		
		return $requestResults;
	}
	
	/**
	 * save the results for returned created entries
	 * 
	 * @param array $requestResults
	 * @param array $bulkUploadResults
	 */
	public static function updateEntriesResults(array $requestResults, array $bulkUploadResults)
	{
		KalturaLog::debug("updateEntriesResults(" . count($requestResults) . ", " . count($bulkUploadResults) . ")");
		
		$this->kClient->startMultiRequest();
		
		KalturaLog::info("Updating " . count($requestResults) . " results");
		
		// checking the created entries
		foreach($requestResults as $index => $requestResult)
		{
			$bulkUploadResult = $bulkUploadResults[$index];
			
			if($requestResult instanceof Exception)
			{
				$bulkUploadResult->entryStatus = KalturaEntryStatus::ERROR_IMPORTING;
				$bulkUploadResult->errorDescription = $requestResult->getMessage();
				$this->addBulkUploadResult($bulkUploadResult);
				continue;
			}
			
			if(! ($requestResult instanceof KalturaMediaEntry))
			{
				$bulkUploadResult->entryStatus = KalturaEntryStatus::ERROR_IMPORTING;
				$bulkUploadResult->errorDescription = "Returned type is " . get_class($requestResult) . ', KalturaMediaEntry was expected';
				$this->addBulkUploadResult($bulkUploadResult);
				continue;
			}
			
			// update the results with the new entry id
			$bulkUploadResult->entryId = $requestResult->id;
			$this->addBulkUploadResult($bulkUploadResult);
		}
		$this->kClient->doMultiRequest();
	}
	
	/**
	 * @param KalturaBatchJob $job
	 * @return boolean
	 */
	public static function isAborted(KalturaBatchJob $job)
	{
		$batchJobResponse = $this->kClient->jobs->getBulkUploadStatus($job->id);
		$updatedJob = $batchJobResponse->batchJob;
		if($updatedJob->abort)
		{
			KalturaLog::info("job[$job->id] aborted");
			$this->closeJob($job, null, null, 'Aborted', KalturaBatchJobStatus::ABORTED);
			
			if($this->kClient->isMultiRequest())
				$this->kClient->doMultiRequest();
				
			return true;
		}
		return false;
	}
		
	/**
	 * 
	 * Starts the bulk upload
	 * @param KalturaBatchJob $job
	 * @param KalturaBulkUploadJobData $bulkUploadJobData
	 */
	private function startBulkUpload(KalturaBatchJob $job, KalturaBulkUploadJobData $bulkUploadJobData)
	{
		KalturaLog::debug("startBulkUpload($job->id)");
		
		//Gets the right Engine instance 
//		$engine = KBulkUploadEngine::getInstance($bulkUploadJobData->getBulkType(), $this->taskConfig);
		$engine = KBulkUploadEngine::getInstance(KalturaBulkUploadType::CSV, $this->taskConfig);
		if(is_null($engine))
		{
			//TODO: handle exceptions
			KalturaLog::ERROR("Bulk upload engine is null");
			throw new KalturaException("Unable to find bulk upload engine, action aborted");
//			$engine = new KBulkUploadEngineCsv($this->taskConfig);
		}
	
		$engine->handleBulkUpload($job, $bulkUploadJobData);

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
	protected function updateExclusiveJob($jobId, KalturaBatchJob $job)
	{
		return $this->kClient->batch->updateExclusiveBulkUploadJob($jobId, $this->getExclusiveLockKey(), $job);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see KBatchBase::freeExclusiveJob()
	 */
	protected function freeExclusiveJob(KalturaBatchJob $job)
	{
		$resetExecutionAttempts = false;
		if($job->status == KalturaBatchJobStatus::ALMOST_DONE)
			$resetExecutionAttempts = true;
			
		$response = $this->kClient->batch->freeExclusiveBulkUploadJob($job->id, $this->getExclusiveLockKey(), $resetExecutionAttempts);
		
		KalturaLog::info("Queue size: $response->queueSize sent to scheduler");
		$this->saveSchedulerQueue(self::getType(), $response->queueSize);
		
		return $response->job;
	}
}
