<?php
/**
 * Class which parses the bulk upload Filter and creates the objects listed in it.
 *
 * @package plugins.bulkUploadFilter
 * @subpackage batch
 */
abstract class BulkUploadEngineFilter extends KBulkUploadEngine
{
	/**
	 * The bulk upload results
	 * @var array
	 */
	protected $bulkUploadResults = array();
	
	protected $handledObjectsCount;
	
	protected $startIndex;
			

	/* (non-PHPdoc)
	 * @see KBulkUploadEngine::handleBulkUpload()
	 */
	public function handleBulkUpload()
	{
		$this->startIndex = $this->getStartIndex($this->job->id);
		
		$this->processObjectsList();
		
		// send all invalid results
		KBatchBase::$kClient->doMultiRequest();
		
		KalturaLog::info("Extracted objects by filter, $this->handledObjectsCount lines with " . ($this->handledObjectsCount - count($this->bulkUploadResults)) . ' invalid records');
				
		//Check if job aborted
		$this->checkAborted();

		//Create the objects from the bulk upload results
		$this->createObjects();
	}
		
	/* (non-PHPdoc)
	 * @see KBulkUploadEngine::addBulkUploadResult()
	 */
	protected function addBulkUploadResult(KalturaBulkUploadResult $bulkUploadResult)
	{
		parent::addBulkUploadResult($bulkUploadResult);
			
	}
	
	abstract protected function listObjects(KalturaFilter $filter, KalturaFilterPager $pager = null); 
	
	abstract protected function createObjectFromResultAndJobData (KalturaBulkUploadResult $bulkUploadResult);

	abstract protected function deleteObjectFromResult (KalturaBulkUploadResult $bulkUploadResult);
	
	abstract protected function fillUploadResultInstance ($object);
	
	abstract protected function getBulkUploadResultObjectType ();
	
	protected function isErrorResult($requestResult){
		if(is_array($requestResult) && isset($requestResult['code'])){
			return true;
		}
		if($requestResult instanceof Exception){
			return true;
		}
		return false;
	}
	
	/**
	 *
	 * Creates a new upload result object from the given parameters
	 * @param KalturaObject $object
	 * @return KalturaBulkUploadResult
	 */
	protected function createUploadResult($object)
	{
	    if($this->handledRecordsThisRun > $this->maxRecordsEachRun)
		{
			$this->exceededMaxRecordsEachRun = true;
			return null;
		}
		$this->handledRecordsThisRun++;
		
	    $bulkUploadResult = $this->fillUploadResultInstance($object);
		$bulkUploadResult->bulkUploadJobId = $this->job->id;
		$bulkUploadResult->lineIndex = $this->startIndex + $this->handledObjectsCount;
		$bulkUploadResult->partnerId = $this->job->partnerId;
		$bulkUploadResult->status = KalturaBulkUploadResultStatus::IN_PROGRESS;
		if (!$bulkUploadResult->action)
		{
		    $bulkUploadResult->action = KalturaBulkUploadAction::ADD;
		}	
		$bulkUploadResult->bulkUploadResultObjectType = $this->getBulkUploadResultObjectType(); 
			
		$this->bulkUploadResults[] = $bulkUploadResult;
		
		return $bulkUploadResult;
	}
	
	/**
	 * Get objects according to the input filter and create bulkUploadResults for each one of them
	 * 
	 */
	protected function processObjectsList()
	{
		KalturaLog::debug("Processing objects, start index: ".$this->startIndex);
		
		$pager = new KalturaFilterPager();
		$pager->pageSize = 100;		
		if(KBatchBase::$taskConfig->params->pageSize)
			$pager->pageSize = KBatchBase::$taskConfig->params->pageSize;			
		$pager->pageIndex = $this->getPagerIndex($pager->pageSize);

		KalturaLog::debug("Getting objects, page index: ".$pager->pageIndex);
		$list = $this->listObjects($this->getData()->filter, $pager);
		$stop = false;
		
		while(count($list->objects) && !$stop)
		{
			foreach ($list->objects as $object) 
			{
				$this->handledObjectsCount ++;
					
				// creates a result object
				$this->createUploadResult($object);
				if($this->exceededMaxRecordsEachRun)
					return;
				    		    
				if(KBatchBase::$kClient->getMultiRequestQueueSize() >= $this->multiRequestSize)
				{
					KBatchBase::$kClient->doMultiRequest();
					$this->checkAborted();
					KBatchBase::$kClient->startMultiRequest();
				}	
			}
			if(count($list->objects) < $pager->pageSize)
				$stop = true;
			else 
			{
				$pager->pageIndex = $this->getPagerIndex($pager->pageSize);						
				KalturaLog::debug("Getting objects, page index: ".$pager->pageIndex);				
				$list = $this->listObjects($this->getData()->filter, $pager);
			}
		}		
	}

