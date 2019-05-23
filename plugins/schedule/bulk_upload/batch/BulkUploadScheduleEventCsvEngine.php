<?php

class BulkUploadScheduleEventCsvEngine extends BulkUploadEngineCsv
{
	
	const OBJECT_TYPE_TITLE = 'schedule-event';
	
	/**
	 * @var KalturaScheduleClientPlugin
	 */
	public $schedulePlugin;
	
	function __construct(KalturaBatchJob $job)
	{
		parent::__construct($job);
		
		$this->schedulePlugin = KalturaScheduleClientPlugin::get(KBatchBase::$kClient);
	}
	
	/**
	 *
	 * Create the entries from the given bulk upload results
	 */
	protected function createObjects()
	{
		KalturaLog::info("job[{$this->job->id}] start creating entries [" . count($this->bulkUploadResults) . "]");
		
		$bulkUploadResults = array();
		foreach ($this->bulkUploadResults as $bulkUploadResult)
		{
			// start a multi request for add entries
			/* @var $bulkUploadResult KalturaBulkUploadResultScheduleEvent */
			$event = $this->initEventObject($bulkUploadResult->eventType);
			$event->summary = $bulkUploadResult->title;
			$event->description = $bulkUploadResult->description;
			$event->tags = $bulkUploadResult->tags;
			$event->startDate = strtotime($bulkUploadResult->startTime);
			$event->duration = $bulkUploadResult->duration;
			$event->categoryIds = $bulkUploadResult->categoryIds;
			$event->ownerId = $bulkUploadResult->eventOrganizerId;
			$event->organizer = $bulkUploadResult->eventOrganizerId;
			
			if($bulkUploadResult->recurrence) {
				$event->recurrenceType = KalturaScheduleEventRecurrenceType::RECURRING;
				$event->recurrence = $this->createRecurrenceObject($bulkUploadResult->recurrence, $bulkUploadResult->endTime);
			}
			else
			{
				$event->recurrenceType = KalturaScheduleEventRecurrenceType::NONE;
				$event->endDate = strtotime($bulkUploadResult->endTime);
			}
			
			try
			{
				KBatchBase::impersonate($this->currentPartnerId);
				
				if ($bulkUploadResult->resourceId)
				{
					$conflictingEvents = $this->schedulePlugin->scheduleEvent->getConflicts($bulkUploadResult->resourceId, $event);
					if ($conflictingEvents->totalCount) {
						//If there are conflicting events for this resource - the event should not be created.
						KalturaLog::err('Conflicting events exist for resource ID ' . $bulkUploadResult->resourceId . ' at the specified time/s. The event will not be created.');
						$bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
						$bulkUploadResult->errorType = KalturaBatchJobErrorTypes::APP;
						$bulkUploadResult->errorDescription .= '\n Conflicting events found for resource ID ' . $bulkUploadResult->resourceId;
						KBatchBase::unimpersonate();
						$bulkUploadResultChunk[] = $bulkUploadResult;
						continue;
					}
				}
				
				$event = $this->schedulePlugin->scheduleEvent->add($event);
				
				// Create association between the resource and the scheduled event.
				$scheduleEventResource = new KalturaScheduleEventResource();
				$scheduleEventResource->resourceId = $bulkUploadResult->resourceId;
				$scheduleEventResource->eventId = $event->id;
				
				
				$this->schedulePlugin->scheduleEventResource->add($scheduleEventResource);
				
				$entry = $this->createTemplateEntry($bulkUploadResult);
				
				$updateEvent = $this->initEventObject($bulkUploadResult->eventType);
				$updateEvent->templateEntryId = $entry->id;
				
				$this->schedulePlugin->scheduleEvent->update($event->id, $updateEvent);
				KBatchBase::unimpersonate();
				
				$bulkUploadResult->status = KalturaBulkUploadResultStatus::OK;
				$bulkUploadResult->templateEntryId = $entry->id;
				$bulkUploadResultChunk[] = $bulkUploadResult;
				
			}
			catch (Exception $e)
			{
				KalturaLog::err('An error occurred during creation of event or associated objects: ' . $e->getMessage());
				KBatchBase::unimpersonate();
				$bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
				$bulkUploadResult->errorDescription .= '\n' . $e->getMessage();
				$bulkUploadResult->errorType = KalturaBatchJobErrorTypes::KALTURA_API;
				$bulkUploadResult->errorCode = $e->getCode();
				$bulkUploadResultChunk[] = $bulkUploadResult;
			}
		}
		
		KalturaLog::info("Updating " . count($bulkUploadResultChunk) . " results");
		
		foreach($bulkUploadResultChunk as $result)
		{
			$this->addBulkUploadResult($result);
		}
		
		KalturaLog::info("job[{$this->job->id}] finish creating objects");
		
	}
	
