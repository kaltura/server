<?php
/**
 * Class which parses the bulk upload iCal and creates the objects defined in it.
 *
 * @package plugins.scheduleBulkUpload
 * @subpackage batch
 */
class BulkUploadEngineICal extends KBulkUploadEngine
{
    const OBJECT_TYPE_TITLE = 'schedule-event';
    const CHUNK_SIZE = 20;
    const MAX_IN_FILTER = 100;

    /**
     * @var int
     */
    protected $itemIndex = 0;
    
	/**
	 * The bulk upload results
	 * @var array
	 */
	protected $bulkUploadResults = array();
    
	/**
	 * The bulk upload items
	 * @var array<kSchedulingICalEvent>
	 */
	protected $items = array();
    
    protected function createUploadResults()
    {
    	$items = $this->items;
    	
		$this->itemIndex = $this->getStartIndex($this->job->id);
		if($this->itemIndex)
		{
			$items = array_slice($items, $this->itemIndex);
		}
		
		$chunks = array_chunk($items, self::CHUNK_SIZE);
		foreach($chunks as $chunk)
		{
			KBatchBase::$kClient->startMultiRequest();
			foreach($chunk as $item)
			{
				/* @var $item kSchedulingICalEvent */
				$bulkUploadResult = $this->createUploadResult($item);
				if($bulkUploadResult)
				{
					$this->bulkUploadResults[] = $bulkUploadResult;
				}
				else
				{
					break;
				}
			}
			KBatchBase::$kClient->doMultiRequest();
		}
    }
    
    protected function createUploadResult(kSchedulingICalEvent $iCal)
    {
    	if($this->handledRecordsThisRun > $this->maxRecordsEachRun)
    	{
    		$this->exceededMaxRecordsEachRun = true;
    		return null;
    	}
    	$this->handledRecordsThisRun++;
    
    	$bulkUploadResult = new KalturaBulkUploadResultScheduleEvent();
    	$bulkUploadResult->bulkUploadJobId = $this->job->id;
    	$bulkUploadResult->lineIndex = $this->itemIndex;
    	$bulkUploadResult->partnerId = $this->job->partnerId;
    	$bulkUploadResult->referenceId = $iCal->getUid();
    	$bulkUploadResult->bulkUploadResultObjectType = KalturaBulkUploadObjectType::SCHEDULE_EVENT;
    	$bulkUploadResult->rowData = $iCal->getRaw();
		$bulkUploadResult->objectStatus = KalturaScheduleEventStatus::ACTIVE;
		$bulkUploadResult->status = KalturaBulkUploadResultStatus::IN_PROGRESS;

    	if($iCal->getMethod() == kSchedulingICal::METHOD_CANCEL)
    	{
    		$bulkUploadResult->action = KalturaBulkUploadAction::CANCEL;
    	}
    
    	$this->itemIndex++;

    	return $bulkUploadResult;
    }

    protected function updateObjectsResults(array $requestResults, array $bulkUploadResults)
    {
    	KBatchBase::$kClient->startMultiRequest();
    
    	// checking the created entries
    	foreach($requestResults as $index => $requestResult)
    	{
    		$bulkUploadResult = $bulkUploadResults[$index];
    			
    		if(KBatchBase::$kClient->isError($requestResult))
    		{
    			$bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
    			$bulkUploadResult->errorType = KalturaBatchJobErrorTypes::KALTURA_API;
    			$bulkUploadResult->objectStatus = $requestResult['code'];
    			$bulkUploadResult->errorDescription = $requestResult['message'];
    			$this->addBulkUploadResult($bulkUploadResult);
    			continue;
    		}
    			
    		if($requestResult instanceof Exception)
    		{
    			$bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
    			$bulkUploadResult->errorType = KalturaBatchJobErrorTypes::KALTURA_API;
    			$bulkUploadResult->errorDescription = $requestResult->getMessage();
    			$this->addBulkUploadResult($bulkUploadResult);
    			continue;
    		}
    			
    		// update the results with the new object Id
    		if ($requestResult->id)
    			$bulkUploadResult->objectId = $requestResult->id;
    			$this->addBulkUploadResult($bulkUploadResult);
    	}
    
    	KBatchBase::$kClient->doMultiRequest();
    }
    
