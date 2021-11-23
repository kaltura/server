<?php
/**
 * @service virtualEvent
 * @package plugins.virtualEvent
 * @subpackage api.services
 */
class VirtualEventService extends KalturaBaseService
{
	const VIRTUAL_EVENT = 'VirtualEvent';
	
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		
		$partnerId = $this->getPartnerId();
		if (!VirtualEventPlugin::isAllowedPartner($partnerId))
		{
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN, "{$this->serviceName}->{$this->actionName}");
		}
		
		$this->applyPartnerFilterForClass(self::VIRTUAL_EVENT);
	}
	
	/**
	 * Add a new virtual event
	 *
	 * @action add
	 * @param KalturaVirtualEvent $virtualEvent
	 * @return KalturaVirtualEvent
	 *
	 */
	public function addAction(KalturaVirtualEvent $virtualEvent )
	{
		/* @var $dbVirtualEvent VirtualEvent */
		$this->validateScheduleEvents($virtualEvent);
		$this->validateGroups($virtualEvent);
		$dbVirtualEvent = $virtualEvent->toInsertableObject();
		$dbVirtualEvent->setPartnerId($this->getPartnerId());
		$dbVirtualEvent->save();
		
		// return the saved object
		$virtualEvent = new KalturaVirtualEvent();
		$virtualEvent->fromObject($dbVirtualEvent, $this->getResponseProfile());
		return $virtualEvent;
	}
	
	/**
	 * Retrieve a virtual event by id
	 *
	 * @action get
	 * @param int $id
	 * @return KalturaVirtualEvent
	 *
	 * @throws KalturaVirtualEventErrors::VIRTUAL_EVENT_NOT_FOUND
	 */
	public function getAction($id)
	{
		// get the object
		$dbVirtualEvent = VirtualEventPeer::retrieveByPK($id);
		if (!$dbVirtualEvent)
		{
			throw new KalturaAPIException(KalturaVirtualEventErrors::VIRTUAL_EVENT_NOT_FOUND, $id);
		}
		
		// return the found object
		$virtualEvent = new KalturaVirtualEvent();
		$virtualEvent->fromObject($dbVirtualEvent, $this->getResponseProfile());
		return $virtualEvent;
	}
	
	/**
	 * Update an existing virtual event
	 *
	 * @action update
	 * @param int $id
	 * @param KalturaVirtualEvent $virtualEvent
	 * @return KalturaVirtualEvent
	 *
	 * @throws KalturaVirtualEventErrors::VIRTUAL_EVENT_NOT_FOUND
	 */
	public function updateAction($id, KalturaVirtualEvent $virtualEvent)
	{
		// get the object
		$dbVirtualEvent = VirtualEventPeer::retrieveByPK($id);
		if (!$dbVirtualEvent)
		{
			throw new KalturaAPIException(KalturaVirtualEventErrors::VIRTUAL_EVENT_NOT_FOUND, $id);
		}
		$this->validateScheduleEvents($virtualEvent);
		$this->validateGroups($virtualEvent);
		
		// save the object
		/** @var VirtualEvent $dbVirtualEvent */
		$dbVirtualEvent = $virtualEvent->toUpdatableObject($dbVirtualEvent);
		$dbVirtualEvent->save();
		
		// return the saved object
		$virtualEvent = new KalturaVirtualEvent();
		$virtualEvent->fromObject($dbVirtualEvent, $this->getResponseProfile());
		return $virtualEvent;
	}
	
	/**
	 * Delete a virtual event
	 *
	 * @action delete
	 * @param int $id
	 *
	 * @throws KalturaVirtualEventErrors::VIRTUAL_EVENT_NOT_FOUND
	 */
	public function deleteAction($id)
	{
		// get the object
		$dbVirtualEvent = VirtualEventPeer::retrieveByPK($id);
		if (!$dbVirtualEvent)
		{
			throw new KalturaAPIException(KalturaVirtualEventErrors::VIRTUAL_EVENT_NOT_FOUND, $id);
		}
		
		// set the object status to deleted
		$dbVirtualEvent->setStatus(KalturaVirtualEventStatus::DELETED);
		$dbVirtualEvent->save();
	}
	
	/**
	 * List virtual events
	 *
	 * @action list
	 * @param KalturaVirtualEventFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaVirtualEventListResponse
	 */
	public function listAction(KalturaVirtualEventFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
		{
			$filter = new KalturaVirtualEventFilter();
		}

		if (!$pager)
		{
			$pager = new KalturaFilterPager();
		}
		
		return $filter->getListResponse($pager, $this->getResponseProfile());
	}
	
	protected function validateScheduleEvents (KalturaVirtualEvent $virtualEvent)
	{
		$partnerId = kCurrentContext::getCurrentPartnerId();
		if ($virtualEvent->agendaScheduleEventId)
		{
			$this->validateSpecificScheduleEvent($partnerId, $virtualEvent->agendaScheduleEventId, VirtualScheduleEventSubType::AGENDA);
		}
		if ($virtualEvent->registrationScheduleEventId)
		{
			$this->validateSpecificScheduleEvent($partnerId, $virtualEvent->registrationScheduleEventId, VirtualScheduleEventSubType::REGISTRATION);
		}
		if ($virtualEvent->mainEventScheduleEventId)
		{
			$this->validateSpecificScheduleEvent($partnerId, $virtualEvent->mainEventScheduleEventId, VirtualScheduleEventSubType::MAIN_EVENT);
		}
	}
	
	protected function validateSpecificScheduleEvent ($partnerId, $scheduleEventId, $subType)
	{
		$dbScheduleEvents = ScheduleEventPeer::retrieveByPartnerIdAndId($partnerId, $scheduleEventId);
		if(!$dbScheduleEvents)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $scheduleEventId);
		}
		foreach ($dbScheduleEvents as $dbScheduleEvent)
		{
			if($dbScheduleEvent->getVirtualScheduleEventSubType() != $subType)
			{
				throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $scheduleEventId);
			}
		}
	}
	
	protected function validateGroups (KalturaVirtualEvent $virtualEvent)
	{
		if (!is_null($virtualEvent->adminsGroupId))
		{
			$this->isValidGroup($virtualEvent->adminsGroupId);
		}
		if (!is_null($virtualEvent->attendeesGroupId))
		{
			$this->isValidGroup($virtualEvent->attendeesGroupId);
		}
	}
	
	protected function isValidGroup($groupId)
	{
		$dbGroup = kuserPeer::getKuserByPartnerAndUid($this->getPartnerId(), $groupId);
		if(!$dbGroup || $dbGroup->getType() != KuserType::GROUP)
		{
			throw new KalturaAPIException(KalturaGroupErrors::INVALID_GROUP_ID, $groupId);
		}
	}
}