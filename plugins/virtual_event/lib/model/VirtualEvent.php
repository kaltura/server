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
	const CUSTOM_DATA_FIELD_ADMINS_GROUP_ID = 'adminsGroupId';
	const CUSTOM_DATA_FIELD_ATTENDEES_GROUP_ID = 'attendeesGroupId';
	const CUSTOM_DATA_FIELD_MAIN_SE_ID = 'mainEventScheduleEventId';
	const CUSTOM_DATA_FIELD_REGISTRATION_SE_ID = 'registrationScheduleEventId';
	const CUSTOM_DATA_FIELD_AGENDA_SE_ID = 'agendaScheduleEventId';
	
	
	public function __construct ()
	{
		parent::__construct();
	}
	
	public function preInsert (PropelPDO $con = null)
	{
		$this->setStatus(VirtualEventStatus::ACTIVE);
		return parent::preInsert($con);
	}
	
	 public function getCacheInvalidationKeys ()
	{
		return array("virtualEvent:id" . strtolower($this->getId()));
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
	
	 public function getMainEventScheduleEventId()
	 {
		 return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_MAIN_SE_ID);
	 }
	
	 public function setMainEventScheduleEventId($v)
	 {
		 $this->putInCustomData(self::CUSTOM_DATA_FIELD_MAIN_SE_ID, $v);
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
 }