	protected function createTemplateEntry (KalturaBulkUploadResultScheduleEvent $bulkUploadResult)
	{
		$templateEntry = $this->initTemplateEntry($bulkUploadResult->eventType);
		$templateEntry->name = $bulkUploadResult->title;
		$templateEntry->description = $bulkUploadResult->description;
		$templateEntry->tags = $bulkUploadResult->tags;
		$templateEntry->userId = $bulkUploadResult->contentOwnerId;
		$templateEntry->creatorId = $bulkUploadResult->contentOwnerId;
		$templateEntry->entitledUsersPublish = $bulkUploadResult->coPublishers;
		$templateEntry->entitledUsersEdit = $bulkUploadResult->coEditors;
		
		$templateEntry = KBatchBase::$kClient->baseEntry->add($templateEntry);
		
		if ($bulkUploadResult->categoryIds)
		{
			$categoryIds = explode(',', $bulkUploadResult->categoryIds);
			foreach ($categoryIds as $categoryId)
			{
				$categoryEntry = new KalturaCategoryEntry();
				$categoryEntry->entryId = $templateEntry->id;
				$categoryEntry->categoryId = $categoryId;
				
				KBatchBase::$kClient->categoryEntry->add($categoryEntry);
			}
		}
		
		return $templateEntry;
	}
	
	/**
	 * @param $eventType
	 */
	protected function initTemplateEntry ($eventType)
	{
		switch ($eventType)
		{
			case KalturaScheduleEventType::RECORD:
				$templateEntry = new KalturaMediaEntry();
				$templateEntry->mediaType = KalturaMediaType::VIDEO;
				break;
			case KalturaScheduleEventType::LIVE_STREAM:
				$templateEntry = new KalturaLiveStreamEntry();
				break;
		}
		
		return $templateEntry;
	}
	
	/**
	 * Parses out the recurrence string of the following format:
	 * FREQ=,INTERVAL=,BYMONTHDAY=, BYMONTH=, BYDAY=, BYSETPOS=, COUNT=
	 * @param string $recurrence
	 * @param string $endTime
	 *
	 * @return KalturaScheduleEventRecurrence
	 */
	protected function createRecurrenceObject ($recurrence, $endTime = null)
	{
		$recurrencePattern = explode(',', $recurrence);
		
		$recurrenceObject = new KalturaScheduleEventRecurrence();
		foreach ($recurrencePattern as $part)
		{
			list ($key, $value) = explode('=', trim($part));
			
			switch ($key)
			{
				case 'FREQ':
					$recurrenceObject->frequency = constant("KalturaScheduleEventRecurrenceFrequency::$value");
					break;
				case 'INTERVAL':
					$recurrenceObject->interval = $value;
					break;
				case 'BYMONTHDAY':
					$recurrenceObject->byMonthDay = $value;
					break;
				case 'BYMONTH':
					$recurrenceObject->byMonth = $value;
					break;
				case 'BYDAY':
					$recurrenceObject->byDay = $value;
					break;
				case 'BYSETPOS':
					$recurrenceObject->byOffset = $value;
					break;
				case 'COUNT':
					$recurrenceObject->count = $value;
					break;
			}
		}
		
		if($endTime)
		{
			$recurrenceObject->count = null;
			$recurrenceObject->until = strtotime($endTime);
		}
		
		KalturaLog::info('Recurrence object: ' . print_r($recurrenceObject, true));
		return $recurrenceObject;
	}
	
	/**
	 * @param $eventType
	 * @return KalturaEntryScheduleEvent
	 */
	protected function initEventObject($eventType)
	{
		switch ($eventType)
		{
			case KalturaScheduleEventType::RECORD:
				return new KalturaRecordScheduleEvent();
			case KalturaScheduleEventType::LIVE_STREAM:
				return new KalturaLiveStreamScheduleEvent();
			default:
				KalturaLog::err("Invalid scheduled event type: $eventType!");
				return null;
		}
	}
	
