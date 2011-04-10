<?php
/**
 * base class for the real KBulkUploadEngine in the system 
 * 
 * @package Scheduler
 * @subpackage BulkUpload
 * @abstract
 */
abstract class KBulkUploadEngine
{
	/**
	 * @var KSchedularTaskConfig
	 */
	protected $taskConfig = null;

	/**
	 * 
	 * The Engine client
	 * @var KalturaClient
	 */
	protected $kClient; 
	
	/**
	 * 
	 * The multirequest counter
	 * @var int
	 */
	protected $multiRequestCounter = 0;
	
	/**
	 * Will return the proper engine depending on the type (KalturaBulkUploadType)
	 *
	 * @param int $provider
	 * @param KSchedularTaskConfig $taskConfig - for the engine
	 * @param KalturaClient kClient - the client for the engine to use
	 * @return KBulkUploadEngine
	 */
	public static function getEngine ( $batchJobSubType , KSchedularTaskConfig $taskConfig, $kClient )
	{
		$engine =  null;
		
		//Gets the engine from the plugin (as we moved all engines to the plugin)
		$engine = KalturaPluginManager::loadObject('KBulkUploadEngine', $batchJobSubType, array($taskConfig, $kClient));
						
		return $engine;
	}

	/**
	 * @param KSchedularTaskConfig $taskConfig
	 */
	protected function __construct( KSchedularTaskConfig $taskConfig, $kClient )
	{
		$this->taskConfig = $taskConfig;
		$this->kClient = $kClient;
	}
	
	/**
	 * @return string
	 */
	abstract public function getName();
	
	/**
	 * 
	 * @param KalturaBatchJob $job
	 * @param KalturaBulkUploadJobData $data
	 * @return KBulkUploadEningeResult
	 */
	public function handleBulkUpload( KalturaBatchJob $job, KalturaBulkUploadJobData $data )
	{
		//TODO: Roni create the flow:
		//0. Init - maybe even give client :)
		//1. Validate - even if empty
		//2. Parse rows
		//3. Close
		
		$this->init($job, $data);
		$this->validateFile($job, $data);
		$this->parseRows($job, $data);
		$this->close($job, $data);
	}

	/**
	 * 
	 * Validates the given file for the job
	 * @param KalturaBatchJob $job
	 * @param KalturaBulkUploadJobData $data
	 */
	protected function validateFile(KalturaBatchJob $job, KalturaBulkUploadJobData $data )
	{
		return;
	}
	
	/**
	 * 
	 * Parse the rows of the given bulk job file and perform the needed actions
	 * @param KalturaBatchJob $job
	 * @param KalturaBulkUploadJobData $data
	 */
	protected function parseRows(KalturaBatchJob $job, KalturaBulkUploadJobData $data )
	{
		return;
	}
	
	/**
	 * 
	 * Inits the engine with the needed params of the batch job 
	 * @param KalturaBatchJob $job
	 * @param KalturaBulkUploadJobData $data
	 */
	protected function init(KalturaBatchJob $job, KalturaBulkUploadJobData $data )
	{
		return;
	}
		
	/**
	 * 
	 * closes the engine with the needed params of the batch job 
	 * @param KalturaBatchJob $job
	 * @param KalturaBulkUploadJobData $data
	 */
	protected function close(KalturaBatchJob $job, KalturaBulkUploadJobData $data )
	{
		return;
	}
		
	/**
	 * @param string $item
	 */
	protected function trimArray(&$item)
	{
		$item = trim($item);
	}
		
	/**
	 * 
	 * Adds a bulk upload result
	 * @param KalturaBulkUploadResult $bulkUploadResult
	 */
	protected function addBulkUploadResult(KalturaBulkUploadResult $bulkUploadResult)
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
	protected function getFileHandle(KalturaBatchJob $job, KalturaBulkUploadJobData $bulkUploadJobData)
	{
		$fileHandle = fopen($bulkUploadJobData->csvFilePath, "r");
		
		if(! $fileHandle) // fails and exit
		{
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
	protected function getStartLineNumber($jobId)
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
	 * @return bool - true if the chunked data was sent, false if the data is not sent and ERROR on error
	 * 
	 */
	protected function sendChunkedData(KalturaBatchJob $job)
	{
		$multiRequestSize = $this->taskConfig->params->multiRequestSize;

		// send chunk of requests
		if($this->multiRequestCounter >= $multiRequestSize)
		{
			$this->kClient->doMultiRequest();
			
			KalturaLog::info("Sent $this->multiRequestCounter invalid lines results");
			
			// check if job aborted
			if(KAsyncBulkUpload::isAborted($job))
			{
				ini_set('auto_detect_line_endings', false);
				throw new KalturaException("Job was aborted", KalturaBatchJobAppErrors::ABORTED); //The job was aborted
			}
			
			// start a new multi request
			$this->kClient->startMultiRequest();
			
			$this->multiRequestCounter = 0;
		}
	}

	/**
	 * 
	 * Gets the number of current multy request counter and decides if to send the chunked data or not
	 * @param KalturaBatchJob $job
	 * @param array $bulkUploadResultChunk
	 */
	protected function sendChunkedDataForPartner(KalturaBatchJob $job, array $bulkUploadResultChunk)
	{
		$multiRequestSize = $this->taskConfig->params->multiRequestSize;
		
		// send chunk of requests
		if($this->multiRequestCounter > $multiRequestSize)
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
				throw new KalturaException("Job was aborted", KalturaBatchJobAppErrors::ABORTED); //The job was aborted
			}
			
			// start a new multi request
			$this->startMultiRequest(true);
			
			$bulkUploadResultChunk = array();
			$this->multiRequestCounter = 0;
		}
	}
	
	/**
	 * 
	 * Start a multirequest, if specified start the multi request for the job partner
	 * @param bool $isSpecificForPartner
	 */
	protected function startMultiRequest($isSpecificForPartner)
	{
		if($isSpecificForPartner)
		{
			$this->kClientConfig->partnerId = $this->taskConfig->getPartnerId();;
			$this->kClient->setConfig($this->kClientConfig);
		}
		$this->kClient->startMultiRequest();
	}
	
	/**
	 * @return array
	 */
	protected function doMultiRequestForPartnerId()
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
	protected function updateEntriesResults(array $requestResults, array $bulkUploadResults)
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
	protected function isAborted(KalturaBatchJob $job)
	{
		$batchJobResponse = $this->kClient->jobs->getBulkUploadStatus($job->id);
		$updatedJob = $batchJobResponse->batchJob;
		if($updatedJob->abort)
		{
			KalturaLog::info("job[$job->id] aborted");
			$this->closeJob($job, null, null, 'Aborted', KalturaBatchJobAppErrors::ABORTED);
			
			if($this->kClient->isMultiRequest())
				$this->kClient->doMultiRequest();
				
			return true;
		}
		return false;
	}
}


//TODO: Roni - see if this is needed (copied from another location)
///**
// * @package Scheduler
// * @subpackage Conversion
// *
// */
//class KBulkUploadEngineResult
//{
//	/**
//	 * @var int
//	 */
//	public $status;
//	
//	/**
//	 * @var string
//	 */
//	public $errMessage;
//	
//	/**
//	 * @var KalturaProvisionJobData
//	 */
//	public $data;
//	
//	/**
//	 * @param int $status
//	 * @param string $errMessage
//	 * @param KalturaProvisionJobData $data
//	 */
//	public function __construct( $status , $errMessage, KalturaBulkUploadJobData $data = null )
//	{
//		$this->status = $status;
//		$this->errMessage = $errMessage;
//		$this->data = $data;
//	}
//}
