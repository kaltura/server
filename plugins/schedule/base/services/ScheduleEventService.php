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
		{
			$dates = $this->getRecurrencesDates($dbScheduleEvent);
			self::setRecurringDates($dates, $dbScheduleEvent);
			$this->createRecurrences($dbScheduleEvent, $dates);
		}
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

		if($dbScheduleEvent->getRecurrenceType() === ScheduleEventRecurrenceType::RECURRING)
		{
			$dates = $this->getRecurrencesDates($dbScheduleEvent);
			self::setRecurringDates($dates, $dbScheduleEvent);
			$this->createRecurrences($dbScheduleEvent, $dates);
		}else
			$dbScheduleEvent->save();

		$scheduleEvent = KalturaScheduleEvent::getInstance($dbScheduleEvent, $this->getResponseProfile());
		$scheduleEvent->fromObject($dbScheduleEvent, $this->getResponseProfile());

		return $scheduleEvent;
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
			$filter = new KalturaScheduleEventFilter();

		if(!$pager)
			$pager = new KalturaFilterPager();

		return $filter->getListResponse($pager, $this->getResponseProfile());
	}


	/**
	 * Create schedule recurrences dates
	 *
	 * @param ScheduleEvent $dbScheduleEvent
	 */
	private function getRecurrencesDates(ScheduleEvent $dbScheduleEvent)
	{
		$maxRecurrences = SchedulePlugin::getScheduleEventmaxRecurrences();
		$datesGenerator = new DatesGenerator($maxRecurrences, $dbScheduleEvent->getRecurrence()->asArray(), array('KalturaLog', 'debug'));
		$dates = $datesGenerator->getDates($dbScheduleEvent->getStartDate(null));

		KalturaLog::debug("Found [" . count($dates) . "] dates");
		return $dates;
	}

	/**
	 * Create schedule recurrences for recurring event
	 *
	 * @param ScheduleEvent $dbScheduleEvent
	 * @param array $dates
	 */
	private function createRecurrences(ScheduleEvent $dbScheduleEvent, $dates)
	{
		if (is_null($dates))
		{
			KalturaLog::debug("No dates have been received");
			return;
		}

		ScheduleEventPeer::deleteByParentId($dbScheduleEvent->getId(), $dates);
		$existingScheduleEvents = ScheduleEventPeer::retrieveByParentIdAndDates($dbScheduleEvent->getId(), $dates);
		$existingDates = array();
		foreach($existingScheduleEvents as $existingScheduleEvent)
		{
			/* @var $existingScheduleEvent ScheduleEvent */
			$existingDates[] = $existingScheduleEvent->getOriginalStartDate(null);
		}
		$dates = array_diff($dates, $existingDates);
		$class = get_class($dbScheduleEvent);
		foreach($dates as $date)
		{
			$scheduleEvent = new $class();
			$scheduleEvent->setRecurrenceType(ScheduleEventRecurrenceType::RECURRENCE);
			$scheduleEvent->setParentId($dbScheduleEvent->getId());
			$scheduleEvent->setStartDate($date);
			$scheduleEvent->setOriginalStartDate($date);
			$scheduleEvent->setEndDate($date + $dbScheduleEvent->getDuration());
			$scheduleEvent->save();
		}
	}

	/**
	 * @param $dates
	 * @param $dbScheduleEvent
	 */
	private static function setRecurringDates($dates, $dbScheduleEvent)
	{
		if (!is_null($dates) && !empty($dates))
		{
			$dbScheduleEvent->setStartDate($dates[0]);
			$dbScheduleEvent->setEndDate($dates[0] + $dbScheduleEvent->getDuration());
		}
		$dbScheduleEvent->save();
	}

	/**
	 * List conflicting events for resourcesIds by event's dates
	 *
	 * @action getConflicts
	 * @param string $resourceIds
	 * @param KalturaScheduleEvent $scheduleEvent
	 * @return KalturaScheduleEventArray
	 */
	public function getConflictsAction($resourceIds, KalturaScheduleEvent $scheduleEvent)
	{
		if (!$resourceIds)
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL, 'resourceIds');

		$dbScheduleEvent = $scheduleEvent->toInsertableObject();
		/* @var $dbScheduleEvent ScheduleEvent */

		$events = array();

		if($dbScheduleEvent->getRecurrenceType() === ScheduleEventRecurrenceType::RECURRING)
		{
			$maxRecurrences = SchedulePlugin::getScheduleEventmaxRecurrences();
			$datesGenerator = new DatesGenerator($maxRecurrences, $dbScheduleEvent->getRecurrence()->asArray(), array('KalturaLog', 'debug'));
			$dates = $datesGenerator->getDates($dbScheduleEvent->getStartDate(null));

			foreach($dates as $date)
				$events = array_merge($events, ScheduleEventPeer::retrieveEventsByResourceIdsAndDateWindow($resourceIds, $date, ($date + $dbScheduleEvent->getDuration())));
		}
		else {
			$events = ScheduleEventPeer::retrieveEventsByResourceIdsAndDateWindow($resourceIds, $dbScheduleEvent->getStartDate(null), $dbScheduleEvent->getEndDate(null));
		}

		return KalturaScheduleEventArray::fromDbArray($events);
	}
}
