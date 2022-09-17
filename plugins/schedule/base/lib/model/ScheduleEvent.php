<?php

/**
 * Skeleton subclass for representing a row from the 'schedule_event' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.schedule
 * @subpackage model
 */
abstract class ScheduleEvent extends BaseScheduleEvent implements IRelatedObject, IIndexable
{
	const CUSTOM_DATA_FIELD_RECURRENCE = 'recurrence';
	const CUSTOM_DATA_FIELD_OWNER_ID = 'ownerId';

	const RESOURCE_PARENT_SEARCH_PERFIX = 'r';
	const RESOURCES_INDEXED_FIELD_PREFIX = 'pid';
	
	const CUSTOM_DATA_FIELD_LINKED_TO = 'linkedTo';
	const CUSTOM_DATA_FIELD_LINKED_BY = 'linkedBy';
	
	
	
	public function __construct() 
	{
		parent::__construct();
		$this->applyDefaultValues();
	}
	
	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or equivalent initialization method).
	 * @see __construct()
	 */
	public function applyDefaultValues()
	{
		$this->setSequence(1);
	}
	
	/* (non-PHPdoc)
	 * @see BaseScheduleEvent::preInsert()
	 */
	public function preInsert(PropelPDO $con = null)
	{
		$this->setStatus(ScheduleEventStatus::ACTIVE);
		$this->setPartnerId(kCurrentContext::getCurrentPartnerId());
		
		if(!$this->getParentId())
		{
			$this->setOwnerId(kCurrentContext::$ks_uid);
			$this->incrementSequence();
		}
		
		if($this->getRecurrenceType() != ScheduleEventRecurrenceType::RECURRENCE)
		{
			if(is_null($this->getClassificationType()))
			{
				$this->setClassificationType(ScheduleEventClassificationType::PUBLIC_EVENT);
			}
		}
		$this->setCustomDataObj();
    	
		return parent::preInsert($con);
	}
	
	/**
	 * {@inheritDoc}
	 * @see BaseScheduleEvent::postInsert()
	 */
	public function postInsert(PropelPDO $con = null)
	{
		parent::postInsert($con);
	
		if (!$this->alreadyInSave)
			kEventsManager::raiseEvent(new kObjectAddedEvent($this));
	}
	
	/**
	 * {@inheritDoc}
	 * @see BaseScheduleEvent::postUpdate()
	 */
	public function postUpdate(PropelPDO $con = null)
	{
		if ($this->alreadyInSave)
			return parent::postUpdate($con);
		
		$objectUpdated = $this->isModified();
		$objectDeleted = false;
		if($this->isColumnModified(ScheduleEventPeer::STATUS) && $this->getStatus() == ScheduleEventStatus::DELETED) {
			$objectDeleted = true;
		}
			
		$ret = parent::postUpdate($con);
		
		if ($objectDeleted)
		{
			kEventsManager::raiseEvent(new kObjectDeletedEvent($this));
		}

		if($objectUpdated)
		{
		    kEventsManager::raiseEvent(new kObjectUpdatedEvent($this));
		}
			
		return $ret;
	}
	
	
	/* (non-PHPdoc)
	 * @see BaseScheduleEvent::preSave()
	 */
	public function preSave(PropelPDO $con = null)
	{
		if($this->getRecurrenceType() != ScheduleEventRecurrenceType::RECURRING && $this->getDuration() != $this->getEndDate(null) - $this->getStartDate(null))
		{
			$this->setDuration($this->getEndDate(null) - $this->getStartDate(null));
		}
    	
		return parent::preSave($con);
	}
	
	public function incrementSequence()
	{
		$this->setSequence(kDataCenterMgr::incrementVersion($this->getSequence()));
	}
	
	/**
	 * @param string $v
	 */
	public function setOwnerId($puserId)
	{
		$kuser = kuserPeer::createKuserForPartner(kCurrentContext::getCurrentPartnerId(), $puserId, kCurrentContext::$is_admin_session);
		$this->setOwnerKuserId($kuser->getId());
		$this->putInCustomData(self::CUSTOM_DATA_FIELD_OWNER_ID, $puserId);
	}
	
	/**
	 * @return string
	 */
	public function getOwnerId()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_OWNER_ID);
	}
	
	/**
	 * @param kScheduleEventRecurrence $v
	 */
	public function setRecurrence(kScheduleEventRecurrence $v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_FIELD_RECURRENCE, $v);
	}

	public function deleteRecurrence()
	{
		$this->removeFromCustomData(self::CUSTOM_DATA_FIELD_RECURRENCE);
	}

	/**
	 * @return kScheduleEventRecurrence
	 */
	public function getRecurrence()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_RECURRENCE);
	}
	
	public function getLinkedTo()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_LINKED_TO);
	}
	
	public function getLinkedBy()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_LINKED_BY);
	}
	
	public function setLinkedTo($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_FIELD_LINKED_TO, $v);
	}
	
	public function setLinkedByString($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_FIELD_LINKED_BY, $v);
	}
	
	public function addAnotherLinkedBy($v)
	{
		$linkedByArray = explode(',', $this->getLinkedBy());
		if (!in_array($v, $linkedByArray))
		{
			$linkedByArray[] = $v;
			$linkedByString = implode(',', $linkedByArray);
			$this->putInCustomData(self::CUSTOM_DATA_FIELD_LINKED_BY, $linkedByString);
		}
	}
	
	public function removeFromLinkedByArray($v)
	{
		$linkedByArray = explode(',', $this->getLinkedBy());
		$key = array_search($v, $linkedByArray);
		if ($key !== false)
		{
			unset($linkedByArray[$key]);
			$linkedByString = implode(',', $linkedByArray);
			$this->putInCustomData(self::CUSTOM_DATA_FIELD_LINKED_BY, $linkedByString);
		}
	}
	
	public function addLinkedByEventOfNewFollower($linkedToEventId)
	{
		$linkedToEvent = ScheduleEventPeer::retrieveByPK($linkedToEventId);
		if (!$linkedToEvent)
		{
			KalturaLog::err("Event $linkedToEventId not found");
			return;
		}
		$linkedToEvent->addAnotherLinkedBy($this->id);
		$linkedToEvent->save();
	}
	
	public function updateStartEndTimeOfFollowerEvents()
	{
		$linkedByEventIds = explode(',', $this->getLinkedBy());
		foreach ($linkedByEventIds as $linkedByEventId)
		{
		//update start & end date for all linked by events
			if(trim($linkedByEventId) == '')
			{
				continue;
			}
			$linkedEvent = ScheduleEventPeer::retrieveByPK($linkedByEventId);
			if (!$linkedEvent)
			{
				KalturaLog::err("Event $linkedByEventId not found");
				continue;
			}
			$linkedEvent->shiftEvent(strtotime($this->getEndDate()));
			$linkedEvent->save();
		}
	}
	
	public function shiftEvent ($parentEndDate)
	{
		$newStartDate = $parentEndDate + $this->getLinkedTo()->offset;
		$this->setStartDate($newStartDate);
		$this->setEndDate($newStartDate + $this->duration);
	}
	
	public function unlinkFollowerEvents()
	{
		$linkedByEventIds = explode(',', $this->getLinkedBy());
		foreach ($linkedByEventIds as $linkedByEventId)
		{
			if(trim($linkedByEventId) == '')
			{
				continue;
			}
			$linkedEvent = ScheduleEventPeer::retrieveByPK($linkedByEventId);
			if (!$linkedEvent)
			{
				KalturaLog::err("Event $linkedByEventId not found");
				continue;
			}
			$linkedEvent->setLinkedTo(null);
			$linkedEvent->save();
		}
		
	}
	
	public function removeCurrentEventFromPrecedingEvent($linkedToEventId)
	{
		$linkedToEvent = ScheduleEventPeer::retrieveByPK($linkedToEventId);
		if (!$linkedToEvent)
		{
			KalturaLog::err("Event $linkedToEventId not found");
			return;
		}
		$linkedToEvent->removeFromLinkedByArray($this->getId());
		$linkedToEvent->save();
	}
	
	public function getLinkedToEndTime()
	{
		$linkedToEventId = $this->getLinkedTo()->getEventId();
		$linkedToEvent = ScheduleEventPeer::retrieveByPK($linkedToEventId);
		if (!$linkedToEvent)
		{
			KalturaLog::err("Event $linkedToEventId not found");
			return;
		}
		return $linkedToEvent->getEndDate();
	}
	
	/**
	 * {@inheritDoc}
	 * @see IIndexable::getIntId()
	 */
	public function getIntId()
	{
		return $this->getId();
	}
	
	/**
	 * {@inheritDoc}
	 * @see IIndexable::getEntryId()
	 */
	public function getEntryId()
	{
		return null;
	}
	
	/**
	 * {@inheritDoc}
	 * @see IIndexable::getIndexObjectName()
	 */
	public function getIndexObjectName()
	{
		return 'ScheduleEventIndex';
	}
	
	/**
	 * {@inheritDoc}
	 * @see IIndexable::indexToSearchIndex()
	 */
	public function indexToSearchIndex()
	{
		kEventsManager::raiseEventDeferred(new kObjectReadyForIndexEvent($this));
	}
	
	public function getSphinxIndexName()
	{
		return kSphinxSearchManager::getSphinxIndexName(ScheduleEventIndex::getObjectIndexName());
	}
	
	public function getEntryIds()
	{
		return '';
	}

	/**
	 * {@inheritDoc}
	 * @see IIndexable::getTemplateEntryId()
	 */
	public function getTemplateEntryId()
	{
		return null;
	}

	public function getCategoryIdsForIndex()
	{
		return '';
	}

	public function getResourceIdsForIndex()
	{
		$resources = ScheduleEventResourcePeer::retrieveByEventIdOrItsParentId($this->getId(), $this->getPartnerId());

		$index = array();
		foreach($resources as $resource)
		{
			/* @var $resource ScheduleEventResource */
				
			$index[] = $resource->getResourceId();
				
			$fullParentIds = $resource->getFullParentIds();
			foreach($fullParentIds as $parentId)
			{
				$index[] = self::RESOURCE_PARENT_SEARCH_PERFIX . $parentId;
			}
		}
	
		$index = array_unique($index);
	
		return self::RESOURCES_INDEXED_FIELD_PREFIX . $this->getPartnerId() . " " .  implode(' ', $index);
	}

	public function getTemplateEntryCategoriesIdsForIndex()
	{
		return '';
	}

	public function getResourceSystemNamesForIndex()
	{
		$eventResources = ScheduleEventResourcePeer::retrieveByEventId($this->getId(), $this->getPartnerId());

		$resourceIds = array();
		$system_names = array();

		foreach ($eventResources as $eventResource)
		{
			$resourceIds[] = $eventResource->getResourceId();
		}

		$resources = ScheduleResourcePeer::retrieveByPKs($resourceIds);
		foreach ($resources as $resource)
		{
			if ($resource != null)
			{
				$resourceSystemName = $resource->getSystemName();
				if ($resourceSystemName != null)
				{
					$resourceSystemName = mySearchUtils::getMd5EncodedString($resourceSystemName);
					$system_names[] = $resourceSystemName;
				}
			}
		}
		return implode(' ', $system_names);
	}

	public function getResourceTagsForIndex()
    {
        $eventResources = ScheduleEventResourcePeer::retrieveByEventId($this->getId(), $this->getPartnerId());

        $resourceIds = array();
        $resourceTags = array();

        foreach ($eventResources as $eventResource)
        {
            $resourceIds[] = $eventResource->getResourceId();
        }

        $resources = ScheduleResourcePeer::retrieveByPKs($resourceIds);
        foreach ($resources as $resource)
        {
            if ($resource && $resource->getTags())
            {
                $resourceTags = array_merge($resourceTags, explode(',', $resource->getTags()));
            }
        }
        return implode(' ', $resourceTags);
    }

	public function getSummary()
	{
		if (parent::getSummary())
			return parent::getSummary();
		if ($this->parent_id)
		{
			$parentObj = ScheduleEventPeer::retrieveByPK($this->parent_id);
			if ($parentObj)
				return $parentObj->getSummary();
		}
	}

	public static function getEventValues($scheduleEvents, $field)
	{
		$fieldVals = array();
		foreach($scheduleEvents as $scheduleEvent) {
			/* @var $scheduleEvent ScheduleEvent */
			$fieldVals[] = $scheduleEvent->$field(null);
		}
		return $fieldVals;
	}

	public function getCacheInvalidationKeys()
	{
		return array("scheduleEvent:id".strtolower($this->getId()));
	}

	public function getBlackoutConflicts()
	{
		return array();
	}
	
	public function getSphinxMatchOptimizations() {
		$objectName = $this->getIndexObjectName();
		return $objectName::getSphinxMatchOptimizations($this);
	}

	public function isRangeIntersects($startTime, $endTime)
	{
		$eventStart = $this->getCalculatedStartTime();
		$eventEnd = $this->getCalculatedEndTime();
		return ($eventStart < $endTime && $eventEnd > $startTime);
	}

	/**
	 * @return int - epoch time
	 */
	public function getCalculatedStartTime()
	{
		return $this->getStartDate(null);
	}

	/**
	 * @return int - epoch time
	 */
	public function getCalculatedEndTime()
	{
		return $this->getEndDate(null);
	}
	
} // ScheduleEvent
