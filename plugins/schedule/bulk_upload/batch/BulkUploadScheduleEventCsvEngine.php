<?php

class BulkUploadScheduleEventCsvEngine extends BulkUploadEngineCsv
{
	
	const OBJECT_TYPE_TITLE = 'schedule-event';
	
	const TEMPLATE_ENTRY_TYPE_MEDIA = 'media';
	
	const TEMPLATE_ENTRY_TYPE_LIVE = 'live';
	
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
			
			if($bulkUploadResult->recurrence)
			{
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
					if ($conflictingEvents->totalCount)
					{
						//If there are conflicting events for this resource - the event should not be created.
						KalturaLog::notice('Conflicting events exist for resource ID ' . $bulkUploadResult->resourceId . ' at the specified time/s. The event will not be created.');
						$this->setResultError($bulkUploadResult, 'Conflicting events found for resource ID ' . $bulkUploadResult->resourceId);
						KBatchBase::unimpersonate();
						$bulkUploadResults[] = $bulkUploadResult;
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
				$bulkUploadResults[] = $bulkUploadResult;
				
			}
			catch (Exception $e)
			{
				KalturaLog::err('An error occurred during creation of event or associated objects: ' . $e->getMessage());
				KBatchBase::unimpersonate();
				$this->setResultError($bulkUploadResult, $e->getMessage(), KalturaBatchJobErrorTypes::KALTURA_API, $e->getCode());
				$bulkUploadResults[] = $bulkUploadResult;
			}
		}
		
		KalturaLog::info("Updating " . count($bulkUploadResults) . " results");
		
		foreach($bulkUploadResults as $result)
		{
			$this->addBulkUploadResult($result);
		}
		
