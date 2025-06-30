<?php

/**
 * The ScheduleEvent service enables you to create and manage (update, delete, retrieve, etc.) scheduled recording events.
 * @service scheduleEvent
 * @package plugins.schedule
 * @subpackage api.services
 */
class ScheduleEventService extends KalturaBaseService
{
	/* (non-PHPdoc)
	 * @see KalturaBaseService::initService()
	 */
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);

		$this->applyPartnerFilterForClass('ScheduleEvent');
		$this->applyPartnerFilterForClass('ScheduleResource');
		$this->applyPartnerFilterForClass('ScheduleEventResource');
	}

	/**
	 * Allows you to add a new KalturaScheduleEvent object
	 *
	 * @action add
	 * @param KalturaScheduleEvent $scheduleEvent
	 * @return KalturaScheduleEvent
	 */
	public function addAction(KalturaScheduleEvent $scheduleEvent)
	{
		// save in database
		$dbScheduleEvent = $scheduleEvent->toInsertableObject();
		/* @var $dbScheduleEvent ScheduleEvent */

		$dates = null;

		if($dbScheduleEvent->getRecurrenceType() === ScheduleEventRecurrenceType::RECURRING)
			$this->createRecurrences($dbScheduleEvent);
		else
			$dbScheduleEvent->save();

		// return the saved object
		$scheduleEvent = KalturaScheduleEvent::getInstance($dbScheduleEvent, $this->getResponseProfile());
		$scheduleEvent->fromObject($dbScheduleEvent, $this->getResponseProfile());
		return $scheduleEvent;

	}

	/**
	 * Retrieve a KalturaScheduleEvent object by ID
	 *
	 * @action get
	 * @param int $scheduleEventId
	 * @return KalturaScheduleEvent
	 *
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */
	public function getAction($scheduleEventId)
	{
		$dbScheduleEvent = ScheduleEventPeer::retrieveByPK($scheduleEventId);
		if(!$dbScheduleEvent)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $scheduleEventId);
		}

		$scheduleEvent = KalturaScheduleEvent::getInstance($dbScheduleEvent, $this->getResponseProfile());
		$scheduleEvent->fromObject($dbScheduleEvent, $this->getResponseProfile());

		return $scheduleEvent;
	}

	/**
	 * Update an existing KalturaScheduleEvent object
	 *
	 * @action update
	 * @param int $scheduleEventId
	 * @param KalturaScheduleEvent $scheduleEvent
	 * @return KalturaScheduleEvent
	 *
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */
	public function updateAction($scheduleEventId, KalturaScheduleEvent $scheduleEvent)
	{
		$dbScheduleEvent = ScheduleEventPeer::retrieveByPK($scheduleEventId);
		if(!$dbScheduleEvent)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $scheduleEventId);
		}

		$currentScheduleEventRecurrenceType = $dbScheduleEvent->getRecurrenceType();

		$dbScheduleEvent = $scheduleEvent->toUpdatableObject($dbScheduleEvent);
		/* @var $dbScheduleEvent ScheduleEvent */

		// In case we update a recurring event to be a single event we need to delete all recurrences and set the sequence to 1
		if($dbScheduleEvent->getRecurrenceType() === ScheduleEventRecurrenceType::NONE && $currentScheduleEventRecurrenceType === ScheduleEventRecurrenceType::RECURRING )
		{
			ScheduleEventPeer::deleteByParentId($dbScheduleEvent->getId());
			$dbScheduleEvent->deleteRecurrence();
			$dbScheduleEvent->setSequence(1);
		}

		if($dbScheduleEvent->getRecurrenceType() === ScheduleEventRecurrenceType::RECURRING && $this->shouldUpdateRecurrences($scheduleEvent))
			$this->updateRecurrences($dbScheduleEvent);
		else
			$dbScheduleEvent->save();

		$scheduleEvent = KalturaScheduleEvent::getInstance($dbScheduleEvent, $this->getResponseProfile());
		$scheduleEvent->fromObject($dbScheduleEvent, $this->getResponseProfile());

		return $scheduleEvent;
	}

	/**
	 * Create new schedule recurrences for recurring event
	 *
	 * @param ScheduleEvent $dbScheduleEvent
	 */
	private function createRecurrences(ScheduleEvent $dbScheduleEvent)
	{
		$dates = $this->getRecurrencesDates($dbScheduleEvent);
		self::setRecurringDates($dates, $dbScheduleEvent);

		if ($dates)
			foreach($dates as $date)
				$this->createRecurrence($dbScheduleEvent, $date);
	}

	/**
	 * update schedule recurrences for recurring event
	 * delete redundant recurrences and create new if needed
	 *
	 * @param ScheduleEvent $dbScheduleEvent
	 */
	private function updateRecurrences(ScheduleEvent $dbScheduleEvent)
	{
		$newDates = $this->getRecurrencesDates($dbScheduleEvent);
		self::setRecurringDates($newDates, $dbScheduleEvent);
		if (is_null($newDates) || empty($newDates))
		{
			KalturaLog::debug("No dates have been received - deleting old recurrences");
			ScheduleEventPeer::deleteByParentId($dbScheduleEvent->getId());
			return;
		}

		$ends = $this->getEndDates($newDates, $dbScheduleEvent->getDuration());
		//get all the recurrences that wasn't changed
		$existingScheduleEvents = ScheduleEventPeer::retrieveByParentIdAndStartAndEndDates($dbScheduleEvent->getId(), $newDates , $ends);

		$existingScheduleEventIds = ScheduleEvent::getEventValues($existingScheduleEvents, 'getId');
		$existingScheduleEventStartDates = ScheduleEvent::getEventValues($existingScheduleEvents, 'getStartDate');
		// delete all old recurrences except the one's that hadn't changed
		KalturaLog::debug("Deleting old recurrences except for ids: " . print_r($existingScheduleEventIds, true));
		ScheduleEventPeer::deleteByParentId($dbScheduleEvent->getId(), $existingScheduleEventIds);

		//create only the new/changed ones
		$dates = array_diff($newDates, $existingScheduleEventStartDates);
		KalturaLog::debug("Adding " .count($dates) . " new recurrences");

		foreach($dates as $date)
			$this->createRecurrence($dbScheduleEvent, $date);
	}


	/**
	 * Mark the KalturaScheduleEvent object as deleted
	 *
	 * @action delete
	 * @param int $scheduleEventId
	 * @return KalturaScheduleEvent
	 *
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 * @throws KalturaScheduleErrors::RECURRENCE_CANT_BE_DELETE
	 */
	public function deleteAction($scheduleEventId)
	{
		$dbScheduleEvent = ScheduleEventPeer::retrieveByPK($scheduleEventId);
		if(!$dbScheduleEvent)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $scheduleEventId);
		}

		if($dbScheduleEvent->getRecurrenceType() == ScheduleEventRecurrenceType::RECURRENCE)
		{
			throw new KalturaAPIException(KalturaScheduleErrors::RECURRENCE_CANT_BE_DELETE, $scheduleEventId, $dbScheduleEvent->getParentId());
		}

		$dbScheduleEvent->setStatus(ScheduleEventStatus::DELETED);
		if($dbScheduleEvent->getLinkedTo())
		{
			$eventToUnlinkId = $dbScheduleEvent->getLinkedTo()->getEventId();
			if (isset($eventToUnlinkId))
			{
				$eventToUnlink = ScheduleEventPeer::retrieveByPK($eventToUnlinkId);
				$eventToUnlink->removeFromLinkedByArray($dbScheduleEvent->getId());
			}
		}
		$dbScheduleEvent->save();
		
		if($dbScheduleEvent->getRecurrenceType() == ScheduleEventRecurrenceType::RECURRING)
		{
			ScheduleEventPeer::deleteByParentId($scheduleEventId);
		}

		$scheduleEvent = KalturaScheduleEvent::getInstance($dbScheduleEvent, $this->getResponseProfile());
		$scheduleEvent->fromObject($dbScheduleEvent, $this->getResponseProfile());

		return $scheduleEvent;
	}

	/**
	 * Mark the KalturaScheduleEvent object as cancelled
	 *
	 * @action cancel
	 * @param int $scheduleEventId
	 * @return KalturaScheduleEvent
	 *
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */
	public function cancelAction($scheduleEventId)
	{
		$dbScheduleEvent = ScheduleEventPeer::retrieveByPK($scheduleEventId);
		if(!$dbScheduleEvent)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $scheduleEventId);
		}

		$dbScheduleEvent->setStatus(ScheduleEventStatus::CANCELLED);
		$dbScheduleEvent->save();

		if($dbScheduleEvent->getRecurrenceType() == ScheduleEventRecurrenceType::RECURRING)
		{
			ScheduleEventPeer::deleteByParentId($scheduleEventId);
		}

		$scheduleEvent = KalturaScheduleEvent::getInstance($dbScheduleEvent, $this->getResponseProfile());
		$scheduleEvent->fromObject($dbScheduleEvent, $this->getResponseProfile());

		return $scheduleEvent;
	}

	/**
	 * List KalturaScheduleEvent objects
	 *
	 * @action list
	 * @param KalturaScheduleEventFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaScheduleEventListResponse
	 */
	public function listAction(KalturaScheduleEventFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
		{
			$filter = new KalturaScheduleEventFilter();
		}

		if(!$pager)
		{
			$pager = new KalturaFilterPager();
		}

		return $filter->getListResponse($pager, $this->getResponseProfile());
	}

	/**
	 * Add feature to live event
	 *
	 * @action updateLiveFeature
	 * @param int $scheduledEventId
	 * @param string $featureName
	 * @param KalturaLiveFeature $liveFeature
	 * @return KalturaLiveStreamScheduleEvent
	 * @throws KalturaAPIException
	 * @throws PropelException
	 */
	public function updateLiveFeatureAction($scheduledEventId, $featureName, KalturaLiveFeature $liveFeature)
	{
		$lockName = "schedule_event" . $scheduledEventId;
		$lock = kLock::create($lockName);
		if ($lock && !$lock->lock())
		{
			KalturaLog::err('Could not lock ' . $lockName);
			throw new KalturaAPIException(KalturaErrors::CANNOT_UPDATE_SCHEDULE_EVENT_FEATURE, $featureName);
		}

		try
		{
			$dbScheduleEvent = ScheduleEventPeer::retrieveByPK($scheduledEventId);
			if (!$dbScheduleEvent)
			{
				throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $scheduledEventId);
			}

			if (!$dbScheduleEvent instanceof LiveStreamScheduleEvent)
			{
				throw new KalturaAPIException(KalturaErrors::INVALID_SCHEDULE_EVENT_TYPE, $scheduledEventId);
			}

			$featureFound = false;
			$featureList = $dbScheduleEvent->getLiveFeatures();
			foreach ($featureList as $index => $feature)
			{
				if ($feature->getSystemName() == $featureName)
				{
					$featureList[$index] = $liveFeature->toUpdatableObject($feature);
					$featureFound = true;
					break;
				}
			}

			if (!$featureFound)
			{
				throw new KalturaAPIException(KalturaErrors::FEATURE_NAME_NOT_FOUND, $featureName);
			}

			$dbScheduleEvent->setLiveFeatures($featureList);
			$dbScheduleEvent->save();
		}
		catch(Exception $e)
		{
			KalturaLog::err('Error in updateLiveFeatureAction');
			throw new KalturaAPIException(KalturaErrors::CANNOT_UPDATE_SCHEDULE_EVENT_FEATURE, $featureName);
		}
		finally
		{
			if ($lock)
			{
				$lock->unlock();
			}
		}

		$scheduleEvent = KalturaScheduleEvent::getInstance($dbScheduleEvent, $this->getResponseProfile());
		$scheduleEvent->fromObject($dbScheduleEvent, $this->getResponseProfile());

		return $scheduleEvent;
	}

	/**
	 * get schedule recurrences dates
	 * @param ScheduleEvent $dbScheduleEvent
	 * @return array unix timestamp
	 */
	private function getRecurrencesDates(ScheduleEvent $dbScheduleEvent)
	{
		$maxRecurrences = SchedulePlugin::getScheduleEventmaxRecurrences();
		$datesGenerator = new DatesGenerator($maxRecurrences, $dbScheduleEvent->getRecurrence()->asArray(), array('KalturaLog', 'debug'));
		$dates = $datesGenerator->getDates($dbScheduleEvent->getStartDate(null));

		KalturaLog::debug("Found [" . count($dates) . "] dates");
		return $dates;
	}

	private function shouldUpdateRecurrences(KalturaScheduleEvent $scheduleEvent)
	{
		$timeRelatedFields = array($scheduleEvent->startDate, $scheduleEvent->endDate, $scheduleEvent->recurrence,
			$scheduleEvent->recurrenceType, $scheduleEvent->duration);
		foreach ($timeRelatedFields as $val)
			if ($val)
				return true;
		return false;
	}
	
	private function getEndDates($startTimes, $duration)
	{
		$ends = array();
		foreach($startTimes as $start)
			$ends[] = $start + $duration;
		return $ends;
	}

	private function createRecurrence($scheduleEvent, $date)
	{
		$newScheduleEvent = $scheduleEvent->createRecurrence($date);
		$newScheduleEvent->save();
	}



	
	/**
	 * @param $dates array
	 * @param $dbScheduleEvent ScheduleEvent
	 */
	private static function setRecurringDates($dates, $dbScheduleEvent)
	{
		if (!is_null($dates) && !empty($dates))
		{
			$dbScheduleEvent->setStartDate($dates[0]);
			$dbScheduleEvent->setEndDate($dates[0] + $dbScheduleEvent->getDuration() + $dbScheduleEvent->getMarginTime());
		}
		$dbScheduleEvent->save();
	}

	/**
	 * List conflicting events for resourcesIds by event's dates
	 * @action getConflicts
	 * @param string $resourceIds comma separated
	 * @param KalturaScheduleEvent $scheduleEvent
	 * @param string $scheduleEventIdToIgnore
	 * @param KalturaScheduleEventConflictType $scheduleEventConflictType
	 * @return KalturaScheduleEventListResponse
	 * @throws KalturaAPIException
	 */
	public function getConflictsAction($resourceIds, KalturaScheduleEvent $scheduleEvent, $scheduleEventIdToIgnore = null,
									   $scheduleEventConflictType = KalturaScheduleEventConflictType::RESOURCE_CONFLICT)
	{
		if (!$resourceIds)
		{
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL, 'resourceIds');
		}

		/* @var $dbScheduleEvent ScheduleEvent */
		$dbScheduleEvent = $scheduleEvent->toObject();
		$events = array();
		$dates = array();
		if($dbScheduleEvent->getRecurrenceType() === ScheduleEventRecurrenceType::RECURRING)
		{
			$maxRecurrences = SchedulePlugin::getScheduleEventmaxRecurrences();
			$datesGenerator = new DatesGenerator($maxRecurrences, $dbScheduleEvent->getRecurrence()->asArray(), array('KalturaLog', 'debug'));
			$dates = $datesGenerator->getDates($dbScheduleEvent->getStartDate(null));
			$duration = $dbScheduleEvent->getDuration();
		}
		else
		{
			$dates[] = $dbScheduleEvent->getStartDate(null);
			$duration = $dbScheduleEvent->getEndDate(null) - $dbScheduleEvent->getStartDate(null);
		}

		foreach($dates as $date)
		{
			if($scheduleEventConflictType == KalturaScheduleEventConflictType::RESOURCE_CONFLICT ||
				$scheduleEventConflictType == KalturaScheduleEventConflictType::BOTH )
			{
				$events = array_merge($events, ScheduleEventPeer::retrieveEventsByResourceIdsAndDateWindow($resourceIds,
					$date, $date + $duration, $scheduleEventIdToIgnore));
			}

			if($scheduleEventConflictType == KalturaScheduleEventConflictType::BLACKOUT_CONFLICT ||
				$scheduleEventConflictType == KalturaScheduleEventConflictType::BOTH )
			{
				$events = array_merge($events, ScheduleEventPeer::retrieveBlackoutEventsByDateWindow($date,
					$date + $duration, $scheduleEventIdToIgnore));
			}
		}

		if (!count($events))
		{
			$this->reserveResources($resourceIds);
		}

		$response = new KalturaScheduleEventListResponse();
		$response->objects = KalturaScheduleEventArray::fromDbArray($events, $this->getResponseProfile());
		$response->totalCount = count($events);
		return $response;
	}

	private function reserveResources($resourceIds)
	{
		$resourceIdsArray = explode(",", $resourceIds);
		$resourceReservator = new kResourceReservation();
		foreach($resourceIdsArray as $resourceId)
		{
			if (!$resourceReservator->reserve($resourceId))
			{
				KalturaLog::info("Could not reserve all resource id [$resourceId]");
				$this->clearAllReservation($resourceReservator, $resourceIdsArray);
				throw new KalturaAPIException(KalturaErrors::RESOURCE_IS_RESERVED, $resourceId);
			}
		}
	}

	private function clearAllReservation($resourceReservator, $resourceIds)
	{
		/* @var kResourceReservation $resourceReservator*/
		foreach($resourceIds as $resourceId)
			$resourceReservator->deleteReservation($resourceId);
	}
}
