<?php

/**
 * ScheduleEvent service lets you create and manage schedule events
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
		$dbScheduleEvent->save();
		
		if($dbScheduleEvent->getRecurrenceType() === ScheduleEventRecurrenceType::RECURRING)
		{
			$this->createRecurrences($dbScheduleEvent);
		}
		
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
		
		$dbScheduleEvent = $scheduleEvent->toUpdatableObject($dbScheduleEvent);
		/* @var $dbScheduleEvent ScheduleEvent */
		$dbScheduleEvent->save();
		
		if($dbScheduleEvent->getRecurrenceType() === ScheduleEventRecurrenceType::RECURRING)
		{
			$this->createRecurrences($dbScheduleEvent);
		}
		
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
	 * Create schedule recurrences for recurring event
	 * 
	 * @param ScheduleEvent $dbScheduleEvent
	 */
	private function createRecurrences(ScheduleEvent $dbScheduleEvent)
	{
		$now = kApiCache::getTime();
		if($dbScheduleEvent->getEndDate(null) < $now)
		{
			KalturaLog::debug("Event [" . $dbScheduleEvent->getId() . "] end time already passed");
			return;
		}
		
		$maxDuration = SchedulePlugin::getScheduleEventmaxDuration();
		$maxRecurrences = SchedulePlugin::getScheduleEventmaxRecurrences();
		$startTime = max($now, $dbScheduleEvent->getStartDate(null));
		$endTime = min($now + $maxDuration, $dbScheduleEvent->getEndDate(null));
		$dates = $dbScheduleEvent->getDates($startTime, $endTime, $maxRecurrences);
		KalturaLog::debug("Found [" . count($dates) . "] dates");
		
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
			$scheduleEvent->setEndDate($date + $scheduleEvent->getDuration());
			$scheduleEvent->save();
		}
	}
}