	/**
	 * 
	 * Create the objects from the given bulk upload results
	 */
	protected function createObjects()
	{
		KalturaLog::info("job[{$this->job->id}] start creating objects");
		
		$bulkUploadResultChunk = array(); // store the results of the created entries
				
		KBatchBase::impersonate($this->currentPartnerId);;
		KBatchBase::$kClient->startMultiRequest();
		
		foreach($this->bulkUploadResults as $bulkUploadResult)
		{
			/* @var $bulkUploadResult KalturaBulkUploadResultCategoryEntry */
		    switch ($bulkUploadResult->action)
		    {
		        case KalturaBulkUploadAction::ADD:
    		        $this->createObjectFromResultAndJobData($bulkUploadResult);       					
        			$bulkUploadResultChunk[] = $bulkUploadResult;
		            break;
		        		            
		        case KalturaBulkUploadAction::DELETE:
		            $bulkUploadResultChunk[] = $bulkUploadResult;
        			$this->deleteObjectFromResult($bulkUploadResult);      			
		            break;
		        
		        default:
		            $bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
		            $bulkUploadResult->errorDescription = "Unsupported action passed: [".$bulkUploadResult->action ."]";
		            break;
		    }
		    
		    if(KBatchBase::$kClient->getMultiRequestQueueSize() >= $this->multiRequestSize)
			{
				$requestResults = KBatchBase::$kClient->doMultiRequest();
				KBatchBase::unimpersonate();
				$this->updateObjectsResults($requestResults, $bulkUploadResultChunk);
				$this->checkAborted();
				KBatchBase::impersonate($this->currentPartnerId);;
				KBatchBase::$kClient->startMultiRequest();
				$bulkUploadResultChunk = array();
			}
		}
		
		// make all the category actions as the partner
		$requestResults = KBatchBase::$kClient->doMultiRequest();
		
		KBatchBase::unimpersonate();
		
		if(count($requestResults))
			$this->updateObjectsResults($requestResults, $bulkUploadResultChunk);


		KalturaLog::info("job[{$this->job->id}] finished creating objects");
	}
	
    protected function updateObjectsResults($requestResults, $bulkUploadResults)
	{
	    KBatchBase::$kClient->startMultiRequest();
		KalturaLog::info("Updating " . count($requestResults) . " results");
		
		foreach($requestResults as $index => $requestResult)
		{
			$bulkUploadResult = $bulkUploadResults[$index];
			
			if(is_array($requestResult) && isset($requestResult['code']))
			{
				if($this->isErrorResult($requestResult)){
				    $bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
				    $bulkUploadResult->errorType = KalturaBatchJobErrorTypes::KALTURA_API;
					$bulkUploadResult->objectStatus = $requestResult['code'];
					$bulkUploadResult->errorDescription = $requestResult['message'];
					$this->addBulkUploadResult($bulkUploadResult);					
				}
				continue;
			}
			
			if($requestResult instanceof Exception)
			{
				if($this->isErrorResult($requestResult)){
					$bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
					$bulkUploadResult->errorType = KalturaBatchJobErrorTypes::KALTURA_API;
					$bulkUploadResult->errorDescription = $requestResult->getMessage();
					$this->addBulkUploadResult($bulkUploadResult);
				}
				continue;
			}
			
			// update the results with the new object Id
			if ($requestResult->id)
			    $bulkUploadResult->objectId = $requestResult->id;
			$this->addBulkUploadResult($bulkUploadResult);
		}
		
		KBatchBase::$kClient->doMultiRequest();
	}
	
	private function getPagerIndex($pageSize)
	{	
		return (int)(($this->startIndex + $this->handledObjectsCount) / $pageSize) + 1;
	}
}
