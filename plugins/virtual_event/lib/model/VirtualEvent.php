<?php

/**
 * Skeleton subclass for representing a row from the 'virtual_event' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.virtual_event
 * @subpackage model
 */
 class VirtualEvent extends BaseVirtualEvent implements IRelatedObject
{
	const CUSTOM_DATA_FIELD_OWNER_ID   = 'ownerId';
    const CUSTOM_DATA_FIELD_ADMINS_GROUP_ID = 'adminsGroupId';
	const CUSTOM_DATA_FIELD_ATTENDEES_GROUP_ID = 'attendeesGroupId';
	const CUSTOM_DATA_FIELD_FULL_SE_ID = 'fullScheduleEventId';
	const CUSTOM_DATA_FIELD_REGISTRATION_SE_ID = 'registrationScheduleEventId';
	const CUSTOM_DATA_FIELD_AGENDA_SE_ID = 'agendaScheduleEventId';
	
	const RESOURCES_INDEXED_FIELD_PREFIX = 'pid';
	
	
	public function __construct ()
	{
		parent::__construct();
//		$this->applyDefaultValues();
	}
	
	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or equivalent initialization method).
	 * @see __construct()
	 */
	public function applyDefaultValues ()
	{
		$this->setSequence(1);
	}
	
	/* (non-PHPdoc)
	 * @see BaseVirtualEvent::preInsert()
	 */
	public function preInsert (PropelPDO $con = null)
	{
		$this->setStatus(VirtualEventStatus::ACTIVE);
		$this->setPartnerId(kCurrentContext::getCurrentPartnerId());
		
		$this->setCustomDataObj();
		
		return parent::preInsert($con);
	}
	
	/**
	 * {@inheritDoc}
	 * @see BaseVirtualEvent::postInsert()
	 */
	public function postInsert (PropelPDO $con = null)
	{
		parent::postInsert($con);
		
		if (!$this->alreadyInSave)
		{
			kEventsManager::raiseEvent(new kObjectAddedEvent($this));
		}
	}
	
	/**
	 * {@inheritDoc}
	 * @see BaseVirtualEvent::postUpdate()
	 */
	public function postUpdate (PropelPDO $con = null)
	{
		if ($this->alreadyInSave)
		{
			return parent::postUpdate($con);
		}
		
		$objectUpdated = $this->isModified();
		$objectDeleted = false;
		if ($this->isColumnModified(VirtualEventPeer::STATUS) && $this->getStatus() == VirtualEventStatus::DELETED)
		{
			$objectDeleted = true;
		}
		
		$ret = parent::postUpdate($con);
		
		if ($objectDeleted)
		{
			kEventsManager::raiseEvent(new kObjectDeletedEvent($this));
		}
		
		if ($objectUpdated)
		{
			kEventsManager::raiseEvent(new kObjectUpdatedEvent($this));
		}
		
		return $ret;
	}
	
	
	/* (non-PHPdoc)
	 * @see BaseVirtualEvent::preSave()
	 */
	public function preSave (PropelPDO $con = null)
	{
		return parent::preSave($con);
	}
	
	public function incrementSequence ()
	{
		$this->setSequence(kDataCenterMgr::incrementVersion($this->getSequence()));
	}
	
	/**
	 * @param string $v
	 */
	public function setOwnerId ($puserId)
	{
		$kuser = kuserPeer::createKuserForPartner(kCurrentContext::getCurrentPartnerId(), $puserId, kCurrentContext::$is_admin_session);
		$this->setOwnerKuserId($kuser->getId());
		$this->putInCustomData(self::CUSTOM_DATA_FIELD_OWNER_ID, $puserId);
	}
	
	/**
	 * @return string
	 */
	public function getOwnerId ()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_OWNER_ID);
	}
	
	/**
	 * {@inheritDoc}
	 * @see IIndexable::getIntId()
	 */
	public function getIntId ()
	{
		return $this->getId();
	}
	
	
	public function getSummary ()
	{
		if (parent::getSummary())
		{
			return parent::getSummary();
		}
	}
	
	public function getCacheInvalidationKeys ()
	{
		return array("virtualEvent:id" . strtolower($this->getId()));
	}
	
	public function getSphinxMatchOptimizations ()
	{
		$objectName = $this->getIndexObjectName();
		
		return $objectName::getSphinxMatchOptimizations($this);
	}
	
	 public function getAdminsGroupId()
	 {
		 return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_ADMINS_GROUP_ID);
	 }
	
	 public function setAdminsGroupId($v)
	 {
		 $this->putInCustomData(self::CUSTOM_DATA_FIELD_ADMINS_GROUP_ID, $v);
	 }
	
	 public function getAttendeesGroupId()
	 {
		 return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_ATTENDEES_GROUP_ID);
	 }
	
	 public function setAttendeesGroupId($v)
	 {
		 $this->putInCustomData(self::CUSTOM_DATA_FIELD_ATTENDEES_GROUP_ID, $v);
	 }
	
	 public function getEventScheduleEventId()
	 {
		 return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_FULL_SE_ID);
	 }
	
	 public function setEventScheduleEventId($v)
	 {
		 $this->putInCustomData(self::CUSTOM_DATA_FIELD_FULL_SE_ID, $v);
	 }
	
	 public function getAgendaScheduleEventId()
	 {
		 return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_AGENDA_SE_ID);
	 }
	
	 public function setAgendaScheduleEventId($v)
	 {
		 $this->putInCustomData(self::CUSTOM_DATA_FIELD_AGENDA_SE_ID, $v);
	 }
	
	 public function getRegistrationScheduleEventId()
	 {
		 return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_REGISTRATION_SE_ID);
	 }
	
	 public function setRegistrationScheduleEventId($v)
	 {
		 $this->putInCustomData(self::CUSTOM_DATA_FIELD_REGISTRATION_SE_ID, $v);
	 }
	
	 /**
	  * @return mixed
	  */
	 public function getIndexObjectName ()
	 {
		 return "VirtualEventIndex";
	 }
 }