    protected function getExistingEvents()
    {
    	$schedulePlugin = KalturaScheduleClientPlugin::get(KBatchBase::$kClient);

    	$pager = new KalturaFilterPager();
    	$pager->pageSize = self::MAX_IN_FILTER;
    	
		KBatchBase::$kClient->startMultiRequest();
		$referenceIds = array();
		foreach($this->bulkUploadResults as $bulkUploadResult)
		{
			/* @var $bulkUploadResult KalturaBulkUploadResultScheduleEvent */
		    if($bulkUploadResult->action == KalturaBulkUploadAction::CANCEL)
		    	continue;
		    
		    $item = $this->items[$bulkUploadResult->lineIndex];
		    /* @var $item kSchedulingICalEvent */
		    
		    if(!$item->getUid())
		    	continue;
		    
		    $referenceIds[] = $item->getUid();
		    if(count($referenceIds) >= self::MAX_IN_FILTER)
		    {
		    	$filter = new KalturaScheduleEventFilter();
		    	$filter->referenceIdIn = implode(',', $referenceIds);
		    	$schedulePlugin->scheduleEvent->listAction($filter, $pager);
		    }
		}
	    if(count($referenceIds))
	    {
	    	$filter = new KalturaScheduleEventFilter();
	    	$filter->referenceIdIn = implode(',', $referenceIds);
	    	$schedulePlugin->scheduleEvent->listAction($filter, $pager);
	    	$referenceIds = array();
	    }
		$results = KBatchBase::$kClient->doMultiRequest();

		$existingEvents = array();
	    if (is_array($results) || is_object($results))
	    {
		    foreach($results as $result)
		    {
			    KBatchBase::$kClient->throwExceptionIfError($result);
			    /* @var $result KalturaScheduleEventListResponse */
			    foreach($result->objects as $scheduleEvent)
			    {
				    /* @var $scheduleEvent KalturaScheduleEvent */
				    $existingEvents[$scheduleEvent->referenceId] = $scheduleEvent->id;
			    }
		    }
	    }
	    return $existingEvents;
    }
    
    protected function createObjects()
    {
    	$schedulePlugin = KalturaScheduleClientPlugin::get(KBatchBase::$kClient);
		
		$existingEvents = $this->getExistingEvents();

		KBatchBase::$kClient->startMultiRequest();
		
		$bulkUploadResultChunk = array();
		foreach($this->bulkUploadResults as $bulkUploadResult)
		{
		    $item = $this->items[$bulkUploadResult->lineIndex];
		    /* @var $item kSchedulingICalEvent */
		    
			$bulkUploadResultChunk[] = $bulkUploadResult;
			KBatchBase::impersonate($this->currentPartnerId);;
			
			/* @var $bulkUploadResult KalturaBulkUploadResultScheduleEvent */
			if($bulkUploadResult->action == KalturaBulkUploadAction::CANCEL)
			{
				$schedulePlugin->scheduleEvent->cancel($bulkUploadResult->referenceId);
			}
			elseif (isset($existingEvents[$bulkUploadResult->referenceId]))
			{
				$scheduleEventId = $existingEvents[$bulkUploadResult->referenceId];
				$schedulePlugin->scheduleEvent->update($scheduleEventId, $item->toObject());
			}
			else 
			{
				$schedulePlugin->scheduleEvent->add($item->toObject());
			}
			
			KBatchBase::unimpersonate();
		
			if(KBatchBase::$kClient->getMultiRequestQueueSize() >= $this->multiRequestSize)
			{
				// make all the media->add as the partner
				$requestResults = KBatchBase::$kClient->doMultiRequest();
		
				$this->updateObjectsResults($requestResults, $bulkUploadResultChunk);
				$this->checkAborted();
				KBatchBase::$kClient->startMultiRequest();
				$bulkUploadResultChunk = array();
			}
		}
		
		// make all the category actions as the partner
		$requestResults = KBatchBase::$kClient->doMultiRequest();
		
		if(count($requestResults))
			$this->updateObjectsResults($requestResults, $bulkUploadResultChunk);

		KalturaLog::info("job[{$this->job->id}] finish modifying users");
    }
    
	/**
	 * {@inheritDoc}
	 * @see KBulkUploadEngine::handleBulkUpload()
	 */
	public function handleBulkUpload()
	{
		$calendar = kSchedulingICal::parse(kFile::getFileContent($this->data->filePath), $this->data->eventsType);
		$this->items = $calendar->getComponents();
		
		$this->createUploadResults();
		$this->createObjects();
	}

	/**
	 * {@inheritDoc}
	 * @see KBulkUploadEngine::getObjectTypeTitle()
	 */
	public function getObjectTypeTitle()
	{
		return self::OBJECT_TYPE_TITLE;
	}
}
