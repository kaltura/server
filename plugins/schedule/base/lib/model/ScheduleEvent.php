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
	protected function setOwnerId($puserId)
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
	
	/**
	 * @return kScheduleEventRecurrence
	 */
	public function getRecurrence()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_RECURRENCE);
	}
	
	/**
	 * Returns a list of timestamps in the specified period.
	 * @param int $periodStart the starting timestamp of the period
	 * @param int $periodEnd the ending timestamp of the period
	 * @return array
	 */
	public function getDates($periodStart = null, $periodEnd = null, $limit = null)
	{
		if($this->getRecurrenceType() == ScheduleEventRecurrenceType::NONE)
		{
			return array($this->getStartDate(null));
		}
		
		if(!$periodStart)
		{
			$periodStart = time();
		}
		if(!$periodEnd)
		{
			$periodEnd = strtotime('+1 year', $periodStart);
		}
		if(!$limit)
		{
			$limit = SchedulePlugin::getScheduleEventmaxRecurrences();
		}
		
		$recurrence = $this->getRecurrence();
		$dates = $recurrence->getDates($periodStart, $periodEnd, $this->getStartDate(null), $limit);
		
		sort($dates);
		if(count($dates) > $limit)
		{
			$dates = array_slice($dates, 0, $limit);
		}
		
		return $dates;
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
	
	public function getEntryIds()
	{
		return '';
	}
	
	public function getCategoryIdsForIndex()
	{
		return '';
	}
	
	public function getResourceIdsForIndex()
	{
		$resources = ScheduleEventResourcePeer::retrieveByEventId($this->getId());
	
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
	
} // ScheduleEvent
