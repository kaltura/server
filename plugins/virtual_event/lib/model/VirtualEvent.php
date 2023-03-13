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
	const CUSTOM_DATA_FIELD_ADMINS_GROUP_IDS = 'adminsGroupIds';
	const CUSTOM_DATA_FIELD_ATTENDEES_GROUP_IDS = 'attendeesGroupIds';
	const CUSTOM_DATA_FIELD_MAIN_SE_ID = 'mainEventScheduleEventId';
	const CUSTOM_DATA_FIELD_REGISTRATION_SE_ID = 'registrationScheduleEventId';
	const CUSTOM_DATA_FIELD_AGENDA_SE_ID = 'agendaScheduleEventId';
	const CUSTOM_DATA_FIELD_DELETION_DUE_DATE = 'deletionDueDate';
	const CUSTOM_DATA_FIELD_REGISTRATION_FORM_SCHEMA = 'registrationFormSchema';
	const CUSTOM_DATA_FIELD_EVENT_URL = 'eventUrl';
	const CUSTOM_DATA_FIELD_WEBHOOK_REGISTRATION_TOKEN = 'webhookRegistrationToken';

	
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
	
	 public function getAdminsGroupIds()
	 {
		 return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_ADMINS_GROUP_IDS);
	 }
	
	 public function setAdminsGroupIds($v)
	 {
		 $this->putInCustomData(self::CUSTOM_DATA_FIELD_ADMINS_GROUP_IDS, $v);
	 }
	
	 public function getAttendeesGroupIds()
	 {
		 return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_ATTENDEES_GROUP_IDS);
	 }
	
	 public function setAttendeesGroupIds($v)
	 {
		 $this->putInCustomData(self::CUSTOM_DATA_FIELD_ATTENDEES_GROUP_IDS, $v);
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
	
	 public function getDeletionDueDate()
	 {
		 return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_DELETION_DUE_DATE);
	 }
	
	 public function setDeletionDueDate($v)
	 {
		 $this->putInCustomData(self::CUSTOM_DATA_FIELD_DELETION_DUE_DATE, $v);
	 }
	 
	 public function getRegistrationFormSchema()
	 {
		 return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_REGISTRATION_FORM_SCHEMA);
	 }

	 public function setRegistrationFormSchema($v)
	 {
		 $v = str_replace(array("\n", "\r", "\t", "\v", " "), '', $v);
		 return $this->putInCustomData(self::CUSTOM_DATA_FIELD_REGISTRATION_FORM_SCHEMA, $v);
	 }

	 public function getEventUrl()
	 {
		 return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_EVENT_URL);
	 }

	 public function setEventUrl($v)
	 {
		 $this->putInCustomData(self::CUSTOM_DATA_FIELD_EVENT_URL, $v);
	 }

	 public function getWebhookRegistrationToken()
	 {
		 return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_WEBHOOK_REGISTRATION_TOKEN);
	 }

	 public function setWebhookRegistrationToken($v)
	 {
		 $this->putInCustomData(self::CUSTOM_DATA_FIELD_WEBHOOK_REGISTRATION_TOKEN, $v);
	 }
 }