		KalturaLog::info("job[{$this->job->id}] finish creating objects");
		
	}
	
	protected function createTemplateEntry (KalturaBulkUploadResultScheduleEvent $bulkUploadResult)
	{
		$templateEntry = $this->initTemplateEntry($bulkUploadResult->templateEntryType);
		$templateEntry->name = $bulkUploadResult->title;
		$templateEntry->description = $bulkUploadResult->description;
		$templateEntry->tags = $bulkUploadResult->tags;
		$templateEntry->userId = $bulkUploadResult->contentOwnerId;
		$templateEntry->creatorId = $bulkUploadResult->contentOwnerId;
		$templateEntry->entitledUsersPublish = $bulkUploadResult->coPublishers;
		$templateEntry->entitledUsersEdit = $bulkUploadResult->coEditors;
		
		if($templateEntry instanceof KalturaLiveStreamEntry)
		{
			$templateEntry = KBatchBase::$kClient->liveStream->add($templateEntry);
		}
		else
		{
			$templateEntry = KBatchBase::$kClient->media->add($templateEntry);
		}
		
		$this->createCategoryAssociations($templateEntry->id, $bulkUploadResult);
		
		return $templateEntry;
	}
	
	/**
	 * @param $entryId
	 * @param KalturaBulkUploadResultScheduleEvent $bulkUploadResult
	 */
	protected function createCategoryAssociations ($entryId, KalturaBulkUploadResultScheduleEvent $bulkUploadResult)
	{
		if ($bulkUploadResult->categoryIds)
		{
			$categoryIds = explode(',', $bulkUploadResult->categoryIds);
			foreach ($categoryIds as $categoryId)
			{
				$categoryEntry = new KalturaCategoryEntry();
				$categoryEntry->entryId = $entryId;
				$categoryEntry->categoryId = $categoryId;
				
				KBatchBase::$kClient->categoryEntry->add($categoryEntry);
			}
		}
	}
	
	/**
	 * @param $eventType
	 */
	protected function initTemplateEntry ($templateEntryType)
	{
		switch ($templateEntryType)
		{
			case self::TEMPLATE_ENTRY_TYPE_MEDIA:
				$templateEntry = new KalturaMediaEntry();
				$templateEntry->mediaType = KalturaMediaType::VIDEO;
				break;
			case self::TEMPLATE_ENTRY_TYPE_LIVE:
				$templateEntry = new KalturaLiveStreamEntry();
				$templateEntry->mediaType = KalturaMediaType::LIVE_STREAM_FLASH;
				$templateEntry->sourceType = KalturaSourceType::LIVE_STREAM;
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
		array_map('trim', $columns);
		if (count($columns) != count($values))
		{
			$this->setResultError($result, 'The number of values is not equal to the number of columns. Event wll not be created.');
			$this->addBulkUploadResult($result);
			return;
		}
		
		$row = array_combine ($columns, $values);
		
		$result->status = KalturaBulkUploadResultStatus::IN_PROGRESS;
		
		//Input validation
		if (!isset($row['action']) || $row['action'] != KalturaBulkUploadAction::ADD)
		{
			//If the action is not 'ADD' - input validation error must be set on the bulk upload result.
			$this->setResultError($result, 'Invalid action type value ' . $row['action'] . ' passed. Only action type 1 (ADD) is supported at this time.');
			$this->addBulkUploadResult($result);
			return;
		}
		
		foreach ($this->getRequiredValueColumns() as $columnName)
		{
			$this->validateInputKeyExists($row, $result, $columnName, "Value for $columnName must be specified!");
		}
		
		//Recurrence should not be passed without the standalone event duration.
		if (isset($row['recurrence']) && $row['recurrence']
			&& (!isset($row['duration']) || !$row['duration']))
		{
			$this->setResultError($result, 'Recurrence pattern cannot be specified without the duration for each event instance. Event was not created.');
			$this->addBulkUploadResult($result);
			return;
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
				$this->setResultError($result, 'Invalid resource system name ' . $row['resource'] . ' passed. Event was not created.');
				$this->addBulkUploadResult($result);
				return;
			}
			else
			{
				$result->resourceId = $resourceResults->objects[0]->id;
			}
		}
		
		
		// If the input validation failed earlier, there is no point setting any further parameters - the scheduled event will not be created anyway.
		if ($result->status != KalturaBulkUploadResultStatus::ERROR)
		{
			/*
			* Determine the category for the scheduled event. If both categoryPaths and categoryIds were passed in the CSV,
			* the event will still be created, but the category associations will not be created.
			*/
			if (isset($row['categoryIds']) && isset($row['categoryPaths'])
				&& $row['categoryIds'] && $row['categoryPaths'])
			{
				$result->errorDescription = '\n Please use categoryIds OR categoryPaths, and not both. Event category association will not be created. ';
			}
			elseif (isset($row['categoryIds']) || isset($row['categoryPaths']))
			{
				$result->categoryIds = implode(',', $this->retrieveCategoriesIds($row));
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
	
	/**
	 * @param array $row
	 * @return array
	 */
	protected function retrieveCategoriesIds (array $row)
	{
		$categoryFilter = new KalturaCategoryFilter();
		if (isset($row['categoryIds']))
		{
			$categoryFilter->idIn = $row['categoryIds'];
		}
		
		if (isset($row['categoryPaths']))
		{
			$categoryFilter->fullNameIn = $row['categoryPaths'];
		}
		
		KBatchBase::impersonate($this->currentPartnerId);
		$categoryResponse = KBatchBase::$kClient->category->listAction($categoryFilter);
		KBatchBase::unimpersonate();
		
		$categoriesIds = array();
		foreach ($categoryResponse->objects as $category)
		{
			$categoriesIds[] = $category->id;
		}
		
		return $categoriesIds;
	}
	
	/**
	 * @param array $inputArray
	 * @param KalturaBulkUploadResultScheduleEvent $bulkUploadResult
	 * @param $key
	 * @param $errorMsg
	 */
	protected function validateInputKeyExists (array $inputArray, KalturaBulkUploadResultScheduleEvent $bulkUploadResult, $key, $errorMsg)
	{
		if (!isset($inputArray[$key]) || !$inputArray[$key])
		{
			$this->setResultError($bulkUploadResult, $errorMsg);
		}
	}
	
	/**
	 * @param KalturaBulkUploadResultScheduleEvent $bulkUploadResult
	 * @param $errorMsg
	 * @param int $errorType
	 * @param null $errorCode
	 */
	protected function setResultError (KalturaBulkUploadResultScheduleEvent $bulkUploadResult, $errorMsg, $errorType = KalturaBatchJobErrorTypes::APP, $errorCode = null)
	{
		$bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
		$bulkUploadResult->errorType = $errorType;
		if ($errorCode)
		{
			$bulkUploadResult->errorCode = $errorCode;
		}
		$bulkUploadResult->errorDescription .= '\n '. $errorMsg;
	}
	
	protected function getRequiredValueColumns ()
	{
		return array(
			'action',
			'eventType',
			'title',
			'resource',
			'startTime',
			'endTime',
			'eventOrganizerId',
			'contentOwnerId',
		);
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
			'templateEntryType',
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