	/**
	 *
	 * Creates a new upload result object from the given parameters
	 * @param array $values
	 * @param array $columns
	 * @return KalturaBulkUploadResult
	 */
	protected function createUploadResult($values, $columns)
	{
		$result = parent::createUploadResult($values, $columns);
		if (!$result)
		{
			return;
		}
		
		$result->bulkUploadResultObjectType = KalturaBulkUploadObjectType::SCHEDULE_EVENT;
		
		/* @var $result KalturaBulkUploadResultScheduleEvent */
		//Input validation
		$row = array_combine ($columns, $values);
		
		$result->status = KalturaBulkUploadResultStatus::IN_PROGRESS;
		
		//Input validation
		if (!isset($row['action']) || $row['action'] != KalturaBulkUploadAction::ADD)
		{
			//If the action is not 'ADD' - input validation error must be set on the bulk upload result.
			$result->status = KalturaBulkUploadResultStatus::ERROR;
			$result->errorType = KalturaBatchJobErrorTypes::APP;
			$result->errorDescription .= '\n Invalid action type value ' . $row['action'] . ' passed. Only action type 1 (ADD) is supported at this time.';
		}
		
		if (!isset ($row['title']))
		{
			//If a title was not provided for the event, it should not be created
			$result->status = KalturaBulkUploadResultStatus::ERROR;
			$result->errorType = KalturaBatchJobErrorTypes::APP;
			$result->errorDescription .= '\n Title value must be specified';
			
		}
		
		if (!isset ($row['startTime']))
		{
			//If a startTime was not provided for the event, it should not be created
			$result->status = KalturaBulkUploadResultStatus::ERROR;
			$result->errorType = KalturaBatchJobErrorTypes::APP;
			$result->errorDescription .= '\n startTime value must be specified';
			
		}
		
		if (!isset($row['endTime']) || !$row['endTime'])
		{
			//If a duration was not provided for the event, it should not be created
			$result->status = KalturaBulkUploadResultStatus::ERROR;
			$result->errorType = KalturaBatchJobErrorTypes::APP;
			$result->errorDescription .= '\n event endTime value must be specified';
			
		}
		
		if (!isset($row['eventOrganizerId']) || !$row['eventOrganizerId'])
		{
			//If a creator was not provided for the event, it should not be created
			$result->status = KalturaBulkUploadResultStatus::ERROR;
			$result->errorType = KalturaBatchJobErrorTypes::APP;
			$result->errorDescription .= '\n eventOrganizerId value must be specified';
			
		}
		
		if (!isset($row['contentOwnerId']) || !$row['contentOwnerId'])
		{
			//If a creator was not provided for the event, it should not be created
			$result->status = KalturaBulkUploadResultStatus::ERROR;
			$result->errorType = KalturaBatchJobErrorTypes::APP;
			$result->errorDescription .= '\n contentOwnerId value must be specified';
			
		}
		
		if (isset($row['resource']) && $row['resource'])
		{
			$resourceFilter = new KalturaScheduleResourceFilter();
			$resourceFilter->systemNameEqual = $row['resource'];
			$resourceFilter->statusEqual = KalturaScheduleResourceStatus::ACTIVE;
			
			$resourceResults = $this->schedulePlugin->scheduleResource->listAction($resourceFilter);
			
			if (!$resourceResults->totalCount)
			{
				//If the resource could not be found - input validation error must be set on the bulk upload result.
				$result->status = KalturaBulkUploadResultStatus::ERROR;
				$result->errorType = KalturaBatchJobErrorTypes::APP;
				$result->errorDescription = '\n Invalid resource system name' . $row['resource'] . ' passed. Event was not created.';
			}
			else
			{
				$result->resourceId = $resourceResults->objects[0]->id;
			}
		}
		
		
		// If the input validation failed earlier, there is no point setting any further parameters - the scheduled event will not be created anyway.
		if ($result->status != KalturaBulkUploadResultStatus::ERROR) {
			//Determine the category for the scheduled event
			if (isset($row['categoryIds']) && isset($row['categoryPaths'])
				&& $row['categoryIds'] && $row['categoryPaths']) {
				$result->errorDescription = '\n Please use categoryIds OR categoryPaths, and not both. Event category association will not be created. ';
			}
			elseif (isset($row['categoryIds']) || isset($row['categoryPaths']))
			{
				$categoryFilter = new KalturaCategoryFilter();
				if (isset($row['categoryIds'])) {
					$categoryFilter->idIn = $row['categoryIds'];
				}
			
				if (isset($row['categoryPaths'])) {
					$categoryFilter->fullNameIn = $row['categoryPaths'];
				}
				
				KBatchBase::impersonate($this->currentPartnerId);
				$categoryResponse = KBatchBase::$kClient->category->listAction($categoryFilter);
				KBatchBase::unimpersonate();
				
				$categoriesIds = array();
				foreach ($categoryResponse->objects as $category) {
					$categoriesIds[] = $category->id;
				}
				
				$result->categoryIds = implode(',', $categoriesIds);
			}
			
			foreach ($row as $columnName => $value)
			{
				if (!in_array($columnName, array('categoryPaths', 'categoryIds')))
				{
					$result->$columnName = $value;
				}
			}
		}
		
		if($result->status == KalturaBulkUploadResultStatus::ERROR)
		{
			$this->addBulkUploadResult($result);
			return;
		}
		
		$this->bulkUploadResults[] = $result;
		
		return $result;
	}
	
	protected function getColumns()
	{
		return array (
			'action',
			'eventType',
			'title',
			'description',
			'tags',
			'categoryIds',
			'categoryPaths',
			'resource',
			'startTime',
			'duration',
			'endTime',
			'recurrence',
			'coEditors',
			'coPublishers',
			'eventOrganizerId',
			'contentOwnerId',
		);
	}
	
	protected function getUploadResultInstance()
	{
		return new KalturaBulkUploadResultScheduleEvent();
	}
	
	/**
	 *
	 * Get object type title for messaging purposes
	 */
	public function getObjectTypeTitle()
	{
		return self::OBJECT_TYPE_TITLE;
	}
}