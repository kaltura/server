<?php


/**
 * Skeleton subclass for representing a row from the 'event_notification_template' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.eventNotification
 * @subpackage model
 */
abstract class EventNotificationTemplate extends BaseEventNotificationTemplate 
{
	const CUSTOM_DATA_EVENT_CONDITIONS = 'eventConditions';
	const CUSTOM_DATA_CONTENT_PARAMETERS = 'contentParameters';
	const CUSTOM_DATA_USER_PARAMETERS = 'userParameters';
	const CUSTOM_DATA_MANUAL_DISPATCH_ENABLED = 'manualDispatchEnabled';
	const CUSTOM_DATA_AUTOMATIC_DISPATCH_ENABLED = 'automaticDispatchEnabled';

	/**
	 * Returns job data for dispatching the event notification
	 * @param kScope $scope
	 * @return kEventNotificationDispatchJobData
	 */
	abstract public function getJobData(kScope $scope = null); 

	public function getEventConditions()									{return $this->getFromCustomData(self::CUSTOM_DATA_EVENT_CONDITIONS);}
	public function getContentParameters()									{return $this->getFromCustomData(self::CUSTOM_DATA_CONTENT_PARAMETERS, null, array());}
	public function getUserParameters()										{return $this->getFromCustomData(self::CUSTOM_DATA_USER_PARAMETERS, null, array());}
	public function getManualDispatchEnabled()								{return $this->getFromCustomData(self::CUSTOM_DATA_MANUAL_DISPATCH_ENABLED);}
	public function getAutomaticDispatchEnabled()							{return $this->getFromCustomData(self::CUSTOM_DATA_AUTOMATIC_DISPATCH_ENABLED);}

	public function setEventConditions(array $v)							{return $this->putInCustomData(self::CUSTOM_DATA_EVENT_CONDITIONS, $v);}
	public function setContentParameters(array $v)							{return $this->putInCustomData(self::CUSTOM_DATA_CONTENT_PARAMETERS, $v);}
	public function setUserParameters(array $v)								{return $this->putInCustomData(self::CUSTOM_DATA_USER_PARAMETERS, $v);}
	public function setManualDispatchEnabled($v)							{return $this->putInCustomData(self::CUSTOM_DATA_MANUAL_DISPATCH_ENABLED, $v);}
	public function setAutomaticDispatchEnabled($v)							{return $this->putInCustomData(self::CUSTOM_DATA_AUTOMATIC_DISPATCH_ENABLED, $v);}
	
} // EventNotificationTemplate
