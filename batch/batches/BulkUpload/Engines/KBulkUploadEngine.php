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
	const BULK_UPLOAD_DATE_FORMAT = '%Y-%m-%dT%H:%i:%s';

	/**
	 * 
	 * The batch current partner id
	 * @var int
	 */
	protected $currentPartnerId;
	
	/**
	 * @var KalturaConfiguration
	 */
	protected $kClientConfig = null;
		
	/**
	 * @var int
	 */
	protected $multiRequestSize = 5;
	
	/**
	 * @var int
	 */
	protected $maxRecords = 1000;

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
	 * 
	 * @var KalturaBatchJob
	 */
	protected $job = null;
	
	/**
	 * 
	 * @var KalturaBulkUploadJobData
	 */
	protected $data = null;

	/**
	 * @param KSchedularTaskConfig $taskConfig
	 */
	public function __construct( KSchedularTaskConfig $taskConfig, KalturaClient $kClient, KalturaBatchJob $job)
	{
		$this->multiRequestSize = $taskConfig->params->multiRequestSize;
		$this->maxRecords = $taskConfig->params->maxRecords;
		
		$this->kClient = $kClient;
		$this->kClientConfig = $kClient->getConfig();
		
		$this->job = $job;
		$this->data = $job->data;
	}
	
	/**
	 * Will return the proper engine depending on the type (KalturaBulkUploadType)
	 *
	 * @param int $provider
	 * @param KSchedularTaskConfig $taskConfig - for the engine
	 * @param KalturaClient kClient - the client for the engine to use
	 * @return KBulkUploadEngine
	 */
	public static function getEngine($batchJobSubType, KSchedularTaskConfig $taskConfig, $kClient, KalturaBatchJob $job)
	{
		$engine =  null;
		
		//Gets the engine from the plugin (as we moved all engines to the plugin)
		$engine = KalturaPluginManager::loadObject('KBulkUploadEngine', $batchJobSubType, array($taskConfig, $kClient, $job));
						
		return $engine;
	}
	
	/**
	 * @return KalturaBatchJob
	 */
	public function getJob()
	{
		return $this->job;
	}

	/**
	 * @return KalturaBulkUploadJobData
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * 
	 * Handles the bulk upload
	 */
	abstract public function handleBulkUpload();
			
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
	 * Gets the start line number for the given job id
	 * @return int - the start line for the job id
	 */
	protected function getStartLineNumber()
	{
		try{
			$bulkUploadLastResult = $this->kClient->batch->getBulkUploadLastResult($this->job->id);
			return $bulkUploadLastResult->lineIndex;
		}
		catch(Exception $e){
			KalturaLog::notice("getBulkUploadLastResult: " . $e->getMessage());
			return 0;
		}
	}
	
	/**
	 * 
	 * Gets the number of current multy request counter and decides if to send the chunked data or not
	 * @return bool - true if the chunked data was sent, false if the data is not sent and ERROR on error
	 * 
	 */
	protected function sendChunkedData()
	{
		// send chunk of requests
		if($this->kClient->getMultiRequestQueueSize() >= $this->multiRequestSize)
		{
			$this->kClient->doMultiRequest();
			
			KalturaLog::info("Sent $this->multiRequestCounter invalid lines results");
			
			// check if job aborted
			$this->isAborted();
			
			// start a new multi request
			$this->kClient->startMultiRequest();
			
			$this->multiRequestCounter = 0;
		}
	}

	/**
	 * 
	 * Gets the number of current multy request counter and decides if to send the chunked data or not
	 * @param array $bulkUploadResultChunk
	 */
	protected function sendChunkedDataForPartner(array $bulkUploadResultChunk)
	{
		// send chunk of requests
		if($this->kClient->getMultiRequestQueueSize() >= $this->multiRequestSize)
		{
			// commit the multi request entries
			$requestResults = $this->doMultiRequestForPartner();
			
			if(count($requestResults) != count($bulkUploadResultChunk))
			{
				$err = __FILE__ . ', line: ' . __LINE__ . ' $requestResults and $$bulkUploadResultChunk must have the same size';
				throw new KalturaException($err, KalturaBatchJobAppErrors::BULK_INVLAID_BULK_REQUEST_COUNT);
			}
				
			// saving the results with the created enrty ids
			$this->updateEntriesResults($requestResults, $bulkUploadResultChunk);
					
			// check if job aborted
			$this->checkAborted();
			
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
	protected function startMultiRequest($isSpecificForPartner = false)
	{
		if($isSpecificForPartner)
		{
			$this->kClientConfig->partnerId = $this->currentPartnerId;
			$this->kClient->setConfig($this->kClientConfig);
		}
		
		$this->kClient->startMultiRequest();
	}
	
	/**
	 * @return array
	 */
	protected function doMultiRequestForPartner()
	{
		$requestResults = $this->kClient->doMultiRequest();
		
		$this->kClientConfig->partnerId = $this->currentPartnerId;
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
	 * 
	 * Checks if the job was aborted (throws exception if so)
	 * @throws KalturaBulkUploadAbortedException
	 */
	protected function checkAborted()
	{
		if($this->kClient->isMultiRequest())
			$this->kClient->doMultiRequest();
			
		$batchJobResponse = $this->kClient->jobs->getBulkUploadStatus($this->job->id);
		$updatedJob = $batchJobResponse->batchJob;
		if($updatedJob->abort)
		{
			KalturaLog::info("job[{$this->job->id}] aborted");
				
			//Throw exception and close the job from the outside 
			throw new KalturaBulkUploadAbortedException("Job was aborted", KalturaBulkUploadAbortedException::JOB_ABORTED);
		}
		return false;
	}
}